<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-br">
	<head>
		<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
		<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
		<title>Hub importador arquivos VEST</title>
	</head>
	<body>
		<style>.container{padding-top: 100px;}</style>
		<div class="container">
			<h4>Importar Produtos</h4>
			<form method="POST" action="../controller/controllerImporta.php" enctype="multipart/form-data" required >
				<div class="row">				
					<div class="col-xs-12 col-md-12 col-lg-12">
						<input type="file" id="arquivo" name="arquivo">					
					</div>	
				</div>
				<br><br>
				<div class="row">				
					<div class="col-xs-6 col-md-6 col-lg-6">
						<button type="submit" id="btnimportar" class="btn btn-primary">Importar</button>								
						<a href="../novoproduto.php" class="btn btn-warning" role="button" aria-pressed="true">Voltar</a>		
					</div>
				</div>		
			</form>	

			<?php
			if(isset($_SESSION['msg'])){
				echo $_SESSION['msg'];
				unset($_SESSION['msg']);
				
				echo "<h3><b>Integrar</b> Produtos</h3>";
				echo "
					<form method='POST' action='../../controller/controllerIntegra.php' enctype='multipart/form-data' required >										
						<button type='submit' id='btnIntegrar' class='btn btn-success'>Integrar</button>								
					</form>	
				";	
			}
			?>

		</div>	

	</body>
</html>