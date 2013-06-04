/**
 * @name NiceJForms
 * @description This a jQuery equivalent for Niceforms ( http://badboy.ro/articles/2007-01-30/niceforms/ ).  All the forms are styled with beautiful images as backgrounds and stuff. Enjoy them!
 * @param Hash hash A hash of parameters
 * @option integer selectRightSideWidth width of right side of the select
 * @option integer selectLeftSideWidth width of left side of the select 
 * @option integer selectAreaHeight
 * @option integer selectAreaOPtionsOverlap
 * @option imagesPath folder where custom form images are stored
 * @type jQuery
 * @cat Plugins/Interface/Forms
 * @author Lucian Lature ( lucian.lature@gmail.com )
 * @credits goes to Lucian Slatineanu ( http://www.badboy.ro )
 * @version 0.1
 */

var selectRightWidthSimple = 19;
var selectRightWidthScroll = 2;
var selectMaxHeight = 200;
var textareaTopPadding = 10;
var textareaSidePadding = 10;

jQuery.NiceJForms = {
	options : {
		selectRightSideWidth     : 21,
		selectLeftSideWidth      : 8,
		selectAreaHeight 	     : 21,
		selectAreaOptionsOverlap : 2,
		imagesPath               : "css/images/default/"
		// other options here
	},
	
	selectText     : 'please select',
	preloads       : new Array(),
	inputs         : new Array(),
	labels         : new Array(),
	textareas      : new Array(),
	selects        : new Array(),
	radios         : new Array(),
	checkboxes     : new Array(),
	texts          : new Array(),
	buttons        : new Array(),
	radioLabels    : new Array(),
	checkboxLabels : new Array(),
	files          : new Array(),
	hasImages      : true,
	
	keyPressed : function(event)
	{
		var pressedKey = event.charCode || event.keyCode || -1;
		
		switch (pressedKey)
		{
			case 40: //down
			var fieldId = this.parentNode.parentNode.id.replace(/sarea/g, "");
			var linkNo = 0;
			for(var q = 0; q < selects[fieldId].options.length; q++) {if(selects[fieldId].options[q].selected) {linkNo = q;}}
			++linkNo;
			if(linkNo >= selects[fieldId].options.length) {linkNo = 0;}
			selectMe(selects[fieldId].id, linkNo, fieldId);
			break;
		
		case 38: //up
			var fieldId = this.parentNode.parentNode.id.replace(/sarea/g, "");
			var linkNo = 0;
			for(var q = 0; q < selects[fieldId].options.length; q++) {if(selects[fieldId].options[q].selected) {linkNo = q;}}
			--linkNo;
			if(linkNo < 0) {linkNo = selects[fieldId].options.length - 1;}
			selectMe(selects[fieldId].id, linkNo, fieldId);
			break;
		default:
			break;
		}
	},
	
	build : function(options)
	{
		if (options)
			jQuery.extend(jQuery.NiceJForms.options, options);	
			
		if (window.event) {
			jQuery('body',document).bind('keyup', jQuery.NiceJForms.keyPressed);
		} else {
			jQuery(document).bind('keyup', jQuery.NiceJForms.keyPressed);
		}
		
		// test if images are disabled or not
		var testImg = document.createElement('img');
		$(testImg).attr("src", jQuery.NiceJForms.options.imagesPath + "blank.gif").attr("id", "imagineTest");
		jQuery('body').append(testImg);
		
		if(testImg.complete)
		{
			if(testImg.offsetWidth == '1') {jQuery.NiceJForms.hasImages = true;}
			else {jQuery.NiceJForms.hasImages = false;}
		}

		$(testImg).remove();
			
		if(jQuery.NiceJForms.hasImages)
		{
			$('form.niceform').each( function()
				{
					el 				= jQuery(this);
					jQuery.NiceJForms.preloadImages();
					jQuery.NiceJForms.getElements(el);
					jQuery.NiceJForms.replaceRadios();
					jQuery.NiceJForms.replaceCheckboxes();
					jQuery.NiceJForms.replaceSelects();
					
					if (!$.browser.safari) {
						jQuery.NiceJForms.replaceTexts();
						jQuery.NiceJForms.replaceTextareas();
						jQuery.NiceJForms.replaceFiles();
						jQuery.NiceJForms.buttonHovers();
					}
				}
			);
		}	
	},
	
	preloadImages: function()
	{
		jQuery.NiceJForms.preloads = $.preloadImages(jQuery.NiceJForms.options.imagesPath + "button_left_xon.gif", jQuery.NiceJForms.options.imagesPath + "button_right_xon.gif", 
		jQuery.NiceJForms.options.imagesPath + "input_left_xon.gif", jQuery.NiceJForms.options.imagesPath + "input_right_xon.gif",
		jQuery.NiceJForms.options.imagesPath + "txtarea_bl_xon.gif", jQuery.NiceJForms.options.imagesPath + "txtarea_br_xon.gif", 
		jQuery.NiceJForms.options.imagesPath + "txtarea_cntr_xon.gif", jQuery.NiceJForms.options.imagesPath + "txtarea_l_xon.gif", jQuery.NiceJForms.options.imagesPath + "txtarea_tl_xon.gif", jQuery.NiceJForms.options.imagesPath + "txtarea_tr_xon.gif");
	},
	
	getElements: function(elm)
	{
		el = elm ? jQuery(elm) : jQuery(this);
		
		var r = 0; var c = 0; var t = 0; var rl = 0; var cl = 0; var tl = 0; var b = 0;
		
		jQuery.NiceJForms.inputs = $('input', el);
		jQuery.NiceJForms.labels = $('label', el);
		jQuery.NiceJForms.textareas = $('textarea', el);
		jQuery.NiceJForms.selects = $('select', el);
		jQuery.NiceJForms.radios = $(':radio', el);
		jQuery.NiceJForms.checkboxes = $(':checkbox', el);
		jQuery.NiceJForms.texts = $(':text', el).add($(':password', el));		
		jQuery.NiceJForms.buttons = $(':submit', el).add($(':button', el));
		jQuery.NiceJForms.files = $(':file', el);
		
		jQuery.NiceJForms.labels.each(function(i){
			labelFor = $(jQuery.NiceJForms.labels[i]).attr("for");
			jQuery.NiceJForms.radios.each(function(q){
				if(labelFor == $(jQuery.NiceJForms.radios[q]).attr("id"))
				{
					if(jQuery.NiceJForms.radios[q].checked)
					{
						$(jQuery.NiceJForms.labels[i]).removeClass().addClass("chosen");	
					}
					
					jQuery.NiceJForms.radioLabels[rl] = jQuery.NiceJForms.labels[i];
					++rl;
				}
			})
			
			jQuery.NiceJForms.checkboxes.each(function(x){
				
				if(labelFor == $(this).attr("id"))
				{
					if(this.checked)
					{
						$(jQuery.NiceJForms.labels[i]).removeClass().addClass("chosen");	
					}
					jQuery.NiceJForms.checkboxLabels[cl] = jQuery.NiceJForms.labels[i];
					++cl;
				}
			})
		});
	},
	
	replaceRadios: function()
	{
		var self = this;
		
		jQuery.NiceJForms.radios.each(function(q){
		
			//alert(q);
			$(this).removeClass().addClass('outtaHere'); //.hide(); //.className = "outtaHere";
			
			
			
			var radioArea = document.createElement('div');
			//console.info($(radioArea));
			if(this.checked) {$(radioArea).removeClass().addClass("radioAreaChecked");} else {$(radioArea).removeClass().addClass("radioArea");};
			
			radioPos = jQuery.iUtil.getPosition(this);
			
			jQuery(radioArea)
				.attr({id: 'myRadio'+q})
				.css({left: radioPos.x + 'px', top: radioPos.y + 'px', margin : '1px'})
				.bind('click', {who: q}, function(e){self.rechangeRadios(e)})
				.insertBefore($(this));
			
			$(jQuery.NiceJForms.radioLabels[q]).bind('click', {who: q}, function(e){self.rechangeRadios(e)});
			
			if (!$.browser.msie) {
				$(this).bind('focus', function(){self.focusRadios(q)}).bind('blur', function() {self.blurRadios(q)});
			}
			
			$(this).bind('click', function(e){self.radioEvent(e)});
		});
		
		return true;
	},
	
	changeRadios: function(who) {
		
		var self = this;
		
		if(jQuery.NiceJForms.radios[who].checked) {
		
			jQuery.NiceJForms.radios.each(function(q){
				if($(this).attr("name") == $(jQuery.NiceJForms.radios[who]).attr("name"))
				{
					this.checked = false;
					$(jQuery.NiceJForms.radioLabels[q]).removeClass();	
				}
			});
			jQuery.NiceJForms.radios[who].checked = true;
			$(jQuery.NiceJForms.radioLabels[who]).addClass("chosen");
			
			self.checkRadios(who);
		}
	},
	
	rechangeRadios:function(e) 
	{
		who = e.data.who;
		
		if(!jQuery.NiceJForms.radios[who].checked) {
			for(var q = 0; q < jQuery.NiceJForms.radios.length; q++) 
			{
				if(jQuery.NiceJForms.radios[q].name == jQuery.NiceJForms.radios[who].name) 
				{
					jQuery.NiceJForms.radios[q].checked = false; 
					//console.info(q);
					jQuery.NiceJForms.radioLabels[q].className = "";
				}
			}
			$(jQuery.NiceJForms.radios[who]).attr('checked', true); 
			jQuery.NiceJForms.radioLabels[who].className = "chosen";
			jQuery.NiceJForms.checkRadios(who);
		}
	},
	
	checkRadios: function(who) {
		$('div').each(function(q){
			if($(this).is(".radioAreaChecked") && $(this).next().attr("name") == $(jQuery.NiceJForms.radios[who]).attr("name")) {$(this).removeClass().addClass("radioArea");}
		});
		$('#myRadio' + who).toggleClass("radioAreaChecked");
	},
	
	focusRadios: function(who) {
		$('#myRadio' + who).css({border: '1px dotted #333', margin: '0'}); return false;
	},
	
	blurRadios:function(who) {
		$('#myRadio' + who).css({border: 'none', margin: '1px'}); return false;
	},
	
	radioEvent: function(e) {
		var self = this;
		if (!e) var e = window.event;
		if(e.type == "click") {for (var q = 0; q < jQuery.NiceJForms.radios.length; q++) {if(this == jQuery.NiceJForms.radios[q]) {self.changeRadios(q); break;}}}
	},
	
	replaceCheckboxes: function () 
	{
		/*
		var self = this;
		
		jQuery.NiceJForms.checkboxes.each(function(q){
			//move the checkboxes out of the way
			$(jQuery.NiceJForms.checkboxes[q]).removeClass().addClass('outtaHere');
			//create div
			var checkboxArea = document.createElement('div');
			
			//console.info($(radioArea));
			if(jQuery.NiceJForms.checkboxes[q].checked) {$(checkboxArea).removeClass().addClass("checkboxAreaChecked");} else {$(checkboxArea).removeClass().addClass("checkboxArea");};
			
			checkboxPos = jQuery.iUtil.getPosition(jQuery.NiceJForms.checkboxes[q]);
			
			jQuery(checkboxArea)
				.attr({id: 'myCheckbox' + q})
				.css({
				left: checkboxPos.x + 'px', 
				top: checkboxPos.y + 'px',
				margin : '1px'
			})
			.bind('click', {who: q}, function(e){self.rechangeCheckboxes(e)})
			.insertBefore($(jQuery.NiceJForms.checkboxes[q]));
			
			if(!$.browser.safari)
			{
				$(jQuery.NiceJForms.checkboxLabels[q]).bind('click', {who:q}, function(e){self.changeCheckboxes(e)})
			}
			else {
				$(jQuery.NiceJForms.checkboxLabels[q]).bind('click', {who:q}, function(e){self.rechangeCheckboxes(e)})
			}
			
			if(!$.browser.msie)
			{
				$(jQuery.NiceJForms.checkboxes[q]).bind('focus', {who:q}, function(e){self.focusCheckboxes(e)});
				$(jQuery.NiceJForms.checkboxes[q]).bind('blur', {who:q}, function(e){self.blurCheckboxes(e)});
			}	
			
			//$(jQuery.NiceJForms.checkboxes[q]).keydown(checkEvent);
		});
		return true;
		*/
		
		jQuery.NiceJForms.checkboxes.each(function(q){
			var el = this;
			var dummy;
			var ref;
			var isIE = false;
			
			if (/MSIE (\d+\.\d+);/.test(navigator.userAgent)) {
				var ieversion=new Number(RegExp.$1);
				if(ieversion < 7) {return false;} //exit script if IE6
				isIE = true;
			}
			
			
			el.oldClassName = el.className;
			dummy = document.createElement('img');
			
			$(dummy)
				.attr({id: 'nfCheckbox' + q})
				.attr('src', jQuery.NiceJForms.options.imagesPath + "0.png");
			
			if(el.checked) {
				$(dummy).removeClass("NFCheck");
				$(dummy).addClass("NFCheck NFh");
			}
			else {
				$(dummy).removeClass("NFCheck NFh");
				$(dummy).addClass("NFCheck");
			}
			
			ref = el;
			/*
			if(isIE == false) {
				$(dummy)
					.css({
						left: el.offsetLeft + 'px',
						top: el.offsetTop + 'px',
						margin: '-1px'
					});
			}
			else {
				$(dummy)
					.css({
						left: el.offsetLeft + 4 + 'px',
						top: el.offsetTop + 4 + 'px'
					});
			}
			*/
			dummy.onclick = function() {
				if(!ref.checked) {
					ref.checked = true;
					this.className = "NFCheck NFh";
				}
				else {
					ref.checked = false;
					this.className = "NFCheck";
				}
			}
			el.onclick = function() {
				if(this.checked) {
					dummy.className = "NFCheck NFh";
				}
				else {
					dummy.className = "NFCheck";
				}
			}
			el.onfocus = function() {
				dummy.className += " NFfocused";
			}
			el.onblur = function() {
				dummy.className = dummy.className.replace(/ NFfocused/g, "");
			}
			el.unload = function() {
				this.parentNode.removeChild(this.dummy);
				this.className = this.oldClassName;
			}

			$(dummy)
				.insertBefore(this);
		});
	},

	rechangeCheckboxes: function(e)
	{
		who = e.data.who;
		var tester = false;
		
		if($(jQuery.NiceJForms.checkboxLabels[who]).is(".chosen")) {
			tester = false;
			$(jQuery.NiceJForms.checkboxLabels[who]).removeClass();
		}
		else if(jQuery.NiceJForms.checkboxLabels[who].className == "") {
			tester = true;
			$(jQuery.NiceJForms.checkboxLabels[who]).addClass("chosen");
		}
		jQuery.NiceJForms.checkboxes[who].checked = tester;
		jQuery.NiceJForms.checkCheckboxes(who, tester);
	},

	checkCheckboxes: function(who, action)
	{
		var what = $('#myCheckbox' + who);
		if(action == true) {$(what).removeClass().addClass("checkboxAreaChecked");}
		if(action == false) {$(what).removeClass().addClass("checkboxArea");}
	},

	focusCheckboxes: function(who) 
	{
		var what = $('#myCheckbox' + who);
		$(what).css(
					{
						border : "1px dotted #333", 
						margin : "0"
					});	
		return false;
	},

	changeCheckboxes: function(e) {
		who = e.data.who;
		//console.log('changeCheckboxes who is ' + who);
		if($(jQuery.NiceJForms.checkboxLabels[who]).is(".chosen")) {
			jQuery.NiceJForms.checkboxes[who].checked = true;
			$(jQuery.NiceJForms.checkboxLabels[who]).removeClass();
			jQuery.NiceJForms.checkCheckboxes(who, false);
		}
		else if(jQuery.NiceJForms.checkboxLabels[who].className == "") {
			jQuery.NiceJForms.checkboxes[who].checked = false;
			$(jQuery.NiceJForms.checkboxLabels[who]).toggleClass("chosen");
			jQuery.NiceJForms.checkCheckboxes(who, true);
		}
	},

	blurCheckboxes: function(who) 
	{
		var what = $('#myCheckbox' + who);
		$(what).css(
					{
						border : 'none', 
						margin : '1px'
					});	
		return false;
	},
	
	replaceSelects: function()
	{
		var self = this;
		
		jQuery.NiceJForms.selects.each(function(q){
			//create and build div structure
			var selectWidth = parseInt(this.offsetWidth);
			
			var selectTop = parseInt(this.offsetTop);
			
			var selectLeft = parseInt(this.offsetLeft);
			
			var text = document.createTextNode(jQuery.NiceJForms.selects[q].options[0].text);

			var dummy = document.createElement('div');
				
			jQuery(dummy)
				.addClass('NFSelect')
				.attr({id: 'nfSelect'+q})
				.css ({
					width: selectWidth + 'px',
					left: this.offsetLeft + 'px',
					top: this.offsetTop + 'px'
				})
				.bind('click', {who:q}, function(e){self.showOptions(e)})
				.keydown(jQuery.NiceJForms.keyPressed);

			var left = document.createElement('img');

			jQuery(left)
				.attr('src', jQuery.NiceJForms.options.imagesPath + '0.png')
				.addClass("NFSelectLeft");

			var right = document.createElement('div');
			
			jQuery(right)
				.addClass('NFSelectRight')
				.attr({id: 'nfSelectRight'+q})
				.append(text);

			jQuery(dummy)
				.append(left).append(right);
			
			
			
			
			//hide the select field
			$(this).hide();
			//insert select div
			//build & place options div
			
			var bg = document.createElement('div');
			
			// selectAreaPos = jQuery.iUtil.getPosition(selectArea);
			
			jQuery(bg)
				.css({
					display: 'none'
				})
				.attr({id: 'nfSelectTarget'+q})
				.addClass("NFSelectTarget");
			
			var opt = document.createElement('ul');
			
			jQuery(opt)
				.attr({id: 'nfSelectOptions'+q})
				.addClass('NFSelectOptions');
			
			jQuery(bg)
				.append(opt);
			
			//get select's options and add to options div
			$(jQuery.NiceJForms.selects[q]).children().each(function(w){
				var optionHolder = document.createElement('li');
				var optionLink = document.createElement('a');
				var optionTxt = document.createTextNode(jQuery.NiceJForms.selects[q].options[w].text);
				
				jQuery(optionLink)
					//.addClass('NFOptionActive')
					.attr({id: 'nfSelectText'+w})
					.attr({href:'#'})
					.css({cursor:'pointer'})
					.append(optionTxt)
					.bind('click', {who: q, id:jQuery.NiceJForms.selects[q].id, option:w, select:q}, function(e){
						//self.showOptions(e);
						self.selectMe(jQuery.NiceJForms.selects[q].id, w, q);
						self.hideOptions(e);
					});
				
				jQuery(optionHolder).append(optionLink);
				jQuery(opt).append(optionHolder);
				
				//check for pre-selected items
				if(jQuery.NiceJForms.selects[q].options[w].selected) {
					// show selected option on select caption
					$(right).text($(jQuery.NiceJForms.selects[q][w]).text());
					
					// **** self.selectMe($(jQuery.NiceJForms.selects[q]).attr("id"), w, q);
				}
			});
			
			jQuery(dummy).append(bg);
			jQuery(dummy).insertBefore(this);
		});
	},

	selectMe: function(selectFieldId, linkNo, selectNo) {
		selectField = $('#' + selectFieldId);
		sFoptions = selectField.children();
		
		//LHB save original value for onchange event
		value = selectField.val();
		
		selectField.children().each(function(k){
			if(k == linkNo) {
				sFoptions[k].selected="selected";
			}
			else {
				sFoptions[k].selected = "";
			}
		});
		
		// show selected option on select caption
		$('#nfSelectRight' + selectNo).text($(sFoptions[linkNo]).text());
		
		//LHB fire the onchange event
		//if(value != $('#' + selectFieldId).val()) {eval($('#' + selectFieldId).attr('onchange').empty().append(/this.value/g, "'" + $('#' + selectFieldId).val() + "'"));}
		
		if(value != $('#' + selectFieldId).val()) {
			$('#' + selectFieldId).trigger('change');
		}
		
	}, 

	showOptions: function(e) {
		
		//muestro las opciones siempre que no este en estado deshabilitado
		
		if (!jQuery.NiceJForms.selects[e.data.who].disabled) {
		
			var dummy = $('#nfSelect' + e.data.who);
			var bg = $('#nfSelectTarget' + e.data.who);
			var opt = $('#nfSelectOptions' + e.data.who);
			/*
			$("#optionsDiv"+e.data.who).toggleClass("optionsDivVisible").toggleClass("optionsDivInvisible").mouseout(function(e){self.hideOptions(e)});
			*/
			//$('#nfSelectRight' + e.data.who).
			$('.NFSelectTarget').each(function() {
				if (this.id != 'nfSelectTarget' + e.data.who) {
					this.style.display = 'none';
				}
			});
			//var allDivs = document.getElementsByTagName('div'); for(var q = 0; q < allDivs.length; q++) {if((allDivs[q].className == "NFSelectTarget") && (allDivs[q] != bg)) {allDivs[q].style.display = "none";}}
			if($(bg).css('display') == "none") {
				$(bg).css('display', 'block');
			}
			else {
				$(bg).css('display', 'none');
			}
			
			if(opt.offsetHeight > selectMaxHeight) {
				$(bg).css('width', self.offsetWidth - selectRightWidthScroll + 33 + 'px');
				$(opt).css('width', self.offsetWidth - selectRightWidthScroll + 'px');
			}
			else {
				$(bg).css('width', self.offsetWidth - selectRightWidthSimple + 33 + 'px');
				$(opt).css('width', self.offsetWidth - selectRightWidthSimple + 'px');
			}
		}
	},
	
	hideOptions: function(e) {
		/*
		if (!e) var e = window.event;
		var reltg = (e.relatedTarget) ? e.relatedTarget : e.toElement;
		if(((reltg.nodeName != 'A') && (reltg.nodeName != 'DIV')) || ((reltg.nodeName == 'A') && (reltg.className=="selectButton") && (reltg.nodeName != 'DIV'))) {this.className = "optionsDivInvisible";};
		e.cancelBubble = true;
		if (e.stopPropagation) e.stopPropagation();
		*/
		$('#nfSelectTarget'+e.data.who).css('display', 'none');
		if (e.stopPropagation) e.stopPropagation();
	},
	
	replaceTexts: function() {
		jQuery.NiceJForms.texts.each(function(q){
			
			// LHB
			
			var where2 = $(this).prev();
			
			var container = document.createElement('div');
			
			jQuery(container)
				.addClass("NFTextCenter");
			
			// fin
			
			//$(jQuery.NiceJForms.texts[q]).css({width:this.size * 10 + 'px'});
			var txtLeft = new Image();
			jQuery(txtLeft)
				.attr({src:jQuery.NiceJForms.options.imagesPath + "0.png"})
				.addClass("NFTextLeft");
			
			var txtRight = new Image();
			jQuery(txtRight)
				.attr({src:jQuery.NiceJForms.options.imagesPath + "0.png"})
				.addClass("NFTextRight");
			
			//$(jQuery.NiceJForms.texts[q]).before(txtLeft).after(txtRight).addClass("NFText");
			
			$(this)
				.before(txtLeft)
				.after(txtRight)
				.addClass("NFText")
				.bind('focus', function() {
					$(container).addClass('NFh');
					$(txtRight).addClass('NFh');
					$(txtLeft).addClass('NFh');
					/*
					$(this).prev().attr('src', jQuery.NiceJForms.options.imagesPath + "input_left_xon.gif");
					$(this).next().attr('src', jQuery.NiceJForms.options.imagesPath + "input_right_xon.gif");
					*/
				})
				.bind('blur', function() {
					$(container).removeClass('NFh');
					$(txtRight).removeClass('NFh');
					$(txtLeft).removeClass('NFh');
					/*
					$(this).prev().attr('src', jQuery.NiceJForms.options.imagesPath + "input-left.gif");
					$(this).next().attr('src', jQuery.NiceJForms.options.imagesPath + "input-right.gif");
					*/
				});
			
			jQuery(container)
				.prepend(jQuery.NiceJForms.texts[q]);

			jQuery(txtLeft)
				.after(container);
			
			
		});
	},
	
	replaceTextareas: function() {
		jQuery.NiceJForms.textareas.each(function(q){
			
			var where = $(this).parent();
			var where2 = $(this).prev();
			
			$(this).css({width: $(this).attr("cols") * 10 + 'px', height: $(this).attr("rows") * 10 + 'px'});
			//create divs
			var container = document.createElement('div');
			jQuery(container)
				.css({width: jQuery.NiceJForms.textareas[q].cols * 10 + 20 + 'px', height: jQuery.NiceJForms.textareas[q].rows * 10 + 20 + 'px'})
				.addClass("txtarea");
			
			var topRight = document.createElement('div');
			jQuery(topRight).addClass("tr");
			
			var topLeft = new Image();
			jQuery(topLeft).attr({src: jQuery.NiceJForms.options.imagesPath + 'txtarea_tl.gif'}).addClass("txt_corner");
			
			var centerRight = document.createElement('div');
			jQuery(centerRight).addClass("cntr");
			var centerLeft = document.createElement('div');
			jQuery(centerLeft).addClass("cntr_l");
			
			if(!$.browser.msie) {jQuery(centerLeft).height(jQuery.NiceJForms.textareas[q].rows * 10 + 10 + 'px')}
			else {jQuery(centerLeft).height(jQuery.NiceJForms.textareas[q].rows * 10 + 12 + 'px')};
			
			var bottomRight = document.createElement('div');
			jQuery(bottomRight).addClass("br");
			var bottomLeft = new Image();
			jQuery(bottomLeft).attr({src: jQuery.NiceJForms.options.imagesPath + 'txtarea_bl.gif'}).addClass('txt_corner');
			
			//assemble divs
			jQuery(topRight).append(topLeft);
			jQuery(centerRight).append(centerLeft).append(jQuery.NiceJForms.textareas[q]);
			jQuery(bottomRight).append(bottomLeft);
			jQuery(container).append(topRight).append(centerRight).append(bottomRight);
			
			jQuery(where2).before(container);
			
			//create hovers
			$(jQuery.NiceJForms.textareas[q]).focus(function(){$(this).prev().removeClass().addClass("cntr_l_xon"); $(this).parent().removeClass().addClass("cntr_xon"); $(this).parent().prev().removeClass().addClass("tr_xon"); $(this).parent().prev().children(".txt_corner").attr('src', jQuery.NiceJForms.options.imagesPath + "txtarea_tl_xon.gif"); $(this).parent().next().removeClass().addClass("br_xon"); $(this).parent().next().children(".txt_corner").attr('src', jQuery.NiceJForms.options.imagesPath + "txtarea_bl_xon.gif")});
			$(jQuery.NiceJForms.textareas[q]).blur(function(){$(this).prev().removeClass().addClass("cntr_l"); $(this).parent().removeClass().addClass("cntr"); $(this).parent().prev().removeClass().addClass("tr"); $(this).parent().prev().children(".txt_corner").attr('src', jQuery.NiceJForms.options.imagesPath + "txtarea_tl.gif"); $(this).parent().next().removeClass().addClass("br"); $(this).parent().next().children(".txt_corner").attr('src', jQuery.NiceJForms.options.imagesPath + "txtarea_bl.gif")});
		});
	},
	
	replaceFiles: function() {
		jQuery.NiceJForms.files.each(function(q){
			
			var dummy;
			var file;
			var center;
			var clone;
			var left;
			var button;
			var self;
			var top;
			var oldClassName;
			
			self = this;
			
			oldClassName = $(this).attr('class');
			
			dummy = document.createElement('div');
			
			$(dummy).addClass("NFFile");
			
			file = document.createElement('div');
			
			$(file).addClass("NFFileNew");
			
			center = document.createElement('div');
			
			$(center).addClass("NFTextCenter");
			
			clone = document.createElement('input');
			
			$(clone)
				.attr('type', 'text')
				.addClass("NFText")
				.val($(this).val());
			
			//el.clone.ref = el;
			
			left = document.createElement('img');
			
			$(left)
				.attr({src:jQuery.NiceJForms.options.imagesPath + "0.png"})
				.addClass("NFTextLeft");
			
			button = document.createElement('img');
			
			$(button)
				.attr({src:jQuery.NiceJForms.options.imagesPath + "0.png"})
				.addClass("NFFileButton")
				.bind('click', function() {
					self.click();
				});

			//el.button.ref = el;
			
			top = $(this).parent();
			
			if ($(this).prev()) {
				var where = $(this).prev();
			}
			else {
				var where = $(top).children().first();
			}
			
			$(this).before(dummy, where);
			
			$(dummy).append(this);
			
			$(center).append(clone);
			
			$(file).append(center);
			
			$(center).before(left);
			
			$(file).append(button);
			
			$(dummy).append(file);
			
			$(this)
				.addClass("NFhidden")
				.bind('focus', function() {
					$(left).addClass("NFh");
					$(center).addClass("NFh");
					$(button).addClass("NFh");
				})			
				.bind('blur', function() {
					$(left).removeClass("NFh");
					$(center).removeClass("NFh");
					$(button).removeClass("NFh");
				})			
				.bind('select', function() {
					this.relatedElement.select();
					this.value = '';
				});			
			
			this.relatedElement = clone;
			
			$(jQuery.NiceJForms.files[q]).unload(function() {
				/*
				$(this).removeClass('NFh'); //$(this).removeClass().addClass("NFText");
				$(this).prev().attr('src', jQuery.NiceJForms.options.imagesPath + "input-left.gif");
				$(this).next().attr('src', jQuery.NiceJForms.options.imagesPath + "input-right.gif");
				*/
				$(this).parent().parent().append(this);
				$(this).parent().remove(dummy);
				$(this).attr('class', oldClassName);
			});			
			
			$(jQuery.NiceJForms.files[q]).change(function() {
				this.relatedElement.value = this.value;
			});			


		});
	},

	buttonHovers: function() {
		/*
		jQuery.NiceJForms.buttons.each(function(i){
			$(this).addClass("buttonSubmit");
			var buttonLeft = document.createElement('img');
			jQuery(buttonLeft).attr({src: jQuery.NiceJForms.options.imagesPath + "button_left.gif"}).addClass("buttonImg");
			
			$(this).before(buttonLeft);
			
			var buttonRight = document.createElement('img');
			jQuery(buttonRight).attr({src: jQuery.NiceJForms.options.imagesPath + "button_right.gif"}).addClass("buttonImg");
			
			if($(this).next()) {$(this).after(buttonRight)}
			else {$(this).parent().append(buttonRight)};
			
			$(this).hover(
				function(){$(this).attr("class", $(this).attr("class") + "Hovered"); $(this).prev().attr("src", jQuery.NiceJForms.options.imagesPath + "button_left_xon.gif"); $(this).next().attr("src", jQuery.NiceJForms.options.imagesPath + "button_right_xon.gif")},
				function(){$(this).attr("class", $(this).attr("class").replace(/Hovered/g, "")); $(this).prev().attr("src", jQuery.NiceJForms.options.imagesPath + "button_left.gif"); $(this).next().attr("src", jQuery.NiceJForms.options.imagesPath + "button_right.gif")}
			);
		});
		*/
		jQuery.NiceJForms.buttons.each(function(i){
			var el = this;
			
			var left, right;
			
			el.oldClassName = el.className;
			
			left = document.createElement('img');
			
			$(left)
				.addClass('NFButtonLeft')
				.attr('src', jQuery.NiceJForms.options.imagesPath + "0.png");
			
			right = document.createElement('img');
			
			$(right)
				.addClass('NFButtonRight')
				.attr('src', jQuery.NiceJForms.options.imagesPath + "0.png");
			/*
			$(this)
				.bind('click', {who: q, id:jQuery.NiceJForms.selects[q].id, option:w, select:q}, function(e){
					//self.showOptions(e);
					self.selectMe(jQuery.NiceJForms.selects[q].id, w, q);
					self.hideOptions(e);
				});
			*/
			
			el.onmouseover = function() {
				this.className = "NFButton NFh";
				$(left).addClass('NFButtonLeft NFh');
				$(right).addClass('NFButtonRight NFh');
			}
			el.onmouseout = function() {
				this.className = "NFButton";
				$(left).removeClass('NFButtonLeft NFh');
				$(left).addClass('NFButtonLeft');
				$(right).removeClass('NFButtonRight NFh');
				$(right).addClass('NFButtonRight');
			}
			/*
			el.init = function() {
				this.parentNode.insertBefore(this.left, this);
				this.parentNode.insertBefore(this.right, this.nextSibling);
				this.className = "NFButton";
			}
			*/
			el.unload = function() {
				this.parentNode.removeChild(this.left);
				this.parentNode.removeChild(this.right);
				this.className = this.oldClassName;
			}
			
			$(left).insertBefore(el);
			$(right).insertAfter(el);
			$(el).addClass('NFButton');
		});
	}
}

