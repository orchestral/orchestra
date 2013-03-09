<!DOCTYPE html>
<html lang="en">
	<head>
		
		@include(locate('orchestra::layout.header'))

	</head>

	<body>

		@include(locate('orchestra::layout.navigation'))

		<section class="container{{ (Orchestra\Site::has('layout::fixed') ? '' : '-fluid') }}">

			@include(locate('orchestra::layout.widgets.messages'))

			@yield('content')

		</section>
		
		@include(locate('orchestra::layout.footer'))
	</body>
</html>
