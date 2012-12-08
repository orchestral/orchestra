<?php

Bundle::start('orchestra');

class RoutingResourcesTest extends Orchestra\Testable\TestCase {
	
	/**
	 * User instance.
	 *
	 * @var Orchestra\Model\User
	 */
	private $user = null;

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		parent::setUp();

		$this->user = Orchestra\Model\User::find(1);
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		unset($this->user);

		parent::tearDown();
	}
	
	/**
	 * Test Request GET (orchestra)/resources
	 *
	 * @test
	 */
	public function testGetResourcesIndexPage()
	{
		$this->markTestIncomplete("Not completed.");
	}
}