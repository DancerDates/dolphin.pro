var aqb_ppp_page_reload_required = false;
function aqb_profile_photo_picker_popup(iProfile) {
	var oDate = new Date();

	if ($('#aqb_ppp_popup').length) {
        $('#aqb_ppp_popup').remove();
    }

    $.get(site_url + 'modules/?r=aqb_profile_photo_picker/action_get_photo_picker/'+iProfile+'/'+oDate.getTime(), function(sResponse) {
        aqb_ppp_page_reload_required = false;
        $(sResponse).prependTo('body').dolPopup({fog: {color: '#444', opacity: .7}, closeOnOuterClick: true, onHide: function(){if (aqb_ppp_page_reload_required) window.location.href = window.location.href}});
    }, 'html');
}

function aqb_profile_photo_picker_choose_photo(iProfile, iPhoto) {
    var oDate = new Date();

    $('.aqb_ppp_photo_item').removeClass('aqb_ppp_selected_photo');
    $('#aqb_ppp_photo_'+iPhoto).addClass('aqb_ppp_selected_photo');

    $.post(site_url + 'modules/?r=aqb_profile_photo_picker/action_set_photo/'+iProfile+'/'+iPhoto+'/'+oDate.getTime(), function(sResponse) {
        if (sResponse) alert(sResponse);
        else aqb_ppp_page_reload_required = true;
    }, 'html');
};