<?php namespace Orchestra\Support;

class Expression {

	/**
	 * The value of the expression.
	 *
	 * @var string
	 */
	protected $value;

	/**
	 * Create a new expression instance.
	 *
	 * @param  string  $value
	 * @return void
	 */
	public function __construct($value)
	{
		$this->value = $value;
	}

	/**
	 * Get the string value of the expression.
	 *
	 * @return string
	 */
	public function get()
	{
		return $this->value;
	}

	/**
	 * Get the string value of the expression.
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->get();
	}
}
