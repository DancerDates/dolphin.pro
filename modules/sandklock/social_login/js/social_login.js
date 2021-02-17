function showSocialLoginLoadingImg() {
    var oPopupOptions = {
            closeOnOuterClick: false
        };
        
    showPopupAnyHtml(site_url + 'm/social_login/show_loading_img', oPopupOptions);
}

function showPopupLoginForm(iActiveTab){
    var sPopupId = 'login_div';
	var oPopupOptions = {};

	var sContentId = 'sys-form-login-join';

	if(!iActiveTab)
		iActiveTab = 0;

    if ($('#' + sPopupId).length && $('#' + sPopupId + ' #tabs-login').length && $('#' + sPopupId + ' #tabs-join').length) {
    	$("#" + sContentId).tabs({active: iActiveTab});

        $('#' + sPopupId).dolPopup(oPopupOptions);
    }
    else {
        if($('#' + sPopupId).length)
            $('#' + sPopupId).remove();
        $('<div id="' + sPopupId + '" style="display:none;"></div>').appendTo('body').load(
            site_url + 'm/social_login/popup_login_form',
            {
                action: 'show_login_form',
                relocate: String(window.location)
            },
            function() {
        		$("#" + sContentId + " ul.sys-flj-navigation li a").each(function() {
        		    jQuery(this).attr("href", location.href.toString()+jQuery(this).attr("href"));
        		});
        		$("#" + sContentId).tabs({active: iActiveTab}).addWebForms();
        		
                $(this).dolPopup(oPopupOptions);
            }
        );
    }
}
	

function showSocialLoginForm(username,email){
    $('#login_div').dolPopupHide();
    $('#sys_popup_ajax').dolPopupHide();
        
	var oPopupOptions = {
            closeOnOuterClick: false,
            onShow: function () {                
    			var timer = setInterval(function(){
    				if($('#sk_captcha_container').length > 0) {
    					if($('#recaptcha_image').html().length > 0) {
    						var top = $(window).height()/2 - $('#login_div').height()/2;
    						$('#login_div').animate({'top' : top},'slow');
    						clearInterval(timer);
    					}
    				} else {
    					var top = $(window).height()/2 - $('#login_div').height()/2;
    					$('#login_div').animate({'top' : top},'slow');
    					clearInterval(timer);
    				}
    			},200);
            }
		};
        
    showPopupAnyHtml(site_url + 'm/social_login/gen_form?username=' + username + '&relocate=' + String(window.location) + '&email=' + email, oPopupOptions);
}

function showSocialLoginMes(mess){
	var oPopupOptions = {
           onShow: function () {
    			var timer = setTimeout(function(){
    				$('#login_div').dolPopupHide(oPopupOptions);
                    $('#sys_popup_ajax').dolPopupHide(oPopupOptions);
    				window.location = window.location.href;
    				clearTimeout(timer);
    			},2500);
           }
    };
    
    showPopupAnyHtml(site_url + 'm/social_login/gen_error?message=' + encodeURIComponent(mess), oPopupOptions);
}
	
function showSocialLoginError(mess,capt){
	var oPopupOptions = {};
        
    showPopupAnyHtml(site_url + 'm/social_login/gen_error?message=' + encodeURIComponent(mess) + '&caption=' + capt, oPopupOptions);
}
	
