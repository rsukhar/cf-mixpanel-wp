<?php

class CF_Ref_Source {

	protected $tracked_utm_params = array('utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content', 'utm_referrer');

	/**
	 * If needed, stores data about referrer and UTM params
	 */
	public function maybe_store_ref_source()
	{
		$referer_domain = isset($_SERVER['HTTP_REFERER']) ? parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST) : '';
		if ($referer_domain === $_SERVER['HTTP_HOST'])
		{
			return;
		}
		$ref_source = $this->get_stored_ref_source();
		$query = $_GET;
		if (isset($query['ref']))
		{
			$ref_source['ref'] = $query['ref'];
		}
		// If at least one tracked utm defined, tracking them all
		if ( ! empty(array_intersect($this->tracked_utm_params, array_keys($query))))
		{
			$ref_source_ref = isset($ref_source['ref']) ? $ref_source['ref'] : '';
			$ref_source = array_intersect_key($query, array_flip($this->tracked_utm_params));
			if ($ref_source_ref)
			{
				$ref_source['ref'] = $ref_source_ref;
			}
		}
		// Using top-level domain instead of subdoimain
		$domain = implode('.', array_slice(explode('.', $_SERVER['HTTP_HOST']), -2));
		// Setting cookie for a month
		setcookie('ref_source', json_encode($ref_source), time() + 30 * 24 * 60 * 60, '/', $domain);
	}

	/**
	 * @return array
	 */
	public function get_stored_ref_source()
	{
		$result = isset($_COOKIE['ref_source']) ? $_COOKIE['ref_source'] : '';
		$result = json_decode($result, TRUE);
		if ( ! is_array($result))
		{
			return array();
		}
		// Keeping only ref_source keys that are intended to be here
		foreach ($result as $key => $value)
		{
			if ($key !== 'ref' AND ! in_array($key, $this->tracked_utm_params))
			{
				unset($result[$key]);
			}
		}

		return $result;
	}

}

$cf_ref_source = new CF_Ref_Source;
add_action( 'init', array( $cf_ref_source, 'maybe_store_ref_source' ) );