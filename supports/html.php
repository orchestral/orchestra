<?php namespace Orchestra\Support;

use Config,
	Laravel\HTML as H;

class HTML extends H {

	/**
	 * Cache application encoding locally to save expensive calls to config::get().
	 *
	 * @var string
	 */
	public static $encoding = null;

	/**
	 * Generate a HTML element
	 *
	 * @static
	 * @access public
	 * @param  string $tag
	 * @param  mixed  $value
	 * @param  array  $attributes
	 * @return string
	 */
	public static function create($tag = 'div', $value = null, $attributes = array())
	{
		if (is_array($value))
		{
			$attributes = $value;
			$value      = null;
		}

		$content = '<'.$tag.static::attributes($attributes).'>';

		if ( ! is_null($value))
		{
			$content .= static::entities($value).'</'.$tag.'>';
		}
		
		return $content;
	}

	/**
	 * Convert HTML characters to entities.
	 *
	 * The encoding specified in the application configuration file will be used.
	 *
	 * @static
	 * @access public
	 * @param  string  $value
	 * @return string
	 */
	public static function entities($value)
	{
		if ($value instanceof Expression) return $value->get();
		
		return htmlentities($value, ENT_QUOTES, static::encoding(), false);
	}

	/**
	 * Create a new HTML expression instance.
	 *
	 * Database expressions are used to inject HTML.
	 * 
	 * @static
	 * @access public
	 * @param  string      $value
	 * @return Expression
	 */
	public static function raw($value)
	{
		return new Expression($value);
	}

	/**
	 * Build a list of HTML attributes from one or two array.
	 *
	 * @static
	 * @access public
	 * @param  array   $attributes
	 * @param  array   $defaults
	 * @return array
	 */
	public static function decorate($attributes, $defaults = null)
	{
		// Special consideration to class, where we need to merge both string from
		// $attributes and $defaults and take union of both.
		$c1       = isset($defaults['class']) ? $defaults['class'] : '';
		$c2       = isset($attributes['class']) ? $attributes['class'] : '';
		$classes  = explode(' ', trim($c1.' '.$c2));
		$current  = array_unique($classes);
		$excludes = array();

		foreach ($current as $c)
		{
			if (starts_with($c, '!'))
			{
				$excludes[] = substr($c, 1);
				$excludes[] = $c;
			}
		}

		$class      = implode(' ', array_diff($current, $excludes));
		$attributes = array_merge($defaults, $attributes);

		empty($class) or $attributes['class'] = $class;

		return $attributes;
	}

	/**
	 * Get the appliction.encoding without needing to request it from Config::get() each time.
	 *
	 * @static
	 * @access protected
	 * @return string
	 */
	protected static function encoding()
	{
		return static::$encoding ?: static::$encoding = Config::get('application.encoding');
	}
}
