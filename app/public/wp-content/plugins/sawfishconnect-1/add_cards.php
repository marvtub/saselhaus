<?php
/* Admin Submenu Page */
function sforc_add_card() //generator to build shortcode
{
	wp_enqueue_script( 'jquery-ui-sortable');
	wp_enqueue_script( 'jquery-ui-autocomplete');


	?>
	<script>
		function sf28c_processShortCodeHTML(ftext) {
			    var ret = ftext.replace(/>/g,'&gt;'); 
			    ret = ret.replace(/</g, '&lt;');
			    ret = ret.replace(/"/g, '&quot;');
			    ret = ret.replace(/[[]/g, '&#091;');
			    ret = ret.replace(/]/g, '&#093;');


 				return ret;
		};

		jQuery(document).ready(function() {
			var sforcShort=
			{
				sobject:"",
				fields:[],
				orderby:"",
				order:"",
				number:"",
				search:"",
				qfilter:"",
				viewType:"",
				cat:"",
				endDate:"",
				eventLink: "",
				rearrange:[],
				shortcode:"",
				calfilterfields:[],
				calpopupfields: []
			};

			jQuery(function() {

				jQuery('#menu-to-edit').sortable({
					start: function(event, ui) {},
					change: function(event, ui) {},
					update: function(event, ui) {

						updateqfields();
						jQuery('#objectname').html(jQuery("#objectselect").val());
					}
				});
			});

			function updateqfields()
			{
				var sortedIDs = jQuery("#menu-to-edit").sortable("toArray");
				var soqlquery = sortedIDs.toString().split(',');

				sforcShort.rearrange = sortedIDs;

				shorttext = buildShortcode(sforcShort);
				jQuery("#dynshortcode").text(shorttext);

			}

			<?php

			$fieldsget = new sf28c_Curl();


			if( isset($_GET['o']) && !is_null($_GET['o']) )
			{
				$sforc_fieldresponse = $fieldsget->getCurl('fields',array('object'=>$_GET['o']));

				echo 'jQuery("#objectselect").val("'.$_GET["o"].'").change();
				sforcShort.sobject="'.$_GET["o"].'";';
			}
			else if( !isset($_GET['o']) || is_null($_GET['o']) )
			{
				$sforc_fieldresponse = $fieldsget->getCurl('fields',array('object'=>'Contact'));

				echo 'jQuery("#objectselect").val("Contact").change();';
			}

			if( !isset( $sforc_fieldresponse['error'] ) )
			{
				$sforc_fieldsarray = sf28c_Response::format($sforc_fieldresponse,'fields');
				$date_timearray = sf28c_Response::format($sforc_fieldresponse,'date_time_fields');

				$sforc_objget = new sf28c_Curl();
				$sforc_objresponse = $sforc_objget->getCURL('objects');
				$sforc_objectsarray = sf28c_Response::format($sforc_objresponse,'objects');

				$standardsoql = array('OR' => 'OR', 'AND' => 'AND', 'NULL' => 'NULL', 'LIKE' => 'LIKE', '$WPId' => 'WordPress User ID (Logged in User ID)', '\'$WPEmail\'' => 'WordPress User Email (Logged in User Email)', '\'$WPUserName\'' => 'WordPress User Name (Logged in User Name)');
				$fields_withsoql = array_merge($sforc_fieldsarray, $standardsoql);
				$fieldlablestoname_withsoql = array_flip($fields_withsoql);

			}

			?>

		    		//Set Defaults
		    		jQuery("#queryorderby")[0].selectedIndex = 0;
		    		jQuery('#setorderby').html(jQuery("#queryorderby").val());
		    		jQuery('#setlimit').html(jQuery("#querylimit").val());
		    		jQuery('#objectname').html(jQuery("#objectselect").val());

		    		sforcShort.orderby = jQuery("#queryorderby").val();
		    		sforcShort.number = jQuery("#querylimit").val();
		    		sforcShort.sobject = jQuery("#objectselect").val();




		    		jQuery("#queryorder,#queryorderby,#querylimit,#querysearch,#queryfilter,input[name=displayas],#calendareventcat,#calendarenddate,#calendareventlink").change(function() {

		    			sforcShort.order=jQuery("#queryorder").val();
		    			sforcShort.orderby=jQuery("#queryorderby").val();
		    			sforcShort.number=jQuery("#querylimit").val();
		    			sforcShort.search=jQuery("#querysearch").val();
		    			sforcShort.qfilter=sf28c_processShortCodeHTML(jQuery("#queryfilter").val());
		    			sforcShort.viewType=jQuery('input[name=displayas]:checked').val(); 
		    			sforcShort.cat=jQuery("#calendareventcat").val();
		    			sforcShort.eventLink=jQuery("#calendareventlink").val();
		    			sforcShort.endDate=jQuery("#calendarenddate").val();

		    			if(sforcShort.fields.length !== 0 || sforcShort.viewType === '3')
		    			{
		    				shorttext = buildShortcode(sforcShort);
 		    				jQuery("#dynshortcode").text(shorttext);
		    			}


		    		});


		    		jQuery("#queryorder").change(function() {


		    			if(jQuery("#queryorder").val() === 'DESC')
		    				jQuery('#setorder').html('order="'+jQuery("#queryorder").val()+'"');
		    			else
		    				jQuery('#setorder').html('');				
		    		});

		    		jQuery("#queryorderby").change(function() {


		    			jQuery('#setorderby').html(jQuery("#queryorderby").val());					
		    		});

		    		jQuery("#querylimit").change(function() {


		    			jQuery('#setlimit').html(jQuery("#querylimit").val());					
		    		});

		    		jQuery("#querysearch").change(function() {

		    			sforcShort.search = jQuery("#querysearch").val();

		    			if(jQuery("#querysearch").val() === 'off')
		    				jQuery('#setsearch').html('search='+'"'+jQuery("#querysearch").val()+'"');					
		    			else
		    				jQuery('#setsearch').html('');	


		    		});

			    		jQuery("#queryfilter").on('change', function() { //add keyup focusout if needed



			    			jQuery('#setfilter').html('filter='+'"'+sf28c_processShortCodeHTML(jQuery("#queryfilter").val())+'"');	
			    		});

			    		jQuery('input[name=displayas]').change(function() {

			    			if(jQuery('input[name=displayas]:checked').val()==='2')
			    			{	
			    				jQuery("#searchoptrow").hide('slow');
			    				jQuery(".xtra-calendar-fields").show('slow');
			    			}
			    			else
			    			{
			    				jQuery("#searchoptrow").show('slow');
			    				jQuery(".xtra-calendar-fields").hide('slow');
			    			}



			    			if(jQuery('input[name=displayas]:checked').val()==='1')
			    				jQuery('#setsforcview').html('t='+'"'+jQuery('input[name=displayas]:checked').val()+'"');
			    			else if(jQuery('input[name=displayas]:checked').val()==='2')
			    				jQuery('#setsforcview').html('t='+'"'+jQuery('input[name=displayas]:checked').val()+'"');
			    			else
			    				jQuery('#setsforcview').html('');					
			    		});  


			    		jQuery(".ofields").change(function() {
							// If checked
							var value = jQuery(this).val(),
							$list = jQuery("#menu-to-edit");
							$calfilter = jQuery("#calenderfiltercheckboxes");
							$calpopup = jQuery("#calendarpopupcheckboxes");

							if (this.checked) {

								sforcShort.fields.push(value);

								//add to the right
								$list.append("<li class='menu-item' id='" + value + "'><div class='menu-item-bar'><div class='menu-item-handle'><span class='item-title'> <span class='menu-item-title'>" + jQuery('.ofields:checkbox[value="' + value + '"]').parent().text() + "</span> </span></div></div></li>");
								

								$calfilter.append("<li id='" + value + "'><label class='menu-item-title'><input type='checkbox' class='menu-item-checkbox caladdfilters' value='" + value + "'>" + jQuery('.ofields:checkbox[value="' + value + '"]').parent().text() + "</label></li>");					 

								$calpopup.append("<li id='" + value + "'><label class='menu-item-title'><input type='checkbox' class='menu-item-checkbox caladdpopup' value='" + value + "'>" + jQuery('.ofields:checkbox[value="' + value + '"]').parent().text() + "</label></li>");					 


							} else {


								if (sforcShort.fields.indexOf(value) > -1) {
									sforcShort.fields.splice( sforcShort.fields.indexOf(value), 1 );
								}

								$list.find('li[id="' + value + '"]').slideUp("fast", function() {
									jQuery(this).remove();

								});


								if (sforcShort.calfilterfields.indexOf(value) > -1) {
									sforcShort.calfilterfields.splice( sforcShort.calfilterfields.indexOf(value), 1 );
								}


								$calfilter.find('li[id="' + value + '"]').slideUp("fast", function() {
									jQuery(this).remove();
								});

								
								if (sforcShort.calpopupfields.indexOf(value) > -1) {
									sforcShort.calpopupfields.splice( sforcShort.calpopupfields.indexOf(value), 1 );
								}

								$calpopup.find('li[id="' + value + '"]').slideUp("fast", function() {
									jQuery(this).remove();
								});




							}

							updateqfields();


							var sforcfieldschecked = jQuery('.ofields:checkbox:checked').length;

							if(sforcfieldschecked !== 0) 
								jQuery('#sforcshortcoderow').show('slow');
							else
								jQuery('#sforcshortcoderow').hide('slow');
							

						});


			    		jQuery('#calenderfiltercheckboxes').on('change', '.caladdfilters', function()
			    		{
			    			var value = jQuery(this).val();
			    			if (this.checked) {
			    				sforcShort.calfilterfields.push(value);			

			    			} else {

			    				if (sforcShort.calfilterfields.indexOf(value) > -1) {
			    					sforcShort.calfilterfields.splice( sforcShort.calfilterfields.indexOf(value), 1 );
			    				}

			    			}


			    			shorttext = buildShortcode(sforcShort);
			    			jQuery("#dynshortcode").text(shorttext);


			    		});

			    		jQuery('#calendarpopupcheckboxes').on('change', '.caladdpopup', function()
			    		{
			    			var value = jQuery(this).val();
			    			if (this.checked) {
			    				sforcShort.calpopupfields.push(value);			

			    			} else {

			    				if (sforcShort.calpopupfields.indexOf(value) > -1) {
			    					sforcShort.calpopupfields.splice( sforcShort.calpopupfields.indexOf(value), 1 );
			    				}

			    			}


			    			shorttext = buildShortcode(sforcShort);
			    			jQuery("#dynshortcode").text(shorttext);


			    		});




			    		jQuery("#objectselect").change(function() {

			    			sforcShort.sobject=jQuery(this).val();


			    			location.href = <?php echo "'"."admin.php?page=".$_GET['page']."'"; ?> + '&o=' + jQuery(this).val();
			    		})



			    		function addatt(att,val)
			    		{
			    			return att+'='+'"'+val+'" ';
			    		}

			    		function calendarcode(field,arr)
			    		{
			    			return `${arr.indexOf(field)+1}`;

			    		}


			    		function buildShortcode(short)
			    		{
			    			newfieldslist=short.rearrange.slice();

			    			if(newfieldslist.includes(short.endDate)===false && short.endDate!=='')
			    				newfieldslist.push(short.endDate);


			    			if(newfieldslist.includes(short.cat)===false && short.cat!=='')
			    				newfieldslist.push(short.cat);


			    			if(newfieldslist.includes(short.eventLink)===false && short.eventLink!=='')
			    				newfieldslist.push(short.eventLink);

			    			calFilterAtt='';
			    			if(short.calfilterfields.length>0)
			    			{
			    				for(x in short.calfilterfields)
			    				{

			    					if(newfieldslist.includes(short.calfilterfields[x])===false)
			    						newfieldslist.push(short.calfilterfields[x]);						

			    					calFilterAtt+=calendarcode(short.calfilterfields[x],newfieldslist);
			    				}

			    				calFilterAtt = addatt('f',calFilterAtt);

			    			}

			    			calPopUpAtt='';
			    			if(short.calpopupfields.length>0)
			    			{
			    				for(x in short.calpopupfields)
			    				{

			    					if(newfieldslist.includes(short.calpopupfields[x])===false)
			    						newfieldslist.push(short.calpopupfields[x]);						

			    					calPopUpAtt+=calendarcode(short.calpopupfields[x],newfieldslist);
			    				}

			    				calPopUpAtt = addatt('p',calPopUpAtt);

			    			}			

			    			caleventLinkAtt ='';
			    			if(short.eventLink.length > 0)
		    				{
		    					caleventLinkAtt = addatt('k',calendarcode(short.eventLink,newfieldslist));
		    				}				

			    			viewTypeAtt='';
			    			if(short.viewType!=='0' && short.viewType!=='')
			    				viewTypeAtt = addatt('t', short.viewType);

			    			searchAtt='';
			    			if(short.search!=='on' && short.search!=='')
			    				searchAtt = addatt('search', short.search);

			    			orderAtt='';
			    			if(short.order!=='ASC' && short.order!=='')
			    				orderAtt = addatt('order', short.order);

			    			filterAtt='';
			    			if(short.qfilter!=='')
			    				filterAtt = addatt('filter', short.qfilter);

			    			calCodeAtt='';
			    			if(short.endDate!=='' || short.cat!=='')
			    				calCodeAtt=addatt('c', calendarcode(short.endDate, newfieldslist) + calendarcode(short.cat, newfieldslist))

			    			if(short.viewType !=='4')
							{							
								code = "[showsforce " + 
								addatt('fields', newfieldslist) + 
								addatt('o', short.sobject) + 
								filterAtt + 
								addatt('by', short.orderby) + 
								addatt('n', short.number) + 
								viewTypeAtt + 
								searchAtt + 
								orderAtt + 
								calCodeAtt + 
								calFilterAtt +
								calPopUpAtt +
								caleventLinkAtt +
								"]";
							}
							else
								code = '[sectionsforce '+addatt('o', short.sobject)+filterAtt+orderAtt+addatt('by', short.orderby)+addatt('n', short.number)+'] Content Here {!Id} [/sectionsforce]';

							return code;

						}
						jQuery( function() {

							var sf28c_fieldsmap = <?php echo json_encode($fieldlablestoname_withsoql); ?>;
							var sf28c_availableTags = <?php echo json_encode(array_keys($fieldlablestoname_withsoql)); ?>;


							function sf28c_split( val ) {

								return val.split(/\s+/);

							}
							function sf28c_extractLast( term ) {
								return sf28c_split( term ).pop();
							}

							jQuery( "#queryfilter" )
							.on( "keydown", function( event ) {


								if ( event.keyCode === jQuery.ui.keyCode.TAB &&
									jQuery( this ).autocomplete( "instance" ).menu.active ) {
									event.preventDefault();
							}
						})
							.autocomplete({
								minLength: 0,
								source: function( request, response ) {
									response( jQuery.ui.autocomplete.filter(
										sf28c_availableTags, sf28c_extractLast( request.term ) ) );
								},
								focus: function() {
									return false;
								},
								select: function( event, ui ) {
									var terms = sf28c_split( this.value );

									terms.pop();
									terms.push(sf28c_fieldsmap[ui.item.value]);

									terms.push( "" );
									this.value = terms.join( " " );

									return false;
								}
							});

						} );


					});
				</script>

				<style type="text/css">
				.click-to-select
				{
					-webkit-touch-callout: all; /* iOS Safari */
					-webkit-user-select: all; /* Safari */
					-khtml-user-select: all; /* Konqueror HTML */
					-moz-user-select: all; /* Firefox */
					-ms-user-select: all; /* Internet Explorer/Edge */
					user-select: all; /* Chrome and Opera */

					color:#44C1FF;
					font-weight: bold;
				}

				@media screen and (min-width: 1350px) { 

					#sf28c-admin-addcards{
						background-image: url(<?php echo plugins_url( 'img/cards.png', __FILE__ );?>);
						background-repeat: no-repeat;
						background-position: right bottom; 
						position: relative;
						background-size: auto 300px;
					}

				}
				.xtra-calendar-fields, #sforcshortcoderow
				{
					display: none;
				}
			</style>

			<div class="wrap">
				<h2>Add New Layout - Cards, Tables, Calendars  &amp; Sections</h2>

				<?php 

				if( isset($_GET['debug']) )
				{	
 
					echo '<pre>';
			    	print_r( $sforc_fieldresponse );
					echo '</pre>';
				}

				if( isset( $sforc_fieldresponse['error']) ): ?>

					<div style='margin:2em 0em 2em 0em;background-color:white;padding:1em;box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);border-left: 4px solid red;'>
						<h2>Please configure the connection in the <b> <a href='<?php echo site_url();?>/wp-admin/admin.php?page=sforc-settings';> settings page</a> to connect to Salesforce.</b></h2>
					</div>		

				<?php endif; ?>

				<?php if( !isset( $sforc_fieldresponse['error']) ): ?>

					<div id="sf28c-admin-addcards" style='margin:2em 0em 2em 0em;background-color:white;padding:2em;box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);'>

						<table class="form-table">

							<tr valign="top">
								<th scope="row"></th>
								<td><h2>Start by selecting the fields to display on your page</h2> </td>
							</tr>


							<tr valign="top">
								<th scope="row"><label>Select Object</label></th>
								<td><select id="objectselect">

									<?php
									foreach ($sforc_objectsarray as $key => $value) {
										echo "<option value='".$key."'>".$value."</option>";
									} 


									?>
								</select> </td>
							</tr>

							<tr valign="top">
								<th scope="row"><label>Select Fields to Display</label></th>
								<td><div class="inside" style="max-width: 30em;">
									<div id="posttype-page" class="posttypediv">
										<div class="tabs-panel tabs-panel-active">
											<ul id="pagechecklist-most-recent" class="categorychecklist form-no-clear">
												<?php


												foreach ($sforc_fieldsarray as $key => $value) {
													echo '<li><label class="menu-item-title"><input type="checkbox" class="menu-item-checkbox ofields"  value="'.$key.'">'.$value.'</label></li>';
												}


												?>
											</ul>
										</div>
									</div>
								</div>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row"><label>Order by field</label> </th>
							<td><select id="queryorderby">
								<?php
								foreach ($sforc_fieldsarray as $key => $value) {
									echo '<option value="'.$key.'">'.$value.'</option>';
								}
								?>
							</select></td>
						</tr>

						<tr valign="top">
							<th scope="row"> Order</th>
							<td>

								<select id="queryorder">
									<option selected="true" value="ASC">Low to High</option>
									<option value="DESC">High to Low</option>				
								</select></td>
							</tr>

							<tr valign="top">
								<th scope="row"><label>Number of Records to show:</label></th>
								<td><input min="1" type="number" value="10" id="querylimit" > </td>
							</tr>

							<tr valign="top" id="searchoptrow">
								<th scope="row"><label>Search : </label></th>
								<td><select id="querysearch">
									<option selected="true" value="on">On</option>
									<option value="off">Off</option>				
								</select></td>
							</tr>			

							<tr valign="top">
								<th scope="row"><label>Add Filter : </label></th>
								<td class="ui-widget"><input id="queryfilter" class="large-text" placeholder="LastName ='Smith'"  style="max-width: 30em;"> </td>
							</tr>

							<tr valign="top">
								<th scope="row"><label>Show Records as : </label></th>
								<td>
									<label><input type="radio" name="displayas" checked="true" value="0">Cards</label> 
									<label style="margin-left: 2em;"><input type="radio" name="displayas" value="1">Table</label>
									<label style="margin-left: 2em;"><input type="radio" name="displayas" value="2">Calendar</label>
									<label style="margin-left: 2em;"><input type="radio" name="displayas" value="4">Section</label>

									<label style="margin-left: 2em;"><input type="radio" name="displayas" value="3">JSON</label>


								</td>
							</tr>

						<tr valign="top" class="xtra-calendar-fields" style="display: none;">
							<th scope="row"><label>Category :</label> </th>
							<td><select id="calendareventcat">
									<option value=""></option>
									<?php
									foreach ($sforc_fieldsarray as $key => $value) {
										echo '<option value="'.$key.'">'.$value.'</option>';
									}
									?>
								</select>
								<p class="description">Add option to filter Events by Category. Else, leave it blank.</p>
							</td>
						</tr>

						<tr valign="top" class="xtra-calendar-fields" style="display: none;">
							<th scope="row"><label>Event End Date/Time :</label> </th>
							<td><select id="calendarenddate">
								<option value=""></option>
								<?php
								foreach ($date_timearray as $key => $value) {
									echo '<option value="'.$key.'">'.$value.'</option>';
								}
								?>
							</select>
							<p class="description">Add an Event End Date or Time. Else, leave it blank.</p>
						</td>
						</tr>

						<tr valign="top" class="xtra-calendar-fields" style="display: none;">
							<th scope="row"><label>Event Link :</label> </th>
							<td><select id="calendareventlink">
									<option value=""></option>
									<?php
									foreach ($sforc_fieldsarray as $key => $value) {
										echo '<option value="'.$key.'">'.$value.'</option>';
									}
									?>
								</select>
								<p class="description">Add a https link to the event. Else, leave it blank.</p>
							</td>
						</tr>

					<tr valign="top" class="xtra-calendar-fields" style="display: none;">
						<th scope="row" ><label>Add Fields on Event Popup : </label></th>
						<td>
							<ul id="calendarpopupcheckboxes" class="categorychecklist form-no-clear">
								


							</ul>
						</td>
			

					</tr>

					<tr valign="top" class="xtra-calendar-fields" style="display: none;">
						<th scope="row"><label>Add More Filters : </label></th>
						<td>
							<ul id="calenderfiltercheckboxes" class="categorychecklist form-no-clear">
								


							</ul>
						</td></tr>
						<tr valign="top" id="sforcshortcoderow">
							<th scope="row"><label>Copy this code to your page, post or text widget content to diplay the records</label> </th>
							<td>
								<div class="click-to-select">

									<span id='dynshortcode'>

									</span>


								</div>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row"><label>Rearrange fields : </label></th>
							<td><ul id="menu-to-edit"> </ul> </td>
						</tr>


					</table>				

				<?php endif; ?>

				</div><?php

				include 'helplinks.php';

			}