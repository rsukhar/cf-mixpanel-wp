<?php

add_action( 'wp_head', array( 'MixPanel', 'insert_tracker' ) );
add_action( 'wp_footer', array( 'MixPanel', 'insert_event' ) );

class MixPanel {

	/*
	* Gets the value of the key mixpanel_event_label for this specific Post
	* @return string the value of the meta box set on the page
	*/
	public static function get_post_event_label() {
		global $post;

		return get_post_meta( $post->ID, 'mixpanel_event_label', TRUE );
	}

	/*
	* Inserts the value for the mixpanel.track() API Call
	* @return boolean technically this should be html..
	*/
	public static function insert_event() {
		$settings = (array) get_option( 'mixpanel_settings' );

		if ( ! isset( $settings['token_id'] ) ) {
			self::no_mixpanel_token_found();

			return FALSE;
		}

		echo "<script type='text/javascript'>
		var rightNow = new Date();
		var humanDate = rightNow.toDateString();

		mixpanel.register_once({
			'First site page': document.title,
			'First site contact': humanDate
		});
		mixpanel.track('Page View');
		</script>";

		return TRUE;
	}

	/**
	 * Adds the Javascript necessary to start tracking via MixPanel.
	 * this gets added to the <head> section usually.
	 *
	 * @return [type] [description]
	 */
	public static function insert_tracker() {
		$settings = (array) get_option( 'mixpanel_settings' );
		if ( ! isset( $settings['token_id'] ) ) {
			self::no_mixpanel_token_found();

			return FALSE;
		}

		require_once dirname( __FILE__ ) . '/mixpaneljs.php';

		return TRUE;
	}

	public static function no_mixpanel_token_found() {
		echo "<!-- No MixPanel Token Defined -->";
	}
}
