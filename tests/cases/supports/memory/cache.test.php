<?php namespace Orchestra\Tests\Supports\Memory;

\Bundle::start('orchestra');

class CacheTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		set_path('storage', \Bundle::path('orchestra').'tests'.DS.'fixtures'.DS.'storage'.DS);

		$value = array(
			'name' => 'Orchestra',
			'theme' => array(
				'backend' => 'default',
				'frontend' => 'default',
			),
		);
		\Cache::put('orchestra.memory.cachemock', $value, 10);
	}
	
	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		\Cache::forget('orchestra.memory.cachemock');
		\File::delete(path('storage').'cache/orchestra.memory.default');
		\File::delete(path('storage').'cache/orchestra.memory.cachemock');
		

		set_path('storage', path('base').'storage'.DS);
	}

	/**
	 * Test Orchestra\Support\Memory\Cache::initiate() method.
	 *
	 * @test
	 * @group support
	 */
	public function testInitiateMethod()
	{
		$stub = \Orchestra\Support\Memory::make('cache.cachemock');
		$this->assertEquals('Orchestra', $stub->get('name'));
		$this->assertEquals('default', $stub->get('theme.backend'));
		$this->assertEquals('default', $stub->get('theme.frontend'));
	}
}