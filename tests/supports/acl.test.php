<?php namespace Orchestra\Tests\Supports;

\Bundle::start('orchestra');

class AclTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		\Orchestra\Support\Acl::register('mock-one', function ($acl)
		{
			$acl->add_actions(array('view blog', 'view forum', 'view news'));
			$acl->allow('guest', array('view blog'));
			$acl->deny('guest', 'view forum');
		});
	}

	/**
	 * Test Orchestra\Support\Acl::make()
	 *
	 * @test
	 * @group support
	 */
	public function testMakeMethod()
	{
		$this->assertInstanceOf('\Orchestra\Support\Acl\Container', 
			\Orchestra\Support\Acl::make('mock-one'));
	}

	/**
	 * Test Orchestra\Support\Acl::register() method.
	 *
	 * @test
	 * @group support
	 */
	public function testRegisterMethod()
	{
		\Orchestra\Support\Acl::register(function ($acl)
		{
			$acl->add_actions(array('view blog', 'view forum', 'view news'));
			$acl->allow('guest', array('view blog'));
			$acl->deny('guest', 'view forum');
		});

		$acl = \Orchestra\Support\Acl::make(null);
		$this->assertInstanceOf('\Orchestra\Support\Acl\Container', $acl);

		$output = $acl->can('view blog');
		$this->assertTrue($output);
		
		$output = $acl->can('view forum');
		$this->assertFalse($output);

		$output = $acl->can('view news');
		$this->assertFalse($output);
	}

	/**
	 * Test Orchestra\Support\Acl::has_role() given 'mock-one'
	 *
	 * @test
	 * @group support
	 */
	public function testHasRoleMethodUsingMockOne()
	{
		$acl = \Orchestra\Support\Acl::make('mock-one');
		$this->assertTrue($acl->has_role('Guest'));
		$this->assertFalse($acl->has_role('Adminy'));
	}

	/**
	 * Test Orchestra\Support\Acl::can() given 'mock-one'
	 *
	 * @test
	 * @group support
	 */
	public function testCanMethodUsingMockOne()
	{
		$acl = \Orchestra\Support\Acl::make('mock-one');
		$this->assertInstanceOf('\Orchestra\Support\Acl\Container', $acl);

		$output = $acl->can('view blog');
		$this->assertTrue($output);
		
		$output = $acl->can('view forum');
		$this->assertFalse($output);

		$output = $acl->can('view news');
		$this->assertFalse($output);
	}

	/**
	 * Test Orchestra\Support\Acl::can() given 'mock-one'
	 *
	 * @test
	 * @group support
	 */
	public function testCanMethodSyncRoles()
	{
		$acl1 = \Orchestra\Support\Acl::make('mock-one');
		$acl2 = \Orchestra\Support\Acl::make('mock-two');

		\Orchestra\Support\Acl::add_role('admin');
		\Orchestra\Support\Acl::add_role('manager');

		$this->assertTrue($acl1->has_role('admin'));
		$this->assertTrue($acl2->has_role('admin'));
		$this->assertTrue($acl1->has_role('manager'));
		$this->assertTrue($acl2->has_role('manager'));

		\Orchestra\Support\Acl::remove_role('manager');

		$this->assertTrue($acl1->has_role('admin'));
		$this->assertTrue($acl2->has_role('admin'));
		$this->assertFalse($acl1->has_role('manager'));
		$this->assertFalse($acl2->has_role('manager'));

		$this->assertTrue(is_array(\Orchestra\Support\Acl::all()));
		$this->assertFalse(array() === \Orchestra\Support\Acl::all());

		\Orchestra\Support\Acl::shutdown();

		$this->assertEquals(array(), \Orchestra\Support\Acl::all());
	}
}