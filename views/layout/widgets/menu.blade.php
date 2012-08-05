<ul class="nav">
	@foreach ($menu->items as $item) 

		@if (1 > count($item->childs)) 
			<li>{{ HTML::link($item->link, $item->title) }}</li>
		@else
			<li class="dropdown" id="{{ $item->id }}-menu">
				<a href="#{{ $item->id }}-menu" rel="{{ $item->id }}-menu" class="dropdown-toggle" data-toggle="dropdown">
					{{ $item->title }}
				</a>
				<ul class="dropdown-menu">
					<li>{{ HTML::link($item->link, $item->title) }}</li>

					@foreach ($item->childs as $child) 
						<li>{{ HTML::link($child->link, $child->title) }}</li>
					@endforeach
				</ul>
			</li>

		@endif
		
	@endforeach
</ul>