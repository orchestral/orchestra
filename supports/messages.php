<?php namespace Orchestra\Support;

use \Session,
	Laravel\Messages as M;

class Messages extends M {

	/**
	 * Messages instance.
	 *
	 * @var Messages
	 */
	public static $instance = null;

	/**
	 * Add a message to the collector.
	 *
	 * <code>
	 *		// Add a message for the e-mail attribute
	 *		$msg = Message::make();
	 *		$msg->add('email', 'The e-mail address is invalid.');
	 * </code>
	 *
	 * @static
	 * @return void
	 */
	public static function make()
	{
		if (is_null(static::$instance))
		{
			static::$instance = new static();
		}

		return static::$instance;
	}

	/**
	 * Shudown the message instance.
	 *
	 * @static
	 * @access public
	 * @return void
	 */
	public static function shutdown()
	{
		if ( ! is_null(static::$instance)) static::$instance->save();
	}

	/**
	 * Retrieve Message instance from Session, the data should be in
	 * serialize, so we need to unserialize it first.
	 *
	 * @static
	 * @access public
	 * @return Messages
	 */
	public static function retrieve()
	{
		$message = null;

		if (Session::has('message'))
		{
			$message = @unserialize(Session::get('message', ''));
		}

		Session::forget('message');

		return $message;
	}

	/**
	 * Add a message to the collector.
	 *
	 * <code>
	 *		// Add a message for the e-mail attribute
	 *		$messages->add('email', 'The e-mail address is invalid.');
	 * </code>
	 *
	 * @param  string  $key
	 * @param  string  $message
	 * @return void
	 */
	public function add($key, $message)
	{
		parent::add($key, $message);

		return $this;
	}

	/**
	 * Save current instance to Session flash during shutdown.
	 *
	 * @access public
	 * @return void
	 */
	public function save()
	{
		Session::flash('message', $this->serialize());
	}

	/**
	 * Compile the instance into serialize
	 *
	 * @access public
	 * @return string   serialize of this instance
	 */
	public function serialize()
	{
		return serialize($this);
	}
}
