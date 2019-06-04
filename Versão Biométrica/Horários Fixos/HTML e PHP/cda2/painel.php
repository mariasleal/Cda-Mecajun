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
			.m3 h3{
				border: 1px solid gray;
				padding: 5px;
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

		<div>
			<table class="m3">
			<tr>
				<td><a style="text-align:center;color:black" href="addu.php"><h3>Adicionar Usuario</h3></a></td>
				<td><a style="text-align:center;color:black" href="editu.php"><h3>Editar Usuario</h3></a></td>
				<td><a style="text-align:center;color:black" href="remu.php"><h3>Remover Usuario</h3></a></td>
			</tr>
			</table>
		</div>
		<form method="POST" action="painel.php">
			<div style="margin-left:5px">
			<h3>Alterações no período de:</h3>
			<p>Data Inicio: <input type="date" name="data1">
			Data Final: <input type="date" name="data2"></p>
			<input type="submit" value="Pesquisar" name="foi">
			</div>
		</form>
		<br>
	<?php
		/* Conexão e verificação de permissão */
		$connect = pg_connect("host=localhost dbname=cda user=postgres password=postgres");
		$query = "SELECT * FROM usuarios WHERE usuario = '" . $_COOKIE['login'] . "' AND permissao > 2";
		$verifica = pg_query($query) or die("Erro na identificação do usuário!");
		if (pg_affected_rows($verifica)<=0){
			echo"<script language='javascript' type='text/javascript'>alert('Você não possui permissão para realizar esta operação!');window.location.href='menu.php';</script>";
			die();
		}

		if(isset($_POST['foi'])){
			/* Se o usuário escolheu uma data */
			$query = "SELECT * FROM historico WHERE date BETWEEN '" . $_POST['data1'] . "' AND '" . $_POST['data2'] . "' ORDER BY date DESC, hora DESC";
			$result = pg_query($query) or die('Erro na busca do mês');
			echo '<div style="margin-left:5px">';
			echo '<br><table style="text-align:center"><table border="1">';
			echo '<tr><th colspan="4">Modificações no período selecionado:</th></tr>';
			echo "<tr><th>Usuario</th><th>Data (YYYY-MM-DD)</th><th>Hora</th><th>Modificacao</th></tr>";
			while($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
				echo "\t<tr>\n";
				foreach ($line as $col_value) {
					echo "\t\t<td>$col_value</td>\n";
				}
				echo "\t</tr>\n";
			}
			echo "\n</table>\n";
			echo '</div>';
			pg_free_result($result);
		}else{
			/* Se o usuário não escolheu uma data, mostra as 10 últimas modificações do mês atual */
			
			/* Variáveis de data com base no horário da máquina */
			$month = date('m');
			$year = date('Y');
			
			/* Pega o ultimo dia do mes*/
			$a_date = $year . "-" . $month . "-01";
			$n_days = date('t', strtotime($a_date));
			
			/* Datas de inicio e fim do mes */
			$firstdate = $year . "-" . $month . "-01";
			$seconddate = $year . "-" . $month . "-" . $n_days;
			
			$i = 0;
			$query1 = "SELECT * FROM Historico WHERE date BETWEEN '" . $firstdate . "' AND '" . $seconddate . "' ORDER BY date DESC, hora DESC";
			$result1 = pg_query($query1) or die('Não foi possível acessar o historico');
			echo '<div style="margin-left:5px">';
			echo '<br><table style="text-align:center"><table border="1">';
			echo '<tr><th colspan="4">Últimas Modificações</th></tr>';
			echo "<tr><th>Usuario</th><th>Data (YYYY-MM-DD)</th><th>Hora</th><th>Modificacao</th></tr>";
			while($line = pg_fetch_array($result1, null, PGSQL_ASSOC)) {
				if($i>9){
					/* Mostra só 10 atualizações */
					break;
				}
				echo "\t<tr>\n";
				$i = $i + 1;
				foreach ($line as $col_value) {
					echo "\t\t<td>$col_value</td>\n";
				}
				echo "\t</tr>\n";
			}
			echo "\n</table>\n";
			echo '</div>';
			pg_free_result($result1);
		}
		pg_close($connect);
	?>
	<br><br><br>
	</body>
	<footer>
		Copyright ©<a href="http://www.mecajun.com/" target="_blank">Mecajun</a>	
	</footer>
</html>

