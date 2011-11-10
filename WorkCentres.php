<?php
/* $Revision: 1.11 $ */

$PageSecurity=9;

include('includes/session.inc');
$title = _('Work Centres');
include('includes/header.inc');

if (isset($_POST['SelectedWC'])){
	$SelectedWC =$_POST['SelectedWC'];
} elseif (isset($_GET['SelectedWC'])){
	$SelectedWC =$_GET['SelectedWC'];
}

if (isset($_POST['submit'])) {

	//initialise no input errors assumed initially before we test
	$InputError = 0;

	/* actions to take once the user has clicked the submit button
	ie the page has called itself with some user input */

	//first off validate inputs sensible

	if (strlen($_POST['Code']) < 2) {
		$InputError = 1;
		prnMsg(_('The Work Centre code must be at least 2 characters long'),'error');
	} 
	if (strlen($_POST['Description'])<3) {
		$InputError = 1;
		prnMsg(_('The Work Centre description must be at least 3 characters long'),'error');
	}
	if (strstr($_POST['Code'],' ') OR strstr($_POST['Code'],"'") OR strstr($_POST['Code'],'+') OR strstr($_POST['Code'],"\\") OR strstr($_POST['Code'],"\"") OR strstr($_POST['Code'],'&') OR strstr($_POST['Code'],'.') OR strstr($_POST['Code'],'"')) {
		$InputError = 1;
		prnMsg(_('The work centre code cannot contain any of the following characters') . " - ' & + \" \\ " . _('or a space'),'error');
	}

	if (isset($SelectedWC) AND $InputError !=1) {

		/*SelectedWC could also exist if submit had not been clicked this code
		would not run in this case cos submit is false of course  see the
		delete code below*/
		
		$sql = "UPDATE workcentres SET location = '" . $_POST['Location'] . "',
						description = '" . $_POST['Description'] . "',
						overheadrecoveryact =" . $_POST['OverheadRecoveryAct'] . ",
						overheadperhour = " . $_POST['OverheadPerHour'] . "
				WHERE code = '" . $SelectedWC . "'";
		$msg = _('The work centre record has been updated');
	} elseif ($InputError !=1) {

	/*Selected work centre is null cos no item selected on first time round so must be adding a	record must be submitting new entries in the new work centre form */

		$sql = "INSERT INTO workcentres (code,
						location,
						description,
						overheadrecoveryact,
						overheadperhour)
					VALUES ('" . $_POST['Code'] . "',
						'" . $_POST['Location'] . "',
						'" . $_POST['Description'] . "',
						" . $_POST['OverheadRecoveryAct'] . ",
						" . $_POST['OverheadPerHour'] . "
						)";
		$msg = _('The new work centre has been added to the database');
	}
	//run the SQL from either of the above possibilites
	
	if ($InputError !=1){
		$result = DB_query($sql,$db,_('The update/addition of the work centre failed because'));
		prnMsg($msg,'success');
		unset ($_POST['Location']);
		unset ($_POST['Description']);
		unset ($_POST['Code']);
		unset ($_POST['OverheadRecoveryAct']);
		unset ($_POST['OverheadPerHour']);
		unset ($SelectedWC);
	}

} elseif (isset($_GET['delete'])) {
//the link to delete a selected record was clicked instead of the submit button

// PREVENT DELETES IF DEPENDENT RECORDS IN 'BOM'
	
	$sql= "SELECT COUNT(*) FROM bom WHERE bom.workcentreadded='$SelectedWC'";
	$result = DB_query($sql,$db);
	$myrow = DB_fetch_row($result);
	if ($myrow[0]>0) {
		prnMsg(_('Cannot delete this work centre because bills of material have been created requiring components to be added at this work center') . '<BR>' . _('There are') . ' ' . $myrow[0] . ' ' ._('BOM items referring to this work centre code'),'warn');
	}  else {
		$sql= "SELECT COUNT(*) FROM contractbom WHERE contractbom.workcentreadded='$SelectedWC'";
		$result = DB_query($sql,$db);
		$myrow = DB_fetch_row($result);
		if ($myrow[0]>0) {
			prnMsg(_('Cannot delete this work centre because contract bills of material have been created having components added at this work center') . '<BR>' . _('There are') . ' ' . $myrow[0] . ' ' . _('Contract BOM items referring to this work centre code'),'warn');
		} else {
			$sql="DELETE FROM workcentres WHERE code='$SelectedWC'";
			$result = DB_query($sql,$db);
			prnMsg(_('The selected work centre record has been deleted'),'succes');
		} // end of Contract BOM test
	} // end of BOM test
}

if (!isset($SelectedWC)) {

/* It could still be the second time the page has been run and a record has been selected for modification - SelectedWC will exist because it was sent with the new call. If its the first time the page has been displayed with no parameters
then none of the above are true and the list of work centres will be displayed with
links to delete or edit each. These will call the same page again and allow update/input
or deletion of the records*/
	echo '<P class="page_title_text"><img src="'.$rootpath.'/css/'.$theme.'/images/maintenance.png" TITLE="' . _('Search') . '" ALT="">' . ' ' . $title;
	
	$sql = 'SELECT workcentres.code,
			workcentres.description,
			locations.locationname,
			workcentres.overheadrecoveryact,
			workcentres.overheadperhour
		FROM workcentres,
			locations
		WHERE workcentres.location = locations.loccode';

	$result = DB_query($sql,$db);
	echo "<CENTER><table border=1>
		<tr BGCOLOR =#800000><th>" . _('WC Code') . "</th>
				<th>" . _('Description') . "</th>
				<th>" . _('Location') . "</th>
				<th>" . _('Overhead GL Account') . "</th>
				<th>" . _('Overhead Per Hour') . "</th>
		</tr></FONT>";

	while ($myrow = DB_fetch_row($result)) {

		printf("<tr><td>%s</td>
				<td>%s</td>
				<td>%s</td>
				<td>%s</td>
				<td ALIGN=RIGHT>%s</td>
				<td><a href=\"%s&SelectedWC=%s\">" . _('Edit') . "</td>
				<td><a href=\"%s&SelectedWC=%s&delete=yes\">" . _('Delete') ."</td>
				</tr>",
				$myrow[0],
				$myrow[1],
				$myrow[2],
				$myrow[3],
				$myrow[4],
				$_SERVER['PHP_SELF'] . '?' . SID,
				$myrow[0], $_SERVER['PHP_SELF'] . '?' . SID,
				$myrow[0]);
	}

	//END WHILE LIST LOOP
	echo '</TABLE></CENTER>';
}

