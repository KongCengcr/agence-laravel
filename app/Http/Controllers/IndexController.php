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
                                            AND permissao.co_tipo_usuario IN (1, 2, 3)'
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
                global $groupDate;
                if($invoices[$i]->co_usuario == $queryName){
                    $date = substr($invoices[$i]->data_emissao,0,7);
      
                    if( empty($groupDate)){
                        $groupDate = $date;

                        $receita_liquida =  ($invoices[$i]->valor - $invoices[$i]->comissao_cn);
                        $comissao = ($invoices[$i]->valor - ($invoices[$i]->valor*$invoices[$i]->total_imp_inc) )* $invoices[$i]->comissao_cn;
                        $lucro = $invoices[$i]->valor - ($invoices[$i]->brut_salario + $invoices[$i]->comissao_cn);
                        $custo_fixo = $invoices[$i]->brut_salario;
                    }elseif($date == $groupDate){

                        $receita_liquida +=  ($invoices[$i]->valor - $invoices[$i]->comissao_cn) ;
                        $comissao += ($invoices[$i]->valor - ($invoices[$i]->valor*$invoices[$i]->total_imp_inc) )* $invoices[$i]->comissao_cn;
                        $lucro += $invoices[$i]->valor - ($invoices[$i]->brut_salario + $invoices[$i]->comissao_cn);
                        $custo_fixo = $invoices[$i]->brut_salario;
                    }else{
                        array_push($fatura, array(
                            "data_emissao" => $date, 
                            "receita_liquida" => $receita_liquida, 
                            "custo_fixo" => $custo_fixo, 
                            "comissao"=> $comissao,
                            "lucro" => $lucro
                            )
                        );
                        $groupDate = $date;

                        $total_receita_liquida += $receita_liquida;
                        $total_custo_fixo += $invoices[$i]->brut_salario;
                        $total_comissao += $comissao;
                        $total_lucro += $lucro;
                    }
                    $name = $invoices[$i]->no_usuario;

                   
                }
            }

            if($to == $groupDate){
                array_push($fatura, array(
                    "data_emissao" => $date, 
                    "receita_liquida" => $receita_liquida, 
                    "custo_fixo" => $custo_fixo, 
                    "comissao"=> $comissao,
                    "lucro" => $lucro
                    )
                );
                $groupDate = '';

                $total_receita_liquida = $receita_liquida;
                $total_custo_fixo = $custo_fixo;
                $total_comissao = $comissao;
                $total_lucro = $lucro;
            }
            
            $nameInvoice = [
                "co_usuario" => $queryName,
                "no_usuario" => $name,
                "fatura" => $fatura,
                "total_receita_liquida" =>  $total_receita_liquida,
                "total_custo_fixo" => $total_custo_fixo,
                "total_comissao" => $total_comissao,
                "total_lucro" => $total_lucro
            ];

            if(count($fatura) > 0){
                array_push($datas, $nameInvoice);
            }
            
            $fatura= [];
        }
        
        return $datas;
    }
}
