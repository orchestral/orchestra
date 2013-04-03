<?php namespace Orchestra\Tests\Facile\Template;

\Bundle::start('orchestra');

class BaseTest extends \PHPUnit_Framework_TestCase {
	
	/**
	 * Test constructing a new Orchestra\Facile\Template\Base.
	 *
	 * @test
	 * @group facile
	 */
	public function testConstructMethod()
	{
		$stub = new \Orchestra\Facile\Template\Base;

		$refl           = new \ReflectionObject($stub);
		$formats        = $refl->getProperty('formats');
		$default_format = $refl->getProperty('default_format');

		$formats->setAccessible(true);
		$default_format->setAccessible(true);

		$this->assertEquals(array('html', 'json'), $formats->getValue($stub));
		$this->assertEquals('html', $default_format->getValue($stub));
	}

	/**
	 * Test Orchestra\Facile\Template\Base::compose_html() method.
	 *
	 * @test
	 * @group facile
	 */
	public function testComposeHtmlMethod()
	{
		$data = array('foo' => 'foo is awesome');
		$stub = with(new \Orchestra\Facile\Template\Base)->compose_html('error.404', $data);

		$this->assertInstanceOf('\Response', $stub);
		$this->assertEquals('error.404', $stub->content->view);
	}

	/**
	 * Test Orchestra\Facile\Template\Base::compose_html() method throws exception
	 * when view is not defined
	 *
	 * @group facile
	 * @expectedException \InvalidArgumentException
	 */
	public function testComposeHtmlMethodThrowsException()
	{
		$data = array('foo' => 'foobar is awesome');
		$stub = with(new \Orchestra\Facile\Template\Base)->compose_html(null, $data);
	}

	/**
	 * Test Orchestra\Facile\Template\Base::compose_json() method.
	 *
	 * @test
	 * @group facile
	 */
	public function testComposeJsonMethod()
	{
		$data = array('foo' => 'foobar is awesome');
		$stub = with(new \Orchestra\Facile\Template\Base)->compose_json(null, $data);

		$this->assertInstanceOf('\Response', $stub);
		$this->assertEquals('{"foo":"foobar is awesome"}', $stub->content);
		$this->assertEquals('application/json; charset=utf-8', 
			$stub->foundation->headers->get('content-type'));
	}
}