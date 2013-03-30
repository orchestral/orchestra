<?php namespace Orchestra\Tests\Facile;

class TemplateTest extends \PHPUnit_Framework_TestCase {
	
	/**
	 * Test constructing a new Orchestra\Facile\Template.
	 *
	 * @test
	 * @group facile
	 */
	public function testConstructMethod()
	{
		$stub = new \Orchestra\Facile\Template;

		$refl           = new \ReflectionObject($stub);
		$format         = $refl->getProperty('format');
		$default_format = $refl->getProperty('default_format');

		$format->setAccessible(true);
		$default_format->setAccessible(true);

		$this->assertEquals(array('html', 'json'), $format->getValue($stub));
		$this->assertEquals('html', $default_format->getValue($stub));
	}

	/**
	 * Test Orchestra\Facile\Template::compose_html() method.
	 *
	 * @test
	 * @group facile
	 */
	public function testComposeHtmlMethod()
	{
		$stub = with(new \Orchestra\Facile\Template)->compose_html(array(
			'view' => 'error.404',
			'foo' => 'foobar is awesome',
		));

		$this->assertInstanceOf('\View', $stub);
	}

	/**
	 * Test Orchestra\Facile\Template::compose_html() method throws exception
	 * when view is not defined
	 *
	 * @group facile
	 * @expectedException \InvalidArgumentException
	 */
	public function testComposeHtmlMethodThrowsException()
	{
		$stub = with(new \Orchestra\Facile\Template)->compose_html(array(
			'foo' => 'foobar is awesome',
		));
	}

	/**
	 * Test Orchestra\Facile\Template::compose_json() method.
	 *
	 * @test
	 * @group facile
	 */
	public function testComposeJsonMethod()
	{
		$stub = with(new \Orchestra\Facile\Template)->compose_json(array(
			'foo' => 'foobar is awesome',
		));

		$this->assertInstanceOf('\Response', $stub);
		$this->assertEquals('{"foo":"foobar is awesome"}', $stub->content);
		$this->assertEquals('application/json; charset=utf-8', 
			$stub->foundation->headers->get('content-type'));
	}
}