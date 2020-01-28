<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class IndexController extends Controller
{

    public function users(Request $request){
        $users = DB::select('SELECT 
                                usuario.co_usuario, 
                                usuario.no_usuario, 
                                permissao.in_ativo, 
                                permissao.co_tipo_usuario 
                                    FROM cao_usuario usuario 
                                        JOIN permissao_sistema permissao 
                                            ON usuario.co_usuario = permissao.co_usuario 
                                        WHERE 
                                            permissao.co_sistema = 1 
                                            AND permissao.in_ativo = "S" 
                                            AND permissao.co_tipo_usuario IN (0, 1, 2)'
                            );
        return $users;
    }

    public function fatura(Request $request){

        // return $request;
        //$queryNames = ["anapaula.chiodaro", "carlos.arruda", "contato"];
        $queryNames = $request->consultors;
        $query = "";
        $queryOr = "";
        if(count($queryNames) > 0){
            foreach($queryNames as $queryName){
                $query .= "os.co_usuario = "."'$queryName'"." or ";
            }
            $queryOr = substr($query,0,-3);
        }else{
            $queryOr = "os.co_usuario = "."'$queryNames[0]'";
        }
        
        //Consulta de factura 
        $invoices = DB::select("SELECT os.co_os, 
                                      os.co_sistema, 
                                      os.co_usuario, 
                                      usuario.no_usuario, 
                                      os.dt_inicio, 
                                      os.dt_fim, 
                                      fatura.num_nf, 
                                      fatura.total, 
                                      fatura.valor,
                                      salario.brut_salario, 
                                      fatura.data_emissao, 
                                      fatura.comissao_cn, 
                                      fatura.total_imp_inc 
                                        FROM cao_os os 
                                        JOIN cao_usuario usuario 
                                            ON os.co_usuario = usuario.co_usuario
                                         JOIN cao_salario salario
                                            ON os.co_usuario =  salario.co_usuario   
                                        JOIN cao_fatura fatura 
                                            ON os.co_os = fatura.co_os 
                                            AND (fatura.data_emissao BETWEEN '$request->from' AND '$request->to')
                                        WHERE             
                                            $queryOr
                                        ORDER BY fatura.data_emissao");
        $datas = [];
        $fatura = [];
        $to =  substr($request->to,0,7); 
        $total_custo_fixo;
        $total_comissao;
        $total_lucro;
        foreach($queryNames as $queryName){
            for ($i=0; $i < count($invoices) ; $i++) { 
                global $name;
                global $total_receita_liquida;
                global $total_custo_fixo;
                global $total_comissao;
                global $total_lucro;
                global $total_imp;
                global $groupDate;
                if($invoices[$i]->co_usuario == $queryName){
                    $date = substr($invoices[$i]->data_emissao,0,7);
      
                    if( empty($groupDate)){
                        $groupDate = $date;

                        $receita_liquida =  $invoices[$i]->valor;
                        $imp_receita = ($invoices[$i]->total_imp_inc/100)* $invoices[$i]->valor;
                        $custo_fixo = $invoices[$i]->brut_salario;
                        $comissao = ($invoices[$i]->valor - ($invoices[$i]->valor*($invoices[$i]->total_imp_inc/100)) )* ($invoices[$i]->comissao_cn/100);
                        
                    }elseif($date == $groupDate){

                        $receita_liquida +=  $invoices[$i]->valor;
                        $imp_receita += ($invoices[$i]->total_imp_inc/100)* $invoices[$i]->valor;
                        $custo_fixo = $invoices[$i]->brut_salario;
                        $comissao += ($invoices[$i]->valor - ($invoices[$i]->valor*($invoices[$i]->total_imp_inc/100)) )* ($invoices[$i]->comissao_cn/100);
                        
                    }else{
                        $receita = $receita_liquida - $imp_receita;
                        $lucro = ($receita - $custo_fixo) - $comissao;

                        array_push($fatura, array(
                            "data_emissao" => $groupDate, 
                            "receita_liquida" => $receita, 
                            "custo_fixo" => $custo_fixo, 
                            "comissao"=> $comissao,
                            "lucro" => ($receita - $custo_fixo) - $comissao,
                            )
                        );
                        $groupDate = $date;

                        $total_receita_liquida += $receita;
                        $total_custo_fixo += $custo_fixo;
                        $total_comissao += $comissao;
                        $total_imp += $imp_receita;
                        $total_lucro += $lucro;
                    }
                    $name = $invoices[$i]->no_usuario;

                   
                }
            }

            if($to == $groupDate){
                $receita = $receita_liquida - $imp_receita;
                $lucro = ($receita - $custo_fixo) - $comissao;
                array_push($fatura, array(
                    "data_emissao" => $date, 
                    "receita_liquida" => $receita, 
                    "custo_fixo" => $custo_fixo, 
                    "comissao"=> $comissao,
                    "lucro" => $lucro,
                    )
                );
                $groupDate = '';

                $total_receita_liquida += $receita;
                $total_custo_fixo += $custo_fixo;
                $total_comissao += $comissao;
                $total_lucro += $lucro;
                $total_imp += $imp_receita;
            }
            
            $nameInvoice = [
                "co_usuario" => $queryName,
                "no_usuario" => $name,
                "fatura" => $fatura,
                "total_receita_liquida" =>  $total_receita_liquida,
                "total_custo_fixo" => $total_custo_fixo,
                "total_comissao" => $total_comissao,
                "total_lucro" => $total_lucro,
                "total_imp" => $total_imp
            ];

            if(count($fatura) > 0){
                array_push($datas, $nameInvoice);
            }
            
            $fatura= [];
            $total_receita_liquida = 0;
            $total_custo_fixo = 0;
            $total_comissao = 0;
            $total_lucro = 0;
            $total_imp = 0;
        }
        
        return $datas;
    }
}
