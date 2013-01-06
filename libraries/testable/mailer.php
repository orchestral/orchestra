<?php namespace Orchestra\Testable;

class Mailer {

	protected $was_sent = false;

	/**
	 * Get a Swift Mailer driver instance.
	 *
	 * @param  string  $driver
	 * @return Driver
	 */
	public static function instance($driver = null)
	{
		return new static();
	}

	public function subject($text) 
	{
		return $this;
	}

	public function body($text) 
	{
		return $this;
	}

	public function html($use_html) 
	{
		return $this;
	}

	public function from($email, $name) 
	{
		return $this;
	}

	public function to($email, $name) 
	{
		return $this;
	}

	public function cc($email, $name) 
	{
		return $this;
	}

	public function bcc($email, $name) 
	{
		return $this;
	}

	public function send() 
	{
		$this->was_sent = true;
	}

	public function was_sent($email)
	{
		return $this->was_sent;
	}
}