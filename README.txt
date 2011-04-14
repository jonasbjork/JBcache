Author  : Jonas Bjork <jonas.bjork@aller.se>
Company : Aller Digitala Affärer, Aller media AB
Webpage : http://aller.se/
Version : 1.0
Date    : 2011-04-12
License : GNU General Public License, GPLv2
Contribution: David V. Wallin <david@dwall.in>

JBCache is a PHP class for filecaching. Useful for any webpage that
does have dynamic content that does not update in real time.

Using an identifier for the start() method that is same every time a
page is being requested is highly recommended!

Changelog:
== 2011-04-14 ==
* Added David V. Wallins patch for gzip compression.
* Rewrote some of the gzip code.
* Making use of destructor for stopping cache and writing file, making it 
  easier to use the cache class. Now you only have to include class-file 
  and instantiate the object with identifier as parameter to start cache.
* Did some code idention clean up.

== 2011-04-12 ==
* First relase of JBCache.
* Caches output to html file.

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

You can use one of these methods:
  require_once('JBCache.class.php');
  $cache = new JBCache();
  $cache->start($_SERVER['REQUEST_URI']);
Or
  require_once('JBCache.class.php');
  $cache = new JBCache($_SERVER['REQUEST_URI']);

You probably can put that $cache = line into end of JBCache.class.php too..

==START_OF_FILE==
<?php
  require_once('JBCache.class.php');
  $cache = new JBCache($_SERVER['REQUEST_URI']);
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
==END_OF_FILE==

The trick is to wrap your normal php/html-file with JBCache. That's all!

Using $_SERVER['REQUEST_URI'] as identifer might be smart, the variable
does contain your URI (whats after hostname). For example this URL:
http://aller.se/a/lot/of/funny/stuff?where=here&probably=notThere would
give us /a/lot/of/funny/stuff?where=here&probably=notThere .

Release early, release often!
http://en.wikipedia.org/wiki/Release_early,_release_often

