<?php namespace Orchestra;

use Laravel\Messages as Laravel_Messages;

class Messages extends Laravel_Messages 
{
	/**
	 * Add a message to the collector.
	 *
	 * <code>
	 *		// Add a message for the e-mail attribute
	 *		Message::make('email', 'The e-mail address is invalid.');
	 * </code>
	 *
	 * @static
	 * @param  string  $key
	 * @param  string  $message
	 * @return void
	 */
	public static function make($key, $message)
	{
		$instance = new static();

		$instance->add($key, $message);

		return $instance;
	}

	public function serialize()
	{
		return serialize($this);
	}
}