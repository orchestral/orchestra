<?php namespace Orchestra\Support\Acl;

use \Str,
	\InvalidArgumentException,
	\RuntimeException,
	Orchestra\Support\Auth as Auth,
	Orchestra\Support\Memory\Driver as MemoryDriver;

class Container {
	
	/**
	 * Acl instance name.
	 * 
	 * @access  protected
	 * @var     string
	 */
	protected $name = null;

	/**
	 * Memory instance.
	 * 
	 * @access  protected
	 * @var     Orchestra\Support\Memory\Driver
	 */
	protected $memory = null;

	/**
	 * List of roles
	 * 
	 * @access  protected
	 * @var     Orchestra\Support\Acl\Fluent
	 */
	protected $roles = null;
	 
	/**
	 * List of actions
	 * 
	 * @access  protected
	 * @var     Orchestra\Support\Acl\Fluent
	 */
	protected $actions = null;
	 
	/**
	 * List of ACL map between roles, action
	 * 
	 * @access  protected
	 * @var     array
	 */
	protected $acl = array();

	/**
	 * Construct a new object.
	 *
	 * @access  public
	 * @param   string        $name
	 * @param   MemoryDriver  $memory
	 */
	public function __construct($name, MemoryDriver $memory = null) 
	{
		$this->name    = $name;
		$this->roles   = new Fluent('roles');
		$this->actions = new Fluent('actions');

		$this->roles->add('guest');
		$this->attach($memory);
	}

	/**
	 * Check whether a Memory instance is already attached to Acl.
	 *
	 * @access public
	 * @return boolean
	 */
	public function attached()
	{
		return ( ! is_null($this->memory));
	}

	/**
	 * Bind current Acl instance with a Memory instance.
	 *
	 * @access  public				
	 * @param   MemoryDriver    $memory
	 * @return  self
	 * @throws  Exception
	 */
	public function attach(MemoryDriver $memory = null)
	{
		if ($this->attached())
		{
			throw new RuntimeException(
				"Unable to assign multiple Orchestra\Support\Memory instance."
			);
		}

		// since we already check instanceof, only check for NULL
		if (is_null($memory)) return;

		$this->memory = $memory;
		$data         = array_merge(array(
			'acl'     => array(),
			'actions' => array(),
			'roles'   => array(),
		), $this->memory->get("acl_".$this->name, array()));

		// Loop through all the roles in memory and add it to
		// this ACL instance.
		foreach ($data['roles'] as $role)
		{
			$this->roles->add($role);
		}

		// Loop through all the actions in memory and add it to 
		// this ACL instance.
		foreach ($data['actions'] as $action)
		{
			$this->actions->add($action);
		}

		// Loop through all the acl in memory and add it to 
		// this ACL instance.
		foreach ($data['acl'] as $id => $allow)
		{
			list($role, $action) = explode(':', $id);
			$this->assign($role, $action, $allow);
		}

		return $this->sync();
	}

	/**
	 * Sync memory with acl instance, make sure anything that added before 
	 * ->with($memory) got called is appended to memory as well.
	 *
	 * @access public
	 * @return void
	 */
	public function sync()
	{
		// Loop through all the acl in memory and add it to this ACL 
		// instance.
		foreach ($this->acl as $id => $allow)
		{
			list($role, $action) = explode(':', $id);
			$this->assign($role, $action, $allow);
		}

		if ( ! is_null($this->memory))
		{
			$name = $this->name;

			$this->memory->put("acl_{$name}.actions", $this->actions->get());
			$this->memory->put("acl_{$name}.roles", $this->roles->get());
			$this->memory->put("acl_{$name}.acl", $this->acl);
		}

		return $this;
	}

	/**
	 * Verify whether current user has sufficient roles to access the 
	 * actions based on available type of access.
	 *
	 * @access  public
	 * @param   mixed   $action     A string of action name
	 * @return  bool
	 * @throws  Exception
	 */
	public function can($action) 
	{
		$roles = array();
		
		if ( ! Auth::guest()) $roles = Auth::roles();
		else
		{
			// only add guest if it's available
			if ($this->roles->has('guest')) array_push($roles, 'guest');
		}

		return $this->check($roles, $action);
	}

