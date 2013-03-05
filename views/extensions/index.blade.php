@layout(locate('orchestra::layout.main'))

<?php use Orchestra\Extension; ?>

@section('content')

<div class="row-fluid">

	@include(locate('orchestra::layout.widgets.header'))

	<table class="table table-bordered table-striped">
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
						<?php 
						$active  = Extension::active($name);
						$started = Extension::started($name); ?>
						@if ( ! ($started))
							{{ $extension->name }}
						@else
							{{ HTML::link(handles('orchestra::extensions/configure/'.$name), $extension->name) }}
						@endif
						@foreach($extension->unresolved as $dep)
							<span class="label label-important">{{ $dep['name'] . ' ' . $dep['version'] }}</span>
						@endforeach
					</strong>
					<div class="pull-right btn-group">
						@if ( ! ($started or $active))
							@if (empty($extension->unresolved))
								{{ HTML::link(handles('orchestra::extensions/activate/'.$name), __('orchestra::label.extensions.actions.activate'), array('class' => 'btn btn-primary btn-mini')) }}
							@else
								<button class="btn btn-mini" disabled="disabled" type="button">{{ __('orchestra::label.extensions.actions.activate') }}</button>
							@endif
						@else
							{{ HTML::link(handles('orchestra::extensions/deactivate/'.$name), __('orchestra::label.extensions.actions.deactivate'), array('class' => 'btn btn-warning btn-mini')) }}
						@endif

					</div>
				</td>
				<td>
					<p>{{ $extension->description }}</p>

					<span class="meta">
						{{ __('orchestra::label.extensions.version', array('version' => $extension->version )) }} |
						{{ __('orchestra::label.extensions.author', array('author' => HTML::link($extension->url ?: '#', $extension->author))) }}
						@if ( ! empty($extension->require))
							| {{ __('orchestra::label.extensions.dependencies') }}: {{ implode(', ', array_keys($extension->require)) }}
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