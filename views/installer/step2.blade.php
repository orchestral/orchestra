@layout('orchestra::layout.main')

@section('content')

<div class="row">
	<div class="span3">
		
		<div class="well" style="padding: 8px 0;">
			<ul class="nav nav-list">
				<li class="nav-header">Installation Process</li>
				<li>
					{{ HTML::link('#', '1. Verify Database Configuration') }}
				</li>
				<li>
					{{ HTML::link('#', '2. Create Administrator Account') }}
				</li>
				<li class="active">
					{{ HTML::link(handles('orchestra::installer/steps/2'), '2. Done') }}
				</li>
			</ul>
		</div>

	</div>

	<div class="span8 form-horizontal">
		
		<h2>Done</h2>

	</div>

</div>

@endsection