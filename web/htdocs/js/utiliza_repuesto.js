	$(document).ready(function() {
		
	    //$('.ask').jConfirmAction();
		/*
		$('#usa_repuesto').validate({
			rules: {
				posicion: 'required',
				ubicacion: 'required'
			}, // end rules
			messages: {
				posicion: {
					required: "Favor ingrese posici&oacute;n"
				},
				ubicacion: {
					required: "Favor ingrese ubicaci&oacute;n"
				}
			}, // end messages
			errorPlacement: function(error, element) {
				if ( element.is(":radio") || element.is(":checkbox")) {
					error.appendTo( element.parent());
				} else {
					error.insertAfter(element);
				}
			} // end errorPlacement			
		}); // end validate()
		*/
				
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

		$('#motivo').change(function() {
			if ($(this).hasClass('error')) {
				$(this).removeClass('error');
				$('.error').remove();
			}
		});
		
		// solamente se aceptan numeros en textboxes de esta clase
        $('.number_input').keydown(function(e)
        {
            var key = e.charCode || e.keyCode || 0;
            // allow backspace, tab, delete, arrows, numbers and keypad numbers ONLY
            return (
                key == 8 || 
                key == 9 ||
                key == 46 ||
                (key >= 37 && key <= 40) ||
                (key >= 48 && key <= 57) ||
                (key >= 96 && key <= 105));
        });		
		
		
		$.NiceJForms.build({
			imagesPath:"nicejforms/css/images/default/"		
		});

		$('#posicion').focus();

	}); // end ready
