<?php
/**
 * Counter-Modul zum Zählen inkl. Ausgabe, IPs sperren und Backup mailen.
 * 
 * @author Chrissyx
 * @copyright (c) 2001 - 2009 by Chrissyx
 * @license http://creativecommons.org/licenses/by-nc-sa/3.0/ Creative Commons 3.0 by-nc-sa
 * @package CHS_Counter
 * @version 2.1
 */
//Caching
if(file_exists('counter/settings.php') && (filemtime('counter/settings.php') > filemtime('counter/settings.dat'))) include('counter/settings.php');
else
{
 //Config: Counter, Backup Hits, Addy, IP Sperre, Bildausgabe
 list($counterdat, $backup, $mail, $ipdat, $img) = @array_map('trim', file('counter/settings.dat')) or die('<b>ERROR:</b> Keine Einstellungen gefunden!');
 $temp = fopen('counter/settings.php', 'w');
 fwrite($temp, "<?php\n//Auto-generated config!\n\$counterdat = '$counterdat';\n\$backup = " . ($backup ? $backup : "''") . ";\n\$mail = '$mail';\n\$ipdat = '$ipdat';\n\$ips = file(\$ipdat);\n\$img = $img;\n?>");
 fclose($temp);
 $ips = file($ipdat);
 $img = ($img == 'false') ? false : true;
}

//Counting
$counter = file_get_contents($counterdat)+1;

//IP Sperre
if($ips)
{
 $save = false;
 if(in_array($_SERVER['REMOTE_ADDR'] . "\n", $ips)) $counter--;
 else
 {
  $temp = fopen($ipdat, 'a');
  fwrite($temp, $_SERVER['REMOTE_ADDR'] . "\n");
  fclose($temp);
  $save = true;
 }
}

//Backup mailen
if($backup && (($counter % $backup) == 0)) mail($mail, $_SERVER['SERVER_NAME'] . ' Counter: Backup', "Hi,\n\ndeine Website http://" . $_SERVER['SERVER_NAME'] . "/ hat so eben $counter Besucher erreicht!\nSollte es zum Crash kommen, kannst Du deinen Counter mit dem oben genannten Wert wieder starten.\n\nSchönen Tag noch,\nDein Counter", 'From: counter@' . $_SERVER['SERVER_NAME'] . "\n" . 'Reply-To: ' . $mail . "\n" . 'X-Mailer: PHP/' . phpversion() . "\n" . 'Content-Type: text/plain; charset=ISO-8859-1'); #\r\n ???

//Ausgabe
if($img) foreach(str_split($counter) as $value) echo('<img src="counter/' . $value . '.png" alt="' . $value . '" />');
else echo($counter);

//Speichern
if(!isset($save) || $save)
{
 $temp = fopen($counterdat, 'w');
 flock($temp, LOCK_EX);
 fwrite($temp, $counter);
 flock($temp, LOCK_UN);
 fclose($temp);
}
?>