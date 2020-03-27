
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta content="text/html; charset=utf-8" http-equiv="content-type">
    <title>Visiona</title>
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
  					include 'Funzioni.php';
  					/**/
  					checkHttps();
  					checkCookies();
  					/**/
  					$stato_connessione = connessioneDB("s239214");
  					if($stato_connessione != NULL)
  					{
  						try
  						{
  							mysqli_autocommit($stato_connessione, false);
  							//echo "Connessione riuscita.<br><br><br>";
  							
  							if( !($risultato = mysqli_query($stato_connessione, "SELECT * FROM Prenotazioni FOR UPDATE")) )
  								throw new Exception("Operazione fallita");
  							
  							echo "<h3>Prenotazioni effettuate:</h3><br>";
  							echo "<table border=1 align=center><tbody>";
  							$totaleRichiesti = 0;
  							$totaleAssegnati = 0;
  							echo "<tr> <td>Email</td>	<td>Richiesti</td>	 <td>Assegnati</td>	  <td>Ora inizio</td>   <td>Ora fine</td></tr>";
  							while( ($riga = mysqli_fetch_array($risultato, MYSQLI_BOTH)))
  							{
  								echo "<tr> <td>".$riga['Email']."</td>	<td>".$riga['Richiesti']."</td>	 <td>".$riga['Assegnati']."</td>  <td>".$riga['OraInizio']."</td>   <td>".$riga['OraFine']."</td> </tr>";
  								$totaleRichiesti += $riga['Richiesti'];
  								$totaleAssegnati += $riga['Assegnati'];
  							}
  							echo"</tbody></table>";
  							
  							echo"<br><br>Sono stati richiesti in totale ".$totaleRichiesti." minuti.";
  							echo"<br>Sono stati assegnati in totale ".$totaleAssegnati." minuti";
  							
  		
  		
  							mysqli_free_result($risultato);
  							
  							if( !mysqli_commit($stato_connessione))
  								throw new Exception("Comando fallito");
  							
  							mysqli_close($stato_connessione);
  						}//fine try
  						catch (Exception $e)
  						{
  							mysqli_rollback($stato_connessione);
  							mysqli_close($stato_connessione);
  							echo "<br>Rollback ".$e->getMessage()."<br>";
  						}
  					}
  		
					else 
						echo "Torna sulla homepage del sito e riprova in un secondo momento.";
  				?>
  			</div>
  			
  		</div>
  		
  		<div id="Footer">Footer</div>
  		
 	</div>
  	
  	

  </body>
</html>