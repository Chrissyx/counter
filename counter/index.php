<?php

#####################################################################
#Script written by Chrissyx                                         #
#You may use and edit this script, if you don't remove this comment!#
#http://www.chrissyx.de(.vu)/                                       #
#####################################################################

//Check, ob Verzeichnis existiert
if (!is_dir("../counter")) die ("<b>ERROR: Verzeichnis \"counter\" nicht gefunden!</b>");

 include("functions.php");

 if ($_POST['mode'] == "login" or $_SESSION['pw'])
 {
  if (!file_exists("pw.dat"))
  {
   $temp = fopen("pw.dat", "w");
   fwrite($temp, md5($_POST['pw']));
   fclose($temp);
   $_SESSION['pw'] = md5($_POST['pw']);
   head("", "CHS - Counter: Administration", "", "", "style.css", "", "");
   echo("Passwort gespeichert - bitte damit <a href=\"" . $_SERVER['PHP_SELF'] . "\">einloggen!</a>");
   tail();
  }
  else
  {
   $pw = file("pw.dat");
   if ((md5($_POST['pw']) == $pw[0]) or $_SESSION['pw'] == $pw[0])
   {
    if (!$_SESSION['pw']) $_SESSION['pw'] = $pw;
    unset($pw);
#-------------------------------
    switch($action)
    {
     case "install":
     if ($_POST['install'])
     {
      head("", "CHS - Counter: Administration - Installation", "Counter, CHS, Chrissyx", "Counter von CHS", "style.css", "", "");
      echo("  Starte Installation, initialisiere Counter...<br />\n");
      if ($_POST['backup2']) $_POST['counter'] .= "\n" . $_POST['backup2'];
      $temp = fopen("counter.dat", "w");
      fwrite($temp, $_POST['counter']);
      fclose($temp);
      echo("  Counter initialisiert!<br />\n");
      if ($_POST['backup2'])
      {
       echo("  Initialisiere BackUp Funktion...<br />\n");
       $temp = fopen("backup.dat", "w");
       fwrite($temp, $_POST['counter']);
       fclose($temp);
       echo("  BackUp Funktion initialisiert!<br />\n");
      }
      if ($_POST['ipsperre'])
      {
       echo("  Initialisiere IP Sperre...<br />\n");
       $temp = fopen("ip.dat", "w");
       fwrite($temp, $_SERVER['REMOTE_ADDR'] . "\n");
       fclose($temp);
       echo("  IP Sperre initialisiert!<br />\n");
      }
      echo("  Installation beendet!<br /><br />\n");
      ?>

  Um den Counter nun zu nutzen, füge diesen Code an der gewünschten Stelle in den Quelltext deiner Seite ein:<br /><br />
  <code>&lt;!-- CHS - Counter --&gt;&lt;?php include("counter/counter.php<?=$_POST['bild']?>"); ?&gt;&lt;!-- /CHS - Counter --&gt;</code><br /><br />
  <a href="<?=$_SERVER['PHP_SELF']?>">Zurück zur Administration</a><br />

      <?php
      tail();
     }
     else
     {
      head("", "CHS - Counter: Administration - Installation", "Counter, CHS, Chrissyx", "Counter von CHS", "style.css", "", "");
      font("4")
      ?>

  CHS - Counter: Installation</span><br /><br />
  Hier kannst Du deinen Counter einrichten und installieren. Bitte treffe deine Einstellungen:<br /><br />
  <form action="<?=$_SERVER['PHP_SELF']?>" method="post">
  Startwert: <input type="text" name="counter" value="0"><br /><br />
  BackUp Funktion aktivieren?<br />
  <input type="radio" name="backup" onClick="this.form.backup2.disabled=false;">Ja, alle <input type="text" name="backup2" value="50" disabled> Hits<br />
  <input type="radio" name="backup" onClick="this.form.backup2.disabled=true;" checked>Nein<br /><br />
  <input type="checkbox" name="ipsperre" value="true"> IP Sperre aktivieren?<br /><br />
  <input type="radio" name="bild" value="" disabled>Ausgabe des Counterstandes als Bild (z.B. "<img src="1.png" alt="1"><img src="2.png" alt="2"><img src="3.png" alt="3">")<br />
  <input type="radio" name="bild" value="" checked>Ausgabe des Counterstandes als Text (z.B. "123")<br /><br />
  <input type="submit" value="Installieren!"> <input type="reset" value="Reset"> <input type="button" value="Zurück" onClick="document.location.href='<?=$_SERVER['PHP_SELF']?>';">
  <input type="hidden" name="action" value="install">
  <input type="hidden" name="install" value="true">
  <input type="hidden" name="mode" value="login">
  </form>

      <?php
      tail();
     }
     break;

     case "uninstall":
     head("", "CHS - Counter: Administration - Deinstallation", "Counter, CHS, Chrissyx", "Counter von CHS", "style.css", "", "");
     echo("  Lösche Counterstand...<br />\n");
     unlink("counter.dat");
     echo("  Counterstand gelöscht!<br />");
     if (file_exists("backup.dat"))
     {
      echo("\n  Lösche BackUp...<br />\n");
      unlink("backup.dat");
      echo("  BackUp gelöscht!<br />");
     }
     if (file_exists("ip.dat"))
     {
      echo("\n  Lösche IP Sperre...<br />\n");
      unlink("ip.dat");
      echo("  IP Sperre gelöscht!<br />");
     }
     echo("<br />\n");
     ?>

  Falls Du den Counter ansich überhaupt nicht mehr nutzen willst, entferne den damals eingefügten Code. Du kannst ihn anhand diesem Kommentar wiederfinden, falls Du nicht mehr weisst, wo er ist: <code>&lt;!-- CHS - Counter --&gt;</code><br />
  Lösche danach den Ordner "counter" von deinem Server.<br /><br />
  <a href="<?=$_SERVER['PHP_SELF']?>">Zurück zur Administration</a><br />

     <?php
     tail();
     break;

     case "backupload":
     head("", "CHS - Counter: Administration", "Counter, CHS, Chrissyx", "Counter von CHS", "style.css", "<meta http-equiv=\"refresh\" content=\"2; URL=backup.dat\">", "");
     ?>

     <span class="b">Aktueller BackUp wird gesendet...</span><br /><br />
     Wenn der Download nicht automatisch startet, bitte <a href="backup.dat">hier</a> klicken!<br /><br />
     <input type="button" value="Zurück" onClick="document.location.href='<?=$_SERVER['PHP_SELF']?>';"><br />

     <?php
     tail();
     break;

     case "backupsave":

     if ($_FILES['newbackup'])
     {
      head("", "CHS - Counter: Administration", "Counter, CHS, Chrissyx", "Counter von CHS", "style.css", "", "");
      if (move_uploaded_file($_FILES['newbackup']['tmp_name'], "backup.dat")) echo("BackUp empfangen!<br />\n Verarbeite...<br />\n");
      if (copy("backup.dat", "counter.dat")) echo("BackUp eingespielt!<br /><br />\n Name: " . $_FILES['newbackup']['name'] . "<br />\n Grösse: " . $_FILES['newbackup']['size'] . "<br />\n");
      ?>
  <br />
  <input type="button" value="Zurück" onClick="document.location.href='<?=$_SERVER['PHP_SELF']?>';"><br />

      <?php
      tail();
     }
     else
     {
      head("", "CHS - Counter: Administration", "Counter, CHS, Chrissyx", "Counter von CHS", "style.css", "", "");
      ?>

     <span class="b">BackUp einspielen</span><br /><br />
     Hier kannst Du ein gesichertes BackUp einspielen und den Counter auf diesen Stand zurück setzen. Bitte wähle dein BackUp:<br /><br />
     <form action="<?=$_SERVER['PHP_SELF']?>" method="post" enctype="multipart/form-data">
     <input type="file" name="newbackup"><br /><br />
     <input type="submit" value="Hochladen"> <input type="button" value="Zurück" onClick="document.location.href='<?=$_SERVER['PHP_SELF']?>';"><br />
     <input type="hidden" name="action" value="backupsave">
     <input type="hidden" name="mode" value="login">
     </form>

      <?php
      tail();
     }
     break;

     default:
     head("", "CHS - Counter: Administration", "Counter, CHS, Chrissyx", "Counter von CHS", "style.css", "", "");
     font("5");
     ?>

  Willkommen!</span><br /><br />
  Hier kannst Du deinen Counter verwalten. Bitte wähle eine Option:<br /><br />
  <table>
   <tr>
    <td>
     <form action="<?=$_SERVER['PHP_SELF']?>" method="post">
     <input type="submit" value="Installieren / Deinstallieren"<?php if (file_exists("counter.dat")) echo(" onClick=\"return confirm('Sicher? Der jetztige Counterstand wird NICHT gespeichert!');\""); ?>>
     <input type="hidden" name="mode" value="login">
     <input type="hidden" name="action" value="<?php (file_exists("counter.dat")) ? print("uninstall") : print("install"); ?>">
     </form>
    </td>
    <td>
     <form action="<?=$_SERVER['PHP_SELF']?>" method="post">
     <input type="submit" value="BackUp abrufen"<?php (!file_exists("backup.dat")) ? print(" disabled") : print(""); ?>>
     <input type="hidden" name="mode" value="login">
     <input type="hidden" name="action" value="backupload">
     </form>
    </td>
    <td>
     <form action="<?=$_SERVER['PHP_SELF']?>" method="post">
     <input type="submit" value="BackUp einspielen"<?php (!file_exists("backup.dat")) ? print(" disabled") : print(""); ?>>
     <input type="hidden" name="mode" value="login">
     <input type="hidden" name="action" value="backupsave">
     </form>
    </td>
    <td class="top">
     <input type="button" value="Beenden" onClick="window.close();">
    </td>
   </tr>
  </table>

     <?php
     tail();
     break;
    }
#-------------------------------
   }
   else die("Falsches Passwort!");
  }
 }
 else
 {
  head("", "CHS - Counter: Administration - LogIn", "", "", "style.css", "", "");
  ?>

  CHS - Counter - LogIn<br />
  <form action="<?=$_SERVER['PHP_SELF']?>" method="post">
  Bitte Passwort angeben: <input type="password" name="pw"><br />
  <input type="submit" value="Einloggen">
  <input type="hidden" name="mode" value="login">
  </form>

  <?php
  tail();
 }
?>