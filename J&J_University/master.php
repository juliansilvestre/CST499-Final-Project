<?php
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.use_only_cookies','1');
    session_start();
}?>


<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"/>
	<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
	<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>

	<!-- Custom styles -->
	<style>
		.navbar-inverse {
			border-radius: 0;
			border: none;
			background-color: #9F35AC;}
		.navbar-inverse .navbar-nav > li > a,
		.navbar-inverse .navbar-brand {
			color: white !important;}
		.navbar-inverse .navbar-nav > li.active > a,
		.navbar-inverse .navbar-nav > li.active > a:hover,
		.navbar-inverse .navbar-nav > li.active > a:focus {
			background-color: #DDA0DD;
			color: black !important;
			font-weight: bold;}

		html, body { height: 100%; margin: 0; }
		body { display: flex; flex-direction: column; min-height: 100vh; }
		.page-content { flex: 1; }
		.footer { text-align: center; background-color: #9F35AC; color: white; padding: 10px 0; }

		.banner-container { display: flex; flex-wrap: wrap; width: 100%; margin: 0; padding: 0; }
		.banner-container img.banner-img,
		.banner-container .banner-text { flex: 1 1 auto; max-height: 200px; object-fit: cover; display: flex; align-items: center; justify-content: center; text-align: center; margin: 0; padding: 0px; border: none; }
		.banner-container .banner-text { background-color: #9F35AC; color: white; }

		.table { width: 100%; border-collapse: collapse; margin-top: 20px; }
		.table th, .table td { border: 1px solid #ddd; padding: 10px; }
		.table th { background-color: #9F35AC; color: white !important; text-align: left; width: 200px; } 
		.table td { text-align: left; }

		.sidebar { width: 220px; background-color: #9F35AC; color: white; padding: 20px; flex-shrink: 0; }
		.sidebar h2 { text-align: center; margin-bottom: 20px; }
		.sidebar a { display: block; color: white; padding: 12px 20px; text-decoration: none; transition: background-color 0.3s; }
		.sidebar a:hover, .sidebar a.active { background-color: #DDA0DD; color: black !important; font-weight: bold; }

		.doublecontent-container { display: flex; gap: 20px; }
		.doublecontent-content { flex: 1; }
		.container { padding: 20px; }

		@media (max-width: 1020px) {
			.banner-container img.img3 { display: none; }
		}
		@media (max-width: 768px) {
			.banner-container img.banner-img { display: none; }
			.banner-container .banner-text { flex: 1 1 100%; }
		}

	</style>
</head>

<body>
	
	<div class="banner-container" style="background-color: #9F35AC">
		<div class="banner-text">
			<h2>J&J<br>University</h2>
		</div>
		<img src="banner-img1.jpg" alt="Banner 1" class="banner-img img1">
		<img src="banner-img2.webp" alt="Banner 2" class="banner-img img2">
		<img src="banner-img3.webp" alt="Banner 3" class="banner-img img3">
	</div>

	<nav class="navbar navbar-inverse">
		<div class="container-fluid">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
			</div>
			<div class="collapse navbar-collapse" id="myNavbar">
				<ul class="nav navbar-nav">
					<li class="<?= ($currentPage == 'home') ? 'active' : '' ?>"><a href="index.php?page=home"><span class="glyphicon glyphicon-home"></span> Home</a></li>
					<li class="<?= ($currentPage == 'courses') ? 'active' : '' ?>"><a href="index.php?page=courses"><span class="glyphicon glyphicon-exclamation-sign"></span> Courses</a></li>
					<li class="<?= ($currentPage == 'courseofferings') ? 'active' : '' ?>"><a href="index.php?page=courseofferings"><span class="glyphicon glyphicon-calendar"></span> Class Schedules</a></li>
				</ul>
				<ul class="nav navbar-nav navbar-right">
					<?php
						if( isset($_SESSION['username'])){
							if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']) {
								echo '<li class="' . (($currentPage == 'offeringsscheduling') ? 'active' : '') . '"><a href="index.php?page=offeringsscheduling"><span class="glyphicon glyphicon-calendar"></span> Scheduling</a></li>';}
							echo '<li class="' . (($currentPage == 'cart') ? 'active' : '') . '"><a href="index.php?page=cart"><span class="glyphicon glyphicon-shopping-cart"></span> Course Cart</a></li>';
							echo '<li class="' . (in_array($currentPage, ['profile','schedule']) ? 'active' : '') . '"><a href="index.php?page=profile"><span class="glyphicon glyphicon-briefcase"></span> Profile</a></li>';
							echo '<li><a href="logout.php"><span class="glyphicon glyphicon-off"></span> Logout</a></li>';}
						else{
							echo '<li class="' . (($currentPage == 'login') ? 'active' : '') . '"><a href="index.php?page=login"><span class="glyphicon glyphicon-user"></span> Login</a></li>';
							echo '<li class="' . (($currentPage == 'registration') ? 'active' : '') . '"><a href="index.php?page=registration"><span class="glyphicon glyphicon-pencil"></span> Registration</a></li>';}
					?>
				</ul>
			</div>
		</div>
	</nav>
	<?php include $currentPage . '.php';?>
	<?php require_once 'footer.php';?>
</body>
</html>