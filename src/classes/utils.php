<?php

require(__DIR__ . '/cpanelaccount.class.php');

class Utils {

    public static function getApiResponse($host, $domain, $username, $password, $checkEmailUsage) {
        $account = new CpanelAccount($host, $username, $password);

        if ($account->login()) {
            $primaryDomainMatches = false;
            $actualPrimaryDomain = strtolower($account->getPrimaryDomain());

            if ($actualPrimaryDomain == $domain) $primaryDomainMatches = true;

            $diskQuotaApiResponse = $account->getDiskUsage();
            $diskQuota = $diskQuotaApiResponse['quotalimit'];
            $diskQuotaUsed = $diskQuotaApiResponse['quotaused'];
            $inodesUsed = $diskQuotaApiResponse['inodesUsed'];
            // Ensure it doesn't try dividing by zero
            $diskUsedPercentage = $diskQuota <= 0 ? 0 : round((intval($diskQuotaUsed) / intval($diskQuota) * 100), 2);
            $addonDomains = $account->getAddonDomains();
            $emailDiskUsage = $checkEmailUsage ? $account->getMailDiskUsage() : 0;

            $response = array(
                'login' => true,
                'primary_domain_matches' => $primaryDomainMatches,
                'primary_domain' => $actualPrimaryDomain,
                'diskquota' => $diskQuota,
                'diskquotaused' => $diskQuotaUsed,
                'diskusedpercentage' => $diskUsedPercentage,
                'inodes_used' => $inodesUsed,
                'addondomains' => $addonDomains,
                'email_disk_usage' => $emailDiskUsage
            );

        } else {  // Login failed
            $response = array(
                'login' => false
            );
        }

        return $response;
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

    public static function enableOutputBuffer() {
        // Print as each account as done rather than load page all at once
        @ini_set('zlib.output_compression', 0);
        @ini_set('implicit_flush', 1);
        @ob_end_clean();
        set_time_limit(0);
        header('Content-type: text/html; charset=utf-8');
        ob_start();
    }

    public static function flushBuffer() { // both of these are needed
        @ob_flush();
        @flush();
    }

}