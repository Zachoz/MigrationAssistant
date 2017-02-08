<?php

require(__DIR__ . '/cpanelaccount.class.php');

class Utils {

    public static function getApiResponse($host, $domain, $username, $password) {
        $account = new CpanelAccount($host, $username, $password);

        if ($account->login()) {
            $primaryDomainMatches = false;
            $actualPrimaryDomain = strtolower($account->getPrimaryDomain());

            if ($actualPrimaryDomain == $domain) $primaryDomainMatches = true;

            $diskQuotaApiResponse = $account->getDiskUsage();
            $diskQuota = $diskQuotaApiResponse['quotalimit'];
            $diskQuotaUsed = $diskQuotaApiResponse['quotaused'];
            $diskUsedPercentage = round((intval($diskQuotaUsed) / intval($diskQuota) * 100), 2);
            $addonDomains = $account->getAddonDomains();

            $response = array(
                'login' => true,
                'primary_domain_matches' => $primaryDomainMatches,
                'primary_domain' => $actualPrimaryDomain,
                'diskquota' => $diskQuota,
                'diskquotaused' => $diskQuotaUsed,
                'diskusedpercentage' => $diskUsedPercentage,
                'addondomains' => $addonDomains
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

}