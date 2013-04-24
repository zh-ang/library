<?php

$strPath = dirname(__FILE__)."/logs";

$strTag = date("Ymd_His_", $_SERVER["REQUEST_TIME"]).uniqid();
$strFile = $strPath."/".$strTag.".request";
$fp = fopen($strFile, "w");
fprintf($fp, "%s %s %s\r\n", $_SERVER["REQUEST_METHOD"], $_SERVER["REQUEST_URI"], $_SERVER["SERVER_PROTOCOL"]);
foreach (apache_request_headers() as $key => $value) {
    fprintf($fp, "%s: %s\r\n", $key, $value);
}
fwrite($fp, "\r\n");
fwrite($fp, file_get_contents("php://input"));
fclose($fp);

header("HTTP/1.1 404 Not Found");
echo <<<HTML
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<html><head>
<title>404 Not Found</title>
</head><body>
<h1>Not Found</h1>
<p>The requested URL {$_SERVER["REQUEST_URI"]} was not found on this server.</p>
</body></html>
HTML;
