<?php namespace Orchestra\Testable;

class FTP {
	
	/**
	 * Assume that we going to accept everything, for now.
	 */
	public function __call($method, $parameters) 
	{
		return $this;
	}
}