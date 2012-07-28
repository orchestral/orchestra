@layout('orchestra::layout.main')

@section('content')

<div class="row-fluid">
	@foreach (Orchestra\Extension\Pane::get() as $id => $pane) 
		<div{{ HTML::attributes($pane->attr) }}>
		@if ( ! empty($pane->html))
			{{ $pane->html }}
		@else
			<table{{ HTML::attributes(array('class' => "table table-bordered")) }}>
				<thead>
					<tr>
						<th>{{ $pane->title }}</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>{{ $pane->content }}</td>
					</tr>
				</tbody>
			</table>
		@endif
		</div>
	@endforeach
</div>

@endsection