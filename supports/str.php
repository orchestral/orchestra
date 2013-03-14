<?php namespace Orchestra\Support;

use Laravel\Str as S;

class Str extends S {

	/**
	 * Convert filter to string, this process is required to filter stream 
	 * data return from Postgres where blob type schema would actually use 
	 * BYTEA and convert the string to stream.
	 *
	 * @static
	 * @access public
	 * @param  mixed    $data
	 * @return string
	 */
	public static function stream_get_contents($data)
	{
		// check if it's actually a resource, we can directly convert 
		// string without any issue.
		if (is_resource($data))
		{
			// Get the content from stream.
			$hex = stream_get_contents($data);

			// For some reason hex would always start with 'x' and if we
			// don't filter out this char, it would mess up hex to string 
			// conversion.
			if (preg_match('/^x(.*)$/', $hex, $matches)) $hex = $matches[1];

			// Check if it's actually a hex string before trying to convert.
			if (ctype_xdigit($hex))
			{
				$data = '';

				// Convert hex to string.
				for ($i = 0; $i < strlen($hex) - 1; $i += 2)
				{
					$data .= chr(hexdec($hex[$i].$hex[$i+1]));
				}
			}
			else 
			{
				$data = $hex;
			}
		}

		return $data;
	}
}