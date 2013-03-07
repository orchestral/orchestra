<?php namespace Orchestra\Tests\Widget;

\Bundle::start('orchestra');

class MenuTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Stub instance.
	 *
	 * @var Orchestra\Widget\Pane
	 */
	private $stub = null;

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		$this->stub = new \Orchestra\Widget\Menu('stub');
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		unset($this->stub);
	}

	/**
	 * Test instanceof stub.
	 *
	 * @test
	 * @group core
	 * @group widget
	 */
	public function testInstanceOfMenu()
	{
		$this->assertInstanceOf('\Orchestra\Widget\Menu', $this->stub);

		$refl   = new \ReflectionObject($this->stub);
		$type   = $refl->getProperty('type');
		$config = $refl->getProperty('config');

		$type->setAccessible(true);
		$config->setAccessible(true);

		$this->assertEquals(array('defaults' => array('title' => '', 'link' => '#')), 
			$config->getValue($this->stub));
		$this->assertEquals('menu', $type->getValue($this->stub));
	}

	/**
	 * Test add an item return properly.
	 *
	 * @test
	 * @group core
	 * @group widget
	 */
	public function testAddMethod()
	{
		$expected = array(
			'foo' => new \Orchestra\Support\Fluent(array(
				'title'   => 'hello world',
				'link'    => '#',
				'id'      => 'foo',
				'childs'  => array(),
			)),
			'foobar' => new \Orchestra\Support\Fluent(array(
				'title'   => 'hello world 2',
				'link'    => '#',
				'id'      => 'foobar',
				'childs'  => array(),
			)),
		);

		$this->stub->add('foo', function ($item)
		{
			$item->title = 'hello world';
		});

		$this->stub->add('foobar', 'after:foo', function ($item)
		{
			$item->title = 'hello world 2';
		});

		$this->assertEquals($expected, $this->stub->get());
	}
}
