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
</body>
<?php
  if ($cache->has_cache()) {
          $cache->stop($_SERVER['REQUEST_URI']);
  }
?>