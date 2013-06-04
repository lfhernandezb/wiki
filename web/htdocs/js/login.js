	$(document).ready(function() {
		
	    //$('.ask').jConfirmAction();

		$('#login').validate({
			rules: {
				usr: 'required',
				pwd: 'required',
				email: {
					required:true,
					email:true
				}
			}, // end rules
			messages: {
				usr: {
					required: "Favor ingrese nombre de usuario"
				},
				pwd: {
					required: "Favor ingrese contrase&ntilde;a"
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

		$('#usr').focus();
		
	}); // end ready
