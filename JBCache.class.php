<?php
/**
 * JBCache is a filecache class for PHP
 * Written by Jonas Björk <jonas.bjork@aller.se>
 * 
 * Gzip-compression added by David V. Wallin <david@dwall.in>
 *
 * (C)2011 Aller Digitala Affärer, Aller media AB
 * Licensed under GNU General Public License v2
 */
define('CACHE_DIR', "cache/"); // cache directory
define('CACHE_TIME', 5*60); // cache time in seconds
define('PURGE_USE', TRUE); // automatic purge of cache?
define('PURGE_FACTOR', 100); // probability of cache purge, low number means higher probability

class JBCache {

    private $cachefile;
    private $fp;
    private $has_cache;
        
    private $m_time;
    private $starttime;
    private $endtime;
    private $totaltime;
    
    /**
     * Construction area. Please bring some concrete.
     */
    public function  __construct() {
        $this->cachefile = "";
        $this->fp = NULL;
        $this->has_cache = FALSE;
	
	$m_time = explode(" ",microtime());
	$m_time = $m_time[0] + $m_time[1];
	$this->starttime = $m_time;
    }
    
    public function __destruct() {
	$rounder = 6;
	$m_time = explode(" ",microtime());
	$m_time = $m_time[0] + $m_time[1];
	$endtime = $m_time;
	$totaltime = ($endtime - $this->starttime);
	echo "Page loading took:". round($totaltime, $rounder) ." seconds";
    }
    
    /**
     * Start the cache wrapper
     *
     * @param string $identifier Something to identify the file you're caching.
     * @return boolean Successful or not?
     */
    public function start($identifier = NULL) {
        if (PURGE_USE) {
            $this->purge_probe();
        }
        
        $this->cachefile = CACHE_DIR.sha1($identifier).".html.gz";
        if (file_exists($this->cachefile) && (time() - CACHE_TIME < filemtime($this->cachefile))) {
	    ob_start();
	    readgzfile($this->cachefile);
	    ob_end_flush();
            printf("<!-- Generated from jbCache - %s -->\n", date("Y-m-d H:i:s", filemtime($this->cachefile)));
            exit;
        }
        if (!is_writeable(CACHE_DIR)) return FALSE;

        $this->fp = fopen(gzuncompress($this->cachefile, 9), 'w');
        if (!$this->fp) return FALSE;

        $this->has_cache = TRUE;
        ob_start();
        return true;
    }

    /**
     * Stop the cache wrapper, save rendered page to file.
     *
     * @return boolean Successful or not?
     */
    public function stop() {
        if (!$this->has_cache) return FALSE;
        if ($this->fp) {
            fwrite($this->fp, gzcompress(ob_get_contents(), 9));
            fclose($this->fp);
            ob_end_flush();
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function has_cache() {
        return $this->has_cache;
    }

    /**
     * Purge the cached files. Using mtime on file and CACHE_TIME constant.
     */
    public function purge() {
        $handle = opendir(CACHE_DIR);
        if ($handle) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != "..") {
                    if ((time()-filemtime(CACHE_DIR.$file)) > CACHE_TIME) {
                        unlink(CACHE_DIR.$file);
                    }
                }
            }
            closedir($handle);
        }
    }

    /**
     * Probe if we should purge cache or not.
     * Using randomization for probe. Set PURGE_FACTOR to:
     * - low value for high probability of purging
     * - high value for low probability of purging
     * 
     * @return boolean
     */
    private function purge_probe() {
        $needle = ceil(PURGE_FACTOR/2);
        srand(time());
        $r = rand()%PURGE_FACTOR;
        if ($r == $needle) {
            $this->purge();
            return TRUE;
        } else {
            return FALSE;
        }
    }

}
