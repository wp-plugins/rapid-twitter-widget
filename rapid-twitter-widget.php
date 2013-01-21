<?php
/*
Plugin Name: Rapid Twitter Widget
Plugin URI: 
Description: Display the <a href="http://twitter.com/">Twitter</a> latest updates from a Twitter user inside a widget. 
Version: 0.3.2
Author: Floate Design Partners, Peter Wilson
Author URI: 
License: GPLv2
*/

define('RAPID_TWITTER_WIDGET_VERSION', '0.3.2');

class Rapid_Twitter_Widget extends WP_Widget {
	
	static $inlinecssout;

	function Rapid_Twitter_Widget() {
		$widget_ops = array('classname' => 'widget_twitter widget_twitter--hidden', 'description' => __( 'Display your tweets from Twitter') );
		parent::WP_Widget('rapid-twitter', __('Twitter'), $widget_ops);
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['account'] = trim( strip_tags( stripslashes( $new_instance['account'] ) ) );
		$instance['account'] = str_replace('http://twitter.com/', '', $instance['account']);
		$instance['account'] = str_replace('/', '', $instance['account']);
		$instance['account'] = str_replace('@', '', $instance['account']);
		$instance['account'] = str_replace('#!', '', $instance['account']); // account for the Ajax URI
		$instance['title'] = strip_tags(stripslashes($new_instance['title']));
		$instance['show'] = absint($new_instance['show']);
		$instance['hidereplies'] = isset($new_instance['hidereplies']);
		$instance['includeretweets'] = isset($new_instance['includeretweets']);
		$instance['beforetimesince'] = $new_instance['beforetimesince'];

		return $instance;
	}

	function form( $instance ) {
		//Defaults
		$instance = wp_parse_args( (array) $instance, array('account' => '', 'title' => '', 'show' => 5, 'hidereplies' => false) );

		$account = esc_attr($instance['account']);
		$title = esc_attr($instance['title']);
		$show = absint($instance['show']);
		if ( $show < 1 || 20 < $show )
			$show = 5;
		$hidereplies = (bool) $instance['hidereplies'];
		$include_retweets = (bool) $instance['includeretweets'];
		$before_timesince = esc_attr($instance['beforetimesince']);

		echo '<p><label for="' . $this->get_field_id('title') . '">' . esc_html__('Title:') . '
		<input class="widefat" id="' . $this->get_field_id('title') . '" name="' . $this->get_field_name('title') . '" type="text" value="' . $title . '" />
		</label></p>
		<p><label for="' . $this->get_field_id('account') . '">' . esc_html__('Twitter username:') . ' 
		<input class="widefat" id="' . $this->get_field_id('account') . '" name="' . $this->get_field_name('account') . '" type="text" value="' . $account . '" />
		</label></p>
		<p><label for="' . $this->get_field_id('show') . '">' . esc_html__('Maximum number of tweets to show:') . '
			<select id="' . $this->get_field_id('show') . '" name="' . $this->get_field_name('show') . '">';

		for ( $i = 1; $i <= 20; ++$i )
			echo "<option value='$i' " . ( $show == $i ? "selected='selected'" : '' ) . ">$i</option>";

		echo '		</select>
		</label></p>
		<p><label for="' . $this->get_field_id('hidereplies') . '"><input id="' . $this->get_field_id('hidereplies') . '" class="checkbox" type="checkbox" name="' . $this->get_field_name('hidereplies') . '"';
		if ( $hidereplies )
			echo ' checked="checked"';
		echo ' /> ' . esc_html__('Hide replies') . '</label></p>';

		echo '<p><label for="' . $this->get_field_id('includeretweets') . '"><input id="' . $this->get_field_id('includeretweets') . '" class="checkbox" type="checkbox" name="' . $this->get_field_name('includeretweets') . '"';
		if ( $include_retweets )
			echo ' checked="checked"';
		echo ' /> ' . esc_html__('Include retweets') . '</label></p>';

		echo '<input name="' . $this->get_field_name('beforetimesince') . '" type="hidden" value=" " />';
	}

	function widget( $args, $instance ) {
		extract( $args );
		
		$account = trim( urlencode( $instance['account'] ) );
		if ( empty($account) ) return;
		$title = apply_filters('widget_title', $instance['title']);
		if ( empty($title) ) $title = __( 'Twitter Updates' );
		$show = absint( $instance['show'] );  // # of Updates to show
		if ( $show > 200 ) {
			// Twitter paginates at 200 max tweets. update() should not have accepted greater than 20
			$show = 200;
		}
		$hidereplies = (bool) $instance['hidereplies'] ? 'true' : 'false';
		$include_retweets = (bool) $instance['includeretweets'] ? 'true' : 'false';
		
		if ( $this->inlinecssout !== true ) {
			echo '<style>.widget_twitter--hidden{display:none;}</style>';
			$this->inlinecssout = true;
		}
		echo $before_widget;

		echo $before_title;
		echo "<a href='" . esc_url( "http://twitter.com/{$account}" ) . "'>" . esc_html($title) . "</a>";
		echo $after_title;
		
		$numbers = array('1','2','3','4', '5', '6', '7', '8', '9', '0');
		$letters = array('a','b','c','d', 'e', 'f', 'g', 'h', 'i', 'j');
		
		$url_ref = '';
		$url_ref .= str_replace($numbers, $letters, $show) . '__';
		$url_ref .= $hidereplies . '__';
		$url_ref .= $include_retweets . '__';
		$url_ref .= $account . '';

		echo $after_widget;
		echo '<script>';
		echo 'if(typeof(RapidTwitter)==\'undefined\'){';
		echo 'RapidTwitter={};RapidTwitter.apis={};';
		echo '}';

		echo 'if(typeof(RapidTwitter.apis.' . $url_ref . ')==\'undefined\'){';
		echo 'RapidTwitter.apis.' . $url_ref . '={';
			echo 'ref: \'' . esc_js($url_ref) . '\'';
			echo ',screen_name:\'' . esc_js($account) . '\'';
			echo ',count:\'' . esc_js($show) . '\'';
			echo ',exclude_replies:\'' . esc_js($hidereplies) . '\'';
			echo ',include_rts:\'' . esc_js($include_retweets) . '\'';
			echo ',beforetimesince:\'' . esc_js($before_timesince) . '\'';
			echo ',widgets: []';
		echo '};';
		echo '}';
		
		
		
		
		echo 'RapidTwitter.apis.' . $url_ref . '.widgets.push(\'' . esc_js($widget_id) . '\');';
		echo '</script>';
		wp_enqueue_script( 'rapid-twitter-widget' );
		
	}

}


add_action( 'widgets_init', 'rapid_twitter_widget_init' );
function rapid_twitter_widget_init() {
	register_widget('Rapid_Twitter_Widget');
}

add_action( 'wp_enqueue_scripts', 'rapid_twitter_widget_script' );
function rapid_twitter_widget_script() {
	$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '-min';
	wp_register_script(
		'rapid-twitter-widget',
		WP_PLUGIN_URL . "/rapid-twitter-widget/rapid-twitter-widget$suffix.js",
		null,
		RAPID_TWITTER_WIDGET_VERSION,
		true
	);
	
}
?>