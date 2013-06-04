	$(document).ready(function() {
		
	    //$('.ask').jConfirmAction();

		$('#edita_usuario').validate({
			rules: {
				nombre_usuario: 'required',
				nombre: 'required',
				email: {
					required: true,
					email: true
				}
			}, // end rules
			messages: {
				nombre_usuario: {
					required: "Favor ingrese 'username'"
				},
				nombre: {
					required: "Favor ingrese nombre"
				},
				email: {
					required: "Favor ingrese email",
					email: "El email ingresado no es v&aacute;lido"
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

		$.NiceJForms.build({
			imagesPath:"nicejforms/css/images/default/"
		});

		$('#nombre_usuario').focus();

	}); // end ready
