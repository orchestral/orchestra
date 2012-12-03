
<?php

use Laravel\Request,
	Laravel\Routing\Controller;

class Controller_Runner
{
	public static function call($destination, $parameters = array(), $method = 'GET')
	{
		Request::foundation()->server->add(array(
			'REQUEST_METHOD' => $method,
		));

		return Controller::call($destination, $parameters);
	}

	public static function get($destination, $parameters = array())
	{
		static::flush();

		return static::call($destination, $parameters, 'GET');
	}

	public static function post($destination, $post_data, $parameters = array())
	{
		static::flush();

		Request::foundation()->request->add($post_data);

		return static::call($destination, $parameters, 'POST');
	}

	public static function flush()
	{
		$request = Request::foundation()->request;

		foreach ($request->keys() as $key)
		{
			$request->remove($key);
		}
	}
}

abstract class Controller_TestCase extends PHPUnit_Framework_TestCase
{
	public function call($destination, $parameters = array(), $method = 'GET')
	{
		Controller_Runner::call($destination, $parameters, $method);
	}

	public function get($destination, $parameters = array())
	{
		return Controller_Runner::get($destination, $parameters);
	}

	public function post($destination, $post_data, $parameters = array())
	{
		return Controller_Runner::post($destination, $post_data, $parameters);
	}
}
