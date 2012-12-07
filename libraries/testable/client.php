<?php namespace Orchestra\Testable;

use \Controller,
	\Request,
	Symfony\Component\HttpFoundation\LaravelRequest;

class Client {

	/**
	 * Call a request.
	 *
	 * @access public
	 * @param  string   $destination
	 * @param  array    $parameters
	 * @param  string   $method
	 * @return Response
	 */
	public function call($destination, $parameters = array(), $method = 'GET', $data = array())
	{
		$this->flush();

		Request::foundation()->request->add($data);

		Request::foundation()->server->add(array(
			'REQUEST_METHOD' => $method,
		));

		return Controller::call($destination, $parameters);
	}

	/**
	 * Alias for GET request.
	 * 
	 * @access public
	 * @param  string   $destination
	 * @param  array    $parameters
	 * @return Response
	 */
	public function get($destination, $parameters = array())
	{
		return $this->call($destination, $parameters, 'GET');
	}

	/**
	 * Alias for POST request.
	 * 
	 * @access public
	 * @param  string   $destination
	 * @param  array    $parameters
	 * @param  array    $data
	 * @return Response
	 */
	public function post($destination,$parameters = array(), $data = array())
	{
		$this->flush();

		return $this->call($destination, $parameters, 'POST', $data);
	}

	/**
	 * Flush the previous request.
	 * 
	 * @access public
	 * @return void
	 */
	public function flush()
	{
		$request = Request::foundation()->request;

		foreach ($request->keys() as $key)
		{
			$request->remove($key);
		}

		Request::$foundation = LaravelRequest::createFromGlobals();
	}
}