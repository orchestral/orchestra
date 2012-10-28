<?php

class TestResponse extends PHPUnit_Framework_TestCase 
{
	/**
	 * Setup the test
	 */	
	public function setUp()
	{
		Bundle::start('orchestra');
	}

	/**
	 * Test Orchestra\Response is an instance of Hybrid\Response
	 *
	 * @test
	 */
	public function testInstanceOf()
	{
		$response = new Orchestra\Response('');
		$this->assertInstanceOf('Hybrid\Response', $response);

		$response = Orchestra\Response::make('');
		$this->assertInstanceOf('Hybrid\Response', $response);
	}
}