<?php namespace Orchestra\Tests;

\Bundle::start('orchestra');

class FacadeTest extends \PHPUnit_Framework_TestCase {
	
	/**
	 * Test Orchestra::VERSION
	 *
	 * @test
	 * @group core
	 */
	public function testVersionSyntax()
	{
		$version = \Orchestra::VERSION;

		$this->assertRegExp('/(\d{1,5})\.(\d{1,5})\.(\d{1,5})(\-[a|b]\d{1,5})?/', 
			$version);
	}

	/**
	 * Test Orchestra Facades.
	 *
	 * @test
	 * @group core
	 */
	public function testOrchestraFacades()
	{
		$this->assertEquals(\Orchestra\Core::acl(), \Orchestra::acl());
		$this->assertEquals(\Orchestra\Core::memory(), \Orchestra::memory());
		$this->assertEquals(\Orchestra\Core::menu(), \Orchestra::menu());
	}
}