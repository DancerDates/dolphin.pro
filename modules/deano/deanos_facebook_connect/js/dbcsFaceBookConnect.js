/***************************************************************************
* Date				: Feb 21, 2013
* Copywrite			: (c) 2013 by Dean J. Bassett Jr.
* Website			: http://www.deanbassett.com
*
* Product Name		: Deanos Facebook Connect
* Product Version	: 4.2.6
*
* IMPORTANT: This is a commercial product made by Dean Bassett Jr.
* and cannot be modified other than personal use.
*  
* This product cannot be redistributed for free or a fee without written
* permission from Dean Bassett Jr.
*
***************************************************************************/

function join_form_click(popup) {
	if(popup == NULL) popup = true;
    var isChecked = $('#i_dbcs_facebook_connect_use_join_form:checked').val() ? true : false;
    if (isChecked) {
		if(popup) {
			var content = '<p>You have enabled the option to send Facebook Info to Dolphin Join Form. With this option on, some features of facebook connect can no longer be used. The following options have been disabled.</p><ul style="font-size:10pt;"><li>Default active status:</li><li>Default membership:</li><li>Auto prompt for nickname if free nick name was not found:</li><li>Auto prompt for email if email address from facebook is not valid:</li><li>Prompt for a password on join <b>as well as all other prompt options.</b></li><li>Comma delimited list of friends to automatically add to new account. (Leave empty to disable):</li><li>Auto friend any current members who are also friends with new member on facebook:</li><li>Set main Facebook photo as profile photo:</li><li>Import Facebook photo albums and photos:</li></ul><p>Click info icon for more information on this feature.</p>';
			var height = 380;
			var width = 600;
			var scroll = false;
			$.msgBox({
				title: "Information",
				content: content,
				type: "info",
				width: width,
				height: height,
				opacity: "0.6",
				//scroll: true,
				imagepath: site_url + "modules/deano/deanos_facebook_connect/templates/base/images/",
			});
		}
        $('#t_dbcs_facebook_connect_das').hide();
        $('#t_dbcs_facebook_connect_default_membership').hide();
        $('#t_dbcs_facebook_connect_auto_prompt_nick').hide();
        $('#t_dbcs_facebook_connect_auto_prompt_email').hide();
        $('#t_dbcs_facebook_connect_auto_prompt_dob').hide();
        $('#t_dbcs_facebook_connect_prompt_pass').hide();
        $('#t_dbcs_facebook_connect_prompt_nick').hide();
        $('#t_dbcs_facebook_connect_prompt_email').hide();
        $('#t_dbcs_facebook_connect_prompt_sex').hide();
        $('#t_dbcs_facebook_connect_prompt_country').hide();
        $('#t_dbcs_facebook_connect_prompt_city').hide();
        $('#t_dbcs_facebook_connect_prompt_zip').hide();
        $('#t_dbcs_facebook_connect_redirect1').hide();
        $('#t_dbcs_facebook_connect_autofriend_list').hide();
        $('#t_dbcs_facebook_connect_facebook_friends').hide();
        $('#t_dbcs_facebook_connect_copy_photo').hide();
        $('#t_dbcs_facebook_connect_import_albums').hide();
        $('#t_dbcs_facebook_connect_import_privacy').hide();

    } else {
        $('#t_dbcs_facebook_connect_das').show();
        $('#t_dbcs_facebook_connect_default_membership').show();
        $('#t_dbcs_facebook_connect_auto_prompt_nick').show();
        $('#t_dbcs_facebook_connect_auto_prompt_email').show();
        $('#t_dbcs_facebook_connect_auto_prompt_dob').show();
        $('#t_dbcs_facebook_connect_prompt_pass').show();
        $('#t_dbcs_facebook_connect_prompt_nick').show();
        $('#t_dbcs_facebook_connect_prompt_email').show();
        $('#t_dbcs_facebook_connect_prompt_sex').show();
        $('#t_dbcs_facebook_connect_prompt_country').show();
        $('#t_dbcs_facebook_connect_prompt_city').show();
        $('#t_dbcs_facebook_connect_prompt_zip').show();
        $('#t_dbcs_facebook_connect_redirect1').show();
        $('#t_dbcs_facebook_connect_autofriend_list').show();
        $('#t_dbcs_facebook_connect_facebook_friends').show();
        $('#t_dbcs_facebook_connect_copy_photo').show();
        $('#t_dbcs_facebook_connect_import_albums').show();
        $('#t_dbcs_facebook_connect_import_privacy').show();
    }

}

