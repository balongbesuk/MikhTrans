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

// hide all error
error_reporting(0);
if (!isset($_SESSION["mikhmon"])) {
  header("Location:../admin.php?id=login");
} else {
  ?>
<div class="row">
<div class="col-12">
<div class="card">
<div class="card-header">
	<h3><i class=" fa fa-users"></i> <?= $_vouchers ?> &nbsp;&nbsp; | &nbsp;&nbsp;<i onclick="location.reload();" class="fa fa-refresh pointer" title="Reload data"></i></h3>
</div>
<div class="card-body">
<div class="overflow" style="max-height: 80vh; padding: 12px;">	
<div class="row" style="display: flex; flex-wrap: wrap; gap: 0;">	
      <div class="col-4" style="margin-bottom: 20px;">
        <div class="box bmh-75 box-bordered voucher-card">
          <div class="box-group">
            <div class="box-group-icon icon-voucher">
              <a title='Open User by profile all' href='./?hotspot=users&profile=all&session=<?= $session; ?>'>
              <i class="fa fa-ticket"></i></a>
            </div>
            <div class="box-group-area">
              <div class="voucher-profile-title">Profile: all</div>
              <div class="voucher-profile-count">
                <?php $countuser = $API->comm("/ip/hotspot/user/print", array("count-only" => ""));
                if ($countuser < 2) {
                  echo $countuser . " Item";
                } elseif ($countuser > 1) {
                  echo $countuser . " Items";
                }
                ?>
              </div>
              <div class="voucher-profile-actions">
                <a title="Open User by profile all" href="./?hotspot=users&profile=all&session=<?= $session; ?>"><i class="fa fa-external-link"></i> <?= $_open ?></a>
                <a title="Generate User by profile all" href="./?hotspot-user=generate&session=<?= $session; ?>"><i class="fa fa-users"></i> <?= $_generate ?></a>
              </div>
            </div>
          </div>
        </div>
      </div>
<?php
// get user profile
$getprofile = $API->comm("/ip/hotspot/user/profile/print");
$TotalReg = count($getprofile);
for ($i = 0; $i < $TotalReg; $i++) {
  $profiledetalis = $getprofile[$i];
  $pname = $profiledetalis['name'];
  ?>
	     <div class="col-4" style="margin-bottom: 20px;">
        <div class="box bmh-75 box-bordered voucher-card">
          <div class="box-group">
            <div class="box-group-icon icon-voucher">
              <a title='Open User by profile <?= $pname; ?>' href='./?hotspot=users&profile=<?= $pname; ?>&session=<?= $session; ?>'>
            	<i class="fa fa-ticket"></i></a>
            </div>
            <div class="box-group-area">
              <div class="voucher-profile-title">Profile: <?= htmlspecialchars($pname); ?></div>
              <div class="voucher-profile-count">
                <?php	$countuser = $API->comm("/ip/hotspot/user/print", array("count-only" => "", "?profile" => "$pname", ));
                if ($countuser < 2) {
                  echo $countuser . " Item";
                } elseif ($countuser > 1) {
                  echo $countuser . " Items";
                }
                ?>
              </div>
              <div class="voucher-profile-actions">
                <a title="Open User by profile <?= $pname; ?>" href="./?hotspot=users&profile=<?= $pname; ?>&session=<?= $session; ?>"><i class="fa fa-external-link"></i> <?= $_open ?></a>
                <a title="Generate User by profile <?= $pname; ?>" href="./?hotspot-user=generate&genprof=<?= $pname; ?>&session=<?= $session; ?>"><i class="fa fa-users"></i> <?= $_generate ?></a>
              </div>
            </div>
          </div>
        </div>
      </div>
      <?php 
    }
  } ?>
      </div>
    </div>
</div>
</div>
</div>
</div>