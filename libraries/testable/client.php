<?php namespace Orchestra\Testable;

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
	public function call($destination, $parameters = array(), $method = 'GET')
	{
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
		$this->flush();

		return $this->call($destination, $parameters, 'GET');
	}

	/**
	 * Alias for POST request.
	 * 
	 * @access public
	 * @param  string   $destination
	 * @param  array    $parameters
	 * @return Response
	 */
	public function post($destination, $post_data, $parameters = array())
	{
		$this->flush();

		Request::foundation()->request->add($post_data);

		return $this->call($destination, $parameters, 'POST');
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
	}
}