<?php
/* includere la chiamata a checkCookies in index.php, visiona.php, login.php*/
/* includere $new nel secondo if di checkInattivita */
/**credenziali database:       localhost  username:s239214  password:onessend  s239214==nomedatabase **/
/**credenziali database prova: localhost    root     ''     prova  **/
/**modifiche: da 390 a 410.Il numeratore è costituito dai minuti assegnati oppure da quelli richiesti ?
 * 
 * 
 */


/**
 * 
 * da 314 a 339
 * da 325 a 340
 * modificare anche dopo 340 
 * 
 * modificare in caso di eliminazione prenotazione
 * 
 */


function checkCookies()
{
	if (! isset ( $_COOKIE ['s239214cookie'] ))
	{
		setcookie ( 's239214cookie', 1, time () + 3600 );
		if (! isset ( $_GET ['s239214cookies'] ))
		{
			header ( 'Location: ' . $_SERVER ['REQUEST_URI'] . '?s239214cookies=true' );
			exit();
		}
		else
		{
			header ( 'Location: Redirezione.php?errmsg=Questo sito necessita che i cookies siano abilitati per funzionare.' );
			exit();
		}
	}
	
}

function connessioneDB($nomeDB)
{
	$ris = mysqli_connect("localhost", "s239214", "onessend", $nomeDB);
	if(!$ris)
	{
		echo "ATTENZIONE: non e' stato possibile connettersi al database, riprovare in un altro momento.<br>";
		echo "Errore nella connessione ".mysqli_connect_errno()." ".mysqli_connect_error()."<br>";
		return NULL;
	}
	if (!mysqli_set_charset($ris, "utf8"))
		die('Errore nel caricamento del set di caratteri utf8: ' . mysqli_error($conn));
		else
			return $ris;
			
}

