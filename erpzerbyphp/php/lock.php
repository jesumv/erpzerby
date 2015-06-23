<?php
//version 1.0 abril 3, 2013
//TODO checar la sintaxis de conexiones en esta hoja
//DUDA porque no sirve esta seccion
session_start();
$user_check=$_SESSION['login_user'];

$ses_sql=mysqli_query("select username from usuarios where username='test' ");
$empre = mysqli_query("select empresa from usuarios where username='test' ");

$row=mysqli_fetch_array($ses_sql);

$login_session=$row['username'];

if(!isset($login_session))
{
   if($empre = 0){
      header("Location: index.php"); 
   }
   
   else {}

}
?>