	/**
	 * Verify whether given roles has sufficient roles to access the 
	 * actions based on available type of access.
	 *
	 * @access  public
	 * @param   mixed   $roles      A string or an array of roles
	 * @param   mixed   $action     A string of action name
	 * @return  bool
	 * @throws  InvalidArgumentException
	 */
	public function check($roles, $action) 
	{
		$actions = $this->actions->get();

		if ( ! in_array(Str::slug($action, '-'), $actions)) 
		{
			throw new InvalidArgumentException(
				"Unable to verify unknown action {$action}."
			);
		}

		$action     = Str::slug($action, '-');
		$action_key = array_search($action, $actions);

		// array_search() will return false when no key is found based on 
		// given haystack, therefore we should just ignore and return false
		if ($action_key === false) return false;

		foreach ((array) $roles as $role) 
		{
			$role     = Str::slug($role, '-');
			$role_key = array_search($role, $this->roles->get());

			// array_search() will return false when no key is found based 
			// on given haystack, therefore we should just ignore and 
			// continue to the next role.
			if ($role_key === false) continue;

			if (isset($this->acl[$role_key.':'.$action_key]))
			{
				return $this->acl[$role_key.':'.$action_key];
			}
		}

		return false;
	}

	/**
	 * Assign single or multiple $roles + $actions to have access
	 * 
	 * @access  public
	 * @param   mixed   $roles          A string or an array of roles
	 * @param   mixed   $actions        A string or an array of action name
	 * @param   bool    $allow
	 * @return  self
	 * @throws  Exception
	 */
	public function allow($roles, $actions, $allow = true) 
	{
		$roles   = $this->roles->filter($roles);
		$actions = $this->actions->filter($actions);

		foreach ($roles as $role) 
		{
			$role = Str::slug($role, '-');

			if ( ! $this->roles->has($role)) 
			{
				throw new InvalidArgumentException("Role {$role} does not exist.");
			}

			foreach ($actions as $action) 
			{
				$action = Str::slug($action, '-');

				if ( ! $this->actions->has($action)) 
				{
					throw new InvalidArgumentException("Action {$action} does not exist.");
				}

				$this->assign($role, $action, $allow);
				$this->sync();
			}
		}

		return $this;
	}

	/**
	 * Assign a key combination of $roles + $actions to have access
	 * 
	 * @access  protected
	 * @param   mixed   $roles          A key or string representation of roles
	 * @param   mixed   $actions        A key or string representation of action name
	 * @param   bool    $allow
	 * @return  void
	 */
	protected function assign($role = null, $action = null, $allow = true)
	{
		if ( ! (is_numeric($role) and $this->roles->exist($role)))
		{
			$role = $this->roles->search($role);
		}

		if ( ! (is_numeric($action) and $this->actions->exist($action)))
		{
			$action = $this->actions->search($action);
		}

		if ( ! is_null($role) and ! is_null($action))
		{
			$key             = $role.':'.$action;
			$this->acl[$key] = $allow;
		}
	}

	/**
	 * Shorthand function to deny access for single or multiple 
	 * $roles and $actions
	 * 
	 * @access  public
	 * @param   mixed   $roles          A string or an array of roles
	 * @param   mixed   $actions        A string or an array of action name
	 * @return  bool
	 */
	public function deny($roles, $actions) 
	{
		return $this->allow($roles, $actions, false);
	}

	/**
	 * Forward call to roles or actions.
	 *
	 * @access public
	 * @param  string   $type           'roles' or 'actions'
	 * @param  string   $operation
	 * @param  array    $parameters
	 * @return Acl\Fluent
	 */
	public function passthru($type, $operation, $parameters)
	{
		return call_user_func_array(array($this->{$type}, $operation), $parameters);
	}

	/**
	 * Magic method to mimic roles and actions manipulation
	 */
	public function __call($method, $parameters)
	{
		if ($method === 'acl') return $this->acl;
		
		$passthru  = array('roles', 'actions');

		// Not sure whether these is a good approach, allowing a passthru 
		// would allow more expressive structure but at the same time lack 
		// the call to `$this->sync()`, this might cause issue when a request
		// contain remove and add roles/actions.
		if (in_array($method, $passthru)) return $this->{$method};
		
		// Preserve legacy CRUD structure for actions and roles.
		$matcher = '/^(add|fill|rename|has|get|remove)_(role|action)(s?)$/';

		if (preg_match($matcher, $method, $matches))
		{
			$operation = $matches[1];
			$type      = $matches[2].'s';
			$multi_add = (isset($matches[3]) and $matches[3] === 's' and $operation === 'add');

			( !! $multi_add) and $operation = 'fill';
			
			$result = $this->passthru($type, $operation, $parameters);

			if ($operation === 'has') return $result;
		}

		return $this->sync();
	}
}