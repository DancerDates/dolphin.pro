<?php
/***************************************************************************
*
* IMPORTANT: This is a commercial product made by AndrewP. It cannot be modified for other than personal usage.
* The "personal usage" means the product can be installed and set up for ONE domain name ONLY.
* To be able to use this product for another domain names you have to order another copy of this product (license).
* This product cannot be redistributed for free or a fee without written permission from AndrewP.
* This notice may not be removed from the source code.
*
***************************************************************************/
bx_import('BxDolEditor');

/**
 * TinyMCE4 editor representation.
 * @see BxDolEditor
 */
class TM4EditorTinyMCE4 extends BxDolEditor
{
    var $_iVisitorID;

    /**
     * Common initialization params
     */
    protected static $CONF_COMMON = "
                    jQuery('{bx_var_selector}').tinymce({
                        {bx_var_custom_init}
                        document_base_url: '{bx_url_root}',
                        remove_script_host: false,
                        relative_urls: false,
                        // script_url: '{bx_url_root}modules/andrew/tm4/bin/tiny_mce_gzip.php',
                        skin: '{bx_var_skin}',
                        language: '{bx_var_lang}',
                        content_css: '{bx_var_css_path}',
                        gecko_spellcheck: true,
                        entity_encoding: 'raw',
                        verify_html: false,
                        selector: '{bx_var_selector}',
                        external_filemanager_path: '{external_filemanager_path}',
                        filemanager_title: 'Responsive Filemanager X',
                        external_plugins: { 'filemanager' : '{external_filemanager_plugin_path}'},
						media_alt_source: true
                    });
    ";

    /**
     * Standard view initialization params
     */
    protected static $WIDTH_STANDARD = '630px';
    protected static $CONF_STANDARD = "
                        width: '100%',
                        height: '270',
                        theme: 'modern',
                        menubar: 'edit insert format table',
                        plugins: [
                            'advlist autolink lists link image charmap print preview hr anchor pagebreak',
                            'searchreplace wordcount visualblocks visualchars code fullscreen',
                            'insertdatetime media nonbreaking save table contextmenu directionality',
                            'emoticons template paste textcolor colorpicker textpattern jbimages responsivefilemanager'
                        ],

                        toolbar1: 'fullpage | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | bullist numlist | outdent indent blockquote | undo redo | styleselect formatselect fontselect fontsizeselect | jbimages | responsivefilemanager',
                        toolbar2: 'cut copy paste | searchreplace | link unlink anchor image media code | insertdatetime preview | forecolor backcolor',
                        toolbar3: 'table | hr removeformat | subscript superscript | charmap emoticons | print | ltr rtl | visualchars visualblocks nonbreaking template pagebreak restoredraft',
                        image_advtab: true,
    ";

    /**
     * Minimal view initialization params
     */
    protected static $WIDTH_MINI = '340px';
    protected static $CONF_MINI = "
                        width: '100%',
                        height: '150',
                        theme: 'modern',
                        menubar: 'edit insert format table',
                        plugins: [
                            'advlist autolink lists link image charmap print preview anchor',
                            'searchreplace visualblocks code fullscreen',
                            'insertdatetime media table contextmenu paste, emoticons'
                        ],
                        toolbar: 'bold italic underline removeformat | bullist numlist | undo redo | alignleft aligncenter alignright | blockquote emoticons | link unlink image',
                        image_advtab: true,
    ";

    /**
     * Full view initialization params
     */
    protected static $WIDTH_FULL = '650px';
    /*
                        classic view:
                        plugins: [
                                'advlist autolink autosave link image lists charmap print preview hr anchor pagebreak spellchecker',
                                'searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking',
                                'table contextmenu directionality emoticons template textcolor paste fullpage textcolor colorpicker textpattern'
                        ],

                        toolbar1: 'fullscreen print | newdocument fullpage | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | styleselect formatselect fontselect fontsizeselect',
                        toolbar2: 'cut copy paste | searchreplace | bullist numlist | outdent indent blockquote | undo redo | link unlink anchor image media code | insertdatetime preview | forecolor backcolor',
                        toolbar3: 'table | hr removeformat | subscript superscript | charmap emoticons | ltr rtl | visualchars visualblocks nonbreaking template pagebreak restoredraft',

                        menubar: false,
                        toolbar_items_size: 'small',
    */
    protected static $CONF_FULL = "
                        width: '100%',
                        height: '320',
                        theme: 'modern',
  
