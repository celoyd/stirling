<?php
include_once('../tools.php');
global $polyglot;

if (isset($_POST['doom'])) {
	deletepost($cx, $_POST['posted']);
	Header('Location: ' . $base_uri);
	exit();
}

if (!($_POST['posted']) && isset($_POST['body'])) {
    if ($polyglot) {
        createpost($cx, $_POST['title'], $_POST['body'], $_POST['uri'], $_POST['lang']);
    } else {
	    createpost($cx, $_POST['title'], $_POST['body'], $_POST['uri']);
    }
	Header('Location: ' . $base_uri);
	exit();
}

if ($_POST['posted']) {
    if ($polyglot) {
	    updatepost($cx, $_POST['posted'], $_POST['title'], $_POST['body'], $_POST['uri'], $_POST['lang']);
    } else {
	    updatepost($cx, $_POST['posted'], $_POST['title'], $_POST['body'], $_POST['uri']);
    }
	Header('Location: ' . $base_uri);
	exit();
}

global $polyglot,$mod_quote,$base_uri;

?><!DOCTYPE html 
	PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<title>Mod</title>
<?php
echo "      <link rel='stylesheet' href='$base_uri/req/ship' type='text/css' />\n";
?>
	</head>
	<body>

<?php 

echo $mod_quote;

if (isset($_GET['posted'])) {
	$p = readpost($cx, $_GET['posted']);
    if ($polyglot) {
	    echo editform($p['posted'], $p['uri'], $p['title'], $p['body'], $p['lang']);
    } else {
	    echo editform($p['posted'], $p['uri'], $p['title'], $p['body']);
    }
        
} else {
	echo editform();
}

?>

	</body>
</html>
