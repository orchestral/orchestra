<div class="navbar navbar-fixed-top">
	<div class="navbar-inner">
		<div class="container{{ isset($fluent_layout) ? '-fluid' : '' }}">
			<section class="nav-collapse">
				
				{{ HTML::link(handles('orchestra'), $orchestra_memory->get('site.name', 'Orchestra'), array('class' => 'brand')) }}

				{{ render('orchestra::layout.widgets.menu', array('menu' => Orchestra\Core::menu())) }}
				
				@if (Config::get('orchestra::navigation.show-user-box', true))
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
			</section>

		</div>
	</div>
</div>
@if ( ! Auth::check())

<script>
jQuery(function ($) {
	$('a[rel="user-menu"]').on('click', function (e) {
		e.preventDefault();
		
		window.location.href = "{{ URL::to(handles('orchestra::login')) }}";

		return false;
	});
});
</script>

@endif

<br>