$(document).ready(function(from, to) {
			
    const validate = (from, to, consultor) => {
        let checkFrom = from.split("-")
        let checkTo = to.split("-")
        if(checkFrom >= checkTo){
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'First date to consult must be higher',
            })
        }else if(consultor <= 0){
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'No user has been selected, please select 1',
            })
        }else{
            return true;
        }
    }
    
        
    
    let valueOption
    let month =`
        <option value="0">Jan</option>
        <option value="01">Fev</option>
        <option value="02">Mar</option>
        <option value="03">Abr</option>
        <option value="04">Mai</option>                        
        <option value="05">Jun</option>
        <option value="06">Jul</option>
        <option value="07">Ago</option>
        <option value="08" selected>Set</option>
        <option value="09">Out</option>                          
        <option value="10">Nov</option>
        <option value="11">Dez</option>`;

    let year = `
        <option value="2003">2003
        <option value="2004">2004
        <option value="2005">2005</option>
        <option value="2006">2006</option>
        <option value="2007" selected>2007</option>`;
        
    $("#from-month").append(month)
    $("#from-year").append(year)
    $("#to-month").append(month)
    $("#to-year").append(year)

    $('.add').click(() => !$('#origin option:selected').remove().appendTo('#destination'));  

    $('.remove').click(() =>  !$('#destination option:selected').remove().appendTo('#origin'));

    const firstDay = (year, month) => {
        let day = new Date(year, month, 1)
        return `${year}-${month}-${day.getDate()}`
    }

    const lastDay = (year, month) => {
        let day = new Date(year, month + 1, 0)
        return `${year}-${month}-${day.getDate()}`
    }

    const getDateMonth = (date) => {
        let months = ["Janeiro","Fevereiro","Março","Abril","Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro"];
        let dateMonth = new Date(date)

        return `${months[dateMonth.getMonth()+1]} de ${dateMonth.getFullYear()}`
    }

    const chartPie = (pieData) => {
        $("#fatura-table").highcharts({
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie'
            },
            title: {
                text: 'participação em serviços'
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                    }
                }
            },
            series: [{
                name: 'ordem de serviço:',
                colorByPoint: true,
                data: pieData
            }]
        });
    }

    const chartColumn = (columnData, columnMonth, from, to) => {
        $("#fatura-table").highcharts({
            chart: {
                type: 'column'
            },
            title: {
                text: 'Performance commerce'
            },
            subtitle: {
                text: `${getDateMonth(from)} a ${getDateMonth(to)}`
            },
            xAxis: {
                categories: columnMonth,
                crosshair: true
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'R$'
                }
            },
            tooltip: {
                headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                    '<td style="padding:0"><b>{point.y:.1f} mm</b></td></tr>',
                footerFormat: '</table>',
                shared: true,
                useHTML: true
            },
            plotOptions: {
                column: {
                    pointPadding: 0.2,
                    borderWidth: 0
                }
            },
            series: columnData
        });
    }

    /*===================================== Query Users =========================================*/
    let selectName

    $.ajax({
            url: "users",
            dataType: "JSON",
            async: true,
            type: "get",
            success: function (data) {

                data.sort((a,b) => (a.no_usuario > b.no_usuario) ? 1 : -1 )
                data.map(item => {
                    selectName += `<option class="selectOption" value="${item.co_usuario}">${item.no_usuario}</option> `
                })
                $("#origin").append(selectName)
            },
            error: function (data) {}
        });
    /*================================== End Query Users ========================================*/	

    /*===================================== Query Fatura ========================================*/
    $('#relatorio').click((e) => {
        e.preventDefault()
        $("#fatura-table").empty()
        const formData = {
            fromMonth: $("#from-month").val(),
            fromYear: $("#from-year").val(),
            toMonth: $("#to-month").val(),
            toYear: $("#to-year").val()
        }

        let from = firstDay(formData.fromYear, formData.fromMonth)
        let to = lastDay(formData.toYear, formData.toMonth)
        let optionSelected = $('#destination')
        
        if(validate(from, to, optionSelected[0]['options'].length)){
            let queryConsultors = []
            for (let index = 0; index < optionSelected[0]['options'].length; index++) {
                queryConsultors.push(`${optionSelected[0]['options'][`${index}`]['value']}`)
            }

            $("#fatura-table").append(`
            <div class="d-flex justify-content-center">
                <div class="spinner-border" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>`)

            $.ajax({
                url: "fatura",
                dataType: "JSON",
                async: true,
                type: "get",
                data: {
                    consultors: queryConsultors,
                    from: from,
                    to: to
                },
                success: function (data) {
                    
                    let contentFatura = ''
                    data.map(user => {
                        contentFatura += `
                            <div class="table-responsive-sm table-bordered mb-4">
                                <table class="table table-striped">
                                <div class="title-name">
                                    <h3>${user.no_usuario}</h3>
                                </div>
                                    <thead>					
                                        <tr>
                                            <th scope="col">Período</th>
                                            <th scope="col">Receita Líquida</th>
                                            <th scope="col">Custo Fixo</th>
                                            <th scope="col">Comissão</th>
                                            <th scope="col">Lucro</th>
                                        </tr>
                                    </thead>
                                    <tbody>`										
                                    
                        user.fatura.map(item => 
                        contentFatura +=`<tr>
                                <th scope="row">${getDateMonth(item.data_emissao)}</th>
                                <td>R$ ${item.receita_liquida}</td>
                                <td>R$ ${item.custo_fixo}</td>
                                <td>R$ ${item.comissao}</td>
                                <td>R$ ${item.lucro}</td>
                            </tr>										
                        `)
                        
                        contentFatura +=`	
                                        <th scope="row">SALDO</th>
                                            <td>R$ ${user.total_receita_liquida}</td>
                                            <td>R$ ${user.total_custo_fixo}</td>
                                            <td>R$ ${user.total_comissao}</td>
                                            <td>R$ ${user.total_lucro}</td>
                                        </tr>			
                                    </tbody>
                                </table>
                            </div>
                            `
                    })
                    $("#fatura-table").empty()
                    $("#fatura-table").append(contentFatura)
                },
                error: function (data) {
                    $("#fatura-table").empty()
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'No results found',
                    })
                }
            });
        }
    })
    /*==================================== End Query Fatura ======================================*/

    /*==================================== Query Pie Chart ========================================*/
    $("#pieChart").click((e) => {
        e.preventDefault()
        $("#fatura-table").empty()
        const formData = {
            fromMonth: $("#from-month").val(),
            fromYear: $("#from-year").val(),
            toMonth: $("#to-month").val(),
            toYear: $("#to-year").val()
        }

        let from = firstDay(formData.fromYear, formData.fromMonth)
        let to = lastDay(formData.toYear, formData.toMonth)
        let optionSelected = $('#destination')

        if(validate(from, to, optionSelected[0]['options'].length)){
            $("#fatura-table").append(`
            <div class="d-flex justify-content-center">
                <div class="spinner-border" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>`)

            let queryConsultors = []

            for (let i = 0; i < optionSelected[0]['options'].length; i++) {
                queryConsultors.push(`${optionSelected[0]['options'][`${i}`]['value']}`)
            }
            

            $.ajax({
                url: "fatura",
                dataType: "JSON",
                async: true,
                type: "get",
                data: {
                    consultors: queryConsultors,
                    from: from,
                    to: to
                },
                success: function (data) {
                    let pieData = []
                    data.map(user => {
                        let dataUser = new Array(user.no_usuario, user.total_receita_liquida)
                        pieData.push(dataUser)
                    })
                    chartPie(pieData)

                },
                error: function (data) {
                    $("#fatura-table").empty()
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'No results found',
                    })
                }
            });
        }	
    })
