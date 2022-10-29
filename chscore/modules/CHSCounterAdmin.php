<?php
/**
 * Admin module for installing and managing the counter.
 *
 * @author Chrissyx <chris@chrissyx.com>
 * @copyright (c) 2004-2022 by Chrissyx
 * @license http://creativecommons.org/licenses/by-nc-sa/3.0/ Creative Commons 3.0 by-nc-sa
 * @package CHS_Counter
 * @version 3.2
 */
/**
 * Installs and manages the counter.
 *
 * @package CHS_Counter
 */
class CHSCounterAdmin implements CHSModule
{
    /**
     * Current performed action.
     *
     * @var string Action identifier
     */
    private $action;

    /**
     * Reference to the {@link CHSLanguage} module.
     *
     * @var CHSLanguage {@link CHSLanguage} module
     */
    private $chsLanguage;

    /**
     * Current file containing the hashed password.
     *
     * @var string Name of password file
     */
    private $curPassFile;

    /**
     * Hashed current user password to access counter ACP.
     *
     * @var string|bool Stored hashed user password to compare with.
     */
    private $curPassHash;

    /**
     * Hash of a possible new requested password, ready to replace current one.
     *
     * @var string|bool New hashed password
     */
    private $newPassHash;

    /**
     * Sets reference to language module.
     */
    function __construct()
    {
        $this->chsLanguage = Loader::getModule('CHSLanguage');
    }

