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
		<div style="margin-left:5px">
		<p>Pesquise o membro que desejas editar:</p>
		<form method="POST" action="edit.php">
			<input type="text" name="nome">
			<input type="submit" value="Pesquisar" name="foi">
		</form>
		</div>
	<?php
		/* Variável de conexão */
		$connect = pg_connect("host=localhost dbname=cda user=postgres password=postgres");
		/* Permissão 1 para editar membro */
		$query = "SELECT * FROM usuarios WHERE usuario = '" . $_COOKIE['login'] . "' AND permissao > 1";
		$verifica = pg_query($query) or die("Erro na identificação do usuário!");
		if (pg_affected_rows($verifica)<=0){
			echo"<script language='javascript' type='text/javascript'>alert('Você não possui permissão para realizar esta operação!');window.location.href='menu.php';</script>";
			die();
		}
		if (isset($_POST['digbutton'])){

				$query = "SELECT * FROM membros WHERE dig_pos = '-1'";
				echo $query;
				$result = pg_query($query) or die('Erro novo!');
				if (pg_affected_rows($result)>0){
					echo "<script language='javascript' type='text/javascript'>alert('Digital pendente para ser cadastrada!');window.location.href='edit.php';</script>";
				}
				else{
					$query = "UPDATE membros SET dig_pos = '-1' WHERE nome = '" . $_COOKIE['edit'] . "'";
					echo $query;
					$result = pg_query($query) or die('Erro novo!');
					echo"<script language='javascript' type='text/javascript'>alert('Siga as instruções na tela LCD');window.location.href='edit.php';</script>";
				}
		}
		$i = 0;
		if(isset($_POST['newsend'])){
			if(!empty($_POST['newnome'])){
				/* Se o usuário escolheu um novo nome */
				
				/* Tratamento de erro para nome já utilizado */
				$query = "SELECT * FROM membros WHERE nome = '" . $_POST['newnome'] . "'";
				$verifica = pg_query($query);
				if (pg_affected_rows($verifica)>0){
					/* Membro com nome igual já existe */
					echo"<script language='javascript' type='text/javascript'>alert('Erro! Membro já existe!');window.location.href='edit.php';</script>";
					die();
				}
				
				/* Atualização da tabela membros: */
				$query = "update membros set nome = '" . $_POST['newnome'] . "' where id = " . $_COOKIE['id'];
				$result = pg_query($query) or die('Erro para trocar o nome! #0');	
				
				/* Atualização da tabela de pontos: */
				$query = "update pontos set nome = '" . $_POST['newnome'] . "' where nome = '" . $_COOKIE['edit'] . "'";
				$result = pg_query($query) or die('Erro para trocar o nome! #2');									
				
				/* Atualização da tabela de logs da porta: */
				$query = "update logsporta set nome = '" . $_POST['newnome'] . "' where nome = '" . $_COOKIE['edit'] . "'";
				$result = pg_query($query) or die('Erro para trocar o nome! #3');				
			
				/* Atualização do histórico */
				$query_hist = "INSERT INTO historico VALUES ('" . $_COOKIE['login']. "', NOW(), NOW(), 'Alterou o nome do membro ''" . $_COOKIE['edit'] . "'' para ''" . $_POST['newnome'] . "''')";
				$result = pg_query($query_hist) or die("Erro ao inserir no historico!");
				
				pg_free_result($result);				
			}

		}

		if(isset($_POST['add'])){
			/*echo "vai?";*/
			if(!empty($_POST['horain']) && !empty($_POST['minin']) && !empty($_POST['horaout']) && !empty($_POST['minout']) && isset($_POST['Day'])){
				/*echo "foi sim!";*/
				$query = "insert into horarios values(" . $_COOKIE['id'] . ",'" . $_POST['Day'] . "','" . $_POST['horain'] . "','" . $_POST['minin'] . "','" . $_POST['horaout'] . "','" . $_POST['minout'] . "')"; 
				$result = pg_query($query) or die('erro!');
				pg_free_result($result1);
				
				/* Atualização do histórico */
				$query_hist = "INSERT INTO historico VALUES ('" . $_COOKIE['login']. "', NOW(), NOW(), 'Adicionou um novo horário para o membro ''" . $_COOKIE['edit'] . "'': (" . $_POST['Day'] . "/" . $_POST['horain'] . ":" . $_POST['minin'] . "->" . $_POST['horaout'] . ":" . $_POST['minout'] . ")')";
				$result = pg_query($query_hist) or die("Erro ao inserir no historico!");
				
				pg_free_result($result);				
				
				echo"<script language='javascript' type='text/javascript'>alert('Horário Adicionado!');window.location.href='edit.php';</script>";
			}
			else {
				echo"<script language='javascript' type='text/javascript'>alert('Por favor preencha todos os campos com 2 digitos!');window.location.href='edit.php';</script>";
				die();
			}
			/*echo "foi?";*/
		}
		elseif(isset($_POST['onlyone'])){
			/* Query para encontrar os horários */
			$query = "SELECT * FROM horarios WHERE id = " . $_COOKIE['id'] . " ORDER BY dia, horain, minin, horaout, minout";
			$result = pg_query($query) or die('Erro!');
			
			/* Loop para apagar o horario selecionado */
			$i = 1;
			while($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
				if($i == $_POST['time']){
					$dia = $line[dia];
					$horain = $line[horain];
					$minin = $line[minin];
					$horaout = $line[horaout];
					$minout = $line[minout];	
					break;				
				}
				$i++;
			}
			pg_free_result($result);
			
			/* Remove o horario */
			$query = "DELETE FROM horarios WHERE id = " . $_COOKIE['id'] . " AND dia = '" . $dia . "' AND horain = '" . $horain . "' AND minin = '" . $minin . "' AND horaout = '" . $horaout . "' AND minout = '" . $minout . "'";
			$result = pg_query($query) or die('Erro!');
			
			pg_free_result($result);

			/* Atualização do histórico */
			$query_hist = "INSERT INTO historico VALUES ('" . $_COOKIE['login']. "', NOW(), NOW(), 'Removeu um horário do membro ''" . $_COOKIE['edit'] . "'': (" . $dia . "/" . $horain . ":" . $minin . "->" . $horaout . ":" . $minout . ")')";
			$result = pg_query($query_hist) or die("Erro ao inserir no historico!");
			
			pg_free_result($result);				
			
		}elseif(isset($_POST['cancel'])){
			echo"<script language='javascript' type='text/javascript'>alert('Operação cancelada pelo usuário!');window.location.href='editar-membro.php';</script>";
		}elseif(isset($_POST['all'])){

			$query = "DELETE FROM horarios WHERE id = " . $_COOKIE['id'];
			/*echo $query;*/
			$result1 = pg_query($query) or die();
			pg_free_result($result1);
			
			/* Atualização do histórico */
			$query_hist = "INSERT INTO historico VALUES ('" . $_COOKIE['login']. "', NOW(), NOW(), 'Deletou todos os horários (" . $_COOKIE['count'] . ") do membro ''" . $_COOKIE['edit'] . "''')";
			$result = pg_query($query_hist) or die("Erro ao inserir no historico!");
			
			pg_free_result($result);			
			
			echo"<script language='javascript' type='text/javascript'>alert('Horários Removidos!');window.location.href='edit.php';</script>";
		
		}elseif(isset($_POST['send'])){/*Apagar horários*/
			/* Query para verificar os horários */
			$query = "SELECT * FROM horarios WHERE id = " . $_COOKIE['id'] . " ORDER BY dia, horain, minin, horaout, minout";
			$result = pg_query($query) or die('Erro!');
			$n_results = pg_num_rows($result);
			
			/* Seta um cookie com o número de horários do membro */
			setcookie("count",$n_results);
			
			echo '<h4>Quais horários deseja apagar?</h4><br>';
			echo '<form method="POST" action="edit.php">';
			
			/* Loop para escolher entre os horários*/
			$i = 1;
			while($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
				if($i == 1){
					echo '<input type="radio" name="time" value="' . $i . '" checked> ' . $line[dia] . ' ( ' . $line[horain] . ':' . $line[minin] . ' -> ' . $line[horaout] . ':' . $line[minout] . ' )<br>';
				}
				else {
					echo '<input type="radio" name="time" value="' . $i . '"> ' . $line[dia] . ' ( ' . $line[horain] . ':' . $line[minin] . ' -> ' . $line[horaout] . ':' . $line[minout] . ' )<br>';
				}
				$i++;
			}	
			
			echo '<br><input type="submit" value="Remover" name="onlyone"><br><br>';
			echo '<input type="submit" value="Remover todos" name="all"><input type="submit" value="Cancelar" name="cancel"></form>';

		}elseif(isset($_POST['foi'])){
			/* Guarda o id do membro que o usuário escolheu em idfunc */
			$query = "SELECT id FROM membros WHERE nome='" . $_POST['nome'] . "'";
			setcookie("edit",$_POST['nome']);
			$result = pg_query($query) or die('Erro!');
			
			/* Se não encontrar nenhum membro com esse nome */
			if (pg_affected_rows($result)<=0){
				echo"<script language='javascript' type='text/javascript'>alert('Nenhum mebro encontrado com esse nome!');window.location.href='edit.php';</script>";
				die();
			}	
				
			$bu = pg_fetch_array($result, null, PGSQL_ASSOC);
			foreach($bu as $xu){
				$idfunc = $xu;
			}
			
			echo '<p style="margin-left:5px">Nome do Membro:</p>';
			echo '<p style="margin-left:5px">' . $_POST['nome'] . '</p>';
			$query = "select * from horarios where id = " . $idfunc;
			$result = pg_query($query) or die('Erro!');
			
			/* Campo para modificar o nome do membro, variável guardada como newnome e o envio guardado como newsend*/
			echo '<form method="POST" action="edit.php">';
			echo ' <input type="text" name="newnome" style="margin-left:5px"> '; 
			echo '<input type="submit" name="newsend" value="Atualizar nome"><br>';
			
			/* Seta o id do membro como cookie */
			setcookie("id",$idfunc);
			
			/* Tabela que mostra os horarios do membro */
			if (pg_affected_rows($result)<=0){
				echo "Nenhum horário cadastrado!";
			}else{
				echo '<br><table border="1" style="text-align:center; margin-left:5px"><tr><th colspan="3">Horarios</th></tr><tr><th >Dia</th><th>Entrada</th><th>Saída</th></tr>';
				while($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
					echo '<tr><th>' . $line[dia] . '</th><td>' . $line[horain] . ':' . $line[minin] . '</td><td>' . $line[horaout] . ':' . $line[minout] . '</td></tr>';
				}
				echo "\n</table><br>\n";/*Adicionar classe sharingan no controle.css*/
				echo '<input type="submit" name="send" value="Apagar Horários"><br>';
			}
			
			/* Tabela para adicionar horários */
			echo '<br><div class="sharingan"><table><tr><td colspan="8">Adicionar Horario</td></tr><tr>';
			echo '<td>Dia da Semana:</td><td><input type="radio" name="Day" value="Sun">Domingo</td><td><input type="radio" name="Day" value="Mon">Segunda-feira</td><td><input type="radio" name="Day" value="Tue">Terça-feira</td><td><input type="radio" name="Day" value="Wed">Quarta-feira</td><td><input type="radio" name="Day" value="Thu">Quinta-feira</td><td><input type="radio" name="Day" value="Fri">Sexta-feira</td><td><input type="radio" name="Day" value="Sat">Sábado</td></tr>';
			echo '</table><table><tr><td>Hora de Entrada:</td><td><input type="number" name="horain"></td><td>Minuto de Entrada:</td><td><input type="number" name="minin"</td></tr><tr><td>Hora de Saída:</td><td><input type="number" name="horaout"></td><td>Minuto de Saída:</td><td><input type="number" name="minout"></td></tr>';
			echo '<td><input type="submit" name="add" value="Adicionar Horário"></td></tr></table></form></div>';
			pg_free_result($result);
			echo '<form method="POST" action="edit.php">';
			echo '<input type="submit" value="Alterar Digital" name="digbutton">';
			echo '</form>';




		}else{
			/* Tabela que mostra o nome dos membros */
			$query = "SELECT nome FROM membros WHERE id > 0";
			$result = pg_query($query) or die('Erro!');
			echo '<br><table border="1" style="margin-left:10px"><tr><th>Membros:</th></tr>';
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
