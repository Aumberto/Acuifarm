<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	{{ HTML::style('css/style.css') }}
	{{ HTML::style('css/jquery-ui.css') }}

	{{ HTML::script('js/jquery.js') }}
	{{ HTML::script('js/jquery-ui-1.10.4.custom.min.js') }}
	<title>Hola</title>
</head>
<body>
	<div class='contenedor'>
	<div class='datos_reales'>
	<h1>STATUS PR0CRIA 07/07/2014</h1>
	
	<table>
		<tr>
			<td>Unidad</td>
			<td>Nº Lote</td>
			<td>Nº peces</td>
			<td>pm (g)</td>
			<td>Biomasa (Kg)</td>
			<td>Pienso (Kg)</td>
			<td>SFR%</td>
		</tr>
		<tr>
			<td>M001</td>
			<td>1303</td>
			<td>246824</td>
			<td>360,2</td>
			<td>88.895</td>
			<td>644</td>
			<td>0,78%</td>
		</tr>
	</table>
	</div>
	<div class='datos_estrategia_anterior'>
		<h1>ESTRATEGIA ANTERIOR S26</h1>
       <table>
		<tr>
			<td>Proveedor</td>
			<td>Tamaño Pellet</td>
			<td>Pienso (Kg)</td>
			<td>Extra FT %</td>
		</tr>
		<tr>
			<td>SKRETTING ESPAÑA, S.A.</td>
			<td>4.0 mm</td>
			<td>650</td>
			<td>100%</td>
		</tr>
	</table>
	</div>
	<div class='datos_propuesta_estrategia'>
      <h1>PROPUESTA GRANJA SEMANAS 29 + 30</h1>
      <table>
      	<tr>
      		<td>Proveedor</td>
			<td>Tamaño Pellet</td>
			<td>Pienso (Kg)</td>
			<td>Extra FT %</td>
			<td>Sacos</td>
			<td>Total Pienso</td>
			<td>SFR%</td>
			<td>% vs FISHTALK</td>
      	</tr>
      </table>
	</div>
	</div>
	
</body>
</html>