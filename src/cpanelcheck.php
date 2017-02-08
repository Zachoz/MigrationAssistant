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
    <script> function setSingleStatus() {
            document.getElementById('statussingle').innerHTML = 'Running...';
        }</script>
    <script> function setMutliStatus() {
            document.getElementById('statusmulti').innerHTML = 'Running...';
        }</script>
</head>
<body>

<? include(__DIR__ . '/includes/header.php'); ?>
<script>document.getElementById('cpanelcheck').className = 'active';</script>
<br><br><br>

<div class="container">
    <h2>Check cPanel accounts</h2>

    <div class="col-md-6" class="pull-left">
        <div class="row">
            <div class="col-md-11" class="pull-left">
                <h3>Check a single account</h3>
                <form action="cpanelsummary.php" method="post">
                    <div class="form-group">
                        <input type="text" placeholder="Server Host name or IP address" class="form-control" id="host"
                               name="host" required>
                    </div>
                    <div class="form-group">
                        <input type="text" placeholder="cPanel Username" class="form-control" id="username"
                               name="username" required>
                    </div>
                    <div class="form-group">
                        <input type="text" placeholder="cPanel Password" class="form-control" id="password"
                               name="password" required>
                    </div>
                    <div class="form-group">
                        <input type="text" placeholder="Primary domain" class="form-control" id="domain" name="domain"
                               required>
                    </div>
                    <button type="submit" class="btn btn-default" onclick="setSingleStatus();">Check Account</button>
                </form>
                <div><h4 id="statussingle"></h4></div>
            </div>
        </div>
    </div>

    <div class="col-md-6" class="pull-left">
        <div class="row">
            <div class="col-md-11" class="pull-left">
                <h3>Check multiple accounts account</h3>
                <form action="cpanelsummary.php" method="post">
                    <div class="form-group">
                        <input type="text" placeholder="Server Host name or IP address" class="form-control" id="host"
                               name="host" required>
                    </div>
                    <div class="form-horizontal">
                        <div class="form-group">
                            <div class="col-md-12">
                                <textarea class="form-control" id="accounts" rows="6" name="accounts"
                                          placeholder="username / password / domain" required></textarea>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-default" onclick="setMutliStatus();">Check Multiple Accounts
                    </button>
                </form>
                <div><h4 id="statusmulti"></h4></div>
            </div>
        </div>
    </div>

</div>

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"
        integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
        crossorigin="anonymous"></script>
</body>
</html>