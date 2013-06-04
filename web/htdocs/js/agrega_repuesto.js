	$(document).ready(function() {
		
	    //$('.ask').jConfirmAction();

		/*
		$('#agrega_repuesto').validate({
			rules: {
				plataforma: 'required',
				region: 'required',
				ciudad: 'required',
				re: 'required',
				fabricante: 'required',
				modelo: 'required',
				descripcion: 'required',
				serial: 'required',
				nombre: 'required',
				sla: 'required'
			}, // end rules
			messages: {
				plataforma: {
					required: "Favor seleccione plataforma"
				},
				region: {
					required: "Favor seleccione regi&oacute;n"
				},
				ciudad: {
					required: "Favor seleccione ciudad"
				},
				re: {
					required: "Favor seleccione R/E"
				},
				fabricante: {
					required: "Favor seleccione fabricante"
				},
				modelo: {
					required: "Favor seleccione modelo"
				},
				descripcion: {
					required: "Favor ingrese descripci&oacute;n"
				},
				serial: {
					required: "Favor ingrese serial"
				},
				nombre: {
					required: "Favor ingrese nombre"
				},
				sla: {
					required: "Favor ingrese sla"
				}
			}, // end messages
			errorPlacement: function(error, element) {
				if ( element.is(":radio") || element.is(":checkbox")) {
					error.appendTo( element.parent());
				} else if ( element.is(":text") ) {
					error.insertAfter(element.parent().next());
				} else {
					error.insertAfter(element);
				}
			} // end errorPlacement			
		}); // end validate()
		*/
		/*
		$('select.error,:text.error').change(function() {
			$(this).removeClass('error');
			$('.error').remove();
		});
		*/
		/*
		$(':text').change(function() {
			$('.error').remove();
		});
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

		// si se ingresa serial, la cantidad de equipos es 1, y no se puede cambiar
		$('#serial').blur(function() {
			var cantidad = $('#cantidad');
			
			if ($(this).val().toString() == '') {
				$(cantidad).val('1');
				$(cantidad).attr('disabled', true)
			}
			else {
				$(cantidad).attr('disabled', false)				
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

		$('#plataforma').change(function() {

			var sel = $(this).val().toString();
			var otro = $('#otro_plataforma');
			
			// alert(sel);
			
			if ($(this).hasClass('error')) {
				$(this).removeClass('error');
				$('.error').remove();
			}

			if (sel == 0) {
				// seleccionado titulo
				otro.parent().parent().parent().hide();
			}
			else if (sel == 9999) {
				// seleccionado 'otra', presento text niceform
				otro.parent().parent().parent().show();
				
				// le doy focus
				otro.focus();
			}
			else
			{
				// re valido en combo
				otro.parent().parent().parent().hide();
			}

		}); // end change
		

		$('#re').change(function() {

			var sel = $(this).val().toString();
			var otro = $('#otro_re');
			
			// alert(sel);
			
			if ($(this).hasClass('error')) {
				$(this).removeClass('error');
				$('.error').remove();
			}

			if (sel == 0) {
				// seleccionado titulo
				otro.parent().parent().parent().hide();
			}
			else if (sel == 9999) {
				// seleccionado 'otra', presento text niceform
				otro.parent().parent().parent().show();
				
				// le doy focus
				otro.focus();
			}
			else
			{
				// re valido en combo
				otro.parent().parent().parent().hide();
			}

		}); // end change

		$('#modelo').change(function() {

			var sel = $(this).val().toString();
			var otro = $('.otro_modelo');
			
			// alert(sel);
			
			if ($(this).hasClass('error')) {
				$(this).removeClass('error');
				$('.error').remove();
			}

			if (sel == 0) {
				// seleccionado titulo
				otro.parent().parent().parent().hide();
			}
			else if (sel == 9999) {
				// seleccionado 'otra', presento text input
				otro.parent().parent().parent().show();
				$('#otro_pid').focus();
			}
			else
			{
				// re en combo
				otro.parent().parent().parent().hide();
			}

		}); // end change

		$('#encargado').change(function() {

			var sel = $(this).val().toString();
			var otro = $('#otro_encargado');
			
			// alert(sel);
			
			if ($(this).hasClass('error')) {
				$(this).removeClass('error');
				$('.error').remove();
			}

			if (sel == 0) {
				// seleccionado titulo
				otro.parent().parent().parent().hide();
			}
			else if (sel == 9999) {
				// seleccionado 'otra', presento text niceform
				otro.parent().parent().parent().show();
				
				// le doy focus
				otro.focus();
			}
			else
			{
				// re valido en combo
				otro.parent().parent().parent().hide();
			}

		}); // end change

		$.NiceJForms.build({
			imagesPath:"nicejforms/css/images/default/"
		});
				
	}); // end ready
