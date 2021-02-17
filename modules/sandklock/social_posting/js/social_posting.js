function showSocialPostingMes(mess,cap,refresh){
	var oPopupOptions = {
        closeOnOuterClick: false,
        onShow: function () {
			if(refresh){
				var timer = setInterval(function(){
					if($('#login_div').length){
						window.location = window.location.href;
						clearInterval(timer);
					}
				},800);
			}
        }
       };
       
    if ($('#login_div').length)
		$('#login_div').remove();
    if ($('#posting_div').length)
  		$('#posting_div').remove();
    
    showPopupAnyHtml(site_url + 'm/social_posting/gen_popup_message?message=' + encodeURIComponent(mess) + '&caption=' + cap, oPopupOptions);
}

function showSocialPostingLoadingImg(img_src){
	var oPopupOptions = {closeOnOuterClick: false};
	showPopupAnyHtml(site_url + 'm/social_posting/show_loading_img?img_src=' + encodeURIComponent(img_src), oPopupOptions);
}

function popupPosting(pageURL,img_src,isLoadingImg){
		var w = 800;
		var h = 500;
		var title ='socialpostingwindow';
		var left = (screen.width/2)-(w/2);
		var top = (screen.height/2)-(h/2);
		var newwindow =  window.open (pageURL, title, 'toolbar=no,location=no,directories=no,status=no,menubar=no, scrollbars=yes,resizable=yes,copyhistory=no,width='+w+',height='+h+',top='+top+',left='+left);
		if (window.focus) {newwindow.focus();}
		
		if(isLoadingImg){
			showSocialPostingLoadingImg(img_src);
			var interval = setInterval(function(){
				if (newwindow.closed) {
					var oPopupOptions = {closeOnOuterClick: false};
                    if($('#posting_div').length && $('#posting_div').css('display') != 'none')
					   $('#posting_div').dolPopupHide(oPopupOptions);
                    if($('#login_div').length && $('#login_div').css('display') != 'none')
                        $('#login_div').dolPopupHide(oPopupOptions);
					clearInterval(interval);
				}
			},500);
		}
};

function socialPostingDisconnect(net,img_src,isRefresh){
	var url = site_url + 'm/social_posting/disconnect';
    var setConnect = (skposting.get_cookie('skposting_popup') == 1 && skposting.opened == true) ? '1' : '0';
	var data ={
		network: net,
        setconnect: setConnect
	};
	$.post(url,data,function(response,status){
		if(status == 'success'){
			if(response.network && response.url){
				var ele = $('a[id="sk_connect_' + response.network + '"]').parent();
				if(isRefresh)
					ele.html('<a id="sk_connect_'+response.network+'" href="javascript:void(popupPosting(\''+response.url+'\',\''+img_src+'\',true))">Connect</a>');
				else
					ele.html('<a id="sk_connect_'+response.network+'" href="javascript:void(popupPosting(\''+response.url+'\',\''+img_src+'\',false))">Connect</a>');
				ele.parent().find('input:checkbox').prop('disabled',true);
			}
			if(response.error)
				alert(response.error);
		}else
			alert('Cant connect to server. Please contact the administrators');
	},'json');
}

function socialPostingHandleReconnecting(network,username,img_src){
	var ele = $('a[id="sk_connect_' + network + '"]').parent();
	ele.html('Connected as '+socialPostingUtf8Decode(username)+' (<a id="sk_connect_'+network+'" href="javascript:void(socialPostingDisconnect(\''+network+'\',\''+img_src+'\',false))">Disconnect</a>)');
	ele.parent().find('input:checkbox').prop('disabled',false);
}

function socialPostingUtf8Decode(utftext) {
	var string = "";
	var i = 0;
	var c = c1 = c2 = 0;
	while (i < utftext.length) {
		c = utftext.charCodeAt(i);
		if (c < 128) {
			string += String.fromCharCode(c);
			i++;
		} else if ((c > 191) && (c < 224)) {
			c2 = utftext.charCodeAt(i + 1);
			string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
			i += 2;
		} else {
			c2 = utftext.charCodeAt(i + 1);
			c3 = utftext.charCodeAt(i + 2);
			string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
			i += 3;
		}
	}
	return string;
}

