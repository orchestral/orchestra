<?php namespace Orchestra\Support\Acl;

use \Str,
	\InvalidArgumentException;

class Fluent {

	/**
	 * Collection name.
	 *
	 * @var string
	 */
	protected $name = null;
	
	/**
	 * Collection of this instance.
	 *
	 * @var array
	 */
	protected $collections = array();

	/**
	 * Construct a new instance.
	 *
	 * @access public	
	 * @param  string   $name
	 * @return void
	 */
	public function __construct($name)
	{
		$this->name = $name;
	}

	/**
	 * Get the collections.
	 *
	 * @access public
	 * @return array
	 */
	public function get()
	{
		return $this->collections;
	}

	/**
	 * Determine whether a key exists in collection
	 *
	 * @access public
	 * @param  string   $key
	 * @return boolean
	 */
	public function has($key)
	{
		$key = strval($key);
		$key = trim(Str::slug($key, '-'));

		return ( ! empty($key) and in_array($key, $this->collections));
	}

	/**
	 * Add multiple key to collection
	 *
	 * @access public
	 * @param  array   $keys
	 * @return bool
	 */
	public function fill(array $keys)
	{
		foreach ($keys as $key) $this->add($key);

		return true;
	}

	/**
	 * Add a key to collection
	 *
	 * @access public
	 * @param  string   $key
	 * @return bool
	 */
	public function add($key)
	{
		if (is_null($key)) 
		{
			throw new InvalidArgumentException("Can't add NULL {$this->name}.");
		}

		$key = trim(Str::slug($key, '-'));

		if ($this->has($key)) return false;

		array_push($this->collections, $key);
		
		return true;
	}

	/**
	 * Rename a key from collection
	 *
	 * @access public
	 * @param  string   $from
	 * @param  string   $to
	 * @return bool
	 */
	public function rename($from, $to)
	{
		$from = trim(Str::slug($from, '-'));
		$to   = trim(Str::slug($to, '-'));

		if (false === ($key = $this->search($from))) return false;

		$this->collections[$key] = $to;
		return true;
	}

	/**
	 * Remove a key from collection
	 *
	 * @access public
	 * @param  string   $key
	 * @return bool
	 */
	public function remove($key)
	{
		if (is_null($key)) 
		{
			throw new InvalidArgumentException("Can't add NULL {$this->name}.");
		}

		$key = trim(Str::slug($key, '-'));

		if ( ! is_null($id = $this->search($key))) 
		{
			unset($this->collections[$id]);
			return true;
		}

		return false;
	}

	/**
	 * Get the ID from a key
	 *
	 * @access public
	 * @param  string   $key
	 * @return int
	 */
	public function search($key)
	{
		$id = array_search($key, $this->collections);
		
		if (false === $id) return null;

		return $id;
	}

	/**
	 * Check if an id is set in the collection.
	 *
	 * @access public
	 * @param  int      $id
	 * @return bool
	 */
	public function exist($id)
	{
		return isset($this->collections[$id]);
	}

	/**
	 * Filter request.
	 *
	 * @access public
	 * @param  mixed    $request
	 * @return array
	 */
	public function filter($request)
	{
		if ( ! is_array($request)) 
		{
			switch (true)
			{
				case $request === '*' :
					$request = $this->get();
					break;
				case $request[0] === '!' :
					$request = array_diff($this->get(), array(substr($request, 1)));
					break;
				default :
					$request = array($request);
					break;
			}
		}
			
		return $request;
	}
}