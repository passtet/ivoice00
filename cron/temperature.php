<?php
$lock_file = __FILE__ . '.lock';
if(file_exists($lock_file)) {
    error_log(__FILE__ . 'is already running');
    exit(0);
}
touch($lock_file);

define('__NAME__', basename(__FILE__));

$xml_host       = 'derevo.no-ip.info';
$xml_url        = "http://$xml_host/st0.xml";
$xml_login      = 'admin';
$xml_password   = 'admin';

$db_host    = 'ivoice00.mysql.ukraine.com.ua';
$db_log     = 'ivoice00_db';
$db_pass    = 'lYLuBH3j';
$db_name    = 'ivoice00_db';
$db_table   = 'temperature';


try {
    if(!testInternet()) error_log('no internet connection');
    if(!testInternet($xml_host)) error_log("no connection with $xml_host");
    if(!testDBConnect($db_host, $db_log, $db_pass, $db_name)) ;

    $xmlData = getXml($xml_url, $xml_login, $xml_password);
    $xmlData = parseXml($xmlData);
    addRecord($xmlData, $db_host, $db_log, $db_pass, $db_name, $db_table);
} catch (Exception $e) {
    unlink($lock_file);
    throw new Exception($e->getMessage());
}

unlink($lock_file);

/*************/
/* FUNCTIONS */
/*************/
function testInternet($host = 'google.com', $port = 80, $auth = array()) {
    $waitTimeoutInSeconds = 30;
    $header ="GET / HTTP/1.0\r\n\r\n";
    $header .="Accept: text/html\r\n";

    if(!empty($auth)) $header .= "Authorization: Basic " . base64_encode("{$auth[0]}:{$auth[1]}") . "\r\n\r\n";

    $fp = fsockopen($host, $port, $errCode, $errStr, $waitTimeoutInSeconds);
    if (!$fp) echo "$errStr ($errCode)\n";

    fputs ($fp, $header);
    fclose($fp);
    return (bool)$fp;
}
function testDBConnect($servername, $username, $password, $dbname) {
    $conn = mysqli_connect($servername, $username, $password, $dbname);
    mysqli_close($conn);
    return $conn;
}

function getXml($xml_url, $xml_login, $xml_password) {
    $ch = curl_init();
    if (FALSE === $ch) throw new Exception('failed to initialize');

    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml'));
    curl_setopt($ch, CURLOPT_URL, $xml_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERPWD, "$xml_login:$xml_password");
    curl_setopt($ch,CURLOPT_FAILONERROR, 1);

    $xmlData = curl_exec($ch);
    if (FALSE === $xmlData) throw new Exception(curl_error($ch) . ' ' . curl_errno($ch));

    curl_close($ch);
    return $xmlData;
}
function parseXml($xmlData) {
    libxml_use_internal_errors(true);
    if(!$data = simplexml_load_string($xmlData)) {
        error_log('error loading ' . $xmlData);
        foreach(libxml_get_errors() as $error) {
            error_log($error->message);
        }
    }
    $data = json_decode(json_encode($data), TRUE);
    return $data;
}

function addRecord($data, $servername, $username, $password, $dbname, $table ){
    $conn = mysqli_connect($servername, $username, $password, $dbname);
    if (!$conn) throw new Exception("Connection failed: " . mysqli_connect_error());

    $sql  = "INSERT INTO $table";
    $sql .= " (`".implode("`, `", array_keys($data))."`)";
    $sql .= " VALUES ('".implode("', '", $data)."') ";

    if (!mysqli_query($conn, $sql)) throw new Exception("Error: " . $sql . "<br>" . mysqli_error($conn));

    mysqli_close($conn);
}