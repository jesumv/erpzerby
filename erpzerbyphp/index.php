<?php
//directiva a la conexion con base de datos
include_once "php/config.php";

session_start();

$error = "";

if($_SERVER["REQUEST_METHOD"] == "POST")
    {
        // username and password sent from form 
        $myusername=mysql_real_escape_string($_POST['username']); 
        $mypassword=mysql_real_escape_string($_POST['password']); 
        $sql="SELECT id,nombre,empresa,nivel FROM admin WHERE username='$myusername' 
        and passcode='$mypassword'";
        $result=mysql_query($sql);
        $row=mysql_fetch_array($result);
        $nivel =$row[3];
        $username = $row[1];
        $empre = $row[2];
        $count=mysql_num_rows($result);
        
        
    // If result matched $myusername and $mypassword, table row must be 1 row
    if($count==1)
    {
		$_SESSION["myusername"] = $username;
        $_SESSION['login_user']=$myusername;
        $_SESSION['username']=$username;
        $_SESSION['nivel']=$nivel;
        $_SESSION['empresa']=$empre;
        
        //selección de hoja según empresa
        switch ($empre) {
            case 0:
                header("location: listaorden.php");
                break;
            case 1:
                header("location: listaorden.php");
                break;
            case 2:
                header("location: confe/inventconfe.php");
                break;
             case 3:
                 header("location: cxc.php");
                break;
            
            default:
                 header("location: php/logout.php");
                break;
        }
        
        
    }
//los datos de acceso no son correctos    
else 
    {
        $error="Su nombre de usuario o contraseña son invalidos";
    }
}
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>INTRANET ZERBY</title>
         <link rel="stylesheet" type="text/CSS" href="css/plantilla1.css" />
         
         
    </head>
     <body >

    		

            <div id="bandasup">
              
             
                
                <div id="loginbox">
            
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                        <label>Usuario  :</label><input type="text" name="username" class="box"/>
                        <label>Contraseña :</label><input type="password" name="password" class="box" />
                        <input type="submit" value=" Enviar "/><br />
                    </form>
                    
                        <div style="font-size:16px; color:#cc0000; margin-top:10px" align="center"> <?php echo $error; ?></div>
                
                </div>
                
                <h1 >Zerby Entregas Oportunas</h1>
                
                <div >
                  <img id="logoprinc1" src="img/logozerby.jpg" alt="logo zerby" />  
                 </div>
           </div>      			
    	
       
    </body>
</html>
