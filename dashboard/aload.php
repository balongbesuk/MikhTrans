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
if (!isset($_SESSION["mikhmon"])) {
  header("Location:../admin.php?id=login");
} else {
// load session MikroTik
  $session = $_GET['session'];
  $load = $_GET['load'];

// lang
include('../include/lang.php');
include('../lang/'.$langid.'.php');

// load config
  include('../include/config.php');
  include('../include/readcfg.php');

// routeros api
  include_once('../lib/routeros_api.class.php');
  include_once('../lib/formatbytesbites.php');
  $API = new RouterosAPI();
  $API->debug = false;



  if ($load == "sysresource") {

    $API->connect($iphost, $userhost, decrypt($passwdhost));

// get MikroTik system clock
    $getclock = $API->comm("/system/clock/print");
    $clock = $getclock[0];
    $timezone = $getclock[0]['time-zone-name'];
    date_default_timezone_set($timezone);
    $_SESSION[$session.'sdate'] = $clock['date'];

// get system resource MikroTik
    $getresource = $API->comm("/system/resource/print");
    $resource = $getresource[0];

// get routeboard info
    $getrouterboard = $API->comm("/system/routerboard/print");
    $routerboard = $getrouterboard[0];

    // Calculate resource percentages
    $total_mem = isset($resource['total-memory']) ? $resource['total-memory'] : 1;
    if ($total_mem <= 0) $total_mem = 1;
    $mem_pct = round((($total_mem - $resource['free-memory']) / $total_mem) * 100);
    
    $total_hdd = isset($resource['total-hdd-space']) ? $resource['total-hdd-space'] : 1;
    if ($total_hdd <= 0) $total_hdd = 1;
    $hdd_pct = round((($total_hdd - $resource['free-hdd-space']) / $total_hdd) * 100);

    if ($livereport == "disable") {
      $col_class = "col-4";
    } else {
      $col_class = "col-3";
    }
    ?>
    
    <div id="r_1" style="display: contents;">
      <!-- Card 1: System Resources (CPU & RAM) -->
      <div class="<?= $col_class ?>">
        <div class="box bmh-75 box-bordered">
          <div class="box-group">
            <div class="box-group-icon icon-cyan"><i class="fa fa-microchip"></i></div>
            <div class="box-group-area">
              <div class="stat-title">System Resources</div>
              <div class="sys-metric">
                <div class="metric-info">
                  <span>CPU <strong id="cpuVal" data-val="<?= $resource['cpu-load'] ?>"><?= $resource['cpu-load'] ?>%</strong></span>
                  <canvas id="cpuSparkline" width="60" height="14"></canvas>
                </div>
                <div class="metric-bar"><div class="metric-bar-fill" style="width: <?= $resource['cpu-load'] ?>%;"></div></div>
              </div>
              <div class="sys-metric" style="margin-top: 8px;">
                <div class="metric-info">
                  <span>RAM <strong id="memVal" data-val="<?= $mem_pct ?>"><?= $mem_pct ?>%</strong></span>
                  <canvas id="memorySparkline" width="60" height="14"></canvas>
                </div>
                <div class="metric-bar"><div class="metric-bar-fill bg-emerald" style="width: <?= $mem_pct ?>%;"></div></div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- Card 2: Storage & Memory -->
      <div class="<?= $col_class ?>">
        <div class="box bmh-75 box-bordered">
          <div class="box-group">
            <div class="box-group-icon icon-violet"><i class="fa fa-hdd-o"></i></div>
            <div class="box-group-area">
              <div class="stat-title">Storage & Memory</div>
              <div class="stat-main-val"><?= formatBytes($resource['free-memory'], 1) ?> <span style="font-size:12px;color:#6b7280;font-weight:600;">free RAM</span></div>
              <div class="sys-metric" style="margin-top: 6px;">
                <div class="metric-info">
                  <span>HDD <?= $hdd_pct ?>%</span>
                  <span style="color:#6b7280;"><?= formatBytes($resource['free-hdd-space'], 1) ?> free</span>
                </div>
                <div class="metric-bar"><div class="metric-bar-fill bg-amber" style="width: <?= $hdd_pct ?>%;"></div></div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- Card 3: System Date & Time (Moved to the Right) -->
      <div class="<?= $col_class ?>">
        <div class="box bmh-75 box-bordered">
          <div class="box-group">
            <div class="box-group-icon icon-indigo"><i class="fa fa-calendar"></i></div>
            <div class="box-group-area">
              <div class="stat-title"><?= $_system_date_time ?></div>
              <div class="stat-main-val"><?= $clock['time'] ?></div>
              <div class="stat-sub-val"><?= ucfirst($clock['date']) ?> &bull; Uptime: <?= formatDTM($resource['uptime']) ?></div>
            </div>
          </div>
        </div>
      </div>
    </div>

<?php 
} else if ($load == "hotspot") {

  $API->connect($iphost, $userhost, decrypt($passwdhost));
// get & counting hotspot users
  $countallusers = $API->comm("/ip/hotspot/user/print", array("count-only" => ""));
  if ($countallusers < 2) {
    $uunit = "item";
  } elseif ($countallusers > 1) {
    $uunit = "items";
  }

// get & counting hotspot active
  $counthotspotactive = $API->comm("/ip/hotspot/active/print", array("count-only" => ""));
  if ($counthotspotactive < 2) {
    $hunit = "item";
  } elseif ($counthotspotactive > 1) {
    $hunit = "items";
  }

  ?>
    
        <div id="r_2" class="card card-hotspot-panel">
          <div class="card-header">
            <h3><i class="fa fa-wifi"></i> Hotspot Overview</h3>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-3 col-box-6">
                <div class="box bg-blue-modern">
                  <a onclick="cancelPage()" href="./?hotspot=active&session=<?= $session; ?>">
                    <div class="action-card-icon"><i class="fa fa-laptop"></i></div>
                    <div class="action-card-val"><?= $counthotspotactive; ?></div>
                    <div class="action-card-label"><?= $_hotspot_active ?></div>
                  </a>
                </div>
              </div>
              <div class="col-3 col-box-6">
                <div class="box bg-green-modern">
                  <a onclick="cancelPage()" href="./?hotspot=users&profile=all&session=<?= $session; ?>">
                    <div class="action-card-icon"><i class="fa fa-users"></i></div>
                    <div class="action-card-val"><?= $countallusers; ?></div>
                    <div class="action-card-label"><?= $_hotspot_users ?></div>
                  </a>
                </div>
              </div>
              <div class="col-3 col-box-6">
                <div class="box bg-yellow-modern">
                  <a onclick="cancelPage()" href="./?hotspot-user=add&session=<?= $session; ?>">
                    <div class="action-card-icon"><i class="fa fa-user-plus"></i></div>
                    <div class="action-card-val"><i class="fa fa-plus"></i></div>
                    <div class="action-card-label">Add User</div>
                  </a>
                </div>
              </div>
              <div class="col-3 col-box-6">
                <div class="box bg-red-modern">
                  <a onclick="cancelPage()" href="./?hotspot-user=generate&session=<?= $session; ?>">
                    <div class="action-card-icon"><i class="fa fa-ticket"></i></div>
                    <div class="action-card-val"><i class="fa fa-magic"></i></div>
                    <div class="action-card-label">Generate</div>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>

<?php 
} else if ($load == "logs") {

  $API->connect($iphost, $userhost, decrypt($passwdhost));

  // move hotspot log to disk
  $getlogging = $API->comm("/system/logging/print", array("?prefix" => "->", ));
  $logging = $getlogging[0];
  if ($logging['prefix'] == "->") {
  } else {
    $API->comm("/system/logging/add", array("action" => "disk", "prefix" => "->", "topics" => "hotspot,info,debug", ));
  }
  
  // get hotspot log
  $getlog = $API->comm("/log/print", array("?topics" => "hotspot,info,debug", ));
  $log = array_reverse($getlog);

  if ($livereport == "disable") {
    $logh = "457px";
    $lreport = "style='display:none;'";
  } else {
    $logh = "350px";
    $lreport = "style='display:block;'";
  }



  ?>
  
              <div id="r_3" class="row">
              <div class="card">
                <div class="card-header">
                  <h3><a href="./?hotspot=log&session=<?= $session; ?>" title="Open Hotspot Log" ><i class="fa fa-align-justify"></i> <?= $_hotspot_log ?></a></h3></div>
                    <div class="card-body">
                      <div style="padding: 0; height: <?= $logh; ?>;" class="mr-t-10 overflow">
                        <table class="table table-sm table-hover card-table-modern" style="font-size: 12px;">
                          <thead>
                            <tr>
                            <th><?= $_time; ?></th>
                            <th><?= $_users ?> (IP)</th>
                            <th><?= $_messages ?></th>
                            </tr>
                          </thead>
                          <tbody>
                      
  <?php


  for ($i = 0; $i < 20; $i++) {
    $mess = explode(":", $log[$i]['message']);
    $time = $log[$i]['time'];
    echo "<tr>";
    if (substr($log[$i]['message'], 0, 2) == "->") {
      // Column 1: Time
      echo "<td>";
      if (strpos($time, ' ') !== false) {
          $parts = explode(' ', $time);
          $date_parts = explode('/', $parts[0]);
          $day = $date_parts[1];
          $month = ucfirst($date_parts[0]);
          echo "<div style='display: flex; flex-direction: column; align-items: flex-start; gap: 2px;'>";
          echo "  <span style='font-size: 9px; font-weight: 700; color: var(--text-muted); text-transform: uppercase;'>$day $month</span>";
          echo "  <span style='font-size: 11px; font-weight: 500; color: var(--text-muted); font-variant-numeric: tabular-nums;'>$parts[1]</span>";
          echo "</div>";
      } else {
          echo "<span style='font-size: 11px; font-weight: 500; color: var(--text-muted); font-variant-numeric: tabular-nums;'>$time</span>";
      }
      echo "</td>";

      // Column 2: User & IP
      echo "<td>";
      $user_ip_str = (count($mess) > 6) ? ($mess[1] . ":" . $mess[2] . ":" . $mess[3] . ":" . $mess[4] . ":" . $mess[5] . ":" . $mess[6]) : $mess[1];
      $user_ip_str = trim($user_ip_str);
      
      $user = $user_ip_str;
      $ip = '';
      if (preg_match('/^(.*?)\s*\((.*?)\)$/', $user_ip_str, $matches)) {
          $user = trim($matches[1]);
          $ip = trim($matches[2]);
      }
      
      echo "<div style='display: flex; flex-direction: column; gap: 2px;'>";
      echo "  <span style='font-weight: 700; color: var(--text-bright); font-size: 12.5px;'>" . htmlspecialchars($user) . "</span>";
      if (!empty($ip)) {
          echo "  <span style='font-weight: 500; color: var(--text-muted); font-size: 11px;'>" . htmlspecialchars($ip) . "</span>";
      }
      echo "</div>";
      echo "</td>";

      // Column 3: Action
      echo "<td>";
      if (count($mess) > 6) {
        $action_msg = $mess[7] . " " . $mess[8] . " " . $mess[9] . " " . $mess[10];
      } else {
        $action_msg = $mess[2] . " " . $mess[3] . " " . $mess[4] . " " . $mess[5];
      }
      $action_msg = trim(str_replace("trying to", "", $action_msg));
      $msg_lower = strtolower($action_msg);
      
      $status_html = '';
      if (strpos($msg_lower, 'logged in') !== false) {
          $status_html = '<span class="log-status success"><i class="fa fa-sign-in"></i> Logged In</span>';
      } elseif (strpos($msg_lower, 'logged out') !== false) {
          $status_html = '<span class="log-status danger"><i class="fa fa-sign-out"></i> Logged Out</span>';
      } elseif (strpos($msg_lower, 'trying') !== false || strpos($msg_lower, 'login') !== false) {
          if (strpos($msg_lower, 'failed') !== false) {
              $status_html = '<span class="log-status warning"><i class="fa fa-warning"></i> Failed</span>';
          } else {
              $status_html = '<span class="log-status info"><i class="fa fa-spinner fa-spin"></i> Trying...</span>';
          }
      } elseif (strpos($msg_lower, 'timeout') !== false) {
          $status_html = '<span class="log-status dark"><i class="fa fa-clock-o"></i> Timeout</span>';
      } else {
          $status_html = '<span class="log-status default"><i class="fa fa-info-circle"></i> ' . htmlspecialchars($action_msg) . '</span>';
      }
      echo $status_html;
      echo "</td>";
    } else {
    }
    echo "</tr>";
  }
  ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
                </div>

<?php 
}

}

?>
