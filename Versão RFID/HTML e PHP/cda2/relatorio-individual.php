<!DOCTYPE html>
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
		<p>Pesquise o membro que desejas gerar o relatorio:</p>
		<form method="POST" action="relatorio-individual.php">
			Nome Completo: <input type="text" name="nome">
			Mês: <input type="month" name="mes">
			<input type="submit" value="Pesquisar" name="foi">
		</form>
		</div>
		<?php
			/* Conexão com a DB */
			$connect = pg_connect("host=localhost dbname=cda user=postgres password=postgres");
			if(isset($_POST['foi'])){
				/* Protecao contra dados vazios */
				if(empty($_POST['mes']) || empty($_POST['nome'])){
					die("<script language='javascript' type='text/javascript'>alert('Por favor preencha todos os campos!');window.location.href='relatorio-individual.php';</script>");
				}
				
				/* Gera o relatório do membro para um determinado mês */
				$mes = $_POST['mes'][5] . $_POST['mes'][6];
				$ano = chop($_POST['mes'],"-" . $mes);
				$query = "SELECT presenca, dia, mes, ano, diasem, horain, minin, horaout, minout FROM pontos WHERE nome = '".$_POST['nome']."' and ano = '" . $ano ."' and mes = '" . $mes . "' ORDER BY dia, horain, minin, horaout, minout";
				$result1 = pg_query($query) or die('Provavelmente digitastes o nome errado');
				
				if (pg_affected_rows($result1)<=0){
					/* Não encontrou nenhum dado do membro para esse mês */
					echo "<script language='javascript' type='text/javascript'>alert('Membro não encontrado nesse mês!');window.location.href='relatorio-individual.php';</script>";
				}else{
					/* Tabela que mostra os horários do membro */
					echo "<br> Relatório do membro: <strong>" . $_POST['nome'] . "</strong><br>";
					$queryhor = "select horarios.dia, horarios.horain, horarios.minin, horarios.horaout, horarios.minout from horarios inner join membros on membros.id = horarios.id where nome='".$_POST['nome']."'";
					$resultado = pg_query($queryhor) or die('Erro nos horários');
					echo '<br><table border="1" style="text-align:center"><tr><th colspan="3">Horários</th></tr><tr><th>Dia da Semana</th><th> Entrada </th><th> Saída </th>';
					while($line = pg_fetch_array($resultado, null, PGSQL_ASSOC)) {
						echo "<tr><th>" . $line[dia] . "</th><td>" . $line[horain] . ":" . $line[minin] . "</td><td> " . $line[horaout] . ":" . $line[minout] . "</td></tr>";
					}
					echo "</table>";

					/* Tabela que mostra o relatório do membro nos dias que ele deveria ter ido */
					echo '<br><table border="1" style="text-align:center">';
					echo '<tr style="font-size:120%"><th>Data</th><th>Horário de Entrada</th><th>Horário da Saída</th></tr>';
					while($line = pg_fetch_array($result1, null, PGSQL_ASSOC)) {
						if($line[presenca] == 1){
							echo '<tr style="background-color:#66FF33; font-size:120%"><th>' . $line[dia] . '/' . $line[mes] . '/' . $line[ano] . ' - ' . $line[diasem] . '</th>';
							echo '<td>' . $line[horain] . ':' . $line[minin] . '</td><td>' . $line[horaout] . ':' . $line[minout] . '</td></tr>';
						}
						else {
							echo '<tr style="background-color:#FF0000; color:#FFFFFF; font-size:120%"><th>' . $line[dia] . '/' . $line[mes] . '/' . $line[ano] . ' - ' . $line[diasem] . '</th>';
							echo '<td>' . $line[horain] . ':' . $line[minin] . '</td><td>' . $line[horaout] . ':' . $line[minout] . '</td></tr>';
						}
					}
					echo "\n</table>\n";
				}
				pg_free_result($result1);
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



		<br>
	</body>
	<footer>
		Copyright ©<a href="http://www.mecajun.com/" target="_blank">Mecajun</a>	
	</footer>
</html>