                        plugins: [
                            'advlist autolink lists link image charmap print preview hr anchor pagebreak',
                            'searchreplace wordcount visualblocks visualchars code fullscreen',
                            'insertdatetime media nonbreaking save table contextmenu directionality',
                            'emoticons template paste textcolor colorpicker textpattern jbimages responsivefilemanager'
                        ],
                        menubar: 'edit insert view format table tools',
                        toolbar1: 'fullscreen preview print | fullpage | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | bullist numlist | outdent indent blockquote | styleselect formatselect fontselect fontsizeselect |  forecolor backcolor | table | ',
                        toolbar2: 'cut copy paste | searchreplace | undo redo | link unlink anchor moxiemanager responsivefilemanager image media  | jbimages',
                        toolbar3: 'hr removeformat | subscript superscript | charmap emoticons | ltr rtl | visualchars visualblocks pagebreak restoredraft code',
                        image_advtab: true,  
    ";

    /**
     * Available editor languages
     */
    protected static $CONF_LANGS = array('ar' => 1, 'be' => 1, 'bg' => 1, 'ca' => 1, 'cn' => 1, 'cs' => 1, 'cy' => 1, 'da' => 1, 'de' => 1, 'en' => 1, 'es' => 1, 'et' => 1, 'fa' => 1, 'fi' => 1, 'fr' => 1, 'gl' => 1, 'he' => 1, 'hu' => 1, 'it' => 1, 'ja' => 1, 'km' => 1, 'ko' => 1, 'lt' => 1, 'lv' => 1, 'mk' => 1, 'ml' => 1, 'nb' => 1, 'nl' => 1, 'no' => 1, 'pl' => 1, 'pt' => 1, 'ro' => 1, 'ru' => 1, 'sk' => 1, 'sl' => 1, 'sq' => 1, 'sv' => 1, 'tr' => 1, 'uk' => 1, 'zh' => 1);

    protected $_oTemplate;
    protected $_bJsCssAdded = false;

    public function __construct ($aObject, $oTemplate = '')
    {
        parent::__construct ($aObject);

        // if ($oTemplate)
            // $this->_oTemplate = $oTemplate;
        // else
            $this->_oTemplate = $GLOBALS['oSysTemplate'];
    }

    /**
     * Get minimal width which is neede for editor for the provided view mode
     */
    public function getWidth ($iViewMode)
    {
        switch ($iViewMode) {
            case BX_EDITOR_MINI:
                return self::$WIDTH_MINI;
            case BX_EDITOR_FULL:
                return self::$WIDTH_FULL;
            break;
            case BX_EDITOR_STANDARD:
            default:
                return self::$WIDTH_STANDARD;
        }
    }

