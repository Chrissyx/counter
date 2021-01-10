<?php

############################################################
#Script written by Chrissyx				   #
#You may use this script, if you don't remove this comment!#
#http://www.chrissyx.de(.vu)/				   #
############################################################

//Check, ob Verzeichnis existiert
if (!is_dir("../counter")) die ("<b>ERROR: Verzeichnis \"counter\" nicht gefunden!</b>");

//Passwortgeschichte
$pw = $HTTP_GET_VARS["pw"];
if (!$pw and !file_exists("pw.var")) die ("<form action=\"install.php\">Passwort festlegen: <input type=\"text\" name=\"pw\"> <input type=\"submit\" value=\"Weiter\"></form>");
elseif ($pw and !file_exists("pw.var"))
{
 $pwvar = fopen ("pw.var", "w");
 fwrite ($pwvar, $pw);
 fclose ($pwvar);
}
else
{
 $pwvar = fopen ("pw.var", "r");
 $pwreal = fgets ($pwvar);
 fclose ($pwvar);
 if (!$pw) die ("Kein Passwort angegeben!");
 elseif ($pwreal <> $pw) die ("Falsches Passwort!");
}

//chmodden
$script = "install.php";
$chmod = decoct(fileperms($script));
if (!$chmod == 100777) chmod ($script, 0777);
$chmod = decoct(fileperms($script));
if (!$chmod == 100777) die ("<b>ERROR: Konnte keine Rechte setzen! Bitte \"install.php\" manuell auf \"chmod 777\" setzen und Script erneut ausführen!");

$script = "../counter";
$chmod = decoct(fileperms($script));
if (!$chmod == 40777) chmod ($script, 0777);
$chmod = decoct(fileperms($script));
if (!$chmod == 40777)  die ("<b>ERROR: Konnte keine Rechte setzen! Bitte das Verzeichnis \"counter\" manuell auf \"chmod 777\" setzen und Script erneut ausführen!");

//Zwischenspeicher löschen
clearstatcache();

//Auf geht's
$counter = $_GET["counter"];
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//DE"
   "http://www.w3.org/TR/html4/loose.dtd">
