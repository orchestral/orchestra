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

	public function body($text) {}

	public function html($use_html) {}

	public function subject($text) {}

	public function send() 
	{
		$this->was_sent = true;
	}

	public function was_sent($email)
	{
		return $this->was_sent;
	}
}