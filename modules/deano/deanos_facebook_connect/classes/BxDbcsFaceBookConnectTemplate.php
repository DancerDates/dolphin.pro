<?php
/***************************************************************************
* Date				: Feb 21, 2013
* Copywrite			: (c) 2013 by Dean J. Bassett Jr.
* Website			: http://www.deanbassett.com
*
* Product Name		: Deanos Facebook Connect
* Product Version	: 4.2.7
*
* IMPORTANT: This is a commercial product made by Dean Bassett Jr.
* and cannot be modified other than personal use.
*  
* This product cannot be redistributed for free or a fee without written
* permission from Dean Bassett Jr.
*
***************************************************************************/
   bx_import('BxDolModuleTemplate');

    class BxDbcsFaceBookConnectTemplate extends BxDolModuleTemplate 
    {
    	/**
    	 * Class constructor
    	 */
    	function BxDbcsFaceBookConnectTemplate(&$oConfig, &$oDb) 
        {
    	    parent::BxDolModuleTemplate($oConfig, $oDb);  
    	}

        function pageCodeAdminStart()
        {
            ob_start();
        }

        function adminBlock ($sContent, $sTitle, $aMenu = array()) 
        {
            return DesignBoxAdmin($sTitle, $sContent, $aMenu);
        }

        function pageCodeAdmin ($sTitle) 
        {
            global $_page;        
            global $_page_cont;

            $_page['name_index'] = 9; 

            $_page['header'] = $sTitle ? $sTitle : $GLOBALS['site']['title'];
            $_page['header_text'] = $sTitle;
            
            $_page_cont[$_page['name_index']]['page_main_code'] = ob_get_clean();

            PageCodeAdmin();
        }


        /**
         * Function will include the js file ;
         *
         * @param  : $sName (string) - name of needed file ;
         * @return : (text) ;
         */
        function addJs($sName)
        {
            return '<script type="text/javascript" src="' . $this -> _oConfig -> getHomeUrl() . 'js/' . $sName . '" language="javascript"/></script>';
        }
        function addJquery()
        {
            return '<script type="text/javascript" src="' . BX_DOL_URL_ROOT . 'plugins/jquery/jquery.js" language="javascript"/></script>';
        }

        /**
         * Function will generate default dolphin's page;
         *
         * @param  : $sPageCaption   (string) - page's title;
         * @param  : $sPageContent   (string) - page's content;
         * @param  : $sPageIcon      (string) - page's icon;
         * @return : (text) html presentation data;
         */
        function getPage($sPageCaption, $sPageContent, $sPageIcon = 'facebook-small-logo.png')
        {
            global $_page;
            global $_page_cont;

            //$iIndex = 54;
            $iIndex = 0;

            $_page['name_index']	= $iIndex;

            // set module's icon;
            //$GLOBALS['oTopMenu'] -> setCustomSubIconUrl( $this -> getIconUrl($sPageIcon) ); 
            $GLOBALS['oTopMenu'] -> setCustomSubHeader($sPageCaption);

            $_page['header']        = $sPageCaption ;
            $_page['header_text']   = $sPageCaption ;
            $_page['css_name']      = 'face_book_connect.css';

            $_page_cont[$iIndex]['page_main_code'] = $sPageContent;
			if($this -> _oConfig -> bDebugEnabled) $this -> _oConfig -> debugLog(__LINE__, 'getPage() Calling PageCode');
            PageCode($this);
			if($this -> _oConfig -> bDebugEnabled) $this -> _oConfig -> debugLog(__LINE__, 'getPage() PageCode called');

        }

		function dbcsPageCode($sTemplateName, $oTemplate = null) {
			global $echo;
			global $_page;
			global $_page_cont;	
			global $oSysTemplate;

			if(empty($oTemplate))
				$oTemplate = $oSysTemplate;
			header( 'Content-type: text/html; charset=utf-8' );
			$echo($oTemplate, $sTemplateName . '.html');
		}

	}
?>