<?php

class App{
    protected $_conf;
    /**
     * @param $url
     * @return DateTime|false|int
     * @throws Exception
     */
    public function curl_get_lastmod( $url ) {
        $curl = curl_init( $url );
        // Issue a HEAD request and follow any redirects.
        curl_setopt( $curl, CURLOPT_NOBODY, true ); //don't fetch the actual page, you only want headers
        curl_setopt( $curl, CURLOPT_HEADER, true );
        curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true ); //stop it from outputting stuff to stdout
        //curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, true );
        //curl_setopt( $curl, CURLOPT_FILETIME, true); // attempt to retrieve the modification date
        //curl_setopt( $curl, CURLOPT_USERAGENT, get_user_agent_string() );
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT ,0);
        curl_setopt($curl, CURLOPT_TIMEOUT, 2); //timeout in seconds

        $data = curl_exec( $curl );
        curl_close( $curl );

        if( $data ) {
            $status = "unknown";
            if( preg_match( "/^HTTP\/1\.[01] (\d\d\d)/", $data, $matches ) ) {
                $status = (int)$matches[1];
            }
            if( preg_match( "/Last-Modified: ([0-9A-Za-z,:\s]+) GMT/", $data, $matches ) ) {
                $lastmod = $matches[1];
            }
            // http://en.wikipedia.org/wiki/List_of_HTTP_status_codes
            if( $status == 200 || ($status > 300 && $status <= 308) ) {
                if (!$lastmod){
                    $now = new DateTime();
                    $lastmod = $now->format('D, d M Y H:i:s');
                }
                $date = date_create_from_format('D, d M Y H:i:s', trim($lastmod),new DateTimeZone('GMT')); //Mon, 19 Oct 2015 14:01:51 GMT
                $date->setTimezone(new DateTimeZone('Europe/Paris'));
                return $date;
            }else{
                return -1;
                /*$exif_data = exif_read_data ( $remote_file );
                $date = new DateTime();
                if (isset($exif_data["FileDateTime"]))
                  return $date->setTimestamp($exif_data["FileDateTime"]);*/
            }
        }

        return -1;
    }

    /**
     * @param array $app
     * @return App
     */
    public function __construct($conf){
        if (!isset($conf['theme'])){
            $conf['theme'] = 'default';
        }
        $this->_conf = $conf;
        return $this;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getConf($key = null){
        if (!$key)
            return $this->_conf;
        if (isset($this->_conf[$key]))
            return $this->_conf[$key];
        return null;
    }

    /**
     * @param string $dir
     * @return bool
     */
    static function sysCheck($dir){
        //conf
        $conf_file = $dir.'/data/app.yaml';
        if (!file_exists($conf_file))
            return $conf_file." do not exist";
        //cache
        $cache_dir = $dir.'/var/cache';
        if (!is_dir($cache_dir))
            return $cache_dir." do not exist";
        if (!is_writable($cache_dir))
            return $cache_dir." dir is not writable";
        //cam
        $cams_file = $dir.'/data/cameras.yaml';
        if (!file_exists($cams_file))
            return $cams_file." do not exist";
        return false;
    }
}
