<?php
  require_once('JBCache.class.php');
  $cache = new JBCache();
  $cache->start($_SERVER['REQUEST_URI']);
?>
<body>
<p>If you had an crystal ball you could see that...</p>
<?php
  for ($i=0; $i<20; $i++) {
      $num = mt_rand(5, 15);
      for ($u=0; $u<20; $u++) {
	  $tot = $num * $i;
	  $tot = $tot * $u;
	  $tot = $tot * $tot;
      }
          printf("The magic number is: %d<br />", $tot);
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