    /**
     * Performs the desired action.
     *
     * @see CHSCore::execute()
     */
    public function execute()
    {
        switch($this->action)
        {
# Login #
            case 'login':
            if(isset($_POST['countpw']))
            {
                //Check for new pass first
                if($this->newPassHash && CHSFunctions::getHash($_POST['countpw']) == $this->newPassHash)
                {
                    $this->curPassHash = $this->newPassHash;
                    CHSFunctions::setPHPDataFile(substr($this->curPassFile, 0, -4), $this->newPassHash);
                }
                //Check normal pass now
                if(CHSFunctions::getHash($_POST['countpw']) == $this->curPassHash)
                {
                    $_SESSION['countpw'] = CHSFunctions::getHash($_POST['countpw']);
                    unset($_POST['countpw']);
                    @header('Location: ' . $_SERVER['PHP_SELF'] . '?module=CHSCounterAdmin&action=admin');
                    exit($this->chsLanguage->getString('logged_in', 'login') . ' <a href="' . $_SERVER['PHP_SELF'] . '?module=CHSCounterAdmin&amp;action=admin">' . $this->chsLanguage->getString('go_on', 'common') . '</a>');
                }
            }
            CHSFunctions::printHead('CHS &ndash; Counter: ' . $this->chsLanguage->getString('title', 'login'), 'Counter, CHS, ' . $this->chsLanguage->getString('title', 'login') . ', Chrissyx', $this->chsLanguage->getString('descr', 'login'), $this->chsLanguage->getString('charset', 'common'), $this->chsLanguage->getLangCode());
            if(isset($_POST['countpw']))
                echo(CHSFunctions::getMsgBox($this->chsLanguage->getString('wrong_pass', 'login'), 'red'));
?>

  <h3>CHS &ndash; Counter: <?=$this->chsLanguage->getString('title', 'login')?></h3>
  <form action="<?=$_SERVER['PHP_SELF']?>?module=CHSCounterAdmin" method="post">
  <?=$this->chsLanguage->getString('enter_pass', 'login')?> <input type="password" name="countpw" <?php if(isset($_POST['countpw'])) echo('style="border-color:#FF0000;" '); ?>/> <?=CHSFunctions::getFont(1)?>(<?=$this->chsLanguage->getString('forgotten', 'login')?> <a href="<?=$_SERVER['PHP_SELF']?>?module=CHSCounterAdmin&amp;action=newpass"><?=$this->chsLanguage->getString('request_new_pass', 'login')?></a>)</span><br />
  <input type="submit" value="<?=$this->chsLanguage->getString('title', 'login')?>" />
  <input type="hidden" name="action" value="login" />
  </form>

<?php
            CHSFunctions::printTail('CHSCounter', 'common');
            break;

# Administration #
            case 'admin':
            if(!isset($_SESSION['countpw']) || $_SESSION['countpw'] != $this->curPassHash)
                exit($this->chsLanguage->getString('error_not_allowed', 'admin'));
            CHSFunctions::printHead('CHS &ndash; Counter: ' . $this->chsLanguage->getString('title', 'admin'), 'Counter, CHS, ' . $this->chsLanguage->getString('title', 'admin') . ', Chrissyx', $this->chsLanguage->getString('descr', 'admin'), $this->chsLanguage->getString('charset', 'common'), $this->chsLanguage->getLangCode());
            $settings = Loader::getModule('CHSConfig')->getConfigSet('CHSCounter');
            if(isset($_POST['update']))
            {
                $msg = CHSFunctions::getMsgBox($this->chsLanguage->getString('fill_out_all', 'common'), 'red');
                if(empty($_POST['counterdat']))
                    $settings['counter'] .= '" style="border-color:#FF0000;';
                elseif(!empty($_POST['backup']) && $_POST['backup'] < 2)
                    $settings['backup'] .= '" style="border-color:#FF0000;';
                elseif(!empty($_POST['backup']) && (empty($_POST['email']) || !CHSFunctions::isValidMail($_POST['email'])))
                    $settings['mail'] .= '" style="border-color:#FF0000;';
                elseif($_POST['countpw'] == $_POST['countpw2'])
                {
                    unset($msg);
                    //New language
                    if($_POST['lang'] != $settings['lang'] && !$this->chsLanguage->setLangCode($_POST['lang']))
                        echo(CHSFunctions::getMsgBox($this->chsLanguage->getString('cant_set_lang', 'admin'), 'yellow'));
                    //New password
                    if(!empty($_POST['countpw']))
                    {
                        $_SESSION['countpw'] = CHSFunctions::getHash($_POST['countpw']);
                        unlink($this->curPassFile); //Delete old and create new pass file
                        CHSFunctions::setPHPDataFile($this->curPassFile = Loader::getDataPath() . md5(time()) . 'CHSCounter.dat', $_SESSION['countpw']);
                    }
                    //New counter value
                    if(is_numeric($_POST['counter']))
                    {
                        $temp = fopen($settings['counter'], 'w');
                        while(!flock($temp, LOCK_EX | LOCK_NB))
                            usleep(mt_rand(1, 100) * 1000); //Wait between 1 to 100 millisecs to get lock
                        fwrite($temp, $_POST['counter']);
                        flock($temp, LOCK_UN);
                        fclose($temp);
                    }
                    //New counter file
                    if($_POST['counterdat'] != $settings['counter'])
                    {
                        rename($settings['counter'], $_POST['counterdat']) or $_POST['counterdat'] = $settings['counter'];
                        if($_POST['counterdat'] == $settings['counter'])
                        {
                            echo(CHSFunctions::getMsgBox($this->chsLanguage->getString('cant_rename_file', 'admin'), 'yellow'));
                            $settings['counter'] .= '" style="border-color:#FFFF00;';
                        }
                    }
                    //Manage IPs
                    if($_POST['ipdat'] != $settings['ip'])
                    {
                        if(empty($_POST['ipdat']) && file_exists($settings['ip']))
                            unlink($settings['ip']);
                        else
                            file_exists($settings['ip']) && !is_dir($settings['ip']) ? rename($settings['ip'], $_POST['ipdat']) : file_put_contents($_POST['ipdat'], $_SERVER['REMOTE_ADDR'] . "\n");
                    }
                    //Save settings
                    Loader::getModule('CHSConfig')->setConfigSet('CHSCounter', array('lang' => $_POST['lang'],
                        'counter' => $_POST['counterdat'],
                        'backup' => $_POST['backup'],
                        'mail' => $_POST['email'],
                        'br' => (isset($_POST['compa']) ? "\n" : "\r\n"),
                        'ip' => $_POST['ipdat'],
                        'img' => $_POST['img'] == 'true'));
                    $settings = Loader::getModule('CHSConfig')->getConfigSet('CHSCounter'); //Reload settings
                    echo(CHSFunctions::getMsgBox($this->chsLanguage->getString('new_settings_saved', 'admin'), 'green'));
                }
                if(isset($msg))
                    echo($msg);
            }
?>

  <h3>CHS &ndash; Counter: <?=$this->chsLanguage->getString('title', 'admin')?></h3>
  <p> <?=$this->chsLanguage->getString('intro', 'admin')?></p>
  <form name="form" action="<?=$_SERVER['PHP_SELF']?>?module=CHSCounterAdmin" method="post">
  <table>
   <tr><td><?=$this->chsLanguage->getString('language', 'admin')?></td><td><select name="lang" size="1" style="width:265px;"><option value=""><?=$this->chsLanguage->getString('automatically', 'admin')?></option><?php
foreach($this->chsLanguage->getLangCodes() as $curCode)
 echo('<option value="' . $curCode . '"' . ($settings['lang'] == $curCode ? ' selected="selected"' : '') . '>' . $this->chsLanguage->getString($curCode, 'common') . '</option>');
?></select></td></tr>
   <tr style="height:5px;"><td colspan="2"></td></tr>
   <tr><td colspan="2"><?=CHSFunctions::getFont(1) . $this->chsLanguage->getString('value_hint', 'admin')?></span></td></tr>
   <tr><td><?=$this->chsLanguage->getString('value', 'admin')?></td><td><input type="text" name="counter" size="40" onfocus="this.value='<?=@file_get_contents($settings['counter'])?>';" /></td></tr>
   <tr><td><?=$this->chsLanguage->getString('loc_counter', 'admin')?></td><td><input type="text" name="counterdat" value="<?=$settings['counter']?>" size="40" /></td></tr>
   <tr style="height:5px;"><td colspan="2"></td></tr>
   <tr><td><?=sprintf($this->chsLanguage->getString('mail_hits', 'admin'), '</td><td><input type="text" name="backup" value="' . $settings['backup'] . '" size="35" />')?></td></tr>
   <tr><td><?=$this->chsLanguage->getString('mail_addr', 'admin')?></td><td><input type="text" name="email" value="<?=$settings['mail']?>" size="40" /></td></tr>
   <tr><td><?=$this->chsLanguage->getString('mail_comp', 'admin')?></td><td><input type="checkbox" name="compa" value="true"<?php if($settings['br'] == "\n") echo(' checked="checked"'); ?> /></td></tr>
   <tr style="height:5px;"><td colspan="2"></td></tr>
   <tr><td colspan="2"><?=CHSFunctions::getFont(1) . $this->chsLanguage->getString('password_hint', 'admin')?></span></td></tr>
   <tr><td><?=$this->chsLanguage->getString('password', 'admin')?></td><td><input type="password" name="countpw"<?php if(isset($_POST['update']) && $_POST['countpw'] != $_POST['countpw2']) echo(' style="border-color:#FF0000;"'); ?> size="40" /></td></tr>
   <tr><td><?=$this->chsLanguage->getString('retype_pass', 'admin')?></td><td><input type="password" name="countpw2"<?php if(isset($_POST['update']) && $_POST['countpw'] != $_POST['countpw2']) echo(' style="border-color:#FF0000;"'); ?> size="40" /></td></tr>
   <tr style="height:5px;"><td colspan="2"></td></tr>
   <tr><td><?=$this->chsLanguage->getString('loc_ip_blocker', 'admin')?></td><td><input type="text" name="ipdat" value="<?=$settings['ip']?>" size="40" /></td></tr>
   <tr><td rowspan="2"><?=$this->chsLanguage->getString('output_counter', 'admin')?></td><td><input type="radio" name="img" value="true"<?php if($settings['img']) echo(' checked="checked"'); ?> /><?=sprintf($this->chsLanguage->getString('as_pic', 'admin'), Loader::getImagesPath() . 'CHSCounter/')?></td></tr>
   <tr><td><input type="radio" name="img" value="false"<?php if(!$settings['img']) echo(' checked="checked"'); ?> /><?=$this->chsLanguage->getString('as_text', 'admin')?></td></tr>
  </table>
  <input type="submit" value="<?=$this->chsLanguage->getString('update_now', 'admin')?>" /> <input type="reset" /> <input type="button" value="<?=$this->chsLanguage->getString('title', 'logout')?>" onclick="document.location='<?=$_SERVER['PHP_SELF']?>?module=CHSCounterAdmin&action=logout';" />
  <input type="hidden" name="action" value="admin" />
  <input type="hidden" name="update" value="true" />
  </form>

<?php
            CHSFunctions::printTail('CHSCounter', 'common');
            break;

# Logout #
            case 'logout':
            session_unset(); //Kill off whole session to re-init the Core to avoid caching issues
            @header('Location: ' . $_SERVER['PHP_SELF'] . '?module=CHSCounterAdmin');
            exit($this->chsLanguage->getString('logged_out', 'logout') . ' <a href="' . $_SERVER['PHP_SELF'] . '">' . $this->chsLanguage->getString('go_on', 'common') . '</a>');
            break;

# New password #
            case 'newpass':
            CHSFunctions::printHead('CHS &ndash; Counter: ' . $this->chsLanguage->getString('title', 'newpass'), 'Counter, CHS, ' . $this->chsLanguage->getString('title', 'newpass') . ', Chrissyx', $this->chsLanguage->getString('descr', 'newpass'), $this->chsLanguage->getString('charset', 'common'), $this->chsLanguage->getLangCode());
            $settings = Loader::getModule('CHSConfig')->getConfigSet('CHSCounter');
            if(empty($settings['mail']))
                echo(CHSFunctions::getMsgBox($this->chsLanguage->getString('no_addr_given', 'newpass'), 'yellow'));
            else
            {
                for($i=0,$newPass=''; $i<10; $i++)
                    $newPass .= chr(mt_rand(33, 126));
                CHSFunctions::setPHPDataFile(substr($this->curPassFile, 0, -4), array($this->curPassHash, CHSFunctions::getHash($newPass)));
                echo(mail($settings['mail'], str_replace('www.', '', $_SERVER['SERVER_NAME']) . ' Counter: ' . $this->chsLanguage->getString('title', 'newpass'), sprintf($this->chsLanguage->getString('mail_text', 'newpass'), $_SERVER['REMOTE_ADDR'], $_SERVER['SERVER_NAME'], $newPass), 'From: counter@' . str_replace('www.', '', $_SERVER['SERVER_NAME']) . $settings['br'] . 'Reply-To: ' . $settings['mail'] . $settings['br'] . 'X-Mailer: PHP/' . phpversion() . $settings['br'] . 'Content-Type: text/plain; charset=' . $this->chsLanguage->getString('charset', 'common')) ? CHSFunctions::getMsgBox($this->chsLanguage->getString('mail_sent', 'newpass'), 'green') : CHSFunctions::getMsgBox($this->chsLanguage->getString('mail_not_sent', 'newpass'), 'red'));
            }
            CHSFunctions::printTail('CHSCounter', 'common');
            break;

# Installation #
            case 'install':
            default:
            CHSFunctions::printHead('CHS &ndash; Counter: ' . $this->chsLanguage->getString('title', 'install'), 'Counter, CHS, ' . $this->chsLanguage->getString('title', 'install') . ', Chrissyx', $this->chsLanguage->getString('descr', 'install'), $this->chsLanguage->getString('charset', 'common'), $this->chsLanguage->getLangCode());
            if($this->action == 'install')
            {
                $msg = CHSFunctions::getMsgBox($this->chsLanguage->getString('fill_out_all', 'common'), 'red');
                if(!is_numeric($_POST['counter']))
                    $_POST['counter'] .= CHSFunctions::$redBorder;
                elseif(empty($_POST['counterdat']))
                    $_POST['counterdat'] .= CHSFunctions::$redBorder;
                elseif(!empty($_POST['backup']) && $_POST['backup'] < '2')
                    $_POST['backup'] .= CHSFunctions::$redBorder;
                elseif(!empty($_POST['backup']) && (empty($_POST['email']) || !CHSFunctions::isValidMail($_POST['email'])))
                    $_POST['email'] .= CHSFunctions::$redBorder;
                elseif(empty($_POST['countpw']) || $_POST['countpw'] != $_POST['countpw2'])
                    $_POST['countpw'] = $_POST['countpw2'] = ' style="border-color:#FF0000;"';
                elseif(!empty($_POST['img']))
                {
                    Loader::getModule('CHSConfig')->setConfigSet('CHSCounter', array('lang' => '',
                        'counter' => $_POST['counterdat'],
                        'backup' => $_POST['backup'],
                        'mail' => $_POST['email'],
                        'br' => (isset($_POST['compa']) ? "\n" : "\r\n"),
                        'ip' => $_POST['ipdat'],
                        'img' => $_POST['img'] == 'true'));
                    file_put_contents($_POST['counterdat'], $_POST['counter']);
                    if(!empty($_POST['ipdat']))
                        file_put_contents($_POST['ipdat'], $_SERVER['REMOTE_ADDR'] . "\n");
                    CHSFunctions::setPHPDataFile(Loader::getDataPath() . md5(time()) . 'CHSCounter.dat', CHSFunctions::getHash($_POST['countpw']));
                    echo(CHSFunctions::getMsgBox($this->chsLanguage->getString('install_finished', 'install'), 'green'));
?>

  <p><?=$this->chsLanguage->getString('note1', 'install')?></p>
  <p><code>&lt;!-- CHS - Counter --&gt;&lt;?php Loader::execute('CHSCounter'); ?&gt;&lt;!-- /CHS - Counter --&gt;</code></p>
  <p><?=$this->chsLanguage->getString('note2', 'install')?></p>
  <p><code>&lt;?php include('chscore/CHSCore.php'); ?&gt;</code></p>
  <p><?=sprintf($this->chsLanguage->getString('note3', 'install'), '<a href="http://www.chrissyx-forum.de.vu/" target="_blank">http://www.chrissyx-forum.de.vu/</a>')?></p>
  <p><a href="http://<?=$_SERVER['SERVER_NAME']?>/"><?=$this->chsLanguage->getString('goto1', 'install')?></a> &ndash; <a href="<?=$_SERVER['PHP_SELF']?>?module=CHSCounterAdmin"><?=$this->chsLanguage->getString('goto2', 'install')?></a></p>

<?php
                    exit(CHSFunctions::printTail('CHSCounter', 'common'));
                }
            }
            if(isset($msg))
                echo $msg;
            if(phpversion() < '5.3')
                echo(CHSFunctions::getMsgBox(sprintf($this->chsLanguage->getString('warning_php_version', 'install'), PHP_VERSION), 'red'));
?>

  <script type="text/javascript">
  function help(data)
  {
   document.getElementById('help').firstChild.nodeValue = data;
  };
  </script>

  <h3>CHS &ndash; Counter: <?=$this->chsLanguage->getString('title', 'install')?></h3>
  <p><?=$this->chsLanguage->getString('intro', 'install')?></p>
  <form action="<?=$_SERVER['PHP_SELF']?>?module=CHSCounterAdmin" method="post">
  <table onmouseout="help('<?=$this->chsLanguage->getString('help', 'install')?>');">
   <tr><td colspan="2"></td><td rowspan="14" style="background-color:yellow; width:200px;"><div class="center" id="help"><?=$this->chsLanguage->getString('help', 'install')?></div></td></tr>
   <tr onmouseover="help('<?=$this->chsLanguage->getString('help1', 'install')?>');"><td><?=$this->chsLanguage->getString('startvalue', 'install')?></td><td><input type="text" name="counter" value="<?=isset($_POST['counter']) ? $_POST['counter'] : '0'?>" size="40" /></td></tr>
   <tr onmouseover="help('<?=$this->chsLanguage->getString('help2', 'install')?>');"><td><?=$this->chsLanguage->getString('loc_counter', 'admin')?></td><td><input type="text" name="counterdat" value="<?=isset($_POST['counterdat']) ? $_POST['counterdat'] : Loader::getDataPath() . 'counter.dat'?>" size="40" /></td></tr>
   <tr style="height:5px;"><td colspan="2"></td></tr>
   <tr onmouseover="help('<?=$this->chsLanguage->getString('help3', 'install')?>');"><td><?=sprintf($this->chsLanguage->getString('mail_hits', 'admin'), '</td><td><input type="text" name="backup" value="' . (isset($_POST['backup']) ? $_POST['backup'] : '') . '" size="35" onfocus="this.value = this.value == \'\' ? \'500\' : this.value;" />')?></td></tr>
   <tr onmouseover="help('<?=$this->chsLanguage->getString('help4', 'install')?>');"><td><?=$this->chsLanguage->getString('mail_addr', 'admin')?></td><td><input type="text" name="email" value="<?=isset($_POST['email']) ? $_POST['email'] : ''?>" size="40" /></td></tr>
   <tr onmouseover="help('<?=$this->chsLanguage->getString('help5', 'install')?>');"><td><?=$this->chsLanguage->getString('mail_comp', 'admin')?></td><td><input type="checkbox" name="compa" value="true"<?=isset($_POST['compa']) ? ' checked="checked"' : ''?> /></td></tr>
   <tr style="height:5px;"><td colspan="2"></td></tr>
   <tr onmouseover="help('<?=$this->chsLanguage->getString('help6', 'install')?>');"><td><?=$this->chsLanguage->getString('password', 'admin')?></td><td><input type="password" name="countpw" size="40"<?=isset($_POST['countpw']) ? $_POST['countpw'] : ''?> /></td></tr>
   <tr onmouseover="help('<?=$this->chsLanguage->getString('help7', 'install')?>');"><td><?=$this->chsLanguage->getString('retype_pass', 'admin')?></td><td><input type="password" name="countpw2" size="40"<?=isset($_POST['countpw2']) ? $_POST['countpw2'] : ''?> /></td></tr>
   <tr style="height:5px;"><td colspan="2"></td></tr>
   <tr onmouseover="help('<?=$this->chsLanguage->getString('help8', 'install')?>');"><td><?=$this->chsLanguage->getString('loc_ip_blocker', 'admin')?></td><td><input type="text" name="ipdat" value="<?=isset($_POST['ipdat']) ? $_POST['ipdat'] : ''?>" size="40" onfocus="this.value = this.value == '' ? '<?=Loader::getDataPath()?>ip.dat' : this.value;" /></td></tr>
   <tr onmouseover="help('<?=$this->chsLanguage->getString('help9', 'install')?>');"><td rowspan="2"><?=$this->chsLanguage->getString('output_counter', 'admin')?></td><td><input type="radio" name="img" value="true"<?=isset($_POST['img']) && $_POST['img'] == 'true' ? ' checked="checked"' : ''?> /><?=sprintf($this->chsLanguage->getString('as_pic', 'admin'), Loader::getImagesPath() . 'CHSCounter/')?></td></tr>
   <tr onmouseover="help('<?=$this->chsLanguage->getString('help9', 'install')?>');"><td><input type="radio" name="img" value="false"<?=!isset($_POST['img']) || (isset($_POST['img']) && $_POST['img'] == 'false') ? ' checked="checked"' : ''?> /><?=$this->chsLanguage->getString('as_text', 'admin')?></td></tr>
  </table>
  <input type="submit" value="<?=$this->chsLanguage->getString('install_now', 'install')?>" onmouseover="help('<?=$this->chsLanguage->getString('help10', 'install')?>');" /> <input type="reset" onmouseover="help('<?=$this->chsLanguage->getString('help11', 'install')?>');" />
  <input type="hidden" name="action" value="install" />
  </form>

<?php
            CHSFunctions::printTail('CHSCounter', 'common');
            break;
        }
    }

