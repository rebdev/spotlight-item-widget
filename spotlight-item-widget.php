<?php
/*
Plugin Name: Spotlight Item
Plugin URI: 
Description: A widget with an image or icon, title, teaser text and optional Read-More button. Based on Charlie Strickler's Call to Action widget <https://wordpress.org/plugins/call-to-action-widget/> but adds an image wrapper div, configurable classes on elements to make it easier to incorporate animations and frameworks, and some other things that are helpful when template building.
Version: 1.2
Author: Rebecca Appleton
License: GPL
*/
class RA_Spotlight_Item_Widget extends WP_Widget {

	function __construct() {
		$widget_ops = array(
				'classname' => 'widget_spotlight', 
				'description' => __('Put the spotlight on something using an image or icon, title, teaser/text and optional "Read More" button.')
		);
		$control_ops = array(
				'id_base' => 'spotlight_widget'
		);
		parent::__construct('spotlight_widget', __('Spotlight Item'), $widget_ops, $control_ops);
	}


	function widget( $args, $instance ) {

		extract($args);

		// Titles and text
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
		$subtitle = apply_filters( 'widget_subtitle', empty( $instance['subtitle'] ) ? '' : $instance['subtitle'], $instance, $this->id_base );
		$text = apply_filters( 'widget_text', empty( $instance['text'] ) ? '' : $instance['text'], $instance );

		// Button
		$buttontext = apply_filters( 'widget_buttontext', empty( $instance['buttontext'] ) ? '' : $instance['buttontext'], $instance );
		$buttonurl = apply_filters( 'widget_buttonurl', empty( $instance['buttonurl'] ) ? '' : $instance['buttonurl'], $instance );
		$buttonclass = apply_filters( 'widget_buttonclass', empty( $instance['buttonclass'] ) ? '' : $instance['buttonclass'], $instance );

		// Image/icon
		$imageurl = apply_filters( 'widget_imageurl', empty( $instance['imageurl'] ) ? '' : $instance['imageurl'], $instance );
		$imageposition = apply_filters( 'widget_imageposition', empty( $instance['imageposition'] ) ? '' : $instance['imageposition'], $instance );	
		$imageclass = apply_filters( 'widget_imageclass', empty( $instance['imageclass'] ) ? '' : $instance['imageclass'], $instance );	

		// Generate classname for widget from title (spaces replaced with hyphens)
		$title_words = explode(" ", $title);
		$widgetclassfromtitle = implode("-", $title_words);


		/*
	 	 *	Righto, let's print us a widget then!
		 */
		echo $before_widget; 
		
		if ( $imageposition != "below" && !empty( $imageurl ) ) : ?>
			<div class="readmore_image <?php echo $imageclass; ?>">
				<img src="<?php echo $imageurl; ?>" alt="<?php echo $title; ?>" class="<?php echo $imageclass; ?>" />
			</div>	
		<?php endif; ?>

		<?php if ( !empty( $title ) ) { echo $before_title . $title . $after_title; } ?>

		<?php if ( !empty( $subtitle ) ) { echo "<h4>" . $subtitle . "</h4>"; } ?>		

		<?php if ( $imageposition == "below" && !empty( $imageurl ))  : ?>		
			<div class="readmore_image <?php echo $imageclass; ?>">
				<img src="<?php echo $imageurl; ?>" alt="<?php echo $title; ?>" class="<?php echo $imageclass; ?>" />
			</div>	
		<?php  endif; ?>

		<div class="readmore_text textwidget"><?php echo !empty( $instance['filter'] ) ? wpautop( $text ) : $text; ?></div>
		
		<?php if ( !empty( $title ) ) : ?>
			<div class="readmore_button"><a href="<?php echo $buttonurl; ?>" class="<?php echo $buttonclass; ?>"><?php echo $buttontext; ?></a></div>	
		<?php endif; ?>

		<?php
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		// Titles
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['subtitle'] = strip_tags($new_instance['subtitle']);

		// Image attributes
		$instance['imageurl'] =  $new_instance['imageurl'];
		$instance['imageposition'] =  $new_instance['imageposition'];	
		$instance['imageclass'] =  $new_instance['imageclass'];	

		// Button attributes
		$instance['buttonurl'] =  $new_instance['buttonurl'];
		$instance['buttontext'] =  $new_instance['buttontext'];
		$instance['buttonclass'] =  $new_instance['buttonclass'];

		if ( current_user_can('unfiltered_html') )
			$instance['text'] =  $new_instance['text'];
		else
			$instance['text'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['text']) ) ); // wp_filter_post_kses() expects slashed	
		$instance['filter'] = isset($new_instance['filter']);

		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'imageurl' => '', 'imageposition' => '', 'imageclass' => '',
															'title' => '', 'subtitle' => '', 'text' => '', 
															'buttontext' => 'Read more', 'buttonurl' => '', 'buttonclass' => 'button' ) );
		// Title and text attributes
		$title = strip_tags($instance['title']);
		$subtitle = strip_tags($instance['subtitle']);
		$text = esc_textarea($instance['text']);

		// Image attributes
		$imageurl = esc_textarea($instance['imageurl']);	
		$imageposition = esc_textarea($instance['imageposition']);	
		$imageclass = esc_textarea($instance['imageclass']);

		// Button attributes
		$buttontext = esc_textarea($instance['buttontext']);		
		$buttonurl = esc_textarea($instance['buttonurl']);						
		$buttonclass = esc_textarea($instance['buttonclass']);
		?>

		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
		
		<p><label for="<?php echo $this->get_field_id('subtitle'); ?>"><?php _e('Subtitle:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('subtitle'); ?>" name="<?php echo $this->get_field_name('subtitle'); ?>" type="text" value="<?php echo esc_attr($subtitle); ?>" /></p>

		<p><label for="<?php echo $this->get_field_id('imageurl'); ?>"><?php _e('Image URL:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('imageurl'); ?>" name="<?php echo $this->get_field_name('imageurl'); ?>" type="text" value="<?php echo esc_attr($imageurl); ?>" /></p>				

		<p>Image Position: &nbsp;&nbsp;		<input type="radio" value="above" id="imgabove" name="<?php echo $this->get_field_name('imageposition'); ?>" <?php if ( $imageposition != "below" ) echo 'checked'; ?>/>		<label for="imgabove">Above title</label>		&nbsp;&nbsp;		<input type="radio" value="below" id="imgbelow" name="<?php echo $this->get_field_name('imageposition'); ?>" <?php if ( $imageposition == "below" ) echo 'checked'; ?>/>		<label for="imgbelow">Below Title</label>				</p>
		
		<p><label for="<?php echo $this->get_field_id('imageclass'); ?>"><?php _e('Image CSS Class:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('imageclass'); ?>" name="<?php echo $this->get_field_name('imageclass'); ?>" type="text" value="<?php echo esc_attr($imageclass); ?>" /></p>

		<p><label for="<?php echo $this->get_field_id('text'); ?>"><?php _e('Text:'); ?></label>
		<textarea class="widefat" rows="5" cols="20" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>"><?php echo $text; ?></textarea>
		
		<p><label for="<?php echo $this->get_field_id('buttontext'); ?>"><?php _e('Button Text:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('buttontext'); ?>" name="<?php echo $this->get_field_name('buttontext'); ?>" type="text" value="<?php echo esc_attr($buttontext); ?>" /></p>
		
		<p><label for="<?php echo $this->get_field_id('buttonurl'); ?>"><?php _e('Button URL:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('buttonurl'); ?>" name="<?php echo $this->get_field_name('buttonurl'); ?>" type="text" value="<?php echo esc_attr($buttonurl); ?>" /></p>

		<p><label for="<?php echo $this->get_field_id('buttonclass'); ?>"><?php _e('Button CSS Class:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('buttonclass'); ?>" name="<?php echo $this->get_field_name('buttonclass'); ?>" type="text" value="<?php echo esc_attr($buttonclass); ?>" /></p>
		<?php
	}
}
// Register widget by creating an anonymous function for it
add_action( 'widgets_init', create_function( '', 'register_widget( "RA_Spotlight_Item_Widget" );' ) );



/* 
 * Add a settings page to the admin menu to set universal plugin preferences
 */
function ra_spotlight_item_menu() {
	$page_title = "Spotlight Items";
	$menu_title = "Spotlight Items";
	$capability = 'manage_options';
	$menu_slug = "ra_spotlight_items";
	$function = "ra_spotlight_item_settings_page"; // Callback

	add_options_page( $page_title, $menu_title, $capability, $menu_slug, $function);
}
add_action('admin_menu', 'ra_spotlight_item_menu');



/* 
 * Register settings
 */
function register_ra_spotlight_item_settings() {

	//register_setting( 'ra_spotlight_item_options_group', 'ra_spotlight_item_morelink_text', 'sanitize_callback_function_name' );

	/* Create a section to which we will add settings */
	//$id = "ra_spotlight_item_general_settings";
	//$title = "General Settings";
	//$callback = "ra_spotlight_general_settings"; // function that fills the section with the desired content
	//$page = "general";
	//add_settings_section( $id, $title, $callback, $page );

	/* Add the settings to the section */
	//add_settings_field( 'ra_spotlight_item_morelink_text', 'Set Read More text globally', 'checkbox', 'ra_spotlight_item', 'theme_options', 'XX_Option2', 
	//									$args = array('id' => 'checkbox2', 'type' => 'checkbox') );


 	// Add the section to reading settings so we can add our
 	// fields to it
 	add_settings_section(
		'ra_spotlight_item_general_settings',
		'General Settings',
		'ra_spotlight_general_settings',
		'ra_spotlight_items'
	);
 	
 	// Add the field with the names and function to use for our new
 	// settings, put it in our new section
 	add_settings_field(
		'ra_spotlight_item_morelink_text',
		'More Link text',
		'create_morelink_field', // function
		'ra_spotlight_items',
		'ra_spotlight_item_general_settings'
	);
 	
 	// Register our setting so that $_POST handling is done for us and
 	// our callback function just has to echo the <input>
 	register_setting( 'ra_spotlight_items', 'ra_spotlight_item_general_settings' );




} 
add_action( 'admin_init', 'register_ra_spotlight_item_settings' );


function sanitize_callback_function_name() {

}


 // ------------------------------------------------------------------
 // Settings section callback function
 // ------------------------------------------------------------------
 //
 // This function is needed if we added a new section. This function 
 // will be run at the start of our section
 //
function ra_spotlight_general_settings() {
	echo '<p>Intro text for our settings section</p>';
}

function create_morelink_field() {
	echo "something";
}


/* 
 * Generate the settings page
 */
function ra_spotlight_item_settings_page() {

	// Ensure user has the required capabilities
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}

	// Hidden field gets set to Y on post.
	$hidden_field_name = 'ra_spotlight_item_submit_hidden';


    // Variables for the field and option names
    
    $useglobalmorelink_field_name = 'ra_spotlight_item_useglobalmorelink';
    $useglobalmorelink_val = get_option( $useglobalmorelink_field_name ); // Read in existing option value from database

    $morelinktext_field_name = 'ra_spotlight_item_morelink_text';
    $morelinktext_val = get_option( $morelinktext_field_name );


	// See if the user has posted us some information
    // If they did, this hidden field will be set to 'Y'
    if( isset($_POST[ $hidden_field_name ]) && $_POST[ $hidden_field_name ] == 'Y' ) {
        // Read their posted value
        $morelinktext_val = $_POST[ $morelinktext_field_name ];
        // Save the posted value in the database
        update_option( $morelinktext_field_name, $morelinktext_val );
        // Put a "settings saved" message on the screen
?>
<div class="updated"><p><strong><?php _e('settings saved.', 'menu-test' ); ?></strong></p></div>
<?php

    }

    // Now display the settings editing screen
	echo '
		<div class="wrap">
			<h2>Spotlight Items</h2>

			<form method="post" action="options.php">'; ?>
				<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">


				<?php 
					settings_fields( 'ra_spotlight_item_general_settings' );
					do_settings_sections( 'ra_spotlight_item_general_settings' );
				?>

				<?php submit_button(); ?>

			<?php echo '</form>
		</div>';
}


?>
