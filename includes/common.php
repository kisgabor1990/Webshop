<?php

session_start();

//require './includes/PHPMailer/Exception.php';
//require './includes/PHPMailer/PHPMailer.php';
//require './includes/PHPMailer/SMTP.php';

require('./includes/constants.php');
require('./includes/db_connection.php');
require('./includes/functions.php');
require('./includes/template_engine.php');

//if (isLoggedIn()) require('./includes/functions_cart_db.php');
//else require('./includes/functions_cart_cookie.php');