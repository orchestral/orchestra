<?php namespace Orchestra\Tests\Supports\Memory;

\Bundle::start('orchestra');

class DriverTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Test Orchestra\Support\Memory\Driver::initiate()
	 *
	 * @test
	 * @group support
	 */
	public function testInitiateMethod()
	{
		$stub = new MemoryDriverStub;
		$this->assertTrue($stub->initiated);
	}

	/**
	 * Test Orchestra\Support\Memory\Driver::shutdown()
	 *
	 * @test
	 * @group support
	 */
	public function testShutdownMethod()
	{
		$stub = new MemoryDriverStub;
		$this->assertFalse($stub->shutdown);
		$stub->shutdown();
		$this->assertTrue($stub->shutdown);
	}

	/**
	 * Test Orchestra\Support\Memory\Driver::stringify()
	 *
	 * @test
	 * @group support
	 */
	public function testStringifyMethod()
	{
		$base_path = \Bundle::path('orchestra').'tests'.DS.'supports'.DS.'memory'.DS.'stub'.DS;

		$stub     = new MemoryDriverStub;
		$expected = 'a:2:{s:4:"name";s:9:"Orchestra";s:5:"theme";a:2:{s:7:"backend";s:7:"default";s:8:"frontend";s:7:"default";}}';
		$stream   = fopen($base_path.'driver1.stub.php', 'r');
		$output   = $stub->stringify($stream);

		$this->assertEquals($expected, $output);

		$expected = array(
			'name'  => 'Orchestra',
			'theme' => array(
				'backend' => 'default',
				'frontend' => 'default',
			),
		);

		$this->assertEquals($expected, unserialize($output));

		$stub     = new MemoryDriverStub;
		$expected = 'a:2:{s:4:"name";s:9:"Orchestra";s:5:"theme";a:2:{s:7:"backend";s:7:"default";s:8:"frontend";s:7:"default";}}';
		$stream   = fopen($base_path.'driver2.stub.php', 'r');
		$output   = $stub->stringify($stream);

		$this->assertEquals($expected, $output);

		$expected = array(
			'name'  => 'Orchestra',
			'theme' => array(
				'backend' => 'default',
				'frontend' => 'default',
			),
		);

		$this->assertEquals($expected, unserialize($output));
	}
}

class MemoryDriverStub extends \Orchestra\Support\Memory\Driver {

	public $initiated = false;
	public $shutdown  = false;

	public function initiate() 
	{
		$this->initiated = true;
	}

	public function shutdown() 
	{
		$this->shutdown = true;
	}
}