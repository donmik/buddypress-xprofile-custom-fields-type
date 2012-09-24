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
                
				//add selector custom post type field type on Add/Edit Xprofile field admin screen
			   if(jQ("div#poststuff select#fieldtype").html() !== null){

					if(jQ('div#poststuff select#fieldtype option[value="select_custom_post_type"]').html() === null){
						var webOption = '<option value="select_custom_post_type">'+params.select_custom_post_type+'</option>';
						jQ("div#poststuff select#fieldtype").append(webOption);
                        // Add onchange event.
                        jQ('div#poststuff select#fieldtype').change(function() {
                            if (jQ(this).val() == 'select_custom_post_type') {
                                jQ('div#select_custom_post_type').show();
                            } else {
                                jQ('div#select_custom_post_type').hide();
                            }
                        })
					}					

				}
                
				//add multiselector custom post type field type on Add/Edit Xprofile field admin screen
			   if(jQ("div#poststuff select#fieldtype").html() !== null){

					if(jQ('div#poststuff select#fieldtype option[value="multiselect_custom_post_type"]').html() === null){
						var webOption = '<option value="multiselect_custom_post_type">'+params.multiselect_custom_post_type+'</option>';
						jQ("div#poststuff select#fieldtype").append(webOption);
                        // Add onchange event.
                        jQ('div#poststuff select#fieldtype').change(function() {
                            if (jQ(this).val() == 'multiselect_custom_post_type') {
                                jQ('div#multiselect_custom_post_type').show();
                            } else {
                                jQ('div#multiselect_custom_post_type').hide();
                            }
                        })
					}					

				}

			},
        
        select : function(type) {
            jQ('div#poststuff select#fieldtype option[value="'+type+'"]').attr('selected', 'selected');
            
            // Show option field if selector custom post type selected.
            if (type == 'select_custom_post_type') {
                jQ('#select_custom_post_type').show();
            }
            else if (type == 'multiselect_custom_post_type') {
                jQ('#multiselect_custom_post_type').show();                
            }
        }
        
		};
		
		jQ(document).ready(function(){
				bxcft.init();
		});
				
	}

)(jQuery);
