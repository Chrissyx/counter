<?php
//$action laden
$action = (!$_POST['action']) ? $_GET['action'] : $_POST['action'];

//Landeseinstellung
setlocale(LC_ALL, "");

//echo Kurzform aktivieren
if (ini_get(short_open_tag) == "0") ini_set(short_open_tag, "1");

//Session laden, TBB2 kompatibel
session_start();
if (!$_SESSION['session_ip']) $_SESSION['session_ip'] = $_SERVER['REMOTE_ADDR'];
else if ($_SESSION['session_ip'] != $_SERVER['REMOTE_ADDR']) die("Nicht erlaubt, diese Session zu verwenden!");

//Funktionen
function head($htmlzusatz, $title, $keywords, $description, $stylesheet, $sonstiges, $bodyzusatz)

#####################################################################
#Script written by Chrissyx                                         #
#You may use and edit this script, if you don't remove this comment!#
#http://www.chrissyx.de(.vu)/                                       #
#####################################################################

{
 $temp = explode(" ", microtime());
 $_SESSION['microtime_start'] = $temp[1] + $temp[0];
 echo("<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\"
   \"http://www.w3.org/TR/html4/loose.dtd\">
<html lang=\"de\"$htmlzusatz>
 <head>
  <title>$title</title>
  <meta name=\"author\" content=\"Chrissyx\">
  <meta name=\"copyright\" content=\"Chrissyx\">
  <meta name=\"keywords\" content=\"$keywords\">
  <meta name=\"description\" content=\"$description\">
  <meta name=\"robots\" content=\"index, follow\">
  <meta name=\"revisit-after\" content=\"7 days\">
  <meta name=\"generator\" content=\"Notepad 4.10.1998\">
  <meta name=\"DC.Language\" scheme=\"rfc1766\" content=\"de\">
  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=ISO-8859-1\">
  <meta http-equiv=\"Content-Style-Type\" content=\"text/css\">
  <link rel=\"stylesheet\" media=\"all\" href=\"$stylesheet\">
  <link rel=\"shortcut icon\" href=\"favicon.ico\">
  <script src=\"javascripts.js\" type=\"text/javascript\"></script>
  " . $sonstiges . "
 </head>
 <body$bodyzusatz>
  <a name=\"top\"></a>\n");
}

function tail()
{

#####################################################################
#Script written by Chrissyx                                         #
#You may use and edit this script, if you don't remove this comment!#
#http://www.chrissyx.de(.vu)/                                       #
#####################################################################

 $temp = explode(" ", microtime());
 $temp = ($temp[1] + $temp[0]) - $_SESSION['microtime_start'];
 echo("  <div class=\"center\">");
 font("1");
 echo("Seitenaufbauzeit: $temp Sekunden - Powered by V3 Technology<br />\n&copy; 2004, 2005 by Chrissyx - <a href=\"http://www.chrissyx.de.vu\" target=\"_blank\">http://www.chrissyx.de.vu</a></span></div>
 </body>
</html>");
}

function font($wert)

#####################################################################
#Script written by Chrissyx                                         #
#You may use and edit this script, if you don't remove this comment!#
#http://www.chrissyx.de(.vu)/                                       #
#####################################################################

{
 switch($wert)
 {
  case "7":
  echo("<span style=\"font-size:300%;\">");
  break;

  case "6":
  echo("<span style=\"font-size:xx-large;\">");
  break;

  case "5":
  echo("<span style=\"font-size:x-large;\">");
  break;

  case "4":
  echo("<span style=\"font-size:large;\">");
  break;

  case "3":
  echo("<span style=\"font-size:medium;\">");
  break;

  case "2":
  echo("<span style=\"font-size:small;\">");
  break;

  case "1.5":
  echo("<span style=\"font-size:x-small;\">");
  break;

  case "1":
  echo("<span style=\"font-size:xx-small;\">");
  break;

  default:
  echo("<span style=\"font-size:$wert;\">");
  break;
 }
}
?>