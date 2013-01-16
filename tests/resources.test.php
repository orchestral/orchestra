<?php

Bundle::start('orchestra');

class ResourcesTest extends PHPUnit_Framework_TestCase {

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
		set_path('app', Bundle::path('orchestra').'tests'.DS.'fixtures'.DS.'application'.DS);

		$this->stub = Orchestra\Resources::make('stub', array(
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
	 */
	public function testMakeAResource()
	{
		$this->assertInstanceOf('Orchestra\Resources', $this->stub);
		$this->assertEquals('ResourceStub', $this->stub->name);
		$this->assertEquals('stub', $this->stub->uses);
		$this->assertEquals('ResourceStub', $this->stub->name());
		$this->assertEquals('stub', $this->stub->uses());

		$foo = Orchestra\Resources::make('foo', array(
			'name' => 'Foobar',
			'uses' => 'foo',
		));

		$this->assertInstanceOf('Orchestra\Resources', $foo);
		$this->assertEquals('Foobar', $foo->name);
		$this->assertEquals('foo', $foo->uses);
		$this->assertTrue($foo->visible);
		$this->assertEquals('Foobar', $foo->name());
		$this->assertEquals('foo', $foo->uses());
		$this->assertTrue($foo->visible());
	}

	/**
	 * Test adding child attribute thrown exception when is reserved
	 * keywords.
	 *
	 * @expectedException InvalidArgumentException
	 */
	public function testAddChildThrownException()
	{
		$this->stub->map('visible', 'stub.visible');
	}

	/**
	 * Test adding child attribute value using setter.
	 *
	 * @test
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
	 */
	public function testVisibilityUsingShow()
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
	 */
	public function testVisibilityUsingHide()
	{
		$this->stub->hide();

		$refl       = new \ReflectionObject($this->stub);
		$attributes = $refl->getProperty('attributes');
		$attributes->setAccessible(true);

		$attrib     = $attributes->getValue($this->stub);

		$this->assertFalse($attrib['visible']);
	}

	/**
	 * Test Orchestra\Resources::call()
	 *
	 * @test
	 */
	public function testCallAResources()
	{
		$resource = Orchestra\Resources::call('stub', 'index', array());

		$this->assertInstanceOf('Laravel\Response', $resource);
		$this->assertEquals(200, $resource->foundation->getStatusCode());
		$this->assertEquals('stub', $resource->content);

		$resource = Orchestra\Resources::call('stub', 'redirect', array());

		$this->assertInstanceOf('Laravel\Redirect', $resource);
		$this->assertEquals(302, $resource->foundation->getStatusCode());
	}

	/**
	 * Test Orchestra\Resources::of()
	 *
	 * @test
	 */
	public function testOfMethod()
	{
		$stub = Orchestra\Resources::of('stub');

		$this->assertEquals($this->stub, $stub);
	}
}
