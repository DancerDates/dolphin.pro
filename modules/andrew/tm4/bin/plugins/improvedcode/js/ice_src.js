tinyMCEPopup.requireLangPack();

function ImprovedCodeEditor() {
	var _cmSettings = null;
	var chr = 0;	// Unused utf-8 character, placeholder for cursor
	var initial_content = null;

	this.init = function () {
		tinyMCEPopup.resizeToInnerSize();

		// Remove Gecko spellchecking
		if (tinymce.isGecko)
			document.body.spellcheck = tinyMCEPopup.editor.getParam("gecko_spellcheck");

		this.resizeInputs();

		// Set CodeMirror cursor to same position as cursor was in TinyMCE:
		var editor_content = tinyMCEPopup.editor.getContent({source_view : true});
		editor_content = editor_content.replace(/<span\s+class="CuRCaRet"([^>]*)>([^<]*)<\/span>/gm, String.fromCharCode(chr));
		tinyMCEPopup.editor.dom.remove(tinyMCEPopup.editor.dom.select('.CuRCaRet'));
		document.getElementById('htmlSource').value = editor_content;

		_setWrap('soft');

		_cmSettings = tinyMCEPopup.getWindowArg("settings");
		if(_cmSettings.optionsBar) {
			document.getElementById('wraped').checked = _cmSettings.lineWrapping;
			document.getElementById('highlighting').checked = true;
			document.getElementById('indented').checked = _cmSettings.autoIndent;
			document.getElementById('linenumbers').checked = _cmSettings.lineNumbers;
		} else {
			var el = document.getElementById('optionsBar');
			el.parentNode.removeChild(el);
		}

		_cmSettings.url = tinyMCEPopup.getWindowArg("plugin_url");
		_cmSettings.themeUrl = "js/codemirror/theme/";
		_cmSettings.theme = _sanitizeTheme(_cmSettings.theme, _cmSettings.themeUrl);
		_loadCssTheme();
		_cmSettings.cme = _initCodeMirror(_cmSettings);
		initial_content = _cmSettings.cme.getDoc().getValue();
		_resizeCodeMirror();
		if(_cmSettings.autoIndent) { _indentCode(true); }
		_cmSettings.cme.on("change",function(){ _updateUndoRedo(); });
		_cmSettings.cme.getDoc().clearHistory();
		_updateUndoRedo();
	};

	this.cancel = function() {
		tinyMCEPopup.editor.setContent(initial_content, {source_view : true});
		tinyMCEPopup.close();
	};

	this.resizeInputs = function() {
		var vp = tinyMCEPopup.dom.getViewPort(window), el;

		el = document.getElementById('htmlSource');

		if (el) {
			el.style.width = (vp.w - 20) + 'px';
			el.style.height = (vp.h - 65) + 'px';
		}

		if(null !== _cmSettings) _resizeCodeMirror();
	};

	this.saveContent = function() {
		var content = document.getElementById('htmlSource').value;
		if(null !== _cmSettings) {
			content = _cmSettings.cme.getDoc().getValue();
		}
		tinyMCEPopup.editor.setContent(content, {source_view : true});
		tinyMCEPopup.close();
	};

	this.toggleHighlighting = function(elm) {
		if(elm.checked) {
			_cmSettings.cme.setOption("theme",_cmSettings.theme);
		} else {
			_cmSettings.cme.setOption("theme","none");
		}
		var ins = (elm.checked) ? "on" : "off";
		var out = (elm.checked) ? "off" : "on";
		_switchClasses($(elm).parent(),ins,out);
		_giveFocus();
	};

	this.toggleIndent = function(elm) {
		_indentCode(elm.checked);
		var ins = (elm.checked) ? "on" : "off";
		var out = (elm.checked) ? "off" : "on";
		_switchClasses($(elm).parent(),ins,out);
		_giveFocus();
	};

	this.toggleLineNumbers = function(elm) {
		_cmSettings.cme.setOption("lineNumbers",elm.checked);
		var ins = (elm.checked) ? "on" : "off";
		var out = (elm.checked) ? "off" : "on";
		_switchClasses($(elm).parent(),ins,out);
		_giveFocus();
	};

	this.toggleWordWrap = function(elm) {
		_cmSettings.cme.setOption("lineWrapping",elm.checked);
		var ins = (elm.checked) ? "on" : "off";
		var out = (elm.checked) ? "off" : "on";
		_switchClasses($(elm).parent(),ins,out);
		_giveFocus();
	};

	/* Undo - Redo */
	this.redo = function() {
		_cmSettings.cme.getDoc().redo();
		_giveFocus();
	};

	this.undo = function() {
		_cmSettings.cme.getDoc().undo();
		_giveFocus();
	};


	////////////////////////////////////////////
	/**
	*	Private Methods
	*/

	function _fileExists(url)
	{
		var http = new XMLHttpRequest();
		http.open('HEAD', url, false);
		http.send();
		return http.status==200;
	};

	function _giveFocus() {
		_cmSettings.cme.refresh();
		_cmSettings.cme.focus();
	};

	function _indentCode(on) {
		var lines = _cmSettings.cme.getDoc().lineCount();
		var indent_mode = on ? "smart" : "substract";
		for(var i=0;i<lines;i++) {
			_cmSettings.cme.indentLine(i,indent_mode);
		}
	};

	function _initCodeMirror(settings,theme) {
		var textArea = document.getElementById("htmlSource");

		CodeMirror.defineInitHook(function(inst){
			// Move cursor to correct position:
			inst.focus();
			var cursor = inst.getSearchCursor(String.fromCharCode(chr), false);
			if (cursor.findNext()) {
				inst.setCursor(cursor.to());
				cursor.replace('');
			}
		});

		var myCodeMirror = CodeMirror.fromTextArea(
								textArea
								,{
									indentUnit: settings.indentUnit
									,tabSize: settings.tabSize
									,lineNumbers: settings.lineNumbers
									,theme: settings.theme
									,lineWrapping: settings.lineWrapping
									,mode: "application/xml"
								}
							);
		document.getElementById("htmlSource").value = myCodeMirror.getDoc().getValue();
		return myCodeMirror;
	};

	function _loadCssTheme() {
		if('' == _cmSettings.theme || 'default' == _cmSettings.theme) return null;

		var fileref = document.createElement("link");
		fileref.setAttribute("rel", "stylesheet");
		fileref.setAttribute("type", "text/css");
		fileref.setAttribute("href", _cmSettings.themeUrl + _cmSettings.theme + ".css");
		document.getElementsByTagName("head")[0].appendChild(fileref);	

	};

	function _resizeCodeMirror() {
		var vp = tinyMCEPopup.dom.getViewPort(window);
		_cmSettings.cme.setSize("100%",vp.h - 100);
	};

	function _sanitizeTheme(theme, themeUrl) {
		if('' == theme || 'default' == theme) return 'default';

		if(_fileExists(themeUrl + theme + ".css")) {
			return theme;
		} else {
			return '';
		}
	};

	function _setWrap(val) {
		var v, n, s = document.getElementById('htmlSource');

		s.wrap = val;

		if (!tinymce.isIE) {
			v = s.value;

			n = s.cloneNode(false);
			n.setAttribute("wrap", val);
			s.parentNode.replaceChild(n, s);
			n.value = v;
		}
	};

	function _switchClasses(elm, ins, out) {
		elm.removeClass(out);
		elm.addClass(ins);
	};

	function _updateUndoRedo() {
		var history = _cmSettings.cme.getDoc().historySize();
		var undoButton = $("#undo_btn");
		if(history.undo < 1) {
			_switchClasses(undoButton, 'off', 'on');
		} else {
			_switchClasses(undoButton, 'on', 'off');
		}

		var redoButton = $("#redo_btn");
		if(history.redo < 1) {
			_switchClasses(redoButton, 'off', 'on');
		} else {
			_switchClasses(redoButton, 'on', 'off');
		}
	};
}

var ice = new ImprovedCodeEditor();
tinyMCEPopup.onInit.add(ice.init, ice);

