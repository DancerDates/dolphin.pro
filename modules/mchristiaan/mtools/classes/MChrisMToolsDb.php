<?
/***************************************************************************
*
* IMPORTANT: This is a commercial product made by MChristiaan and cannot be modified for other than personal usage. 
* This product cannot be redistributed for free or a fee without written permission from MChristiaan. 
* This notice may not be removed from the source code.
*
***************************************************************************/

require_once( BX_DIRECTORY_PATH_INC . 'header.inc.php' );
bx_import('BxDolModuleDb');

class MChrisMToolsDb extends BxDolModuleDb {
	var $_oConfig;
	
	function MChrisMToolsDb(&$oConfig) {
		parent::__construct();
        $this->_sPrefix = $oConfig->getDbPrefix();
    }
	
	function str_replace_assoc($array,$string){
		$from_array = array();
		$to_array = array();
	   
		foreach ($array as $k => $v){
			$from_array[] = $k;
			$to_array[] = $v;
		}
	   
		return str_replace($from_array,$to_array,$string);
	}
	
	function getString ($iTId,$iTLId) {
		global $site;
        $s = $this->getOne("SELECT `String` FROM `" . $this->_sPrefix . "keys`, `". $this->_sPrefix . "strings` WHERE `ID` = '$iTId' AND `ID` = `IDKey` AND `IDLanguage` = '$iTLId'");
		return $s;
    }

	function updateString ($Text,$iTId,$iTLId) {
		$Text = mysqli_real_escape_string($Text);
		$this->query("UPDATE `" . $this->_sPrefix . "strings` SET `String`='$Text' where `IDKey`='$iTId' and `IDLanguage`='$iTLId'");
		return 1;
	}
	
	function getStringTPE ($iTId,$iTLId) {
		global $site;
        $s = $this->getOne("SELECT `String` FROM `" . $this->_sPrefix . "keys`, `". $this->_sPrefix . "strings` WHERE `ID` = '$iTId' AND `ID` = `IDKey` AND `IDLanguage` = '$iTLId'");
		$s = str_replace( '<site>', $site['title'], $s);
		return $s;
    }    
	
	function updateStringTPE ($Text,$iTId,$iTLId) {
        global $site;
		$Text = str_replace( $site['title'], '<site>', $Text);
		$Text = mysqli_real_escape_string($Text);
		$this->query("UPDATE `" . $this->_sPrefix . "strings` SET `String`='$Text' where `IDKey`='$iTId' and `IDLanguage`='$iTLId'");
		return 1;
    }
	
	function getStringFAQ ($iTId,$iTLId) {
		global $site;
		$replace = array(
			'<div class="faq_cont">' => '<faq_'. _t('_mchristiaan_faq_acontent') .'>',
			'<div class="faq_header">' => '<faq_'. _t('_mchristiaan_faq_aheader') .'>',
			'<div class="faq_snippet">' => '<faq_'. _t('_mchristiaan_faq_asnippet') .'>',
			'</div>' => '</faq>'
		);
        $s = $this->getOne("SELECT `String` FROM `" . $this->_sPrefix . "keys`, `". $this->_sPrefix . "strings` WHERE `ID` = '$iTId' AND `ID` = `IDKey` AND `IDLanguage` = '$iTLId'");
		$s = $this->str_replace_assoc($replace,$s);
		return $s;
    }
	
	function updateStringFAQ ($Text,$iTId,$iTLId) {
        global $site;
		$replace = array(
			'<faq_'. _t('_mchristiaan_faq_acontent') .'>' => '<div class="faq_cont">',
			'<faq_'. _t('_mchristiaan_faq_aheader') .'>' => '<div class="faq_header">',
			'<faq_'. _t('_mchristiaan_faq_asnippet') .'>' => '<div class="faq_snippet">',
			'</faq>' => '</div>'
		);
		$Text = $this->str_replace_assoc($replace,$Text);
		$Text = mysqli_real_escape_string($Text);
		$this->query("UPDATE `" . $this->_sPrefix . "strings` SET `String`='$Text' where `IDKey`='$iTId' and `IDLanguage`='$iTLId'");
		return 1;
    }
	
	function getfirstLang() {
		return $this->getOne("SELECT `ID` FROM `" . $this->_sPrefix . "languages` LIMIT 1");
    }
	
	function getLangs() {
		return $this->getAll("SELECT * FROM `" . $this->_sPrefix . "languages`");
    }
	
	function getId($Key) {
		return $this->getOne("SELECT `ID` FROM `" . $this->_sPrefix . "keys` WHERE `Key` = '$Key'");
    }
	
}

?>
