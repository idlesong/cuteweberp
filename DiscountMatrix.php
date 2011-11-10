<?php
/* $Revision: 1.7 $ */

$PageSecurity = 11;
include('includes/session.inc');
$title = _('Discount Matrix Maintenance');
include('includes/header.inc');


if (isset($Errors)) {
	unset($Errors);
}
	
$Errors = array();	
$i=1;

if (isset($_POST['submit'])) {

	//initialise no input errors assumed initially before we test
	$InputError = 0;

	if (!is_numeric($_POST['QuantityBreak'])){
		prnMsg( _('The quantity break must be entered as a positive number'),'error');
		$InputError =1;
		$Errors[$i] = 'QuantityBreak';
		$i++;		
	}

	if ($_POST['QuantityBreak']<=0){
		prnMsg( _('The quantity of all items on an order in the discount category') . ' ' . $_POST['DiscountCategory'] . ' ' . _('at which the discount will apply is 0 or less than 0') . '. ' . _('Positive numbers are expected for this entry'),'warn');
		$InputError =1;
		$Errors[$i] = 'QuantityBreak';
		$i++;		
	}
	if (!is_numeric($_POST['DiscountRate'])){
		prnMsg( _('The discount rate must be entered as a positive number'),'warn');
		$InputError =1;
		$Errors[$i] = 'DiscountRate';
		$i++;		
	}
	if ($_POST['DiscountRate']<=0 OR $_POST['DiscountRate']>=70){
		prnMsg( _('The discount rate applicable for this record is either less than 0% or greater than 70%') . '. ' . _('Numbers between 1 and 69 are expected'),'warn');
		$InputError =1;
		$Errors[$i] = 'DiscountRate';
		$i++;		
	}

	/* actions to take once the user has clicked the submit button
	ie the page has called itself with some user input */

	if ($InputError !=1) {

		$sql = "INSERT INTO discountmatrix (salestype, 
							discountcategory, 
							quantitybreak, 
							discountrate) 
					VALUES('" . $_POST['SalesType'] . "', 
						'" . $_POST['DiscountCategory'] . "', 
						" . $_POST['QuantityBreak'] . ", 
						" . ($_POST['DiscountRate']/100) . ')';

		$result = DB_query($sql,$db);
		prnMsg( _('The discount matrix record has been added'),'success');
		unset($_POST['DiscountCategory']);
		unset($_POST['SalesType']);
		unset($_POST['QuantityBreak']);
		unset($_POST['DiscountRate']);
	}
} elseif (isset($_GET['Delete']) and $_GET['Delete']=='yes') {
/*the link to delete a selected record was clicked instead of the submit button */

	$sql="DELETE FROM discountmatrix
		WHERE discountcategory='" .$_GET['DiscountCategory'] . "'
		AND salestype='" . $_GET['SalesType'] . "'
		AND quantitybreak=" . $_GET['QuantityBreak'];

	$result = DB_query($sql,$db);
	prnMsg( _('The discount matrix record has been deleted'),'success');
}

$sql = 'SELECT sales_type,
		salestype,
		discountcategory,
		quantitybreak,
		discountrate
	FROM discountmatrix INNER JOIN salestypes
		ON discountmatrix.salestype=salestypes.typeabbrev
	ORDER BY salestype,
		discountcategory,
		quantitybreak';

$result = DB_query($sql,$db);

echo '<CENTER><table>';
echo "<tr><th>" . _('Sales Type') . "</th>
	<th>" . _('Discount Category') . "</th>
	<th>" . _('Quantity Break') . "</th>
	<th>" . _('Discount Rate') . ' %' . "</th></TR>";

$k=0; //row colour counter

while ($myrow = DB_fetch_array($result)) {
	if ($k==1){
		echo '<tr class="EvenTableRows">';
		$k=0;
	} else {
		echo '<tr class="OddTableRows">';
		$k=1;
	}
	$DeleteURL = $_SERVER['PHP_SELF'] . '?' . SID . '&Delete=yes&SalesType=' . $myrow['salestype'] . '&DiscountCategory=' . $myrow['discountcategory'] . '&QuantityBreak=' . $myrow['quantitybreak'];

	printf("<td>%s</td>
		<td>%s</td>
		<td>%s</td>
		<td>%s</td>
		<td><a href='%s'>" . _('Delete') . '</td>
		</tr>',
		$myrow['sales_type'],
		$myrow['discountcategory'],
		$myrow['quantitybreak'],
		number_format($myrow['discountrate']*100,2) ,
		$DeleteURL);

}

echo '</TABLE><HR>';

echo "<FORM METHOD='post' action=" . $_SERVER['PHP_SELF'] . '?' . SID . '>';


echo '<TABLE>';

$sql = 'SELECT typeabbrev,
		sales_type
	FROM salestypes';

$result = DB_query($sql, $db);

echo '<TR><TD>' . _('Customer Price List') . ' (' . _('Sales Type') . '):</TD><TD>';

echo "<SELECT TABINDEX=1 NAME='SalesType'>";

while ($myrow = DB_fetch_array($result)){
	if (isset($_POST['SalesType']) and $myrow['typeabbrev']==$_POST['SalesType']){
		echo "<OPTION SELECTED VALUE='" . $myrow['typeabbrev'] . "'>" . $myrow['sales_type'];
	} else {
		echo "<OPTION VALUE='" . $myrow['typeabbrev'] . "'>" . $myrow['sales_type'];
	}
}

echo '</SELECT>';


echo '<TR><TD>' . _('Discount Category Code') . ':</TD><TD>';
if (!isset($_POST['DiscCat'])) {$_POST['DiscCat']='';}
echo "<INPUT TABINDEX=2 TYPE='Text' NAME='DiscountCategory' MAXLENGTH=2 SIZE=2 VALUE='" . $_POST['DiscCat'] . "'></TD></TR>";

echo '<TR><TD>' . _('Quantity Break') . ":</TD><TD><input tabindex=3 "
	 . (in_array('QuantityBreak',$Errors) ? "class='inputerror'" : "")
	 ." type='Text' name='QuantityBreak' SIZE=10 MAXLENGTH=10></TD></TR>";

echo '<TR><TD>' . _('Discount Rate') . " (%):</TD><TD><input tabindex=4 "
	. (in_array('DiscountRate',$Errors) ? "class='inputerror'" : "") .
		"type='Text' name='DiscountRate' SIZE=4 MAXLENGTH=4></TD></TR>";
echo '</TABLE>';

echo "<CENTER><input tabindex=5 type='Submit' name='submit' value='" . _('Enter Information') . "'></CENTER>";

echo '</FORM>';

include('includes/footer.inc');
?>
