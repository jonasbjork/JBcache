<?php

/**
 * JBCache is a filecache class for PHP
 * Written by Jonas Björk <jonas.bjork@aller.se>
 * 
 * Contributing Developer: David V. Wallin <david@dwall.in>
 *
 * (C)2011 Aller Digitala Affärer, Aller media AB
 * Licensed under GNU General Public License v2
 */
define('CACHE_DIR', "cache/"); // cache directory
define('CACHE_TIME', 5 * 60); // cache time in seconds
define('PURGE_USE', TRUE); // automatic purge of cache?
define('PURGE_FACTOR', 100); // probability of cache purge, low number means higher probability
define('GZIP_COMPRESSION', TRUE); // want gzip-compression or not?
define('GZIP_LEVEL', 3); // define compression level (1-9, where 9 is highest)

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
    public function __construct($identifier = NULL) {
        $m_time = explode(" ", microtime());
        $m_time = $m_time[0] + $m_time[1];
        $this->starttime = $m_time;

        if ($identifier) {
            $this->start($identifier);
        } else {
            $this->cachefile = "";
            $this->fp = NULL;
            $this->has_cache = FALSE;
        }

    }

    public function __destruct() {
        $this->stop();
    }

    /**
     * Should we show compressed content or not?
     * 
     * @param none
     * @return Nothing - Includes or reads the file
     * @author David V. Wallin <david@dwall.in>
     */
    private function show_cached_content() {
        if (GZIP_COMPRESSION == TRUE) {
            readgzfile($this->cachefile);
        } elseif (GZIP_COMPRESSION == FALSE) {
            include($this->cachefile);
        } else {
            return false;
        }
    }

    /**
     * Set the name of the cached file
     * 
     * @param string $identifier Something to identify the file you're caching.
     * @return string With either .html or .html.gz as a fileending
     * @author David V. Wallin <david@dwall.in>
     */
    private function start_cache_file($identifier = NULL) {

        if ( GZIP_COMPRESSION == TRUE ) {
            return CACHE_DIR . sha1($identifier) . ".html.gz";
        } elseif (GZIP_COMPRESSION == FALSE) {
            return CACHE_DIR . sha1($identifier) . ".html";
        } else {
            return false;
        }
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

        $this->cachefile = $this->start_cache_file($identifier);

        if (file_exists($this->cachefile) && (time() - CACHE_TIME < filemtime($this->cachefile))) {
            $this->show_cached_content();
            exit;
        } else {
            if (!is_writeable(CACHE_DIR))
                return FALSE;
            $this->fp = fopen($this->cachefile, 'c');
            if (!$this->fp)
                return FALSE;

            $this->has_cache = TRUE;
            ob_start();
            return true;
        }
    }

    /**
     * Writes the file-content to the cached file. Either compressed or not.
     * 
     * @param none
     * @return false if GZIP_COMPRESSION isn't defined otherwise just writes.
     * @author David V. Wallin <david@dwall.in>
     */
    private function write_file_content() {
        $page = ob_get_contents();
        if (GZIP_COMPRESSION == TRUE) {
            fwrite($this->fp, gzencode($page, GZIP_LEVEL));
        } elseif (GZIP_COMPRESSION == FALSE) {
            fwrite($this->fp, $page);
        } else {
            return false;
        }
	return $page;
    }

    /**
     * Stop the cache wrapper, save rendered page to file.
     *
     * @return boolean Successful or not?
     */
    public function stop() {
        if (!$this->has_cache)
            return FALSE;
        if ($this->has_cache && $this->fp) {
            $rounder = 6;
            $m_time = explode(" ", microtime());
            $m_time = $m_time[0] + $m_time[1];
            $endtime = $m_time;
            $totaltime = ($endtime - $this->starttime);
            printf("<!-- Generated from JBCache ( http://github.com/jonasbjork/JBcache ) - %s -->\n", date("Y-m-d H:i:s"));
            printf("<!-- Page loading took: %s seconds. -->\n", round($totaltime, $rounder));

            $this->write_file_content();
            fclose($this->fp);
            ob_end_flush();
	    print $page; 
            $this->has_cache = FALSE;
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
                    if ((time() - filemtime(CACHE_DIR . $file)) > CACHE_TIME) {
                        unlink(CACHE_DIR . $file);
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
        $needle = ceil(PURGE_FACTOR / 2);
        srand(time());
        $r = rand() % PURGE_FACTOR;
        if ($r == $needle) {
            $this->purge();
            return TRUE;
        } else {
            return FALSE;
        }
    }

}