function hide_profile_type() {
	$('#t_dbcs_facebook_connect_prompt_profile_type').hide();
}

function use_popup_click(popup) {
	if(popup == NULL) popup = true;
    var isChecked = $('#i_dbcs_facebook_connect_use_popup:checked').val() ? true : false;
    if (isChecked) {
		var content = '<p>You have enabled the option to use the facebook popup login method.<br /><br />This method requires you be using the new version 4.2.3 button code.<br /><br />The option will not work if your using the older code. See Button Install Instructions.txt for the new code to use.</p>';
		var height = 250;
		var width = 480;
		var scroll = false;
		$.msgBox({
			title: "Information",
			content: content,
			type: "info",
			width: width,
			height: height,
			opacity: "0.6",
			//scroll: true,
			imagepath: site_url + "modules/deano/deanos_facebook_connect/templates/base/images/",
		});
	}
}

/*
function prompt_pass_click() {
    var isChecked = $('#i_dbcs_facebook_connect_prompt_pass:checked').val() ? true : false;
    if (isChecked) {
        $('#t_dbcs_facebook_connect_prompt_nick').show();
        $('#t_dbcs_facebook_connect_prompt_email').show();
    } else {
        $('#t_dbcs_facebook_connect_prompt_nick').hide();
        $('#t_dbcs_facebook_connect_prompt_email').hide();
    }
}
*/

