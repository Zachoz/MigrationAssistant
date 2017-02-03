<?php

require_once('cpanelaccount.class.php');

$host = $_POST['host'];
$domain = strtolower($_POST['domain']);
$username = $_POST['username'];
$password = $_POST['password'];

$response = "";

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

    $response = json_encode(
        array(
            'login' => true,
            'primary_domain_matches' => $primaryDomainMatches,
            'primary_domain' => $actualPrimaryDomain,
            'diskquota' => $diskQuota,
            'diskquotaused' => $diskQuotaUsed,
            'diskusedpercentage' => $diskUsedPercentage,
            'addondomains' => $addonDomains
        )
        , true);

} else {  // Login failed
    $response = json_encode(
        array(
            'login' => false
        ), true);
}

print $response;