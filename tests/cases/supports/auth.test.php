<?php namespace Orchestra\Tests\Supports;

\Bundle::start('orchestra');

class AuthTest extends \Orchestra\Testable\TestCase {

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		\Event::override('orchestra.auth: roles', function ($user_id, $roles)
		{
			return array('admin', 'editor');
		});
	}

	/**
	 * Test Orchestra\Support\Auth::roles() returning valid roles
	 * 
	 * @test
	 * @group support
	 */
	public function testRolesMethod()
	{
		$expected = array('admin', 'editor');
		$output   = \Orchestra\Support\Auth::roles();

		$this->assertEquals($expected, $output);
	}

	/**
	 * Test Orchestra\Support\Auth::is() returning valid roles
	 * 
	 * @test
	 * @group support
	 */
	public function testIsMethod()
	{
		$this->assertTrue(\Orchestra\Support\Auth::is('admin'));
		$this->assertTrue(\Orchestra\Support\Auth::is('editor'));
		$this->assertFalse(\Orchestra\Support\Auth::is('user'));
	}
}