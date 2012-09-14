jQuery(function($) {
	$('div.btn-group[data-toggle-name]').each(function() {
		var group, form, name, hidden, buttons;

		group   = $(this);
		form    = group.parents('form').eq(0);
		name    = group.attr('data-toggle-name');
		hidden  = $('input[name="' + name + '"]', form);
		buttons = $('button', group);

		buttons.each(function(){
			var button, setActive;

			button = $(this);

			setActive = function setActive() {
				if(button.val() == hidden.val()) {
					button.addClass('active');
				}
			};
			
			button.on('click', function() {
				buttons.removeClass('active');

				hidden.val($(this).val());

				setActive();
			});

			setActive();
		});
	});
});