<?php
/***************************************************************************
*
* IMPORTANT: This is a commercial product made by MChristiaan and cannot be modified for other than personal usage. 
* This product cannot be redistributed for free or a fee without written permission from MChristiaan. 
* This notice may not be removed from the source code.
*
***************************************************************************/

define('BX_SECURITY_EXCEPTIONS', true);
$aBxSecurityExceptions = array(
    'POST.abus_text',
    'REQUEST.abus_text',
    'POST.abus_pttext',
    'REQUEST.abus_pttext',
    'POST.abus_bttext',
    'REQUEST.abus_bttext',
	
	'POST.conus_text',
    'REQUEST.conus_text',
    'POST.conus_pttext',
    'REQUEST.conus_pttext',
    'POST.conus_bttext',
    'REQUEST.conus_bttext',
	
	'POST.priv_text',
    'REQUEST.priv_text',
    'POST.priv_pttext',
    'REQUEST.priv_pttext',
    'POST.priv_bttext',
    'REQUEST.priv_bttext',
	
	'POST.tpe_text',
    'REQUEST.tpe_text',
    'POST.tpe_pttext',
    'REQUEST.tpe_pttext',
    'POST.tpe_bttext',
    'REQUEST.tpe_bttext',
	
	'POST.faq_text',
    'REQUEST.faq_text',
    'POST.faq_pttext',
    'REQUEST.faq_pttext',
    'POST.faq_bttext',
    'REQUEST.faq_bttext',
	
	'POST.help_text',
    'REQUEST.help_text',
    'POST.help_pttext',
    'REQUEST.help_pttext',
    'POST.help_bttext',
    'REQUEST.help_bttext',
	
	'POST.adv_text',
    'REQUEST.adv_text',
    'POST.adv_pttext',
    'REQUEST.adv_pttext',
    'POST.adv_bttext',
    'REQUEST.adv_bttext',
);

bx_import('BxDolAlerts');
bx_import('BxDolModule');
bx_import('BxDolPaginate');
bx_import('BxDolPageView');

class MChrisMToolsModule extends BxDolModule {

    //Variables	
	var $mPageTmpl;
	
	function MChrisMToolsModule(&$aModule) {        
        parent::__construct($aModule);
	    $this->_oConfig->init($this->_oDb);
	    $this->_oTemplate->init($this->_oDb);
		
        $mPageTmpl=array
            (
            'name_index' => 114,
            'header' => $GLOBALS['site']['title'],
            'header_text' => '',
            );		

        $GLOBALS['MChrisMToolsModule'] = &$this;
		
    }
	
