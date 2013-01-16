<?php

Bundle::start('orchestra');

class InstallerRequirementTest extends PHPUnit_Framework_TestCase {
	
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
		Session::$instance = null;
		Session::load();

		$this->stub = new Orchestra\Installer\Requirement;
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		Session::$instance = null;
	}

	/**
	 * Test Orchestra\Installer\Requirement can be constructed.
	 *
	 * @test
	 */
	public function testConstructInstance()
	{
		$this->assertInstanceOf('Orchestra\Installer\Requirement', 
			$this->stub);
	}

	/**
	 * Test Orchestra\Installer\Requirement::installable() return a boolean.
	 *
	 * @test
	 */
	public function testInstallableMethodReturnBoolean()
	{
		$this->assertTrue(is_bool($this->stub->installable()));
	}

	/**
	 * Test Orchestra\Installer\Requirement::checklist() return an array.
	 *
	 * @test
	 */
	public function testChecklistMethodReturnArray()
	{
		$this->assertTrue(is_array($this->stub->checklist()));
	}
}