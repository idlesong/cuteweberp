<?php
/* $Revision: 1.3 $ */

$PageSecurity = 15;

include('includes/session.inc');
$title = _('Customer Types') . ' / ' . _('Maintenance');
include('includes/header.inc');

if (isset($_POST['SelectedType'])){
	$SelectedType = strtoupper($_POST['SelectedType']);
} elseif (isset($_GET['SelectedType'])){
	$SelectedType = strtoupper($_GET['SelectedType']);
}

if (isset($Errors)) {
	unset($Errors);
}

$Errors = array();

echo '<P class="page_title_text"><img src="'.$rootpath.'/css/'.$theme.'/images/maintenance.png" TITLE="' . _('Customer Types') . '" ALT="">' . _('Customer Type Setup') . '</P>';
echo '<DIV class="page_help_text">' . _('Add/edit/delete Customer Types') . '</DIV><BR>';

if (isset($_POST['submit'])) {

	//initialise no input errors assumed initially before we test
	$InputError = 0;

	/* actions to take once the user has clicked the submit button
	ie the page has called itself with some user input */

	//first off validate inputs sensible
	$i=1;
	if (strlen($_POST['typename']) >100) {
		$InputError = 1;
		echo prnMsg(_('The customer type name description must be 100 characters or less long'),'error');
		$Errors[$i] = 'CustomerType';
		$i++;
	}

	if (isset($SelectedType) AND $InputError !=1) {

		$sql = "UPDATE debtortype
			SET typename = '" . $_POST['typename'] . "'
			WHERE typeid = '$SelectedType'";

		$msg = _('The customer type') . ' ' . $SelectedType . ' ' .  _('has been updated');
	} elseif ( $InputError !=1 ) {

		// First check the type is not being duplicated

		$checkSql = "SELECT count(*)
			     FROM debtortype
			     WHERE typeid = '" . $_POST['typeid'] . "'";

		$checkresult = DB_query($checkSql,$db);
		$checkrow = DB_fetch_row($checkresult);

		if ( $checkrow[0] > 0 ) {
			$InputError = 1;
			prnMsg( _('The customer type ') . $_POST['typeid'] . _(' already exist.'),'error');
		} else {

			// Add new record on submit

			$sql = "INSERT INTO debtortype
						(typename)
					VALUES ('" . $_POST['typename'] . "')";


			$msg = _('Customer type') . ' ' . $_POST["typename"] .  ' ' . _('has been created');
			$checkSql = "SELECT count(typeid)
			     FROM debtortype";
			$result = DB_query($checkSql, $db);
			$row = DB_fetch_row($result);

		}
	}

	if ( $InputError !=1) {
	//run the SQL from either of the above possibilites
		$result = DB_query($sql,$db);


	// Fetch the default price list.
		$sql = "SELECT confvalue
					FROM config
					WHERE confname='DefaultCustomerType'";
		$result = DB_query($sql,$db);
		$CustomerTypeRow = DB_fetch_row($result);
		$DefaultCustomerType = $CustomerTypeRow[0];

	// Does it exist
		$checkSql = "SELECT count(*)
			     FROM debtortype
			     WHERE typeid = '" . $DefaultCustomerType . "'";
		$checkresult = DB_query($checkSql,$db);
		$checkrow = DB_fetch_row($checkresult);

	// If it doesnt then update config with newly created one.
		if ($checkrow[0] == 0) {
			$sql = "UPDATE config
					SET confvalue='" . $_POST['typeid'] . "'
					WHERE confname='DefaultCustomerType'";
			$result = DB_query($sql,$db);
			$_SESSION['DefaultCustomerType'] = $_POST['typeid'];
		}

		prnMsg($msg,'success');

		unset($SelectedType);
		unset($_POST['typeid']);
		unset($_POST['typename']);
	}

} elseif ( isset($_GET['delete']) ) {

	// PREVENT DELETES IF DEPENDENT RECORDS IN 'DebtorTrans'
	// Prevent delete if saletype exist in customer transactions

	$sql= "SELECT COUNT(*)
	       FROM debtortrans
	       WHERE debtortrans.type='$SelectedType'";

	$ErrMsg = _('The number of transactions using this customer type could not be retrieved');
	$result = DB_query($sql,$db,$ErrMsg);

	$myrow = DB_fetch_row($result);
	if ($myrow[0]>0) {
		prnMsg(_('Cannot delete this type because customer transactions have been created using this type') . '<br>' . _('There are') . ' ' . $myrow[0] . ' ' . _('transactions using this type'),'error');

	} else {

		$sql = "SELECT COUNT(*) FROM debtorsmaster WHERE typeid='$SelectedType'";

		$ErrMsg = _('The number of transactions using this Type record could not be retrieved because');
		$result = DB_query($sql,$db,$ErrMsg);
		$myrow = DB_fetch_row($result);
		if ($myrow[0]>0) {
			prnMsg (_('Cannot delete this type because customers are currently set up to use this type') . '<br>' . _('There are') . ' ' . $myrow[0] . ' ' . _('customers with this type code'));
		} else {

			$sql="DELETE FROM debtortype WHERE typeid='$SelectedType'";
			$ErrMsg = _('The Type record could not be deleted because');
			$result = DB_query($sql,$db,$ErrMsg);
			prnMsg(_('Customer type') . $SelectedType  . ' ' . _('has been deleted') ,'success');

			unset ($SelectedType);
			unset($_GET['delete']);

		}
	} //end if sales type used in debtor transactions or in customers set up
}

