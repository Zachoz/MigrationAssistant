<?php

class CpanelAccount {

    public $host, $username, $password;

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

        return $loginSuccessful;
    }

    public function getPrimaryDomain() {
        $response = json_decode($this->execApiCall("DomainLookup", "getmaindomain"), true);
        return $response['cpanelresult']['data'][0]['main_domain']; // data returns another array
    }

    public function getAddonDomains() {
        $response = json_decode($this->execApiCall("DomainLookup", "getbasedomains"), true);
        $domainsList = $response['cpanelresult']['data'][0]['domain']; // data returns another array
        $domains = explode(",", $domainsList);
        return $domains;
    }

    public function getDiskUsage() {
        $response = json_decode($this->execApiCall("DiskUsage", "fetchdiskusagewithextras"), true);
        return array(
            'quotaused' => $response['cpanelresult']['data'][0]['quotaused'],
            'quotalimit' => $response['cpanelresult']['data'][0]['quotalimit'],
            'mailman' => $response['cpanelresult']['data'][0]['mailman']
        );
    }

    private function execApiCall($module, $function) {
        $query = "https://" . $this->host . ":2083/json-api/cpanel?cpanel_jsonapi_user=" . $this->username .
            "&cpanel_jsonapi_apiversion=2&cpanel_jsonapi_module=" . $module . "&cpanel_jsonapi_func=" . $function;

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
            // log error if curl exec fails
        }
        curl_close($curl);

        return $result;
    }

}

?>