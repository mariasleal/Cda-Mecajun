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
		<form method="POST" action="add.php">
			<table>
			<tr><td>Nome:</td><td><input type="text" name="nome"></td></tr>
			</table>
			<input type="submit" value="Criar Membro" name="send">
		</form>
		<?php
			/* Variável de acesso à DB */
			$dbconn = pg_connect("host=localhost dbname=cda user=postgres password=postgres");
			/* Usuário precisa de permissão 2 */
			$query = "SELECT * FROM usuarios WHERE usuario = '" . $_COOKIE['login'] . "' AND permissao > 2";
			$verifica = pg_query($query) or die("Erro na identificação do usuário!");
			if (pg_affected_rows($verifica)<=0){
				echo"<script language='javascript' type='text/javascript'>alert('Você não possui permissão para realizar esta operação!');window.location.href='menu.php';</script>";
				die();
			}
			$query = "SELECT * FROM membros WHERE rfid = -1";
			$verifica = pg_query($query) or die("Erro!");
			if (pg_affected_rows($verifica)>0){
				echo"<script language='javascript' type='text/javascript'>alert('Cadastre primeiro o cartão do membro pendente antes de adicionar um novo');window.location.href='menu.php';</script>";
				die();
			}
		
			if(isset($_POST['send'])){
				if(!empty($_POST['nome'])){
					/* Seleciona os membros com o nome desejado */
					$query = "select * from membros where nome = '" . $_POST['nome'] . "'";
					$verifica = pg_query($query);
					if (pg_affected_rows($verifica)>0){
						/* Membro com nome igual já existe */
						echo"<script language='javascript' type='text/javascript'>alert('Erro! Membro já existe!');window.location.href='add.php';</script>";
						die();
					}
					do{
						/* Gera um id "aleatório" para o novo membro*/
						$novoID = rand(1,200);
						/* Procura se o id já está sendo utilizado até encontrar um que não esteja */
						$query = "select from membros where ID = " . $novoID;
						$verifica = pg_query($query);
					}while(pg_affected_rows($verifica)>0);
				
					/* Adiciona o novo membro na DB com código RFID = -1 e ID aleatório não repetido */
					$query = "INSERT INTO membros VALUES('". $_POST['nome'] . "',-1," . $novoID . ", -1)";
					$away = pg_query($query) or die("<script language='javascript' type='text/javascript'>alert('Erro! Não foi possível inserir um novo membro');window.location.href='add.php';</script>");
					pg_free_result($away);
					
					/* Adiciona no histórico */
					$query_hist = "INSERT INTO historico VALUES ('" . $_COOKIE['login']. "', NOW(), NOW(), 'Adicionou o novo membro ''" . $_POST['nome'] . "''')";
					$result = pg_query($query_hist) or die("Erro ao inserir no historico!");
					
					pg_free_result($result);
					
					pg_close($dbconn);
					echo"<script language='javascript' type='text/javascript'>alert('Membro Adicionado!');window.location.href='editar-membro.php';</script>";
					/*header("Location:editar-membro.php");*/
				}else{
					echo "<script language='javascript' type='text/javascript'>alert('Erro! Campo(s) em branco!');window.location.href='add.php';</script>";
				}
		
			}
		?>


	</body>
	<footer>
		Copyright ©<a href="http://www.mecajun.com/" target="_blank">Mecajun</a>	
	</footer>
</html>