function pophelp(section) {
    //alert(section);
    var content = '';
    var height = 160;
    var width = 450;
    var scroll = false;
    if (section == 'dbcs_facebook_connect_api_key') {
        content = 'Enter your Facebook Application ID in this field. Keys can be obtained at the facebook developers website. <a href="http://www.facebook.com/developers/">http://www.facebook.com/developers/</a>';
    }
    if (section == 'dbcs_facebook_connect_secret_key') {
        content = 'Enter your Facebook Application Secret Key in this field. Keys can be obtained at the facebook developers website. <a href="http://www.facebook.com/developers/">http://www.facebook.com/developers/</a>';
    }
    if (section == 'dbcs_facebook_connect_use_popup') {
        content = 'Check this box if you prefer to use the facebook popup logon box instead of the full page logon.<br /><br />NOTE: The popup option will not work if login is selected from within the dropdown auth type selector.';
        height = 230;
    }

    if ((section == 'dbcs_facebook_connect_option1') || (section == 'dbcs_facebook_connect_option2') || (section == 'dbcs_facebook_connect_option3') || (section == 'dbcs_facebook_connect_option4')) {
        content = '<div><ul><li>Use facebook field for logon(First) </li><li> Use facebook field for logon(Second)</li><li> Use facebook field for logon(Third)</li><li>Use facebook field for logon(Fourth)</li></ul>These 4 fields are processed in order. When a member joins, facebook connect will attempt to create an unused logon id. It will attempt to use an id created from the first field here first. If that is in use, the second one is tried, and if that fails the third. If all 3 combinations are in use, facebook connect will fail to create an account.</div>';
        width = 500;
        height = 320;
    }
    if (section == 'dbcs_facebook_connect_allow_spaces') {
        content = 'This option will tell facebook connect to allow the use of spaces in nicknames. Dolphin must be modified to allow the use of spaces in nicknames.<br><br><b>DO NOT ENABLE THIS OPTION IF YOU HAVE NOT MADE ANY SUCH MODIFICATIONS.<br><br></b>Also do not ask me how to make the needed modifications to dolphin. I do not know how to do it. This feature was added per request by those who have made the changes. Everyone else is to leave this option off.';
        width = 500;
        height = 320;
    }
    if (section == 'dbcs_facebook_connect_nag_time') {
		content = 'This feature informs your members is any required profile information is missing. It does this every number of hours set in this field.<br><br>You may put 0 in this field to diable. Thats the number Zero.';
        height = 200;
    }
    if (section == 'dbcs_facebook_connect_redirect2') {
        content = 'Here you can set the page you want facebook connect to redirect the member to after connecting via facebook connect.<br><br>Note. This is not used for first time signup.';
        height = 180;
    }
    if (section == 'dbcs_facebook_connect_logout_redirect') {
        content = 'Here you can set the page you want facebook connect to redirect the member to after logging out of the site.';
        height = 150;
    }
    if (section == 'dbcs_facebook_connect_unregister_redirect') {
        content = 'Here you can set the page you want facebook connect to redirect the member to when they unregister/delete their account.';
        height = 150;
    }
    if (section == 'dbcs_facebook_connect_fb_logout') {
        content = 'With this option on, if member connected to your site using facebook connect, then when logging out of the site they will also be logged out of facebook.';
        height = 180;
    }
    if (section == 'dbcs_facebook_connect_match_email') {
        content = 'If this option is on, then facebook connect on first time signup will attempt to match the email address it obtained from facebook to a dolphin account with the same email address.<br><br>If one is found, facebook connect will assume this is the proper account and will connect the user to it instead of creating a new account.';
        height = 240;

    }
    if (section == 'dbcs_facebook_connect_set_status_active_oc') {
        content = 'This option allows you to force unconfirmed accounts that either joined via facebook connect or had previously joined with facebook connect to an active state.<br><br>This feature was added due to problems with the free Deny Unconfirmed mod by ilbellodelweb which is not compatiable with this facebook connect module.';
        height = 260;

    }
    if (section == 'dbcs_facebook_connect_use_join_form') {
        content = 'If this option is on, then all needed information is gathered from facebook to prefill the dolphin join form. Then passes control back to dolphin.<br><br>If this option is used, the rest of the facebook connect features below this one will not be used. The signup process is handled by dolphin and will work the same way as normal signups.<br><br><b>NOTE:</b> This feature is not avilable in dolphin 7.0.0<br><br><b>Also NOTE:</b> The module installs a Facebook ID field in the dolphin join form. That field must remain there if this option is to be used.';
        width = 500;
        height = 350;
	}
    if (section == 'dbcs_facebook_connect_das') {
		content = 'This option allows you to override dolphins current moderation settings for those joining with facebook connect.';
    }
    if (section == 'dbcs_facebook_connect_default_membership') {
		content = 'This option allows you to override dolphins membership assignment to new account. Here you can choose what membership level to set accounts to that join via facebook connect.';
    }
    if (section == 'dbcs_facebook_connect_auto_prompt_nick') {
		content = 'With this option on, facebook connect will automatically prompt for a nickname if all 3 of the auto generated ones are in use. If this option is off and the option Prompt for a nickname on join is also off then facebook connect will abort the signup with the reason nick name is in use.';
        height = 240;
    }
    if (section == 'dbcs_facebook_connect_auto_prompt_email') {
		content = 'With this option on, facebook connect will automatically prompt for a email address if the one provided by facebook is invalid. This is common for facebook mobile accounts. If this option is off and the option Prompt for a email on join is also off then facebook connect will abort the signup with the reason email address not valid.';
        height = 250;
	}
    if (section == 'dbcs_facebook_connect_auto_prompt_dob') {
		content = 'With this option on, facebook connect will automatically prompt for a date of birth if the one provided by facebook is invalid. This could occure if permissions for birth date was not granted. If this option is off and the option Prompt for a date of birth on join is also off then facebook connect will default to today minus the sites min age setting.';
        height = 250;
	}

    if (section == 'dbcs_facebook_connect_prompt_profile_type') {
		content = 'This option is only available if the Profile Types Splitter module from AQB Soft is installed.<br /><br />If so, check this option to prompt new member for a profile type.';
        height = 190;
    }

    if (section == 'dbcs_facebook_connect_prompt_pass') {
		content = 'If this option is on, then the joining member will be prompted for a password upon joining the site.<br><br>Facebook connect normally randomly generates a password.';
        height = 200;
    }
    if (section == 'dbcs_facebook_connect_prompt_nick') {
		content = 'If this option is on, then the joining member will be prompted to enter a new nickname.<br><br>Facebook connect normally generates a nick name based on above nickname settings.';
        height = 180;
    }
    if (section == 'dbcs_facebook_connect_prompt_email') {
		content = 'If this option is on, then the joining member will be prompted to enter a new email address.<br><br>Facebook connect normally uses the email address it obtains from facebook.';
        height = 180;
    }
    if (section == 'dbcs_facebook_connect_prompt_sex') {
		content = 'If this option is on, then the joining member will be prompted to select their Sex/Gender.';
    }

    if (section == 'dbcs_facebook_connect_prompt_country') {
		content = 'If this option is on, then the joining member will be prompted to select their country.';
    }
    if (section == 'dbcs_facebook_connect_prompt_city') {
		content = 'If this option is on, then the joining member will be prompted to enter their city.';
    }
    if (section == 'dbcs_facebook_connect_prompt_zip') {
		content = 'If this option is on, then the joining member will be prompted to enter their zip code.';
    }
    if (section == 'dbcs_facebook_connect_redirect1') {
		content = 'This field can contain the name of the page you want the member redirected to when the join process is complete.';
    }
    if (section == 'dbcs_facebook_connect_autofriend_list') {
		content = 'Here you can enter dolphin member id\'s of members you want to automatically be added as friends of the new member during the join process.<br><br>The field must contain member ID numbers, not nicknames.<br><br>More than one can be specified by seperating them with a comma.';
        width = 500;
		height = 250;
    }
    if (section == 'dbcs_facebook_connect_facebook_friends') {
		content = 'If this option is on, then the new joining member will be added as a friend to all existing dolphin members who are also friends with the new member on facebook.<br><br>The existing members had to have origionally signed up using facebook connect for this feature to work.';
        height = 240;
    }
    if (section == 'dbcs_facebook_connect_copy_photo') {
		content = 'If this option is on, the the primary photo on facebook for the joining member will be set as the profile photo in the new dolphin account.';
    }
    if (section == 'dbcs_facebook_connect_import_albums') {
		content = 'If this option is on, then new joining member will be prompted to choose if they want to import their facebook photo albums to the new dolphin account.';
    }
    if (section == 'dbcs_facebook_connect_use_geo_ip') {
		content = 'If this option is on, facebook connect will attempt to determine the country using geoip lookup of joining IP address.';
    }
    if (section == 'dbcs_facebook_connect_dcnty') {
		content = 'The default country code to use if geoip lookup is off.';
    }
    if (section == 'dbcs_facebook_connect_import_privacy') {
		content = 'This option determines how privacy of imported albums will be handled.';
    }

    if (section == 'dbcs_facebook_connect_show_email') {
		content = 'This option allows you to show the email address as the login id instead of the nickname on the finish page and welcome email.';
    }

    if (content == '') {
        $.msgBox({
            title: "Information",
            content: 'Test Content - ' + section,
            type: "info",
            width: width,
            height: height,
            opacity: "0.6",
            scroll: scroll,
            imagepath: site_url + "modules/deano/deanos_facebook_connect/templates/base/images/",
        });
    } else {
		$.msgBox({
			title: "Information",
			content: content,
			type: "info",
			width: width,
			height: height,
			opacity: "0.6",
			//scroll: true,
			imagepath: site_url + "modules/deano/deanos_facebook_connect/templates/base/images/",
		});
	}
    return;
}

