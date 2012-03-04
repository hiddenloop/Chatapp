<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Application Routes
	|--------------------------------------------------------------------------
	|
	| Simply tell Laravel the HTTP verbs and URIs it should respond to. It's a
	| piece of cake to create beautiful applications using the elegant RESTful
	| routing available in Laravel.
	|
	| Let's respond to a simple GET request to http://example.com/hello:
	|
	|		'GET /hello' => function()
	|		{
	|			return 'Hello World!';
	|		}
	|
	| You can even respond to more than one URI:
	|
	|		'GET /hello, GET /world' => function()
	|		{
	|			return 'Hello World!';
	|		}
	|
	| It's easy to allow URI wildcards using (:num) or (:any):
	|
	|		'GET /hello/(:any)' => function($name)
	|		{
	|			return "Welcome, $name.";
	|		}
	|
	*/
	//9886023241
	//'GET /' => function()
	//{
	//	return View::make('home.index');
	//},
	'POST /upload/(:any)' => function($slug) {
		$chatsearch = Chat::where('chatslug', '=', $slug)->first();
		if (!$chatsearch) {
			return json_encode(array("error" => "invalid chat"));
		}
		$socialauth = new Socialauth();
		if ($socialauth->user_id) {
			$imgupload = Input::file('imgupload');
			$imguploadname = Input::file('imgupload.name');
			$imguploadext = File::extension($imguploadname);
			if(!file_exists($_SERVER{'DOCUMENT_ROOT'} ."/uploads/".$chatslug."/".$socialauth->user_id))  {
				mkdir($_SERVER{'DOCUMENT_ROOT'} ."/uploads/".$chatslug."/".$socialauth->user_id, 0777,true);
			}
			$filename=uniqid().".".$imguploadext;
			File::upload('imgupload', $_SERVER{'DOCUMENT_ROOT'} ."/uploads/".$chatslug."/".$socialauth->user_id."/".$filename);
			return json_encode(array(
				'url' => $_SERVER['HTTP_HOST']."/uploads/".$chatslug."/".$socialauth->user_id."/".$filename,
				'success' => true
			));
		} else {
			
		} 
	},
	'GET /authforchati/(:any)/(:any)/(:any)' => function($slug, $service, $user_id) {
		$chatsearch = Chat::where('chatslug', '=', $slug)->first();
		if (!$chatsearch) {
			return Redirect::to('/');
		}
		$socialauth = new Socialauth();
		$socialauth->authenticate($service);
		if ($socialauth->error) {
			return Response::make(View::make('error.500')->with('error', $socialauth->error), 500);
		}
		if ($socialauth->user_id) {
			$redischat =new Redischat($chatsearch->chatslug, $chatsearch->score);
			$redischat->newAuth($user_id);
			return 'You can now close this window.';
		} else {
			return Redirect::to('/')->with('error', 'authentication failed');
		}
	},
	'POST /authforchat/(:any)' => function($service) {
		$directurl = Input::get('directurl');
		setcookie('gotoUrl', $directurl);
		$socialauth = new SocialAuth;
		$socialauth->authenticate($service);
		if ($socialauth->error) {
			return Response::make(View::make('error.500')->with('error', $socialauth->error), 500);
		}
		if ($socialauth->user_id) {
			return Redirect::to($_COOKIE['gotoUrl']);
		} else {
			return Redirect::to('/')->with('error', 'authentication failed');
		}
	},
	'GET /authforchat/(:any)' => function($service) {
		if ($_COOKIE['gotoUrl'])
		{
     		return Redirect::to($_COOKIE['gotoUrl']);
		} else {
			return Response::make(View::make('error.500')->with('error', 'url not in session'), 500);
		}
	},
	'GET /chatnow/(:any)' => function($slug)
	{
		$chatsearch = Chat::where('chatslug', '=', $slug)->first();
		if (!$chatsearch) {
			return Redirect::to('/');
		}
		$socialauth = new Socialauth();
		$chatadmin = Chatadmin::where('chat_id', '=', $chatsearch->id)->where('user_id', '=', $socialauth->user_id)->first();
		if ($chatadmin) {
			$admin = true;
		} else {
			$admin = false;
		}
		$redischat =new Redischat($chatsearch->chatslug, $chatsearch->score);
		if (Session::get('anonid')) {
				$user_id = Session::get('anonid');
			} else {
				$user_id = uniqid();
				Session::put('anodid', $user_id);
			}
		//if (!$socialauth->user_id) {
		return View::make('chatnow.indexnonauth')->with('socialauth', $socialauth)->with('chat', $chatsearch)->with('admin', $admin)->with('redischat', $redischat);
		//}
		//return View::make('chatnow.index')->with('socialauth', $socialauth)->with('chat', $chatsearch)->with('admin', $admin)->with('redischat', $redischat);
	},
	'GET /getscore/(:any)' => function ($slug) {
		$chatsearch = Chat::where('chatslug', '=', $slug)->first();
		if ($chatsearch) {
			$redischat =new Redischat($chatsearch->chatslug, $chatsearch->score);
			$score = $redischat->getScore();
			if (!$score) {
				return json_encode(array('score' => 'None updated yet'));
			}
 			return $score;
		} else {
			return 'Error';
		}
	},
	'POST /sendscore/(:any)' => function($slug) {
		$score = Input::get('score');
		$chatsearch = Chat::where('chatslug', '=', $slug)->first();
		if (!$chatsearch) {
			return json_encode(array("success" => false));
		}
		$socialauth = new Socialauth();	
		if (!$socialauth->user_id) {
			return json_encode(array("success" => false));
		} 
		$chatadmins = Chatadmin::where('chat_id', '=', $chatsearch->id)->where('user_id', '=', $socialauth->user_id)->first();
		if ($chatadmins or $role == "admin") {
				$chatadmin = true;
				$redischat =new Redischat($chatsearch->chatslug, $chatsearch->score);
				$redischat->setScore($score);
				return json_encode(array("success" => true));
		} else {
				$chatadmin = false;
				return json_encode(array("error" => 'access denied'));
		}
	},
	'GET /chatinfo/(:any)' => function($slug) {
		$chatsearch = Chat::where('chatslug', '=', $slug)->first();
		if ($chatsearch) {
			$socialauth = new Socialauth();
			if (!$socialauth->user_id) {
				if (Session::get('anonid')) {
					$user_id = Session::get('anonid');
				} else {
					$user_id = uniqid();
					Session::put('anodid', $user_id);
				}
				$role = "anonymous";
				$name = "anonymous";
				$imgurl = $_SERVER['HTTP_HOST'].'/img/anon.png';
				$chatadmin = false;
			} else {
				$user_id = $socialauth->user_id;
				$role = $socialauth->user_role;
				$imgurl = $socialauth->facebook_photoURL == "NA" ? $socialauth->twitter_profile->photoURL : $socialauth->facebook_profile->photoURL;
				$name =  $socialauth->facebook_status ? $socialauth->facebook_profile->firstName.' '.$socialauth->facebook_profile->lastName : $socialauth->twitter_profile->firstName;
				$chatadmin = Chatadmin::where('chat_id', '=', $chatsearch->id)->where('user_id', '=', $socialauth->user_id)->first();
				if ($chatadmin or $role == "admin") {
					$chatadmin = true;
					if ($role == "admin") {
						$siteadmin = true;
					} else {
						$siteadmin = false;
					}
				} else {
					$chatadmin = false;
				}
			} 
		} else {
			$error = "chat not found";
		}
		return json_encode(array('user_id' => $user_id, 'role' => $role, 'imgurl' => $imgurl, 'name' => $name, 'chatadmin' => $chatadmin, 'error' => $error, 'siteadmin' => $siteadmin));
	},
	'GET /makeadmin/(:any)/(:any)' => function($slug, $adminid) {
		$chatsearch = Chat::where('chatslug', '=', $slug)->first();
		if (!$chatsearch) {
			return json_encode(array("success" => false));
		}
		$socialauth = new Socialauth();	
		if (!$socialauth->user_id) {
			return json_encode(array("success" => false));
		} 
		$chatadmins = Chatadmin::where('chat_id', '=', $chatsearch->id)->where('user_id', '=', $socialauth->user_id)->first();
		if ($chatadmins or $role == "admin") {
				$chatadmin = true;
		} else {
				$chatadmin = false;
		}
		if ($chatadmin) {
			$insert = new Chatadmin;
			$insert->chat_id = $chatsearch->id;
			$insert->user_id = $adminid;
			$insert->save();
			$redischat = new Redischat($chatsearch->chatslug, $chatsearch->score);
			$redischat->addAdmin($adminid);
			return json_encode(array("success" => true));
		} else {
			return json_encode(array("success" => false));
		}
	},
	'POST /chatauth/(:any)' => function($slug) {
		$chatsearch = Chat::where('chatslug', '=', $slug)->first();
		if (!$chatsearch) {
			return "error!";
		}
		$socialauth = new Socialauth();		
		if (!$socialauth->user_id) {
			if (Session::get('anonid')) {
				$user_id = Session::get('anonid');
			} else {
				$user_id = uniqid();
				Session::put('anodid', $user_id);
			}
			$role = "anonymous";
			$name = "anonymous";
			$imgurl = $_SERVER['HTTP_HOST'].'/img/anon.png';
			$chatadmin = false;
		} else {
			$chatadmin = Chatadmin::where('chat_id', '=', $chatsearch->id)->where('user_id', '=', $socialauth->user_id)->first();
			if ($chatadmin or $role == "admin") {
				$chatadmin = true;
			} else {
				$chatadmin = false;
			}
			$user_id = $socialauth->user_id;
			$role = $socialauth->user_role;
			$imgurl = $socialauth->facebook_photoURL == "NA" ? $socialauth->twitter_profile->photoURL : $socialauth->facebook_profile->photoURL;
			$name =  $socialauth->facebook_status ? $socialauth->facebook_profile->firstName.' '.$socialauth->facebook_profile->lastName : $socialauth->twitter_profile->firstName;
		}
		$pusher = new Pusher(PUSHERKEY, PUSHERSECRET, PUSHERAPPID);
		$presence_data = array('name' => $name, 'imgURL' => $imgurl, 'role' => $role, 'user_id' => $user_id, 'chatadmin' => $chatadmin);
		echo $pusher->presence_auth($_POST['channel_name'], $_POST['socket_id'], $user_id, $presence_data);
	},
	'POST /sendchat/(:any)' => function($chatslug) {
		$socialauth = new Socialauth();
		if (!$socialauth->user_id) {
			header('', true, 403);
  			echo( "Not authorized" );
		}
		$chatsearch = Chat::where('chatslug', '=', $chatslug)->first();
		if (!$chatsearch) {
			header('', true, 403);
  			echo( "Chat not found" );
		}
		$chatadmin = Chatadmin::where('chat_id', '=', $chatsearch->id)->where('user_id', '=', $socialauth->user_id)->first();
		if ($chatadmin) {
			$admin = true;
		} else {
			$admin = false;
		}
		$posttext = Input::get('chat_text');
		$postimgsrc = Input::get('img_source');
		$postimgcode = Input::get('img_code');
		$postvidsrc = Input::get('vid_source');
		$postvidcode = Input::get('vid_code');
		if (!$posttext and !$postimgcode and !$postvidcode) {
			return json_encode(array('msgerror' => 'Please enter something. You cannot send a blank chat'));
		}
		if (!$postimgsrc=="NA" and !$postimgcode) {
			return json_encode(array('msgerror' => 'Please enter valid image code.'));
		}
		if (!$postvidsrc=="NA" and !$postvidcode) {
			return json_encode(array('msgerror' => 'Please enter valid video code.'));
		}
		$redischat = new Redischat($chatsearch->chatslug, $chatsearch->score);
		//$imgurl = $socialauth->facebook_photoURL == "NA" ? $socialauth->twitter_profile->photoURL : $socialauth->facebook_profile->photoURL;
		$name =  $socialauth->facebook_status ? $socialauth->facebook_profile->firstName.' '.$socialauth->facebook_profile->lastName : $socialauth->twitter_profile->firstName;
		$msg = "<b>".$name . " :</b> ";
		$msg .= $posttext ? $posttext.'<br/>' : '';
		if ($postimgsrc=='twitpic') {
			$msg.="<a href='http://twitpic.com/$postimgcode' target='_blank'><img src='http://twitpic.com/show/thumb/$postimgcode' /></a><br/>";
		}
		if ($postimgsrc=='yfrog') {
			$msg.="<a href='http://yfrog.com/$postimgcode' target='_blank'><img src='http://yfrog.com/$postimgcode:small' /></a><br/>";
		}
		if ($postvidsrc=='youtube') {
			$msg.="<iframe width='320' height='240' src='http://chatapp.priyolahiri.co.cc/embed/$postvidcode' frameborder='0' allowfullscreen></iframe>";
		}
		if ($socialauth->user_role == "normal") {
			$redischat->addMsgMod($msg);
		} else {
			$redischat->addMsg($msg);
		}
		return json_encode(array('msgsuccess' => true));
	},
	'GET /embed/(:any)' => function($vidid) {
		$url = "http://www.youtube.com/embed/$vidid";
		$crl = curl_init();
        $timeout = 5;
        curl_setopt ($crl, CURLOPT_URL,$url);
        curl_setopt ($crl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($crl, CURLOPT_CONNECTTIMEOUT, $timeout);
        $ret = curl_exec($crl);
        curl_close($crl);
        return $ret;
		//return Redirect::to("http://www.youtube.com/embed/$vidid");
	},
	'GET /getchat/(:any)' => function($chatslug) {
		$socialauth = new Socialauth();
		$chatsearch = Chat::where('chatslug', '=', $chatslug)->first();
		if (!$chatsearch) {
			header('', true, 403);
  			echo( "Chat not found" );
		}
		$redischat = new Redischat($chatsearch->chatslug, $chatsearch->score);
		return json_encode($redischat->getChat());
	} ,
	'GET /getchatmod/(:any)' => function($chatslug) {
		$socialauth = new Socialauth();
		if (!$socialauth->user_id) {
			header('', true, 403);
  			echo( "Not authorized" );
		}
		$chatsearch = Chat::where('chatslug', '=', $chatslug)->first();
		if (!$chatsearch) {
			header('', true, 403);
  			echo( "Chat not found" );
		}
		$chatadmin = Chatadmin::where('chat_id', '=', $chatsearch->id)->where('user_id', '=', $socialauth->user_id)->first();
		if ($chatadmin) {
			$admin = true;
		} else {
			$admin = false;
		}
		$redischat = new Redischat($chatsearch->chatslug, $chatsearch->score);
		return json_encode($redischat->getModChat());
	} ,
	'GET /chatapprove/(:any)/(:any)' => function($chatslug, $id) {
		$socialauth = new Socialauth();
		if (!$socialauth->user_id) {
			header('', true, 403);
  			echo( "Not authorized" );
		}
		$chatsearch = Chat::where('chatslug', '=', $chatslug)->first();
		if (!$chatsearch) {
			header('', true, 403);
  			echo( "Chat not found" );
		}
		$chatadmin = Chatadmin::where('chat_id', '=', $chatsearch->id)->where('user_id', '=', $socialauth->user_id)->first();
		if ($chatadmin) {
			$admin = true;
			$redischat = new Redischat($chatsearch->chatslug, $chatsearch->score);
			return $redischat->approve($id);
		} else {
			header('', true, 403);
  			echo( "Not authorized" );
		}
		
	}
);