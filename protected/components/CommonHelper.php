<?php

class CommonHelper {
    public static function BoolToInt($value) {
        return $value ? 1 : 0;
    }
    
    public static function IntToBool($value) {
        return $value > 0;
    }

    public static function SplitWithVBar($str) {
        return explode("|", $str);
    }
    
    public static function JoinWithVBar($array) {
        return implode("|", $array);
    }
    
    public static function SendAsync($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1);
        /*$curl_cookies = session_name() . '=' . session_id() . '; path=/';
        curl_setopt($ch, CURLOPT_COOKIE, $curl_cookies);*/
        curl_exec($ch);
        curl_close($ch);
    }
}

?>
