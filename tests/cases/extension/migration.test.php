<?php namespace Orchestra\Tests\Extension;

class MigrationTest extends \PHPUnit_Framework_TestCase {
	
	/**
	 * Construct a new Migration successful when Orchestra Platform is 
	 * installed.
	 *
	 * @test
	 */
	public function testMigrationSuccessfulWhenIsInstalled()
	{
		\Orchestra\Installer::$status = true;
		$stub = new MigrationStub;

		$this->assertInstanceOf('\Orchestra\Extension\Migration', $stub);
	}

	/**
	 * Construct a new Migration throws exception when Orchestra Platform 
	 * is not installed.
	 *
	 * @test
	 * @expectedException \RuntimeException
	 */
	public function testMigrationThrowsExceptionWhenNotInstalled()
	{
		\Orchestra\Installer::$status = false;
		$stub = new MigrationStub;
	}
}

class MigrationStub extends \Orchestra\Extension\Migration {

	public function up() {}
	public function down() {}
}