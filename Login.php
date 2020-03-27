<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta content="text/html; charset=utf-8" http-equiv="content-type">
    <title>Visiona</title>
    <link rel="stylesheet" type="text/css" href="strutturaPagina.css">
    
    
  </head>
  
  <body>
  	
  	<div id="PageWrapper">
  	
  		<div id="Header"><h1> Benvenuto nel sito di prenotazioni.</h1></div>
  		
  		<div id="Menu-Content-Wrap">
  		
  			<div id="Menu-Left">
  			
  				<form action="index.php" method="post">
					<input type="submit" value="Torna all'homepage.">	
  				</form>
  				
  				<br>
  				
  				<form action="Login.php" method="post">
					<input type="submit" name="logout" value="Logout.">     
				</form>
  				
  				<br>
  				
  				<form action="Login.php" method="post">
					<input type="submit" name="offerta" value="Prenotazione">   <input type="text" name="off" value="Minuti" id="o" >  
				</form>
				
				<br><br>
				<form action="Login.php" method="post">
					<input type="submit" name="eliminaPren" value="Cancella prenotazione.">     
				</form>
  		
  			</div>
  			
  			<div id="MainContent">
  				<?php 
  					include "Funzioni.php";
  					
  					checkInattivita();
  					checkHttps();
  					checkCookies();
  					//session_start();//VA MESSA IN checkInattivita()
  				
  					
  					if( isset($_POST['password']) && isset($_POST['username'])  )
  					{
  						//controllo di non essere gia' autenticato
  						$l = checkUtenteLoggato();
  						if($l == true)
  						{
  							echo "Ben tornato ".$_SESSION['myuser-s239214']."";
  						}
  						else
  						{
  							echo "Nuova richiesta login.<br>";
  							//hai ricevuto una richiesta di login
  							$username = $_POST['username'];
  							$password = $_POST['password'];
  						
  							//sanificazione delle stringhe
  							$username = nl2br(htmlentities($username));
  							$password = nl2br(htmlentities($password));
  						
  							/**verifico la validita sintattica delle credenziali**/
  							$s1 = controllaEmail($username);
  							$s2 = controllaPassword($password);
  						
  							if($s1 == false || $s2 == false)
  							{	/*
  								* REDIRECT SULL'HOMEPAGE COME HA FATTO MASALA, E DISTRUZIONE DELLA SESSIONE IN CORSO
  								*/
  								myDestroySessionAndRedirect("");
  								
  							}
  							else
  							{
  								//verifico che l'utente sia registrato
  								$query = checkUtenteRegistrato($username, $password);
  								$_SESSION['myuser-s239214'] = $username; //tengo un indice sull'utente che si e' loggato
  								echo "<br>Utente autenticato<br>";
  							}
  						}
  						
  					}//FINE PRIMO IF
  				
  					else if( isset($_POST['logout']) )
  					{
  					
  						/***
  						 * distruzione della sessione e redirezione sulla home
  					 	*/
  						destroySession();
  						header('HTTP/1.1 307 temporary redirect');
  						header("Location: index.php?msg=".urlencode(""));
  						exit();
  					}//FINE SECONDO IF
  				
  					else if( isset($_POST['offerta']) )
  					{
  						/**ricevo la richiesta dei minuti di prenotazione**/
  						
  						$minuti = $_POST['off'];
  						transazionePrenot($minuti, $_SESSION['myuser-s239214']);
  						
  						
  						
  					}//FINE TERZO IF
  					
  					else if( isset($_POST['eliminaPren']) )
  					{
  						cancellaPrenotazione($_SESSION['myuser-s239214']);
  					}//FINE QUARTO IF
  					
  					if( isset($_POST["usernameReg"]) && isset($_POST["passwordReg"]) )
  					{
  						registrazione($_POST["usernameReg"], $_POST["passwordReg"]);
  						echo "<h4>L'utente e' stato registrato correttamente.</h4><br>";
  						
  					}//FINE IF
  					
  					//visionaBID();
  				?>
  			</div>
  			
  		</div>
  		<div id="Footer">Footer</div>
  	</div>
  	
  	  	
  	
		    
		
		
		 
		
		
  </body>
</html>

