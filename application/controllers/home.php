<?php

class Home_Controller extends Controller {
	public function action_index()
	{
		$socialauth= new Socialauth();
		$error = Session::get('error');
		return View::make('home.index')->with('socialauth', $socialauth)->with('error', $error);
	}

}