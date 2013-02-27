<?php namespace Orchestra\Support\Memory;

class Eloquent extends Driver {

	/**
	 * Storage name
	 * 
	 * @access  protected
	 * @var     string  
	 */
	protected $storage = 'eloquent';

	/**
	 * Cached key value map with md5 checksum
	 *
	 * @access  protected
	 * @var     array
	 */
	protected $key_map = array();

	/**
	 * Load the data from database using Eloquent ORM
	 *
	 * @access  public
	 * @return  void
	 */
	public function initiate() 
	{
		$this->name = isset($this->config['name']) ? $this->config['name'] : $this->name;
		
		$memories = call_user_func(array($this->name, 'all'));

		foreach ($memories as $memory)
		{
			$value = $this->stringify($memory->value);

			$this->put($memory->name, unserialize($value));

			$this->key_map[$memory->name] = array(
				'id'       => $memory->id,
				'checksum' => md5($value),
			);
		}
	}

	/**
	 * Add a shutdown event using Eloquent ORM
	 *
	 * @access  public
	 * @return  void
	 */
	public function shutdown() 
	{
		foreach ($this->data as $key => $value)
		{
			$is_new   = true;
			$id       = null;
			$checksum = '';
			
			if (array_key_exists($key, $this->key_map))
			{
				$is_new = false;
				extract($this->key_map[$key]);
			}

			$serialize = serialize($value);

			if ($checksum === md5($serialize))
			{
				continue;
			}

			$count = call_user_func(array($this->name, 'where'), 'name', '=', $key)->count();

			if (true === $is_new and $count < 1)
			{
				call_user_func(array($this->name, 'create'), array(
					'name'  => $key,
					'value' => $serialize,
				));
			}
			else
			{
				$memory = call_user_func(array($this->name, 'where'), 'name', '=', $key)->first();
				$memory->fill(array(
					'value' => $serialize,
				));

				$memory->save();
			}
		}
	}
}