<html lang="de">
 <head>
  <link rel="SHORTCUT ICON" href="http://www.chrissyx.com/favicon.ico">
  <title>Chrissyx Homepage Scripts - Counter Administration</title>
  <meta http-equiv="content-type" content="text/html; charset=ISO-8859-1">
  <meta name="author" content="Chrissyx">
  <meta name="dc.language" scheme="rfc1766" content="de">
  <link rel="stylesheet" type="text/css" href="http://www.chrissyx.com/style.css">

  <script type="text/javascript">
  function frage(z)
  {
   if (z==1) if (confirm("Sicher? Der jetztige Counterstand wird NICHT gespeichert!")) location.href="install.php?pw=<?php echo ($pwreal); ?>&counter=reset";
   if (z==2) location.href="install.php?pw=<?php echo ($pwreal); ?>&counter=save";
   if (z==3) location.href="install.php?pw=<?php echo ($pwreal); ?>&counter=backup";
   if (z==4) self.close();
  }
  </script>

 </head>
 <body>

  <?php
  if ($counter == save)
  {
   ?>
    <b>Counter BackUp sichern</b><br><br>
    Klicke <a href="backup.var">hier</a>, um den jetzigen BackUp Stand zu sichern. Bewahre die Datei "backup.var" gut auf, wenn Du sie mal einspielen willst!<br>
    Klicke danach <a href="install.php?pw=<?php echo ($pwreal); ?>">hier</a>, um in die Administration zurück zu kommen.
   <?php

   die ("<br><br><br><br>© 2004 by Chrissyx<br><a href=\"http://www.chrissyx.com/\" target=\"_blank\">http://www.chrissyx.com/</a><br><a href=\"http://www.chrissyx.de(.vu)/\" target=\"_blank\">http://www.chrissyx.de(.vu)/</a>");
  }

  if ($counter == reset)
  {
   if (file_exists("backup.var")) unlink ("backup.var");
   unlink ("counter.var");
   ?>

  <b>Counter Deinstallation</b><br><br>
  Counter deinstalliert.<br><br><br>
  <b>Counter Neuinstallation</b><br><br>
  Klicke <a href="install.php?pw=<?php echo ($pwreal); ?>">hier</a>, für eine Neuinstallation des Counters.<br>
  Klicke <a href="javascript:self.close()">hier</a>, um zu beenden.

  <?php
  die ("<br><br><br><br>© 2004 by Chrissyx<br><a href=\"http://www.chrissyx.com/\" target=\"_blank\">http://www.chrissyx.com/</a><br><a href=\"http://www.chrissyx.de(.vu)/\" target=\"_blank\">http://www.chrissyx.de(.vu)/</a>");
  }

  if ($counter == upload)
  {
   unlink ("backup.var");
   ?>

    <b>"backup.var" Upload</b><br><br>
    Uploading...<br><br>

   <?php
   if (isset($_FILES["upload"]) and ! $_FILES["upload"]["error"])
   {
    move_uploaded_file($_FILES["upload"]["tmp_name"], "backup.var");
    echo ("Datei " . $_FILES["upload"]["name"] . " wurde hochgeladen. Sie ist " . $_FILES["upload"]["size"] . " Byte gross und vom Typ " . $_FILES["upload"]["type"] . ".<br><br>");
   }
   ?>

    Klicke <a href="install.php?pw=<?php echo ($pwreal); ?>">hier</a>, um in die Administration zurück zukommen.

   <?php
   die ("<br><br><br><br>© 2004 by Chrissyx<br><a href=\"http://www.chrissyx.com/\" target=\"_blank\">http://www.chrissyx.com/</a><br><a href=\"http://www.chrissyx.de(.vu)/\" target=\"_blank\">http://www.chrissyx.de(.vu)/</a>");
  }

  if ($counter == backup)
  {
   ?>

   <b>Counter BackUp einspielen</b><br><br>
   Hier kannst Du ein altes BackUp einspielen.<br>
   Bitte lade deine alte "backup.var" hier hoch!<br><br>

   <form action="install.php?pw=<?php echo ($pwreal); ?>&counter=upload" method="post" enctype="multipart/form-data">
   <!--Passwort eingeben: <input type="text" name="pw"><br><br>-->
   <input type="file" name="upload">
   <input type="submit" value="Hochladen">
   </form>

   <a href="install.php?pw=<?php echo ($pwreal); ?>">Abbruch.</a>

   <?php

   die ("<br><br><br><br>© 2004 by Chrissyx<br><a href=\"http://www.chrissyx.com/\" target=\"_blank\">http://www.chrissyx.com/</a><br><a href=\"http://www.chrissyx.de(.vu)/\" target=\"_blank\">http://www.chrissyx.de(.vu)/</a>");
  }

  if (file_exists("counter.var"))
  {
  
  ?>

  <b>Wilkommen in der Counter Administration!</b><br><br>
  Hier kannst Du den Counter deinstallieren, neuinstallieren oder (wenn aktiviert) das aktuelle BackUp sichern oder ein altes BackUp einspielen.<br>
  Bitte wähle aus:<br><br>

  <input type="button" value="Deinstallation/Neuinstallation" onClick="frage(1)">

  <?php
   if (file_exists("backup.var"))
   {
    ?>

   <input type="button" value="BackUp abrufen" onClick="frage(2)"> <input type="button" value="BackUp einspielen" onClick="frage(3)">

    <?php
   } else
   {
    ?>

   <input type="button" value="BackUp abrufen" onClick="frage(2)" disabled> <input type="button" value="BackUp einspielen" onClick="frage(3)" disabled>

  <?php
   }
   ?>

    <input type="button" value="Beenden" onClick="frage(4)">

   <?php
  } else
   {
    if ($counter == "")
    {
  ?>

  <table>
   <tr>
    <td>
     <form action="install.php">
     <u>Willkommen zur Chrissyx Homepage Scripts - Counter Installation!</u><br><br>
     Passwort eingeben: <input type="text" name="pw" tabindex="9"><br><br>
     Bitte gebe den Startwert an, bei dem der Counter starten soll:<br>
     <input type="text" name="counter" size="58" value="0" tabindex="1"><br><br>

     IP Sperre aktivieren?<br>
     <input type="radio" name="ip" value="1" disabled tabindex="2">Ja<br>
     <input type="radio" name="ip" value="0" disabled tabindex="3">Nein<br><br>

     BackUp Funktion aktivieren?<br>
     <input type="radio" name="backup" value="1" tabindex="4">Ja, alle <input type="text" name="hits" size="5" tabindex="5"> Hits<br>
     <input type="radio" name="backup" value="0" tabindex="6" checked>Nein<br><br>

     Wenn Du alle Einstellungen getroffen hast, klicke auf "Installieren".<br>
     <p align="center"><input type="submit" value="Installieren" tabindex="7"> <input type="reset" value="Reset" tabindex="8"></p>
     </form>
    </td>
   </tr>
  </table>

  <?php

  } else
  {
   if ($counter >= 0)
   {
    $backup = $_GET["backup"];
    switch ($backup)
    {
     case 1:
     {
      if (file_exists("backup.var"))
      {
       echo ("<b>\"backup.var\" exixtiert schon!</b>");
      } else
      {
      $hits = $_GET["hits"];
      $hits .= _;
      $hits .= $hits;
      $hits .= 0;
      $backupfile = fopen ("backup.var", "w");
      fwrite($backupfile, $hits);
      fclose($backupfile);
      }
     }
     case 0: default:
     {
     $counterfile = fopen ("counter.var", "w");
     fwrite($counterfile, $counter);
     fclose($counterfile);
     ?>

  <b>Installation erfolgreich!</b><br>
  Füge nun diese Codezeile in den Quelltext <u>an der entsprechenden Stelle</u> deiner Seite ein, um den Counter nutzen zu können:<br><br>
  <hr>
  <code>
  &lt;?php include ("counter/counter.php");  ?&gt;
  </code>
  <hr><br>

  Klicke <a href="install.php?pw=<?php echo ($pwreal); ?>">hier</a> oder rufe das Installationsscript nochmal auf, um in die Counter Administration zu kommen.<br>
  Ansonsten kannst Du das Installationsscript löschen, wenn Du keine weiteren Sachen machen möchtest.

     <?php
     }
    }
   } else echo ("<b>ERROR: Kein Startwert angegeben!</b>");
   }
  }


  ?>
  <br><br><br>
  © 2004 by Chrissyx<br>
  <a href="http://www.chrissyx.com/" target="_blank">http://www.chrissyx.com/</a><br>
  <a href="http://www.chrissyx.de(.vu)/" target="_blank">http://www.chrissyx.de(.vu)/</a>
  </font>
 </body>
</html>