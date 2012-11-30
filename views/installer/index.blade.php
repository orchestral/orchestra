@layout('orchestra::layout.main')

<?php use Orchestra\HTML; ?>

@section('content')

<div class="row">
	<div class="well span3" style="padding: 8px 0;">
		<ul class="nav nav-list">
			<li class="nav-header">Installation Process</li>
			<li class="active">
				{{ HTML::link(handles('orchestra::installer'), '1. Check Requirements') }}
			</li>
		</ul>

	</div>

	<div id="installation" class="span6 form-horizontal">

		<h3>{{ __('orchestra::installation.system.title') }}</h3>

		<p>{{ __('orchestra::installation.system.description') }}</p>

		<table class="table table-bordered table-striped requirements">
			<thead>
				<tr>
					<th>{{ __('orchestra::installation.system.requirement') }}</th>
					<th>{{ __('orchestra::installation.system.status') }}</th>
				</tr>
			</thead>
			<tbody>
				@foreach ($requirements as $name => $requirement)
				<tr>
					<td>
						{{ __("orchestra::installation.system.{$name}.name", $requirement['data']) }}
						@unless ($requirement['is'] === $requirement['should'])
						<div class="alert{{ true === $requirement['explicit'] ? ' alert-error ' : '' }}">
							<strong>{{ __("orchestra::installation.solution") }}:</strong>
							{{ __("orchestra::installation.system.{$name}.solution", $requirement['data']) }}
						</div>
						@endunless
					</td>
					<td>
						@if ($requirement['is'] === $requirement['should'])
							<button class="btn btn-success btn-block disabled">
								{{ __('orchestra::installation.status.work') }}
							</button>
						@else
							@if (true === $requirement['explicit'])
								<button class="btn btn-danger btn-block disabled">
									{{ __('orchestra::installation.status.not') }}
								</button>
							@else
								<button class="btn btn-warning btn-block disabled">
									{{ __('orchestra::installation.status.still') }}
								</button>
							@endif
						@endif
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>

		<h3>{{ __('orchestra::installation.database.title') }}</h3>

		<p>
			{{ __('orchestra::installation.verify', array('filename' => HTML::create('code', 'application/config/database.php', array('title' => path('app').'config/database.php')))) }}
		</p>

		<fieldset>

			<div class="control-group">
				<label class="control-label">{{ __('orchestra::installation.database.type') }}</label>
				<div class="controls">
					<span class="uneditable-input input-xlarge">{{ $database['driver'] }}</span>
				</div>
			</div>

			@if (isset($database['host']))
			<div class="control-group">
				<label class="control-label">{{ __('orchestra::installation.database.host') }}</label>
				<div class="controls">
					<span class="uneditable-input input-xlarge">{{ $database['host'] }}</span>
				</div>
			</div>
			@endif

			<div class="control-group">
				<label class="control-label">{{ __('orchestra::installation.database.name') }}</label>
				<div class="controls">
					<span class="uneditable-input input-xlarge">{{ $database['database'] }}</span>
				</div>
			</div>

			@if (isset($database['username']))
			<div class="control-group">
				<label class="control-label">{{ __('orchestra::installation.database.username') }}</label>
				<div class="controls">
					<span class="uneditable-input input-xlarge">{{ $database['username'] }}</span>
				</div>
			</div>
			@endif

			@if (isset($database['password']))
			<div class="control-group">
				<label class="control-label">{{ __('orchestra::installation.database.password') }}</label>
				<div class="controls">
					<span class="uneditable-input input-xlarge">{{ $database['password'] }}</span>
					<p class="help-block">{{ __('orchestra::installation.hide-password') }}</p>
				</div>
			</div>
			@endif

			<div class="control-group">
				<label class="control-label">{{ __('orchestra::installation.connection.status') }}</label>
				<div class="controls">
					@if (true === $database['status'])
					<button class="btn btn-success disabled input-xlarge">
						{{ __('orchestra::installation.connection.success') }}
					</button>
					@else
					<button class="btn btn-danger disabled input-xlarge">
						{{ __('orchestra::installation.connection.fail') }}
					</button>
					@endif
				</div>
			</div>

		</fieldset>

		<fieldset>

			<h3>{{ __('orchestra::installation.auth.title') }}</h3>

			<p>
				{{ __('orchestra::installation.verify', array('filename' => HTML::create('code', 'application/config/auth.php', array('title' => path('app').'config/auth.php')))) }}
			</p>

			<div class="control-group">
				<label class="control-label {{ 'fluent' === $auth['driver'] ? 'error' : '' }}">
					{{ __('orchestra::installation.auth.driver') }}
				</label>
				<div class="controls">
					<span class="uneditable-input input-xlarge">{{ $auth['driver'] }}</span>
					@if ('fluent' === $auth['driver'])
					<p class="help-block">{{ __('orchestra::installation.auth.requirement.driver') }}</p>
					@endif
				</div>
			</div>

			<div class="control-group {{ false === $auth_status ? 'error' : '' }} {{ 'eloquent' !== $auth['driver'] ? 'hide' : '' }}">
				<label class="control-label">
					{{ __('orchestra::installation.auth.model') }}
				</label>
				<div class="controls">
					<span class="uneditable-input input-xlarge">{{ $auth['model'] }}</span>
					@if (false === $auth_status)
					<p class="help-block">
						{{ __('orchestra::installation.auth.requirement.driver', array('class' => HTML::create('code', 'Orchestra\Model\User'))) }}
					</p>
					@endif
				</div>
			</div>

		</fieldset>

		@if ($installable)

		<div class="form-actions clean">
			{{ HTML::link(handles('orchestra::installer/steps/1'), 'Next', array('class' => 'btn btn-primary')) }}
		</div>

		@endif

	</div>

</div>

@endsection
