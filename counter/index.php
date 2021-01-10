<?php

#####################################################################
#Script written by Chrissyx                                         #
#You may use and edit this script, if you don't remove this comment!#
#http://www.chrissyx.de(.vu)/                                       #
#####################################################################

$temp = explode('/', $_SERVER['PHP_SELF']);
if(($temp[count($temp)-2]) != 'counter') die('<b>ERROR:</b> Konnte Verzeichnis "counter" nicht finden! "index.php" aufgerufen?');
elseif(!file_exists('counter.php')) die('<b>ERROR:</b> Konnte "counter.php" nicht finden!');
elseif(!file_exists('style.css')) die('<b>ERROR:</b> Konnte "style.css" nicht finden!');
elseif(!file_exists('functions.php')) die('<b>ERROR:</b> Konnte "functions.php" nicht finden!');
else include('functions.php');

if(file_exists('settings.dat'))
{
 if(!$_SESSION['countpw'] && !$_POST['countpw']) $action = 'login';
 else
 {
  $pw = @file_get_contents('countpw.dat') or die('<b>ERROR:</b> Passwort nicht gefunden!');
  if(!$action || !in_array($action, array('login', 'logout', 'admin'))) $action = 'login';
 }
}

$temp = $temp[count($temp)-1];
if(decoct(fileperms($temp)) != '100775') chmod($temp, 0775) or die('<b>ERROR:</b> Konnte für "' . $temp . '" keine Rechte setzen!');
elseif(decoct(fileperms('counter.php')) != '100775') chmod('counter.php', 0775) or die('<b>ERROR:</b> Konnte für "counter.php" keine Rechte setzen!');
elseif(decoct(fileperms('../counter/')) != '40775') chmod('../counter/', 0775) or die('<b>ERROR:</b> Konnte für den Ordner "counter" keine Rechte setzen!');
clearstatcache();

