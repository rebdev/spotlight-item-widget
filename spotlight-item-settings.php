<?php



// --------------------------------------------------------------------------
// Add a submenu page to the Settings admin menu for plugin preferences
// --------------------------------------------------------------------------
// 
function add_new_settings_page_to_settings_menu() {
	// Some variables to feed to add_options_page
	$page_title = "Spotlight Items";
	$menu_title = "Spotlight Items";
	$capability = 'manage_options';
	$menu_slug = "ra_spotlight_items";
	$function = "ra_spotlight_settings_page_callback"; // Callback
	add_options_page( $page_title, $menu_title, $capability, $menu_slug, $function);
}
add_action('admin_menu', 'add_new_settings_page_to_settings_menu');



// --------------------------------------------------------------------------
// Add all sections, fields and settings during admin_init
// --------------------------------------------------------------------------
// 
function ra_spotlight_settings_api_init() {

	// Add a General settings section to the ra_spotlight_items-slugged page
	//  so we can add our fields to it
	add_settings_section('ra_spotlight_settings_general',
		'General Settings',
		'ra_spotlight_settings_general_section_callback',
		'ra_spotlight_items');


	// Add  to the General settings section a field to set More Link text to globally controlled
	add_settings_field('ra_spotlight_setmorelinktextglobally',
		'Set More Link text globally',
		'ra_spotlight_setmorelinktextglobally_fieldcallback',
		'ra_spotlight_items',
		'ra_spotlight_settings_general');

	// Register our setting so that $_POST handling is done for us and our callback function just has to echo the <input>
 	register_setting( 'ra_spotlight_items', 'ra_spotlight_setmorelinktextglobally' );


	// Add a field for More Link text to the General settings section
	add_settings_field('ra_spotlight_morelinktext',
		'More Link text',
		'ra_spotlight_morelinktext_fieldcallback',
		'ra_spotlight_items',
		'ra_spotlight_settings_general');

	// Register our setting so that $_POST handling is done for us and our callback function just has to echo the <input>
 	register_setting( 'ra_spotlight_items', 'ra_spotlight_morelinktext' );


}
add_action('admin_init', 'ra_spotlight_settings_api_init');




// --------------------------------------------------------------------------
// Output intro text for General Settings section
// --------------------------------------------------------------------------
// 
function ra_spotlight_settings_general_section_callback() {
	//echo '<p>General settings section.</p>';
}




// --------------------------------------------------------------------------
// Output the page
// --------------------------------------------------------------------------
// 
function ra_spotlight_settings_page_callback() {
	?>

	<div class="wrap">
		<h1>Spotlight Items</h1>

		<br />
		<form method="POST" action="options.php">

			<?php 
				// Pass slug name of page, also referred to in Settings API as option group name
				settings_fields( 'ra_spotlight_items' );
			?>

			<?php 
				// Output the settings section(s) we've already added to this page
				do_settings_sections( 'ra_spotlight_items' );
			?>

			<?php submit_button(); ?>

		</form>

	</div>
	<?php
}



// --------------------------------------------------------------------------
// Output the Read More Text field
// --------------------------------------------------------------------------
// 
function ra_spotlight_morelinktext_fieldcallback() {
	echo '<input name="ra_spotlight_morelinktext" id="ra_spotlight_morelinktext" 
					type="text" value="' . get_option('ra_spotlight_morelinktext') . '" />';
}


// --------------------------------------------------------------------------
// Output the Set Read More Globally Text field
// --------------------------------------------------------------------------
//
function ra_spotlight_setmorelinktextglobally_fieldcallback() {
	echo '<input name="ra_spotlight_setmorelinktextglobally" id="ra_spotlight_setmorelinktextglobally" 
					type="checkbox" value="1" class="code" ' . checked( 1, get_option('ra_spotlight_setmorelinktextglobally'), false) . ' />';
}

?>
