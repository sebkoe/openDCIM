<?php
	require_once( 'db.inc.php' );
	require_once( 'facilities.inc.php' );

	$user = new User();

	$user->UserID = $_SERVER['REMOTE_USER'];
	$user->GetUserRights( $facDB );

	if(!$user->ContactAdmin){
		// No soup for you.
		header('Location: '.redirect());
		exit;
	}

	$period=new EscalationTimes();
	$status='';

	if(isset($_REQUEST['escalationtimeid'])){
		$period->EscalationTimeID=$_REQUEST['escalationtimeid'];
		if(isset($_POST['action'])){
			if($_POST['timeperiod']!=null && $_POST['timeperiod']!=''){
				switch($_POST['action']){
					case 'Create':
						$period->TimePeriod=$_POST['timeperiod'];
						$period->CreatePeriod($facDB);
						break;
					case 'Update':
						$period->TimePeriod=$_POST['timeperiod'];
						$status='Updated';
						$period->UpdatePeriod($facDB);
						break;
					case 'Delete':
						$period->DeletePeriod($facDB);
						header('Location: '.redirect("timeperiods.php"));
						exit;
				}
			}
		}
		$period->GetEscalationTime($facDB);
	}
	$periodList=$period->GetEscalationTimeList($facDB);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>openDCIM Data Center Inventory</title>
  <link rel="stylesheet" href="css/inventory.php" type="text/css">
  <!--[if lt IE 9]>
  <link rel="stylesheet"  href="css/ie.css" type="text/css">
  <![endif]-->
  <script type="text/javascript" src="scripts/jquery.min.js"></script>
</head>
<body>
<div id="header"></div>
<div class="page">
<?php
	include( 'sidebar.inc.php' );
?>
<div class="main">
<h2><?php echo $config->ParameterArray['OrgName']; ?></h2>
<h3>Data Center Time Periods Listing</h3>
<h3><?php echo $status; ?></h3>
<div class="center"><div>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
<div class="table">
<div>
   <div><label for="escalationtimeid">Escalation Time Period</label></div>
   <div><input type="hidden" name="action" value="query"><select name="escalationtimeid" id="escalationtimeid" onChange="form.submit()">
   <option value=0>New Time Period</option>
<?php
	foreach( $periodList as $periodRow ) {
		print_r($periodRow);
		if($period->EscalationTimeID == $periodRow->EscalationTimeID){$selected=' selected';}else{$selected="";}
		print "<option value=\"$periodRow->EscalationTimeID\"$selected>$periodRow->TimePeriod</option>\n";
	}
?>
	</select></div>
</div>
<div>
   <div><label for="timeperiod">Description</label></div>
   <div><input type="text" name="timeperiod" id="timeperiod" size="80" value="<?php echo $period->TimePeriod; ?>"></div>
</div>
<div class="caption">
<?php
	if($period->EscalationTimeID >0){
		echo '   <input type="submit" name="action" value="Update">
	 <input type="submit" name="action" value="Delete">';
	}else{
		echo '	 <input type="submit" name="action" value="Create">';
	}
?>
</div>
</div> <!-- END div.table -->
</form>
</div></div>
<a href="index.php">[ Return to Main Menu ]</a>
</div><!-- END div.main -->
</div><!-- END div.page -->
</body>
</html>
