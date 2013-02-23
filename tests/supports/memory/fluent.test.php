<?php namespace Orchestra\Tests\Supports\Memory;

\Bundle::start('orchestra');

class FluentTest extends \Orchestra\Testable\TestCase {

	/**
	 * Test Orchestra\Support\Memory\Fluent::initiate() method.
	 *
	 * @test
	 * @group support
	 */
	public function testInitiateMethod()
	{
		$stub = \Orchestra\Support\Memory::make('fluent.stub', array(
			'table' => 'orchestra_options',
		));

		$this->assertInstanceOf('\Orchestra\Support\Memory\Fluent', $stub);
	}

	/**
	 * Test Orchestra\Support\Memory\Fluent::shutdown() method.
	 *
	 * @test
	 * @group support
	 */
	public function testShutdownMethod()
	{
		$stub = new \Orchestra\Support\Memory\Fluent('stub', array(
			'table' => 'orchestra_options',
		));

		$stub->put('foo', 'foobar');
		$stub->put('fluent-stub', 'Foobar was awesome');
		$stub->shutdown();

		$stub = new \Orchestra\Support\Memory\Fluent('stub', array(
			'table' => 'orchestra_options',
		));

		$this->assertEquals('foobar', $stub->get('foo'));
		$this->assertEquals('Foobar was awesome', $stub->get('fluent-stub'));

		$stub->put('fluent-stub', 'Foobar is awesome');
		$stub->shutdown();

		$db = \DB::table('orchestra_options')->where_name('fluent-stub')->first();
		$this->assertEquals('s:17:"Foobar is awesome";', $db->value);

		$db = \DB::table('orchestra_options')->where_name('foo')->first();
		$this->assertEquals('s:6:"foobar";', $db->value);
	}
}