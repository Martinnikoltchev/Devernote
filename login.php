<?php

    /*
     * Copyright 2010-2012 Evernote Corporation.
     *
     * This sample web application demonstrates the process of using OAuth to authenticate to
     * the Evernote web service. More information can be found in the Evernote API Overview
     * at http://dev.evernote.com/documentation/cloud/.
     *
     * This application uses the PHP OAuth Extension to implement an OAuth client.
     * To use the application, you must install the PHP OAuth Extension as described
     * in the extension's documentation: http://www.php.net/manual/en/book.oauth.php
     */

    // Include our configuration settings
    require_once 'config.php';

    // Include our OAuth functions
    require_once 'functions.php';

    // Use a session to keep track of temporary credentials, etc
    session_start();

    // Status variables
    $lastError = null;
    $currentStatus = null;

    // Request dispatching. If a function fails, $lastError will be updated.
    if (isset($_GET['action'])) {
        $action = $_GET['action'];
        if ($action == 'callback') {
            if (handleCallback()) {
                if (getTokenCredentials()) {
                    listNotebooks();
                }
            }
        } elseif ($action == 'authorize') {
            if (getTemporaryCredentials()) {
                // We obtained temporary credentials, now redirect the user to evernote.com to authorize access
                header('Location: ' . getAuthorizationUrl());
            }
        } elseif ($action == 'reset') {
            resetSession();
        }
    }
?>

<html>
    <head>
        <!-- Javascript Files -->
        <script type="text/javascript" src="../static/js/jquery-2.1.1.min.js"></script>
        <script type="text/javascript" src="../static/js/bootstrap.min.js"></script>

        <!-- CSS Files -->
        <link rel="stylesheet" type="text/css" href="../static/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="../static/css/bootstrap-theme.min.css">
    </head>
    <body>

        <nav class = "navbar navbar-default" role="navigation">
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <li><a class="navbar-brand" href="../index.php">Devernote</a></li>
                    <li><a href="work.html">Text Editor</a></li>
                </ul>
            </div>
        </nav>
<?php if (isset($lastError)) { ?>
        <p style="color:red">An error occurred: <?php echo htmlspecialchars($lastError);  ?></p>
<?php } elseif ($action != 'callback') { ?>

        <p>
            <a href="login.php?action=authorize">Click here</a> to authorize this application to access your Evernote account. You will be directed to evernote.com to authorize access, then returned to this application after authorization is complete.
        </p>

<?php } else { ?>
        
    <script type="text/javascript">
        window.location = "../index.php";
    </script>

    <p>
        Click <a href="../index.php">here</a> to redirect to the home page (if it's not automatic).
    </p>

    <?php if (isset($_SESSION['notebooks'])) { ?>
        <ul>
        <?php foreach ($_SESSION['notebooks'] as $notebook) { ?>
            <li><?php echo htmlspecialchars($notebook); ?></li>
        <?php } ?>
        </ul>

    <?php } // if (isset($_SESSION['notebooks'])) ?>
<?php } // if (isset($lastError)) ?>


    </body>
</html>