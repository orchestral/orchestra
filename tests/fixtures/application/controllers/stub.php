<?php

class Stub_Controller extends Controller {

	public function action_index()
	{
		return 'stub';
	}

	public function action_redirect()
	{
		return Redirect::to('stub/index');
	}
}
