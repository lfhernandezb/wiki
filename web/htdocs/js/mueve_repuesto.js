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
				
				// de doy focus
				otro.focus();
			}
			else
			{
				// re valido en combo
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
