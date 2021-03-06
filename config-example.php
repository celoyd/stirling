<?php
// Basic settings
$pg_connect_string = "host=localhost port=5432 dbname=database user=user";
$base_uri = "http://stirling/";
$blog_title = "Stirling";
$titlediv = "<div id='title'><h1><a href='/naus'>$blog_title</a></h1><br />
	by $blog_author<br />
	(<a href='/naus/about' id='about'>learn more</a>)</div>";
$mod_quote = "<blockquote id='mod_quote'>Über allen Gipfeln<br />
<span style='margin-left: 1.5em;'>Ist Ruh,<br />
In allen Wipfeln<br />
<span style='margin-left: 1.5em;'>Spürest du<br />
Kaum einen Hauch;<br />
<span style='margin-left: 1.5em;'>Die Vögelein schweigen im Walde.<br />
Warte nur, balde<br />
<span style='margin-left: 1.5em;'>Ruhest du auch.</blockquote>\n";
$authorclause = "<author>
		<name>Panthalassans</name>
		<uri>http://panthalassa.net/</uri>
		<email>naus@panthalassa.net</email>
	</author>";


// Language settings
$polyglot = false;
$lang = "en";			// comment this line out if polyglot is enabled.
?>
