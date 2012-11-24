<?php namespace Orchestra;

use \Closure, \IoC;

class Mail {

	/**
	 * Make a new Orchestra\Mail instance.
	 *
	 * <code>
	 * 		Orchestra\Mail::make('view.file', array(), function ($mail)
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
	public static function make($view, $data = array(), Closure $callback)
	{
		$instance = new static($view, $data, $callback);

		return $instance->mailer;
	}

	/**
	 * Swiftmailer mail instance.
	 *
	 * @var  Object
	 */
	private $mailer = null;

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
		$view         = View::make($view, $data);
		$this->mailer = IoC::resolve('orchestra.mailer');

		$this->mailer->body($view->render(), 'text/html');

		call_user_func($callback, $this->mailer);
	}

	/**
	 * Dynamically retrieve the value.
	 */
	public function __get($method)
	{
		return $this->{$method};
	}
}
