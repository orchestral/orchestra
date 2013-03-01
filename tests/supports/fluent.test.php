<?php namespace Orchestra\Tests\Supports;

class FluentTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Test the Fluent constructor.
	 *
	 * @test
	 * @group support
	 */
	public function testAttributesAreSetByConstructor()
	{
		$array  = array('name' => 'Taylor', 'age' => 25);
		$fluent = new \Orchestra\Support\Fluent($array);

		$refl = new \ReflectionObject($fluent);
		$attributes = $refl->getProperty('attributes');
		$attributes->setAccessible(true);

		$this->assertEquals($array, $attributes->getValue($fluent));
		$this->assertEquals($array, $fluent->get_attributes());
	}

	/**
	 * Test the Fluent::get() method.
	 *
	 * @test
	 * @group support
	 */
	public function testGetMethodReturnsAttribute()
	{
		$fluent = new \Orchestra\Support\Fluent(array('name' => 'Taylor'));

		$this->assertEquals('Taylor', $fluent->get('name'));
		$this->assertEquals('Default', $fluent->get('foo', 'Default'));
		$this->assertEquals('Taylor', $fluent->name);
		$this->assertNull($fluent->foo);
	}

	/**
	 * Test the Fluent magic methods can be used to set attributes.
	 *
	 * @test
	 * @group support
	 */
	public function testMagicMethodsCanBeUsedToSetAttributes()
	{
		$fluent = new \Orchestra\Support\Fluent;

		$fluent->name = 'Taylor';
		$fluent->developer();
		$fluent->age(25);

		$this->assertEquals('Taylor', $fluent->name);
		$this->assertTrue($fluent->developer);
		$this->assertEquals(25, $fluent->age);
		$this->assertInstanceOf('\Orchestra\Support\Fluent', $fluent->programmer());
	}

	/**
	 * Test the Orchestra\Support\Fluent::__isset() method.
	 *
	 * @test
	 * @group support
	 */
	public function testIssetMagicMethod()
	{
		$array  = array('name' => 'Taylor', 'age' => 25);
		$fluent = new \Orchestra\Support\Fluent($array);

		$this->assertTrue(isset($fluent->name));

		unset($fluent->name);

		$this->assertFalse(isset($fluent->name));
	}
}