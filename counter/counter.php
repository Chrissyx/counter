<?php

#####################################################################
#Script written by Chrissyx                                         #
#You may use and edit this script, if you don't remove this comment!#
#http://www.chrissyx.de(.vu)/                                       #
#####################################################################

//Caching
if(file_exists('counter/settings.php') && (filemtime('counter/settings.php') > filemtime('counter/settings.dat'))) include('counter/settings.php');
else
{
 //Config: Counter, Backup Mail, Addy, IP Sperre, Bildausgabe
 list($counterdat, $backup, $mail, $ipdat, $img) = @array_map('trim', file('counter/settings.dat')) or die('<b>ERROR:</b> Keine Einstellungen gefunden!');
 $temp = fopen('counter/settings.php', 'w');
 fwrite($temp, "<?php\n //Auto-generated config!\n \$counterdat = '$counterdat';\n \$backup = " . (($backup) ? $backup : "''") . ";\n \$mail = '$mail';\n \$ipdat = '$ipdat';\n \$ips = file(\$ipdat);\n \$img = $img;\n?>");
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
  $ips[] = $_SERVER['REMOTE_ADDR'] . "\n";
  $temp = fopen($ipdat, 'w');
  fwrite($temp, implode('', $ips));
  fclose($temp);
  $save = true;
 }
}

//Backup mailen
if($backup && (($counter % $backup) == 0)) mail($mail, 'Counter', "Hi,\n\ndeine Website " . $_SERVER['SERVER_NAME'] . " hat so eben $counter Besucher erreicht!\nSollte es zum Crash kommen, kannst Du deinen Counter mit dem oben genannten Wert wieder starten.\n\nSchönen Tag noch,\nDein Counter", 'From: counter@' . $_SERVER['SERVER_NAME'] . "\n" . 'Reply-To: ' . $mail . "\n" . 'X-Mailer: PHP/' . phpversion() . "\n" . 'Content-Type: text/plain; charset=ISO-8859-1' . "\n"); #\r\n ???

//Ausgabe
if($img) foreach(str_split($counter) as $value) echo('<img src="counter/' . $value . '.png" alt="' . $value . '" />');
else echo($counter);

//Speichern
if($save || !isset($save))
{
 $temp = fopen($counterdat, 'w');
 flock($temp, LOCK_EX);
 fwrite($temp, $counter);
 flock($temp, LOCK_UN);
 fclose($temp);
}
?>