function validatePromptForm(form) {
	//$('#error_NickName').hide();
	//$('#error_Password').hide();
	//$('#error_Password_confirm').hide();
	//$('#error_Email').hide();
	//$('#error_Sex').hide();
	//$('#error_Country').hide();
	//$('#error_City').hide();
	//$('#error_zip').hide();

	$('#i_NickName').hide();
	$('#i_Password').hide();
	$('#i_Password_confirm').hide();
	$('#i_Email').hide();
	$('#i_Sex').hide();
    $('#i_DateOfBirth').hide();
	$('#i_Country').hide();
	$('#i_City').hide();
	$('#i_zip').hide();

	$.post(site_url + "modules/?r=deanos_facebook_connect/check_form_errors/", $(form).serialize(), function(data) {
		//alert("Data Loaded: " + data);
		//var s = $.parseJSON(data);
		// The above jquery call does not work on dolphin 7.0.9 and under becuse parseJSON is not available in
		// those versions of jquery. Replaced with the line below instead.
		var s = eval("(" + data + ")"); 
		if(s.Errors == true) {
			//alert('Errors');
			if(s.NickName != null) {
				$('#i_NickName').attr('float_info',s.NickName);
				$('#i_NickName').show();
				//$('#error_NickName').html(s.NickName);
				//$('#error_NickName').show();
			}
			if(s.Password != null) {
				$('#i_Password').attr('float_info',s.Password);
				$('#i_Password').show();
			}
			if(s.Password_confirm != null) {
				$('#i_Password_confirm').attr('float_info',s.Password_confirm);
				$('#i_Password_confirm').show();
			}
			if(s.Email != null) {
				$('#i_Email').attr('float_info',s.Email);
				$('#i_Email').show();
			}

			if(s.Sex != null) {
				$('#i_Sex').attr('float_info',s.Sex);
				$('#i_Sex').show();
			}

            if(s.DateOfBirth != null) {
                $('#i_DateOfBirth').attr('float_info',s.DateOfBirth);
                $('#i_DateOfBirth').show();
            }

			if(s.Country != null) {
				$('#i_Country').attr('float_info',s.Country);
				$('#i_Country').show();
			}
			if(s.City != null) {
				$('#i_City').attr('float_info',s.City);
				$('#i_City').show();
			}
			if(s.zip != null) {
				$('#i_zip').attr('float_info',s.zip);
				$('#i_zip').show();
			}

		} else {
			form.submit();
		}
	});
	return false;
}


