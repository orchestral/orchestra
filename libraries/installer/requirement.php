<?php namespace Orchestra\Installer;

use RuntimeException,
	Orchestra\HTML;

class Requirement {

	/**
	 * Installation checklist for Orchestra Platform.
	 *
	 * @var array
	 */
	protected $checklist = array();

	/**
	 * Installable status
	 *
	 * @var boolean
	 */
	protected $installable = true;
	
	/**
	 * Construct a new instance.
	 *
	 * @access public
	 * @param  Orchestra\Installer\Publisher    $publisher
	 * @return void
	 */
	public function __construct(Publisher $publisher)
	{
		try
		{
			$asset_writable = $publisher->publish();
		}
		catch (RuntimeException $e)
		{
			$asset_writable = false;
		}

		$this->checklist = array(
			'storage_writable' => array(
				'is'       => (is_writable(path('storage'))),
				'should'   => true,
				'explicit' => false,
				'data'     => array(
					'path' => HTML::create('code', 'storage', array('title' => path('storage'))),
				),
			),
			'bundle_writable' => array(
				'is'       => (is_writable(path('bundle'))),
				'should'   => true,
				'explicit' => false,
				'data'     => array(
					'path' => HTML::create('code', 'bundles', array('title' => path('bundle'))),
				),
			),
			'asset_writable'  => array(
				'is'       => ($asset_writable),
				'should'   => true,
				'explicit' => true,
				'data'     => array(
					'path' => HTML::create('code', 'public'.DS.'bundles', array('title' => path('public').'bundles'.DS)),
				),
			),
		);

		foreach ($this->checklist as $requirement)
		{
			if ($requirement['is'] !== $requirement['should'] 
				and true === $requirement['explicit'])
			{
				$this->installable = false;
			}
		}
	}

	/**
	 * Get checklist result.
	 *
	 * @access public
	 * @return array
	 */
	public function checklist()
	{
		return $this->checklist;
	}

	/**
	 * Get installable status.
	 * 
	 * @access public
	 * @return bool
	 */
	public function installable()
	{
		return $this->installable;
	}
}