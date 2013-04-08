<?php namespace Orchestra\Tests\Theme;

class DefinitionTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Base path.
	 *
	 * @var string
	 */
	private $base_path = null;

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		$this->base_path = \Bundle::path('orchestra').'tests'.DS.'fixtures'.DS.'public'.DS.'themes'.DS;
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		unset($this->base_path);
	}

	/**
	 * Test Orchestra\Theme\Definition::__construct() with valid 
	 * definition file.
	 *
	 * @test
	 * @group theme
	 */
	public function testConstructMethod()
	{
		$stub = new \Orchestra\Theme\Definition($this->base_path.DS.'valid');

		$refl = new \ReflectionObject($stub);
		$item = $refl->getProperty('item');
		$item->setAccessible(true);

		$expected = (object) array(
			'name'     => 'Foo',
			'autoload' => array('start.php'),
		);

		$this->assertInstanceOf('\Orchestra\Theme\Definition', $stub);
		$this->assertEquals(array('start.php'), $stub->autoload);
		$this->assertTrue(is_null($stub->description));
		$this->assertEquals($expected, $item->getValue($stub));
		$this->assertTrue(isset($stub->autoload));
		$this->assertFalse(isset($stub->foo));
	}

	/**
	 * Test Orchestra\Theme\Definition::__construct() throws exception 
	 * when fetching invalid definition file.
	 *
	 * @group theme
	 * @expectedException \RuntimeException
	 */
	public function testConstructMethodThrowsException()
	{
		new \Orchestra\Theme\Definition($this->base_path.DS.'invalid');
	}
}