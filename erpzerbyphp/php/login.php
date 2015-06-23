<?php

// get MySQL login data
require("config.php");
// enable sessions
session_start();

$error = "";

if($_SERVER["REQUEST_METHOD"] == "POST")
	{
		// username and password sent from form 
		
		$myusername='test'; 
		$mypassword='test'; 
		
		$sql="SELECT nombre FROM admin WHERE username= $myusername and passcode=$mypassword;
		$result=mysql_query($sql);
		$row=mysql_fetch_array($result);
		$active=$row['active'];
		
		$count=mysql_num_rows($result);
		
		
	// If result matched $myusername and $mypassword, table row must be 1 row
	if($count==1){
	    session_register("myusername");
		$_SESSION['login_user']=$myusername;
       		 $_SESSION['username']=$row[0];
		header("location: welcome.php");
	}
else 
	{
		//$error="Su nombre de usuario o contraseña son invalidos";
		session_register("myusername");
		$_SESSION['login_user']=$myusername;
       		 $_SESSION['username']=$row[0];
		header("location: welcome.php");
	
	}
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>INTRANET ZERBY</title>

<style type="text/css">
body
{
font-family:Arial, Helvetica, sans-serif;
font-size:14px;

}
label
{
font-weight:bold;

width:100px;
font-size:14px;

}
.box
{
border:#666666 solid 1px;

}
</style>
</head>
<body bgcolor="#FFFFFF">

	<div align="center">
	<div style="width:300px; border: solid 1px #333333; " align="left">
	<div style="background-color:#333333; color:#FFFFFF; padding:3px;"><b>Registro de Usuario</b></div>
	<div style="margin:30px">

<form action="" method="post">
	<label>Usuario  :</label><input type="text" name="username" class="box"/><br /><br />
	<label>Contraseña :</label><input type="password" name="password" class="box" /><br/><br />
	<input type="Submit" value=" Enviar "/><br />
</form>
	<div style="font-size:11px; color:#cc0000; margin-top:10px"> <?php echo $error; ?></div>
	</div>
	</div>
	</div>

</body>
</html>
