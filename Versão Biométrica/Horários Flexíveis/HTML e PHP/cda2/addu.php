<!DOCTYPE html>
<html lang="pt-br">
	<head><header>
		<link rel="stylesheet" type="text/css" href="Controle.css">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Controle de Acesso</title>
		<a class="b" href="menu.php"><h2>Controle de Acesso</h2></a>

	</header>
		<style>
			.m3 td{
				padding: 15px;
			}
		</style>
	</head>
	<body>
		<ul>
  			<li><a href="menu.php">Início</a></li>
			<li><a href="relatorios.php">Relatórios</a></li>
  			<li><a href="horarios.php">Horários</a></li>
  			<li><a href="editar-membro.php">Editar Membro</a></li>
  			<li><a href="relatorio-individual.php">Relatório Individual</a></li>
			<li><a class="active" href="painel.php">Painel de Controle</a></li>
			<li><a href="ajuda.html">Ajuda</a></li>
		</ul>
		<form method="POST" action="addu.php">
			<table>
			<tr><td>Novo Usuario:</td><td><input type="text" name="nome"></td></tr>
			<tr><td>Senha:</td><td><input type="text" name="senha"></td></tr>
			<tr><td>Permissão (1,2 ou 3):</td><td><input type="text" name="perm"></td></tr>
			</table>
			<input type="submit" value="Adicionar Usuário" name="send">
		</form>
		<?php
			/* Conexão para adicionar usuário */ 
			$dbconn = pg_connect("host=localhost dbname=cda user=postgres password=postgres");
			$query = "SELECT * FROM usuarios WHERE usuario = '" . $_COOKIE['login'] . "' AND permissao > 2";
			$verifica = pg_query($query) or die("erro ao selecionar");
			if (pg_affected_rows($verifica)<=0){
				echo"<script language='javascript' type='text/javascript'>alert('Você não possui permissão para realizar esta operação!');window.location.href='menu.php';</script>";
				die();
			}
			
			/* Proteção contra caracteres especiais */
			if((strpbrk($_POST['senha'], "\',:.<>/?\"") != FALSE) || (strpbrk($_POST['nome'], "\',:.<>/?\"") != FALSE)){
				die("<script language='javascript' type='text/javascript'>alert('Favor não utilizar caracteres especiais!');window.location.href='addu.php';</script>");
			}			
			
			if(isset($_POST['send'])){
				if(!empty($_POST['nome']) && !empty($_POST['senha']) && !empty($_POST['perm'])){
					/* Proteção contra nomes de usuários repetidos */
					$query = "SELECT * FROM usuarios WHERE usuario = '" . $_POST['nome'] . "'";
					$result = pg_query($query) or die("<script language='javascript' type='text/javascript'>alert('Erro!');window.location.href='addu.php';</script>");
					if(pg_num_rows($result) > 0){
						pg_free_result($result);
						die("<script language='javascript' type='text/javascript'>alert('Erro! Login em uso.');window.location.href='addu.php';</script>");
					}
					pg_free_result($result);
					
					$query = "INSERT INTO usuarios VALUES('". $_POST['nome'] . "','" . $_POST['senha'] . "'," . $_POST['perm'] . ")";
					$away = pg_query($query) or die("<script language='javascript' type='text/javascript'>alert('Erro!');window.location.href='addu.php';</script>");
					pg_free_result($away);
					
					/* Adiciona no histórico */
					$query_hist = "INSERT INTO historico VALUES ('" . $_COOKIE['login']. "', NOW(), NOW(), 'Adicionou o novo usuário ''" . $_POST['nome'] . "''')";
					$result = pg_query($query_hist) or die("Erro ao inserir no historico!");
					
					pg_free_result($result);
					
					echo"<script language='javascript' type='text/javascript'>alert('Usuario Adicionado!');window.location.href='painel.php';</script>";
				}else{
					echo "<script language='javascript' type='text/javascript'>alert('Erro! Campo(s) em branco!');window.location.href='addu.php';</script>";
				}
			}
			pg_close($dbconn);
		?>

	
	</body>
	<footer>
		Copyright ©<a href="http://www.mecajun.com/" target="_blank">Mecajun</a>	
	</footer>
</html>
