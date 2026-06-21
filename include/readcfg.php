<?php
/*
 *  Copyright (C) 2018 Laksamadi Guko.
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
session_start();
// hide all error
error_reporting(0);
if (substr($_SERVER["REQUEST_URI"], -11) == "readcfg.php") {
    header("Location:./");
};
// read config

// Validate session parameter against registered sessions if provided
if (!empty($session)) {
  if (!isset($data[$session]) || $session === 'mikhmon') {
    if (isset($_SESSION["mikhmon"])) {
      header("Location:./admin.php?id=sessions");
    } else {
      header("Location:./admin.php?id=login");
    }
    exit;
  }
}

$useradm = isset($data['mikhmon'][1]) ? explode('<|<', $data['mikhmon'][1])[1] : '';
$passadm = isset($data['mikhmon'][2]) ? explode('>|>', $data['mikhmon'][2])[1] : '';

if (!empty($session) && isset($data[$session]) && $session !== 'mikhmon') {
  $iphost = isset($data[$session][1]) ? explode('!', $data[$session][1])[1] : '';
  $userhost = isset($data[$session][2]) ? explode('@|@', $data[$session][2])[1] : '';
  $passwdhost = isset($data[$session][3]) ? explode('#|#', $data[$session][3])[1] : '';
  $hotspotname = isset($data[$session][4]) ? explode('%', $data[$session][4])[1] : '';
  $dnsname = isset($data[$session][5]) ? explode('^', $data[$session][5])[1] : '';
  $currency = isset($data[$session][6]) ? explode('&', $data[$session][6])[1] : '';
  $areload = isset($data[$session][7]) ? explode('*', $data[$session][7])[1] : '';
  $iface = isset($data[$session][8]) ? explode('(', $data[$session][8])[1] : '';
  $infolp = isset($data[$session][9]) ? explode(')', $data[$session][9])[1] : '';
  $idleto = isset($data[$session][10]) ? explode('=', $data[$session][10])[1] : '';
  $sesname = isset($data[$session][10]) ? explode('+', $data[$session][10])[1] : '';
  $livereport = isset($data[$session][11]) ? explode('@!@', $data[$session][11])[1] : '';
}

$cekindo['indo'] = array(
    'RP', 'Rp', 'rp', 'IDR', 'idr', 'RP.', 'Rp.', 'rp.', 'IDR.', 'idr.',
);


