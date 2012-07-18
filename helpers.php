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
		if (strpos($name, '::') !== false)
		{
			list($bundle, $to) = Bundle::parse($name);
		}
		else 
		{
			$bundle = $name;
			$to     = '';
		}

		if ( ! Bundle::exists($bundle)) return null;

		$handles = Bundle::option($bundle, 'handles');
		$handles = rtrim($handles, '/');
		$to      = ltrim($to, '/');

		return url($handles.'/'.$to);
	}
}