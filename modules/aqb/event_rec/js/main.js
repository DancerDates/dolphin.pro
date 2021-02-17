/***************************************************************************
* 
*     copyright            : (C) 2009 AQB Soft
*     website              : http://www.aqbsoft.com
*      
* IMPORTANT: This is a commercial product made by AQB Soft. It cannot be modified for other than personal usage.
* The "personal usage" means the product can be installed and set up for ONE domain name ONLY. 
* To be able to use this product for another domain names you have to order another copy of this product (license).
* 
* This product cannot be redistributed for free or a fee without written permission from AQB Soft.
* 
* This notice may not be removed from the source code.
* 
***************************************************************************/

(function($){
    $.extend({aqb_months_weeks : {},
			aqbChangeTimePanels : function() {
				$('[name="aqb_time_settings[]"]').change(function(){						
						$('.aqb-event-rec-panel-sub').each(function(){
							if ($(this).is(':visible')) $(this).hide('slow');
							else $(this).show('slow');
						})									
					});					
				},
			
			aqbChangePattern : function(iVal) {
						var text = '';
						switch(parseInt(iVal)){
							case 1:text = this.aqb_months_weeks.days; break;
							case 2:text = this.aqb_months_weeks.weeks; break;
							case 3:text = this.aqb_months_weeks.months; 
						}
						
						$('#aqb-weeks-month').html(text); 				
			},	
			
			aqbChangeRange : function(iVal) {
					
					switch(parseInt(iVal)){
						case 1 :
									$('#aqb-occurrence').attr("disabled", true);
									$('#aqb-date-end').attr("disabled", true);
								break;
						case 2 :
									$('#aqb-occurrence').attr("disabled", false);
									$('#aqb-date-end').attr("disabled", true);								
								break;
						case 3 :
									$('#aqb-occurrence').attr("disabled", true);
									$('#aqb-date-end').attr("disabled", false);
								break;						
					}				
			},	
			
			aqbValidateEventTime : function(aValues){
				var sMessage = '', oObectFocus = null;
				
				iMode = parseInt($('[name="aqb_time_settings[]"]:checked').val()); 
				
				if (($('#aqb-start').val().trim() == '' && iMode)  || ($('[name="EventStart"]').val().trim() == '' && !iMode)){ 
					sMessage = aValues[0];					
					if (!iMode) oObectFocus = $('[name="EventStart"]'); else oObectFocus = $('#aqb-start');					
				}

				if (!sMessage && (($('#aqb-end').val().trim() == '' && iMode)  || ($('[name="EventEnd"]').val().trim() == '' && !iMode))){ 
					sMessage = aValues[1];					
					if (!iMode) oObectFocus = $('[name="EventEnd"]'); else oObectFocus = $('#aqb-end');					
				}
								
				if (!iMode && $('[name="EventEnd"]').val() && $('[name="EventEnd"]').val() <= $('[name="EventStart"]').val()){
						sMessage = aValues[10];
						oObectFocus = $('[name="EventEnd"]');
				}	
					
				if (iMode && !sMessage){ 		
					if (!$('input[name=repeat]:checked').val()) sMessage = aValues[2];
					else if($('input[name=repeat]:checked').val() == 2 && !$('[name="repeat_week_days[]"]:checked').val()){ 						
						sMessage = aValues[4];
					}
					
					if (!$('#aqb-date-start').val() && !sMessage){
						oObectFocus = $('#aqb-date-start');
						sMessage = aValues[5];
					}	
					
					if (!$('input[name=range_date]:checked').val() && !sMessage){
						sMessage = aValues[6];
					}	

					if (!sMessage && ($('input[name=range_date]:checked').val() == 2 && !$('[name="occurrence"]').val())){
						oObectFocus = $('#aqb-occurrence');
						sMessage = aValues[7];
					}else if (!sMessage && ($('input[name=range_date]:checked').val() > 2 && !$('[name="date_end"]').val())){
						oObectFocus = $('#aqb-date-end');
						sMessage = aValues[8];
					}else if (!sMessage && $('input[name=range_date]:checked').val() == 3 && $('#aqb-date-end').val() && $('#aqb-date-end').val() <= $('#aqb-date-start').val()){
						sMessage = aValues[10];
						oObectFocus = $('#aqb-date-end');
					}	
				}
				
				if (sMessage){
					alert(sMessage);
					if (oObectFocus !== null) oObectFocus.focus();
					$("html, body").animate({ scrollTop: $('.aqb-event-rec-area').offset().top - $(window).height()/4 });
					
					return false;
				}
				
				return true;
			},					
			});		
})(jQuery);