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

$latestUpdateDate = null;
$fileName = $_SERVER['HOME'] . '/.check-dy-update.ip';

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

$now = time ();

$dateDiff = $now - $latestUpdateDate;
$lastUpdate = floor($dateDiff/(60*60*24));

if ($currIp != $ip[0] && $lastUpdate > 4) {
    // update ip address to the dy.fi
    updateDomainDNSRecords ($argv[1], $argv[2], $argv[3]);
    updateIpToFile($currIp);
}

exit (0);

function updateDomainDNSRecords ($user, $password, $domain)
{
    global $fileName;
    $rs = curl_init("https://dy.fi/nic/update?hostname=$domain");
    curl_setopt($rs, CURLOPT_USERPWD, "$user:$password");
    curl_exec($rs);
    $info = curl_getinfo($rs, CURLINFO_HTTP_CODE);
    if ($info == 200)  {
        touch ($fileName);
    }
    echo "https response code : $info";
    curl_close($rs);
}

function updateIpToFile ($newIp)
{
    global $fileName;
    file_put_contents ($fileName, $newIp);
}

function getIpFromFile ()
{
    $ip = null;
    global $fileName;
    global $latestUpdateDate;
    if (file_exists($fileName))  {
        $latestUpdateDate = filemtime($fileName);
        $ip = file ($fileName);
    } else {
        $ip = null;
    }
    return $ip;
}