(
    function(jQ) {
        bxcft =
		{
    		init : function() {
                if (jQ("div#poststuff select#fieldtype").html() !== null) {
                    //add birthdate field type on Add/Edit Xprofile field admin screen
					if (jQ('div#poststuff select#fieldtype option[value="birthdate"]').html() === undefined ||
                        jQ('div#poststuff select#fieldtype option[value="birthdate"]').html() == null) {
                        var birthdateOption = '<option value="birthdate">'+params.birthdate+'</option>';
						jQ("div#poststuff select#fieldtype").append(birthdateOption);
                        // Add onchange event.
                        jQ('div#poststuff select#fieldtype').change(function() {
                            if (jQ(this).val() == 'birthdate') {
                                jQ('div#birthdate').show();
                            } else {
                                jQ('div#birthdate').hide();
                            }
                        });
					}					
                
                    //add email field type on Add/Edit Xprofile field admin screen
					if (jQ('div#poststuff select#fieldtype option[value="email"]').html() === undefined ||
                        jQ('div#poststuff select#fieldtype option[value="email"]').html() == null) {
						var emailOption = '<option value="email">'+params.email+'</option>';
						jQ("div#poststuff select#fieldtype").append(emailOption);
					}					
                
                    //add web field type on Add/Edit Xprofile field admin screen
					if (jQ('div#poststuff select#fieldtype option[value="web"]').html() === undefined ||
                        jQ('div#poststuff select#fieldtype option[value="web"]').html() == null){
                        var webOption = '<option value="web">'+params.web+'</option>';
						jQ("div#poststuff select#fieldtype").append(webOption);
					}					
                
                    //add datepicker field type on Add/Edit Xprofile field admin screen
					if (jQ('div#poststuff select#fieldtype option[value="datepicker"]').html() === undefined ||
                        jQ('div#poststuff select#fieldtype option[value="datepicker"]').html() == null){
                        var datepickerOption = '<option value="datepicker">'+params.datepicker+'</option>';
						jQ("div#poststuff select#fieldtype").append(datepickerOption);
					}					
                
                    //add selector custom post type field type on Add/Edit Xprofile field admin screen
					if (jQ('div#poststuff select#fieldtype option[value="select_custom_post_type"]').html() === undefined ||
                        jQ('div#poststuff select#fieldtype option[value="select_custom_post_type"]').html() == null){
                        var customposttypeOption = '<option value="select_custom_post_type">'+params.select_custom_post_type+'</option>';
                        jQ("div#poststuff select#fieldtype").append(customposttypeOption);
                        // Add onchange event.
                        jQ('div#poststuff select#fieldtype').change(function() {
                            if (jQ(this).val() == 'select_custom_post_type') {
                                jQ('div#select_custom_post_type').show();
                            } else {
                                jQ('div#select_custom_post_type').hide();
                            }
                        });
					}					
                
                    //add multiselector custom post type field type on Add/Edit Xprofile field admin screen
					if (jQ('div#poststuff select#fieldtype option[value="multiselect_custom_post_type"]').html() === undefined ||
                        jQ('div#poststuff select#fieldtype option[value="multiselect_custom_post_type"]').html() == null){
                        var multicustomposttypeOption = '<option value="multiselect_custom_post_type">'+params.multiselect_custom_post_type+'</option>';
						jQ("div#poststuff select#fieldtype").append(multicustomposttypeOption);
                        // Add onchange event.
                        jQ('div#poststuff select#fieldtype').change(function() {
                            if (jQ(this).val() == 'multiselect_custom_post_type') {
                                jQ('div#multiselect_custom_post_type').show();
                            } else {
                                jQ('div#multiselect_custom_post_type').hide();
                            }
                        })
					}					
                
                    //add checkbox acceptance field type on Add/Edit Xprofile field admin screen
					if (jQ('div#poststuff select#fieldtype option[value="checkbox_acceptance"]').html() === undefined || 
                        jQ('div#poststuff select#fieldtype option[value="checkbox_acceptance"]').html() == null){
						var checkboxAcceptanceOption = '<option value="checkbox_acceptance">'+params.checkbox_acceptance+'</option>';
						jQ("div#poststuff select#fieldtype").append(checkboxAcceptanceOption);
                    }
                
                    //add image field type on Add/Edit Xprofile field admin screen
					if (jQ('div#poststuff select#fieldtype option[value="image"]').html() === undefined ||
                        jQ('div#poststuff select#fieldtype option[value="image"]').html() == null) {
                        var imageOption = '<option value="image">'+params.image+'</option>';
						jQ("div#poststuff select#fieldtype").append(imageOption);
					}					
                
                    //add file field type on Add/Edit Xprofile field admin screen
					if (jQ('div#poststuff select#fieldtype option[value="file"]').html() === undefined ||
                        jQ('div#poststuff select#fieldtype option[value="file"]').html() == null){
						var fileOption = '<option value="file">'+params.file+'</option>';
						jQ("div#poststuff select#fieldtype").append(fileOption);
					}				
                
                    //add color field type on Add/Edit Xprofile field admin screen
					if (jQ('div#poststuff select#fieldtype option[value="color"]').html() === undefined ||
                        jQ('div#poststuff select#fieldtype option[value="color"]').html() == null){
						var colorOption = '<option value="color">'+params.color+'</option>';
						jQ("div#poststuff select#fieldtype").append(colorOption);
                    }				
                
                    //add number field type on Add/Edit Xprofile field admin screen
					if (jQ('div#poststuff select#fieldtype option[value="number"]').html() === undefined ||
                        jQ('div#poststuff select#fieldtype option[value="number"]').html() == null){
						var numberOption = '<option value="number">'+params.number+'</option>';
						jQ("div#poststuff select#fieldtype").append(numberOption);
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
                else if (type == 'birthdate') {
                    jQ('#birthdate').show();    
                }
            }
		};
		
		jQ(document).ready(function(){
            bxcft.init();
		});
	}
)(jQuery);
