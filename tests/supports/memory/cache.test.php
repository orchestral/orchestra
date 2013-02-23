<?php namespace Orchestra\Tests\Support\Memory;

\Bundle::start('orchestra');

class CacheTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Mark test not completed
	 */
	public function testNotCompleted()
	{
		$this->markTestIncomplete("Not completed");	
	}
}