/*

This version heavily modified by Dean Bassett, 2013 
Changes to allow additional params such as height, width, scrolling, 
and a per message box path for images rather than the global setting.
Also changed content area from pargraph and span tags to a div with margins.
The div handles the html content i am using much better.

jQuery.msgBox plugin 
Copyright 2011, Halil İbrahim Kalyoncu
License: BSD
modified by Oliver Kopp, 2012.
 * added support for configurable image paths
 * a new msgBox can be shown within an existing msgBox
*/

/*
contact :

halil@ibrahimkalyoncu.com
koppdev@googlemail.com

*/

// users may change this variable to fit their needs
//var msgBoxImagePath = "../images/";

jQuery.msgBox = msg;
function msg(options) {
    var isShown = false;
    var typeOfValue = typeof options;
    var defaults = {
        content: (typeOfValue == "string" ? options : "Message"),
        title: "Warning",
        type: "alert",
        autoClose: false,
        timeOut: 0,
        showButtons: true,
        buttons: [{ value: "Ok" }],
        inputs: [{ type: "text", name: "userName", header: "User Name" }, { type: "password", name: "password", header: "Password" }],
        success: function (result) { },
        beforeShow: function () { },
        afterShow: function () { },
        beforeClose: function () { },
        afterClose: function () { },
        opacity: 0.1
    };
    options = typeOfValue == "string" ? defaults : options;
    if (options.type != null) {
        switch (options.type) {
            case "alert":
                options.title = options.title == null ? "Warning" : options.title;
                break;
            case "info":
                options.title = options.title == null ? "Information" : options.title;
                break;
            case "error":
                options.title = options.title == null ? "Error" : options.title;
                break;
            case "confirm":
                options.title = options.title == null ? "Confirmation" : options.title;
                options.buttons = options.buttons == null ? [{ value: "Yes" }, { value: "No" }, { value: "Cancel" }] : options.buttons;
                break;
            case "prompt":
                options.title = options.title == null ? "Log In" : options.title;
                options.buttons = options.buttons == null ? [{ value: "Login" }, { value: "Cancel" }] : options.buttons;
                break;
            default:
                image = "alert.png";
        }
    }
    options.timeOut = options.timeOut == null ? (options.content == null ? 500 : options.content.length * 70) : options.timeOut;
    options = $.extend(defaults, options);
    if (options.autoClose) {
        setTimeout(hide, options.timeOut);
    }
    var image = "";
    switch (options.type) {
        case "alert":
            image = "alert.png";
            break;
        case "info":
            image = "info.png";
            break;
        case "error":
            image = "error.png";
            break;
        case "confirm":
            image = "confirm.png";
            break;
        default:
            image = "alert.png";
    }

    var divId = "msgBox" + new Date().getTime();

    var divMsgBoxId = divId;
    var divMsgBoxContentId = divId + "Content";
    var divMsgBoxImageId = divId + "Image";
    var divMsgBoxButtonsId = divId + "Buttons";
    var divMsgBoxBackGroundId = divId + "BackGround";

    var msgBoxImagePath = options.imagepath;

    var buttons = "";
    $(options.buttons).each(function (index, button) {
        buttons += "<input class=\"msgButton\" type=\"button\" name=\"" + button.value + "\" value=\"" + button.value + "\" />";
    });

    var inputs = "";
    $(options.inputs).each(function (index, input) {
        var type = input.type;
        if (type == "checkbox" || type == "radiobutton") {
            inputs += "<div class=\"msgInput\">" +
            "<input type=\"" + input.type + "\" name=\"" + input.name + "\" " + (input.checked == null ? "" : "checked ='" + input.checked + "'") + " value=\"" + (typeof input.value == "undefined" ? "" : input.value) + "\" />" +
            "<text>" + input.header + "</text>" +
            "</div>";
        }
        else {
            inputs += "<div class=\"msgInput\">" +
            "<span class=\"msgInputHeader\">" + input.header + "<span>" +
            "<input type=\"" + input.type + "\" name=\"" + input.name + "\" value=\"" + (typeof input.value == "undefined" ? "" : input.value) + "\" />" +
            "</div>";
        }
    });

    var divBackGround = "<div id=" + divMsgBoxBackGroundId + " class=\"msgBoxBackGround\"></div>";
    var divTitle = "<div class=\"msgBoxTitle\">" + options.title + "</div>";
    var divContainer = "<div class=\"msgBoxContainer\"><div id=" + divMsgBoxImageId + " class=\"msgBoxImage\"><img src=\"" + msgBoxImagePath + image + "\"/></div><div id=" + divMsgBoxContentId + " class=\"msgBoxContent\" style=\"overflow-y: " + (options.scroll ? "scroll" : "hidden") + "; width: " + (options.width - 94) + "px;height: " + (options.height - 70) + "px;\"><div style=\"margin-top: 8px; margin-bottom: 8px;\">" + options.content + "</div></div></div>";
    var divButtons = "<div id=" + divMsgBoxButtonsId + " class=\"msgBoxButtons\">" + buttons + "</div>";
    var divInputs = "<div class=\"msgBoxInputs\">" + inputs + "</div>";

    var divMsgBox;
    var divMsgBoxContent;
    var divMsgBoxImage;
    var divMsgBoxButtons;
    var divMsgBoxBackGround;

    if (options.type == "prompt") {
        $("html").append(divBackGround + "<div id=" + divMsgBoxId + " class=\"msgBox\" style=\"width: " + options.width + "px;\">" + divTitle + "<div>" + divContainer + (options.showButtons ? divButtons + "</div>" : "</div>") + "</div>");
        divMsgBox = $("#" + divMsgBoxId);
        divMsgBoxContent = $("#" + divMsgBoxContentId);
        divMsgBoxImage = $("#" + divMsgBoxImageId);
        divMsgBoxButtons = $("#" + divMsgBoxButtonsId);
        divMsgBoxBackGround = $("#" + divMsgBoxBackGroundId);

        divMsgBoxImage.remove();
        divMsgBoxButtons.css({ "text-align": "center", "margin-top": "5px" });
        divMsgBoxContent.css({ "width": "100%", "height": "100%" });
        divMsgBoxContent.html(divInputs);
    }
    else {
        $("html").append(divBackGround + "<div id=" + divMsgBoxId + " class=\"msgBox\" style=\"width: " + options.width + "px;\">" + divTitle + "<div>" + divContainer + (options.showButtons ? divButtons + "</div>" : "</div>") + "</div>");
        divMsgBox = $("#" + divMsgBoxId);
        divMsgBoxContent = $("#" + divMsgBoxContentId);
        divMsgBoxImage = $("#" + divMsgBoxImageId);
        divMsgBoxButtons = $("#" + divMsgBoxButtonsId);
        divMsgBoxBackGround = $("#" + divMsgBoxBackGroundId);
    }

    //var width = divMsgBox.width();
    var width = options.width;

    var height = divMsgBox.height();
    var windowHeight = $(window).height();
    var windowWidth = $(window).width();

    var top = windowHeight / 2 - height / 2;
    var left = windowWidth / 2 - width / 2;

    show();

    function show() {
        if (isShown) {
            return;
        }
        divMsgBox.css({ opacity: 0, top: top - 50, left: left });
        divMsgBox.css("background-image", "url('" + msgBoxImagePath + "msgBoxBackGround.png')");
        divMsgBoxBackGround.css({ opacity: options.opacity });
        options.beforeShow();
        divMsgBoxBackGround.css({ "width": $(document).width(), "height": getDocHeight() });
        $(divMsgBoxId + "," + divMsgBoxBackGroundId).fadeIn(0);
        divMsgBox.animate({ opacity: 1, "top": top, "left": left }, 200);
        setTimeout(options.afterShow, 200);
        isShown = true;
        $(window).bind("resize", function (e) {
            //var width = divMsgBox.width();
            var width = options.width;

            var height = divMsgBox.height();
            var windowHeight = $(window).height();
            var windowWidth = $(window).width();

            var top = windowHeight / 2 - height / 2;
            var left = windowWidth / 2 - width / 2;

            divMsgBox.css({ "top": top, "left": left });
        });
    }

    function hide() {
        if (!isShown) {
            return;
        }
        options.beforeClose();
        divMsgBox.animate({ opacity: 0, "top": top - 50, "left": left }, 200);
        divMsgBoxBackGround.fadeOut(300);
        setTimeout(function () { divMsgBox.remove(); divMsgBoxBackGround.remove(); }, 300);
        setTimeout(options.afterClose, 300);
        isShown = false;
    }

    function getDocHeight() {
        var D = document;
        return Math.max(
        Math.max(D.body.scrollHeight, D.documentElement.scrollHeight),
        Math.max(D.body.offsetHeight, D.documentElement.offsetHeight),
        Math.max(D.body.clientHeight, D.documentElement.clientHeight));
    }

    function getFocus() {
        divMsgBox.fadeOut(200).fadeIn(200);
    }

    $("input.msgButton").click(function (e) {
        e.preventDefault();
        var value = $(this).val();
        if (options.type != "prompt") {
            options.success(value);
        }
        else {
            var inputValues = [];
            $("div.msgInput input").each(function (index, domEle) {
                var name = $(this).attr("name");
                var value = $(this).val();
                var type = $(this).attr("type");
                if (type == "checkbox" || type == "radiobutton") {
                    inputValues.push({ name: name, value: value, checked: $(this).attr("checked") });
                }
                else {
                    inputValues.push({ name: name, value: value });
                }
            });
            options.success(value, inputValues);
        }
        hide();
    });

    divMsgBoxBackGround.click(function (e) {
        if (!options.showButtons || options.autoClose) {
            hide();
        }
        else {
            getFocus();
        }
    });
};
