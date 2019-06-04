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
			table tr{
				text-align: center;
			}
			.tabelao{	
				width:1500px;
				float:left;
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
		<form method="POST" action="horarios.php">
		<?php
			/* Conexao com a DB */
			$connect = pg_connect("host=localhost dbname=cda user=postgres password=postgres");
			if(isset($_POST['Rem'])){
				/* Armazena qual horario deve ser removido */
				$rem = $_POST['Rem'];
				/* Loop para encontrar o horario */
				$cont = 0;
				for($i=1;$i<=7;$i++){
					switch($i){
						case 1:
							$dayname = "Mon";
							break;
						case 2:
							$dayname = "Tue";
							break;
						case 3:
							$dayname = "Wed";
							break;
						case 4:
							$dayname = "Thu";
							break;
						case 5:
							$dayname = "Fri";
							break;
						case 6:
							$dayname = "Sat";
							break;
						case 7:
							$dayname = "Sun";
							break;
					};
					$query = "SELECT horain, minin, horaout, minout FROM horarios_flex WHERE diasem = '" . $dayname . "' ORDER BY horain, minin, horaout, minout";
					$result = pg_query($query);
					$x = pg_affected_rows($result);
					/* Verifica se o horario a ser removido e desse dia, se nao, move on... */
					if($x + $cont >= $rem){
						/* Loop para encontrar o horario entre os que pertencem ao dia */
						while($line = pg_fetch_array($result, null, PGSQL_ASSOC)){
							$cont++;
							if($cont == $rem){
								$query = "DELETE FROM horarios_flex WHERE diasem = '" . $dayname . "' AND horain = '" . $line[horain] . "' AND minin = '" . $line[minin] . "' AND horaout = '" . $line[horaout] . "' AND minout = '" . $line[minout] . "'";
								$result = pg_query($query);
								if(pg_affected_rows($result)==1){
									/* Retorna sucesso */
									echo"<script language='javascript' type='text/javascript'>alert('Horário removido!');window.location.href='horarios.php';</script>";
									die();
								}
								else{
									/* Retorna falha */
									echo"<script language='javascript' type='text/javascript'>alert('Erro ao remover o horário!');window.location.href='horarios.php';</script>";
									die();
								};
							};
						};
					};
					$cont += $x;
				};
			}
			elseif(isset($_POST['cancel'])){
				echo"<script language='javascript' type='text/javascript'>alert('Operacao cancelada!');window.location.href='horarios.php';</script>";
				die();
			}
			elseif(isset($_POST['conf'])){
				/* Confirmar que nao existem conflitos entre os horarios atuais e o a ser inserido?? */
				if(!empty($_POST['horain']) && !empty($_POST['minin']) && !empty($_POST['horaout']) && !empty($_POST['minout'])){
					/* Verifica se os espacos nao foram preenchidos errados */
					$query = "INSERT INTO horarios_flex values('" . $_COOKIE['numday'] . "','" . $_POST['horain'] . "','" . $_POST['minin'] . "','" . $_POST['horaout'] . "','" . $_POST['minout'] . "')";
					$result = pg_query($query) or die('erro!');
					
					if(pg_affected_rows($result) == 1){
						echo"<script language='javascript' type='text/javascript'>alert('Horário inserido!');window.location.href='horarios.php';</script>";
						die();
					}
					else{
						echo"<script language='javascript' type='text/javascript'>alert('Erro ao inserir horário');window.location.href='horarios.php';</script>";
						die();
					}
					
					pg_free_result($result);
				}
				else{
					echo"<script language='javascript' type='text/javascript'>alert('Por favor preencha os campos com dois dígitos');window.location.href='horarios.php';</script>";
					die();
				};
			}
			elseif(isset($_POST['Add'])){
				/* Adicionar um novo horario no dia */
				/* Apenas mostra quatro campos para o usuario preencher com o tempo novo */
				switch($_POST['Add']){
					case 1:
						$daynamebr = "Segunda-Feira";
						setcookie("numday", "Mon");
						break;
					case 2:
						$daynamebr = "Terça-Feira";
						setcookie("numday", "Tue");
						break;
					case 3:
						$daynamebr = "Quarta-Feira";
						setcookie("numday", "Wed");
						break;
					case 4:
						$daynamebr = "Quinta-Feira";
						setcookie("numday", "Thu");
						break;
					case 5:
						$daynamebr = "Sexta-Feira";
						setcookie("numday", "Fri");
						break;
					case 6:
						$daynamebr = "Sábado";
						setcookie("numday", "Sat");
						break;
					case 7:
						$daynamebr = "Domingo";
						setcookie("numday", "Sun");
						break;
				};
				
				echo '<p>Digite um novo horário para '. $daynamebr .'</p><br>';
				echo '<p>Entrada: <input type="text" name="horain">:<input type="text" name="minin"></p><br>';
				echo '<p>Saída: <input type="text" name="horaout">:<input type="text" name="minout"></p>';
				echo '<input type="submit" value="Adicionar" name="conf"><input type="submit" value="Cancelar" name="cancel">';
			}
			elseif(isset($_POST['Change'])){ /* If caso o cidadao deseje mudar os horarios uteis */
				echo '<div style="width:80%">';
				echo '<table style="width:100%" border="1px">';
				echo '<tr><th>Horários Úteis</th></tr>';
				echo '</table>';
				/* Aqui para imprimir os horários atualmente liberados */
				/* Loop de 1 a 7 para facilitar a relacao com os dias da semana */
				$j = 0; /* Contador de qual horario deve ser apagado */
				for($i=1;$i<=7;$i++){
					$dayname = "Tot";
					$size = 14.5;
					switch($i){
						case 1:
							$dayname = "Mon";
							$daynamebr = "Segunda-Feira";
							break;
						case 2:
							$dayname = "Tue";
							$daynamebr = "Terça-Feira";
							break;
						case 3:
							$dayname = "Wed";
							$daynamebr = "Quarta-Feira";
							break;
						case 4:
							$dayname = "Thu";
							$daynamebr = "Quinta-Feira";
							break;
						case 5:
							$dayname = "Fri";
							$daynamebr = "Sexta-Feira";
							break;
						case 6:
							$dayname = "Sat";
							$daynamebr = "Sábado";
							$size = 13.75;
							break;
						case 7:
							$dayname = "Sun";
							$daynamebr = "Domingo";
							$size = 13.75;
							break;
					};
					/* Comeca uma table com float left */
					echo '<table style="float:left; width:' . $size . '%" border="1px">';
					echo '<tr><th>' . $daynamebr . '</th></tr>';
					
					/* Pesquisa dos horarios disponiveis nesse dia */
					$query = "SELECT horain, minin, horaout, minout FROM horarios_flex WHERE diasem = '" . $dayname . "' ORDER BY horain, minin, horaout, minout";
					$result = pg_query($query);
					
					while($line = pg_fetch_array($result, null, PGSQL_ASSOC)){
						$j++;
						echo '<tr><td>Entrada: ' . $line[horain] . ':' . $line[minin] . '<br>Saída: ' . $line[horaout] . ':' . $line[minout] . '<br><button type="submit" name="Rem" value="' . $j . '">Remover</button></td></tr>';
					};
					
					/* Botao para adicionar novo horario */
					echo '<tr><td><button type="submit" name="Add" value="' . $i . '">Adicionar</button></td></tr>';
					echo '</table>';
				}
				echo '</div>';
			}
			else {
				/* Essa página só imprime a tabela dos horários */
				echo '<div style="width:80%">';
				for($i=1;$i<=7;$i++){
					$dayname = "Tot";
					$size = 14.5;
					switch($i){
						case 1:
							$dayname = "Mon";
							$daynamebr = "Segunda-Feira";
							break;
						case 2:
							$dayname = "Tue";
							$daynamebr = "Terça-Feira";
							break;
						case 3:
							$dayname = "Wed";
							$daynamebr = "Quarta-Feira";
							break;
						case 4:
							$dayname = "Thu";
							$daynamebr = "Quinta-Feira";
							break;
						case 5:
							$dayname = "Fri";
							$daynamebr = "Sexta-Feira";
							break;
						case 6:
							$dayname = "Sat";
							$daynamebr = "Sábado";
							$size = 13.75;
							break;
						case 7:
							$dayname = "Sun";
							$daynamebr = "Domingo";
							$size = 13.75;
							break;
					};
					/* Comeca uma table com float left */
					echo '<table style="float:left; width:' . $size . '%" border="1px">';
					echo '<tr><th>' . $daynamebr . '</th></tr>';
					
					/* Pesquisa dos horarios disponiveis nesse dia */
					$query = "SELECT id, horain, minin, horaout, minout FROM horarios_fixos WHERE diasem = '" . $dayname . "' ORDER BY horain, minin, horaout, minout";
					$result = pg_query($query);
					
					while($line = pg_fetch_array($result, null, PGSQL_ASSOC)){
						$qnome = "select nome from membros where id=" . $line[id];
						$rnome = pg_query($qnome) or die('erro nome');                                              
						$bu = pg_fetch_array($rnome, null, PGSQL_ASSOC);
						foreach($bu as $xu){
							$nomefunc = $xu;
						}
						echo '<tr><th>' . $nomefunc . '</th></tr>';
						echo '<tr><td>Entrada: ' . $line[horain] . ':' . $line[minin] . ' Saída: ' . $line[horaout] . ':' . $line[minout] . '</td></tr>';
					};
					
					/* Botao para adicionar novo horario */
					echo '</table>';
				}
				echo '</div>';
				/* Mostra um botão apenas se o usuario tiver permissao para definir horarios */
				/* Permissão 3 para definir os horários */
				$query = "SELECT * FROM usuarios WHERE usuario = '" . $_COOKIE['login'] . "' AND permissao > 2";
				$verifica = pg_query($query) or die("Erro na identificação do usuário!");
				if (pg_affected_rows($verifica)>0){
					echo '<div style="clear:both">';
					echo '<input type="submit" value="Mudar os horários úteis" name="Change">';
					echo '</div>';
				};
			}
			pg_close($dbconn);
		?>
		</form>
	</div>
	</body>
	<footer>
		Copyright ©<a href="http://www.mecajun.com/" target="_blank">Mecajun</a>	
	</footer>
</html>
