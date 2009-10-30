<?php 
$t0 = microtime(1);

include_once('tools.php');

if ($polyglot) {
	$lang = get_lang($_GET['search'],$_SERVER['HTTP_ACCEPT_LANGUAGE']);
}
?>
<!DOCTYPE html 
	PUBLIC '-//W3C//DTD XHTML 1.0 Strict//EN'
	'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'>";
<?php
if ($lang) {
    echo "	<html xmlns='http://www.w3.org/1999/xhtml' xml:lang='$lang' lang='$lang'>\n";
} else {
    echo "	<html xmlns='http://www.w3.org/1999/xhtml' xml:lang='en' lang='en'>\n";
}
?>
	<head>
		<?php echo "<title>$blog_title</title>\n"; ?>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
		<link rel="stylesheet" href="/ship" type='text/css' />
<?php
echo "		<link rel='alternate' type='application/atom+xml' title='Atom' href='$base_uri/atom' />\n";
?>
		<script type="text/javascript" src="/jquery-1.3.2.min.js"></script>
		<script type="text/javascript">
update = function (e) {
	if ($(window).scrollTop() + $(window).height()
		> $("div.post:last").position().top) {

		k = $("div.post:last").next();
		if (k.attr('class') == 'stub') {
			k.load("just?id=" + $("div.stub:first").attr('id'));
			k.attr({'class': 'post'});
			k.fadeIn(1000);
			return true; } }
	return false; }

window.onscroll = update;

window.onload = function () {
	// ugly patch for when your screen is so tall or the first n posts
	// are so short that you can't start scrolling
	a = function() { if (update()) setTimeout(a, 400); }
	a(); // because looping onload seems tacky
};

		</script>
	</head>
	<body>
<?php

echo $titlediv;
echo "<div id='main'>";

if ($_GET['search'] != 'index.php') {
	$f = readbyuri($cx, $_GET['search']);
	if ($f['uri']) {
		echo wrappost($f, $class='post');
	}
	else {
//		echo "could not find post with title " . $_GET['search'];
		echo wrappost(readpost($cx, $_GET['search']), $class='post'); }
} else {

	$toc = array(); // php sux
	foreach (readtoc($cx) as $c) {
		array_push($toc, $c['posted']); }
	
	$chunksize = 5;
	
	$starters = array_slice($toc, 0, $chunksize);
	$stubs = array_slice($toc, $chunksize);
	
	foreach ($starters as $starter) {
		echo wrappost(readpost($cx, unttime($starter)), $class='post');
	}
	
	foreach ($stubs as $stub) {
		$stub = ttime($stub);
		echo "<div class='stub' style='display: none' id='$stub'></div>\n";
	}

}

$t1 = microtime(1) - $t0;
echo "<!-- $t1 seconds -->";
?>
</div>

	</body>
</html>