/*=================================== End Query Pie Chart =======================================*/

/*==================================== Query Column Chart ========================================*/
$("#columnChart").click((e) => {
        e.preventDefault()
        $("#fatura-table").empty()
        const formData = {
            fromMonth: $("#from-month").val(),
            fromYear: $("#from-year").val(),
            toMonth: $("#to-month").val(),
            toYear: $("#to-year").val()
        }

        let from = firstDay(formData.fromYear, formData.fromMonth)
        let to = lastDay(formData.toYear, formData.toMonth)		
        
        let optionSelected = $('#destination')
        if(validate(from, to, optionSelected[0]['options'].length)){
            $("#fatura-table").append(`
            <div class="d-flex justify-content-center">
                <div class="spinner-border" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>`)

            let fromDate = new Date(from)
            let toDate = new Date(to);
            let fromDateMoth = fromDate.getMonth()
            let toDateMonth = toDate.getMonth()

            let months = [
                "Jan",
                "Fev",
                "Mar",
                "Abr",
                "Mai",
                "Jun",
                "Jul",
                "Ago",
                "Set",
                "Out",
                "Nov",
                "Dez"];

            let columnMonth = []

            for (let i = fromDateMoth + 1; i <= toDateMonth + 1; i++) {
                columnMonth.push(months[i]);
            }
            
            let queryConsultors = []

            for (let i = 0; i < optionSelected[0]['options'].length; i++) {
                queryConsultors.push(`${optionSelected[0]['options'][`${i}`]['value']}`)
            }

            $.ajax({
                url: "fatura",
                dataType: "JSON",
                async: true,
                type: "get",
                data: {
                    consultors: queryConsultors,
                    from: from,
                    to: to
                },
                success: function (data) {
                    let columnData = []
                    data.map(user => {
                        let faturaData = []
                        user.fatura.map(fatura => {
                            let faturaUser = fatura.receita_liquida
                            faturaData.push(faturaUser)
                        })
                        let dataUser = new Object({name: user.no_usuario, data: faturaData})
                        columnData.push(dataUser)
                    })
                    chartColumn(columnData, columnMonth, from, to)

                },
                error: function (data) {
                    $("#fatura-table").empty()
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'No results found',
                    })
                }
            });
        }	
    })
/*=================================== End Query Column Chart =======================================*/

});