	function actionAdministration () {
			
		if (!$GLOBALS['logged']['admin']) { // check access to the page
			$this->mPageTmpl['header'] = 'Error';
			//$this->mPageTmpl['header_text'] = 'Error';
            $mPageTmpl['name_index']['page_main_code'] = $this->_oTemplate->displayAccessDenied ();
			$this->_oTemplate->pageCode($this->mPageTmpl, array('page_main_code' => $mPageTmpl['name_index']['page_main_code']), false);
            return;
        }
		
		$mtools_css = $this->_oTemplate->addCss('main.css', true);
		
		if (get_magic_quotes_gpc()) {
			function stripslashes_deep($value)
			{
				$value = is_array($value) ?
							array_map('stripslashes_deep', $value) :
							stripslashes($value);

				return $value;
			}

			$_POST = array_map('stripslashes_deep', $_POST);
			$_GET = array_map('stripslashes_deep', $_GET);
			$_COOKIE = array_map('stripslashes_deep', $_COOKIE);
			$_REQUEST = array_map('stripslashes_deep', $_REQUEST);
		}

				
        $this->mPageTmpl['header'] = _t('_mchristiaan_mtoolsH');
        $this->mPageTmpl['header_text'] = _t('_mchristiaan_mtoolsH');
		
		$mtools_prgr = $_GET['mtools_prgr'];
		$mtools_action = $_GET['mtools_action'];
		$mtools_lang = $_GET['mtools_lang'];

		if ($mtools_prgr == '')
			$TPRGR = 1;
		else
			$TPRGR = $mtools_prgr;
			
		if ($mtools_lang == '')
			$TLID = $this->_oDb->getfirstLang();
		else
			$TLID = $mtools_lang;
				
		$iTLangs = $this->_oDb->getLangs();
		
		if($TPRGR != 5){			
			$Editor = $this->editor();
			$pageCode .= $Editor;
		}
		
		$JScript = $this->jscript();
		$pageCode .= $JScript;
		
		switch ($TPRGR)
		{		
			default:
			case 1:		
					$mtools_pttext = $_POST['abus_pttext'];
					$mtools_bttext = $_POST['abus_bttext'];
					$mtools_text = $_POST['abus_text'];
					$mPageTmpl['name_index']['page_main_code'].=$this->ABUSCode($TLID,$iTLangs,$pageCode,$mtools_css,$TPRGR,$mtools_action,$mtools_pttext,$mtools_bttext,$mtools_text);
					break;
			case 2:						
					$mtools_pttext = $_POST['conus_pttext'];
					$mtools_bttext = $_POST['conus_bttext'];
					$mtools_text = $_POST['conus_text'];
					$mPageTmpl['name_index']['page_main_code'].=$this->CONUSCode($TLID,$iTLangs,$pageCode,$mtools_css,$TPRGR,$mtools_action,$mtools_pttext,$mtools_bttext);
					break;
			case 3:						
					$mtools_pttext = $_POST['priv_pttext'];
					$mtools_bttext = $_POST['priv_bttext'];
					$mtools_text = $_POST['priv_text'];
					$mPageTmpl['name_index']['page_main_code'].=$this->PRIVCode($TLID,$iTLangs,$pageCode,$mtools_css,$TPRGR,$mtools_action,$mtools_pttext,$mtools_bttext,$mtools_text);
					break;
			case 4:						
					$mtools_pttext = $_POST['tpe_pttext'];
					$mtools_bttext = $_POST['tpe_bttext'];
					$mtools_text = $_POST['tpe_text'];
					$mPageTmpl['name_index']['page_main_code'].=$this->TPECode($TLID,$iTLangs,$pageCode,$mtools_css,$TPRGR,$mtools_action,$mtools_pttext,$mtools_bttext,$mtools_text);
					break;
			case 5:						
					$mtools_pttext = $_POST['faq_pttext'];
					$mtools_bttext = $_POST['faq_bttext'];
					$mtools_text = $_POST['faq_text'];
					$mPageTmpl['name_index']['page_main_code'].=$this->FAQCode($TLID,$iTLangs,$pageCode,$mtools_css,$TPRGR,$mtools_action,$mtools_pttext,$mtools_bttext,$mtools_text);
					break;
			case 6:						
					$mtools_pttext = $_POST['help_pttext'];
					$mtools_bttext = $_POST['help_bttext'];
					$mtools_text = $_POST['help_text'];
					$mPageTmpl['name_index']['page_main_code'].=$this->HELPCode($TLID,$iTLangs,$pageCode,$mtools_css,$TPRGR,$mtools_action,$mtools_pttext,$mtools_bttext,$mtools_text);
					break;
			case 7:						
					$mtools_pttext = $_POST['adv_pttext'];
					$mtools_bttext = $_POST['adv_bttext'];
					$mtools_text = $_POST['adv_text'];
					$mPageTmpl['name_index']['page_main_code'].=$this->ADVCode($TLID,$iTLangs,$pageCode,$mtools_css,$TPRGR,$mtools_action,$mtools_pttext,$mtools_bttext,$mtools_text);
					break;
		}
		$this->_oTemplate->pageCode($this->mPageTmpl, array('page_main_code' => $mPageTmpl['name_index']['page_main_code']), true);
  	}
	
	function jscript(){
		return $dbSc1 = '
			<script type="text/javascript">
				function hideit(){
				  var o = document.getElementById("hidethisone");
				  o.style.display = "none";
				}
				window.onload = function(){
				  setTimeout("hideit()",5000); // 5 seconds after user (re)load the page
				};
			</script>		
		';
	}
	
	function editor(){
		$dbSc1 = '<script type="text/javascript" src="' . BX_DOL_URL_ROOT . 'plugins/tiny_mce/tiny_mce_gzip.js"></script>';
		$dbSc2 = '
		<!-- tinyMCE gz -->	
		<script type="text/javascript">
			tinyMCE_GZ.init({
				plugins : "table,save,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,zoom,media,searchreplace,print,contextmenu,directionality,fullscreen",
				themes : "advanced",
				languages : "en",
				disk_cache : true,
				debug : false
			});

			if (window.attachEvent)
				window.attachEvent( "onload", InitTiny );
			else
				window.addEventListener( "load", InitTiny, false);

			function InitTiny() {
				// Notice: The simple theme does not use all options some of them are limited to the advanced theme
				tinyMCE.init({
					convert_urls : false,
					remove_linebreaks : false,
					mode : "specific_textareas",
					theme : "advanced",

					editor_selector : /(form_input_textarea form_input_html)/,

					plugins : "table,save,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,zoom,media,searchreplace,print,contextmenu,directionality,fullscreen",

					theme_advanced_buttons1_add : "fontselect,fontsizeselect",
					theme_advanced_buttons2_add_before: "cut,copy,paste,pastetext,pasteword,image,media,separator,search,replace,separator",
					theme_advanced_buttons2_add : "separator,insertdate,inserttime,separator,forecolor,backcolor",
					theme_advanced_buttons3_add : "emotions",
					theme_advanced_toolbar_location : "top",
					theme_advanced_toolbar_align : "left",
					theme_advanced_statusbar_location : "bottom",

					plugi2n_insertdate_dateFormat : "%Y-%m-%d",
					plugi2n_insertdate_timeFormat : "%H:%M:%S",			
					theme_advanced_resizing : false,
					theme_advanced_resize_horizontal : false,

					paste_use_dialog : false,
					paste_auto_cleanup_on_paste : true,
					paste_convert_headers_to_strong : false,
					paste_strip_class_attributes : "all",
					paste_remove_spans : false,
					paste_remove_styles : false
				});
			}
		</script>
		';
		return $sCode = $dbSc1 . $dbSc2;
	}

