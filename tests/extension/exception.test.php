<?php

class ExtensionExceptionsTest extends PHPUnit_Framework_TestCase {

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		Bundle::start('orchestra');
	}

	/**
	 * Test exception can be thrown.
	 *
	 * @expectedException Orchestra\Extension\UnresolvedException
	 */
	public function testUnsolvedExceptionCanBeThrown()
	{
		throw new Orchestra\Extension\UnresolvedException(array());
	}

	/**
	 * Test exception contain proper dependencies.
	 *
	 * @test
	 */
	public function testUnsolvedException()
	{
		$expected = array(
			'oneauth',
			'cello',
		);

		try
		{
			throw new Orchestra\Extension\UnresolvedException($expected);
		}
		catch (Orchestra\Extension\UnresolvedException $e)
		{
			$this->assertEquals("Unable to resolve dependencies", $e->getMessage());
			$this->assertEquals($expected, $e->getDependencies());
		}
	}
}
