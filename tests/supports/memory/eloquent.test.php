<?php namespace Orchestra\Tests\Supports\Memory;

\Bundle::start('orchestra');

class EloquentTest extends \Orchestra\Testable\TestCase {

	/**
	 * Test Orchestra\Support\Memory\Eloquent::initiate() method.
	 *
	 * @test
	 * @group support
	 */
	public function testInitiateMethod()
	{
		$stub = \Orchestra\Support\Memory::make('eloquent.stub', array(
			'name' => '\Orchestra\Tests\Supports\Memory\OptionStub',
		));

		$this->assertInstanceOf('\Orchestra\Support\Memory\Eloquent', $stub);
	}

	/**
	 * Test Orchestra\Support\Memory\Eloquent::shutdown() method.
	 *
	 * @test
	 * @group support
	 */
	public function testShutdownMethod()
	{
		$stub = new \Orchestra\Support\Memory\Eloquent('stub', array(
			'name' => '\Orchestra\Tests\Supports\Memory\OptionStub',
		));

		$stub->put('foo', 'foobar');
		$stub->put('eloquent-stub', 'Foobar was awesome');
		$stub->shutdown();

		$stub = new \Orchestra\Support\Memory\Eloquent('stub', array(
			'name' => '\Orchestra\Tests\Supports\Memory\OptionStub',
		));

		$this->assertEquals('Foobar was awesome', $stub->get('eloquent-stub'));

		$stub->put('eloquent-stub', 'Foobar is awesome');
		$stub->shutdown();

		$eloquent = OptionStub::where('name', '=', 'eloquent-stub')->first();
		$this->assertEquals('s:17:"Foobar is awesome";', $eloquent->value);

		$eloquent = OptionStub::where('name', '=', 'foo')->first();
		$this->assertEquals('s:6:"foobar";', $eloquent->value);
	}
}

class OptionStub extends \Eloquent {

	/**
	 * Define the table name.
	 *
	 * @var string
	 */
	public static $table = 'orchestra_options';

	/**
	 * Timestamps
	 * 
	 * @var boolean
	 */
	public static $timestamps = false;

}