	function savemsg($i){
		if($i==1){
				return $pCode .= '	
					<div id="hidethisone" class="adm-db-content-wrapper">
						<div id="quotes_box">
							' . MsgBox(_t('_mchristiaan_mtoolsS_success')) . '</div>
							<div class="clear_both"></div>
						</div>
					</div>					
					';
		}else{
				return $pCode .= '	
					<div id="hidethisone" class="adm-db-content-wrapper">
						<div id="quotes_box">
							' . MsgBox(_t('_mchristiaan_mtoolsS_error')) . '</div>
							<div class="clear_both"></div>
						</div>
					</div>					
					';
		}
	}
	
	function formheader($iTLangs,$TLID,$TPRGR){
		$pageCode .= '<form class="mtools_form" method="POST" action="?r=mtools/administration&mtools_action=save&mtools_prgr='.$TPRGR.'&mtools_lang=' . $TLID . '">
					  <table border="0">
						<tr>
						  <td><table border="0">
							  <tr>
								<td colspan="5" class="caption">' . _t('_mchristiaan_mtoolsT_caption') . '</td>
							  </tr>
							  <tr class="mtools_prgr">
								<td><a href="?r=mtools/administration&mtools_prgr=1&mtools_lang='.$TLID.'">' . _t('_mchristiaan_mtoolsTABU_link') . '</a></td>
								<td><a href="?r=mtools/administration&mtools_prgr=2&mtools_lang='.$TLID.'">' . _t('_mchristiaan_mtoolsTCT_link') . '</a></td>
								<td><a href="?r=mtools/administration&mtools_prgr=3&mtools_lang='.$TLID.'">' . _t('_mchristiaan_mtoolsTPRIV_link') . '</a></td>
								<td><a href="?r=mtools/administration&mtools_prgr=4&mtools_lang='.$TLID.'">' . _t('_mchristiaan_mtoolsTTPE_link') . '</a></td>
								<td><a href="?r=mtools/administration&mtools_prgr=5&mtools_lang='.$TLID.'">' . _t('_mchristiaan_mtoolsTFAQ_link') . '</a></td>
								<td><a href="?r=mtools/administration&mtools_prgr=6&mtools_lang='.$TLID.'">' . _t('_mchristiaan_mtoolsTHELP_link') . '</a></td>
								<td><a href="?r=mtools/administration&mtools_prgr=7&mtools_lang='.$TLID.'">' . _t('_mchristiaan_mtoolsTADV_link') . '</a></td>
							  </tr>
						  </table></td></tr>
						<tr>
						  <td><table border="0">
							  <tr>
								<td class="caption">' . _t('_mchristiaan_mtoolsL_caption') . '</td>
							  </tr>							  
					';
		$i = 0;			
		foreach ($iTLangs as $iTLID => $iTLdata) {
			
			if($i==0)
				$pageCode .= '<tr>';

			$tdLID = (int)$iTLdata['ID'];
			$tdLName = $iTLdata['Name'];
			$tdLTitle = $iTLdata['Title'];
			$tdLFlag = $iTLdata['Flag'];
			$tdImUrl = BX_DOL_URL_ROOT . 'media/images/flags/'. $tdLFlag .'.gif';
			if ($tdLID == $TLID) {
				$pageCode .= '<td id="selected"><img src="'. $tdImUrl .'"/>&nbsp;' . $tdLTitle . '</td>';
			} else {
				$pageCode .= '<td><img src="'. $tdImUrl .'"/>&nbsp;<a href="?r=mtools/administration&mtools_prgr='.$TPRGR.'&mtools_lang=' . $tdLID . '">' . $tdLTitle . '</a></td>';
			}
			
			$i++;			
			if($i==4){
				$pageCode .= '</tr>';
				$i=0;
			}
		}
		
		if($i%4 !=0)
			$pageCode .= '</tr>';
		
		$pageCode .= '</table></td></tr>';
		
		return $pageCode;
	}
	
