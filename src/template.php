<?php
namespace modes;

?><!doctype html>
<html>
	<head>
		<title><?=$args['title']?></title>
		<link rel="stylesheet" type="text/css" href="<?=WWW_ROOT?>/css/screen.css?<?=VERSION?>" media="screen, projection">
		<link rel="icon" type="image/png" href="<?=WWW_ROOT?>/favicon.ico">
	</head>
	<body id="page-id-<?=$part?>">
		<div id="page-wrap">
			<header id="page-header">
				<h1 class="header-title"><a href="<?=get_url()?>"><?=NAME?></a> <span><?=$args['title']?></span></h1>
				<?php if (isset($_SESSION['user'])) : ?>
				<ol id="header-nav">
				<?php
					$items = array(
						'Upload' => 'home',
						'List' => 'list',
						'Tags' => 'tags',
						'User' => 'user'
						);
				foreach($items as $title => $nav_part) :
					$cls = $part === $nav_part ? ' active' : '';
				?>
				<li class="nav-<?=$nav_part . $cls?>"><a href="<?=get_url($nav_part)?>"><?=$title?></a></li>
				<?php endforeach; ?>
					<li class="header-logout"><a href="<?=WWW_ROOT . '/logout/' . short_id()?>">Log out</a> (<?=$_SESSION['user']?>)</li>
				</ol>
				<?php endif; ?>


			</header>
			<?php if (!empty($_SESSION['messages'])) : ?>
			<ol id="messages">
				<li><?=join($_SESSION['messages'], '</li><li>')?></li>
			</ol>
			<?php 
			unset($_SESSION['messages']);
			endif; 
			?>
			<div id="content">
				<?php require $template_part; ?>
			</div>
			<footer id="page-footer">
				<p><a href="<?=GITHUB_URL?>"><?=NAME?></a> <?=VERSION?></p>
			</footer>
		</div>
	</body>
</html>