@layout('orchestra::layout.main')

@section('content')

<div class="row-fluid">
	
	<div class="page-header">
		<h2>{{ __('orchestra::title.extensions.list') }}</h2>
	</div>

	<table class="table table-bordered table-stripped">
		<thead>
			<tr>
				<th>Extension</th>
				<th>Description</th>
			</tr>
		</thead>
		<tbody>
			@forelse ($extensions as $name => $extension)
			<tr>
				<td>
					<strong>
						@if ( ! ($started = Orchestra\Extension::started($name)))
							{{ $extension->name }}
						@else
							{{ HTML::link(handles('orchestra::extensions/configure/'.$name), $extension->name) }}
						@endif
					</strong>
					<div class="pull-right btn-group">
						@if ( ! $started)
							{{ HTML::link(handles('orchestra::extensions/activate/'.$name), 'Activate', array('class' => 'btn btn-primary btn-mini')) }}
						@else
							{{ HTML::link(handles('orchestra::extensions/deactivate/'.$name), 'Deactivate', array('class' => 'btn btn-warning btn-mini')) }}
							{{ HTML::link(handles('orchestra::extensions/upgrade/'.$name), 'Upgrade', array('class' => 'btn btn-primary btn-mini')) }}
						@endif

					</div>
				</td>
				<td>
					<p>{{ $extension->description }}</p>

					<span class="meta">
						Version {{ $extension->version }} | By {{ HTML::link($extension->url ?: '#', $extension->author) }}
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