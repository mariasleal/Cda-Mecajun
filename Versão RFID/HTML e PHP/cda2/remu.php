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
	<h3>Remover Usuario</h3>
	<form method="POST" action="remu.php">
		<table>
		<tr><td>Nome:</td><td><input type="text" name="nome"></td></tr>
		</table>
		<input type="submit" value="Remover Usuario" name="send">
	<?php
		/* Conexao com a DB e verificacao da permissao do usuario */
		$dbconn = pg_connect("host=localhost dbname=cda user=postgres password=postgres");
		$query = "SELECT * FROM usuarios WHERE usuario = '" . $_COOKIE['login'] . "' AND permissao > 2";
		$verifica = pg_query($query) or die("erro ao selecionar");
		if (pg_affected_rows($verifica)<=0){
			echo"<script language='javascript' type='text/javascript'>alert('Você não possui permissão para realizar esta operação!');window.location.href='menu.php';</script>";
			die();
		}
		
		if(isset($_POST['nope'])){
			/* Remoção descontinuada */
			echo"<script language='javascript' type='text/javascript'>alert('Operação cancelada pelo usuário!');window.location.href='painel.php';</script>";
		}elseif(isset($_POST['yep'])){
			/* Remoção confirmada */
			$query = "DELETE FROM usuarios WHERE usuario = '" . $_COOKIE['rfunc'] . "'";
			/*echo $query;*/
			$result1 = pg_query($query) or die();
			pg_free_result($result1);
			
			/* Inserção da modificação no historico*/
			$query_hist = "INSERT INTO historico VALUES ('" . $_COOKIE['login']. "', NOW(), NOW(), 'Removeu o usuário: " . $_COOKIE['rfunc'] . "')";
			$result = pg_query($query_hist) or die("Erro ao inserir no historico!");
			
			pg_free_result($result);			
			
			echo"<script language='javascript' type='text/javascript'>alert('Usuario Removido!');window.location.href='painel.php';</script>";
		}elseif(isset($_POST['send'])){
			if ($_POST['nome'] == $_COOKIE['login']){
				echo"<script language='javascript' type='text/javascript'>alert('Não é possível se remover!');window.location.href='painel.php';</script>";
			}else{
				echo $_POST['nome'];
				echo '<h4>Deseja realmente remover o usuario?</h4><input type="submit" value="SIM" name="yep"><input type="submit" value="NÃO" name="nope">';
				setcookie("rfunc",$_POST['nome']);
			}
		}else{
			$query = "SELECT usuario FROM usuarios";
			$result = pg_query($query) or die('Erro!');
			echo '<br><br><table border="1" style="margin-left:10px"><tr><th>Usuários:</th></tr>';
			while($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
				foreach ($line as $col_value) {
					echo '<tr><td>' . $col_value . '</td></tr>';
				}

			}
			echo "\n</table>\n";
			pg_free_result($result);
		}
		pg_close($dbconn);

	?>
	</form>
	
	</body>
	<footer>
		Copyright ©<a href="http://www.mecajun.com/" target="_blank">Mecajun</a>	
	</footer>
</html>
