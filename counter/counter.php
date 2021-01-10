<?php

#####################################################################
#Script written by Chrissyx                                         #
#You may use and edit this script, if you don't remove this comment!#
#http://www.chrissyx.de(.vu)/                                       #
#####################################################################

 $counter = file("counter/counter.dat");
 $backup = trim($counter[1])-1;
 $counter = trim($counter[0])+1;

 if (file_exists("counter/ip.dat"))
 {
  $save = false;
  $temp = fopen("counter/ip.dat", "r");
  $iparray = fread($temp, filesize("counter/ip.dat"));
  fclose($temp);
  $iparray = explode("\n", $iparray);
  if (in_array($_SERVER['REMOTE_ADDR'], $iparray) && file_exists("counter/backup.dat")) $backup++;
  if (in_array($_SERVER['REMOTE_ADDR'], $iparray)) $counter--;
  else $save = true;
 }

 if ($backup <= 0 && file_exists("counter/backup.dat"))
 {
  $backup = file("counter/backup.dat");
  $backup = $backup[1];
  if ($backup > $counter)
  {
   $counter = file("counter/backup.dat");
   $counter = trim($counter[0]);
  }
  $temp = fopen("counter/backup.dat", "w");
  fwrite($temp, $counter . "\n" . $backup);
  fclose($temp);
 }

 if ($bild)
 {
  settype($counter, "string");
  $size = strlen($counter);
  for ($i=0; $i<$size; $i++) echo("<img src=\"counter/" . $counter[$i] . ".png\" alt=\"" . $counter[$i] . "\">");
  settype($counter, "integer");
 }
 else echo ($counter);

 if (file_exists("counter/ip.dat") && $save)
 {
  if ($iparray[count($iparray)-1] == "") array_pop($iparray);     
  $iparray[count($iparray)] = $_SERVER['REMOTE_ADDR'];
  $temp = fopen("counter/ip.dat", "w");
  fwrite($temp, implode("\n", $iparray));
  fclose($temp);
 }

 if ($save == true || !isset($save))
 {
  if (file_exists("counter/backup.dat")) $counter .= "\n" . $backup;
  $temp = fopen("counter/counter.dat", "w");
  fwrite($temp, $counter);
  fclose($temp);
 }
?>