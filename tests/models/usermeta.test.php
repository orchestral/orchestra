<?php

Bundle::start('orchestra');

class ModelUserMetaTest extends Orchestra\Testable\TestCase {

	/**
	 * Test Orchestra\Model\User\Meta configuration.
	 *
	 * @test
	 */
	public function testConfiguration()
	{
		$this->assertEquals('user_meta', Orchestra\Model\User\Meta::$table);
	}
}