var skposting={
	opened: false,
    unloading: false,
	skip: function (){
		try
		{
			if(skpostingskip == true)
				return true;
			else
				return false;
		}
		catch(err)
		{
			return false;
		}
	},
	call: function(){
		var ck=skposting.get_cookie('skposting_popup');
		if (skposting.opened == false &&  ck == 1) {
		   skposting.opena();
		} else if(ck == '0' && skposting.opened == true){
			skposting.opened = false;
			$('#posting_div').dolPopupHide();
		}
	},
	set_cookie: function (name, value) {
		var cookie_string = name + "=" + escape(value);
		cookie_string += "; path=/";
		document.cookie = cookie_string;
	}, 
	get_cookie: function (cookie_name) {
		var results = document.cookie.match('(^|;) ?' + cookie_name
				+ '=([^;]*)(;|$)');
		if (results)
			return (unescape(results[2]));
		else
			return null;
	},	
	opena: function () {		
		var oPopupOptions = {};
        
		if ($('#posting_div').length){
			$('#posting_div').remove();
		}
        
		skposting.opened = true;
        
		$('<div id="posting_div" style="display:none;"></div>').appendTo('body').load(
			site_url + 'modules/?r=social_posting/show_posting_form+&_t=' + new Date().getTime(), 
			{
				action : 'show_posting_form',
				relocate : String(window.location)
			}, 
			function() {
				$(this).dolPopup(oPopupOptions);
				$el =  $('#posting_div');
				$(document).click(function(e) {
					if ($el.hasClass('bx-popup-active') && $el.is(':visible')) {
						if ($(e.target).parents('#' + $el.attr('id')).length == 0) {
							skposting.set_cookie('skposting_popup', 0);
							skposting.opened = false;
						}
					}
					return true;
				});
			});
	},
	not_publish: function(){
		skposting.opened = false;
		skposting.set_cookie('skposting_popup', 0);
		$('#posting_div').dolPopupHide();
	},
	post: function(){       
		var _plurk = $('input#plurk').is(':checked') ? 1 : 0;
		var _twitter = $('input#twitter').is(':checked') ? 1 : 0;
		var _linkedin = $('input#linkedin').is(':checked') ? 1 : 0;
		var _mailru = $('input#mailru').is(':checked') ? 1 : 0;
		var _lastfm = $('input#lastfm').is(':checked') ? 1 : 0;
		var _tumblr = $('input#tumblr').is(':checked') ? 1 : 0;
		var _facebook = $('input#facebook').is(':checked') ? 1 : 0;
		if(_plurk == 0 && _twitter == 0 && _linkedin == 0 && _mailru == 0 && _lastfm == 0 && _tumblr == 0 && _facebook == 0) 
		{
			alert('Please select network to post?');
			return;
		}
		var _message = $('.sk_posting_form_input:eq(0) textarea').val();
		if(_message == '')
		{
			alert('Please type a message to post?');
			return;
		}
		var _auto_publish = $('input#auto_publish').is(':checked') ? 1 : 0;
		var _ask_again = $('input#no_ask').is(':checked') ? 1 : 0;
		
		var data = {
			plurk: _plurk,
			twitter: _twitter,
			linkedin: _linkedin,
			mailru: _mailru,
			lastfm: _lastfm,
			tumblr: _tumblr,
			facebook: _facebook,
			auto_publish: _auto_publish,
			no_ask: _ask_again,
			message: _message
		};
        
        $('#posting_div input[type=\'button\']').val('Wait...').prop("disabled", true);
		
		$.post(site_url + 'm/social_posting/ajax_post',data,function(response,status){
			if(status == 'success'){
				alert(response);
				skposting.set_cookie('skposting_popup', 0);
				setTimeout(function() { $('#posting_div').dolPopupHide();skposting.opened = false; }, 700); 
			}else
				alert('Couldn\'t connect to server');
		});
	}
}

$(document).ready(function() {
	if(skposting.skip() == false){
		window.setInterval(skposting.call, 800);	
	}
});
