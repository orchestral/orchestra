<?php

return array(
	
	'form'  => array(

		/*
		|------------------------------------------------------------------
		| Default Error Message String
		|------------------------------------------------------------------
		|
		| Set default error message string format for Orchestra\Support\Form.
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
			'select'   => array('class' => 'span12'),
			'textarea' => array('class' => 'span12'),
			'input'    => array('class' => 'span12'),
			'password' => array('class' => 'span12'),
			'radio'    => array(),
			'checkbox' => array(),
		),

		'templates' => array(
			'select' => function($data)
			{
				return Form::select(
					$data->name, 
					$data->options, 
					$data->value, 
					$data->markup
				);
			},
			'checkbox' => function ($data)
			{
				return Form::checkbox(
					$data->name, 
					$data->value, 
					$data->checked
				);
			},
			'radio' => function ($data)
			{
				return Form::radio(
					$data->name, 
					$data->value, 
					$data->checked
				);
			},
			'textarea' => function ($data)
			{
				return Form::textarea(
					$data->name,
					$data->value,
					$data->markup
				);
			},
			'password' => function ($data)
			{
				return Form::password(
					$data->name, 
					$data->markup
				);
			},
			'input' => function ($data)
			{
				return Form::input(
					$data->type,
					$data->name,
					$data->value, 
					$data->markup
				);
			},
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