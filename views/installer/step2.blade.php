@layout('orchestra::layout.main')

@section('content')

<div class="row">

	<div class="well span3" style="padding: 8px 0;">
		<ul class="nav nav-list">
			<li class="nav-header">Installation Process</li>
			<li>
				{{ HTML::link('#', '1. Check Requirements') }}
			</li>
			<li>
				{{ HTML::link('#', '2. Create Administrator Account') }}
			</li>
			<li class="active">
				{{ HTML::link(handles('orchestra::installer/steps/2'), '2. Done') }}
			</li>
		</ul>
	</div>

	<div class="span6 form-horizontal">

		<h2>Done</h2>

	</div>

</div>

@endsection
