	$(document).ready(function() {
		
	    //$('.ask').jConfirmAction();
		// crea datepickers
		new JsDatePick({
			useMode:2,
			target:"fecha_desde",
			dateFormat:"%Y-%m-%d",
			imgPath:"jsdatepick-calendar/img/"
			/*
			selectedDate:{				
				day:1,						
				month:1,
				year:2012
			},
			yearsRange:[2012,2020],
			limitToToday:false,
			cellColorScheme:"ocean_blue",
			weekStartDay:1*/
		});

		new JsDatePick({
			useMode:2,
			target:"fecha_hasta",
			dateFormat:"%Y-%m-%d",
			imgPath:"jsdatepick-calendar/img/"
			/*selectedDate:{				This is an example of what the full configuration offers.
				day:5,						For full documentation about these settings please see the full version of the code.
				month:9,
				year:2006
			},
			yearsRange:[1978,2020],
			limitToToday:false,
			cellColorScheme:"beige",
			dateFormat:"%m-%d-%Y",
			imgPath:"img/",
			weekStartDay:1*/
		});

		//$('.ask').jConfirmAction();
		

		$('#motivo').change(function() {

			var sel = $(this).val().toString();
			var id_razon = $('#id_razon');
			
			// alert(sel);
			
			if (sel == 0) {
				// seleccionado titulo
				id_razon.attr('disabled', true);
				id_razon.text('');
				id_razon.parent().parent().parent().hide();
			}
			else
			{
				// motivo valido en combo
				id_razon.attr('disabled', false);
				id_razon.parent().parent().parent().show();
				id_razon.focus();
			}

		}); // end change
		
		$('#dialog').dialog({
		    autoOpen: false,
		    height: 280,
		    modal: true,
		    resizable: false,
		    buttons: {
		    	'Cerrar': function() {
		    		$(this).dialog('close');
		      	} /*,
		    	'Change Rating': function() {
		    		$(this).dialog('close');
		      		// Update Rating
		    	}
		    	*/
		  	}
		});

		// NFInit();
		
	    
		$.NiceJForms.build({
			imagesPath:"nicejforms/css/images/default/"
		});
					    
	}); // end ready
	
	