    /**
     * Detects valid user action and prepares password hashes.
     *
     * @see CHSCore::onLoad()
     */
    public function onLoad()
    {
        if(isset($_GET['module']) && $_GET['module'] == get_class())
        {
            Loader::execute('CHSFunctions');
            $this->action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : '');
            //Update reference to lang module
            $this->chsLanguage = Loader::getModule('CHSLanguage');
            if(!$this->chsLanguage->setModule('CHSCounter')) //Set shortcut
                trigger_error(__METHOD__ . '(): Cannot set module name as shortcut', E_USER_WARNING);
            if(Loader::getModule('CHSConfig')->hasConfigSet('CHSCounter'))
            {
                if(($code = Loader::getModule('CHSConfig')->getConfigValue('CHSCounter', 'lang')) != '')
                    $this->chsLanguage->setLangCode($code);
                if(!in_array($this->action, array('login', 'logout', 'admin', 'newpass')))
                    $this->action = 'login';
                $this->curPassHash = @current($passHashes = CHSFunctions::getPHPDataFile(substr($this->curPassFile = current(glob(Loader::getDataPath() . '*CHSCounter.dat.php')), 0, -4))) or exit($this->chsLanguage->getString('error_no_user', 'admin'));
                $this->newPassHash = next($passHashes);
            }
            exit($this->execute());
        }
    }
}
?>