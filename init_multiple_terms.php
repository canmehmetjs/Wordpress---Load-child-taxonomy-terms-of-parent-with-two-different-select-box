
<form method="post" >


	<div id="loading_bar"></div>
	<div class="form-group">
		<span class="input_info"><?php _e('Make','theme_name');?></span>
		<select class="bonuin_select car_make " name="vehicle_make" id="vehicle_make" required>
		   <option value=""><?php _e('Select Make','theme_name');?></option>
		   <?php
		   // Display only parents here .
		   $terms = get_terms( array(
				// Put your taxonomy name  here.
				'taxonomy' => 'vehicle_make',
				'parent' => 0, 
				'hide_empty' => false,
			) );
			
			foreach ($terms as $term){?>
				<!-- We are going to send value for $_POST and data-makeId's TERM_ID for ajax request -->
				<option value="<?php echo $term->term_id;?>" data-makeId="<?php echo $term->term_id ?>"><?php echo $term->name;?></option> 
			<?php
			wp_reset_query(); // if you're not in your main loop! otherwise you can skip this
			} ?>
			
		   
		</select>    
		<script type="text/javascript">
				jQuery(function(){
					jQuery('#vehicle_make').change(function(){
						var $mainCat= jQuery(this).find(':selected').attr('data-makeId');
						if ($mainCat != '0' ){
							// call ajax
							jQuery("#vehicle_model").empty();
							jQuery.ajax
							(
								{
									url:"<?php bloginfo('wpurl'); ?>/wp-admin/admin-ajax.php",     
									type:'POST',
									data:'action=get_model_lists_ajax&main_catid=' + $mainCat,
									beforeSend:function()
									{
									 jQuery("#loading_bar").show();
									},
									success:function(results)
									{
										//  alert(results);
										jQuery("#loading_bar").hide();
										jQuery("#vehicle_model").removeAttr("disabled");       
										jQuery("#vehicle_model").append(results);  
									}
								}
							);   
						}
															 
					});
				});               
		</script>
	</div>
	<div class="form-group">
		<span class="input_info"><?php _e('Model','theme_name');?></span>
		<select class="bonuin_select car_make " name="vehicle_model" id="vehicle_model" disabled required>
			<option value=""><?php _e('Select Model','theme_name');?></option>
		</select>    
	</div>		

	
											

</form>




<?php 
// Put this code in your functions.php or in my case I've included a file in functions php that requires this kind of extra functions.

function get_models_list() {

// Check if Main Category Term iD is sent
if(isset($_POST['main_catid'])){
	
   // Provide Taxonomy name
   $taxonomy_name = 'vehicle_make';
   
   $term_id = $_POST['main_catid'];
   
	  // Check if Term Id is not empty
	   $option = '';
	   if ($term_id != ''){
			// Get all childs of Term id 
		   $uchildren =get_terms( $taxonomy_name, array(
				'hide_empty' => false, 
				'parent' => $term_id 
				)
			);
			
			// Loop each child term that will be send to Ajax results on success
			foreach ($uchildren as $term) {
				$option .= '<option value="'.$term->slug.'">';
				$option .= $term->name;
			   $option .= '</option>';
			}
		    echo '<option value="" selected="selected">Select a Model</option>'.$option;
		
		
	   }else{
		     echo '<option value="" selected="selected">Chose make first</option>'.$option;
	   }
		;
	die();
   } // end if
}
add_action('wp_ajax_get_model_lists_ajax', 'get_models_list');
add_action('wp_ajax_nopriv_get_model_lists_ajax', 'get_models_list');//for users that are not logged in.


?>