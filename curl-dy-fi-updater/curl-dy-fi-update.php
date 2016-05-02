<?php

// Copyright (c) 2016 Olli-Pekka Wallin <opwallin@gmail.com>

// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 2 of the License, or
// (at your option) any later version.
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// You should have received a copy of the GNU General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.

$latestUpdateTime = null;

if ($argc != 4 ) {
    echo 'Invalid count of parameters' . PHP_EOL;
    echo 'basic usage is : curl-dy-fi-update.php username password' . PHP_EOL;
    echo ''  . PHP_EOL ;
    die(1);
}

$externalContent = file_get_contents('http://checkip.dy.fi/');
preg_match('/\b(?:\d{1,3}\.){3}\d{1,3}\b/', $externalContent, $m);
$currIp = $m[0];
$ip = getIpFromFile();
if ($currIp != $ip[0] ) {
    // update ip address to the dy.fi
    $userName = $argv[1];
    $password = $argv[2];
    $domain = $argv[3];
    updateDomainDNSRecords ($userName, $password, $domain);
    updateIpToFile($currIp);
}

echo $currIp;
exit (0);

function updateDomainDNSRecords ($user, $password, $domain)
{
    $rs = curl_init("http://dy.fi");
    curl_close($rs);
}

function updateIpToFile ($newIp)
{
    $filename = $_SERVER['HOME'] . '/.check-dy-update.ip';
    file_put_contents ($filename, $newIp);
}

function getIpFromFile ()
{
    $ip = null;
    global $latestUpdateTime;
    $filename = $_SERVER['HOME'] . '/.check-dy-update.ip';
    if (file_exists($filename))  {
        $latestUpdateTime = filemtime ($filename);
        $ip = file ($filename);
    } else {
        $ip = null;
    }
    return $ip;
}