if (!isset($SelectedType)){

/* It could still be the second time the page has been run and a record has been selected for modification - SelectedType will exist because it was sent with the new call. If its the first time the page has been displayed with no parameters
then none of the above are true and the list of sales types will be displayed with
links to delete or edit each. These will call the same page again and allow update/input
or deletion of the records*/

	$sql = 'SELECT typeid, typename FROM debtortype';
	$result = DB_query($sql,$db);

	echo '<CENTER><TABLE BORDER=1>';
	echo "<tr>
		<TH>" . _('Type Name') . "</TH>
	</TR>";

$k=0; //row colour counter

while ($myrow = DB_fetch_row($result)) {
	if ($k==1){
		echo '<TR class="EvenTableRows">';
		$k=0;
	} else {
		echo '<TR class="OddTableRows">';
		$k=1;
	}

	printf("
		<td>%s</td>
		<td><a href='%sSelectedType=%s'>" . _('Edit') . "</td>
		<td><a href='%sSelectedType=%s&delete=yes' onclick=\"return confirm('" . _('Are you sure you wish to delete this Customer Type?') . "');\">" . _('Delete') . "</td>
		</tr>",
		$myrow[1],
		$_SERVER['PHP_SELF'] . '?' . SID, $myrow[0],
		$_SERVER['PHP_SELF'] . '?' . SID, $myrow[0]);
	}
	//END WHILE LIST LOOP
	echo '</table></CENTER>';
}

//end of ifs and buts!
if (isset($SelectedType)) {

	echo '<CENTER><P><A HREF="' . $_SERVER['PHP_SELF'] . '?' . SID . '">' . _('Show All Types Defined') . '</A></CENTER><p>';
}
if (! isset($_GET['delete'])) {

	echo "<FORM METHOD='post' action=" . $_SERVER['PHP_SELF'] . '?' . SID . '>';
	echo '<P><CENTER><TABLE BORDER=1>'; //Main table
	echo '<TD><TABLE>'; // First column


	// The user wish to EDIT an existing type
	if ( isset($SelectedType) AND $SelectedType!='' )
	{

		$sql = "SELECT typeid,
			       typename
		        FROM debtortype
		        WHERE typeid='$SelectedType'";

		$result = DB_query($sql, $db);
		$myrow = DB_fetch_array($result);

		$_POST['typeid'] = $myrow['typeid'];
		$_POST['typename']  = $myrow['typename'];

		echo "<INPUT TYPE=HIDDEN NAME='SelectedType' VALUE=" . $SelectedType . ">";
		echo "<INPUT TYPE=HIDDEN NAME='typeid' VALUE=" . $_POST['typeid'] . ">";
		echo "<CENTER><TABLE> <TR><TD>";

		// We dont allow the user to change an existing type code

		echo 'Type ID: ' . $_POST['typeid'] . '</TD></TR>';

	} else 	{

		// This is a new type so the user may volunteer a type code

		echo "<CENTER><TABLE>";

	}

	if (!isset($_POST['typename'])) {
		$_POST['typename']='';
	}
	echo "<TR><TD>" . _('Type Name') . ":</TD><TD><input type='Text' name='typename' value='" . $_POST['typename'] . "'></TD></TR>";

   	echo '</TABLE>'; // close table in first column
   	echo '</TD></TR></TABLE>'; // close main table

	echo '<P><INPUT TYPE=submit NAME=submit VALUE="' . _('Accept') . '"><INPUT TYPE=submit NAME=Cancel VALUE="' . _('Cancel') . '"></CENTER>';

	echo '</FORM>';

} // end if user wish to delete


include('includes/footer.inc');
?>
