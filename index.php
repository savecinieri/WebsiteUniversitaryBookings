 <?php
include 'Funzioni.php';
checkHttps();
checkCookies();
?>
  
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta content="text/html; charset=utf-8" http-equiv="content-type">
    <title>Prova web</title>
    <link rel="stylesheet" type="text/css" href="strutturaPagina.css">
    	<!-- rivedere checkemail e inserire parametri alle funzioni di check-->
		<script type="text/javascript">
		       	function checkPassword($id1, $id2)
				{
		       		var username = document.getElementById($id1).value;
		       		//var letmatch = username.match(/[a-zA-Z]/g);
		       		var letmatch = username.match(/[a-z]/g);
		       		var letmatchMaiusc = username.match(/[A-Z]/g);
		       	    var digmatch= username.match(/[0-9]/g);
		       	    if(letmatch && digmatch && letmatchMaiusc)
		       	    {
		       	    	var msg = document.getElementById($id2);
						msg.value = "Password valida";
		       	    }
		       	    else
		       	    {
		       	    	var msg = document.getElementById($id2);
						msg.value = "Password non valida";
						alert("Password non valida");	
		       	    }
		       		
				}
				function checkEmail($id1,$id2)
				{
					var email = document.getElementById($id1).value;
					//alert(email);
					if(email.indexOf("@") > -1)
					{
						var msg = document.getElementById($id2);
						msg.value = "Email valida";
					}
					else
					{
						var msg = document.getElementById($id2);
						msg.value = "Email non valida";
						alert("Email non valida");
					}
				}
				
		</script> 
  </head>
  
  <body>
  
  <div id="PageWrapper">
  	
  	<div id="Header"><h1> Benvenuto nel sito di prenotazioni.</h1></div>
  
  	<div id="Menu-Content-Wrap">
  		
  		<div id="Menu-Left">
  			<form action="Visiona.php" method="post">
  				Visiona le prenotazioni.<br>
  				<input type="submit" value="Vai alle prenotazioni.">
  			</form>	
  			
  			<br>
  			
  			<form action="Login.php" method="post">
  				<input type="text" name="username" value="Username" id="us" onchange=checkEmail("us","msgUs")>
  				<input type=text id="msgUs" readonly value="Warning di input"><br>
  				<input type="password" name="password" value="Password" id="pass" onchange=checkPassword("pass","msgPass")>
  				<input type=text id="msgPass" readonly value="Warning di input"> <br>
  	
  				<input type="submit" value="Effettua login.">
  				
  			</form>
  		
  			<br>
  			
  			<form action="Login.php" method="post">
  
  				<input type="text" name="usernameReg" value="Username" id=usReg onchange=checkEmail("usReg","msgUsReg")>
  				<input type=text id="msgUsReg" readonly value="Warning di input"><br>
  	
  				<input type="password" name="passwordReg" value="Password" id="pass2" onchange=checkPassword("pass2","msgPass2")>
  				<input type=text id="msgPass2" readonly value="Warning di input">															<br>
  	
  				<input type="submit" value="Effettua registrazione.">
 	 		</form>
  		
  		</div>
  		
  		
  		<div id="MainContent">
  			<noscript><h3>Attenzione, il sito potrebbe non funzionare: Javascript disabilitato!</h3></noscript>
  			<p>
  				Sito di prenotazioni gestito dal sito del politecnico di Torino.
  				<br>Se non sei registrato provvedi alla registrazione, effettua il login ed effettua la tua prenotazione.</br>
  			</p>
  		
  		</div>
  	
  	</div>
  	
  	<div id="Footer">Footer</div>
  	
  	</div> <!-- fine page wrapper -->
  	
  </body>
</html>