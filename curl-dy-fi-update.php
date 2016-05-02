

<?php

$externalContent = file_get_contents('http://checkip.dy.fi/');
preg_match('/\b(?:\d{1,3}\.){3}\d{1,3}\b/', $externalContent, $m);
$currIp = $m[0];

$ip = getIpFromFile();
if ($ip != null)  {
    if ($currIp != $ip ) {

    }
} else  {
    updateIpToFile($ip);
}

echo $currIp;


function updateIpToFile ($newIp)
{
    $filename = $_SERVER['HOME'] . '/.check-dy-update.ip';
    file_put_contents ($filename, $newIp);
}

function getIpFromFile ()
{
    $ip = null;
    $filename = $_SERVER['HOME'] . '/.check-dy-update.ip';
    if (file_exists($filename))  {
        $ip = file ($filename);
    } else {
        $ip = null;
    }
    return $ip;
}