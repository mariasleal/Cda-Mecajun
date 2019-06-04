<html>
	<head>
		<link rel="stylesheet" type="text/css" href="Controle.css">
		<header>
			<title>Controle de Acesso</title>
			<h2>Controle de Acesso</h2>
		</header>
	</head>

	<body>
		<div class="aba_login">
			<form method="POST" action="cda2.php">
			<br>
			<table>
				<!-- Campo de preenchimento das variáveis -->
				<tr><td><label>Login:</label></td><td><input type="text" name="login" id="login"></td></tr>
				<tr><td><label>Senha:</label></td><td><input type="password" name="senha" id="senha"></td></tr>
			</table>
			<div>
				<input type="submit" value="Entrar" id="entrar" name="entrar">
			</div>
			<br>
		</div>
		<?php
			/* Variáveis de acesso à database*/
			$login = $_POST['login'];
			$entrar = $_POST['entrar'];
			$senha = $_POST['senha'];	/*md5($_POST['senha']);*/
			
			/* Proteção contra SQL injections */
			if((strpbrk($senha, "\',:.<>/?\"") != FALSE) || (strpbrk($login, "\',:.<>/?\"") != FALSE)){
				die("<script language='javascript' type='text/javascript'>alert('Favor não utilizar caracteres especiais!');window.location.href='cda2.php';</script>");
			}
			
			$connect = pg_connect("host=localhost dbname=cda user=postgres password=postgres");
			
			if(isset($entrar)){
				$query = "SELECT * FROM usuarios WHERE usuario = '" . $login . "' AND senha = '" . $senha . "'";
				// echo $query;
				$verifica = pg_query($query) or die("erro ao selecionar");
				if (pg_affected_rows($verifica)<=0){
					/* Acesso negado!*/
					echo"<script language='javascript' type='text/javascript'>alert('Login e/ou senha incorretos');window.location.href='cda2.php';</script>";
					die();
				}else{
					/* Acesso permitido, salvar o usuário com um cookie e chamar a tela de menu*/
					setcookie("login",$login);
					header("Location:menu.php");
				}
			}
		?>
		<footer>
			Copyright ©<a href="http://www.mecajun.com/" target="_blank">Mecajun</a>	
		</footer>
	</body>
</html>
