	$(document).ready(function() {
		
		//$('.ask').jConfirmAction();
		/*
		$('#establece_umbral').submit(function(e) {
			var element;
			var val;
			var message;
			var previousElement;
			var data;

			var texts = ['umbral'];		
			var success =  true;
			var errorElement = document.createElement('label');
			
			$(errorElement).addClass('error');
			
			
			$(texts).each(function() {
				if (success) {
					element = $('#'+this);
	
					if ($(element).val() == '') {
						message = 'Favor ingrese '+this;
						previousElement = element;
						success = false;
					}
				}
			}); // end each
			
			
			
			if (!success) {
				$(element).addClass('error');
				$(errorElement).text(message);
				$(errorElement).insertAfter(previousElement);
				$(element).focus();
			}
			
			return success;
		}); // end submit
		*/
		/*
		$.validator.addMethod('require-one', function (value) {
				return $('.require-one:checked').size() > 0; 
			}, 
			'Favor escoja al menos un usuario.'
		);
		
		var checkboxes = $('.require-one');
		var checkbox_names = $.map(checkboxes, function(e,i) { return $(e).attr("name")}).join(" ");
		*/
		/*
		$('#agrega_zona').validate({
			rules: {
				zona: {
					required: true,
					number: true
				}
			}, // end of rules
			messages: {
				zona: {
					required: "Favor ingrese la descripci&oacute;n de la zona",
					number: "El umbral debe ser un valor num&eacute;rico"
				}
			}, // end of messages
		    groups: { checks: checkbox_names },
		    errorPlacement: function(error, element) {
		             if (element.attr("type") == "checkbox")
		               error.insertAfter(checkboxes[checkboxes.size() - 1]);
		             else
		               error.insertAfter(element);
		    } 			
		}); // end validate
		*/
		/*
		$('#establece_umbral').validate({
			rules: {
				umbral: {
					required: true,
					number: true
				}
			}, // end of rules
			messages: {
				umbral: {
					required: "Favor ingrese el umbral de existencias",
					number: "El umbral debe ser un valor num&eacute;rico"
				}
			}, // end of messages
		    groups: { checks: checkbox_names },
		    errorPlacement: function(error, element) {
		             if (element.attr("type") == "checkbox")
		               error.insertAfter(checkboxes[checkboxes.size() - 1].nextSibling.nextSibling);
		             else
		               error.insertAfter(element);
		    } 			
		}); // end validate
		*/		
		
		// NFInit();
		
		$(':text').keyup(function() {
			
			if ($(this).hasClass('error') && $(this).val() != '') {
				$(this).removeClass('error');
				$('.error').remove();
			}
		});

		$(':text').blur(function() {
			
			if ($(this).hasClass('error') && $(this).val() != '') {
				$(this).removeClass('error');
				$('.error').remove();
			}
		});
	    
		$.NiceJForms.build({
			imagesPath:"nicejforms/css/images/default/"
		});
	    
	    $('#zona').focus();
	}); // end ready	
	
	function goBack() {
		window.history.back()
	}
