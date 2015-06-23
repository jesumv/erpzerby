<?php
    // Request selected language
$hl = (isset($_POST["hl"])) ? $_POST["hl"] : false;
if(!defined("L_LANG") || L_LANG == "L_LANG")
{
    if($hl) define("L_LANG", $hl);

    // You need to tell the class which language you want to use.
    // L_LANG should be defined as en_US format!!! Next line is an example, just put your own language from the provided list
    else define("L_LANG", "es_ES");
}

?>

<form action="somewhere.php" method="post">
<?php
//get class into the page
require_once("calendar/tc_calendar.php");

//instantiate class and set properties
$myCalendar = new tc_calendar("date1", true);
$myCalendar->setIcon("images/iconCalendar.gif");
$myCalendar->setDate(1, 1, 2000);

//output the calendar
$myCalendar->writeScript();   
?>
</form>