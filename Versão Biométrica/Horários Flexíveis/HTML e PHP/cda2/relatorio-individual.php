<!DOCTYPE html>
<!-- Linhas alterada:
		 41, 49, 62, 137, 139, 154, 156, 117, 134, 151, 162, 165
-->

<html lang="pt-br">
	<link rel="stylesheet" type="text/css" href="Controle.css">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<head><header>
		<title>Controle de Acesso</title>
		<a class="b" href="menu.php"><h2>Controle de Acesso</h2></a>
	</header>
		<style>
			table{
				border: 1;
				text-align:center;
			}	
		</style>	
	</head>
	<body>
		<ul>
  			<li><a href="menu.php">Início</a></li>
			<li><a href="relatorios.php">Relatórios</a></li>
  			<li><a href="horarios.php">Horários</a></li>
  			<li><a href="editar-membro.php">Editar Membro</a></li>
  			<li><a class="active" href="relatorio-individual.php">Relatório Individual</a></li>
			<li><a href="painel.php">Painel de Controle</a></li>
			<li><a href="ajuda.html">Ajuda</a></li>
		</ul>
		<div style="margin-left:5px">
		<p>Pesquise o membro que desejas gerar o relatório:</p>
		<form method="POST" action="relatorio-individual.php">
			Nome Completo: <input type="text" name="nome">
			Mês: <input type="month" name="mes">
			<input type="submit" value="Pesquisar" name="foi">
		</form>
		</div>
		<?php
			/* Conexão com a DB */
			$connect = pg_connect("host=localhost dbname=cda user=postgres password=postgres");
			
			function get_day_info($id, $weekname, $day, $weeknum, $year, $month){
				/* Pesquisa no BD se existem horas fixas a serem cumpridas nesse dia */
				$query = "SELECT horain, minin, horaout, minout FROM horarios_fixos WHERE id = " . $id . " AND diasem = '" . $weekname . "'";
				$result = pg_query($query) or die('Problema no nome do dia da semana');
				
				if (pg_affected_rows($result)<=0){
					/* Não encontrou nenhum horario fixo */
					echo '<tr>';
					echo '<td>' . $day . '/' . $month . ' - ' . $weekname . '</td>';
				}
				else {
					echo '<tr style="background-color:#DCDCDC">';
					/* Contador de quantas horas o cidadao deve ter cumprido no dia */
					$cont = 0;
					while($line = pg_fetch_array($result, null, PGSQL_ASSOC)){
						$time2 = $line[horaout]*60 + $line[minout];
						$time1 = $line[horain]*60 + $line[minin];
						$cont += $time2 - $time1;
					};
					$hours = $cont/60;
					$minutes = $cont%60;
					echo '<td>' . $day . '/' . $month . ' - ' . $weekname . '</td>';
					/*echo '<td>Tempo obrigatorio: ' . $hours . ' Horas e ' . $minutes . ' Minutos.';*/
				};
				$query = "SELECT * FROM hourbank WHERE id = " . $id . " AND diasem = '" . $weekname . "' AND semana = '" . $weeknum . "' AND ano = '" . $year . "'";
				$result = pg_query($query) or die('Problema no banco de horas!');
				$line = pg_fetch_array($result, null, PGSQL_ASSOC);
				echo '<td>' . $line[hrfixo] . '</td><td>' . $line[minfixo] . '</td><td>' . $line[hrflex] . '</td><td>' . $line[minflex] . '</td><td>' . $line[hrinv] . '</td><td>' . $line[mininv] . '</td>';
				echo '</td></tr>';
				pg_free_result($result);
			}
		
			if(isset($_POST['foi'])){
				/* Protecao contra dados vazios */
				if(empty($_POST['mes']) || empty($_POST['nome'])){
					die("<script language='javascript' type='text/javascript'>alert('Por favor preencha todos os campos!');window.location.href='relatorio-individual.php';</script>");
				}
				
				/* Guarda o ID do membro em uma variavel */
				$query = "SELECT id FROM membros WHERE nome = '" . $_POST['nome'] . "'";
				$result = pg_query($query) or die('Provavelmente digitastes o nome errado');
				
				if (pg_affected_rows($result)<=0){
					/* Não encontrou nenhum membro */
					echo "<script language='javascript' type='text/javascript'>alert('Membro não encontrado');window.location.href='relatorio-individual.php';</script>";
				}
				else {
					$line = pg_fetch_array($result, null, PGSQL_ASSOC);
					$id = $line[id];
				};
				
				$tmpdate = $_POST['mes'] . "-01";
				$monthname = date('F', strtotime($tmpdate));
				
				/* Numero da primeira semana do mes e o ano no padrao ISO */
				$weeknum1 = date('W', strtotime($tmpdate));
				$year1 = date('o', strtotime($tmpdate));				
								
				/* Variavel que indica quantos dias tem o mes */
				$n_days = date('t', strtotime($tmpdate));
				
				/* Numero da ultima semana do mes e o ano no padrao ISO */
				$tmpdate = $_POST['mes'] . "-" . $n_days;
				$weeknum2 = date('W', strtotime($tmpdate));
				$year2 = date('o', strtotime($tmpdate));

				/* Caso que ocorre para os meses de Dezembro ou Janeiro*/
				$excpetion = 0;
				if($year1 != $year2){
					if($weeknum1 == 52 || $weeknum1 == 53){
						/* Mes escolhido foi Janeiro e a primeira semana e considerada como sendo do ano passado */
						$exception = 1;
					}
					elseif($weeknum2 == 1){
						/* Mes escolhido foi Dezembro e a ultima semana e considerada como sendo do ano seguinte */
						$exception = 2;
						$tmpdate = $_POST['mes'] . "-" . ($n_days - 7);
						$weeknum2 = date('W', strtotime($tmpdate));
					};
				}

				/* Inicio da Tabela do banco de horas */
				echo '<table width="50%" border="2px solid black">';
				$ano = date('o', strtotime($tmpdate));
				echo '<tr><th colspan="7">' . $monthname . '-' . $ano . '</th></tr>';
				$k = 1;
				
				/* Verificacao da primeira excecao */
				if($exception == 1){
					/* Imprimir os dados referentes a ultima semana do ano passado*/
					echo '<tr><th colspan="7">1a Semana</th></tr>';
					for($i = 1; $i<=7; $i++){
						/* Pesquisa do '$i'ésimo dia da semana */
						$str = $year1 . '-W' . sprintf("%02d", $weeknum1) . '-' . $i;
						$day = date('d', strtotime($str));
						$month = date('m', strtome($str));
						$weekname = date('D', strtotime($str));
						get_day_info($id, $weekname, $day, $weeknum1, $year1, $month);
					};
					$weeknum1 = '01';
					$year1 = $year2;
					$k = 2;
				};
				
				/* Loop para iterar entre todas as semanas que tem alguma interseccao com o mes pesquisado */
				while($weeknum1 <= $weeknum2){
					/* Imprimir os dados referentes a semana */
					echo '<tr><th colspan="7">' . $k . 'ª Semana</th></tr>';
					echo '<tr><th rowspan="2">Dia</th><th colspan="2">Fixo</th><th colspan="2">Flexível</th><th colspan="2">Inválido</th></tr>';
					echo '<tr><th>Hora</th><th>Min</th><th>Hora</th><th>Min</th><th>Hora</th><th>Min</th></td></tr>';
					for($i = 1; $i<=7; $i++){
						/* Pesquisa do '$i'ésimo dia da semana */
						$str = $year1 . '-W' . sprintf("%02d", $weeknum1) . '-' . $i;
						$day = date('d', strtotime($str));
						$month = date('m', strtotime($str));
						$weekname = date('D', strtotime($str));
						get_day_info($id, $weekname, $day, $weeknum1, $year1, $month);
					};
					$k++;			
					$weeknum1++;
				};
				
				if($exception == 2){
					/* Imprimir os dados referentes a primeira semana do ano seguinte*/
					echo '<tr><th colspan="7">' . $k . 'a Semana</th></tr>';
					for($i = 1; $i<=7; $i++){
						/* Pesquisa do '$i'ésimo dia da semana */
						$str = $year2 . '-W01-' . $i;
						$day = date('d', strtotime($str));
						$weekname = date('D', strtotime($str));
						get_day_info($id, $weekname, $day, 01, $year1 + 1);
					};
				};
				
				echo '</table>';
			}else{
				/* Tabela para ver o nome dos membros */
				$query = "SELECT nome FROM membros";
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
			pg_close($connect);
		?>
		<br><br><br><br>
	</body>
	<footer>
		Copyright ©<a href="http://www.mecajun.com/" target="_blank">Mecajun</a>	
	</footer>
</html>
