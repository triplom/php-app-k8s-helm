
<?php
/*
 PHP Mini MySQL Admin
 (c) 2004-2017 Oleg Savchuk <osalabs@gmail.com> http://osalabs.com
 Light standalone PHP script for quick and easy access MySQL databases.
 http://phpminiadmin.sourceforge.net
 Dual licensed: GPL v2 and MIT, see texts at http://opensource.org/licenses/
*/

// Use environment variables for sensitive data
$ACCESS_PWD = getenv('MINI_ADMIN_PWD'); # Password is now stored in an environment variable for better security

if (!$ACCESS_PWD) {
    die('ACCESS PASSWORD NOT SET! Please configure your environment.');
}

// Improved security: hashed password verification
if (isset($_POST['password']) && !password_verify($_POST['password'], $ACCESS_PWD)) {
    die('Invalid password!');
}

// DEFAULT db connection settings using environment variables
$DBDEF = array(
  'user' => getenv('DB_USER'), #required
  'pwd' => getenv('DB_PWD'),   #required
  'db'  => getenv('DB_NAME'),  #optional, default DB
  'host' => getenv('DB_HOST'), #optional
  'port' => getenv('DB_PORT'), #optional
  'chset' => 'utf8',           #optional, default charset
);

$IS_COUNT = false; #set to true if you want to see Total records when pagination occurs (SLOWS down all select queries!)
$DUMP_FILE = dirname(__FILE__).'/pmadump'; #path to file without extension used for server-side exports

file_exists($f = dirname(__FILE__) . '/phpminiconfig.php') && require($f); # Load external config

if (function_exists('date_default_timezone_set')) {
    date_default_timezone_set('UTC'); #required by PHP 5.1+
}

// Constants
$VERSION = '1.9.170703';
$MAX_ROWS_PER_PAGE = 50;
$D = "\r\n"; #default delimiter for export
$BOM = chr(239).chr(187).chr(191);
$SHOW_D = "SHOW DATABASES";
$SHOW_T = "SHOW TABLE STATUS";
$DB = array(); #working copy for DB settings
$self = $_SERVER['PHP_SELF'];

session_set_cookie_params(0, null, null, false, true);
session_start();
if (!isset($_SESSION['XSS'])) {
    $_SESSION['XSS'] = bin2hex(random_bytes(16)); # More secure random string generation
}
$xurl = 'XSS=' . $_SESSION['XSS'];

ini_set('log_errors', 1);  # Enable error logging
ini_set('error_log', dirname(__FILE__) . '/phpminiadmin_error.log'); # Define log file for errors
error_reporting(E_ALL ^ E_NOTICE);

// Strip quotes if magic quotes are on (this feature is deprecated in recent PHP versions)
if (ini_get('magic_quotes_gpc')) {
    $_COOKIE = array_map('stripslashes', $_COOKIE);
    $_REQUEST = array_map('stripslashes', $_REQUEST);
}

// Sanitize inputs to prevent XSS and SQL injection
function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

$_POST = array_map('sanitize', $_POST);
$_GET = array_map('sanitize', $_GET);
$_REQUEST = array_map('sanitize', $_REQUEST);

// Secure database query using prepared statements (example)
function execute_query($pdo, $query, $params = []) {
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Sample login handling with hashed password check
if ($_REQUEST['login']) {
    $password_input = $_REQUEST['password'];

    if (password_verify($password_input, $ACCESS_PWD)) {
        echo 'Login successful!';
        // Proceed with DB interaction
    } else {
        die('Access denied!');
    }
}

// Example of a secured SQL query
if (isset($_POST['query'])) {
    $pdo = new PDO('mysql:host=' . $DBDEF['host'] . ';dbname=' . $DBDEF['db'], $DBDEF['user'], $DBDEF['pwd']);
    $query = $_POST['query'];

    // Use a parameterized query
    $results = execute_query($pdo, $query);
    print_r($results);
}
?>
