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

ini_set('max_execution_time', 300);

if (!isset($_SESSION["mikhmon"])) {
	header("Location:../admin.php?id=login");
} else {
	$qpid = $_GET['qpid'];
	$rem = $_GET['remove'];
	$charup = array(
		"lower" => "abcd",
		"upper" => " ABCD",
		"upplow" => " aBcD",
		"mix" => " 5ab2c34d",
		"mix1" => " 5AB2C34D",
		"mix2" => "5aB2c34D",
	);

	$charvc = array(
		"lower" => " abcd2345",
		"upper" => " ABCD2345",
		"upplow" => " aBcD2345",
		"num" => " 1234",
	);

if(isset($qpid) && isset($rem)){
	$API->comm("/system/script/remove", array(
		".id" => "$qpid",
));
echo '<script>window.location.reload()</script>';
}	

	// get quick print
$getquickprint = $API->comm("/system/script/print", array("?.id" => "$qpid"));
  $quickprintdetails = $getquickprint[0];
  $qpid = $quickprintdetails['.id'];
  $quickprintsource = explode("#",$quickprintdetails['source']);
  $package = $quickprintsource[1];
  $server = $quickprintsource[2];
  $usermode = $quickprintsource[3];
  $userlength = $quickprintsource[4];
  $prefix = $quickprintsource[5];
  $char = $quickprintsource[6];
  $profile = $quickprintsource[7];
  $timelimit = $quickprintsource[8];
  $datalimit = $quickprintsource[9];
	$comment = $quickprintsource[10];
	if($usermode == "up"){
		$tusermode =  $_user_pass;
		$tchar = $charup[$char];
	}elseif($usermode == "vc"){
		$tusermode =  $_user_user;
		$tchar = $charvc[$char];
	}
	if (substr(formatBytes2($datalimit, 2), -2) == "MB") {
		$udatalimit = $datalimit / 1048576;
		$xdatalimit = 1048576;
    $MG = "MB";
  } elseif (substr(formatBytes2($datalimit, 2), -2) == "GB") {
		$udatalimit = $datalimit / 1073741824;
		$xdatalimit = 1073741824;
    $MG = "GB";
  } else{
		$udatalimit = "";
		$xdatalimit = 1048576;
    $MG = "MB";
  }

	// array color
    $color = array('1' => 'bg-blue', 'bg-indigo', 'bg-purple', 'bg-pink', 'bg-red', 'bg-yellow', 'bg-green', 'bg-teal', 'bg-cyan', 'bg-grey', 'bg-light-blue');

    $srvlist = $API->comm("/ip/hotspot/print");
    $getprofile = $API->comm("/ip/hotspot/user/profile/print");
	

	if (isset($_POST['name'])) {
        $name = ($_POST['name']);
        $sname = "Quick_Print_".(preg_replace('/\s+/', '-', $_POST['name']));
		$server = ($_POST['server']);
		$user = ($_POST['user']);
		$userl = ($_POST['userl']);
		$prefix = ($_POST['prefix']);
		$char = ($_POST['char']);
		$profile = ($_POST['profile']);
		$timelimit = ($_POST['timelimit']);
		$datalimit = ($_POST['datalimit']);
		$adcomment = ($_POST['adcomment']);
		$mbgb = ($_POST['mbgb']);
		if ($timelimit == "") {
			$timelimit = "0";
		} else {
			$timelimit = $timelimit;
		}
		if ($datalimit == "") {
			$datalimit = "0";
		} else {
			$datalimit = $datalimit * $mbgb;
		}
		if ($adcomment == "") {
			$adcomment = "";
		} else {
			$adcomment = $adcomment;
		}
		$getprofile = $API->comm("/ip/hotspot/user/profile/print", array("?name" => "$profile"));
		$ponlogin = $getprofile[0]['on-login'];
		$getvalid = explode(",", $ponlogin)[3];
		$getprice = explode(",", $ponlogin)[2];
		$getsprice = explode(",", $ponlogin)[4];
		$getlock = explode(",", $ponlogin)[6];

        $source = '#'.$name.'#'.$server.'#'.$user.'#'.$userl.'#'.$prefix.'#'.$char.'#'.$profile.'#'.$timelimit.'#'.$datalimit.'#'.$adcomment.'#'.$getvalid.'#'.$getprice.'_'.$getsprice.'#'.$getlock;

		if (isset($qpid)){
			$API->comm("/system/script/set", array(
				".id" => "$qpid",
				"name" => "$sname",
				"source" => "$source",
				"comment" => "QuickPrintMikhmon",
		));
		}else{
        $API->comm("/system/script/add", array(
            "name" => "$sname",
            "source" => "$source",
            "comment" => "QuickPrintMikhmon",
				));
			}

		echo "<script>window.location='./?hotspot=list-quick-print&session=" . $session . "'</script>";
		
	}



}
?>
<style>
.qp-layout-row {
    display: flex !important;
    gap: 24px !important;
    width: 100% !important;
    flex-wrap: wrap !important;
    box-sizing: border-box !important;
}
.qp-col-form {
    flex: 1 1 360px !important;
    box-sizing: border-box !important;
    display: flex !important;
    flex-direction: column !important;
}
.qp-col-table {
    flex: 2 2 540px !important;
    box-sizing: border-box !important;
    display: flex !important;
    flex-direction: column !important;
}