jQuery.preloadImages = function()
{
	var imgs = new Array();
	for(var i = 0; i<arguments.length; i++)
	{
		imgs[i] = jQuery("<img>").attr("src", arguments[i]);
	}
	
	return imgs;
}

jQuery.iUtil = {
	getPosition : function(e)
	{
		var x = 0;
		var y = 0;
		var restoreStyle = false;
		var es = e.style;
		if (jQuery(e).css('display') == 'none') {
			oldVisibility = es.visibility;
			oldPosition = es.position;
			es.visibility = 'hidden';
			es.display = 'block';
			es.position = 'absolute';
			restoreStyle = true;
		}
		var el = e;
		while (el){
			x += el.offsetLeft + (el.currentStyle && !jQuery.browser.opera ?parseInt(el.currentStyle.borderLeftWidth)||0:0);
			y += el.offsetTop + (el.currentStyle && !jQuery.browser.opera ?parseInt(el.currentStyle.borderTopWidth)||0:0);
			el = el.offsetParent;
		}
		el = e;
		while (el && el.tagName  && el.tagName.toLowerCase() != 'body')
		{
			x -= el.scrollLeft||0;
			y -= el.scrollTop||0;
			el = el.parentNode;
		}
		if (restoreStyle) {
			es.display = 'none';
			es.position = oldPosition;
			es.visibility = oldVisibility;
		}
		return {x:x, y:y};
	},
	getPositionLite : function(el)
	{
		var x = 0, y = 0;
		while(el) {
			x += el.offsetLeft || 0;
			y += el.offsetTop || 0;
			el = el.offsetParent;
		}
		return {x:x, y:y};
	}
};