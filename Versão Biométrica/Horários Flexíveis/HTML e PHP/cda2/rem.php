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
	<h3>Remover Membro</h3>
	<form method="POST" action="rem.php">
		<table>
		<tr><td>Nome:</td><td><input type="text" name="nome"></td></tr>
		</table>
		<input type="submit" value="Remover Membro" name="send">
	<?php
		/* Variável de conexão */
		$dbconn = pg_connect("host=localhost dbname=cda user=postgres password=postgres");
		/* Permissão 2 para remover membro */
		$query = "SELECT * FROM usuarios WHERE usuario = '" . $_COOKIE['login'] . "' AND permissao > 2";
		$verifica = pg_query($query) or die("Erro na identificação do usuário!");
		if (pg_affected_rows($verifica)<=0){
			echo"<script language='javascript' type='text/javascript'>alert('Você não possui permissão para realizar esta operação!');window.location.href='editar-membro.php';</script>";
			die();
		}
		if(isset($_POST['nope'])){
			echo"<script language='javascript' type='text/javascript'>alert('Operação cancelada pelo usuário!');window.location.href='editar-membro.php';</script>";
		}elseif(isset($_POST['yep'])){
			/* Apaga os horários do membro */
			
			$query = "SELECT id FROM membros WHERE nome = '" . $_COOKIE['rfunc'] . "'";
			$result = pg_query($query) or die('Erro!');
			
			/* Se não encontrar nenhum id para o usuário com esse nome */
			if (pg_affected_rows($result)<=0){
				echo"<script language='javascript' type='text/javascript'>alert('Erro ao apagar os dados do membro');window.location.href='edit.php';</script>";
				die();
			}		
			$bu = pg_fetch_array($result, null, PGSQL_ASSOC);
			foreach($bu as $xu){
				$idfunc = $xu;
			}
			
			$query1 = "DELETE FROM horarios WHERE id = '" . $idfunc . "'";
			$result1 = pg_query($query1) or die();
			pg_free_result($result1);
			
			/* Deleta o membro */
			$novoID2 = rand(-200,-1);
			$query = "UPDATE membros SET id =" . $novoID2 . "WHERE nome = '" . $_COOKIE['rfunc'] . "'";
			/*echo $query;*/
			$result = pg_query($query) or die();
			pg_free_result($result);
			
			/* Adiciona no histórico */
			$query_hist = "INSERT INTO historico VALUES ('" . $_COOKIE['login']. "', NOW(), NOW(), 'Removeu o membro ''" . $_COOKIE['rfunc'] . "''')";
			$result = pg_query($query_hist) or die("Erro ao inserir no historico!");
			
			pg_free_result($result);			
			
			echo"<script language='javascript' type='text/javascript'>alert('Membro Removido!');window.location.href='editar-membro.php';</script>";
			/*header("Location:editar-membro.php");*/
		}elseif(isset($_POST['send'])){
			$query = "SELECT * FROM membros WHERE nome = '" . $_POST['nome'] . "'";
			$existefunc = pg_query($query) or die("Erro na verificacao do membro");
			if (pg_affected_rows($existefunc)<=0){
				echo"<script language='javascript' type='text/javascript'>alert('Membro não encontrado!');window.location.href='rem.php';</script>";
			}else{
				/* Guarda o nome do membro a ser removido */
				echo $_POST['nome'];
				echo '<h4>Deseja realmente remover o membro?</h4><input type="submit" value="SIM" name="yep"><input type="submit" value="NÃO" name="nope">';
				setcookie("rfunc",$_POST['nome']);
			}
		}else{
			/* Tabela para indicar os membros cadastrados */
			$query = "SELECT nome FROM membros WHERE id > 0";
			$result = pg_query($query) or die('Erro!');
			echo '<br><br><table border="1" style="margin-left:10px"><tr><th>Membros:</th></tr>';
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
