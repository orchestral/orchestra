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

		$this->assertEquals('<title>Orchestra Test Suite</title>', \HTML::title());

		\Orchestra\Site::set('title', 'Home');

		$this->assertEquals('<title>Home &mdash; Orchestra Test Suite</title>', \HTML::title());

		$memory->put('site.format.title', ':page-title at :site-title');
		$this->assertEquals('<title>Home at Orchestra Test Suite</title>', \HTML::title());
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

	/**
	 * Test blade compile @title
	 * 
	 * @test
	 * @group core
	 */
	public function testBladeCompileTitle()
	{
		$expected = '<?php echo Orchestra\Site::get("title"); ?>';
		$output   = \Blade::compile_string('@title');

		$this->assertEquals($expected, $output);
	}

	/**
	 * Test blade compile @description
	 * 
	 * @test
	 * @group core
	 */
	public function testBladeCompileDescription()
	{
		$expected = '<?php echo Orchestra\Site::get("description"); ?>';
		$output   = \Blade::compile_string('@description');

		$this->assertEquals($expected, $output);
	}
}