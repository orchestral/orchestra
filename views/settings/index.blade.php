@layout(locate('orchestra::layout.main'))

@section('content')

<div class="row-fluid">

	<div class="span8">
		@include(locate('orchestra::layout.widgets.header'))
		{{ $form }}
	</div>

	<div class="span4">
		@placeholder('orchestra.settings')
		@placeholder('orchestra.helps')
	</div>

</div>

<script>
	jQuery(function onSettingPageReady ($) { 'use strict';
		var ev, emailDefault, emailPassword;

		ev            = Javie.Events.make();
		emailDefault  = $('select[name="email_default"]');
		emailPassword = $('#email_smtp_password').hide();

		$('#smtp_change_password_button').on('click', function (e) {
			e.preventDefault();
			
			$('input[name="stmp_change_password"]').val('yes');
			emailPassword.show();
			$('#smtp_change_password_container').hide();

			return false;
		});

		// Listen to email.default changed event. 
		ev.listen('setting.changed: email.default', function listenToEmailDefaultChange(e, self) {
			var value = self.value ? self.value : '';

			$('input[name^="email_smtp"], input[name^="email_sendmail"], input[name^="email_from"]')
				.parent().parent().hide();

			switch (value) {
				case 'smtp' :
					$('input[name^="email_smtp"]').parent().parent().show();
					break;
				case 'sendmail' :
					$('input[name^="email_sendmail"]').parent().parent().show();
					$('input[name^="email_from"]').parent().parent().show();
					break;
				default :
					$('input[name^="email_from"]').parent().parent().show();
					break;
			}
		});

		// bind onChange event to publish an event.
		emailDefault.on('change', function onChangeEmailDefault (e) {
			ev.fire('setting.changed: email.default', [e, this]);
		});

		// lets trigger an onChange event.
		emailDefault.trigger('change');
	});
</script>

@endsection
