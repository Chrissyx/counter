<?php
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

 if ($backup == 0)
 {
  $backup = file("counter/backup.dat");
  $backup = $backup[1];
  $temp = fopen("counter/backup.dat", "w");
  fwrite($temp, $counter . "\n" . $backup);
  fclose($temp);
 }

 if ($_GET['bild'])
 {
  settype($counter, "string");
  for ($i=0; $i<strlen($counter); $i++) switch($counter[$i])
  {
   case 0: echo("<img src=\"counter/0.png\" alt=\"0\">"); break;
   case 1: echo("<img src=\"counter/1.png\" alt=\"1\">"); break;
   case 2: echo("<img src=\"counter/2.png\" alt=\"2\">"); break;
   case 3: echo("<img src=\"counter/3.png\" alt=\"3\">"); break;
   case 4: echo("<img src=\"counter/4.png\" alt=\"4\">"); break;
   case 5: echo("<img src=\"counter/5.png\" alt=\"5\">"); break;
   case 6: echo("<img src=\"counter/6.png\" alt=\"6\">"); break;
   case 7: echo("<img src=\"counter/7.png\" alt=\"7\">"); break;
   case 8: echo("<img src=\"counter/8.png\" alt=\"8\">"); break;
   case 9: echo("<img src=\"counter/9.png\" alt=\"9\">"); break;
  }
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