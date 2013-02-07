<?php

Bundle::start('orchestra');

class WidgetPaneTest extends PHPUnit_Framework_TestCase {

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
		$this->stub = new Orchestra\Widget\Pane('stub');
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
	 */
	public function testInstanceOfPane()
	{
		$expected = array(
			'defaults' => array(
				'attr'    => array(),
				'title'   => '',
				'content' => '',
				'html'    => '',
			),
		);

		$this->assertInstanceOf('Orchestra\Widget\Pane', $this->stub);

		$refl   = new \ReflectionObject($this->stub);
		$type   = $refl->getProperty('type');
		$config = $refl->getProperty('config');

		$type->setAccessible(true);
		$config->setAccessible(true);

		$this->assertEquals($expected, $config->getValue($this->stub));
		$this->assertEquals('pane', $type->getValue($this->stub));
	}

	/**
	 * Test add an item return properly.
	 *
	 * @test
	 */
	public function testAddMethod()
	{
		$expected = array(
			'foo' => new Laravel\Fluent(array(
				'attr'    => array(),
				'title'   => '',
				'content' => 'hello world',
				'html'    => '',
				'id'      => 'foo',
				'childs'  => array(),
			)),
			'foobar' => new Laravel\Fluent(array(
				'attr'    => array(),
				'title'   => 'hello world',
				'content' => '',
				'html'    => '',
				'id'      => 'foobar',
				'childs'  => array(),
			)),
		);

		$this->stub->add('foo', function ($item)
		{
			$item->content('hello world');
		});

		$this->stub->add('foobar', 'after:foo', function ($item)
		{
			$item->title('hello world');
		});

		$this->assertEquals($expected, $this->stub->get());
	}
}
