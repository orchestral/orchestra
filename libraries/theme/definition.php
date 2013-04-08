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
			
			if (isset($this->item->autoload) and is_array($this->item->autoload))
			{
				foreach ($this->item->autoload as $file)
				{
					include_once $path.ltrim($file, DS);
				}
			}
		}
	}

	/**
	 * Get configuration information by key.
	 */
	public function __get($key)
	{
		if ( ! isset($this->item->{$key})) return null;

		return $this->item->{$key};
	}
}