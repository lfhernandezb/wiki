	$(document).ready(function() {
		
	    //$('.ask').jConfirmAction();

		// example, custom validation rule - text only
		$.validator.addMethod("textOnly", 
			function(value, element) {
				return !/[0-9]*/.test(value);
			}, 
		"Alpha Characters Only."
		); // end addMethod


		$('#edita_usuario').validate({
			rules: {
				nombre_usuario: {
					required: true,
					uniqueUsername: true
				},
				nombre: 'required',
				email: {
					required: true,
					//email: true,
					uniqueEmail: true
				}
			}, // end rules
			messages: {
				nombre_usuario: {
					required: "Favor ingrese 'username'",
					uniqueUsername: "El username ya existe en el sistema."
				},
				nombre: {
					required: "Favor ingrese nombre"
				},
				email: {
					required: "Favor ingrese email",
					email: "El email ingresado no es v&aacute;lido",
					uniqueEmail: "Ya existe un usuario con ese email."
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
