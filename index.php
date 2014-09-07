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
        <script type="text/javascript" src="static/js/jquery-2.1.1.min.js"></script>
        <script type="text/javascript" src="static/js/bootstrap.min.js"></script>

        <!-- CSS Files -->
        <link rel="stylesheet" type="text/css" href="static/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="static/css/bootstrap-theme.min.css">
        <style type="text/css">
            body { 
                background: url(static/img/congruent_outline.png) no-repeat center center fixed; 
                -webkit-background-size: cover;
                -moz-background-size: cover;
                -o-background-size: cover;
                background-size: cover;
            }
            h1,h3{
                color:white;
                text-align: center;
                font-weight: lighter;
                -webkit-touch-callout: none;
                -webkit-user-select: none; /* Webkit */
                -moz-user-select: none;    /* Firefox */
                -ms-user-select: none;     /* IE 10  */

                /* Currently not supported in Opera but will be soon */
                -o-user-select: none;
                user-select: none;
            }
            h1{
                margin-top: 250px;
                font-size:100px;
            }
            h3{
                font-size:40px;
            }
        </style>
    </head>
    <body>
        <script type="text/javascript">
        </script>
        <nav class = "navbar navbar-default" role="navigation">
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <li><a class="navbar-brand" href="">Devernote</a></li>
                    <li><a href="templates/work.html">Text Editor</a></li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li><a href="login.php?action=authorize">Login</a></li>
                </ul>
            </div>
        </nav>
        <div id="content_container">
            <h1>DEVERNOTE</h1>
            <h3>coding anywhere</h3>
            <p>
                <button class="btn btn-default">Continue</button>
            </p>
        </div>
    </body>
</html>
