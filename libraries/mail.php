<?php namespace Orchestra;

use \Closure, \IoC;

class Mail {

	/**
	 * Pretending to send an email.
	 *
	 * @var boolean
	 */
	public static $pretending = false;

	/**
	 * Set pretend status.
	 *
	 * @static
	 * @access public
	 * @param  boolean  $pretend
	 * @return void
	 */
	public static function pretend($pretend = false)
	{
		static::$pretending = $pretend;
	}

	/**
	 * Make a new Orchestra\Mail instance.
	 *
	 * <code>
	 * 		Orchestra\Mail::send('view.file', array(), function ($mail)
	 * 		{
	 * 			$mail->to('example@test.com', "Username")
	 * 				->subject("An awesome title")
	 * 				->send();
	 * 		});
	 * </code>
	 *
	 * @static
	 * @access public
	 * @param  string   $view
	 * @param  mixed    $data
	 * @param  Closure  $callback
	 * @return self
	 */
	public static function send($view, $data = array(), Closure $callback)
	{
		$instance = new static($view, $data, $callback);

		// Automatically send the e-mail.
		if ( ! static::$pretending) $instance->mailer->send();

		return $instance;
	}

	/**
	 * Swiftmailer mail instance.
	 *
	 * @var  Object
	 */
	protected $mailer = null;

	/**
	 * View instance.
	 *
	 * @var  Laravel\View
	 */
	protected $view = null;

	/**
	 * Construct a new instance.
	 *
	 * @access public
	 * @param  string   $view
	 * @param  mixed    $data
	 * @param  Closure  $callback
	 * @return void
	 */
	public function __construct($view, $data = array(), Closure $callback)
	{
		$this->view   = View::make($view, $data);
		$this->mailer = IoC::resolve('orchestra.mailer');

		if ( ! static::$pretending)
		{
			$this->mailer->body($this->view);
			$this->mailer->html(true);
		}

		call_user_func($callback, $this->mailer);
	}

	/**
	 * Check whether email was actually sent.
	 *
	 * @access public
	 * @param  mixed    $emails
	 * @return boolean
	 */
	public function was_sent($emails)
	{
		if (static::$pretending) return true;

		return $this->mailer->was_sent($emails);
	}
}