    /**
     * Attach editor to HTML element, in most cases - textarea.
     * @param $sSelector - jQuery selector to attach editor to.
     * @param $iViewMode - editor view mode: BX_EDITOR_STANDARD, BX_EDITOR_MINI, BX_EDITOR_FULL
     * @param $bDynamicMode - is AJAX mode or not, the HTML with editor area is loaded dynamically.
     */
    public function attachEditor ($sSelector, $iViewMode = BX_EDITOR_STANDARD, $bDynamicMode = false)
    {
        $bOverDynMode = getParam('sys_template_cache_js_enable');
        $bDynamicMode = ($bOverDynMode) ? true : $bDynamicMode;

        // set visual mode
        switch ($iViewMode) {
            case BX_EDITOR_MINI:
                 $sToolsItems = self::$CONF_MINI;
                break;
            case BX_EDITOR_FULL:
                $sToolsItems = self::$CONF_FULL;
            break;
            case BX_EDITOR_STANDARD:
            default:
                 $sToolsItems = self::$CONF_STANDARD;
        }

        // detect language
        $sLang = (isset(self::$CONF_LANGS[$GLOBALS['sCurrentLanguage']]) ? $GLOBALS['sCurrentLanguage'] : 'en');

        $sOverPath = '';
        $this->_iVisitorID = getLoggedId();
        if($this->_iVisitorID) {
          $sOverPath = BX_DIRECTORY_PATH_ROOT . 'modules/andrew/tm4/images/' . $this->_iVisitorID . '/';
          if (! file_exists($sOverPath)) {
            //@mkdir($sOverPath); // windows
            @mkdir($sOverPath, 0777); // unix/linux
          }
        }

        // initialize editor
        $sInitEditor = $this->_replaceMarkers(self::$CONF_COMMON, array(
            'bx_var_custom_init' => $sToolsItems,
            'bx_var_plugins_path' => bx_js_string(BX_DOL_URL_PLUGINS, BX_ESCAPE_STR_APOS),
            'bx_var_css_path' => bx_js_string($this->_oTemplate->getCssUrl('editor.css'), BX_ESCAPE_STR_APOS),
            'bx_var_skin' => bx_js_string($this->_aObject['skin'], BX_ESCAPE_STR_APOS),
            'bx_var_lang' => bx_js_string($sLang, BX_ESCAPE_STR_APOS),
            'bx_var_selector' => bx_js_string($sSelector, BX_ESCAPE_STR_APOS),
            'bx_url_root' => bx_js_string(BX_DOL_URL_ROOT, BX_ESCAPE_STR_APOS),
            'external_filemanager_path' => bx_js_string(BX_DOL_URL_ROOT . 'modules/andrew/tm4/bin/plugins/rfilemanager/', BX_ESCAPE_STR_APOS),
            'external_filemanager_plugin_path' => bx_js_string('plugins/responsivefilemanager/plugin.min.js', BX_ESCAPE_STR_APOS),
        ));

        if ($bDynamicMode) {
            $aJs = array(BX_DOL_URL_ROOT . 'modules/andrew/tm4/bin/tinymce.min.js', BX_DOL_URL_ROOT . 'modules/andrew/tm4/bin/jquery.tinymce.min.js');
            $s = $this->_oTemplate->addJs($aJs, true);

            $sScript = $s . "
            <script>
                $(document).ready(function () {
                    $sInitEditor
                });
            </script>";

            /*$sScript = "<script>
                if ('undefined' == typeof(jQuery(document).tinymce)) {
                    $.getScript('" . bx_js_string(BX_DOL_URL_ROOT . 'modules/andrew/tm4/bin/jquery.tinymce.min.js', BX_ESCAPE_STR_APOS) . "', function(data, textStatus, jqxhr) {
                        $sInitEditor
                    });
                } else {
                    $sInitEditor
                }
            </script>";*/

        } else {
            $sScript = "
            <script>
                $(document).ready(function () {
                    $sInitEditor
                });
            </script>";

        }

        return $this->_addJsCss($bDynamicMode) . $sScript;
    }

    /**
     * Add css/js files which are needed for editor display and functionality.
     */
    protected function _addJsCss($bDynamicMode = false, $sInitEditor = '')
    {
        if ($bDynamicMode)
            return '';
        if ($this->_bJsCssAdded)
            return '';

        $aJs = array(BX_DOL_URL_ROOT . 'modules/andrew/tm4/bin/tinymce.min.js', BX_DOL_URL_ROOT . 'modules/andrew/tm4/bin/jquery.tinymce.min.js');

        $this->_oTemplate->addJs($aJs);

        if (isset($GLOBALS['oAdmTemplate']))
            $GLOBALS['oAdmTemplate']->addJs($aJs);

        $this->_bJsCssAdded = true;
        return '';
    }

}
