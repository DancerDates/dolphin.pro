function showSocialInviterLoadingImg(url) {
    if($('#sk_inviter_loading_img').length)
        $('#sk_inviter_loading_img').remove();
        
    var oPopupOptions = {closeOnOuterClick: false};
    
    $('<div id="sk_inviter_loading_img" style="display: none;"><img src="'+url+'"/></div>').prependTo('body');
		$('#sk_inviter_loading_img').dolPopup(oPopupOptions);
}

function showSocialInviterBlock(friends){
	var oPopupOptions = {closeOnOuterClick: false};
	
	if ($('#sk_friend_block').length){
		$('#sk_friend_block').remove();
	}
	$('<div id="sk_friend_block" style="display: none;"></div>').prependTo('body').load(
		site_url + 'm/social_inviter/ajax_mode/friend_list_block',
		{
			ajaxmode: 'true',
			list: friends,
			relocate: String(window.location),
		},
		function() {
			$(this).dolPopup(oPopupOptions);
			if($(this).is(':visible'))
				setTimeout(function(){$('#sk_inviter_loading_img').hide();},200);
		});
}

function showSocialInviterImport(){
    showSocialInviterLoadingImg(site_url + 'templates/base/images/loading.gif');
    
	var oPopupOptions = {closeOnOuterClick: false};
	
	if ($('#sk_import_form').length){
		$('#sk_import_form').remove();
	}
	$('<div id="sk_import_form" style="display: none;"></div>').prependTo('body').load(
		site_url + 'm/social_inviter/ajax_mode/import_form',
		{
			ajaxmode: 'true',
			relocate: String(window.location),
		},
		function() {
			$('#sk_inviter_loading_img').hide();
			$(this).dolPopup(oPopupOptions);
		});
}

function showSocialInviterMes(mess,capt){
	var oPopupOptions = {
	   onShow: function () {
	       $('#sk_inviter_loading_img').hide();
			var _body = $('body');
			if(_body.css('overflow') == 'hidden')
				_body.css('overflow','visible');
	   }
	};
    
    showPopupAnyHtml(site_url + 'm/social_inviter/ajax_mode/popup_message?ajaxmode=true&message=' + encodeURIComponent(mess) + '&caption=' + capt, oPopupOptions);
}