	function ABUSCode ($TLID,$iTLangs,$pageCode,$mtools_css,$TPRGR,$mtools_action,$mtools_pttext,$mtools_bttext,$mtools_text) {
	
		$abus_action = $mtools_action;
		$abus_pttext = $mtools_pttext;
		$abus_bttext = $mtools_bttext;
		$abus_text = $mtools_text;
		
		$PageTitle = '_ABOUT_US_H';
		$BoxTitle = '_About';
		$StringText = '_ABOUT_US';
		$iTPTId = $this->_oDb->getId($PageTitle); // get our terms page title id
		$iTBTId = $this->_oDb->getId($BoxTitle); // get our terms box title id	
	    $iTId = $this->_oDb->getId($StringText); // get our terms id
		
	    if(empty($iTId) && empty($iTPTId) && empty($iTBTId)) { // if terms Id is not found display page not found
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
		
        $iTPTString = $this->_oDb->getString($iTPTId,$TLID);
		$iTBTString = $this->_oDb->getString($iTBTId,$TLID);
		$iTString = $this->_oDb->getString($iTId,$TLID);
		
        if (empty($iTString) || empty($iTBTString) || empty($iTPTString)) { // check if entry exists
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
		
		if ($abus_action == 'save' && ($abus_pttext != '' || $abus_bttext != '' || $abus_text != '')){
			$p = $this->_oDb->updateString($abus_pttext,$iTPTId,$TLID);
			$b = $this->_oDb->updateString($abus_bttext,$iTBTId,$TLID);
			$t = $this->_oDb->updateString($abus_text,$iTId,$TLID);
			compileLanguage($TLID);
			
			if ($t == 1 && $p == 1 && $b == 1){
				$pageCode .= $this->savemsg(1);
			}else{
				$pageCode .= $this->savemsg(0);
			}
		}
	
		$pageCode .= $this->formheader($iTLangs,$TLID,$TPRGR);
		
		if($abus_pttext == '')
			$iTPTtext = $iTPTString;
		else
			$iTPTtext = $abus_pttext;
			
		if($abus_bttext == '')
			$iTBTtext = $iTBTString;
		else
			$iTBTtext = $abus_bttext;
			
		if($abus_text == '')
			$iTtext = $iTString;
		else
			$iTtext = $abus_text;
		
		$pageCode .= '
				<tr>
				<td><table border="0">
				<tr id="editor_page_title_text">
				  <td class="caption">' . _t('_mchristiaan_abusP_caption') . '</td>
				  <td><input type="text" class="form_input_text form_input_html" name="abus_pttext" value="'.$iTPTtext.'"/></td>
				  <td>&nbsp;</td>
				</tr>
				<tr id="editor_box_title_text">
				  <td class="caption">' . _t('_mchristiaan_abusB_caption') . '</td>
				  <td><input type="text" class="form_input_text form_input_html" name="abus_bttext" value="'.$iTBTtext.'"/></td>
				  <td>&nbsp;</td>
				</tr>
				</table></td></tr>
				<tr id="editor_text">
				  <td><textarea class="form_input_textarea form_input_html" rows="30" name="abus_text" cols="94">' . $iTtext . '</textarea></td>
				</tr>
				  <td><input type="submit" value="Save" name="B1"></td>
				</tr>
			  </table>
			</form>
			';
			
            return DesignBoxContent(_t('_mchristiaan_abusH_text'), $mtools_css . $pageCode, 1);
	
	}
	
	function CONUSCode ($TLID,$iTLangs,$pageCode,$mtools_css,$TPRGR,$mtools_action,$mtools_pttext,$mtools_bttext) {
	
		$conus_action = $mtools_action;
		$conus_pttext = $mtools_pttext;
		$conus_bttext = $mtools_bttext;
		
		$PageTitle = '_CONTACT_H';
		$BoxTitle = '_CONTACT_H1';
		$iTPTId = $this->_oDb->getId($PageTitle); // get our terms page title id
		$iTBTId = $this->_oDb->getId($BoxTitle); // get our terms box title id
		
	    if(empty($iTPTId) && empty($iTBTId)) { // if terms Id is not found display page not found
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
		
        $iTPTString = $this->_oDb->getString($iTPTId,$TLID);
		$iTBTString = $this->_oDb->getString($iTBTId,$TLID);
		
        if (empty($iTBTString) || empty($iTPTString)) { // check if entry exists
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
		
		if ($conus_action == 'save' && ($conus_pttext != '' || $conus_bttext != '')){
			$p = $this->_oDb->updateString($conus_pttext,$iTPTId,$TLID);
			$b = $this->_oDb->updateString($conus_bttext,$iTBTId,$TLID);
			compileLanguage($TLID);
			
			if ($p == 1 && $b == 1){
				$pageCode .= $this->savemsg(1);
			}else{
				$pageCode .= $this->savemsg(0);
			}
		}
	
		$pageCode .= $this->formheader($iTLangs,$TLID,$TPRGR);
		
		if($conus_pttext == '')
			$iTPTtext = $iTPTString;
		else
			$iTPTtext = $conus_pttext;
			
		if($conus_bttext == '')
			$iTBTtext = $iTBTString;
		else
			$iTBTtext = $conus_bttext;
			
		$homeurl = BX_DOL_URL_ROOT;
		
		$pageCode .= '
				<tr>
				<td><table border="0">
				<tr id="editor_page_title_text">
				  <td class="caption">' . _t('_mchristiaan_conusP_caption') . '</td>
				  <td><input type="text" class="form_input_text form_input_html" name="conus_pttext" value="'.$iTPTtext.'"/></td>
				  <td>&nbsp;</td>
				</tr>
				<tr id="editor_box_title_text">
				  <td class="caption">' . _t('_mchristiaan_conusB_caption') . '</td>
				  <td><input type="text" class="form_input_text form_input_html" name="conus_bttext" value="'.$iTBTtext.'"/></td>
				  <td>&nbsp;</td>
				</tr>
				</table></td></tr>
				<tr><td><table border="0">
				<tr>				
				  <td class="note">' . _t('_mchristiaan_conus_note_text') . '<br/> '.$homeurl.'contact.php</td>
				</tr>
				</table></td></tr>
				  <td><input type="submit" value="Save" name="B1"></td>
				</tr>
			  </table>
			</form>
			';
            
            return DesignBoxContent(_t('_mchristiaan_conusH_text'), $mtools_css . $pageCode, 1);
	
	}
	
	function PRIVCode ($TLID,$iTLangs,$pageCode,$mtools_css,$TPRGR,$mtools_action,$mtools_pttext,$mtools_bttext,$mtools_text) {
	
		$priv_action = $mtools_action;
		$priv_pttext = $mtools_pttext;
		$priv_bttext = $mtools_bttext;
		$priv_text = $mtools_text;
		
		$PageTitle = '_PRIVACY_H';
		$BoxTitle = '_PRIVACY_H1';
		$StringText = '_PRIVACY';
		$iTPTId = $this->_oDb->getId($PageTitle); // get our terms page title id
		$iTBTId = $this->_oDb->getId($BoxTitle); // get our terms box title id	
	    $iTId = $this->_oDb->getId($StringText); // get our terms id
		
	    if(empty($iTId) && empty($iTPTId) && empty($iTBTId)) { // if terms Id is not found display page not found
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
		
        $iTPTString = $this->_oDb->getString($iTPTId,$TLID);
		$iTBTString = $this->_oDb->getString($iTBTId,$TLID);
		$iTString = $this->_oDb->getString($iTId,$TLID);
		
        if (empty($iTString) || empty($iTBTString) || empty($iTPTString)) { // check if entry exists
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
		
		if ($priv_action == 'save' && ($priv_pttext != '' || $priv_bttext != '' || $priv_text != '')){
			$p = $this->_oDb->updateString($priv_pttext,$iTPTId,$TLID);
			$b = $this->_oDb->updateString($priv_bttext,$iTBTId,$TLID);
			$t = $this->_oDb->updateString($priv_text,$iTId,$TLID);
			compileLanguage($TLID);
			
			if ($t == 1 && $p == 1 && $b == 1){
				$pageCode .= $this->savemsg(1);
			}else{
				$pageCode .= $this->savemsg(0);
			}
		}
	
		$pageCode .= $this->formheader($iTLangs,$TLID,$TPRGR);
		
		if($priv_pttext == '')
			$iTPTtext = $iTPTString;
		else
			$iTPTtext = $priv_pttext;
			
		if($priv_bttext == '')
			$iTBTtext = $iTBTString;
		else
			$iTBTtext = $priv_bttext;
			
		if($priv_text == '')
			$iTtext = $iTString;
		else
			$iTtext = $priv_text;
		
		$pageCode .= '
				<tr>
				<td><table border="0">
				<tr id="editor_page_title_text">
				  <td class="caption">' . _t('_mchristiaan_privP_caption') . '</td>
				  <td><input type="text" class="form_input_text form_input_html" name="priv_pttext" value="'.$iTPTtext.'"/></td>
				  <td>&nbsp;</td>
				</tr>
				<tr id="editor_box_title_text">
				  <td class="caption">' . _t('_mchristiaan_privB_caption') . '</td>
				  <td><input type="text" class="form_input_text form_input_html" name="priv_bttext" value="'.$iTBTtext.'"/></td>
				  <td>&nbsp;</td>
				</tr>
				</table></td></tr>
				<tr id="editor_text">
				  <td><textarea class="form_input_textarea form_input_html" rows="30" name="priv_text" cols="94">' . $iTtext . '</textarea></td>
				</tr>
				  <td><input type="submit" value="Save" name="B1"></td>
				</tr>
			  </table>
			</form>
			';
            
            return DesignBoxContent(_t('_mchristiaan_privH_text'), $mtools_css . $pageCode, 1);
	
	}
	
	function TPECode ($TLID,$iTLangs,$pageCode,$mtools_css,$TPRGR,$mtools_action,$mtools_pttext,$mtools_bttext,$mtools_text) {
	
		$tpe_action = $mtools_action;
		$tpe_pttext = $mtools_pttext;
		$tpe_bttext = $mtools_bttext;
		$tpe_text = $mtools_text;
		
		$PageTitle = '_TERMS_OF_USE_H';
		$BoxTitle = '_TERMS_OF_USE_H1';
		$StringText = '_TERMS_OF_USE';
		$iTPTId = $this->_oDb->getId($PageTitle); // get our terms page title id
		$iTBTId = $this->_oDb->getId($BoxTitle); // get our terms box title id	
	    $iTId = $this->_oDb->getId($StringText); // get our terms id
		
	    if(empty($iTId) && empty($iTPTId) && empty($iTBTId)) { // if terms Id is not found display page not found
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
		
        $iTPTString = $this->_oDb->getString($iTPTId,$TLID);
		$iTBTString = $this->_oDb->getString($iTBTId,$TLID);
		$iTString = $this->_oDb->getStringTPE($iTId,$TLID);
		
        if (empty($iTString) || empty($iTBTString) || empty($iTPTString)) { // check if entry exists
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
		
		if ($tpe_action == 'save' && ($tpe_pttext != '' || $tpe_bttext != '' || $tpe_text != '')){
			$p = $this->_oDb->updateString($tpe_pttext,$iTPTId,$TLID);
			$b = $this->_oDb->updateString($tpe_bttext,$iTBTId,$TLID);
			$t = $this->_oDb->updateStringTPE($tpe_text,$iTId,$TLID);
			compileLanguage($TLID);
			
			if ($t == 1 && $p == 1 && $b == 1){
				$pageCode .= $this->savemsg(1);
			}else{
				$pageCode .= $this->savemsg(0);
			}
		}
	
		$pageCode .= $this->formheader($iTLangs,$TLID,$TPRGR);
		
		if($tpe_pttext == '')
			$iTPTtext = $iTPTString;
		else
			$iTPTtext = $tpe_pttext;
			
		if($tpe_bttext == '')
			$iTBTtext = $iTBTString;
		else
			$iTBTtext = $tpe_bttext;
			
		if($tpe_text == '')
			$iTtext = $iTString;
		else
			$iTtext = $tpe_text;
		
		$pageCode .= '
				<tr>
				<td><table border="0">
				<tr id="editor_page_title_text">
				  <td class="caption">' . _t('_mchristiaan_tpeP_caption') . '</td>
				  <td><input type="text" class="form_input_text form_input_html" name="tpe_pttext" value="'.$iTPTtext.'"/></td>
				  <td>&nbsp;</td>
				</tr>
				<tr id="editor_box_title_text">
				  <td class="caption">' . _t('_mchristiaan_tpeB_caption') . '</td>
				  <td><input type="text" class="form_input_text form_input_html" name="tpe_bttext" value="'.$iTBTtext.'"/></td>
				  <td>&nbsp;</td>
				</tr>
				</table></td></tr>
				<tr id="editor_text">
				  <td><textarea class="form_input_textarea form_input_html" rows="30" name="tpe_text" cols="94">' . $iTtext . '</textarea></td>
				</tr>
				  <td><input type="submit" value="Save" name="B1"></td>
				</tr>
			  </table>
			</form>
			';
			
            return DesignBoxContent(_t('_mchristiaan_tpeH_text'), $mtools_css . $pageCode, 1);
	
	}

	function FAQCode ($TLID,$iTLangs,$pageCode,$mtools_css,$TPRGR,$mtools_action,$mtools_pttext,$mtools_bttext,$mtools_text) {
	
		$faq_action = $mtools_action;
		$faq_pttext = $mtools_pttext;
		$faq_bttext = $mtools_bttext;
		$faq_text = $mtools_text;
		
		$PageTitle = '_FAQ_H';
		$BoxTitle = '_FAQ_H1';
		$StringText = '_FAQ_INFO';
		$iTPTId = $this->_oDb->getId($PageTitle); // get our terms page title id
		$iTBTId = $this->_oDb->getId($BoxTitle); // get our terms box title id	
	    $iTId = $this->_oDb->getId($StringText); // get our terms id
		
	    if(empty($iTId) && empty($iTPTId) && empty($iTBTId)) { // if terms Id is not found display page not found
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
		
        $iTPTString = $this->_oDb->getString($iTPTId,$TLID);
		$iTBTString = $this->_oDb->getString($iTBTId,$TLID);
		$iTString = $this->_oDb->getStringFAQ($iTId,$TLID);
		
        if (empty($iTString) || empty($iTBTString) || empty($iTPTString)) { // check if entry exists
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
		
		if ($faq_action == 'save' && ($faq_pttext != '' || $faq_bttext != '' || $faq_text != '')){
			$p = $this->_oDb->updateString($faq_pttext,$iTPTId,$TLID);
			$b = $this->_oDb->updateString($faq_bttext,$iTBTId,$TLID);
			$t = $this->_oDb->updateStringFAQ($faq_text,$iTId,$TLID);
			compileLanguage($TLID);
			
			if ($t == 1 && $p == 1 && $b == 1){
				$pageCode .= $this->savemsg(1);
			}else{
				$pageCode .= $this->savemsg(0);
			}
		}
	
		$pageCode .= $this->formheader($iTLangs,$TLID,$TPRGR);
		
		if($faq_pttext == '')
			$iTPTtext = $iTPTString;
		else
			$iTPTtext = $faq_pttext;
			
		if($faq_bttext == '')
			$iTBTtext = $iTBTString;
		else
			$iTBTtext = $faq_bttext;
			
		if($faq_text == '')
			$iTtext = $iTString;
		else
			$iTtext = $faq_text;
			
		$example = highlight_string('<faq_' . _t('_mchristiaan_faq_acontent') . '>
<faq_' . _t('_mchristiaan_faq_aheader') . '>Where can I get a free Dolphin license?</faq>
<faq_' . _t('_mchristiaan_faq_asnippet') . '>www.boonex.com</faq>
</faq>', true);
		
		$pageCode .= '
				<tr>
				<td><table border="0">
				<tr id="editor_page_title_text">
				  <td class="caption">' . _t('_mchristiaan_faqP_caption') . '</td>
				  <td><input type="text" class="form_input_text form_input_html" name="faq_pttext" value="'.$iTPTtext.'"/></td>
				  <td>&nbsp;</td>
				</tr>
				<tr id="editor_box_title_text">
				  <td class="caption">' . _t('_mchristiaan_faqB_caption') . '</td>
				  <td><input type="text" class="form_input_text form_input_html" name="faq_bttext" value="'.$iTBTtext.'"/></td>
				  <td>&nbsp;</td>
				</tr>
				</table></td></tr>
				<td>
				<table border="0">
						<tr>
						  <td class="caption">' . _t('_mchristiaan_faq_ex_caption') . '</td>
						</tr>
						<tr>
						  <td class="faq_note">';
							$pageCode .= $example;
							$pageCode .= '
						  </td>
						</tr>
				</table>
				</td>
				<tr id="editor_text">
				  <td><textarea class="form_input_textarea form_input_html" rows="30" name="faq_text" cols="94">' . $iTtext . '</textarea></td>
				</tr>
				  <td><input type="submit" value="Save" name="B1"></td>
				</tr>
			  </table>
			</form>			
			';
			
            return DesignBoxContent(_t('_mchristiaan_faqH_text'), $mtools_css . $pageCode, 1);
	
	}
	
	function HELPCode ($TLID,$iTLangs,$pageCode,$mtools_css,$TPRGR,$mtools_action,$mtools_pttext,$mtools_bttext,$mtools_text) {
	
		$help_action = $mtools_action;
		$help_pttext = $mtools_pttext;
		$help_bttext = $mtools_bttext;
		$help_text = $mtools_text;
		
		$PageTitle = '_HELP_H';
		$BoxTitle = '_HELP_H1';
		$StringText = '_HELP';
		$iTPTId = $this->_oDb->getId($PageTitle); // get our terms page title id
		$iTBTId = $this->_oDb->getId($BoxTitle); // get our terms box title id	
	    $iTId = $this->_oDb->getId($StringText); // get our terms id
		
	    if(empty($iTId) && empty($iTPTId) && empty($iTBTId)) { // if terms Id is not found display page not found
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
		
        $iTPTString = $this->_oDb->getString($iTPTId,$TLID);
		$iTBTString = $this->_oDb->getString($iTBTId,$TLID);
		$iTString = $this->_oDb->getString($iTId,$TLID);
		
        if (empty($iTString) || empty($iTBTString) || empty($iTPTString)) { // check if entry exists
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
		
		if ($help_action == 'save' && ($help_pttext != '' || $help_bttext != '' || $help_text != '')){
			$p = $this->_oDb->updateString($help_pttext,$iTPTId,$TLID);
			$b = $this->_oDb->updateString($help_bttext,$iTBTId,$TLID);
			$t = $this->_oDb->updateString($help_text,$iTId,$TLID);
			compileLanguage($TLID);
			
			if ($t == 1 && $p == 1 && $b == 1){
				$pageCode .= $this->savemsg(1);
			}else{
				$pageCode .= $this->savemsg(0);
			}
		}
	
		$pageCode .= $this->formheader($iTLangs,$TLID,$TPRGR);
		
		if($help_pttext == '')
			$iTPTtext = $iTPTString;
		else
			$iTPTtext = $help_pttext;
			
		if($help_bttext == '')
			$iTBTtext = $iTBTString;
		else
			$iTBTtext = $help_bttext;
			
		if($help_text == '')
			$iTtext = $iTString;
		else
			$iTtext = $help_text;
		
		$pageCode .= '
				<tr>
				<td><table border="0">
				<tr id="editor_page_title_text">
				  <td class="caption">' . _t('_mchristiaan_helpP_caption') . '</td>
				  <td><input type="text" class="form_input_text form_input_html" name="help_pttext" value="'.$iTPTtext.'"/></td>
				  <td>&nbsp;</td>
				</tr>
				<tr id="editor_box_title_text">
				  <td class="caption">' . _t('_mchristiaan_helpB_caption') . '</td>
				  <td><input type="text" class="form_input_text form_input_html" name="help_bttext" value="'.$iTBTtext.'"/></td>
				  <td>&nbsp;</td>
				</tr>
				</table></td></tr>
				<tr id="editor_text">
				  <td><textarea class="form_input_textarea form_input_html" rows="30" name="help_text" cols="94">' . $iTtext . '</textarea></td>
				</tr>
				  <td><input type="submit" value="Save" name="B1"></td>
				</tr>
			  </table>
			</form>
			';
			
            return DesignBoxContent(_t('_mchristiaan_helpH_text'), $mtools_css . $pageCode, 1);
	
	}
	
	function ADVCode ($TLID,$iTLangs,$pageCode,$mtools_css,$TPRGR,$mtools_action,$mtools_pttext,$mtools_bttext,$mtools_text) {
	
		$adv_action = $mtools_action;
		$adv_pttext = $mtools_pttext;
		$adv_bttext = $mtools_bttext;
		$adv_text = $mtools_text;
		
		$PageTitle = '_ADVICE_H';
		$BoxTitle = '_ADVICE_H1';
		$StringText = '_ADVICE';
		$iTPTId = $this->_oDb->getId($PageTitle); // get our terms page title id
		$iTBTId = $this->_oDb->getId($BoxTitle); // get our terms box title id	
	    $iTId = $this->_oDb->getId($StringText); // get our terms id
		
	    if(empty($iTId) && empty($iTPTId) && empty($iTBTId)) { // if terms Id is not found display page not found
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
		
        $iTPTString = $this->_oDb->getString($iTPTId,$TLID);
		$iTBTString = $this->_oDb->getString($iTBTId,$TLID);
		$iTString = $this->_oDb->getString($iTId,$TLID);
		
        if (empty($iTString) || empty($iTBTString) || empty($iTPTString)) { // check if entry exists
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
		
		if ($adv_action == 'save' && ($adv_pttext != '' || $adv_bttext != '' || $adv_text != '')){
			$p = $this->_oDb->updateString($adv_pttext,$iTPTId,$TLID);
			$b = $this->_oDb->updateString($adv_bttext,$iTBTId,$TLID);
			$t = $this->_oDb->updateString($adv_text,$iTId,$TLID);
			compileLanguage($TLID);
			
			if ($t == 1 && $p == 1 && $b == 1){
				$pageCode .= $this->savemsg(1);
			}else{
				$pageCode .= $this->savemsg(0);
			}
		}
	
		$pageCode .= $this->formheader($iTLangs,$TLID,$TPRGR);
		
		if($adv_pttext == '')
			$iTPTtext = $iTPTString;
		else
			$iTPTtext = $adv_pttext;
			
		if($adv_bttext == '')
			$iTBTtext = $iTBTString;
		else
			$iTBTtext = $adv_bttext;
			
		if($adv_text == '')
			$iTtext = $iTString;
		else
			$iTtext = $adv_text;
		
		$pageCode .= '
				<tr>
				<td><table border="0">
				<tr id="editor_page_title_text">
				  <td class="caption">' . _t('_mchristiaan_advP_caption') . '</td>
				  <td><input type="text" class="form_input_text form_input_html" name="adv_pttext" value="'.$iTPTtext.'"/></td>
				  <td>&nbsp;</td>
				</tr>
				<tr id="editor_box_title_text">
				  <td class="caption">' . _t('_mchristiaan_advB_caption') . '</td>
				  <td><input type="text" class="form_input_text form_input_html" name="adv_bttext" value="'.$iTBTtext.'"/></td>
				  <td>&nbsp;</td>
				</tr>
				</table></td></tr>
				<tr id="editor_text">
				  <td><textarea class="form_input_textarea form_input_html" rows="30" name="adv_text" cols="94">' . $iTtext . '</textarea></td>
				</tr>
				  <td><input type="submit" value="Save" name="B1"></td>
				</tr>
			  </table>
			</form>
			';
			
            return DesignBoxContent(_t('_mchristiaan_advH_text'), $mtools_css . $pageCode, 1);
	
	}
	
}
?>
