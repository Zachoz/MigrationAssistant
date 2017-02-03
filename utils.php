<?php

class Utils {

    public static function getApiResponse($host, $domain, $username, $password) {
        $postData = http_build_query(
            array(
                'username' => $username,
                'password' => $password,
                'domain' => $domain,
                'host' => $host
            )
        );

        $options = array('http' =>
            array(
                'method' => 'POST',
                'header' => 'Content-type: application/x-www-form-urlencoded',
                'content' => $postData
            )
        );

        $context = stream_context_create($options);

        return json_decode(file_get_contents('https://migration.zachoz.com/accountcheck.php', false, $context), true);
    }

    public static function passAccountCredentials($line) {
        $accountSplit = explode(" / ", $line);

        return array( // follow the exact order!
            'username' => $accountSplit[0],
            'password' => $accountSplit[1],
            'domain' => $accountSplit[2]
        );
    }

    public static function passEmailAccountCredentials($line) {
        $accountSplit = explode(" / ", $line);

        return array( // follow the exact order!
            'email' => $accountSplit[0],
            'password' => $accountSplit[1]
        );
    }

}