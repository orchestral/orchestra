<?php namespace Orchestra\Tests\Supports;

\Bundle::start('orchestra');

class HTMLTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Test Orchestra\Support\HTML::create() with content
	 * 
	 * @test
	 * @group support
	 */
	public function testCreateWithContent()
	{
		$expected = '<div class="foo">Bar</div>';
		$output   = \Orchestra\Support\HTML::create('div', 'Bar', array('class' => 'foo'));

		$this->assertEquals($expected, $output);
	}

	/**
	 * Test Orchestra\Support\HTML::create() without content
	 * 
	 * @test
	 * @group support
	 */
	public function testCreateWithoutContent()
	{
		$expected = '<img src="hello.jpg" class="foo">';
		$output   = \Orchestra\Support\HTML::create('img', array(
			'src' => 'hello.jpg', 
			'class' => 'foo',
		));

		$this->assertEquals($expected, $output);

		$expected = '<img src="hello.jpg" class="foo">';
		$output   = \Orchestra\Support\HTML::create('img', null, array(
			'src' => 'hello.jpg', 
			'class' => 'foo',
		));

		$this->assertEquals($expected, $output);
	}

	/**
	 * Test Orchestra\Support\HTML::entities() method
	 *
	 * @test
	 * @group support
	 */
	public function testEntitiesMethod()
	{
		$output = \Orchestra\Support\HTML::raw('<img src="foo.jpg">');
		$this->assertEquals('<img src="foo.jpg">', 
			\Orchestra\Support\HTML::entities($output));

		$output = '<img src="foo.jpg">';
		$this->assertEquals('&lt;img src=&quot;foo.jpg&quot;&gt;', 
			\Orchestra\Support\HTML::entities($output));
	}

	/**
	 * Test Orchestra\Support\HTML::raw() method.
	 *
	 * @test
	 * @group support
	 */
	public function testRawExpressionMethod()
	{
		$output = \Orchestra\Support\HTML::raw('hello');
		$this->assertInstanceOf('\Orchestra\Support\Expression', $output);
	}

	/**
	 * Test Orchestra\Support\HTML::decorate() method.
	 *
	 * @test
	 * @group support
	 */
	public function testDecorateMethod()
	{
		$output = \Orchestra\Support\HTML::decorate(
			array('class' => 'span4 table'), 
			array('id' => 'foobar')
		);
		$expected = array('id' => 'foobar', 'class' => 'span4 table');
		$this->assertEquals($expected, $output);

		$output = \Orchestra\Support\HTML::decorate(
			array('class' => 'span4 !span12'), 
			array('class' => 'span12')
		);
		$expected = array('class' => 'span4');
		$this->assertEquals($expected, $output);

		$output = \Orchestra\Support\HTML::decorate(
			array('id' => 'table'), 
			array('id' => 'foobar', 'class' => 'span4')
		);
		$expected = array('id' => 'table', 'class' => 'span4');
		$this->assertEquals($expected, $output);
	}
}
