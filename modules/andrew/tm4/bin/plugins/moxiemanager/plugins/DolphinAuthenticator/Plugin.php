<?php

$aPathInfo = pathinfo(__FILE__);
require_once ($aPathInfo['dirname'] . '/../../../../../../inc/header.inc.php');
require_once (BX_DIRECTORY_PATH_INC . 'profiles.inc.php');

/**
 * This class handles MoxieManager DolphinAuthenticator authentication.
 */
class MOXMAN_DolphinAuthenticator_Plugin implements MOXMAN_Auth_IAuthenticator 
{
	public function authenticate(MOXMAN_Auth_User $user) 
    {
		$config = MOXMAN::getConfig();

        if (!isLogged())
			return false;

        $s = getUsername();
        $sPath  = BX_DIRECTORY_PATH_ROOT . 'media/moxie/files/' . substr($s, 0, 1) . '/' . substr($s, 0, 2) . '/' . substr($s, 0, 3) . '/' . $s;
        $this->_mkdir_r($sPath);

        $config->put('filesystem.rootpath', $sPath);

		$config->replaceVariable("user", $s);
		$user->setName($s);

		return true;
	}

    protected function _mkdir_r($dirName, $rights = 0777)
    {
        $dirs = explode('/', $dirName);
        $dir = '';
        foreach ($dirs as $part) {
            $dir .= $part . '/';
            if (!is_dir($dir) && strlen($dir) > 0 && !file_exists($dir))
                if (!mkdir($dir, $rights))
                    return false;
        }
        return true;
    }

}

MOXMAN::getAuthManager()->add("DolphinAuthenticator", new MOXMAN_DolphinAuthenticator_Plugin());

