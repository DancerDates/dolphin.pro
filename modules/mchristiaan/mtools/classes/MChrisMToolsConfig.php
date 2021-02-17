<?
/***************************************************************************
*
* IMPORTANT: This is a commercial product made by MChristiaan and cannot be modified for other than personal usage. 
* This product cannot be redistributed for free or a fee without written permission from MChristiaan. 
* This notice may not be removed from the source code.
*
***************************************************************************/

bx_import('BxDolConfig');

class MChrisMToolsConfig extends BxDolConfig {

	function MChrisMToolsConfig($aModule) {
	    parent::__construct($aModule);
	}
	
	function init(&$oDb) {
        $this->_oDb = &$oDb;
	}
}

?>
