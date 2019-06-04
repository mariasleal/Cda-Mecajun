<!DOCTYPE html>
<html lang="pt-br">
	<link rel="stylesheet" type="text/css" href="Controle.css">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<head><header>
		<title>Controle de Acesso</title>
		<a class="b" href="menu.php"><h2>Controle de Acesso</h2></a>
	</header></head>
	
	<body>
		<ul>
  			<li><a href="menu.php">Início</a></li>
			<li><a href="relatorios.php">Relatórios</a></li>
  			<li><a href="horarios.php">Horários</a></li>
  			<li><a class="active" href="editar-membro.php">Editar Membro</a></li>
  			<li><a href="relatorio-individual.php">Relatório Individual</a></li>
			<li><a href="painel.php">Painel de Controle</a></li>
			<li><a href="ajuda.html">Ajuda</a></li>
		</ul>

		<?php
			/* Variável de acesso à database*/
			$dbconn = pg_connect("host=localhost dbname=cda user=postgres password=postgres");
			$query = "SELECT * FROM usuarios WHERE usuario = '" . $_COOKIE['login'] . "' AND permissao > 1";
			$verifica = pg_query($query) or die("Erro na identificação do usuário!");
			if (pg_affected_rows($verifica)<=0){
				/* Usuário precisa de permissão 1 para ter acesso ao resto do menu*/
				echo"<script language='javascript' type='text/javascript'>alert('Você não possui permissão para realizar esta operação!');window.location.href='menu.php';</script>";
				die();
			}
		?>
		<div class="aba_login">
			<br>
			<h3 style="text-align:center"><a href="add.php">Adicionar Membro</a></h3>
			<h3 style="text-align:center"><a href="edit.php">Editar Membro</a></h3>
			<h3 style="text-align:center"><a href="rem.php">Remover Membro</a></h3>
			<br>
		</div>



	</body>
	<footer>
		Copyright ©<a href="http://www.mecajun.com/" target="_blank">Mecajun</a>	
	</footer>
</html>
