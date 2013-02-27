<?php namespace Orchestra\Repository;

use Orchestra\Support\Memory\Driver,
	Orchestra\Model\User\Meta as User_Meta;

class User extends Driver {
	
	/**
	 * Storage name
	 * 
	 * @access  protected
	 * @var     string  
	 */
	protected $storage = 'usermeta';

	/**
	 * Cached key value map with md5 checksum
	 *
	 * @access  protected
	 * @var     array
	 */
	protected $key_map = array();

	/**
	 * Initiate the instance.
	 *
	 * @access  public
	 * @return  void
	 */
	public function initiate() {}

	/**
	 * Get value of a key
	 *
	 * @access  public
	 * @param   string  $key        A string of key to search.
	 * @param   mixed   $default    Default value if key doesn't exist.
	 * @return  mixed
	 */
	public function get($key = null, $default = null)
	{
		$key   = str_replace('.', '/user-', $key);
		$value = array_get($this->data, $key, null);

		if ( ! is_null($value)) return $value;

		list($name, $user_id) = explode('/user-', $key);

		$user_meta = User_Meta::name($name, $user_id);

		if ( ! is_null($user_meta))
		{
			$this->put($key, $user_meta->value);

			$this->key_map[$key] = array(
				'id'       => $key,
				'checksum' => md5($user_meta->value),
			);

			return $user_meta->value;
		}

		$this->put($key, null);

		return value($default);
	}

	/**
	 * Set a value from a key
	 *
	 * @access  public
	 * @param   string  $key        A string of key to add the value.
	 * @param   mixed   $value      The value.
	 * @return  mixed
	 */
	public function put($key, $value = '')
	{
		$key   = str_replace('.', '/user-', $key);
		$value = value($value);
		array_set($this->data, $key, $value);

		return $value;
	}

	/**
	 * Delete value of a key
	 *
	 * @access  public
	 * @param   string  $key        A string of key to delete.
	 * @return  bool
	 */
	public function forget($key = null)
	{
		$key = str_replace('.', '/user-', $key);
		return array_set($this->data, $key, null);
	}

	/**
	 * Add a shutdown event.
	 *
	 * @access  public
	 * @return  void
	 */
	public function shutdown() 
	{
		foreach ($this->data as $key => $value)
		{
			$is_new   = true;
			$checksum = '';
			
			if (array_key_exists($key, $this->key_map))
			{
				$is_new = false;
				extract($this->key_map[$key]);
			}

			list($name, $user_id) = explode('/user-', $key);

			if ($checksum === md5($value) or empty($user_id)) continue;

			$user_meta = User_Meta::where('name', '=', $name)
						->where('user_id', '=', $user_id)->first();

			if (true === $is_new and is_null($user_meta))
			{
				if (is_null($value)) continue;

				// Insert the new key:value
				User_Meta::create(array(
					'name'    => $name,
					'user_id' => $user_id,
					'value'   => $value,
				));
			}
			else
			{
				if (is_null($value))
				{
					$user_meta->delete();
				}
				else
				{
					// Update the key:value
					$user_meta->value = $value;
					$user_meta->save();
				}
			}
		}
	}


}