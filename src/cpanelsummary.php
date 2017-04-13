<?php
require(__DIR__ . '/classes/utils.php');

Utils::enableOutputBuffer();

$accounts = array();

$host = $_POST['host']; // set host

if (!isset($_POST['accounts'])) { // if 'accounts' exists, they're testing multiple accounts
    $accounts[] = array( // testing single account
        'username' => $_POST['username'],
        'password' => $_POST['password'],
        'domain' => $_POST['domain']
    );
} else { // Testing multiple accounts
    $splitAccounts = preg_split("/\\r\\n|\\r|\\n/", $_POST['accounts']);

    foreach ($splitAccounts as $accountStr) {
        $accounts[] = Utils::passAccountCredentials($accountStr);
    }
}

$checkEmailUsage = $_POST['checkemailusage'] == "Yes";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Migration Assistant</title>

    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <style>
        p {
            margin: 0 0 5px !important;
        }
    </style>
</head>
<body>

<? include('includes/header.php'); ?>
<script>document.getElementById('cpanelcheck').className = 'active';</script>
<br><br><br>

<?
// Make sure all required data exists
if ($host == null || $host == "") {
    echo "<div class='container'><h2>Data missing! Please make sure all fields are filled in!</h2></div>";
    die();
}

// Check that cPanel server is accessible
if (!@fsockopen($host, 2083, $errno, $errstr, 10)) { // if connection to cPanel server fails
    echo "<div class='container'><h2>Connection to cPanel on " . $host . ":2083 failed!</h2>";
    echo "Error: " . $errstr;
    echo "</div>";
    die();
}
?>

<div class="container">
    <h2>Checking accounts on: <? echo $host; ?></h2>
    <h3 id="currentacc">Testing account: 0 / <? echo count($accounts); ?></h3>
    <br>

    <?php
    Utils::flushBuffer(); // Load the header and shit
    ?>

    <div class="col-md-6" class="pull-left">
        <div class="row">
            <?php
            $testingAccountNumber = 0;

            foreach ($accounts as $account) {
                $testingAccountNumber++;
                // This is fucking disgusting but it works so fuck it
                echo "<script>document.getElementById('currentacc').innerHTML = 
                        document.getElementById('currentacc').innerHTML.replace('" . ($testingAccountNumber - 1) . "', '" . ($testingAccountNumber) . "');</script>";
                Utils::flushBuffer(); // make sure this actually gets printed

                $response = Utils::getApiResponse($host, $account['domain'], $account['username'], $account['password'], $checkEmailUsage);

                if ($response['login'] == "true") {

                    $primaryDomainMatch = ($account['domain'] !== "") ? $response['primary_domain_matches'] : true; // Domain doesnt need to be specified
                    $enoughFreeDiskSpace = floatval($response['diskusedpercentage']) <= 60.0;
                    $diskUsed = round((intval($response['diskquotaused'])), 2);
                    $diskQuota = (intval($response['diskquota']));
                    $inodesUsed = $response['inodes_used'];
                    $emailDiskUsage = $response['email_disk_usage'];
                    $possibleApiError = ($diskUsed == 0 && $diskQuota == 0); // if both of these are 0, likely an API error
                    $inodesOverLimit = $inodesUsed >= 200000;
                    $overFiveGbOfMail = $emailDiskUsage >= 4900;
                    $warning = (!$primaryDomainMatch || !$enoughFreeDiskSpace || $possibleApiError || $inodesOverLimit || $overFiveGbOfMail);

                    echo "<div class='panel panel-" . (!$warning ? "success" : "warning") . "'>";

                    echo "<div class='panel-heading'><b>" . $account['username'] . " / " . $account['domain'] . "</b></div>";
                    echo "<div class='panel-body'>";
                    // Body of panel
                    echo "<p>Login: <b>Success</b></p>";
                    if ($account['domain'] !== "") echo "<p>Primary domains match: <b>" . ($response['primary_domain_matches'] ? "Yes" : "No") . "</b></p>";
                    echo "<p>Primary domain: " . ($response['primary_domain']) . "</p>";

                    if (!$primaryDomainMatch && in_array($account['domain'], $response['addondomains']))
                        echo "<p>Domain (" . $account['domain'] . ") exists as addon domain: <b>Yes</b></p>";

                    echo("<p>Disk Usage: " . $diskUsed . "MB / " . $diskQuota . "MB (Disk used: " .
                        floatval($response['diskusedpercentage']) . "%)</p>");
                    echo("<p>Inodes used: " . $inodesUsed . "</p>");
                    if ($checkEmailUsage) echo("<p>Email disk usage: " . $emailDiskUsage . "MB</p>");
                    echo "</div>";
                    echo "<div class='panel-footer'>"; // open panel-footer
                    if (!$warning) {
                        echo "<p>Ready to migrate</p>";
                    } else {
                        if (!$primaryDomainMatch) echo "<p>Primary domains do not match!</p>";
                        if (!$enoughFreeDiskSpace) echo "<p>Not enough free disk space! Less than 40% available!</p>";
                        if ($inodesOverLimit) echo "<p>Account inode usage is over limit! (" . $inodesUsed . " / 200000)</p>";
                        if ($overFiveGbOfMail) echo "<p>Total email usage is close to or over 5GB! (" . $emailDiskUsage . "MB)</p>";
                        if ($possibleApiError) echo "<p>Query returned 0MB disk usage! This can indicate that the host is 
                            inteferring with cPanel's API, which will cause the copy tool to fail!</p>";
                    }
                    echo "</div>"; // close panel-footer
                    echo "</div>";

                } else {
                    echo "<div class='panel panel-danger'>";
                    echo "<div class='panel-heading'><b>" . $account['username'] . " / " . $account['domain'] . "</b></div>";
                    echo "<div class='panel-body'>";
                    // Body of panel
                    echo "<p>Login: <b>Failed</b></p>";
                    echo "</div>";
                    echo "<div class='panel-footer'>Please request correct login details</div>";
                    echo "</div>";

                }

                echo "<br>";
                
                Utils::flushBuffer();
            }

            ?>

            <script>document.getElementById('currentacc').innerHTML = (document.getElementById('currentacc').innerHTML + " - Complete!")</script>

        </div>
    </div>
</div>


<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"
        integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
        crossorigin="anonymous"></script>
</body>
</html>