//end of ifs and buts!

if (isset($SelectedWC)) {
	echo '<P class="page_title_text"><img src="'.$rootpath.'/css/'.$theme.'/images/maintenance.png" TITLE="' . _('Search') . '" ALT="">' . ' ' . $title;
	echo "<CENTER><A HREF='" . $_SERVER['PHP_SELF'] . '?' . SID . "'>" . _('Show all Work Centres') . '</A></CENTER>';
}

echo "<P><FORM METHOD='post' action='" . $_SERVER['PHP_SELF'] . '?' . SID . "'>";

if (isset($SelectedWC)) {
	//editing an existing work centre

	$sql = "SELECT code,
			location,
			description,
			overheadrecoveryact,
			overheadperhour
		FROM workcentres
		WHERE code='$SelectedWC'";

	$result = DB_query($sql, $db);
	$myrow = DB_fetch_array($result);

	$_POST['Code'] = $myrow['code'];
	$_POST['Location'] = $myrow['location'];
	$_POST['Description'] = $myrow['description'];
	$_POST['OverheadRecoveryAct']  = $myrow['overheadrecoveryact'];
	$_POST['OverheadPerHour']  = $myrow['overheadperhour'];

	echo '<INPUT TYPE=HIDDEN NAME=SelectedWC VALUE=' . $SelectedWC . '>';
	echo "<INPUT TYPE=HIDDEN NAME=Code VALUE='" . $_POST['Code'] . "'>";
	echo '<CENTER><TABLE><TR><TD>' ._('Work Centre Code') . ':</TD><TD>' . $_POST['Code'] . '</TD></TR>';

} else { //end of if $SelectedWC only do the else when a new record is being entered
	if (!isset($_POST['Code'])) {
		$_POST['Code'] = '';
	}
	echo '<CENTER><TABLE><TR>
			<TD>' . _('Work Centre Code') . ":</TD>
			<TD><input type='Text' name='Code' SIZE=6 MAXLENGTH=5 value='" . $_POST['Code'] . "'></TD>
			</TR>";
}

$SQL = 'SELECT locationname,
		loccode
	FROM locations';
$result = DB_query($SQL,$db);

if (!isset($_POST['Description'])) {
	$_POST['Description'] = '';
}

echo '<TR><TD>' . _('Work Centre Description') . ":</TD>
	<TD><input type='Text' name='Description' SIZE=21 MAXLENGTH=20 value='" . $_POST['Description'] . "'></TD>
	</TR>
	<TR><TD>" . _('Location') . ":</TD>
		<TD><SELECT name='Location'>";

while ($myrow = DB_fetch_array($result)) {
	if (isset($_POST['Location']) and $myrow['loccode']==$_POST['Location']) {
		echo "<OPTION SELECTED VALUE='";
	} else {
		echo "<OPTION VALUE='";
	}
	echo $myrow['loccode'] . "'>" . $myrow['locationname'];

} //end while loop

DB_free_result($result);


echo '</SELECT></TD></TR>
	<TR><TD>' . _('Overhead Recovery GL Account') . ":</TD>
		<TD><SELECT name='OverheadRecoveryAct'>";

//SQL to poulate account selection boxes
$SQL = 'SELECT accountcode,
		accountname
	FROM chartmaster INNER JOIN accountgroups
		ON chartmaster.group_=accountgroups.groupname
	WHERE accountgroups.pandl!=0
	ORDER BY accountcode';

$result = DB_query($SQL,$db);

while ($myrow = DB_fetch_array($result)) {
	if (isset($_POST['OverheadRecoveryAct']) and $myrow['accountcode']==$_POST['OverheadRecoveryAct']) {
		echo '<OPTION SELECTED VALUE=';
	} else {
		echo '<OPTION VALUE=';
	}
	echo $myrow['accountcode'] . '>' . $myrow['accountname'];

} //end while loop
DB_free_result($result);

if (!isset($_POST['OverheadPerHour'])) {
	$_POST['OverheadPerHour']=0;
}

echo '</TD></TR>';
echo '<TR><TD>' . _('Overhead Per Hour') . ":</TD>
	<TD><input type='Text' name='OverheadPerHour' SIZE=6 MAXLENGTH=6 onKeyPress='return restrictToNumbers(this, event)' value=" . $_POST['OverheadPerHour'] . '></TD></TR>
	</TABLE>';

echo "<br><CENTER><input type='Submit' name='submit' value='" . _('Enter Information') . "'>";

if (!isset($_GET['SelectedWC']) or $_GET['SelectedWC']=='') {
	echo "<script>defaultControl(document.forms[0].Code);</script>";
} else {
	echo "<script>defaultControl(document.forms[0].Description);</script>";
}

echo '</FORM>';
include('includes/footer.inc');
?>
