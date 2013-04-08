<?php namespace Orchestra\Tests\Theme;

\Bundle::start('orchestra');

class ContainerTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Base path.
	 *
	 * @var string
	 */
	private $base_path = null;

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
		$_SERVER['theme.start'] = null;

		$this->base_path = \Bundle::path('orchestra').'tests'.DS.'fixtures'.DS;
		set_path('public', $this->base_path.'public'.DS);

		$this->stub = new \Orchestra\Theme\Container('default');
	}

	/**
	 * Teardown the test environment.
	 */
	public function tearDown()
	{
		unset($this->stub);
		unset($this->base_path);
		unset($_SERVER['theme.start']);

		\Config::set('application.index', 'index.php');
		\Config::set('application.url', '');

		set_path('public', path('base').'public'.DS);
	}

	/**
	 * Test constuct a new Orchestra\Theme\Container.
	 *
	 * @test
	 * @group theme
	 */
	public function testConstructThemeContainer()
	{
		$this->assertInstanceOf('\Orchestra\Theme\Container', $this->stub);

		$theme  = new \Orchestra\Theme\Container('default');
		$refl   = new \ReflectionObject($theme);
		$name   = $refl->getProperty('name');
		$config = $refl->getProperty('config');

		$name->setAccessible(true);
		$config->setAccessible(true);

		$this->assertInstanceOf('\Orchestra\Theme\Container', $theme);
		$this->assertEquals('default', $name->getValue($theme));
		$this->assertInstanceOf('\Orchestra\Theme\Definition', $config->getValue($theme));
	}

	/**
	 * Test Orchestra\Theme\Container::start() method.
	 *
	 * @test
	 * @group theme
	 */
	public function testStartMethod()
	{
		$stub = new \Orchestra\Theme\Container('valid');
		$stub->start();

		$this->assertTrue($_SERVER['theme.start']);
	}

	/**
	 * Test Orchestra\Theme\Container::theme_path() method.
	 *
	 * @test
	 * @group theme
	 */
	public function testThemePathMethod()
	{
		$expected = path('public').'themes'.DS.'default'.DS;

		$this->assertEquals($expected, $this->stub->theme_path());
	}

	/**
	 * Test Orchestra\Theme\Container::to() method return proper URL.
	 *
	 * @test
	 * @group theme
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
	 * @group theme
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
	 * @group theme
	 */
	public function testParseFileFromTheme()
	{
		$theme    = $this->base_path.'public'.DS.'themes'.DS;
		$expected = "path: {$theme}default/home/index.blade.php";

		$this->assertEquals($expected, $this->stub->parse('home.index'));
		$this->assertEquals($expected, $this->stub->path('home.index'));

		$expected = "path: {$theme}default/bladevsphp/foo.blade.php";

		$this->assertEquals($expected, $this->stub->parse('bladevsphp.foo'));
		$this->assertEquals($expected, $this->stub->path('bladevsphp.foo'));

		$expected = "path: {$theme}default/bladevsphp/foobar.php";

		$this->assertEquals($expected, $this->stub->parse('bladevsphp.foobar'));
		$this->assertEquals($expected, $this->stub->path('bladevsphp.foobar'));

		$expected = "path: {$theme}default/bundles/orchestra/dashboard/index.blade.php";

		$this->assertEquals($expected, $this->stub->parse('orchestra::dashboard.index'));
		$this->assertEquals($expected, $this->stub->path('orchestra::dashboard.index'));
	}

	/**
	 * Test Orchestra\Theme\Container::parse() return proper file from
	 * theme using alias.
	 *
	 * @test
	 * @group theme
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
	 * @group theme
	 */
	public function testParseFileFromViewWhenThemeIsNull()
	{
		$expected = "foobar.index";

		$this->assertEquals($expected, $this->stub->parse('foobar.index'));
		$this->assertEquals($expected, $this->stub->path('foobar.index'));
	}

	/**
	 * Test Orchestra\Theme\Container::flush() method
	 *
	 * @test
	 * @group theme
	 */
	public function testFlushMethod()
	{
		$this->stub->parse('foobar.index');

		$refl  = new \ReflectionObject($this->stub);
		$cache = $refl->getProperty('cache');
		$cache->setAccessible(true);

		$result = $cache->getValue($this->stub);

		$this->assertEquals('foobar.index', $result['foobar.index']);

		$this->stub->flush();

		$this->assertEquals(array(), $cache->getValue($this->stub));
	}
}
