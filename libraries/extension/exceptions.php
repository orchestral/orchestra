<?php namespace Orchestra\Extension;

use \Exception as E;

class UnresolvedException extends E {

	private $deps = array();

	public function __construct($deps = array())
	{
		$this->deps = (array) $deps;
		parent::__construct("Unable to resolve dependencies");
	}

	public function getDependencies()
	{
		return $this->deps;
	}
}