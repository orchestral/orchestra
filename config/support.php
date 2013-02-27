<?php

return array(
	
	'form'  => array(

		/*
		|------------------------------------------------------------------
		| Default Error Message String
		|------------------------------------------------------------------
		|
		| Set default error message string format for Hybrid\Form.
		|
		*/

		'error_message' => '<p class="help-block error">:message</p>',

		/*
		|------------------------------------------------------------------
		| Default Submit Button String
		|------------------------------------------------------------------
		|
		| Set default submit button string or language replacement key for 
		| Orchestra\Support\Form.
		|
		*/

		'submit_button'  => 'label.submit',
		
		/*
		|------------------------------------------------------------------
		| Default View Layout
		|------------------------------------------------------------------
		|
		| Orchestra\Support\Form would require a View to parse the provided 
		| form instance.
		|
		*/

		'view' => 'orchestra::support.form.horizontal',
		
		/*
		|------------------------------------------------------------------
		| Layout Configuration
		|------------------------------------------------------------------
		|
		| Set default submit button for Orchestra\Support\Form.
		|
		*/

		'fieldset' => array(
			'select'   => array('class' => 'span4'),
			'textarea' => array('class' => 'span4'),
			'input'    => array('class' => 'span4'),
			'password' => array('class' => 'span4'),
			'radio'    => array(),
			'checkbox' => array(),
		),
	),

	'table' => array(

		/*
		|------------------------------------------------------------------
		| Default Empty Message String
		|------------------------------------------------------------------
		|
		| Set default empty message string or language replacement key for 
		| Orchestra\Support\Table.
		|
		*/

		'empty_message'  => 'message.no-record',
		
		/*
		|------------------------------------------------------------------
		| Default View Layout
		|------------------------------------------------------------------
		|
		| Orchestra\Support\Table would require a View to parse the 
		| provided table instance.
		|
		*/

		'view' => 'orchestra::support.table.horizontal',
	),
);