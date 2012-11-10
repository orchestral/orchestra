<?php

use Orchestra\View;

class Orchestra_Dashboard_Controller extends Orchestra\Controller
{
	/**
	 * Construct Dashboard Controller, only authenticated user should 
	 * be able to access this controller.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();

		$this->filter('before', 'orchestra::auth');
	}

	/**
	 * Dashboard Page
	 *
	 * @access public
	 * @return Response
	 */
	public function get_index()
	{
		$panes = Orchestra\Widget::make('pane.orchestra')->get();

		if (empty($panes))
		{
			$pane = new Laravel\Fluent(array(
				'id'      => 'orchestra.welcome',
				'attr'    => array(),
				'title'   => '',
				'content' => '',
			));

			$pane->attr = array('class' => 'hero-unit');
			$pane->html = '<h2>Welcome to your new Orchestra site!</h2>
				<p>If you need help getting started, check out our documentation on First Steps with Orchestra. If you’d rather dive right in, here are a few things most people do first when they set up a new Orchestra site. 
				<!-- If you need help, use the Help tabs in the upper right corner to get information on how to use your current screen and where to go for more assistance.--></p>';

			$panes['orchestra.welcome'] = $pane;
		}

		return View::make('orchestra::resources.dashboard', compact('panes'));
	}
}