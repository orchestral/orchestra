<?php

Bundle::start('orchestra');

class MacroTest extends Orchestra\Testable\TestCase {

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		parent::setUp();

		URI::$uri = 'orchestra';
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		URI::$uri = null;
	}
	
	/**
	 * Test HTML::title() macro.
	 *
	 * @test
	 */
	public function testHTMLTitleMacro()
	{
		$memory = Orchestra::memory();
		$memory->put('site.name', 'Orchestra Test Suite');

		$this->assertEquals('Orchestra Test Suite', HTML::title(null));
		$this->assertEquals('Home &mdash; Orchestra Test Suite', HTML::title('Home'));

		$memory->put('site.format.title', ':page-title at :site-title');
		$this->assertEquals('Home at Orchestra Test Suite', HTML::title('Home'));
	}

	/**
	 * Test blade compile @placeholder
	 */
	public function testBladeCompilePlaceholder()
	{
		$expected = '<?php foreach (Orchestra\Widget::make("placeholder."."foo")->get() as $_placeholder_): echo value($_placeholder_->value ?:""); endforeach; ?>';
		$output   = Blade::compile_string('@placeholder("foo")');

		$this->assertEquals($expected, $output);
	}
}