function checkHttps()
{
	if ( !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
	{  /*La richiesta e' stata fatta su HTTPS*/ }
	else
	{  // Redirect su HTTPS  // eventuale distruzione sessione e cookie relativo
		$redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		header('HTTP/1.1 301 Moved Permanently');
		header('Location: ' . $redirect);
		exit();
	}
}

function controllaEmail($email)
{
	$ind = strpos($email, "@", 0);
	if( $ind == false)
		return false;
		else
			return $ind;
}

function controllaPassword($password)
{
	$e1 = "/[0-9]/"; $e2 = "/[a-z]/"; $e3 = "/[A-Z]/";
	$s1=preg_replace($e1,"",$password);   $s2=preg_replace($e2,"",$password);  $s3=preg_replace($e3,"",$password);
	if(strlen($s1) > 0 && strlen($s2) > 0 && strlen($s3) > 0)
		return true;
		else
			return false;
			
}

function checkUtenteRegistrato($username, $password)
{
	
	$statoConnessione = connessioneDB("s239214");
	
	$r = false;
	if($statoConnessione != NULL)
	{
		
		//echo "Ricevuto->/".$username."/<br>";
		$q = "SELECT * FROM utenti WHERE Email = '".$username."' AND Password = '".md5($password)."' " ;
		$risultato = mysqli_query($statoConnessione, $q);
		if(!$risultato)
			myDestroySessionAndRedirect("");
			
			
			
			
			
			if(mysqli_num_rows($risultato) == 0)
			{
				//utente non regisrato
				myDestroySessionAndRedirect("");
				//echo "<h1>ATTENZIONE:</h1><br>non sei registrato, torna all'homepage ed effettua la registrazione";
				//echo "Stai cercando /".$username."/<br>";
				$r = false;
			}
			else
			{
				echo "<h1>Benvenuto utente ".$username."<h1>";
				$r = true;
				
			}
			
			//rilascio risorse
			mysqli_free_result($risultato);
			mysqli_close($statoConnessione);
			return $r;
	}
	else
		myDestroySessionAndRedirect("");
		
}

function myDestroySessionAndRedirect($mess)//in realta effettua solo il reindirizzamento sulla pagina che avvisa di aver inserito cred.errate o tentato il login senza essere registato
{
	header('HTTP/1.1 307 temporary redirect');
	header("Location: Redirezione.php?msg=".urlencode($mess));
	exit();
	
}

function checkInattivita()
{
	session_start();		$time = time();			$diff = 0;		$new = false;
	if( isset($_SESSION['time-s239214']) )
	{
		$t0 = $_SESSION['time-s239214'];   $diff = $time - $t0;
	}
	else
		$new = true;
		if(/*$new ||*/ $diff > 120)
		{
			destroySession();
			header('HTTP/1.1 307 temporary redirect');
			header("Location: Timeout.php?msg=".urlencode(""));
			exit();
		}
		else
		{
			$_SESSION['time-s239214'] = time();
			echo "Tempo ultimo accesso aggiornato.<br>";
		}
		
}
function checkUtenteLoggato()
{
	if( isset($_SESSION['myuser-s239214']) )
		return true;
		else
			return false;
}

function destroySession()
{
	$_SESSION = array();
	if( ini_get("session_use_cookies()") )
	{
		$params = session_get_cookie_params();
		setcookie(session_name(), '', time()-3600*24, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
	}
	session_destroy();
}

function transazionePrenot($minuti,$utente)
{
	echo "<h3>Utente ".$utente." <h3>";
	$minuti = nl2br(htmlentities($minuti));
	if( is_numeric($minuti) == false )
	{
		echo "Devi inserire un valore numerico.<br>".$minuti;
		return ;
	}
	else if(is_numeric($minuti) == true && ($minuti <= 0 || $minuti >180))
	{
		echo "Il numero dei minuti richiesti deve essere maggiore di 0 e massimo pari a 180.<br>";
		return;
	}
	else
	{
		//se sto cercando di fare la prenotazione allora sono loggato ma anche registrato
		//verifico di non aver gia' fatto richiesta
		//verifico che ci siano minuti liberi
		
		$statoConnessione = connessioneDB("s239214");
		if($statoConnessione !=NULL)
		{
			try {
				mysqli_autocommit($statoConnessione, false);
				
				
				$queryPrenotazione = "	SELECT *
										FROM Prenotazioni
										WHERE Email = '".$utente."'
										FOR UPDATE";
				if( !($risultato = mysqli_query($statoConnessione, $queryPrenotazione)) ) //da liberare
					throw new Exception("Comando fallito nella prenotazione<br>");
				if(mysqli_num_rows($risultato) == 0 )
				{
					//non ho ancora fatto prenotazioni
					echo "Non avevi fatto prenotazioni sino a questo momento.<br>";
					$minuti = round ( $minuti );
					//verifico i minuti liberi
					$queryPrenotazione = "	SELECT *
											FROM Prenotazioni
											FOR UPDATE";
					if( !($risultato = mysqli_query($statoConnessione, $queryPrenotazione)) ) //da liberare
						throw new Exception("Comando fallito nella prenotazione<br>");
					$totale = 0;
					while ( ($riga = mysqli_fetch_array($risultato, MYSQLI_BOTH)) )
					{
						$totale = $totale + $riga['Assegnati'];
					}
					$minuti_liberi = 180 - $totale;
							
					if($minuti_liberi == 0)
					{
						echo "Non ci sono minuti disponibili, impossibile effettuare la prenotazione.<br>";
						return;
					}
					else
					{
						//posso inserire la prenotazione, ricavo il massimo id prenotazione
						$queryPrenotazione = "SELECT MAX(IDprenotazione) AS IDmassimo
											  FROM Prenotazioni
											  FOR UPDATE";
						
						if( !($risultato = mysqli_query($statoConnessione, $queryPrenotazione)) ) //da liberare
							throw new Exception("Comando fallito nella prenotazione<br>");
						$riga = mysqli_fetch_array($risultato, MYSQLI_BOTH);
						$ID = 0;
						if( $riga['IDmassimo'] == NULL)
							$ID = 1;
						else 	
							$ID = $riga['IDmassimo'] + 1;
						//$ID contiene l'identificativo dell'ultima prenotazione
						/****************************************************************************/	
							
						
						//confrontiamo i minuti liberi con la richiesta
						if($minuti_liberi >= $minuti)
						{
							//inserisco senza fare modifiche
							/***********devo calcolare ora inizio e ora fine***********/
							$inizio = null;
							$fine = null;
							$queryPrenotazione = "SELECT *
											  	  FROM Prenotazioni
											      FOR UPDATE";
							
							if( !($risultato = mysqli_query($statoConnessione, $queryPrenotazione)) ) //da liberare
							{
								echo "Errore<br>";
								throw new Exception("Comando fallito nella prenotazione<br>");
							}
							$riga = mysqli_fetch_array($risultato, MYSQLI_BOTH);
							if( mysqli_num_rows($risultato) == 0 )
							{
								$inizio = "14:00";
								$fine = calcolaOrario($inizio,$minuti);
							}
							else
							{
								$inizio = $riga['OraFine'];
								$fine = calcolaOrario($inizio,$minuti);
							}
							
							/**********************************************************/
							
							$queryPrenotazione = " INSERT INTO Prenotazioni (IDprenotazione, Email, Richiesti, Assegnati, OraInizio, OraFine) VALUES (".$ID.", '".$utente."', ".$minuti.", ".$minuti.", '".$inizio."', '".$fine."');";
							if( !mysqli_query($statoConnessione, $queryPrenotazione) )
								throw new Exception("Comando fallito nella prenotazione<br>");
										
						}
						else if($minuti_liberi < $minuti)
						{
							//devo aggiustare le richieste
							echo "Le richieste sono state modificate.<br>";
							//calcolo il denominatore
							$denominatore = $minuti;
							$totale = 0;
							$queryPrenotazione = "	SELECT *
												    FROM Prenotazioni
												    FOR UPDATE";
							if( !($risultato = mysqli_query($statoConnessione, $queryPrenotazione)) ) //da liberare
								throw new Exception("Comando fallito nella prenotazione<br>");
										
							while ( ($riga = mysqli_fetch_array($risultato, MYSQLI_BOTH)) )
							{
								$denominatore = $denominatore + $riga['Richiesti'];
							}
							///////////////echo $denominatore."<br>";
										
							//modifico i valori assegnati e calcolo il totale
							$queryPrenotazione = "	SELECT *
													FROM Prenotazioni
													FOR UPDATE";
							if( !($risultato = mysqli_query($statoConnessione, $queryPrenotazione)) ) //da liberare
								throw new Exception("Comando fallito nella prenotazione<br>");
							$totale = 0;
							$inizio = null;
							$fine = null;
							$orario = null;
							while ( ($riga = mysqli_fetch_array($risultato, MYSQLI_BOTH)) )
							{
								$assegnatiAttuali = $riga['Richiesti'];
								$assegnatiModificati = round ( ( $assegnatiAttuali/($denominatore) ) * 180 );
								
								
								/*****calcolo ora inizio e ora fine***/
								if( $totale == 0)
									$inizio = "14:00";
								else 
									$inizio = $orario;
								$fine = calcolaOrario($inizio, $assegnatiModificati);
								$orario = $fine; // da usare al prossimo giro
								/*************************************/
								
								$totale = $totale + $assegnatiModificati;
								$utente_corrente = $riga['Email'];
								
												
								$queryPrenotazione = "UPDATE Prenotazioni SET Assegnati = ".$assegnatiModificati.", 
																			  OraInizio = '".$inizio."',
																			  OraFine = '".$fine."'											
													  						  WHERE Email = '".$utente_corrente."'";
								if( !mysqli_query($statoConnessione, $queryPrenotazione) )
									throw new Exception("Comando fallito nella prenotazione<br>");
													
													
							}
							////////////echo "totale->".$totale;
							//inserisco la richiesta
							$minutiNew = 180 - $totale;
							$inizio = $orario;
							$fine = calcolaOrario($inizio, $minutiNew);
							$queryPrenotazione = "INSERT INTO Prenotazioni (IDprenotazione, Email, Richiesti, Assegnati, OraInizio, OraFine) VALUES (".$ID.", '".$utente."', ".$minuti.", ".$minutiNew.", '".$inizio."', '".$fine."')";
							if( !mysqli_query($statoConnessione, $queryPrenotazione) )
								throw new Exception("Comando fallito nella transazione<br>");
						}
					}
				}
				else
				{
					echo "Non e' possibile effettuare piu' di una prenotazione.<br>";
					return;
				}
					
				//LIBERARE LE RISORSE !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
				mysqli_free_result($risultato);
				/////////////////////!!!!!!!!!!!!!!!!!!!!!!!////////////////
				if(!mysqli_commit($statoConnessione))
				{
					throw new Exception("Commit fallito<br>");
				}
		}
		catch (Exception $e)
		{
			mysqli_rollback($statoConnessione);
			echo "<br>Rollback ".$e->getMessage()."<br>";
		}
		finally {
			mysqli_close($statoConnessione);
		}
	}
	else
		echo "Impossibile connettersi al database ed effettuare la prenotazione.<br>";
			
	}
	
	
}
/****************************************************************************************************************************/
function cancellaPrenotazione($utente)
{
	$statoConnessione = connessioneDB("s239214");
	$var = 0;
	if($statoConnessione !=NULL)
	{
		try{
			mysqli_autocommit($statoConnessione, false);
		
			//controllo di aver fatto la richiesta
			$queryPrenotazione = "	SELECT *
									FROM Prenotazioni
									WHERE Email = '".$utente."'
									FOR UPDATE";
			if( !($risultato = mysqli_query($statoConnessione, $queryPrenotazione)) ) //da liberare
			{
				$var = 0;
				throw new Exception("Comando fallito nella prenotazione<br>");
			}
			if(mysqli_num_rows($risultato) == 0 )
				echo "<h3>Non hai ancora fatto richiesta di prenotazione.</h3>";
			else 
			{
				//elimino il richiedente
				$queryPrenotazione = "DELETE FROM Prenotazioni WHERE Email = '".$utente."' ";
				if( !mysqli_query($statoConnessione, $queryPrenotazione) )
				{
					$var=1;
					throw new Exception("Comando fallito nella transazione<br>");
				}
				
				//calcolo il totale dei minuti richiesti
				$queryPrenotazione = "	SELECT *
										FROM Prenotazioni
										FOR UPDATE";
				if( !($risultato = mysqli_query($statoConnessione, $queryPrenotazione)) ) //da liberare
				{
					$var=2;
					throw new Exception("Comando fallito nella cancellazione<br>");
				}
				$totale = 0;
				while ( ($riga = mysqli_fetch_array($risultato, MYSQLI_BOTH)) )
				{
					$totale = $totale + $riga['Richiesti'];
					
				}
				if( $totale <= 180)
				{
					//sono assegnati minuti pari a quelli richiesti
					$queryPrenotazione = "	SELECT *
											FROM Prenotazioni
											FOR UPDATE";
					if( !($risultato = mysqli_query($statoConnessione, $queryPrenotazione)) ) //da liberare
					{
						$var=3;
						throw new Exception("Comando fallito nella cancellazione<br>");
					}
					
					/***************per ogni utente calcolo ora inizio e ora fine*********************/
					$totale = 0;
					$inizio = null;
					$fine = null;
					$orario = null;
						
						
					while ( ($riga = mysqli_fetch_array($risultato, MYSQLI_BOTH)) )
					{
						//$totale = $totale + $riga['Richiesti'];
						$assegnatiNew = $riga['Richiesti'];
						if( $totale == 0)
						{
							$inizio = "14:00";
							$totale++;
						}
						else
							$inizio = $orario;
						$fine = calcolaOrario($inizio, $assegnatiNew);
						$orario = $fine; // da usare al prossimo giro
						
					
						
						$utente_corrente = $riga['Email'];
						$queryPrenotazione = "UPDATE Prenotazioni SET Assegnati = ".$assegnatiNew.",
																	  OraInizio = '".$inizio."',
															     	  OraFine = '".$fine."'
																	  WHERE Email = '".$utente_corrente."'";
						if( !mysqli_query($statoConnessione, $queryPrenotazione) )
						{
							$var=4;
							throw new Exception("Comando fallito nella prenotazione<br>");
						}
					}
				}
				else 
				{
					echo"Redistribuzione orari dovuti alla cancellazione.<br>";
					//redistribuzione
					//calcolo il denominatore
					$denominatore = 0;
					//$totale = 0;
					$queryPrenotazione = "	SELECT *
											FROM Prenotazioni
										    FOR UPDATE";
					if( !($risultato = mysqli_query($statoConnessione, $queryPrenotazione)) ) //da liberare
					{
						$var = 5;
						throw new Exception("Comando fallito nella prenotazione<br>");
					}
					
					$numeroUtenti = 0;
					while ( ($riga = mysqli_fetch_array($risultato, MYSQLI_BOTH)) )
					{
						$denominatore = $denominatore + $riga['Richiesti'];/***richiesti***/
						$numeroUtenti++;
					}
					
					//modifico i valori assegnati e calcolo il totale
					$queryPrenotazione = "	SELECT *
											FROM Prenotazioni
											FOR UPDATE";
					if( !($risultato = mysqli_query($statoConnessione, $queryPrenotazione)) ) //da liberare
					{
						$var = 6;
						throw new Exception("Comando fallito nella prenotazione<br>");
					}
					
					$totale = 0;
					$totale2 = 0;
					$inizio = null;
					$fine = null;
					$orario = null;
					$cont = 0;
					while ( ($riga = mysqli_fetch_array($risultato, MYSQLI_BOTH)) )
					{
						$cont++;
						if( $cont == $numeroUtenti)
						{
							$utente_corrente = $riga['Email'];
							break;//sono all'ultimo utente che deve prendere 180 - sommaMinuti
						}
						$assegnatiAttuali = $riga['Richiesti'];/**Assegnati, ma potrebbe essere Richiesti**/
						$assegnatiModificati = round ( ( $assegnatiAttuali/($denominatore) ) * 180 );
						if( $totale == 0)
						{
							$inizio = "14:00";
							$totale++;
						}
						else
							$inizio = $orario;
						$fine = calcolaOrario($inizio, $assegnatiModificati);
						$orario = $fine; // da usare al prossimo giro
							
						
						$totale2 = $totale2 + $assegnatiModificati;
						$utente_corrente = $riga['Email'];
						
						$queryPrenotazione = "UPDATE Prenotazioni SET Assegnati = ".$assegnatiModificati.",
																	  OraInizio = '".$inizio."',
																	  OraFine = '".$fine."'
																	  WHERE Email = '".$utente_corrente."'";
						if( !mysqli_query($statoConnessione, $queryPrenotazione) )
						{	
							echo "****".$totale."****";
							throw new Exception("Comando fallito nella prenotazione<br>");
						}
					}
					$minutiNew = 180 - $totale2;
					$inizio = $orario;
					$fine = calcolaOrario($inizio, $minutiNew);
					$queryPrenotazione = "UPDATE Prenotazioni SET Assegnati = ".$minutiNew.",
																  OraInizio = '".$inizio."',
																  OraFine = '".$fine."'
																  WHERE Email = '".$utente_corrente."'";
					if( !mysqli_query($statoConnessione, $queryPrenotazione) )
						throw new Exception("Comando fallito nella transazione<br>");
					//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
					
				}
				/**********************************************/
				echo "<h3>Cancellazione effettuata con successo</h3>";
			}
		
			
			//LIBERARE LE RISORSE !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
			mysqli_free_result($risultato);
			/////////////////////!!!!!!!!!!!!!!!!!!!!!!!////////////////
			if(!mysqli_commit($statoConnessione))
			{
				throw new Exception("Commit fallito<br>");
			}
		}
		catch (Exception $e)
		{
			mysqli_rollback($statoConnessione);
			echo "<br>Rollback ".$e->getMessage()."<br>";
			echo "---->".$var;
		}
		finally{
			mysqli_close($statoConnessione);
		}
	}
	else 
		echo "Impossibile connettersi al database ed effettuare la prenotazione.<br>";
	
}
  
  /*
   *
   */
  function registrazione($user, $pass)
  {
  	
  	if( isset($_SESSION['myuser-s239214']))
  	{
  		//sto cercando di registrarmi ma sono già loggato, quindi sono già registrato
  		/*
  		 * redirect
  		 */
  		header('HTTP/1.1 307 temporary redirect');
  		header("Location: GiaRegistrato.php?msg=".urlencode(""));
  		exit();
  	}
  	$_SESSION["username-s239214"] = $user;
  	$_SESSION["password-s239214"] = $pass;
  	
  	$username = nl2br(htmlentities($_SESSION["username-s239214"]));
  	$password = nl2br(htmlentities($_SESSION["password-s239214"]));
  	
  	$s1 = controllaEmail($username);
  	$s2 = controllaPassword($password);
  	if($s1 == false || $s2 == false)
  	{
  		/*
  		 * redirect
  		 */
  		header('HTTP/1.1 307 temporary redirect');
  		header("Location: Redirezione.php?msg=".urlencode(""));
  		exit();
  		
  	}
  	else
  	{
  		$statoConnessione = connessioneDB("s239214");
  		if($statoConnessione != NULL)
  		{
  			try
  			{
  				mysqli_autocommit($statoConnessione, false);
  				
  				if( !($risultato = mysqli_query($statoConnessione, "SELECT * FROM utenti WHERE Email = '".$username."' FOR UPDATE", MYSQLI_STORE_RESULT)) )
  					throw new Exception("Registrazione fallita.<br>");
  					if( mysqli_num_rows($risultato) == 0)
  					{
  						$nuovoUtente = "INSERT INTO utenti (Email, Password) VALUES ( '".$username."' , '".md5($password)."' )";
  						//$nuovoUtente = "INSERT INTO utenti SET email = ' ".$username ." ', password = ' ".$password ." ' ";
  						if(!mysqli_query($statoConnessione, $nuovoUtente))
  						{
  							//echo "<h4>Non e' stato possibile inserire l'utente, riprova piu' tardi.</h4><br>";
  							/*
  							 * rimandato
  							 */
  							/*header('HTTP/1.1 307 temporary redirect');
  							 header("Location: Redirezione.php?msg=".urlencode(""));
  							 exit();*/
  							throw new Exception("Registrazione fallita.<br>");
  						}
  						else
  						{
  							/*
  							 * setto la variabile di utente
  							 */
  							$_SESSION['myuser-s239214'] = $username;
  						}
  					}
  					else
  					{
  						//echo "<h4>L'utente e' gia' registrato.</h4>";
  						/*
  						 * rimandato
  						 */
  						header('HTTP/1.1 307 temporary redirect');
  						header("Location: GiaRegistrato.php?msg=".urlencode(""));
  						//rilascio risorse
  						mysqli_free_result($risultato);
  						exit();
  						
  					}
  					//rilascio risorse
  					mysqli_free_result($risultato);
  					
  					if( !mysqli_commit($statoConnessione))
  						throw new Exception("Comando fallito");
  						
  						mysqli_close($statoConnessione);
  			}//fine try
  			
  			catch(Exception $e)
  			{
  				mysqli_rollback($statoConnessione);
  				mysqli_close($statoConnessione);
  				echo "<br>Rollback ".$e->getMessage()."<br>";
  				
  			}
  		}
  		else
  		{
  			header('HTTP/1.1 307 temporary redirect');
  			header("Location: Redirezione.php?msg=".urlencode(""));
  			exit();
  		}
  	}
  }
  
function calcolaOrario($oraInizio, $offset)
{
	$t1=explode(":",$oraInizio);//$t1[0]==ora   $t1[1]==minuti
	
	$ora = $t1[0];
	$minuti = $t1[1];
	
	
	$minuti += $offset;//minuti totali
	
	$ora += floor($minuti/60);
	$minuti = fmod($minuti,60);
	//echo $minuti;
	
	if($minuti == 0)
		$minuti = '00';
	else if($minuti<10)
		$minuti = "0".$minuti;
	
	return $ora.":".$minuti;
}
   
   
?>








