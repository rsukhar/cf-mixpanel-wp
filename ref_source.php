<?php

class CF_Ref_Source {

	/**
	 * Dev note: keep this function clean from Kohana helpers, so it can be easily copied to WordPress plugin
	 */
	public function maybe_store_affiliator_data()
	{
		$http_referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
		if (parse_url($http_referer, PHP_URL_HOST) === $_SERVER['HTTP_HOST'])
		{
			// Wrong source to track
			return;
		}
		if ( ! isset($_GET) OR ! isset($_GET['ref']) OR empty($_GET['ref']))
		{
			return;
		}
		$prev_affiliator_data = $this->get_stored_affiliator_data();
		if (isset($prev_affiliator_data['username']) AND $prev_affiliator_data['username'] === $_GET['ref'])
		{
			return;
		}
		$affiliator_data = array(
			'username' => $_GET['ref'],
			'affiliation_date' => date('Y-m-d H:i:s'),
		);
		// Getting top-level domain from subdomains when needed
		$domain = implode('.', array_slice(explode('.', $_SERVER['HTTP_HOST']), -2));
		setcookie('affiliator_data', json_encode($affiliator_data), time() + 30 * 24 * 60 * 60, '/', $domain);
		// Storing in gloval var so the value will be available in the same application run
		$_COOKIE['affiliator_data'] = json_encode($affiliator_data);
	}

	/**
	 * @return array
	 *
	 * Dev note: keep this function clean from Kohana helpers, so it can be easily copied to WordPress plugin
	 */
	public function get_stored_affiliator_data()
	{
		$raw_result = isset($_COOKIE['affiliator_data']) ? $_COOKIE['affiliator_data'] : '';
		$result = json_decode($raw_result, TRUE);
		if ( ! is_array($result))
		{
			$result = array();
		}
		$result = array_intersect_key($result, array_flip(array('username', 'affiliation_date')));

		return $result;
	}

	protected $tracked_utm_params = array('utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content', 'utm_referrer');

	/**
	 * Dev note: keep this function clean from Kohana helpers, so it can be easily copied to WordPress plugin
	 */
	public function maybe_store_utm_params()
	{
		$http_referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
		if (parse_url($http_referer, PHP_URL_HOST) === $_SERVER['HTTP_HOST'])
		{
			// Wrong source to track
			return;
		}
		$prev_utm_params = $this->get_stored_utm_params();
		if ( ! empty($prev_utm_params))
		{
			// Using only first-touch approach for now
			return;
		}
		$utm_params = array_intersect_key($_GET, array_flip($this->tracked_utm_params));
		if ( ! isset($utm_params['utm_referrer']))
		{
			$utm_params['utm_referrer'] = $http_referer;
		}
		$domain = implode('.', array_slice(explode('.', $_SERVER['HTTP_HOST']), -2));
		setcookie('utm_params', json_encode($utm_params), time() + 30 * 24 * 60 * 60, '/', $domain);
		// Storing in gloval var so the value will be available in the same application run
		$_COOKIE['utm_params'] = json_encode($utm_params);
	}

	/**
	 * @return array
	 *
	 * Dev note: keep this function clean from Kohana helpers, so it can be easily copied to WordPress plugin
	 */
	public function get_stored_utm_params()
	{
		$raw_result = isset($_COOKIE['utm_params']) ? $_COOKIE['utm_params'] : '';
		$result = json_decode($raw_result, TRUE);
		if ( ! is_array($result))
		{
			$result = array();
		}
		$result = array_intersect_key($result, array_flip($this->tracked_utm_params));

		return $result;
	}

}

global $cf_ref_source;
$cf_ref_source = new CF_Ref_Source;
add_action( 'init', array( $cf_ref_source, 'maybe_store_affiliator_data' ) );
add_action( 'init', array( $cf_ref_source, 'maybe_store_utm_params' ) );