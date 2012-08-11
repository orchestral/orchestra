@layout('orchestra::layout.main')

@section('content')

<div class="row">
	<div class="span3">
		
		<div class="well" style="padding: 8px 0;">
			<ul class="nav nav-list">
				<li class="nav-header">Installation Process</li>
				<li class="active">
					{{ HTML::link(handles('orchestra::installer'), '1. Verify Database Configuration') }}
				</li>
			</ul>
		</div>
	
	</div>

	<div class="span8 form-horizontal">
		
		<h3>Database Setting</h3>

		<p>
			Please ensure following configuration is correct based on your <code>application/config/database.php</code>.
		</p>

		<fieldset>

			<div class="control-group">
				<label class="control-label">Database Type</label>
				<div class="controls">
					<span class="uneditable-input span4">{{ $database['driver'] }}</span>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label">Host</label>
				<div class="controls">
					<span class="uneditable-input span4">{{ $database['host'] }}</span>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label">Database</label>
				<div class="controls">
					<span class="uneditable-input span4">{{ $database['database'] }}</span>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label">User</label>
				<div class="controls">
					<span class="uneditable-input span4">{{ $database['username'] }}</span>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label">Password</label>
				<div class="controls">
					<span class="uneditable-input span4">{{ $database['password'] }}</span>
					<p class="help-block">Password is hidden</p>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label">Connection Status</label>
				<div class="controls">
					@if (true === $database['status'])
					<button class="btn btn-success disabled">Successful</btn>
					@else
					<button class="btn btn-danger disabled">Fail</btn>
					@endif
				</div>
			</div>

		</fieldset>

		<?php 

		$auth_fluent_status = true;
		$auth_eloquent_status = true;

		if ($auth['driver'] === 'fluent' and $auth['table'] !== 'users') $auth_fluent_status = false;
		if ($auth['driver'] === 'eloquent' and ltrim($auth['model'], '\\') !== 'Orchestra\Model\User') $auth_eloquent_status = false; ?>

		<fieldset>
			
			<h3>Authentication Setting</h3>

			<p>
				Please ensure following configuration is correct based on your <code>application/config/auth.php</code>.
			</p>

			<div class="control-group">
				<label class="control-label">Driver</label>
				<div class="controls">
					<span class="uneditable-input span4">{{ $auth['driver'] }}</span>
				</div>
			</div>

			<div class="control-group {{ false === $auth_fluent_status ? 'error' : '' }} ">
				<label class="control-label">Table</label>
				<div class="controls">
					<span class="uneditable-input span4">{{ $auth['table'] }}</span>
					@if (false === $auth_fluent_status)
					<p class="help-block">Table name should be `users`</p>
					@endif
				</div>
			</div>

			<div class="control-group {{ false === $auth_eloquent_status ? 'error' : '' }} ">
				<label class="control-label">Table</label>
				<div class="controls">
					<span class="uneditable-input span4">{{ $auth['model'] }}</span>
					@if (false === $auth_eloquent_status)
					<p class="help-block">Model name should be `Orchestra\Model\User`</p>
					@endif
				</div>
			</div>

		</fieldset>

		@if (true === $database['status'] and true === $auth_fluent_status && true === $auth_eloquent_status)

		<div class="form-actions clean">
			{{ HTML::link(handles('orchestra::installer/steps/1'), 'Next', array('class' => 'btn btn-primary')) }}
		</div>

		@endif


		
	</div>

</div>

@endsection