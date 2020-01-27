<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Agence</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@9.7.0/dist/sweetalert2.min.css">
		<link rel="stylesheet" href="css/style.css">
		<!-- Styles -->
        
    </head>
    <body>

	<header>
		<nav class="navbar navbar-expand-lg navbar-light bg-light">
		<a class="navbar-brand" href="#">
			<img src="" width="30" height="30" alt="">
		</a>
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="navbarSupportedContent">
				<ul class="navbar-nav mr-auto">
					<li class="nav-item active">
						<a class="nav-link" href="#">Agence</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="#">Projetos</a>
					</li>					
					<li class="nav-item">
						<a class="nav-link" href="#">Administrativo</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="#">Comercial</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="#">Financeiro</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="#">Usuário</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="#">Salir</a>
					</li>			
				</ul>
			</div>
		</nav>
	</header>

    <div>
        @yield('content')
    </div>
        
    <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9.7.0/dist/sweetalert2.min.js"></script>
	<script src="js/highcharts.js"></script>
	<script src="js/script.js"></script>
  </body>
</html>
