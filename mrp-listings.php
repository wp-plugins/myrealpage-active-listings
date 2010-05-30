<?php
/*
Plugin Name: myRealPage Active Listings
Plugin URI: http://www.splitmango.com/
Description: Display your myRealPage active listings on your Wordpress blog
Author: SplitMango Media Inc.
Version: 1.0
Author URI: http://www.splitmango.com/
*/

add_action( 'widgets_init', 'load_listing_widgets' );
function load_listing_widgets() {
	register_widget( 'myRealPage_Active_Listings' );
}

class myRealPage_Active_Listings extends WP_Widget {

	function myRealPage_Active_Listings() {
		$widget_ops = array( 'classname' => 'myRealPage_listings', 'description' => __('Display your myRealPage Active Listings', 'example') );

		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'listing_widget' );

		$this->WP_Widget( 'listing_widget', __('myRealPage Active Listings', 'example'), $widget_ops, $control_ops );
	}

	/**
	 * How to display the widget on the screen.
	 */
	function widget( $args, $instance ) {
		extract( $args );

		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'] );
		$website = $instance['website'];
		
		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title )
			echo $before_title . $title . $after_title;

		/* Display website from widget settings if one was input. */
		if ( $website )
			include('simple_html_dom.php');
			$listings = $website . "/mylistings.html";
			$html = file_get_html($listings);
       
foreach($html->find('link[type=application/rss+xml]') as $element)
	$xmltouse = $website . $element->href;
	
	$xml = simplexml_load_file($xmltouse);
	$count = count(simplexml_load_file($xmltouse)->xpath('//item'));
	$random = (rand()%$count);
	$title = $xml->channel->item[$random]->title;
	$link = $xml->channel->item[$random]->link;
	$description = substr($xml->channel->item[$random]->description,0,600) . " ... [<a href=\"$link\">More</a>]";
	echo $title . "<br/>" . $description;
			
	}

	/**
	 * Update the widget settings.
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags for title and name to remove HTML (important for text inputs). */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['website'] = strip_tags( $new_instance['website'] );

		return $instance;
	}

	/**
	 * Displays the widget settings controls on the widget panel.
	 * Make use of the get_field_id() and get_field_name() function
	 * when creating your form elements. This handles the confusing stuff.
	 */
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array( 'title' => __('Active Listing', 'example'), 'website' => __('http://yourwebsite.com', 'example'));
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<!-- Widget Title: Text Input -->
		<p><?php _e('Title:', 'hybrid'); ?></label><br/><input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:250px;" />
		</p>

		<!-- myRealPage Website URL -->
		<p><?php _e('Your myRealPage Website:', 'example'); ?><br/><input id="<?php echo $this->get_field_id( 'name' ); ?>" name="<?php echo $this->get_field_name( 'website' ); ?>" value="<?php echo $instance['website']; ?>" style="width:250px" />
		</p>

	<?php
	}
}

?>