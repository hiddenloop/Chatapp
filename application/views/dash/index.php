<!DOCTYPE html>
<html>
	<!-- BEGIN HEAD -->
	<head>
		<title>Chatapp | Dashboard</title>
		<meta charset="UTF-8">
		<meta name="description" content="" />
		<!-- BEGIN SCRIPTS -->
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js"></script>
		<!--[if lt IE 9]><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
		<script type="text/javascript" src="/js/prettify.js"></script>                                   <!-- PRETTIFY -->
		<script type="text/javascript" src="/js/kickstart.js"></script>                                  <!-- KICKSTART -->
		<!-- END SCRIPTS -->
		<!-- BEGIN STYLES -->
		<link rel="stylesheet" type="text/css" href="/css/kickstart.css" media="all" />                  <!-- KICKSTART -->
		<link rel="stylesheet" type="text/css" href="/css/style.css" media="all" />                          <!-- CUSTOM STYLES -->
		<!-- END STYLES -->
	</head>
	<!-- END HEAD -->
	<!-- BEGIN BODY -->
	<body>
		<a id="top-of-page"></a>
		<!-- BEGIN PAGEWRAP -->
		<div id="wrap" class="clearfix">
			<!-- BEGIN HEADER AREA-->
			<div class="col_12" id="header_area">
				<div class="col_8">
					<h1>Chatapp</h1>
				</div>
				<div class="col_4">
					This is for development use only for internal testing. If you're seeing this and you don't know us, you're not suppossed to be here.
				</div>
			</div>
			<!-- END HEADER AREA -->
			<div class=clear></div>
			<!-- BEGIN MENU -->
			<ul class="menu">
				<li class="current"><a href="">Dash</a></li>
				<li><a href="">Connect Account</a></li>
				<li><a href="/chats"><span class="icon" data-icon="R"></span>Chats</a>
					<ul>
						<li><a href=""><span class="icon" data-icon="G"></span>Create</a></li>
						<li class="divider"><a href=""><span class="icon" data-icon="T"></span>li.divider</a></li>
						<li><a href=""><span class="icon" data-icon="G"></span>Active Chats</a></li>
						<li><a href=""><span class="icon" data-icon="A"></span>Finished Chats</a></li>
					</ul>
				</li>
				<li><a href="/logout">Logout</a></li>
			</ul>	
	 		<!-- END MENU -->
	 		<div class="clear"></div>
	 		<!-- BEGIN BODY AREA -->
			<div class="col_12" id="body_area">
				<!-- BEGIN CONTENT AREA -->
				<div class="col_12" id="content_area">
					<h3>Dashboard</h3>
					<?php
						$imgurl = ($socialauth->facebook_profile->photoURL == "NA") ? $socialauth->twitter_profile->photoURL : $socialauth->facebook_profile->photoURL;
						$name = !$socialauth->twitter_profile->lastName ? $socialauth->facebook_profile->firstName.' '.$socialauth->facebook_profile->lastName : $socialauth->twiiter_profile->firstName
					?>
					<img src="<?php echo($imgurl) ?>">Hi, <?php echo($name) ?>. Welcome to your dashboard. Use the menu on the top to access your options. 
				</div>
				<!-- END CONTENT AREA -->
			</div>
			<!-- END BODY AREA -->
			<div class="clear"></div>
			<!-- BEGIN FOOTER -->
			<div id="footer">
				&copy; Copyright Priyadarshi Lahiri 2011–2012 All Rights Reserved.
				<a id="link-top" href="#top-of-page">Top</a>
			</div>
			<!-- END FOOTER -->
		</div>
		<!-- END PAGEWRAP -->
	</body>
	<!-- END BODY -->
</html>