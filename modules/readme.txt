/*************************Product owner info********************************
*
*     author               : AndrewP
*     contactinfo          : Prikaznov Andrey. (email: aramisgc@gmail.com)   
*
/*************************Product info**************************************
*
*                          TinyMCE4
*                          -----------------
*     version              : 2.0.1
*     compability          : Dolphin 7.1.x
*     License type
*
* IMPORTANT: This is a commercial product made by AndrewP. It cannot be modified for other than personal usage.
* The "personal usage" means the product can be installed and set up for ONE domain name ONLY.
* To be able to use this product for another domain names you have to order another copy of this product (license).
* This product cannot be redistributed for free or a fee without written permission from AndrewP.
* This notice may not be removed from the source code.
*
*     Upgrade possibilities : All possible upgrades will be added into the same product
*
***************************************************************************/

============================== INSTALLATION ==============================

1) Unzip the file
2) Upload directory "andrew" inside the folder "/modules" on your site via FTP.
3) In the Dolphin administration go to MODULES>> Add & Manage 
4) Among the modules are not installed, search for and select "TinyMCE4 version 2.0.1 by AndrewP" and then click "Install"

Now we need to modify two dolphin files. Pay attention, that already changed versions of both files are in our package (in necessary folders).
  Thus you may consider to merge files instead of making changes manually.

5) With Notepad++ or any other text editor (Zend as example), open the file 'administration/templates/base/splash.html'

  Search for (line 8):

        if($(oCheckbox).is(':checked'))
        	tinyMCE.execCommand('mceAddControl', false, sEditorId);
        else
        	tinyMCE.execCommand('mceRemoveControl', false, sEditorId);

  Change it with:

        if($(oCheckbox).is(':checked'))
        	tinyMCE.execCommand('mceAddEditor', false, sEditorId);
        else
        	tinyMCE.execCommand('mceRemoveEditor', false, sEditorId);

  Save the file.

6) With Notepad++ or any other text editor (Zend as example), open the file 'templates/base/mail_box_view_message.html'

  Search for (line 119):

    tinyMCE.execCommand('mceAddControl', false, htmlSelectors[0]);

  Change it with:

    tinyMCE.execCommand('mceAddEditor', false, htmlSelectors[0]);

  Save the file.

7) With Notepad++ or any other text editor (Zend as example), open the file 'inc/js/mail_box.js'

  Search for (line 214):

			var ed = tinyMCE.get(htmlSelectors[0]);

  Change it with:

			var ed = tinyMCE.activeEditor;

  Search for (line 264):

                    tinyMCE.execCommand('mceRemoveControl', false, htmlSelectors[0]);

  Change it with:

                    tinyMCE.execCommand('mceRemoveEditor', false, htmlSelectors[0]);

  Save the file.

8) With Notepad++ or any other text editor (Zend as example), open the file 'inc/js/jquery.webForms.js'

  Search for (line 265):

                tinyMCE.execCommand('mceRemoveControl', false, this.id);

  Change it with:

                tinyMCE.execCommand('mceRemoveEditor', false, this.id);

  Search for (line 267):

                tinyMCE.execCommand('mceAddControl', false, this.id);

  Change it with:

                tinyMCE.execCommand('mceAddEditor', false, this.id);

  Save the file.

INSTALLATION COMPLETE!

===================== BOONEX FORUM MODULE CHANGES ======================

1) Six files of the boonex forum module were modified accordingly the new version of TinyMCE:

modules\boonex\forum\classes\Forum.php
modules\boonex\forum\js\BxForum.js
modules\boonex\forum\layout\base\xsl\canvas_includes.xsl
modules\boonex\forum\layout\base\xsl\edit_post.xsl
modules\boonex\forum\layout\base\xsl\new_topic.xsl
modules\boonex\forum\layout\base\xsl\post_reply.xsl

In case if you've never edited these files, you may consider to make backup of your files (listed above) and upload our files

2) Goto the 'Manage forum' page : forum/?action=goto&manage_forum=1

  Here you need to click all 'Compile Language' links in the block's header menu

3) Clean all dolphin caches : administration/cache.php?mode=clear

======== IMPORTANT NOTES (if your website's version is 7.1.0) ==========

You may notice, that it couldn't work with the admin's page builder. In order to solve this situation, read the following instruction:

Due to the fact that your dolphin version is outdated, the new tinyMCE doesn't work 100% properly with the page builder.
The easiest way is to upgrade your dolphin till the latest version (7.1.4). In case if your dolphin is heavily modified, you may consider 'upgrading' several files of your dolphin installation:

1) 'administration\pageBuilder.php':
  You need to add the following line below the copyright comments:

  define('BX_PAGE_BUILDER', true);

  Save the file.

2) 'inc\classes\BxDolTemplate.php'. Search for :
      $this->addLocationJs('system_plugins_jquery', BX_DIRECTORY_PATH_PLUGINS . 'jquery/' , BX_DOL_URL_PLUGINS . 'jquery/');

  You need to replace it with:

        if (defined('BX_PAGE_BUILDER')) {
          $this->addLocationJs('system_plugins_jquery', BX_DIRECTORY_PATH_PLUGINS . 'jquery_714/' , BX_DOL_URL_PLUGINS . 'jquery_714/');
        } else {
          $this->addLocationJs('system_plugins_jquery', BX_DIRECTORY_PATH_PLUGINS . 'jquery/' , BX_DOL_URL_PLUGINS . 'jquery/');
        }

  Save the file.

3) 'inc\admin_design.inc.php '. Search for :
      'jquery.js',

  You need to add two new lines below it:

    'jquery-migrate.min.js',
    'jquery.ui.position.min.js',

  Save the file.

4) Now you need to download the latest dolphin package (v 7.1.4) to your computer. Download it and extract it in a particular directory

5) You need to refresh the 'inc\js\classes\BxDolPageBuilder.js' file with the newest version (that you can find in the dolphin 7.1.4)

6) Find 'plugins\jquery\' directory in the dophin 7.1.4, and upload this directory with the 'jquery_714' name, so the new jQuery should be accessible by 'plugins\jquery_714\'

That's it.

============================== UNINSTALL ===============================

1. Goto your admin panel to page: administration/modules.php
    Choose installed module 'TinyMCE4 version 2.0.1 by AndrewP' and click uninstall button

2. Roll back the changes you did during the installation (see steps 5 and 6).

3. Uninstall finished.

=========================================================================================

 Best Regards
 AndrewP
 You are welcome to view and test my products here at:
 http://www.boonex.com/market/posts/AndrewP
 http://www.script-tutorials.com/  |  http://www.demozzz.ru/

 REPORTING FOR ANY BUGS, PROBLEMS WITH INSTALLATION, OR CUSTOMIZATIONS contact us at aramisgc@gmail.com