function AmsSettings(oOptions) {    
    this._iOwnerId = oOptions.iOwnerId == undefined ? 0 : parseInt(oOptions.iOwnerId);
}

AmsSettings.prototype.showErrorMsg = function(sErrorCode, sRelocate) {
	var oErrorDiv = $('#' + sErrorCode);
	var $this = this;

	setTimeout( function(){
		oErrorDiv.show(1000)
		setTimeout( function(){
			oErrorDiv.hide(1000);
            window.open (sRelocate,'_self');
		}, 3000);
	}, 500);
}
