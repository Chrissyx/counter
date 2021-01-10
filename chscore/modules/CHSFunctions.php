<?php
/**
 * General functions for each module with initial instructions.
 *
 * @author Chrissyx <chris@chrissyx.com>
 * @copyright (c) 2009, 2010 by Chrissyx
 * @license http://creativecommons.org/licenses/by-nc-sa/3.0/ Creative Commons 3.0 by-nc-sa
 * @package CHS_Core
 * @version 1.0
 */
class CHSFunctions implements CHSModule
{
 /**
  * Some general initial instructions.
  *
  * @see CHSModule::execute()
  */
 public function execute()
 {
  //Execution time of module
  $_SESSION['microtime'] = microtime(true);
  //Activate echo short tags
  if(ini_get('short_open_tag') == '0')
   ini_set('short_open_tag', '1');
 }

 /**
  * Generates an XHTML head for each site.
  *
  * @param string $title Title of document
  * @param string $keywords Metatag for keywords
  * @param string $description Metatag for description
  * @param string $charset Used charset / encoding
  * @param string $lang Language of document
  * @param string $appendix Optional stuff to insert into body
  * @param string $headTags More optional tags inside the head
  * @param string $bodyTag Additional optional statements for body-tag, <b>start with space!</b>
  * @param string $htmlTag Additional optional statements for html-tag, <b>start with space!</b>
  * @param string $style Name of used CSS file
  * @see printTail()
  */
 public static function printHead($title, $keywords, $description, $charset='ISO-8859-1', $lang='de', $appendix=null, $headTags=null, $bodyTag=null, $htmlTag=null, $style='style.css')
 {
  echo('<?xml version="1.0" encoding="' . $charset . '" standalone="no" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="' . $lang . '" xml:lang="' . $lang . '"' . $htmlTag . '>
 <head>
  <title>' . $title . '</title>
  <meta name="author" content="Chrissyx" />
  <meta name="copyright" content="&copy; 2001 - 2010 by Chrissyx" />
  <meta name="keywords" content="' . $keywords . '" />
  <meta name="description" content="' . $description . '" />
  <meta name="robots" content="all" />
  <meta name="revisit-after" content="2 days" />
  <meta name="generator" content="Notepad 4.10.1998" />
  <meta http-equiv="Content-Language" content="' . $lang . '" />
  <meta http-equiv="Content-Type" content="application/xhtml+xml; charset=' . $charset . '" />
  <meta http-equiv="Content-Style-Type" content="text/css" />
  <meta http-equiv="Content-Script-Type" content="text/javascript" />
  <meta http-equiv="X-UA-Compatible" content="IE=8" />
  <link rel="stylesheet" media="all" href="../../styles/' . $style . '" />
' . ($headTags ? '  ' . $headTags . "\n" : '') . ' </head>
 <body' . $bodyTag . '>
' . $appendix);
 }

 /**
  * Generates ending XHTML tags of a document and displays localized execution time with memory usage.
  *
  * @param string $module Name of module to get translation from there
  * @param string $section Name of section in module's language file
  * @param string $code Language code
  * @see printHead()
  */
 public static function printTail($module='CHSCore', $section=null, $code=null)
 {
  echo('  <div class="stats">' . self::getFont(1) . sprintf(Loader::getModule('CHSLanguage')->getString('techstats', $section, $module, $code), number_format(microtime(true)-$_SESSION['microtime'], 4, Loader::getModule('CHSLanguage')->getString('dec_point', $section, $module, $code), Loader::getModule('CHSLanguage')->getString('thousands_sep', $section, $module, $code)), number_format(memory_get_usage()/1024, 2, Loader::getModule('CHSLanguage')->getString('dec_point', $section, $module, $code), Loader::getModule('CHSLanguage')->getString('thousands_sep', $section, $module, $code))) . '</span></div>
 </body>
</html>');
 }

 /**
  * Returns relative &lt;font size=&quot;xx&quot;&gt; as absolute CSS syle tag. <b>Do not forget: &lt;/span&gt;!</b>
  *
  * @param int $value HTML font size ranging from 1 to 7 or custom size attribute
  * @return string span-element with desired font size
  */
 public static function getFont($value)
 {
  switch($value)
  {
   case 7:
   return '<span style="font-size:300%;">';
   break;

   case 6:
   return '<span style="font-size:xx-large;">';
   break;

   case 5:
   return '<span style="font-size:x-large;">';
   break;

   case 4:
   return '<span style="font-size:large;">';
   break;

   case 3:
   return '<span style="font-size:medium;">';
   break;

   case 2:
   return '<span style="font-size:small;">';
   break;

   case 1.5:
   return '<span style="font-size:x-small;">';
   break;

   case 1:
   return '<span style="font-size:xx-small;">';
   break;

   default:
   return '<span style="font-size:' . $value . ';">';
   break;
  }
 }

 /**
  * Returns a formatted paragraph with stated text and colors.
  *
  * @param string $text Text to be displayed
  * @param string $color Basic color of message box
  */
 public static function getMsgBox($text, $color='#000000')
 {
  switch($color)
  {
   case 'green':
   $bColor = 'F0FFF0';
   $color = '#00FF00';
   break;

   case 'red':
   $bColor = 'FFD1D1';
   $color = '#FF0000';
   break;

   case 'yellow':
   $bColor = 'FFFFDD';
   $color = '#FFD700';
   break;

   default:
   $bColor = 'FFFFFF';
   break;
  }
  return '  <p style="background-color:#' . $bColor . '; border:2px solid ' . $color . '; color:' . $color . '; padding:3px;">' . $text . '</p>';
 }

 /**
  * Returns contents of a PHP-protected data file.
  * 
  * @param string $filename Name of data file
  * @param string $callback Callback to be executed on each file entry
  * @return array|bool File contents or false in case of stated file not found
  */
 public static function getPHPDataFile($filename, $callback='trim')
 {
  return file_exists($filename . '.php') ? array_map($callback, array_slice(file($filename . '.php'), 1)) : false;
 }

 /**
  * Writes contents of an array to a PHP-protected data file.
  * 
  * @param string $filename Name of data file
  * @param mixed $contents Single value or array with contents to be written
  * @return Number of bytes that were written to the file or false on failure
  */
 public static function setPHPDataFile($filename, $contents=array())
 {
  if(!is_array($contents))
   $contents = array($contents);
  array_unshift($contents, '<?php exit(\'<b>ERROR:</b> Access denied!\'); ?>');
  return file_put_contents($filename . '.php', implode("\n", $contents), LOCK_EX);
 }

 /**
  * Returns the hash value for stated string.
  *
  * @param string $data string to hash
  * @return string Hash value
  */
 public static function getHash($data)
 {
  return function_exists('hash') ? hash('sha512', $data) : md5($data);
 }
}
?>