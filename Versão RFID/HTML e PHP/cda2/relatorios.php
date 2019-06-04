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
			<li><a class="active" href="relatorios.php">Relatórios</a></li>
  			<li><a href="horarios.php">Horários</a></li>
  			<li><a href="editar-membro.php">Editar Membro</a></li>
  			<li><a href="relatorio-individual.php">Relatório Individual</a></li>
			<li><a href="painel.php">Painel de Controle</a></li>
			<li><a href="ajuda.html">Ajuda</a></li>
		</ul>		
		
		<div style="margin-left:5px">
				<!-- <p>Informe para qual período deseja gerar o relatorio:</p>
				<p>Deixe o segundo campo vazio para pesquisar um dia em particular</p>
				<form method="POST" action="relatorios.php">
					De: <input type="date" name="date1">
					a: <input type="date" name="date2">
					<input type="submit" value="Pesquisar" name="ok">
				</form>-->
		<p>Informe para qual dia deseja gerar o relatorio:</p>
		<form method="POST" action="relatorios.php">
			<input type="date" name="date1">
			<input type="submit" value="Pesquisar" name="ok">
		</form>
		</div>
		
	<?php
		/* Variável de acesso à database*/
		$dbconn = pg_connect("host=localhost dbname=cda user=postgres password=postgres");
	
		/* Inicia as variaveis de pesquisa como 0*/
		$day1 = 0;
		$month1 = 0;
		$year1 = 0;
		$day2 = 0;
		$month2 = 0;
		$year2 = 0;
	
		/* Setup das variaveis de pesquisa */
		if(isset($_POST['ok'])){
			if(!empty($_POST['date1'])){
				$day1 = $_POST['date1'][8] . $_POST['date1'][9];
				$month1 = $_POST['date1'][5] . $_POST['date1'][6];
				$year1 = $_POST['date1'][0] . $_POST['date1'][1] . $_POST['date1'][2] . $_POST['date1'][3];
				
				/*if(!empty($_POST['date2'])){
					$day2 = $_POST['date2'][8] . $_POST['date2'][9];
					$month2 = $_POST['date2'][5] . $_POST['date2'][6];
					$year2 = $_POST['date2'][0] . $_POST['date2'][1] . $_POST['date2'][2] . $_POST['date2'][3];				
				}*/
			}
			else{
				/* Variáveis do dia atual */
				$day1 = date('d');
				$month1 = date('m');
				$year1 = date('Y');
			}
		}
		else {
			/* Variáveis do dia atual */
			$day1 = date('d');
			$month1 = date('m');
			$year1 = date('Y');
		}

		/* Busca com base na data informada */
		if($day2 == 0){
			/* Se uma data final não foi informada */
			$query = "SELECT * FROM logsporta WHERE dia = '" . $day1 . "' AND mes = '" . $month1 . "' AND ano = '" . $year1 . "' ORDER BY ano, mes, dia, hora, min, seg";
		
			/* Print da data*/
			echo '<div style="margin-left:5px">';
			echo '<p>Relatório de ' . $day1 . '/' . $month1 . '/' . $year1 . '</p>';
			echo '</div>';
		}
		/*else {
			 Se foi específicado um período completo 
			$query_1 = "SELECT * FROM logsporta WHERE dia >= '" . $day1 . "' AND dia <= '" . $day2 . "' AND mes >= '" . $month1 . "' AND mes <= '" . $month2 . "' AND ano >= '" . $year1 . "' AND ano <= '" . $year2 . "' ";
			$query_2 = "ORDER BY ano, mes, dia, hora, min, seg";
			$query = $query_1 . $query_2;
			
			 Print da data
			echo '<div style="margin-left:5px">';
			echo '<p>Relatório de ' . $day1 . '/' . $month1 . '/' . $year1 . ' a ' . $day2 . '/' . $month2 . '/' . $year2 . '</p>';
			echo '</div>';
		}*/
		$result = pg_query($query) or die('Erro!');
		
		echo '<div style="margin-left:5px">';
		echo '<br><ul style="display:inline; font-size:13px"><table border="1" >';
		echo "<tr><th>Nome</th><th>RFID</th><th>Entrada/Saída</th><th>Dia</th><th>Mês</th><th>Ano</th><th>Hora</th><th>Minuto</th><th>Segundo</th></tr>";
		while($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
			$i = 0;
			echo '<tr>';
			foreach ($line as $col_value) {
				if($i==2){
					if($col_value == 1){
						echo '<td> Entrou </td>';
					}
					elseif($col_value == 0){
						echo '<td> Saiu </td>';
					}
					else{
						echo '<td> Ignorado </td>';
					}
				}
				else{
					echo '<td>' . $col_value . '</td>';
				}
				$i++;
			}
			echo '</tr>';
		}
		echo "\n</table>\n";
		echo '</div>';
		pg_free_result($result);

		pg_close($dbconn);
	?>	
	<br><br><br>
	</body>
	<footer>
		Copyright ©<a href="http://www.mecajun.com/" target="_blank">Mecajun</a>	
	</footer>
</html>
