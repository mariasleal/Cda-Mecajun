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

		<div style="margin-left:5px">
		<p>Pesquise o usuário que desejas editar:</p>
		<form method="POST" action="editu.php">
			<input type="text" name="nome" title="Coloque o nome do usuário ou copie da tabela abaixo">
			<input type="submit" value="Pesquisar" name="foi">
		</form>
		</div>
	<?php
		/* Conexão com a DB */
		$connect = pg_connect("host=localhost dbname=cda user=postgres password=postgres");
		$query = "SELECT * FROM usuarios WHERE usuario = '" . $_COOKIE['login'] . "' AND permissao > 2";
		$verifica = pg_query($query) or die("Erro na identificação do usuário!");
		if (pg_affected_rows($verifica)<=0){
			echo"<script language='javascript' type='text/javascript'>alert('Você não possui permissão para realizar esta operação!');window.location.href='menu.php';</script>";
			die();
		}
		
		/* Array para ser preenchido com os dados dos usuários */
		$colunas = array("Usuario","Senha", "Permissão");
		$i = 0;

		if(isset($_POST['send'])){
			/* Alterações sobre o usuário */

			/* Proteção contra caracteres especiais */
			if(!empty($_POST[$colunas[0]])){
				if(strpbrk($_POST[$colunas[0]], "\',:.<>/?\"") != FALSE){
					die("<script language='javascript' type='text/javascript'>alert('Favor não utilizar caracteres especiais!');window.location.href='editu.php';</script>");
				}
			}
			if(!empty($_POST[$colunas[1]])){
				if(strpbrk($_POST[$colunas[1]], "\',:.<>/?\"") != FALSE){
					die("<script language='javascript' type='text/javascript'>alert('Favor não utilizar caracteres especiais!');window.location.href='editu.php';</script>");
				}
			}

			/* Proteção contra login repetido */
			if(!empty($_POST[$colunas[0]])){
				$query = "SELECT * FROM usuarios WHERE usuario = '" . $_POST[$colunas[0]] . "'";
				$result = pg_query($query) or die("<script language='javascript' type='text/javascript'>alert('Erro!');window.location.href='editu.php';</script>");
				if(pg_num_rows($result) > 0){
					pg_free_result($result);
					die("<script language='javascript' type='text/javascript'>alert('Erro! Login em uso.');window.location.href='editu.php';</script>");
				}
				pg_free_result($result);				 
			}	

			/* Adiciona no historico a mudanca a ser realizada */
			$hist1 = "INSERT INTO historico VALUES('" .  $_COOKIE['login'] . "', NOW(), NOW(), 'Modificou os dados do usuario ''" . $_POST[$colunas[0]] . "''";
			/* Compara o nome antigo do usuario com o novo */
			if(strcmp($_COOKIE['edit'], $_POST[$colunas[0]]) != 0){
				/* Nomes diferentes */
				$hist1 = $hist1 . " (''" . $_COOKIE['edit'] . "'')')";
			}
			else {
				$hist1 = $hist1 . "')";
			}
			/*echo $hist1;*/
			$hist_r = pg_query($hist1) or die("Erro!");
			pg_free_result($hist_r);	
			
			/* Vetor com os novos dados a serem inseridos */
			$colunasbd = array("usuario","senha","permissao");
			$query_atualizar = "UPDATE usuarios SET ";

			/* Loop de preenchimento da query */
			for($i=0;$i<3;$i=$i+1){
				if(!empty($_POST[$colunas[$i]])){
					$query_atualizar .= " " . $colunasbd[$i]. " = '". $_POST[$colunas[$i]] . "',"; 
					if($i==0){						
						/* Altera os históricos com o nome antigo do usuário */
						$queryhis = "UPDATE historico SET usuario = '" . $_POST[$colunas[$i]] . "' where usuario = '" . $_COOKIE['edit'] ."'";
						$hishis = pg_query($queryhis) or die("historico nao atualizados");
						pg_free_result($hishis);
					}
				}	
			}
			$query_atualizar = chop($query_atualizar,",");
			$query_atualizar .= " WHERE usuario = '". $_COOKIE['edit'] ."'";
			/* echo $query_atualizar; */
			$away = pg_query($query_atualizar) or die("Erro!");
			pg_free_result($away);

			echo "Usuario Atualizado";

		}elseif(isset($_POST['foi'])){
			/* O usuário clicou para editar outros usuários */
			
			$query = "SELECT * FROM usuarios WHERE usuario='" . $_POST['nome'] . "'";
			
			/* 'Seta' um cookie com o nome do usuário a ser alterado */
			setcookie("edit",$_POST['nome']);
			
			
			$result = pg_query($query) or die('Erro!');
			echo '<form method="POST" action="editu.php">';
			echo "<br><table>\n";
			$i = 0;
			while($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {

				foreach ($line as $col_value) {
					if($i == 2){
						echo '<tr><td>' . $colunas[$i] . '</td><td>| ' . $col_value . '</td><td> | <input type="text" title="Alterar a permissão irá afetar o acesso futuro" name="' . $colunas[$i] . '"></td></tr>';
					}else{
						echo '<tr><td>' . $colunas[$i] . '</td><td>| ' . $col_value . '</td><td> | <input type="text" name="' . $colunas[$i] . '"></td></tr>';
					}
					$i = $i+1;
				}

			}
			echo "\n</table>\n";
			echo '<input type="submit" name="send" value="Enviar">';
			echo "</form>";
			pg_free_result($result);	
			
		}else{
			/* Imprime a tabela de usuários */
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
		pg_close($connect);

	?>
	
	</body>
	<footer>
		Copyright ©<a href="http://www.mecajun.com/" target="_blank">Mecajun</a>	
	</footer>
</html>
