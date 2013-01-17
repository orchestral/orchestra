<?php

Bundle::start('orchestra');

class FacadeTest extends PHPUnit_Framework_TestCase {
	
	/**
	 * Test Orchestra::VERSION
	 *
	 * @test
	 */
	public function testVersionSyntax()
	{
		$version = Orchestra::VERSION;

		$this->assertRegExp('/(\d{1,5})\.(\d{1,5})\.(\d{1,5})(\-[a|b]\d{1,5})?/', 
			$version);
	}
}