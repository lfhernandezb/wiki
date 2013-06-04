	$(document).ready(function() {
		
		//$('.ask').jConfirmAction();
				
		// paginacion
		
		// se desean a lo mas 10 paginas
		var desired_pages = 10;
		
	    //getting the amount of elements inside content div  
	    var number_of_items = $('tbody').children().size();  

	    //how much items per page to show  
	    var show_per_page = Math.max(Math.ceil(number_of_items/desired_pages), 5);  
	    
	    //calculate the number of pages we are going to have  
	    var number_of_pages = Math.ceil(number_of_items/show_per_page);  
	  
	    //set the value of our hidden input fields  
	    $('#current_page').val(0);  
	    $('#show_per_page').val(show_per_page);  
	  
	    //now when we got all we need for the navigation let's make it '  
	  
	    /* 
	    what are we going to have in the navigation? 
	        - link to previous page 
	        - links to specific pages 
	        - link to next page 
	    */
	    
	    /*
	     <span class="disabled"><< prev</span>
	     <span class="current">1</span>
	     <a href="">2</a>
	     <a href="">3</a>
	     <a href="">4</a>
	     <a href="">5</a>…<a href="">10</a><a href="">11</a><a href="">12</a>...<a href="">100</a><a href="">101</a><a href="">next >></a>
	     */
	    /* original
	    var navigation_html = '<a class="previous_link" href="javascript:previous();">Prev</a>';  
	    var current_link = 0;  
	    while(number_of_pages > current_link){  
	        navigation_html += '<a class="page_link" href="javascript:go_to_page(' + current_link +')" longdesc="' + current_link +'">'+ (current_link + 1) +'</a>';  
	        current_link++;  
	    }  
	    navigation_html += '<a class="next_link" href="javascript:next();">Next</a>';  
	    */
	    
	    var navigation_html = '<a class="previous_link" href="javascript:previous();"><< prev</a>';  
	    var current_link = 0;  
	    while(number_of_pages > current_link){  
	        navigation_html += '<a class="page_link" href="javascript:go_to_page(' + current_link +')" longdesc="' + current_link +'">'+ (current_link + 1) +'</a>';  
	        current_link++;  
	    }  
	    navigation_html += '<a class="next_link" href="javascript:next();">next >></a>';  
	    
	    $('.pagination').html(navigation_html);  
	  
	    //add active_page class to the first page link  
	    $('.pagination .page_link:first').addClass('current');  
	  
	    //hide all the elements inside content div  
	    $('tbody').children().hide(); //css('display', 'none');  
	  
	    //and show the first n (show_per_page) elements  
	    $('tbody').children().slice(0, show_per_page).show(); //css('display', 'block');
	    
	    // fin paginacion
	    
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

		$.NiceJForms.build({
			imagesPath:"nicejforms/css/images/default/"
		});
	});
	
	// paginacion
	
	function previous(){  
		  
	    new_page = parseInt($('#current_page').val()) - 1;  
	    //if there is an item before the current active link run the function  
	    if($('.current').prev('.page_link').length==true){  
	        go_to_page(new_page);  
	    }  
	  
	}  
	  
	function next(){  
	    new_page = parseInt($('#current_page').val()) + 1;  
	    //if there is an item after the current active link run the function  
	    if($('.current').next('.page_link').length==true){  
	        go_to_page(new_page);  
	    }  
	  
	}  
	function go_to_page(page_num){  
	    //get the number of items shown per page  
	    var show_per_page = parseInt($('#show_per_page').val());  
	  
	    //get the element number where to start the slice from  
	    start_from = page_num * show_per_page;  
	  
	    //get the element number where to end the slice  
	    end_on = start_from + show_per_page;  
	  
	    //hide all children elements of content div, get specific items and show them  
	    //$('tbody').children().css('display', 'none').slice(start_from, end_on).css('display', 'block');  
	    $('tbody').children().hide().slice(start_from, end_on).show(); //css('display', 'block');
	  
	    /*get the page link that has longdesc attribute of the current page and add active_page class to it 
	    and remove that class from previously active page link*/  
	    $('.page_link[longdesc=' + page_num +']').addClass('current').siblings('.current').removeClass('current');  
	  
	    //update the current page input field  
	    $('#current_page').val(page_num);  
	}  
	
	// fin paginacion
	
    function exportar() {
    	/*
        $("#export_plataforma").val($("#plataforma").val());
        $("#export_region").val($("#region").val());
        $("#export_ciudad").val($("#ciudad").val());
        $("#export_re").val($("#re").val());

        $("#export_fabricante").val($("#fabricante").val());
        $("#export_modelo").val($("#modelo").val());
        */
        $("#export").submit();
    }

	
	/*
	function eliminaRepuesto(id_repuesto) {
		var self = this;
	
	    if($(this).next('div.question').length <= 0)  
	        $(this).after('<div class="question">Are you sure?<br> <span class="yes">Yes</span><span class="cancel">Cancel</span></div>');  
	  
	    $('.question').animate({opacity: 1}, 300);  
	  
	    $('.yes').live('click', function(){  
			// 
			var data;
			var str;
			
	        $.ajax({
	             async: false,
	             url: "<{$receiver}>?do=Ajax&req=delRepuesto&id_repuesto=" + id_repuesto,
	             type: 'GET',
	             dataType: 'json',
	             error: function(xhr){
	             	alert("An error occured: " + xhr.status + " " + xhr.statusText);
	             },
	             success: function(output_string){
	             	//alert(output_string);
	                 data = output_string;
	             }
	        });
	
	        if (data.respuesta == '1') {
	        	$(self).parent().parent().hide();
	        }
	    });  
	  
	    $('.cancel').live('click', function(){  
	        $(this).parents('div.question').fadeOut(300, function() {  
	            $(this).remove();  
        	});  	
		});
	}	
	*/
