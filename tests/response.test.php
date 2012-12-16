<?php

Bundle::start('orchestra');

class ResponseTest extends PHPUnit_Framework_TestCase {

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