<?php
include_once('../tools.php');

if (isset($_POST['doom'])) {
	deletepost($cx, $_POST['posted']);
	Header('Location: ' . $base_uri);
	exit();
}

if (!($_POST['posted']) && isset($_POST['body'])) {
	createpost($cx, $_POST['title'], $_POST['body'], $_POST['uri']);
	Header('Location: ' . $base_uri);
	exit();
}

if ($_POST['posted']) {
	updatepost($cx, $_POST['posted'], $_POST['title'], $_POST['body'], $_POST['uri']);
	Header('Location: ' . $base_uri);
	exit();
}


?><!DOCTYPE html 
	PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<title>Mod</title>
		<link rel="stylesheet" href="/ship" type='text/css' />
	</head>
	<body>

<blockquote style='font-family: Minion Pro; font-style: italic; margin-left: 0' xml:lang='ru'>
<span title='I was whole and I was broken'>Целым был и был разбитым,</span><br />
<span style='margin-left: 1.5em;' title='I was alive and I was dead'>Был живым и был убитым,</span><br />
<span title='I was clear water, I was poison'>Частой был водой, был ядом</span><br />
<span style='margin-left: 1.5em;' title='I was green grapes on the vine'>Был зеленым виноградом;</span><br />
<span title='Sleep lies in the early morning, in the evenings'>Спать ложился рано утром, вечерами</span><br />
<span style='margin-left: 1.5em;' title='Everything calls someone.'>Всë взонил кому-то.</span>
</blockquote>

<p style='margin-left: 1.5em;'><code>//syntax markdown</code> will parse your post as Markdown;<br />
<code>//alias foo bar</code> will replace any instance of foo with bar.</p>

<?php

if (isset($_GET['posted'])) {
	$p = readpost($cx, $_GET['posted']);
	echo editform($p['posted'], $p['uri'], $p['title'], $p['body']);
} else {
	echo editform();
}

?>

	</body>
</html>
