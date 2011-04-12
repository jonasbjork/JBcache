Author  : Jonas Bjork <jonas.bjork@aller.se>
Company : Aller Digitala Affärer, Aller media AB
Webpage : http://aller.se/
Version : 1.0
Date    : 2011-04-12
License : GNU General Public License, GPLv2

JBCache is a PHP class for filecaching. Useful for any webpage that
does have dynamic content that does not update in real time.

Using an identifier for the start() method that is same every time a
page is being requested is highly recommended!

Everything between start() end stop() will be cached!

Example of usage:

Let's pretend you have written this page (skipping code that is not
needed for example):

==START_OF_FILE==
<body>
<p>If you had an crystal ball you could see that...</p>
<?php
  for ($i=0; $i<20; $i++) {
    printf("The magic number is: %d<br />", $i);
  }
?>
<p>No wonder we want an crystal ball, right?</p>
<!-- Really cool code, right? ;) -->
</body>
==END_OF_FILE==

Ok, that would do alot of PHP-parsing wouldn't it? Well this example
is not cpu-demanding at all, that is almost. Now let's bring some nice
file cache to the file.

==START_OF_FILE==
<?php
  require_once('JBCache.class.php');
  $cache = new JBCache();
  $cache->start($_SERVER['REQUEST_URI']);
?>
<body>
<p>If you had an crystal ball you could see that...</p>
<?php
  for ($i=0; $i<20; $i++) {
    printf("The magic number is: %d<br />", $i);
  }
?>
<p>No wonder we want an crystal ball, right?</p>
<!-- Really cool code, right? ;) -->
</body>
<?php
  if ($cache->has_cache()) {
    $cache->stop($_SERVER['REQUEST_URI']);
  }
?>
==END_OF_FILE==

The trick is to wrap your normal php/html-file with JBCache. That's all!

Using $_SERVER['REQUEST_URI'] as identifer might be smart, the variable
does contain your URI (whats after hostname). For example this URL:
http://aller.se/a/lot/of/funny/stuff?where=here&probably=notThere would
give us /a/lot/of/funny/stuff?where=here&probably=notThere .

Release early, release often!
http://en.wikipedia.org/wiki/Release_early,_release_often

