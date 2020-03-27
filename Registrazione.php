<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta content="text/html; charset=utf-8" http-equiv="content-type">
    <title>Prova web</title>
	<link rel="stylesheet" type="text/css" href="strutturaPagina.css">
  </head>
	
	<body>
		
		<div id="PageWrapper">
			<div id="Header"><h1> Benvenuto nel sito di aste online.</h1></div>
  		
  			<div id="Menu-Content-Wrap">
  		
  				<div id="Menu-Left">
  			
  					<form action="index.php" method="post">
						<input type="submit" value="Torna all'homepage.">	
  					</form>
  			
  				</div>
  			
  				<div id="MainContent">
  					<?php
    
						session_start();
	
						//$_SESSION['start'] = "qui";
						$_SESSION["username-s239214"] = $_POST["usernameReg"];
						$_SESSION["password-s239214"] = $_POST["passwordReg"];
						/*if(isset($_SESSION["start"]))
							echo "Reindirizzato"; -> DOPO IL REINDIRIZZAMENTO LA SESSIONE RESTA E CON LEI LE VARIABILI SALVATE NEL VETTORE $_SESSION*/
	
						//$username = $_SESSION["username"];
						//$password = $_SESSION["password"];
	
						//sanificazione stringhe
						$username = nl2br(htmlentities(/*$username*/$_SESSION["username-s239214"]));
						$password = nl2br(htmlentities(/*$password)*/$_SESSION["password-s239214"]));
						//verifica su email e password;
						include 'Funzioni.php';
						$s1 = controllaEmail($username);
						$s2 = controllaPassword($password);
						if($s1 == false || $s2 == false)
						{
							echo "Credenziali errate. Torna nell'homepage.<br>";
							
						}
						else
						{
							//controllo se l'utente e' gia' registrato
		
							$statoConnessione = connessioneDB("s239214");
							if($statoConnessione != NULL)
							{
								$risultato = mysqli_query($statoConnessione, "SELECT * FROM utenti WHERE Email = '".$username."' ", MYSQLI_STORE_RESULT);
								if( mysqli_num_rows($risultato) == 0)
								{
									$nuovoUtente = "INSERT INTO utenti (Email, Password) VALUES ( '".$username."' , '".md5($password)."' )"; 
									//$nuovoUtente = "INSERT INTO utenti SET email = ' ".$username ." ', password = ' ".$password ." ' ";
									if(!mysqli_query($statoConnessione, $nuovoUtente))
									{
										echo "Non e' stato possibile inserire l'utente, riprova piu' tardi.<br>";
									}
									else
										echo "L'utente e' stato registrato correttamente, torna nella home per effettuare il login.<br>";
								}
								else
								{	
									echo "L'utente e' gia' registrato";
								}
								//rilascio risorse
								mysqli_free_result($risultato);
								mysqli_close($statoConnessione);
						}
						else
							echo "Torna sulla homepage del sito e riprova in un secondo momento.";
					}
				?>
  				</div>
  				
  			</div>
  			
  			<div id="Footer">Footer</div>
  		</div>
		
	</body>	
</html>
