<?php namespace Orchestra\Support\Memory;

use \DB,
	Orchestra\Support\Str;

class Fluent extends Driver {
	
	/**
	 * Storage name
	 * 
	 * @access  protected
	 * @var     string  
	 */
	protected $storage = 'fluent';

	/**
	 * Database table name
	 *
	 * @access  protected
	 * @var     string
	 */
	protected $table   = null;

	/**
	 * Cached key value map with md5 checksum
	 *
	 * @access  protected
	 * @var     array
	 */
	protected $key_map = array();

	/**
	 * Load the data from database using Fluent Query Builder
	 *
	 * @access  public
	 * @return  void
	 */
	public function initiate() 
	{
		$this->table = isset($this->config['table']) ? $this->config['table'] : $this->name;
		
		$memories = DB::table($this->table)->get();

		foreach ($memories as $memory)
		{
			$value = Str::stream_get_contents($memory->value);

			$this->put($memory->name, unserialize($value));

			$this->key_map[$memory->name] = array(
				'id'       => $memory->id,
				'checksum' => md5($value),
			);
		}
	}

	/**
	 * Add a shutdown event using Fluent Query Builder
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

			$count = DB::table($this->table)->where('name', '=', $key)->count();

			if (true === $is_new and $count < 1)
			{
				// Insert the new key:value
				DB::table($this->table)
					->insert(array(
						'name'  => $key,
						'value' => $serialize,
					));
			}
			else
			{
				// Update the key:value
				DB::table($this->table)
					->where('id', '=', $id)
					->update(array(
						'value' => $serialize,
					)); 
			}
		}
	}
}