<?php
/**
 * Counter module to count incl. output, IP listing and backup mailing.
 *
 * @author Chrissyx <chris@chrissyx.com>
 * @copyright (c) 2004 - 2010 by Chrissyx
 * @license http://creativecommons.org/licenses/by-nc-sa/3.0/ Creative Commons 3.0 by-nc-sa
 * @package CHS_Counter
 * @version 3.1
 */
class CHSCounter implements CHSModule
{
 /**
  * Loaded configuration set is stored here.
  *
  * @var array Loaded configuration values
  */
 private $config = array();

 /**
  * Current counter value is accessed here.
  *
  * @var int Current counter value
  */
 private $curCounter = 0;

 /**
  * Detected path to images of numbers.
  *
  * @var string Path to images of numbers
  */
 private $path;

 /**
  * Write a new counter value if IP is not known or mode is set to save every hit.
  *
  * @var bool Write a new counter value
  */
 private $update = true;

 /**
  * Loads the configuration set.
  *
  * @see CHSConfig::getConfigSet()
  */
 function __construct()
 {
  if(($this->config = Loader::getModule('CHSConfig')->getConfigSet('CHSCounter')) === false)
   exit(Loader::getModule('CHSLanguage')->getString('error_no_settings', 'counter', 'CHSCounter'));
  $this->path = Loader::getImagesPath() . 'CHSCounter/';
 }

 /**
  * Checks for a known IP regarding unique hits. Saves new IP or adjusts counter.
  */
 private function processIP()
 {
  $this->update = false;
  if(in_array($_SERVER['REMOTE_ADDR'] . "\n", file($this->config['ip'])))
   $this->curCounter--;
  else
  {
   file_put_contents($this->config['ip'], $_SERVER['REMOTE_ADDR'] . "\n", LOCK_EX | FILE_APPEND);
   $this->update = true;
  }
 }

 /**
  * Performs the counting with all associated actions.
  *
  * @see CHSCore::execute()
  */
 public function execute()
 {
  $temp = fopen($this->config['counter'], 'r+');
  flock($temp, LOCK_SH);
  //Counting
  $this->curCounter = fgets($temp)+1;
  //Manage IP
  if(!empty($this->config['ip']))
   $this->processIP();
  //Backup
  if($this->config['backup'] > 0 && ($this->curCounter % $this->config['backup'] == 0))
   mail($this->config['mail'], str_replace('www.', '', $_SERVER['SERVER_NAME']) . ' Counter: ' . Loader::getModule('CHSLanguage')->getString('subject', 'counter', 'CHSCounter'), sprintf(Loader::getModule('CHSLanguage')->getString('mail_text', 'counter', 'CHSCounter'), $_SERVER['SERVER_NAME'], $this->curCounter), 'From: counter@' . str_replace('www.', '', $_SERVER['SERVER_NAME']) . $this->config['br'] . 'Reply-To: ' . $this->config['mail'] . $this->config['br'] . 'X-Mailer: PHP/' . phpversion() . $this->config['br'] . 'Content-Type: text/plain; charset=' . Loader::getModule('CHSLanguage')->getString('charset', 'common', 'CHSCounter'));
  //Output
  if($this->config['img'])
   foreach(str_split($this->curCounter) as $value)
    echo('<img src="' . $this->path . $value . '.png" alt="' . $value . '" />');
  else
   echo($this->curCounter);
  //Save counter
  if($this->update)
  {
   //Get proper lock, "shared" is not enough anymore
   while(!flock($temp, LOCK_EX))
    usleep(mt_rand(1, 100)*1000); //Wait between 1 to 100 millisecs to get lock
   rewind($temp); //Revert action from fgets()
   fwrite($temp, $this->curCounter);
  }
  //Release lock
  flock($temp, LOCK_UN);
  fclose($temp);
 }
}
?>