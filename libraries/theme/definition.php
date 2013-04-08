<?php namespace Orchestra\Theme;

use RuntimeException;

class Definition {
	
	/**
	 * Theme configuration.
	 *
	 * @var array
	 */
	protected $item = array();

	/**
	 * Load the theme.
	 *
	 * @access public
	 * @param  string   $path
	 * @return void
	 */
	public function __construct($path)
	{
		$path = rtrim($path, DS).DS;
		
		if (is_file($path.'theme.json'))
		{
			$this->item = json_decode(
				file_get_contents($path.'theme.json')
			);

			if (is_null($this->item))
			{
				// json_decode couldn't parse, throw an exception
				throw new RuntimeException(
					"Theme [{$path}]: cannot decode theme.json file"
				);
			}
		}
	}

	/**
	 * Magic method to get item by key.
	 */
	public function __get($key)
	{
		if ( ! isset($this->item->{$key})) return null;

		return $this->item->{$key};
	}

	/**
	 * Magic Method to check isset by key.
	 */
	public function __isset($key)
	{
		return isset($this->item->{$key});
	}
}