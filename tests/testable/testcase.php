<?php namespace Orchestra\Testable;

use \Auth, 
	\Bundle,
	\Config,
	\Cookie,
	\DB,
	\Event,
	\File,
	\Orchestra as O,
	\PHPUnit_Framework_TestCase;

abstract class TestCase extends PHPUnit_Framework_TestCase {

	/**
	 * The client instance.
	 *
	 * @var Orchestra\Testable\CLient
	 */
	protected $client;

	/**
	 * The application instance.
	 *
	 * @var Orchestra\Testable\Application
	 */
	protected $app;

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		$base_path =  Bundle::path('orchestra').'tests'.DS.'fixtures'.DS;
		set_path('storage', $base_path.'storage'.DS);

		if ( ! Event::listeners('orchestra.testable: setup-db'))
		{
			Event::listen('orchestra.testable: setup-db', function ()
			{
				Config::set('database.connections.testdb', array(
					'driver'   => 'sqlite',
					'database' => ':memory:',
					'prefix'   => '',
				));
			});
		}

		$this->createClient();
		$this->createApplication();
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		$this->shutdownApplication();
		unset($this->client);
		Event::first('orchestra.testable: teardown-db');
	}

	/**
	 * Call a controller.
	 *
	 * @access public
	 * @return mixed
	 */
	public function call()
	{
		return call_user_func_array(array($this->client, 'call'), func_get_args());
	}

	/**
	 * Create a new client.
	 *
	 * @access public
	 * @return Orchestra\Testable\Client
	 */
	public function createClient()
	{
		$this->client = new Client;
	}

	/**
	 * Mock login as a user
	 *
	 * @access public			
	 * @param  mixed    $user   Login as a user when $user is instance of 
	 *                          Orchestra\Model\User
	 * @param  mixed    $driver
	 * @return void
	 */
	public function be(\Orchestra\Model\User $user = null, $driver = null)
	{
		if (is_null($user))
		{
			Auth::driver($driver)->logout();
			return ;
		}

		Auth::driver($driver)->login($user->id);
	}

	/**
	 * Create application
	 *
	 * @access public
	 * @return void
	 */
	public function createApplication()
	{
		$this->app = new Application;
	}

	/**
	 * Remove Application.
	 * 
	 * @access public
	 * @return void
	 */
	public function removeApplication()
	{
		if ($this->app instanceof Application) $this->app->remove();
	}

	/**
	 * Shutdown Application.
	 * 
	 * @access public
	 * @return void
	 */
	public function shutdownApplication()
	{
		if ($this->app instanceof Application) $this->app->shutdown();
	}

	/**
	 * Restart Application.
	 * 
	 * @access public
	 * @return void
	 */
	public function restartApplication()
	{
		$this->removeApplication();
		$this->createApplication();
	}

	/**
	 * Assert request has 200 response.
	 *
	 * @access public
	 * @return void
	 */
	public function assertResponseOk()
	{
		$this->assertResponseIs(200);
	}

	/**
	 * Assert request has 404 response.
	 *
	 * @access public
	 * @param  string   $redirect
	 * @return void
	 */
	public function assertResponseNotFound()
	{
		$this->assertResponseIs(404);
	}

	/**
	 * Assert request has response with selected status.
	 *
	 * @access public
	 * @param  string   $status
	 * @return void
	 */
	public function assertResponseIs($status = 200)
	{
		$response = $this->client->response;
		$this->assertInstanceOf('\Laravel\Response', $response);
		$this->assertEquals($status, $response->foundation->getStatusCode());
	}

	/**
	 * Assert view is added.
	 *
	 * @access public
	 * @param  string   $view
	 * @return void
	 */
	public function assertViewIs($view)
	{
		$response = $this->client->response;
		$this->assertEquals($view, $response->content->view);
	}

	/**
	 * Assert view has a given piece of bound data.
	 *
	 * @access public	
	 * @param  string   $key
	 * @param  mixed    $value
	 * @return void
	 */
	public function assertViewHas($key, $value = null)
	{
		if (is_array($key)) return $this->assertViewHasAll($key);

		$content = $this->client->response->content->data;

		$this->assertTrue(isset($content[$key]));

		if ( ! is_null($value))
		{
			$this->assertEquals($content[$key], $value);
		}
	}

	/**
	 * Assert that the view has a given list of bound data.
	 *
	 * @param  array  $bindings
	 * @return void
	 */
	public function assertViewHasAll(array $bindings)
	{
		foreach ($bindings as $key => $value)
		{
			if (is_int($key)) 
			{
				$this->assertViewHas($value);
			}
			else
			{
				$this->assertViewHas($key, $value);
			}
		}
	}

	/**
	 * Assert that the session has a given list of values.
	 *
	 * @param  string|array  $key
	 * @param  mixed  $value
	 * @return void
	 */
	public function assertSessionHas($key, $value = null)
	{
		if (is_array($key)) return $this->assertSessionHasAll($key);

		if (is_null($value))
		{
			$this->assertTrue(\Session::has($key));
		}
		else
		{
			$this->assertEquals($value, \Session::get($key));
		}
	}

	/**
	 * Assert that the session has a given list of values.
	 *
	 * @param  array  $bindings
	 * @return void
	 */
	public function assertSessionHasAll(array $bindings)
	{
		foreach ($bindings as $key => $value)
		{
			if (is_int($key))
			{
				$this->assertSessionHas($value);
			}
			else
			{
				$this->assertSessionHas($key, $value);
			}
		}
	}

	/**
	 * Assert that the session has errors bound.
	 *
	 * @return void
	 */
	public function assertSessionHasErrors()
	{
		return $this->assertSessionHas('errors');
	}

	/**
	 * Assert that the session has messages bound.
	 *
	 * @return void
	 */
	public function assertMessagesHas()
	{
		if (is_array($key)) return $this->assertMessagesHasAll($key);

		$messages = \Orchestra\Support\Messages::make();

		if (is_null($value))
		{
			$this->assertTrue($messages->has($key));
		}
		else
		{
			$this->assertEquals($value, $messages->get($key));
		}
	}

	/**
	 * Assert that the messages has a given list of values.
	 *
	 * @param  array  $bindings
	 * @return void
	 */
	public function assertMessagesHasAll(array $bindings)
	{
		foreach ($bindings as $key => $value)
		{
			if (is_int($key))
			{
				$this->assertMessagesHas($value);
			}
			else
			{
				$this->assertMessagesHas($key, $value);
			}
		}
	}

	/**
	 * Assert request is redirected.
	 *
	 * @access public
	 * @return void
	 */
	public function assertRedirected()
	{
		$response = $this->client->response;
		$this->assertInstanceOf('\Laravel\Redirect', $response);
		$this->assertEquals(302, $response->foundation->getStatusCode());
	}

	/**
	 * Assert request is redirected to.
	 *
	 * @access public
	 * @param  string   $uri
	 * @return void
	 */
	public function assertRedirectedTo($uri)
	{
		$response = $this->client->response;
		$this->assertRedirected();
		$this->assertEquals($uri, $response->foundation->headers->get('location'));
	}
}