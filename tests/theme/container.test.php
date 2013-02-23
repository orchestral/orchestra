<?php namespace Orchestra\Tests\Theme;

\Bundle::start('orchestra');

class ContainerTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Stub instance.
	 *
	 * @var  Orchestra\Theme\Container
	 */
	private $stub = null;

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		\URL::$base = null;
		\Config::set('application.index', '');
		\Config::set('application.url', 'http://localhost/');

		set_path('public', \Bundle::path('orchestra').'tests'.DS.'fixtures'.DS.'public'.DS);

		$this->stub = new \Orchestra\Theme\Container('default');
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		unset($this->stub);

		\Config::set('application.index', 'index.php');
		\Config::set('application.url', '');

		set_path('public', path('base').'public'.DS);
	}

	/**
	 * Test constuct a new Orchestra\Theme\Container.
	 *
	 * @test
	 * @group core
	 */
	public function testConstructThemeContainer()
	{
		$this->assertInstanceOf('\Orchestra\Theme\Container', $this->stub);

		$theme = \Orchestra\Theme::container('frontend', 'default');
		$this->assertInstanceOf('\Orchestra\Theme\Container', $theme);
	}

	/**
	 * Test Orchestra\Theme\Container::to() return proper URL.
	 *
	 * @test
	 * @group core
	 */
	public function testToReturnProperUrl()
	{
		$this->assertEquals('http://localhost/themes/default/style.css',
			$this->stub->to('style.css'));
		$this->assertEquals('http://localhost/themes/default/js/script.min.js',
			$this->stub->to('js/script.min.js'));
	}

	/**
	 * Test Orchestra\Theme\Container::to_asset() return proper URL.
	 *
	 * @test
	 * @group core
	 */
	public function testToAssetReturnProperUrl()
	{
		$this->assertEquals('/themes/default/style.css',
			$this->stub->to_asset('style.css'));
		$this->assertEquals('/themes/default/js/script.min.js',
			$this->stub->to_asset('js/script.min.js'));
	}

	/**
	 * Test Orchestra\Theme\Container::parse() return proper file from
	 * theme.
	 *
	 * @test
	 * @group core
	 */
	public function testParseFileFromTheme()
	{
		$theme    = \Bundle::path('orchestra').'tests'.DS.'fixtures'.DS.'public'.DS.'themes'.DS;
		$expected = "path: {$theme}default/home/index.blade.php";

		$this->assertEquals($expected, $this->stub->parse('home.index'));
		$this->assertEquals($expected, $this->stub->path('home.index'));
	}

	/**
	 * Test Orchestra\Theme\Container::parse() return proper file from
	 * theme using alias.
	 *
	 * @test
	 * @group core
	 */
	public function testParseFileFromThemeUsingAlias()
	{
		$this->stub->map(array(
			'foo.index' => 'home.index',
		));

		$theme    = \Bundle::path('orchestra').'tests'.DS.'fixtures'.DS.'public'.DS.'themes'.DS;
		$expected = "path: {$theme}default/home/index.blade.php";

		$this->assertEquals($expected, $this->stub->parse('foo.index'));
		$this->assertEquals($expected, $this->stub->path('foo.index'));
	}

	/**
	 * Test Orchestra\Theme\Container::parse() return original view name
	 * when file is not available from theme.
	 *
	 * @test
	 * @group core
	 */
	public function testParseFileFromViewWhenThemeIsNull()
	{
		$expected = "foobar.index";

		$this->assertEquals($expected, $this->stub->parse('foobar.index'));
		$this->assertEquals($expected, $this->stub->path('foobar.index'));
	}
}