/* Form inputs & fields matching premium theme */
.form-field-group {
    display: flex !important;
    flex-direction: column !important;
    gap: 8px !important;
    margin-bottom: 18px !important;
    width: 100% !important;
    box-sizing: border-box;
}
.form-field-group label {
    font-size: 13px !important;
    font-weight: 700 !important;
    color: var(--text-muted) !important;
    text-align: left !important;
}
.form-field-group input.form-control,
.form-field-group select.form-control {
    height: 48px !important;
    border: 1px solid var(--border-color) !important;
    border-radius: 12px !important;
    background: var(--bg-card, #ffffff) !important;
    color: var(--text-main) !important;
    padding: 0 16px !important;
    font-size: 14px !important;
    outline: none !important;
    transition: all 0.25s ease !important;
    box-sizing: border-box !important;
    width: 100% !important;
}
.form-field-group input.form-control:focus,
.form-field-group select.form-control:focus {
    border-color: var(--primary) !important;
    box-shadow: 0 0 0 3.5px var(--primary-glow) !important;
}

/* Group inputs for Data Limit */
.modern-input-group {
    display: flex !important;
    width: 100% !important;
    gap: 8px !important;
    box-sizing: border-box;
}
.modern-input-group input {
    flex: 2 !important;
}
.modern-input-group select {
    flex: 1 !important;
    padding: 0 8px !important;
}

/* Buttons style */
.btn-modern-action {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    height: 40px !important;
    padding: 0 16px !important;
    border-radius: 10px !important;
    font-size: 13px !important;
    font-weight: 700 !important;
    gap: 8px !important;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
    border: 1px solid transparent !important;
    cursor: pointer !important;
    text-decoration: none !important;
}
.btn-modern-action.btn-save {
    background: var(--primary) !important;
    color: #ffffff !important;
    box-shadow: 0 4px 12px rgba(0, 139, 201, 0.2) !important;
}
.btn-modern-action.btn-save:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(0, 139, 201, 0.3) !important;
}
.btn-modern-action.btn-close {
    background: rgba(245, 158, 11, 0.08) !important;
    color: #f59e0b !important;
    border-color: rgba(245, 158, 11, 0.15) !important;
}
.btn-modern-action.btn-close:hover {
    background: #f59e0b !important;
    color: #ffffff !important;
}

