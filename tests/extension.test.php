<?php

class ExtensionTest extends PHPUnit_Framework_TestCase {

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		Bundle::start('orchestra');
	}

	/**
	 * Test invalid extension is not started.
	 *
	 * @test
	 */
	public function testInvalidExtensionIsNotStarted()
	{
		$this->assertFalse(Orchestra\Extension::started('unknownfoo'));
	}

	/**
	 * Test detect extensions.
	 *
	 * @test
	 */
	public function testDetectExtension()
	{
		$this->markTestIncomplete('Not done');
	}
}
