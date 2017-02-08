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
</head>
<body>

<? include(__DIR__ . '/includes/header.php'); ?>
<script>document.getElementById('home').className = 'active';</script>
<br><br><br>

<div class="container">
    <h2>Migration Assistant</h2>
    <p>This tools allows you to automagically check the login credentials for both cPanel and email accounts.</p>

    <div class="col-md-6" class="pull-left">
        <div class="row">
            <div class="col-md-11" class="pull-left">
                <h3>Checking cPanel accounts</h3>
                <p>This tool will automatically check that the cPanel login credentials work. It will also check the
                    following:</p>
                <ul>
                    <li>That the primary domains match</li>
                    <li>There is sufficient disk space available (40% free)</li>
                </ul>
                <h4>Checking multiple accounts:</h4>
                <p>Accounts can be checked in bulk, by inputting them in the same format (spaces required) that they
                    come through in via ticket:</p>
                <code>username / password / domain</code>
                <br><br>
                <p>Each account must be added on a new line in the exact format. They can literally be copied and pasted
                    from the migration ticket.</p>
                <h4>Things to note:</h4>
                <ul>
                    <li>This will only check over SSL/TLS (port 2083)</li>
                    <li>SSL certificates are not checked or validated</li>
                    <li>This tool may fail on hosts that interfere with or block cPanel's API2</li>
                    <li>IP may be blocked if too many failed logins/API requests are made</li>
                </ul>
                <h4>If a log in fails:</h4>
                <p>If a login fails, especially during a bulk test, try either testing those accounts again in this
                    tool, or via the actual cPanel login. If an account has a successful login, it will show data, and
                    you'll be able to tell it worked. If a login fails, it may be due to some other factor.</p>
            </div>
        </div>
    </div>

    <div class="col-md-6" class="pull-left">
        <div class="row">
            <div class="col-md-11" class="pull-left">
                <h3>Checking email accounts</h3>
                <p>This tool will automatically check that email login credentials are working. It will also check:</p>
                <ul>
                    <li>The email quota of the mailbox (this may display incorrectly for cPanel based email accounts)
                    </li>
                    <li>The number of emails in the mailbox</li>
                </ul>
                <h4>Checking multiple accounts:</h4>
                <p>Accounts can be checked in bulk, by inputting them in the follow format (spaces required):</p>
                <code>example@email.com / password</code>
                <br><br>
                <p>Each account must be added on a new line in the exact format. They can literally be copied and pasted
                    from the migration ticket.</p>
                <h4>Things to note:</h4>
                <ul>
                    <li>This will only check over IMAP SSL/TLS (port 993)</li>
                    <li>SSL certificates are not checked or validated</li>
                    <li>cPanel email accounts do not show correct email usage quotas, and will rather show the cPanel
                        account's quota
                    </li>
                </ul>
            </div>
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