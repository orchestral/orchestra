{{-- Load from orchestra::users.index --}}

<div class="navbar hidden-phone">

	<div class="navbar-inner">

		{{ Form::open(handles('orchestra::users'), 'GET', array('class' => 'navbar-form')) }}
			
			<div class="pull-left">
				&nbsp;{{ Form::text('q', Input::get('q'), array('placeholder' => __('orchestra::label.search.keyword'), 'class' => 'input-xlarge'))}}
			</div>
			
			<div class="pull-left">
				&nbsp;{{ Form::select('roles[]', $roles, Input::get('roles'), array('multiple' => true, 'placeholder' => __('orchestra::label.users.roles'), 'class' => 'input-xlarge'))}}
			</div>
			
			<div class="pull-left">
				&nbsp;{{ Form::button(__('orchestra::label.search.button'), array('type' => 'submit', 'class' => 'btn btn-primary')) }}
			</div>

		{{ Form::close() }}
	
	</div>
	
</div>