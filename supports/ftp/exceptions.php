<?php namespace Orchestra\Support\FTP;

class RuntimeException extends \RuntimeException {

	protected $parameters = array();

	public function __construct($exception, array $parameters = array())
	{
		$this->parameters = $parameters;
		parent::__construct($exception);
	}
}

class ServerException extends \RuntimeException {}