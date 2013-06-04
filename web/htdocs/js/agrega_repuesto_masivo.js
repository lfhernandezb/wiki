	$(document).ready(function() {
		
	    //$('.ask').jConfirmAction();
		
		$('#agrega_repuesto').validate({
			rules: {
				upload: 'required'
			}, // end rules
			messages: {
				upload: {
					required: "Favor especifique archivo a procesar"
				}
			}, // end messages
			errorPlacement: function(error, element) {
				if (element.is(":radio") || element.is(":checkbox")) {
					error.appendTo( element.parent());
				} else if (element.is(":text") || element.is(":file")) {
					error.insertAfter(element.parent().next());
				} else {
					error.insertAfter(element);
				}
			} // end errorPlacement			
		}); // end validate()
		/*
        <{if isset($exito)}>
           <{if $exito}>
			<{/if}>
			
		<{else}>
		<{/if}>
		*/
		$.NiceJForms.build({
			imagesPath:"nicejforms/css/images/default/"
		});
		
	});
