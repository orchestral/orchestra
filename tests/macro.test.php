<?php namespace Orchestra\Tests;

\Bundle::start('orchestra');

class MacroTest extends \Orchestra\Testable\TestCase {

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		parent::setUp();

		\URI::$uri = 'orchestra';
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		\URI::$uri              = null;
		\Orchestra\Site::$items = array();
	}
	
	/**
	 * Test HTML::title() macro.
	 *
	 * @test
	 * @group core
	 */
	public function testHTMLTitleMacro()
	{
		$memory = \Orchestra::memory();
		$memory->put('site.name', 'Orchestra Test Suite');

		$this->assertEquals('Orchestra Test Suite', \HTML::title());

		\Orchestra\Site::set('title', 'Home');

		$this->assertEquals('Home &mdash; Orchestra Test Suite', \HTML::title());

		$memory->put('site.format.title', ':page-title at :site-title');
		$this->assertEquals('Home at Orchestra Test Suite', \HTML::title());
	}

	/**
	 * Test blade compile @placeholder
	 * 
	 * @test
	 * @group core
	 */
	public function testBladeCompilePlaceholder()
	{
		$expected = '<?php foreach (Orchestra\Widget::make("placeholder."."foo")->get() as $_placeholder_): echo value($_placeholder_->value ?:""); endforeach; ?>';
		$output   = \Blade::compile_string('@placeholder("foo")');

		$this->assertEquals($expected, $output);
	}
}