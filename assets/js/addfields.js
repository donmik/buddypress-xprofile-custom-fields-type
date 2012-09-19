(

	function(jQ){

		bxcft =
		{
		
		init : function(){
							
				//add birthdate field type on Add/Edit Xprofile field admin screen
			   if(jQ("div#poststuff select#fieldtype").html() !== null){

					if(jQ('div#poststuff select#fieldtype option[value="birthdate"]').html() === null){
						var birthdateOption = '<option value="birthdate">'+params.birthdate+'</option>';
						jQ("div#poststuff select#fieldtype").append(birthdateOption);
					}					

				}		
                
				//add email field type on Add/Edit Xprofile field admin screen
			   if(jQ("div#poststuff select#fieldtype").html() !== null){

					if(jQ('div#poststuff select#fieldtype option[value="email"]').html() === null){
						var emailOption = '<option value="email">'+params.email+'</option>';
						jQ("div#poststuff select#fieldtype").append(emailOption);
					}					

				}	
                
				//add web field type on Add/Edit Xprofile field admin screen
			   if(jQ("div#poststuff select#fieldtype").html() !== null){

					if(jQ('div#poststuff select#fieldtype option[value="web"]').html() === null){
						var webOption = '<option value="web">'+params.web+'</option>';
						jQ("div#poststuff select#fieldtype").append(webOption);
					}					

				}	
                
				//add datepicker field type on Add/Edit Xprofile field admin screen
			   if(jQ("div#poststuff select#fieldtype").html() !== null){

					if(jQ('div#poststuff select#fieldtype option[value="datepicker"]').html() === null){
						var webOption = '<option value="datepicker">'+params.datepicker+'</option>';
						jQ("div#poststuff select#fieldtype").append(webOption);
					}					

				}

			},
        
        select : function(type) {
            jQ('div#poststuff select#fieldtype option[value="'+type+'"]').attr('selected', 'selected');
        }
        
		};
		
		jQ(document).ready(function(){
				bxcft.init();
		});
				
	}

)(jQuery);