switch($action)
{
 case 'login':
 if(md5($_POST['countpw']) == $pw)
 {
  $_SESSION['countpw'] = md5($_POST['countpw']);
  unset($_POST['countpw']);
  @header('Location: ' . $_SERVER['PHP_SELF'] . '?action=admin');
  die('Eingeloggt! <a href="' . $_SERVER['PHP_SELF'] . '?action=admin">Weiter...</a>');
 }
 else
 {
  head(null, 'CHS - Counter: Login', 'Counter, CHS, Login, Chrissyx', 'Login des Counters von CHS');
  font('4');
  ?>
  CHS - Counter: Login</span><br />
  <form action="<?=$_SERVER['PHP_SELF']?>" method="post">
  Bitte Passwort angeben: <input type="password" name="countpw" <?php if($_POST['countpw']) echo('style="border-color:#FF0000;" /><br />
  <span style="color:#FF0000; font-weight:bold;">&raquo; Falsches Passwort!</span><br '); ?>/><br />
  <input type="submit" value="Einloggen" />
  <input type="hidden" name="action" value="login" />
  </form>
  <?php
  tail();
  exit();
 }
 break;

 case 'admin':
 if($_SESSION['countpw'] != $pw) die('<b>ERROR:</b> Keine Adminrechte!');
 $settings = array_map('trim', file('settings.dat'));
 head(null, 'CHS - Counter: Administration', 'Counter, CHS, Administration, Chrissyx', 'Administration des Counters von CHS');
 if($_POST['update'])
 {
  $temp = "  <span style=\"color:#FF0000; font-weight:bold;\">&raquo; Bitte alle relevanten Felder korrekt ausfüllen!</span><br /><br />\n";
  if(!$_POST['counterdat']) $settings[0] .= '" style="border-color:#FF0000;';
  elseif(($_POST['backup'] != '') && ($_POST['backup'] < 2 || !$_POST['email']))
  {
   $settings[1] .= '" style="border-color:#FF0000;';
   $settings[2] .= '" style="border-color:#FF0000;';
  }
  elseif($_POST['countpw'] == $_POST['countpw2'])
  {
   if($_POST['countpw'])
   {
    $_SESSION['countpw'] = md5($_POST['countpw']);
    $temp = fopen('countpw.dat', 'w');
    fwrite($temp, $_SESSION['countpw']);
    fclose($temp);
   }
   if($_POST['counter'] != '')
   {
    $temp = fopen('../' . $settings[0], 'w');
    fwrite($temp, $_POST['counter']);
    fclose($temp);
   }
   if($_POST['counterdat'] != $settings[0])
   {
    $temp = fopen('../' . $_POST['counterdat'], 'w');
    fwrite($temp, file_get_contents('../' . $settings[0]));
    fclose($temp);
    unlink('../' . $settings[0]);
   }
   if($_POST['ipdat'] != $settings[3])
   {
    if($_POST['ipdat'] == '' && file_exists('../' . $settings[3])) unlink('../' . $settings[3]); //Sperre löschen
    else
    {
     $temp = fopen('../' . $_POST['ipdat'], 'w');
     if(file_exists('../' . $settings[3]) && !is_dir('../' . $settings[3]))
     {
      //Sperre umziehen
      fwrite($temp, file_get_contents('../' . $settings[3]));
      unlink('../' . $settings[3]);
	 }
     else fwrite($temp, $_SERVER['REMOTE_ADDR'] . "\n"); //Sperre erstellen
     fclose($temp);
	}
   }
   $temp = fopen('settings.dat', 'w');
   fwrite($temp, $_POST['counterdat'] . "\n" . $_POST['backup'] . "\n" . $_POST['email'] . "\n" . $_POST['ipdat'] . "\n" . $_POST['img']);
   fclose($temp);
   $settings = array_map('trim', file('settings.dat'));
   $temp = "  <span class=\"green\">&raquo; Neue Einstellungen gespeichert!</span><br /><br />\n";
  }
 }
 else unset($temp);
 font('4');
 ?>CHS - Counter: Administration</span><br /><br />
  Hier kannst Du alle Einstellungen deines Counter einsehen und anpassen.<br /><br />
<?=$temp?>  <form name="form" action="<?=$_SERVER['PHP_SELF']?>" method="post">
  <table>
   <tr><td colspan="2"><?php font('1'); ?>Wenn Du den aktuellen Counterstand nicht verändern willst, lasse das Feld einfach frei.</span></td></tr>
   <tr><td>Wert:</td><td><input type="text" name="counter" size="25" onfocus="this.value='<?=@file_get_contents('../' . $settings[0])?>';" /></td></tr>
   <tr><td>Speicherort Counter:</td><td><input type="text" name="counterdat" value="<?=$settings[0]?>" size="25" /></td></tr>
   <tr><td colspan="2"></td></tr>
   <tr><td>Backup per Mail? Alle</td><td><input type="text" name="backup" value="<?=$settings[1]?>" /> Hits</td></tr>
   <tr><td>E-Mail Backup Adresse:</td><td><input type="text" name="email" value="<?=$settings[2]?>" size="25" /></td></tr>
   <tr><td colspan="2"></td></tr>
   <tr><td colspan="2"><?php font('1'); ?>Falls Du dein Passwort nicht ändern willst, lasse die beiden Felder einfach frei.</span></td></tr>
   <tr><td>Passwort:</td><td><input type="password" name="countpw"<?php if($_POST['countpw'] != $_POST['countpw2']) echo(' style="border-color:#FF0000;"'); ?> size="25" /></td></tr>
   <tr><td>Passwort wiederholen:</td><td><input type="password" name="countpw2"<?php if($_POST['countpw'] != $_POST['countpw2']) echo(' style="border-color:#FF0000;"'); ?> size="25" /></td></tr>
   <tr><td colspan="2"></td></tr>
   <tr><td>IP Sperre Speicherort:</td><td><input type="text" name="ipdat" value="<?=$settings[3]?>" size="25" /></td></tr>
   <tr><td rowspan="2">Ausgabe des Counters</td><td><input type="radio" name="img" value="true"<?php if($settings[4] == 'true') echo(' checked="checked"'); ?> />als Bild (z.B. &quot;<img src="1.png" alt="1" /><img src="2.png" alt="2" /><img src="3.png" alt="3" />&quot;)</td></tr>
   <tr><td><input type="radio" name="img" value="false"<?php if($settings[4] == 'false') echo(' checked="checked"'); ?> />als Text (z.B. &quot;123&quot;)</td></tr>
  </table>
  <input type="submit" value="Update!" /> <input type="reset" value="Reset" /> <input type="button" value="Logout" onclick="document.location='<?=$_SERVER['PHP_SELF']?>?action=logout';" />
  <input type="hidden" name="action" value="admin" />
  <input type="hidden" name="update" value="true" />
  </form>
 <?php
 tail();
 break;

 case 'logout':
 unset($_SESSION['countpw']);
 @header('Location: ' . $_SERVER['PHP_SELF']);
 die('Ausgeloggt! <a href="' . $_SERVER['PHP_SELF'] . '">Weiter...</a>');
 break;

 case 'install':
 head(null, 'CHS - Counter: Installation', 'Counter, CHS, Installation, Chrissyx', 'Installation des Counters von CHS');
 echo("  Starte Installation...<br />\n");
 if((isset($_POST['counter']) && $_POST['counterdat'] && $_POST['img'] && $_POST['countpw']) && ($_POST['countpw'] == $_POST['countpw2']))
 {
  if(($_POST['backup']) && ($_POST['backup'] < 2 || !$_POST['email'])) echo('  <span class="b">ERROR:</span> Bitte alle relevanten Felder korrekt ausfüllen! <a href="' . $_SERVER['PHP_SELF'] . "\">Zurück...</a>\n  ");
  else
  {
   $temp = fopen('settings.dat', 'w');
   fwrite($temp, $_POST['counterdat'] . "\n" . $_POST['backup'] . "\n" . $_POST['email'] . "\n" . $_POST['ipdat'] . "\n" . $_POST['img']);
   fclose($temp);
   $temp = fopen('../' . $_POST['counterdat'], 'w');
   fwrite($temp, $_POST['counter']);
   fclose($temp);
   if($_POST['ipdat'])
   {
    $temp = fopen('../' . $_POST['ipdat'], 'w');
    fwrite($temp, $_SERVER['REMOTE_ADDR'] . "\n");
    fclose($temp);
   }
   $temp = fopen('countpw.dat', 'w');
   fwrite($temp, md5($_POST['countpw']));
   fclose($temp);
   echo("  Installation abgeschlossen!<br /><br />\n");
  ?>

  Um den Counter nun zu nutzen, füge diesen Code an der gewünschten Stelle in den Quelltext deiner Seite ein:<br /><br />
  <code>&lt;!-- CHS - Counter --&gt;&lt;?php include('counter/counter.php'); ?&gt;&lt;!-- /CHS - Counter --&gt;</code><br /><br />
  Sollte es Probleme geben, lies dir die FAQ in der Readme.txt durch oder frage zur Not im Forum unter <a href="http://www.chrissyx-forum.de.vu/" target="_blank">http://www.chrissyx-forum.de.vu/</a> nach.<br /><br />
  <a href="http://<?=$_SERVER['SERVER_NAME']?>/">Zur Seite</a>

  <?php
  }
 }
 else echo('  <span class="b">ERROR:</span> Bitte alle relevanten Felder korrekt ausfüllen! <a href="' . $_SERVER['PHP_SELF'] . "\">Zurück...</a>\n  ");
 tail();
 break;
 
 default:
 head(null, 'CHS - Counter: Installation', 'Counter, CHS, Installation, Chrissyx', 'Installation des Counters von CHS');
 ?>

  <script type="text/javascript">
  function help(data)
  {

  /*******************************************************************\
  *Script written by Chrissyx                                         *
  *You may use and edit this script, if you don't remove this comment!*
  *http://www.chrissyx.de(.vu)/                                       *
  \*******************************************************************/

   document.getElementById('help').firstChild.nodeValue = data;
  };
  </script>

  <?php font('4'); ?>CHS - Counter: Installation</span><br /><br />
  Hier kannst Du deine Shoutbox einrichten und installieren. Bitte treffe folgende Einstellungen:<br /><br />
  <form action="<?=$_SERVER['PHP_SELF']?>" method="post">
  <table onmouseout="help('Hier findest Du jeweils eine kleine Hilfe zu den Einstellungen. Aktiviere JavaScript, falls sich hier nichts ändert.');">
   <tr><td colspan="2"></td><td rowspan="13" width="200" style="background-color:yellow"><div id="help">Hier findest Du jeweils eine kleine Hilfe zu den Einstellungen. Aktiviere JavaScript, falls sich hier nichts ändert.</div></td></tr>
   <tr onmouseover="help('Lege hier den Startwert fest, mit dem der Counter starten soll.');"><td>Startwert:</td><td><input type="text" name="counter" value="0" size="25" /></td></tr>
   <tr onmouseover="help('Hier speichert der Counter den aktuellen Besucherstand und gibt diesen dann aus. Braucht eigentlich nicht geändert zu werden.');"><td>Speicherort Counter:</td><td><input type="text" name="counterdat" value="counter/counter.dat" size="25" /></td></tr>
   <tr><td colspan="2"></td></tr>
   <tr onmouseover="help('Optional: Du kannst dir zur Sicherheit immer einen bestimmten Counterstand per Mail schicken lassen, so dass im Falle eines Crashs keine Daten verloren gehen. Wenn ja, dann gebe hier an, nach wievielen Hits Du benachrichtigt werden möchtest. Der Wert sollte nicht zu klein sein, schließlich betreibst Du einen Counter und keine E-Mail Bombe. ;)');"><td>Backup per Mail? Alle</td><td><input type="text" name="backup" value="500" /> Hits</td></tr>
   <tr onmouseover="help('Wenn Du die &quot;Backup per Mail&quot;-Funktion nutzen möchtest, gib hier noch deine E-Mail Adresse an. Ist der Startwert des Counters auf 0, solltest Du gleich eine Testmail beim ersten Aufruf bekommen.');"><td>E-Mail Backup Adresse:</td><td><input type="text" name="email" size="25" /></td></tr>
   <tr><td colspan="2"></td></tr>
   <tr onmouseover="help('Gib hier dein Passwort an, zum späteren Verwalten des Counters.');"><td>Passwort:</td><td><input type="password" name="countpw" size="25" /></td></tr>
   <tr onmouseover="help('Das oben angegebene Passwort bitte wiederholen zur Verifizierung.');"><td>Passwort wiederholen:</td><td><input type="password" name="countpw2" size="25" /></td></tr>
   <tr><td colspan="2"></td></tr>
   <tr onmouseover="help('Wenn Du keinen Hit-Counter haben möchtest (Zählt jeden Seitenaufruf), sondern einen echten Besucherzähler (Zählt jede IP nur einmal), gib hier an, wo der Counter bereits gezählte IPs speichern soll. Der dann vorgegebene Wert braucht eigentlich nicht geändert zu werden.');"><td>IP Sperre Speicherort:</td><td><input type="text" name="ipdat" size="25" onfocus="this.value='counter/ip.dat';" /></td></tr>
   <tr onmouseover="help('Lege hier fest, wie die Ausgabe des Counters erfolgen soll. Text passt sich leichter dem Style deiner Seite an, allerdings kannst Du auch eigene Bilder für die Zahlen verwenden.');"><td rowspan="2">Ausgabe des Counters</td><td><input type="radio" name="img" value="true" />als Bild (z.B. &quot;<img src="1.png" alt="1" /><img src="2.png" alt="2" /><img src="3.png" alt="3" />&quot;)</td></tr>
   <tr onmouseover="help('Lege hier fest, wie die Ausgabe des Counters erfolgen soll. Text passt sich leichter dem Style deiner Seite an, allerdings kannst Du auch eigene Bilder für die Zahlen verwenden.');"><td><input type="radio" name="img" value="false" checked="checked" />als Text (z.B. &quot;123&quot;)</td></tr>
  </table>
  <input type="submit" value="Installieren!" onmouseover="help('Alles eingestellt? Dann los! :)');" /> <input type="reset" value="Reset" onmouseover="help('Stelle die Voreinstellungen wieder her.');" />
  <input type="hidden" name="action" value="install" />
  </form>
  <?php
 tail();
 break;
}
?>