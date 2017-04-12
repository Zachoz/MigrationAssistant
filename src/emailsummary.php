<?php
require(__DIR__ . '/classes/utils.php');

Utils::enableOutputBuffer();

$accounts = array();

$host = $_POST['host']; // set host

if (!isset($_POST['accounts'])) { // if 'accounts' exists, they're testing multiple accounts
    $accounts[] = array( // testing single account
        'email' => $_POST['email'],
        'password' => $_POST['password']
    );
} else { // Testing multiple accounts
    $splitAccounts = preg_split("/\\r\\n|\\r|\\n/", $_POST['accounts']);

    foreach ($splitAccounts as $accountStr) {
        error_log("str" . $accountStr);
        $accounts[] = Utils::passEmailAccountCredentials($accountStr);
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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

<? include(__DIR__ . '/includes/header.php'); ?>
<script>document.getElementById('emailcheck').className = 'active';</script>
<br><br><br>

<?
// Make sure all required data exists
if ($host == null || $host == "") {
    echo "<div class='container'><h2>Data missing! Please make sure all fields are filled in!</h2></div>";
    die();
}

// Check that cPanel server is accessible
if (!fsockopen($host, 993, $errno, $errstr, 10)) { // if connection to email server fails
    echo "<div class='container'><h2>Connection to mail server on " . $host . ":993 failed!</h2>";
    echo "Error: " . $errstr;
    echo "</div>";
    die();
}
?>

<div class="container">
    <h2>Checking email accounts on: <? echo $host; ?></h2>
    <h3 id="currentacc">Testing account: 0 / <? echo count($accounts); ?></h3>
    <br>

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

                $mailbox = @imap_open("{" . $host . ":993/ssl/novalidate-cert}INBOX", $account['email'], $account['password']);

                if ($mailbox) { // login successful
                    $quota = imap_get_quotaroot($mailbox, "INBOX");

                    echo "<div class='panel panel-success'>";

                    echo "<div class='panel-heading'><b>" . $account['email'] . "</b></div>";
                    echo "<div class='panel-body'>";
                    // Body of panel
                    echo "<p>Login: <b>Success</b></p>";
                    echo "<p>Quota used: " . round(($quota['STORAGE']['usage'] / 1024), 2) . "MB</p>";
                    echo "<p>Total quota: " . ($quota['STORAGE']['limit'] / 1024) . "MB</p>";
                    echo "<p>Number of emails in INBOX: " . imap_num_msg($mailbox) . "</p>";
                    echo "</div>";
                    echo "<div class='panel-footer'>"; // open panel-footer
                    echo "<p>Ready to migrate!</p>";
                    echo "</div>"; // close panel-footer
                    echo "</div>";

                    imap_close($mailbox);
                } else {
                    echo "<div class='panel panel-danger'>";
                    echo "<div class='panel-heading'><b>" . $account['email'] . "</b></div>";
                    echo "<div class='panel-body'>";
                    // Body of panel
                    echo "<p>Login: <b>Failed</b></p>";
                    echo "<p>Error: " . imap_last_error() . "</p>";
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