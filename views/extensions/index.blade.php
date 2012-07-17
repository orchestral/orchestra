@layout('orchestra::layout.main')

@section('content')

<div class="row-fluid">
	
	<div class="page-header">
		<h2>{{ __('orchestra::title.extensions') }}</h2>
	</div>

	<table class="table table-bordered table-stripped">
		<thead>
			<tr>
				<th>Extension</th>
				<th>Description</th>
			</tr>
		</thead>
		<tbody>
			@forelse ($extensions as $extension)
			<tr>
				<td>
					<strong>{{ Str::title($extension->name) }}</strong>
					<br>
					{{ HTML::link('orchestra/extensions/activate/'.$extension->name, 'Activate') }}
				</td>
				<td>{{ $extension->description }}</td>
			</tr>
			@empty
			<tr>
				<td colspan="2">No data</td>
			</tr>
			@endforelse
		</tbody>
	</table>

</div>

@endsection