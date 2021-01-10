<?php
/**
 * Loads the core and stores it in the session for further accessing.
 * Dubbed &quot;LeetCore&quot;^^
 *
 * @author Chrissyx <chris@chrissyx.com>
 * @copyright (c) 2009, 2010 by Chrissyx
 * @license http://creativecommons.org/licenses/by-nc-sa/3.0/ Creative Commons 3.0 by-nc-sa
 * @package CHS_Core
 * @version 1.0
 */
/**
 * Interface template for every implementing module.
 *
 * @package CHS_Core
 */
interface CHSModule
{
 /**
  * Executes this module.
  *
  * @see CHSCore::execute()
  */
 public function execute();
}

/**
 * The module manager.
 *
 * @package CHS_Core
 */
class CHSCore
{
 /**
  * Detected modules are stored here.
  *
  * @see hasModule()
  * @var array Available modules
  */
 private $availableModules = array();

 /**
  * (Absolute) path to directory of data files.
  * 
  * @var string Path to data
  */
 private $dataPath;

 /**
  * (Absolute) path to directory of language files.
  * 
  * @var string Path to languages
  */
 private $langPath;

 /**
  * Loaded modules are stored here after first execution.
  *
  * @see CHSCore::getModule()
  * @var array Created modules
  */
 private $loadedModules = array();

 /**
  * (Absolute) path to directory of modules.
  * 
  * @var string Path to modules
  */
 private $modulesPath;

 /**
  * Detects paths and available modules to execute.
  */
 function __construct()
 {
  $this->dataPath = realpath(($rootDir = dirname(__FILE__) . '/') . 'data/') . DIRECTORY_SEPARATOR;
  $this->langPath = realpath($rootDir . 'languages/') . DIRECTORY_SEPARATOR;
  $this->modulesPath = realpath($rootDir . 'modules/') . DIRECTORY_SEPARATOR;
  foreach(glob($this->modulesPath . '*.php') as $value)
   $this->availableModules[] = basename($value, '.php');
 }

 /**
  * Backs up loaded modules in serialized form.
  */
 function __destruct()
 {
  foreach(array_keys($this->loadedModules) as $value)
   if(is_object($this->loadedModules[$value]['class']))
    $this->loadedModules[$value]['class'] = serialize($this->loadedModules[$value]['class']);
 }

 /**
  * Loads and executes the stated module. Triggers an error if module could not be found or does not implement the needed interface.
  *
  * @param string $module The module to load and execute
  * @see CHSModule::execute()
  * @see getModule()
  * @return mixed Boolean ONLY if an error occured and state of its report. The loaded class otherweise.
  */
 public function execute($module)
 {
  $this->getModule($module)->execute();
 }

 /**
  * Returns absolute path to directory of data files.
  *
  * @return string Path to data folder
  */
 public function getDataPath()
 {
  return $this->dataPath;
 }

 /**
  * Returns absolute path to directory of language files.
  *
  * @return string Path to language folder
  */
 public function getLangPath()
 {
  return $this->langPath;
 }

 /**
  * Loads the stated module. Triggers an error if module could not be found or does not implement the needed interface.
  * 
  * @param string $module The module to load
  * @return mixed The loaded class or false on failure.
  */
 public function getModule($module)
 {
  //Module is not loaded at all
  if(!isset($this->loadedModules[$module]))
  {
   if(!$this->hasModule($module))
    return !trigger_error('CHSCore::getModule(' . str_replace('&', '&amp;', $module) . '): module does not exist', E_USER_WARNING);
   $this->loadedModules[$module]['eval'] = trim(php_strip_whitespace($this->modulesPath . $module . '.php'), '<?ph>'); //Get *pure* code
   eval($this->loadedModules[$module]['eval']);
   if(!class_exists($module, false))
    return !trigger_error('CHSCore::getModule(' . str_replace('&', '&amp;', $module) . '): module is not a valid class', E_USER_WARNING);
   $class = new $module;
   if(!$class instanceof CHSModule)
    return !trigger_error('CHSModule::execute(' . str_replace('&', '&amp;', $module) . '): module does not support needed interface', E_USER_WARNING);
   $this->loadedModules[$module]['class'] = $class;
  }
  //Module was loaded before and serialized, restore it back by letting the script engine know the original class structure
  elseif(is_string($this->loadedModules[$module]['class']))
  {
   eval($this->loadedModules[$module]['eval']); //Prevents any needed __autoload() action :)
   $this->loadedModules[$module]['class'] = unserialize($this->loadedModules[$module]['class']);
  }
  //Finally return prepared module
  return $this->loadedModules[$module]['class'];
 }

 /**
  * Returns absolute path to directory of modules.
  *
  * @return string Path to modules folder
  */
 public function getModulesPath()
 {
  return $this->modulesPath;
 }

 /**
  * Checks availability of a module.
  *
  * @param string $module Name of module
  * @return bool Module is available for loading and execution
  */
 public function hasModule($module)
 {
  return in_array($module, $this->availableModules);
 }
}

/**
 * Gateway to access the core for modules and their execution of modules.
 *
 * @package CHS_Core
 * @see CHSCore
 */
class Loader
{
 /**
  * Executes the stated module.
  *
  * @param string $module Name of module
  * @see CHSCore::execute()
  */
 public static function execute($module)
 {
  $_SESSION['CHSCore']->execute((string) $module);
 }

 /**
  * Returns absolute path to directory of data files.
  *
  * @return string Path to data folder
  * @see CHSCore::getDataPath()
  */
 public static function getDataPath()
 {
  return $_SESSION['CHSCore']->getDataPath();
 }

 /**
  * Returns absolute path to directory of language files.
  *
  * @return string Path to language folder
  * @see CHSCore::getLangPath()
  */
 public static function getLangPath()
 {
  return $_SESSION['CHSCore']->getLangPath();
 }

 /**
  * Returns the stated module.
  *
  * @param string $module Name of module
  * @return Loaded module class
  * @see CHSCore::getModule()
  */
 public static function getModule($module)
 {
  return $_SESSION['CHSCore']->getModule((string) $module);
 }

 /**
  * Returns absolute path to directory of modules.
  *
  * @return string Path to modules folder
  * @see CHSCore::getModulesPath()
  */
 public static function getModulesPath()
 {
  return $_SESSION['CHSCore']->getModulesPath();
 }
}

//Start session and core if needed
if(!isset($_SESSION))
 session_start();
if(!isset($_SESSION['CHSCore']))
 $_SESSION['CHSCore'] = new CHSCore;
?>