$( document ).ready(function() {
		$( "#network_ui" ).sortable({
			update: function(event,ui){
				var order = $('#network_ui').sortable('serialize');
				console.log(order);
				orderProvider(order);
			}
		});
		$( "#network_ui" ).disableSelection();
	});
	
	function orderProvider(order) {
		$.ajax({
			type : "POST",
			url : site_url+"m/social_login/ajax_mode/order_network",
			data : "ajaxmode=true&" + order,
			success : function(msg) {
				//console.log(msg);
			}
		});
	}
	
	function enableProvider(network_name, status) {
		$('#sk_' + network_name + "_updating").hide();
		$.ajax({
			type : "POST",
			url : site_url+"m/social_login/ajax_mode/status_network",
			data : "ajaxmode=true" + "&network=" + network_name + "&status=" + status,
			success : function(msg) {
				$('#sk_' + network_name + "_updating").hide();
			}
		});
	}
	
	jQuery.fn.iphoneSwitch = function(start_state, switched_on_callback,switched_off_callback) {

		var state = start_state == 'enabled' ? start_state : 'disabled';

		// define default settings
		var settings = {
			mouse_over : 'pointer',
			mouse_out : 'default',
			switch_on_container_path : site_url+'modules/sandklock/social_login/templates/base/images/iphone_switch_container_off.png',
			switch_off_container_path : site_url+'modules/sandklock/social_login/templates/base/images/iphone_switch_container_off.png',
			switch_path : site_url+'modules/sandklock/social_login/templates/base/images/iphone_switch.png',
			switch_height : 27,
			switch_width : 94
		};

		jQuery.extend(settings, 'switch_on_container_path');

		// create the switch
		return this.each(function() {

			var container;
			var image;

			// make the container
			container = jQuery('<div class="iphone_switch_container" style="height:'+settings.switch_height+'px; width:'+settings.switch_width+'px; position: relative; overflow: hidden"></div>');

			// make the switch image based on starting state
			image = jQuery('<img class="iphone_switch" style="height:'
					+ settings.switch_height
					+ 'px; width:'
					+ settings.switch_width
					+ 'px; background-image:url('
					+ settings.switch_path
					+ '); background-repeat:none; background-position:'
					+ (state == 'enabled' ? 0 : -53)
					+ 'px" src="'
					+ (state == 'disabled' ? settings.switch_on_container_path
							: settings.switch_off_container_path)
					+ '" />');
			//alert(image)
			// insert into placeholder
			jQuery(this).html(jQuery(container).html(jQuery(image)));

			jQuery(this).mouseover(function() {
				jQuery(this).css("cursor", settings.mouse_over);
			});

			// click handling
			jQuery(this).click(function() {
				$('#' + jQuery(this).attr('id') + '_updating').show();
				if (state == 'enabled') {
					jQuery(this).find('.iphone_switch').animate(
									{
										backgroundPosition : -53
									},
									"slow",
									function() {
										//jQuery(this).attr('src', settings.switch_off_container_path);
										switched_off_callback();
									});
					state = 'disabled';
				} else {
					jQuery(this).find('.iphone_switch').animate({backgroundPosition : 0
							}, "slow", function() {
								switched_on_callback();
							});
					//jQuery(this).find('.iphone_switch').attr('src', settings.switch_on_container_path);
					state = 'enabled';
				}
			});
		});
	};