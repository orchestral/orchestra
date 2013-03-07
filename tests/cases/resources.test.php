<?php namespace Orchestra\Tests;

\Bundle::start('orchestra');

class ResourcesTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Stub instance.
	 *
	 * @var  Orchestra\Resources
	 */
	private $stub = null;

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		set_path('app', \Bundle::path('orchestra').'tests'.DS.'fixtures'.DS.'application'.DS);

		$this->stub = \Orchestra\Resources::make('stub', array(
			'name' => 'ResourceStub',
			'uses' => 'stub',
		));
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		set_path('app', path('base').'application'.DS);

		unset($this->stub);
	}

	/**
	 * Test Orchestra\Resources::make().
	 *
	 * @test
	 * @group core
	 * @group resources
	 */
	public function testConstructResource()
	{
		$this->assertInstanceOf('\Orchestra\Resources', $this->stub);
		$this->assertEquals('ResourceStub', $this->stub->name);
		$this->assertEquals('stub', $this->stub->uses);
		$this->assertEquals('ResourceStub', $this->stub->name());
		$this->assertEquals('stub', $this->stub->uses());

		$foo = \Orchestra\Resources::make('foo', array(
			'name' => 'Foobar',
			'uses' => 'foo',
		));

		$this->assertInstanceOf('\Orchestra\Resources', $foo);
		$this->assertEquals('Foobar', $foo->name);
		$this->assertEquals('foo', $foo->uses);
		$this->assertTrue($foo->visible);
		$this->assertEquals('Foobar', $foo->name());
		$this->assertEquals('foo', $foo->uses());
		$this->assertTrue($foo->visible());
	}

	/**
	 * Test Orchestra\Resources::make() thrown an exception.
	 *
	 * @expectedException \InvalidArgumentException
	 * @group core
	 * @group resources
	 */
	public function testMakeAResourceThrowsException()
	{
		$stub = \Orchestra\Resources::make('stubber', array());
	}

	/**
	 * Test adding child attribute thrown exception when is reserved
	 * keywords.
	 *
	 * @expectedException \InvalidArgumentException
	 * @group core
	 * @group resources
	 */
	public function testAddChildUsingReservedKeywordsThrowsException()
	{
		$this->stub->map('visible', 'stub.visible');
	}

	/**
	 * Test adding child attribute value using setter.
	 *
	 * @test
	 * @group core
	 * @group resources
	 */
	public function testAddChildUsingSetter()
	{
		$this->stub->foobar = 'stub.foo';
		$expected = array('foobar' => 'stub.foo');

		$this->assertEquals($expected, $this->stub->childs);
		$this->assertEquals($expected, $this->stub->childs());

		$this->stub->map('hello', 'stub.helloworld');
		$expected = array(
			'foobar' => 'stub.foo',
			'hello'  => 'stub.helloworld',
		);

		$this->assertEquals($expected, $this->stub->childs);
		$this->assertEquals($expected, $this->stub->childs());
	}

	/**
	 * Test set visibility using Orchestra\Resources::show()
	 *
	 * @test
	 * @group core
	 * @group resources
	 */
	public function testDisplayResourceViaShowMethod()
	{
		$this->stub->show();

		$refl       = new \ReflectionObject($this->stub);
		$attributes = $refl->getProperty('attributes');
		$attributes->setAccessible(true);

		$attrib     = $attributes->getValue($this->stub);

		$this->assertTrue($attrib['visible']);
	}

	/**
	 * Test set visibility using Orchestra\Resources::hide()
	 *
	 * @test
	 * @group core
	 * @group resources
	 */
	public function testDisplayResourceViaHideMethod()
	{
		$this->stub->hide();

		$refl       = new \ReflectionObject($this->stub);
		$attributes = $refl->getProperty('attributes');
		$attributes->setAccessible(true);

		$attrib     = $attributes->getValue($this->stub);

		$this->assertFalse($attrib['visible']);
	}

	/**
	 * Test set visibility using Orchestra\Resources::visibility()
	 *
	 * @test
	 * @group core
	 * @group resources
	 */
	public function testResourceVisibilityViaVisibilityMethod()
	{
		$this->stub->visibility(function ()
		{
			return true;
		});

		$refl       = new \ReflectionObject($this->stub);
		$attributes = $refl->getProperty('attributes');
		$attributes->setAccessible(true);

		$attrib     = $attributes->getValue($this->stub);

		$this->assertTrue(value($attrib['visible']));
	}

	/**
	 * Test Orchestra\Resources::call() method.
	 *
	 * @test
	 * @group core
	 * @group resources
	 */
	public function testCallMethod()
	{
		$resource = \Orchestra\Resources::call('stub', 'index', array());

		$this->assertInstanceOf('\Laravel\Response', $resource);
		$this->assertEquals(200, $resource->foundation->getStatusCode());
		$this->assertEquals('stub', $resource->content);

		$resource = \Orchestra\Resources::call('stub', 'redirect', array());

		$this->assertInstanceOf('\Laravel\Redirect', $resource);
		$this->assertEquals(302, $resource->foundation->getStatusCode());
	}

	/**
	 * Test Orchestra\Resources::of() method.
	 *
	 * @test
	 * @group core
	 * @group resources
	 */
	public function testOfMethod()
	{
		$stub = \Orchestra\Resources::of('stub');

		$this->assertEquals($this->stub, $stub);

		$laravel        = \Orchestra\Resources::of('laravel', 'foo');
		$laravel->hello = 'stub'; 

		$this->assertInstanceOf('\Orchestra\Resources', $laravel);
		$this->assertInstanceOf('\Laravel\Response', 
			\Orchestra\Resources::call('laravel.hello', 'index', array()));
	}

	/**
	 * Test Orchestra\Resources::__call() method throws an exception.
	 *
	 * @expectedException \InvalidArgumentException
	 * @group core
	 * @group resources
	 */
	public function testCallMethodThrowsException()
	{
		$stub = \Orchestra\Resources::of('stub');
		$stub->hello('invalid request');
	}

	/**
	 * Test Orchestra\Resources::response() method when $content 
	 * is string.
	 *
	 * @test
	 * @group core
	 * @group resources
	 */
	public function testResponseMethodWhenIsString()
	{
		$response = \Orchestra\Resources::response('login');
		
		$this->assertEquals('login', $response);
	}

	/**
	 * Test Orchestra\Resources::response() method when $content 
	 * is null.
	 *
	 * @test
	 * @group core
	 * @group resources
	 */
	public function testResponseMethodWhenIsFalse()
	{
		$response = \Orchestra\Resources::response(false);

		$this->assertInstanceOf('\Laravel\Response', $response);
		$this->assertEquals(404, $response->foundation->getStatusCode());
	}

	/**
	 * Test Orchestra\Resources::response() method when $content 
	 * is instanceof Laravel\Redirect.
	 *
	 * @test
	 * @group core
	 * @group resources
	 */
	public function testResponseMethodWhenIsRedirect()
	{
		$response = \Orchestra\Resources::response(
			\Redirect::to(handles('login'))
		);

		$this->assertInstanceOf('\Laravel\Redirect', $response);
		$this->assertEquals(302, $response->foundation->getStatusCode());
		$this->assertEquals(handles('login'), 
			$response->foundation->headers->get('location'));
	}

	/**
	 * Test Orchestra\Resources::response() method when $content 
	 * is instanceof Laravel\Response.
	 *
	 * @test
	 * @group core
	 * @group resources
	 */
	public function testResponseMethodWhenIsResponse()
	{
		$response = \Orchestra\Resources::response(
			\Response::make('found', 200)
		);

		$this->assertInstanceOf('\Laravel\Response', $response);
		$this->assertEquals(200, $response->foundation->getStatusCode());

		$response = \Orchestra\Resources::response(
			\Response::make('server error', 500)
		);

		$this->assertInstanceOf('\Laravel\Response', $response);
		$this->assertEquals(500, $response->foundation->getStatusCode());
	}

	/**
	 * Test Orchestra\Resources::response() method when $content 
	 * is instanceof Closure.
	 *
	 * @test
	 * @group core
	 * @group resources
	 */
	public function testResponseMethodWhenIsClosure()
	{
		$response = \Orchestra\Resources::response('login', function ($content)
		{
			return \Redirect::to(handles($content));
		});

		$this->assertInstanceOf('\Laravel\Redirect', $response);
		$this->assertEquals(302, $response->foundation->getStatusCode());
		$this->assertEquals(handles('login'), 
			$response->foundation->headers->get('location'));
	}
}
