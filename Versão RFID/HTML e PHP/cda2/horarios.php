<!DOCTYPE html>
<html lang="pt-br">
	<link rel="stylesheet" type="text/css" href="Controle.css">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<head><header>
		<title>Controle de Acesso</title>
		<a class="b" href="menu.php"><h2>Controle de Acesso</h2></a>
	</header>
		<style>
			table td{
				padding-right: 10px;
				padding-left: 10px;
			}
			table th{
				padding-left: 10px;
			}
			.tabelao{	
				width:1500px;
			}

		</style>	
	</head>
	
	<body>
		<ul>
  			<li><a href="menu.php">Início</a></li>
			<li><a href="relatorios.php">Relatórios</a></li>
  			<li><a class="active" href="horarios.php">Horários</a></li>
  			<li><a href="editar-membro.php">Editar Membro</a></li>
  			<li><a href="relatorio-individual.php">Relatório Individual</a></li>
			<li><a href="painel.php">Painel de Controle</a></li>
			<li><a href="ajuda.html">Ajuda</a></li>
		</ul>
		<div class="tabelao">
		<?php
			/* Essa página só imprime a tabela dos horários */
			$connect = pg_connect("host=localhost dbname=cda user=postgres password=postgres");
			$query1 = "SELECT * FROM horarios where dia='Mon'";
			$result1 = pg_query($query1) or die('Erro, Mon');
			echo '<br><ul style="display:inline; font-size:13px"><li><table border="1" >';
			echo "<tr><th>Segunda-feira</th></tr>";
			while($line = pg_fetch_array($result1, null, PGSQL_ASSOC)) {
				$i=0;
				foreach ($line as $col_value) {
					$i++;
					if($i==1){
						$qnome = "select nome from membros where id=" . $col_value;
						$rnome = pg_query($qnome) or die('erro nome');
						$bu = pg_fetch_array($rnome, null, PGSQL_ASSOC);
						foreach($bu as $xu){
							$nomefunc = $xu;
						}
						echo "\t<tr><th>$nomefunc</th></tr>\n\t<tr><td>Entrada: ";
					}elseif($i==5){
						echo "Saída: $col_value:";
					}elseif($i==3){
						echo "$col_value:";
		
			}elseif($i!=2){
						echo "$col_value ";
					}
				}
				echo "\t</td></tr>\n";
			}
			echo "\n</table></li>\n";
			$query1 = "SELECT * FROM horarios where dia='Tue'";
			$result1 = pg_query($query1) or die('Erro, Tue');
			echo '<li><table border="1">';
			echo "<tr><th>Terça-feira</th></tr>";
			while($line = pg_fetch_array($result1, null, PGSQL_ASSOC)) {
				$i=0;
				foreach ($line as $col_value) {
					$i++;
					if($i==1){
						$qnome = "select nome from membros where id=" . $col_value;
						$rnome = pg_query($qnome) or die('erro nome');
						$bu = pg_fetch_array($rnome, null, PGSQL_ASSOC);
						foreach($bu as $xu){
							$nomefunc = $xu;
						}
						echo "\t<tr><th>$nomefunc</th></tr>\n\t<tr><td>Entrada: ";
					}elseif($i==5){
						echo "Saída: $col_value:";
					}elseif($i==3){
						echo "$col_value:";
					}elseif($i!=2){
						echo "$col_value ";
					}
				}
				echo "\t</td></tr>\n";
			}
			echo "\n</table></li>\n";
			$query1 = "SELECT * FROM horarios where dia='Wed'";
			$result1 = pg_query($query1) or die('Erro, Wed');
			echo '<li><table border="1" >';
			echo "<tr><th>Quarta-feira</th></tr>";
			while($line = pg_fetch_array($result1, null, PGSQL_ASSOC)) {
				$i=0;
				foreach ($line as $col_value) {
					$i++;
					if($i==1){
						$qnome = "select nome from membros where id=" . $col_value;
						$rnome = pg_query($qnome) or die('erro nome');
						$bu = pg_fetch_array($rnome, null, PGSQL_ASSOC);
						foreach($bu as $xu){
							$nomefunc = $xu;
						}
						echo "\t<tr><th>$nomefunc</th></tr>\n\t<tr><td>Entrada: ";
					}elseif($i==5){
						echo "Saída: $col_value:";
					}elseif($i==3){
						echo "$col_value:";
					}elseif($i!=2){
						echo "$col_value ";
					}
				}
				echo "\t</td></tr>\n";
			}
			echo "\n</table></li>\n";
			$query1 = "SELECT * FROM horarios where dia='Thu'";
			$result1 = pg_query($query1) or die('Erro, Thu');
			echo '<li><table border="1" >';
			echo "<tr><th>Quinta-feira</th></tr>";
			while($line = pg_fetch_array($result1, null, PGSQL_ASSOC)) {
				$i=0;
				foreach ($line as $col_value) {
					$i++;
					if($i==1){
						$qnome = "select nome from membros where id=" . $col_value;
						$rnome = pg_query($qnome) or die('erro nome');
						$bu = pg_fetch_array($rnome, null, PGSQL_ASSOC);
						foreach($bu as $xu){
							$nomefunc = $xu;
						}
						echo "\t<tr><th>$nomefunc</th></tr>\n\t<tr><td>Entrada: ";
					}elseif($i==5){
						echo "Saída: $col_value:";
					}elseif($i==3){
						echo "$col_value:";
					}elseif($i!=2){
						echo "$col_value ";
					}
				}
				echo "\t</td></tr>\n";
			}
			echo "\n</table></li>\n";
			$query1 = "SELECT * FROM horarios where dia='Fri'";
			$result1 = pg_query($query1) or die('Erro, Fri');
			echo '<li><table border="1" >';
			echo "<tr><th>Sexta-feira</th></tr>";
			while($line = pg_fetch_array($result1, null, PGSQL_ASSOC)) {
				$i=0;
				foreach ($line as $col_value) {
					$i++;
					if($i==1){
						$qnome = "select nome from membros where id=" . $col_value;
						$rnome = pg_query($qnome) or die('erro nome');
						$bu = pg_fetch_array($rnome, null, PGSQL_ASSOC);
						foreach($bu as $xu){
							$nomefunc = $xu;
						}
						echo "\t<tr><th>$nomefunc</th></tr>\n\t<tr><td>Entrada: ";
					}elseif($i==5){
						echo "Saída: $col_value:";
					}elseif($i==3){
						echo "$col_value:";
					}elseif($i!=2){
						echo "$col_value ";
					}
				}
				echo "\t</td></tr>\n";
			}
			echo "\n</table></li>\n";
			$query1 = "SELECT * FROM horarios where dia='Sat'";
			$result1 = pg_query($query1) or die('Erro, Sat');
			echo '<li><table border="1" >';
			echo "<tr><th>Sábado</th></tr>";
			while($line = pg_fetch_array($result1, null, PGSQL_ASSOC)) {
				$i=0;
				foreach ($line as $col_value) {
					$i++;
					if($i==1){
						$qnome = "select nome from membros where id=" . $col_value;
						$rnome = pg_query($qnome) or die('erro nome');
						$bu = pg_fetch_array($rnome, null, PGSQL_ASSOC);
						foreach($bu as $xu){
							$nomefunc = $xu;
						}
						echo "\t<tr><th>$nomefunc</th></tr>\n\t<tr><td>Entrada: ";
					}elseif($i==5){
						echo "Saída: $col_value:";
					}elseif($i==3){
						echo "$col_value:";
					}elseif($i!=2){
						echo "$col_value ";
					}
				}
				echo "\t</td></tr>\n";
			}
			echo "\n</table></li>\n";
			$query1 = "SELECT * FROM horarios where dia='Sun'";
			$result1 = pg_query($query1) or die('Erro, Sun');
			echo '<li><table border="1" >';
			echo "<tr><th>Domingo</th></tr>";
			while($line = pg_fetch_array($result1, null, PGSQL_ASSOC)) {
				$i=0;
				foreach ($line as $col_value) {
					$i++;
					if($i==1){
						$qnome = "select nome from membros where id=" . $col_value;
						$rnome = pg_query($qnome) or die('erro nome');
						$bu = pg_fetch_array($rnome, null, PGSQL_ASSOC);
						foreach($bu as $xu){
							$nomefunc = $xu;
						}
						echo "\t<tr><th>$nomefunc</th></tr>\n\t<tr><td>Entrada: ";
					}elseif($i==5){
						echo "Saída: $col_value:";
					}elseif($i==3){
						echo "$col_value:";
					}elseif($i!=2){
						echo "$col_value ";
					}
				}
				echo "\t</td></tr>\n";
			}
			echo "\n</table></li>\n";
			pg_free_result($result1);
			pg_close($dbconn);
		?>	
	</div>
	</body>
	<footer>
		Copyright ©<a href="http://www.mecajun.com/" target="_blank">Mecajun</a>	
	</footer>
</html>
