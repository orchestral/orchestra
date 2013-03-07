@if (Orchestra\Site::get('navigation::show-user-box', true))
<ul class="nav pull-right">
	<li class="dropdown" id="user-menu">
		<p>
			<a href="#user-menu" rel="user-menu" class="btn dropdown-toggle" data-toggle="dropdown">
				<i class="icon-user"></i> {{ ( ! Auth::guest() ? Auth::user()->fullname : __('orchestra::title.login')) }}
			</a> 
		</p>

		@if (Auth::check())

		<ul class="dropdown-menu">
			
			<li>{{ HTML::link(handles('orchestra::account'), __('orchestra::title.account.profile')) }}</li>
			<li>{{ HTML::link(handles('orchestra::account/password'), __('orchestra::title.account.password')) }}</li>
			<li class="divider"></li>
			<li>{{ HTML::link(handles('orchestra::logout'), __('orchestra::title.logout')) }}</li>
		</ul>

		@endif

	</li>
</ul>
@endif