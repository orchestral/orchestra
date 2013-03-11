<?php namespace Orchestra\Tests;

\Bundle::start('orchestra');

class ThemeTest extends \Orchestra\Testable\TestCase {

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		parent::setUp();
		\Orchestra\View::$theme = 'frontend';
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		\Orchestra\View::$theme = 'frontend';
		parent::tearDown();
	}

	/**
	 * Test Orchestra\Theme::start() is register propery IoC.
	 *
	 * @test
	 * @group core
	 */
	public function testStartThemeRegisterProperIoC()
	{
		$this->assertTrue(\IoC::registered('orchestra.theme: backend'));
		$this->assertTrue(\IoC::registered('orchestra.theme: frontend'));
	}

	/**
	 * Test Orchestra\Theme::__construct()
	 *
	 * @test
	 * @group core
	 */
	public function testConstructThemeContainer()
	{
		$theme = new \Orchestra\Theme\Container;

		$this->assertInstanceOf('\Orchestra\Theme\Container', $theme);
		$this->assertInstanceOf('\Orchestra\Theme\Container',
			\IoC::resolve('orchestra.theme: frontend'));
		$this->assertInstanceOf('\Orchestra\Theme\Container',
			\IoC::resolve('orchestra.theme: backend'));
		$this->assertInstanceOf('\Orchestra\Theme\Container',
			\Orchestra\Theme::container('frontend', 'default'));
	}

	/**
	 * Test Orchestra\Theme::container()
	 *
	 * @test
	 * @group core
	 */
	public function testThemeContainer()
	{
		$this->assertEquals(\Orchestra\Theme::resolve(),
			\Orchestra\Theme::container(\Orchestra\View::$theme));
		$this->assertTrue(is_array(\Orchestra\Theme::$containers));
	}

	/**
	 * Test Orchestra\Theme::resolve()
	 *
	 * @test
	 * @group core
	 */
	public function testThemeResolver()
	{
		$frontend = \IoC::resolve('orchestra.theme: frontend');
		$backend  = \IoC::resolve('orchestra.theme: backend');

		$this->assertFalse(\Orchestra\Theme::resolve() === $backend);
		$this->assertTrue(\Orchestra\Theme::resolve() === $frontend);

		\Event::fire('orchestra.started: backend');

		$this->assertTrue(\Orchestra\Theme::resolve() === $backend);
		$this->assertFalse(\Orchestra\Theme::resolve() === $frontend);
	}

	/**
	 * Test Orchestra\Theme::__callStatic() passthru methods.
	 *
	 * @test
	 * @group core
	 */
	public function testCallStaticPassthruMethods()
	{
		$theme = \Bundle::path('orchestra').'tests'.DS.'fixtures'.DS.'public'.DS.'themes'.DS;
		
		\Orchestra\Theme::map(array('foo' => 'error.404'));

		$this->assertEquals('error.404', \Orchestra\Theme::path('foo'));
	}
}
