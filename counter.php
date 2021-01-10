<?php

############################################################
#Script written by Chrissyx				   #
#You may use this script, if you don't remove this comment!#
#http://www.chrissyx.de(.vu)/				   #
############################################################

if (file_exists("counter/counter.var"))
{

 if (file_exists("counter/backup.var"))
 {
  $exist = true;
 } else
 {
  $exist = false;
 }


 if ($exist == true)
 {
  $backupfile = fopen ("counter/backup.var", "r");
  $temp = fgets($backupfile);
  fclose($backupfile);
  $array = explode("_", $temp);
  $hits = $array[0];
  $hitsfull = $array[1];
  $backup = $array[2];
 }


 $counterfile = fopen ("counter/counter.var", "r");
 $counter = fgets($counterfile);
 fclose($counterfile);

 if ($backup >= $counter)
 {
  $counter = $backup;
 }
 $counter++;
 if ($exist == true)
 {
  $hits--;
 }
 echo ($counter);

 $counterfile = fopen ("counter/counter.var", "w");
 fwrite($counterfile, $counter);
 fclose($counterfile);


 if ($exist == true)
 {
  if ($hits <= 0)
  {
   $hits = $hitsfull;
   $backup = $counter;
  }
  $hits .= _;
  $hits .= $hitsfull;
  $hits .= _;
  $hits .= $backup;
  $backupfile = fopen ("counter/backup.var", "w");
  fwrite($backupfile, $hits);
  fclose($backupfile);
 }

} else
{
 echo ("<b>ERROR: Keine \"counter.var\" gefunden! Führe das Installationsscript aus!</b>");
}

?>