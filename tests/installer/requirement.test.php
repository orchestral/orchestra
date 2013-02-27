<?php namespace Orchestra\Tests\Installer;

\Bundle::start('orchestra');

class RequirementTest extends \PHPUnit_Framework_TestCase {
	
	/**
	 * Object stub.
	 *
	 * @var Orchestra\Installer\Requirement
	 */
	protected $stub = null;

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		\Session::$instance = null;
		\Session::load();

		$this->stub = new \Orchestra\Installer\Requirement(
			new \Orchestra\Installer\Publisher
		);
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		unset($this->stub);
		
		\Session::$instance = null;
	}

	/**
	 * Test Orchestra\Installer\Requirement can be constructed.
	 *
	 * @test
	 * @group installer
	 */
	public function testConstructRequirement()
	{
		$this->assertInstanceOf('\Orchestra\Installer\Requirement', 
			$this->stub);
	}

	/**
	 * Test Orchestra\Installer\Requirement::installable() return a boolean.
	 *
	 * @test
	 * @group installer
	 */
	public function testInstallableMethodReturnBoolean()
	{
		$refl = new \ReflectionObject($this->stub);
		$installable = $refl->getProperty('installable');
		$installable->setAccessible(true);

		$this->assertTrue(is_bool($this->stub->installable()));
		$this->assertTrue(is_bool($installable->getValue($this->stub)));
		$this->assertEquals($installable->getValue($this->stub), $this->stub->installable());
	}

	/**
	 * Test Orchestra\Installer\Requirement::checklist() return an array.
	 *
	 * @test
	 * @group installer
	 */
	public function testChecklistMethodReturnArray()
	{
		$refl = new \ReflectionObject($this->stub);
		$checklist = $refl->getProperty('checklist');
		$checklist->setAccessible(true);

		$this->assertTrue(is_array($this->stub->checklist()));
		$this->assertTrue(is_array($checklist->getValue($this->stub)));
		$this->assertEquals($checklist->getValue($this->stub), $this->stub->checklist());
	}

	/**
	 * Test Orchestra\Installer\Requirement::checklist() method is not 
	 * writable.
	 *
	 * @test
	 * @group installer
	 */
	public function testChecklistMethodIsNotWritable()
	{
		$mock = $this->getMock('\Orchestra\Installer\Publisher', array('publish'));
		$mock->expects($this->any())
			->method('publish')
			->will($this->throwException(new \RuntimeException));

		$stub = new \Orchestra\Installer\Requirement($mock);

		$this->assertFalse($stub->installable());
	}
}