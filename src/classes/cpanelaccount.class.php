<?php

class CpanelAccount {

    public $host, $username, $password;
    private $loginSuccessful;

    function __construct($host, $username, $password) {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
    }

    public function login() {
        $response = $this->execApiCall("DomainLookup", "getmaindomain"); // just run any API call
        $responseArray = json_decode($response, true);
        $loginSuccessful = true;

        if (isset($responseArray['cpanelresult']['error']) && ($responseArray['cpanelresult']['error'] == "Access denied")) {
            $loginSuccessful = false;
        }

        $this->loginSuccessful = $loginSuccessful;

        return $loginSuccessful;
    }

    public function getPrimaryDomain() {
        $response = json_decode($this->execApiCall("DomainInfo", "domains_data"), true);
        return $response['data']['main_domain']['domain'];
    }

    public function getAddonDomains() {
        $response = json_decode($this->execApiCall("DomainInfo", "domains_data"), true);
        $addonDomainsList = $response['data']['addon_domains']; // data returns another array
        $parkedDomainsList = $response['data']['parked_domains']; // data returns another array
        $domains = array();
        
        foreach ($addonDomainsList as $domain) $domains[] = $domain['domain'];
        foreach ($parkedDomainsList as $domain) $domains[] = $domain; // I'm too tired right now to see why this doesn't need ['domain'}

        return $domains;
    }

    public function getDiskUsage() {
        $response = json_decode($this->execApiCall("Quota", "get_quota_info"), true);
        return array(
            'quotaused' => $response['data']['megabytes_used'],
            'quotalimit' => $response['data']['megabyte_limit'],
            'inodesUsed' => $response['data']['inodes_used']
        );
    }

    public function getMailDiskUsage() {
        $accountsListResponse = json_decode($this->execApiCall("Email", "list_pops"), true);
        $emailAccounts = array();

        foreach ($accountsListResponse['data'] as $account) {
            $emailAccounts[] = $account['email'];
        }

        $diskUsageBytes = 0;

        foreach($emailAccounts as $account) {
            $userSplit = explode("@", $account); // index 0 is username, index 1 is domain

            // Fix for cPanel default email account
            if (!isset($userSplit[1])) $userSplit[1] = "null"; // API doesn't care what domain is supplied so long as something is thrown to it
            
            $emailUsageApiResponse = json_decode($this->execApiCall("Email", "get_disk_usage", ("user=" . $userSplit[0] . "&domain=" . $userSplit[1])), true);
            $diskUsed = floatval($emailUsageApiResponse['data']['diskused']);
            $diskUsageBytes += $diskUsed;
        }

        return $diskUsageBytes;

    }

    public function execApiCall($module, $function, $parametres = "") {
        $query = "https://" . $this->host . ":2083/execute/" . $module . "/" . $function . "?" . $parametres;

        // Need to use API2 to more easily test login credentials.
        // If login fails, API2 actually outputs a json response saying the login failed
        // If a login fails with UAPI, it sends back the HTML for the fucking login page.
        if (!isset($this->loginSuccessful)) {
            $query = "https://" . $this->host . ":2083/json-api/cpanel?cpanel_jsonapi_user=" . $this->username .
                "&cpanel_jsonapi_apiversion=2&cpanel_jsonapi_module=" . $module . "&cpanel_jsonapi_func=" . $function;
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);       // Allow self-signed certs (since it's probs gonna go dodgy)
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);       // Allow certs that do not match the hostname
        curl_setopt($curl, CURLOPT_HEADER, 0);               // Do not include header in output
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);       // Return contents of transfer on curl_exec
        $header[0] = "Authorization: Basic " . base64_encode($this->username . ":" . $this->password) . "\n\r";
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);    // set the username and password
        curl_setopt($curl, CURLOPT_URL, $query);            // execute the query
        $result = curl_exec($curl);
        if ($result == false) {
            error_log("curl_exec threw error \"" . curl_error($curl) . "\" for $query");
        }
        curl_close($curl);

        return $result;
    }

}

?>