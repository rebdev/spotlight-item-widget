<?php
/*
Plugin Name: Spotlight Item
Plugin URI: 
Description: A widget with an image or icon, title, teaser text and optional Read-More button. Based on Charlie Strickler's Call to Action widget <https://wordpress.org/plugins/call-to-action-widget/> but adds an image wrapper div, configurable classes on elements to make it easier to incorporate animations and frameworks, and some other things that are helpful when template building.
Version: 1.2
Author: Rebecca Appleton
License: GPL
*/
include_once('admin/spotlight-item-settings.php');

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

		// If set to use globally-set More Link Text (from the plugin settings rather than the widget settings), grab that string too.
		$morelinktext = null;
		if (get_option('ra_spotlight_setmorelinktextglobally')) {
			$morelinktext = get_option('ra_spotlight_morelinktext');
		}


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

		<div class="readmore_text textwidget">
			<?php echo !empty( $instance['filter'] ) ? wpautop( $text ) : $text; ?>
		</div>
		
		<?php if ( !empty( $title ) ) : ?>
			<div class="readmore_button">
				<a href="<?php echo $buttonurl; ?>" class="<?php echo $buttonclass; ?>">
					<?php echo '' . ($morelinktext ? $morelinktext : $buttontext); ?>
				</a>
			</div>	
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

?>
