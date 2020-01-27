@extends('Layout.app')
@section('content')
<section>
		<div class="section-content mt-5">
			<form >
			<div class="row">
				<div class="col-sm-2">
					<h3>Período</h3>
				</div>
				<div class="col-sm-10 contents">
					<div class="form-group ml-2">
						<select class="form-control" id="from-month" class="date-month">
						</select>
					</div>
					<div class="form-group ml-2">
						<select class="form-control" id="from-year" class="date-year">
						</select>
					</div>
					<div class="ml-3">a</div>
					<div class="form-group ml-3">
						<select class="form-control" id="to-month" class="date-month">
						</select>
					</div><div class="form-group ml-2">
						<select class="form-control" id="to-year" class="date-year">
						</select>
					</div>
				</div>
				
			</div>
			<div class="row">
				<div class="col-md-12 col-lg-2">
					<h2>Consultores</h2>
				</div>
				<div class="col-md-4 col-lg-4">
					<div class="form-group height-100" id="selectName">
						<select class="form-control height-100" name="origin" id="origin" multiple="multiple" >
							
						</select>
					</div>
				</div>
				<div class="col-md-4 col-lg-1 center-btn">
						<div type="button" class="add  btn btn-light">»</div></br>
						<div type="button" class="remove  btn btn-light">«</div>
				</div>
				<div class="col-md-4 col-lg-4">
					<div class="form-group height-100">
						<select class="form-control height-100" name="destination" id="destination" multiple="multiple"></select>
					</div>
				</div>
				<div class="col-md-12 col-lg-1 center-btn">
					
					<div type="button" id="relatorio" class="btn btn-light">Relatório</div>
					<div type="button" id="pieChart" class=" btn btn-light">Pizza</div>
					<div type="button" id="columnChart" class=" btn btn-light">Gráfico</div>
				</div>
					</div>
				</form>
			
		</div>
	</section>

	<section>
		<div class="section-content" id="fatura-table">			
		</div>
	</section>
@endsection