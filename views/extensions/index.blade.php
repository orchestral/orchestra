@layout('orchestra::layout.main')

@section('content')

<div class="row-fluid">

	<div class="page-header">
		<h2>{{ __('orchestra::title.extensions.list') }}</h2>
	</div>

	<table class="table table-bordered table-stripped">
		<thead>
			<tr>
				<th>{{ __('orchestra::label.extensions.name') }}</th>
				<th>{{ __('orchestra::label.description') }}</th>
			</tr>
		</thead>
		<tbody>
			@forelse ($extensions as $name => $extension)
			<tr>
				<td>
					<strong>
						@if ( ! ($started = Orchestra\Extension::started($name)))
							{{ $extension->name }}
							@forelse($extension->activable as $dep)
								<span class="label label-important">{{ $dep['name'] . ' ' . $dep['version'] }}</span>
							@empty
							@endforelse
						@else
							{{ HTML::link(handles('orchestra::extensions/configure/'.$name), $extension->name) }}
						@endif
					</strong>
					<div class="pull-right btn-group">
						@if ( ! $started )
							@if (empty($extension->activable))
								{{ HTML::link(handles('orchestra::extensions/activate/'.$name), __('orchestra::label.extensions.actions.activate'), array('class' => 'btn btn-primary btn-mini')) }}
							@else
								<button class="btn btn-mini" disabled="disabled" type="button">{{ __('orchestra::label.extensions.actions.activate') }}</button>
							@endif
						@else
							@unless($name === DEFAULT_BUNDLE or false === Orchestra\Extension::option($name, "web_upgrade"))
								{{ HTML::link(handles('orchestra::extensions/upgrade/'.$name), __('orchestra::label.extensions.actions.upgrade'), array('class' => 'btn btn-mini')) }}
							@endunless
							{{ HTML::link(handles('orchestra::extensions/deactivate/'.$name), __('orchestra::label.extensions.actions.deactivate'), array('class' => 'btn btn-warning btn-mini')) }}

						@endif

					</div>
				</td>
				<td>
					<p>{{ $extension->description }}</p>

					<span class="meta">
						{{ __('orchestra::label.extensions.version', array('version' => $extension->version )) }} |
						{{ __('orchestra::label.extensions.author', array('author' => HTML::link($extension->url ?: '#', $extension->author))) }}
						@if ( isset($extension->require))
							| {{ __('orchestra::label.extensions.dependencies') }}: {{ implode(', ', array_keys((array)$extension->require)) }}
						@endif
					</span>
				</td>
			</tr>
			@empty
			<tr>
				<td colspan="2">{{ __('orchestra::label.no-extension') }}</td>
			</tr>
			@endforelse
		</tbody>
	</table>

</div>

@endsection