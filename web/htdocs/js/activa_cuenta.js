	$(document).ready(function() {
		
	    //$('.ask').jConfirmAction();

		$('#login').validate({
			rules: {
				password: 'required',
				new_password: {
					required: true,
					minlength: 6
				},
				confirm_password: {
					equalTo: "#new_password"
				}
			}, // end rules
			messages: {
				password: {
					required: "Favor ingrese contrase&ntilde;a actual"
				},
				new_password: {
					required: "Favor ingrese nueva contrase&ntilde;a"
				},
				confirm_password: {
					equalTo: "Favor repita la nueva contrase&ntilde;a"
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

		$('#password').focus();
		
	}); // end ready
	