/* Table styling override */
.table-responsive-modern {
    border: 1px solid var(--border-color) !important;
    border-radius: 12px !important;
    overflow-x: auto !important;
    box-shadow: 0 2px 8px rgba(0,0,0,0.01) !important;
    background: var(--bg-card, #ffffff) !important;
    width: 100%;
}
.table-modern-qp {
    border-collapse: collapse !important;
    width: 100% !important;
}
.table-modern-qp th {
    background: var(--table-header-bg, #F8FAFC) !important;
    color: var(--text-muted) !important;
    font-weight: 800 !important;
    font-size: 11.5px !important;
    text-transform: uppercase !important;
    letter-spacing: 0.5px !important;
    padding: 14px 16px !important;
    border-bottom: 1px solid var(--border-color) !important;
    text-align: left !important;
}
.table-modern-qp td {
    padding: 12px 16px !important;
    border-bottom: 1px solid var(--border-color) !important;
    color: var(--text-main) !important;
    font-size: 13px !important;
    text-align: left !important;
    vertical-align: middle !important;
}
.table-modern-qp tr:last-child td {
    border-bottom: none !important;
}
.table-modern-qp tr:hover td {
    background: var(--bg-card-hover, rgba(0,0,0,0.01)) !important;
}

/* Action link buttons inside table */
.qp-action-icon {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    width: 28px !important;
    height: 28px !important;
    border-radius: 8px !important;
    cursor: pointer !important;
    transition: all 0.2s !important;
    text-decoration: none !important;
    border: 1px solid transparent !important;
}
.qp-action-icon.qp-edit {
    background: rgba(14, 165, 233, 0.08) !important;
    color: #0ea5e9 !important;
}
.qp-action-icon.qp-edit:hover {
    background: #0ea5e9 !important;
    color: #ffffff !important;
}
.qp-action-icon.qp-delete {
    background: rgba(239, 68, 68, 0.08) !important;
    color: #ef4444 !important;
}
.qp-action-icon.qp-delete:hover {
    background: #ef4444 !important;
    color: #ffffff !important;
}
</style>

<div class="qp-layout-row">
    
    <!-- Left Column: Add / Edit Quick Print -->
    <div class="qp-col-form">
        <div class="card" style="box-shadow: var(--shadow-card); border-radius: var(--radius); border: 1px solid var(--border-color); height: 100%;">
            <div class="card-header" style="padding: 14px 20px !important;">
                <h3 class="card-title"><i class="fa fa-ticket"></i> <?php if(isset($qpid)){echo $_edit;}else{echo $_add;} echo ' '. $_quick_print ?> <small id="loader" style="display: none;" ><i><i class='fa fa-circle-o-notch fa-spin'></i> <?= $_processing ?> </i></small></h3> 
            </div>
            <div class="card-body" style="padding: 24px !important;">
                <form autocomplete="off" method="post" action="">
                    
                    <div class="form-field-group">
                        <label for="name"><?= $_name ?></label>
                        <input class="form-control" type="text" id="name" name="name" value="<?= $package ?>" required="1" placeholder="Nama Paket Quick Print">
                    </div>

                    <div class="form-field-group">
                        <label for="server">Server</label>
                        <select class="form-control" name="server" id="server" required="1">
                            <?php if(isset($qid)){echo '<option>'. $server .'</option>';}else{echo '<option>all</option>';} ?>
                            <?php $TotalReg = count($srvlist);
                            for ($i = 0; $i < $TotalReg; $i++) {
                                echo "<option>" . $srvlist[$i]['name'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-field-group">
                        <label for="user"><?= $_user_mode ?></label>
                        <select class="form-control" onchange="defUserl();" id="user" name="user" required="1">
                            <?php if(isset($qpid)){echo '<option value="'.$usermode.'">'.$tusermode.'</option>';}?>
                            <option value="up"><?= $_user_pass ?></option>
                            <option value="vc"><?= $_user_user ?></option>
                        </select>
                    </div>

                    <div class="form-field-group">
                        <label for="userl"><?= $_user_length ?></label>
                        <select class="form-control" id="userl" name="userl" required="1">
                            <?php if(isset($qpid)){echo '<option>'.$userlength.'</option>';}?>
                            <option>4</option>
                            <option>3</option>
                            <option>4</option>
                            <option>5</option>
                            <option>6</option>
                            <option>7</option>
                            <option>8</option>
                        </select>
                    </div>

                    <div class="form-field-group">
                        <label for="prefix"><?= $_prefix ?></label>
                        <input class="form-control" type="text" id="prefix" maxlength="4" autocomplete="off" name="prefix" value="<?= $prefix ?>" placeholder="Contoh: CS">
                    </div>

                    <div class="form-field-group">
                        <label for="char"><?= $_character ?></label>
                        <select class="form-control" name="char" id="char" required="1">
                            <?php if(isset($qpid)){echo '<option value="'.$char.'">'.$_random.' '.$tchar.'</option>';}?>
                            <option id="lower" style="display:block;" value="lower"><?= $_random ?> abcd</option>
                            <option id="upper" style="display:block;" value="upper"><?= $_random ?> ABCD</option>
                            <option id="upplow" style="display:block;" value="upplow"><?= $_random ?> aBcD</option>
                            <option id="lower1" style="display:none;" value="lower"><?= $_random ?> abcd2345</option>
                            <option id="upper1" style="display:none;" value="upper"><?= $_random ?> ABCD2345</option>
                            <option id="upplow1" style="display:none;" value="upplow"><?= $_random ?> aBcD2345</option>
                            <option id="mix" style="display:block;" value="mix"><?= $_random ?> 5ab2c34d</option>
                            <option id="mix1" style="display:block;" value="mix1"><?= $_random ?> 5AB2C34D</option>
                            <option id="mix2" style="display:block;" value="mix2"><?= $_random ?> 5aB2c34D</option>
                            <option id="num" style="display:none;" value="num"><?= $_random ?> 1234</option>
                        </select>
                    </div>

                    <div class="form-field-group">
                        <label for="uprof"><?= $_profile ?></label>
                        <select class="form-control" onchange="GetVP();" id="uprof" name="profile" required="1">
                            <?php if (isset($qpid)) {
                                echo "<option>" . $profile . "</option>";
                            }
                            $TotalReg = count($getprofile);
                            for ($i = 0; $i < $TotalReg; $i++) {
                                echo "<option>" . $getprofile[$i]['name'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-field-group">
                        <label for="timelimit"><?= $_time_limit ?></label>
                        <input class="form-control" type="text" id="timelimit" autocomplete="off" name="timelimit" value="<?= $timelimit ?>" placeholder="Contoh: 30d atau 12h">
                    </div>

                    <div class="form-field-group">
                        <label><?= $_data_limit ?></label>
                        <div class="modern-input-group">
                            <input class="form-control" type="number" min="0" max="9999" name="datalimit" value="<?= $udatalimit; ?>" placeholder="Limit">
                            <select class="form-control" name="mbgb" required="1">
                                <?php if(isset($qpid)){echo '<option value="'.$xdatalimit.'">'.$MG.'</option>';}?>
                                <option value=1048576>MB</option>
                                <option value=1073741824>GB</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-field-group">
                        <label for="adcomment"><?= $_comment ?></label>
                        <input class="form-control" type="text" title="No special characters" id="adcomment" autocomplete="off" name="adcomment" value="<?= $comment ?>" placeholder="Komentar script">
                    </div>

                    <div id="GetValidPrice" style="margin-bottom: 20px; font-weight: bold; font-size: 13px; color: var(--text-main); text-align: left;">
                        <?php if ($genprof != "") {
                            echo $ValidPrice;
                        } ?>
                    </div>

                    <div style="display: flex; gap: 8px; margin-top: 24px; border-top: 1px solid var(--border-color); padding-top: 20px;">
                        <button type="submit" name="save" onclick="loader()" class="btn-modern-action btn-save">
                            <i class="fa fa-save"></i> <?= $_save ?>
                        </button>
                        <?php if(isset($qpid)){echo "
                            <a class='btn-modern-action btn-close' href='./?hotspot=list-quick-print&session=".$session."'> <i class='fa fa-close'></i> ".$_cancel."</a>";
                        }else{
                            echo "<a class='btn-modern-action btn-close' href='./?hotspot=quick-print&session=".$session."'> <i class='fa fa-close'></i> ".$_close."</a>";
                        } ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Right Column: Package Quick Print List -->
    <div class="qp-col-table">
        <div class="card" style="box-shadow: var(--shadow-card); border-radius: var(--radius); border: 1px solid var(--border-color);">
            <div class="card-header" style="padding: 14px 20px !important;">
                <h3 class="card-title"><i class="fa fa-ticket"></i> <?= $_package.' '.  $_quick_print ?></h3>
            </div>
            <div class="card-body" style="padding: 24px !important;">
                <div class="table-responsive-modern">
                    <table class="table-modern-qp table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th style="width: 70px; text-align: center;">Aksi</th>	
                                <th><?= $_package ?></th>
                                <th>Server</th>
                                <th><?= $_user_mode ?></th>
                                <th><?= $_user_length ?></th>
                                <th><?= $_prefix ?></th>
                                <th><?= $_profile ?></th>
                                <th><?= $_time_limit ?></th>
                                <th><?= $_data_limit ?></th>
                                <th><?= $_validity ?></th>
                                <th><?= $_price ?></th>
                                <th><?= $_selling_price ?></th>
                                <th><?= $_lock_user ?></th>
                                <th><?= $_comment ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // get quick print
                            $getquickprint = $API->comm("/system/script/print", array("?comment" => "QuickPrintMikhmon"));
                            $TotalReg = count($getquickprint);
                            for ($i = 0; $i < $TotalReg; $i++) {
                              $quickprintdetails = $getquickprint[$i];
                              $qpid = $quickprintdetails['.id'];
                              $quickprintsource = explode("#",$quickprintdetails['source']);
                              $package = $quickprintsource[1];
                              $server = $quickprintsource[2];
                              $usermode = $quickprintsource[3];
                              $userlength = $quickprintsource[4];
                              $prefix = $quickprintsource[5];
                              $char = $quickprintsource[6];
                              $profile = $quickprintsource[7];
                              $timelimit = $quickprintsource[8];
                              $datalimit = $quickprintsource[9];
                              $comment = $quickprintsource[10];
                              $validity = $quickprintsource[11];
                              $getprice = explode("_",$quickprintsource[12])[0];
                              $getsprice = explode("_",$quickprintsource[12])[1];
                              $userlock = $quickprintsource[13];
                              if ($currency == in_array($currency, $cekindo['indo'])) {
                                $price = $currency . " " . number_format($getprice, 0, ",", ".");
                                $sprice = $currency . " " . number_format($getsprice, 0, ",", ".");
                              } else {
                                $price = $currency . " " . number_format($getprice);
                                $sprice = $currency . " " . number_format($getsprice);
                              }
                            ?>
                            <tr>
                                <td style="text-align: center; white-space: nowrap;">
                                    <a class="qp-action-icon qp-edit" href="./?hotspot=list-quick-print&qpid=<?= $qpid; ?>&session=<?= $session; ?>" title="Edit <?= $package; ?>">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <a class="qp-action-icon qp-delete" href="javascript:void(0)" onclick="if(confirm('Are you sure to delete (<?= $package; ?>)?')){loadpage('./?hotspot=list-quick-print&remove&qpid=<?= $qpid; ?>&session=<?= $session; ?>')}else{}" title="Remove <?= $package; ?>">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                </td>	
                                <td><strong><?= $package; ?></strong></td>
                                <td><?= $server ?></td>
                                <td><?= $usermode ?></td>
                                <td><?= $userlength ?></td>
                                <td><?= $prefix ?: "-" ?></td>
                                <td><?= $profile ?></td>
                                <td><?= $timelimit ?: "-" ?></td>
                                <td><?= formatBytes($datalimit, 2) ?></td>
                                <td><?= $validity ?: "-" ?></td>
                                <td><span style="font-weight: 700; color: var(--primary);"><?= $price ?></span></td>
                                <td><span style="font-weight: 700; color: var(--success);"><?= $sprice ?></span></td>
                                <td><?= $userlock ?: "-" ?></td>
                                <td><?= $comment ?: "-" ?></td>
                            </tr>
                            <?php 
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// get valid $ price
function GetVP(){
  var prof = document.getElementById('uprof').value;
  var url = "./process/getvalidprice.php?name=";
  var session = "&session=<?= $session; ?>"
  var getvalidprice = url+prof+session
  $("#GetValidPrice").load(getvalidprice);
}
</script>
</div>
