<?php

Bundle::start('orchestra');

class RoutingForgotTest extends Orchestra\Testable\TestCase {

	/**
	 * Test Request GET (orchestra)/forgot
	 *
	 * @test
	 */
	public function testGetForgotPage()
	{
		$response = $this->call('orchestra::forgot@index');

		$this->assertInstanceOf('Laravel\Response', $response);
		$this->assertEquals(200, $response->foundation->getStatusCode());
		$this->assertEquals('orchestra::forgot.index', $response->content->view);
	}

	/**
	 * Test Request POST (orchestra)/forgot with invalid csrf
	 *
	 * @test
	 */
	public function testPostForgotPageFailedInvalidCsrf()
	{
		$response = $this->call('orchestra::forgot@index', array(), 'POST', array(
			'email' => 'example@test.com',
		));

		$this->assertInstanceOf('Laravel\Response', $response);
		$this->assertEquals(500, $response->foundation->getStatusCode());
	}
}