<?php

session_start();

$config = parse_ini_file('/var/www/test/config.ini');
$mysqli = new mysqli($config['servername'], $config['username'], $config['password'], $config['dbname']);


//display alert
function alert($alert)
{
    clean_data($alert);
    echo "<script>swal({$alert})</script>";
}


//clean user data
function clean_data($data)
{
    global $mysqli;
    $data = $mysqli->escape_string($data);
    $data = trim($data);
    $data = htmlspecialchars($data);
    $data = stripslashes($data);
    return $data;
}

//get user ip
function ip()
{
    return $_SERVER['REMOTE_ADDR'];
}

//random gen
function gen($length)
{
    $length = $length / 2;
    return bin2hex(random_bytes($length));
}

//generate_csrf
function csrf_gen()
{
    if (isset($_SESSION['token'])) {
        return $_SESSION['token'];
    } else {
        $_SESSION['token'] = gen(32);
        return $_SESSION['token'];
    }
}

//validate_csrf
function csrf_val($post_token)
{
    if (!isset($_SESSION['token'])) {
        logout('CSRF error!');
    }

    if (!(hash_equals($_SESSION['token'], $post_token))) {
        logout('CSRF error!');
    } else {
        unset($_SESSION['token']);
    }
}

//check if user has been logged in
function login()
{
    if (!$_SESSION['logged_in']) {
        logout('Please Log In!','error');
    }

    //check if account is active
    if (!$_SESSION['user_active']) {
        logout('Your Account is  inactive or is temporarily disabled!');
    }

    //auto logout after 10min no activity
    if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 600)) {
        logout('Your session has expired!');
    } else {
        $_SESSION['LAST_ACTIVITY'] = time();
    }

    //regenerate session id (sec against session stealing)
    if (!isset($_SESSION['CREATED'])) {
        $_SESSION['CREATED'] = time();
    } elseif (time() - $_SESSION['CREATED'] > 600) {
        session_regenerate_id(true);
        $_SESSION['CREATED'] = time();
    }

    //check if session is stolen
    if ($_SESSION['ip'] != ip()) {
        logout('Hack attempt detected!');
    }
}

function login_admin()
{
    login();

    if (!$_SESSION['user_type']) {
        logout('This page is only availible for administrators!');
    }
}


function login_user($owner)
{
    login();

    if ($_SESSION['user_name'] != $owner) {
        logout('This page is only availible for ' . $owner . '!');
    }
}

function logout($alert)
{
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), “”, time()-3600, “/” );
    }
    $_SESSION = array();
    session_destroy();
    header('Location: /' . $alert);
    exit;
}
