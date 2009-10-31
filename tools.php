<?php
include_once("config.php");
include_once("markdown.php");

$cx = pg_pconnect($pg_connect_string);

set_magic_quotes_runtime(0);

$allowed_syntaxes = array("markdown"=>Markdown);

// all this time stuff is pretty gross

$timestamp = gmdate("Y-m-d\TH:i:s\Z", time());

function ttime($t) {
	return str_replace(' ', 'T', $t); }

function unttime($t) {
	return str_replace('T', ' ', $t); }

function rfc3339($t) {
	preg_match_all("|(....-..-..) (..:..:..)\.[0-9]*([-+]..).*|", $t, $o, PREG_PATTERN_ORDER);
	return $o[1][0] . "T" . $o[2][0] . $o[3][0] . ":00"; // bad, bad, bad
}

function clean($post) {
	$z = $post;
	$suspects = array('uri', 'title', 'body');
	foreach ($suspects as $suspect) {
		$z[$suspect] = stripslashes($post[$suspect]); }
	return $z;
}

function justdate($d) {
	$phpsux = explode(array(' ', 'T'), $d);
	return $phpsux[0]; }

function createpost($cx, $title, $body, $uri='', $lang='') {
    global $polyglot;
	try {
        if (!$polyglot) {
		    $qy = pg_prepare($cx, 'createpost', 'insert into post (title, body, uri, posted) values ($1, $2, $3, now())');
        } else {
            $qy = pg_prepare($cx, 'createpost', 'insert into post (title, body, uri, lang, posted) values($1, $2, $3, $4, now())');
        }
	} catch (Exception $e) { }
    if (!$polyglot) {
	    pg_execute($cx, 'createpost', array($title, $body, $uri));
    } else {
        pg_execute($cx, 'createpost', array($title, $body, $uri, $lang)); }
}

function readpost($cx, $id) {
	$qy = pg_query_params($cx, 'select * from post where posted = $1', array($id));
	$post = pg_fetch_assoc($qy);
	$post = array_map("clean", array($post));
	$post = $post[0]; // jesus
//	echo print_r($post);
	return $post;
}

function readtoc($cx,$lang=false) {
    global $polyglot;
    if ($polyglot and $lang) {
        $qy = pg_query($cx, "select posted from post where lang='$1' order by posted desc", array($lang));
    } else {
 	    $qy = pg_query($cx, 'select posted from post order by posted desc;');
    }
 	$fms = pg_fetch_all($qy);
 	return $fms;
}

function readnlatest($cx, $n) {
 	$qy = pg_query_params($cx, 'select * from post order by posted desc limit $1', array($n));
 	$fms = pg_fetch_all($qy);
 	return array_map("clean", $fms);
}

function readbyuri($cx, $uri) {
 	$qy = pg_query_params($cx, 'select * from post where uri = $1 limit 1', array($uri));
 	$fms = pg_fetch_assoc($qy);
 	return clean($fms);
}

function readbyposted($cx, $uri) {
 	$qy = pg_query_params($cx, 'select * from post where posted = $1 limit 1', array(unttime($uri)));
 	$fms = pg_fetch_assoc($qy);
 	return clean($fms);
}

function updatepost($cx, $posted, $title, $body, $uri, $lang=false) {
    global $polyglot;
    if (!$polyglot) {
	    pg_query_params($cx, 'update post set title = $1, body = $2, uri = $3 where posted = $4', array($title, $body, $uri, $posted));
    } else {
        pg_query_params($cx, 'update post set title = $1, body = $2, uri = $3, lang = $4 where posted = $5',array($title, $body, $uri, $lang, $posted));
    }
}

function deletepost($cx, $id) {
	$qy = pg_query_params($cx, 'delete from post where posted = $1', array($id)); }

function editform($posted='', $uri='', $title='', $body='', $lang='') {
    global $polyglot;
	if ($posted) {
		$ck = "<label for='doom'>delete? </label><input name='doom' type='checkbox' value='unchecked' />";
	} else { $ck = ''; }
    if ($polyglot) {
        $langbox = "<p><label for='lang'>language </label><input name='lang' type='text' style='width: 15em;' value=\"$lang\"/></p>\n";
    } else { $langbox = ''; }

// escape the double quotes in the $uri and $title

	$title = str_replace('"','\"',$title);
	$uri = str_replace('"','\"',$uri);
	return "<form action='./' method='post' accept-charset='UTF-8'>
	<p><input type='hidden' name='posted' value='$posted' /></p>
	<p><label for='title'>Title: </label><input name='title' type='text' style='width: 30em;' value=\"$title\"/></p>
	<p><label for='URI'>URI: </label><input name='uri' type='text' style='width: 15em;' value=\"$uri\"/></p>
    $langbox<p><textarea name='body' style='width: 45em; height: 30em'>$body</textarea></p>
	<p> $ck <input type='submit' value='&darr;' accesskey='s' /></p>
</form>";
}

// templating

function wrappost($f, $class='post') {
	return "<div class='$class'>" . posttohtml($f) . "</div>\n\n"; }


function posttohtml($f,$suppress_edit=false) {
	global $allowed_syntaxes,$base_uri;

    preg_match_all('/^\/\/.+?$/m',$f['body'],$directives);
    $f['body'] = preg_replace('/^\/\/.+?$/m','$1',$f['body']);
    $f['body'] = str_replace('\\/','/',$f['body']);
    foreach($directives[0] as $directive) {
        $directive = preg_replace('/^\/\//','',$directive);
        $directive = explode(" ",rtrim($directive));
        switch($directive[0]) {
            case "syntax":
                if (isset($allowed_syntaxes[$directive[1]])) {
                    $f['body'] = $allowed_syntaxes[$directive[1]]($f['body']);
                }
            case "alias":
                $f['body'] = str_replace($directive[1],$directive[2],$f['body']);
        }
    }
	$f['posted'] = ttime($f['posted']);
	if (!$f['uri']) { $f['uri'] = $f['posted']; }
		if ($anchor) {
	$anchorclause = "<span class='anchor'><a href='${f[uri]}'><!-- &#x25cc; -->※</a> </span>";
	} else { $anchorclause = ''; }
	$posted = justdate($f['posted']);

	if ($f['edited']) {
		$edited = "*";
		if ($edited == $posted) { $edited = ''; }
	} else { $edited = ''; }
	
	if ($suppress_edit) {
		return "\n<h1 title='$posted$edited'><a href='${f[uri]}'>${f[title]}</a></h1>\n\n" . "${f[body]}\n\n" . "\n";
	} else {
		return "\n<h1 title='$posted$edited'><a href='${f[uri]}'>${f[title]}</a></h1>\n\n" . "${f[body]}\n\n" . "<p class='editlink'><a href='http://fearchar.net/tan-sec/mod?posted=${f[posted]}'>[edit]</a></p>\n";
	} 
}
?>