function socialLoginCheckInfo() {
	$(document).ready(function() {
		var url = site_url + '/m/social_login/check_info';
		var e = $('#sk_login_form #email').val();
		var m = $('#sk_login_form input[name=\'opt\']:eq(0)').prop('checked') == true ? 1 : 0;
		var p = $('#sk_login_form #pwd').val();
		var u = $('#sk_login_form #username').val();
		var res = $('#recaptcha_response_field').val();
		var challenge = $('#recaptcha_challenge_field').val();
		var data = {
			username: u,
			email: e,
			password: p,
			map: m,
			recaptcha_response_field: res,
			recaptcha_challenge_field: challenge
		};
		$.post(url, data, function(response, status) {
			if (status == 'success') {
				socialLoginRemoveError();
				var flag = true;
				if (response.username) {
				    var username_ele = $('#sk_login_form #username');
					if (response.username.error == 'existed') {
						$('#sk_login_form input[name=\'opt\']:eq(0)').parent().attr('onclick', 'socialLoginDisplayInput(\'username\')');
                        socialLoginShowError(username_ele, response.username.mess);
						$('#sk_mapping_info p:eq(0) span').html(response.username.label);
						$('#sk_login_form div.bx-form-element:eq(0)').show();
						$('#sk_login_form div.bx-form-element:eq(1)').show();
						flag = false;
					}
					if (response.username.error == 'incorrect') {
						$('#sk_login_form input[name=\'opt\']:eq(0)').parent().attr('onclick', 'socialLoginDisplayInput(\'not_existed\')');
						socialLoginShowError(username_ele, response.username.mess);
						flag = false;
					}
				}
				if (response.email) {
				    var email_ele = $('#sk_login_form #email');
					if (response.email.error == 'existed') {
						$('#sk_login_form input[name=\'opt\']:eq(0)').parent().attr('onclick', 'socialLoginDisplayInput(\'email\')');
                        socialLoginShowError(email_ele, response.email.mess);
						$('#sk_mapping_info p:eq(0) span').html(response.email.label);
						$('#sk_login_form div.bx-form-element:eq(0)').show();
						$('#sk_login_form div.bx-form-element:eq(1)').show();
						flag = false;
					}
					if (response.email.error == 'incorrect') {
						$('#sk_login_form input[name=\'opt\']:eq(0)').parent().attr('onclick', 'socialLoginDisplayInput(\'not_existed\')');
						socialLoginShowError(email_ele, response.email.mess);
						flag = false;
					}
				}
				if (response.password) {
					if (response.password.error == 'incorrect') {
                        var pwd_ele = $('#sk_login_form #pwd');
                        socialLoginShowError(pwd_ele, response.password.mess);
						flag = false;
					}
				}
				if (response.captcha) {
					if (response.captcha.error != 'valid') {
                        var captcha_ele = $('#sk_captcha_container');
                        socialLoginShowError(captcha_ele, response.captcha.mess);
						captcha_ele.html(response.captcha.error);
						flag = false;
					}
				}
				if ($('#sk_login_form div.bx-form-element:eq(0)').is(':visible')) $('#sk_guide').hide();
				else
				$('#sk_guide').show();
				if (flag) {
					var url = site_url + '/m/social_login/add_info';
					var data = {
						opt: $('#sk_login_form input[name=\'opt\']:eq(0)').prop('checked') == true  ? 'map' : 'new',
						username: $('#sk_login_form #username').val(),
						email: $('#sk_login_form #email').val()
					};
                    
                    showSocialLoginLoadingImg();
                    
					$.post(url, data, function(response, status) {
						if (status == 'success') {
							if (response.error) showSocialLoginError(response.error, 'error');
							else {
								showSocialLoginError(response, 'message');
								var timer = setTimeout(function() {
									window.location = window.location.href;
									clearTimeout(timer);
								}, 2500);
							}
						}
					});
				}
			} else {
				alert('An error occured, we are sorry');
			}
		}, "json");
	});
}

function socialLoginDisplayInput(is_existed) {
	var username_input = $('#sk_login_form div.bx-form-element:eq(2)');
	var email_input = $('#sk_login_form div.bx-form-element:eq(3)');
	var pwd_input = $('#sk_login_form div.bx-form-element:eq(4)');
	if ($('#sk_login_form input[name=\'opt\']:eq(0)').prop('checked') == true) {
		pwd_input.show();
		$('#sk_captcha_container').parent().parent().parent().show();
		if (is_existed == 'email') {
			email_input.show();
			username_input.hide();
		} else if (is_existed == 'username') {
			email_input.hide();
			username_input.show();
		} else {
			if ($('#sk_login_form #email').val()) {
				email_input.show();
				username_input.hide();
			} else {
				if ($('#sk_login_form #username').val()) {
					email_input.hide();
					username_input.show();
				}
			}
		}
	} else {
		if ($('#sk_captcha_container').length > 0) $('#sk_captcha_container').parent().parent().parent().hide();
		pwd_input.hide();
		username_input.show();
		email_input.show();
	}
    
    socialLoginRemoveErrorField($('#sk_login_form #username'));
	socialLoginRemoveErrorField($('#sk_login_form #email'));
    
	if ($('#recaptcha_image').html().length > 0) {
		var top = $(window).height() / 2 - $('#login_div').height() / 2;
		$('#login_div').animate({
			'top': top
		}, 'slow');
	}
}

function socialLoginRemoveError() {
	socialLoginRemoveErrorField($('#sk_login_form #username'));
	socialLoginRemoveErrorField($('#sk_login_form #email'));
	socialLoginRemoveErrorField($('#sk_login_form #pwd'));
	socialLoginRemoveErrorField($('#sk_captcha_container'));
}

function socialLoginClosePopup() {
    var oPopupOptions = {
            closeOnOuterClick: false
        };
    $('#login_div').dolPopupHide(oPopupOptions);
    $('#sys_popup_ajax').dolPopupHide(oPopupOptions);
}

function socialLoginShowError(field_ele, error_text){
    field_ele.parents('.bx-form-element:first').addClass('bx-form-element-error').find('.bx-form-error > [float_info]').attr('float_info', error_text).parent().show();
}

function socialLoginRemoveErrorField(field_ele){
    field_ele.parents('.bx-form-element:first').removeClass('bx-form-element-error').find('.bx-form-error').hide();
}