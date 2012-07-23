<?php 

if ( ! function_exists('handles'))
{
	/**
	 * Return handles configuration for a bundle
	 * 
	 * @param  string $bundle Bundle name
	 * @return string       URL path
	 */
	function handles($name)
	{
		$handles = '';

		if (strpos($name, '::') !== false)
		{
			list($bundle, $to) = Bundle::parse($name);
		}
		else 
		{
			$bundle = $name;
			$to     = '';
		}

		// In situation where bundle is not registered, it best to assume 
		// that we are handle "application" routing
		if ( ! Bundle::exists($bundle))
		{
			$bundle = DEFAULT_BUNDLE;
			$to     = $bundle;

			// DEFAULT_BUNDLE should handle root path
			$handles = '';
		}
		else
		{
			$handles = Bundle::option($bundle, 'handles');
			$handles = rtrim($handles, '/');
		}
		
		$to = ltrim($to, '/');

		return url($handles.'/'.$to);
	}
}