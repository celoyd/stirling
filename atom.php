<?php
Header("Content-Type: application/atom+xml");
include_once('tools.php');
$latest = readnlatest($cx, 16);
$authorclause = "<author>
		<name>Panthalassans</name>
		<uri>http://panthalassa.net/</uri>
		<email>naus@panthalassa.net</email>
	</author>";

echo "<?xml version='1.0' encoding='utf-8'?>
<feed xmlns='http://www.w3.org/2005/Atom'>
	<title type='text'>$blog_title</title>
	<link rel='alternate' type='text/html'
	href='$base_uri' />
	<link rel='self' href='$base_uri/atom' />
	$authorclause
	<id>tag:panthalassa.net,2009:$blog_title</id>\n"; ?>
	<rights type="text">Copyright © 2009 Panthalassa. Fair use will be interpreted liberally.</rights>
	<updated><?php echo ttime($latest[0]['posted']); ?>:00</updated>
<?php


foreach ($latest as $post) {
	if (!$post['title']) { $post['title'] = "(" . $post['posted'] . ")"; }
	echo "<entry>\n\t<title type='html'>" . htmlspecialchars($post['title']) . "</title>
	<published>" . rfc3339($post['posted']) . "</published>
	<updated>" . rfc3339($post['posted']) . "</updated>
	$authorclause\n";
	// This next bit is an unapologetic hack. Fix it if you'd like; I didn't really see a better way.
	if (preg_match('/^http:/',$post['uri'])) {
		echo "<link rel='alternate' type='text/html' href='" . $post['uri'] . "' />\n";
	} else {
		echo "<link rel='alternate' type='text/html' href='$base_uri" . urlencode($post['uri']) . "' />\n";
	}
	echo "<id>tag:panthalassa.net,2009:" . urlencode(rfc3339($post['posted'])) . "</id>
	<content type='html'>
	" . htmlspecialchars(posttohtml($post,true)) . "
	</content>
	</entry>";
}
?>
</feed>
