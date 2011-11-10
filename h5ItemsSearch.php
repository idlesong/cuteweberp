<?php

/* $Revision: 1.32 $ */
$PageSecurity = 4;

include('includes/DefinePOClass.php');
include('includes/SQL_CommonFunctions.inc');

/* Session started in header.inc for password checking and authorisation level check */
include('includes/session.inc');
$title = _('Search Items');
//if (!isset($_SESSION['PO'])){
//   header ('Location:' . $rootpath . '/PO_Header.php?' . SID);
//   exit;
//}
//include('includes/header.inc');
echo '<link href="'.$rootpath. '/css/'. $_SESSION['Theme'] .'/default.css" rel="stylesheet" type="text/css" />';
echo '<meta charset="utf-8">';

//$Maximum_Number_Of_Parts_To_Show = 250;

if (isset($_POST['Select'])){

$_SESSION['msg_SelectedItem']=$_POST['Select'];
echo $_POST['Select'];

echo '<script language="JavaScript" type="text/javascript">
    window.opener.location.href = window.opener.location.href;
    if (window.opener.progressWindow)
    {
        window.opener.progressWindow.close();
    }
    window.close();
	</script>';
exit;
}


$_POST['Search']=1;
$_POST['Keywords']='%';
$_POST['StockCat']='All';
if (isset($_POST['Search'])){  /*ie seach for stock items , database stuff only*/

	if ($_POST['Keywords'] and $_POST['StockCode']) {
		$msg=_('Stock description keywords have been used in preference to the Stock code extract entered');
	}
	if ($_POST['Keywords']) {
		//insert wildcard characters in spaces

		$i=0;
		$SearchString = '%';
		while (strpos($_POST['Keywords'], ' ', $i)) {
			$wrdlen=strpos($_POST['Keywords'],' ',$i) - $i;
			$SearchString=$SearchString . substr($_POST['Keywords'],$i,$wrdlen) . '%';
			$i=strpos($_POST['Keywords'],' ',$i) +1;
		}
		$SearchString = $SearchString. substr($_POST['Keywords'],$i).'%';

		if ($_POST['StockCat']=='All'){
			$sql = 'SELECT stockmaster.stockid,
					stockmaster.description,
					stockmaster.units
				FROM stockmaster INNER JOIN stockcategory
				ON stockmaster.categoryid=stockcategory.categoryid
				WHERE stockmaster.mbflag!='."'D'".'
				and stockmaster.mbflag!='."'A'".'
				and stockmaster.mbflag!='."'K'".'
				and stockmaster.discontinued!=1
				and stockmaster.description ' . LIKE . "'$SearchString'
				ORDER BY stockmaster.stockid";
		} else {
			$sql = 'SELECT stockmaster.stockid,
					stockmaster.description,
					stockmaster.units
				FROM stockmaster INNER JOIN stockcategory
				ON stockmaster.categoryid=stockcategory.categoryid
				WHERE stockmaster.mbflag!='."'D'".'
				and stockmaster.mbflag!='."'A'".'
				and stockmaster.mbflag!='."'K'".'
				and stockmaster.discontinued!=1
				and stockmaster.description ' . LIKE . "'$SearchString'
				and stockmaster.categoryid='" . $_POST['StockCat'] . "'
				ORDER BY stockmaster.stockid";
		}

	} elseif ($_POST['StockCode']){

		$_POST['StockCode'] = '%' . $_POST['StockCode'] . '%';

		if ($_POST['StockCat']=='All'){
			$sql = 'SELECT stockmaster.stockid,
					stockmaster.description,
					stockmaster.units
				FROM stockmaster INNER JOIN stockcategory
				ON stockmaster.categoryid=stockcategory.categoryid
				WHERE stockmaster.mbflag!='."'D'".'
				and stockmaster.mbflag!='."'A'".'
				and stockmaster.mbflag!='."'K'".'
				and stockmaster.discontinued!=1
				and stockmaster.stockid ' . LIKE .  " '" . $_POST['StockCode'] . "'
				ORDER BY stockmaster.stockid";
		} else {
			$sql = 'SELECT stockmaster.stockid,
					stockmaster.description,
					stockmaster.units
				FROM stockmaster INNER JOIN stockcategory
				ON stockmaster.categoryid=stockcategory.categoryid
				WHERE stockmaster.mbflag!='."'D'".'
				and stockmaster.mbflag!='."'A'".'
				and stockmaster.mbflag!='."'K'".'
				and stockmaster.discontinued!=1
				and stockmaster.stockid ' . LIKE . " '" . $_POST['StockCode'] . "'
				and stockmaster.categoryid='" . $_POST['StockCat'] . "'
				ORDER BY stockmaster.stockid";
		}

	} else {
		if ($_POST['StockCat']=='All'){
			$sql = 'SELECT stockmaster.stockid,
					stockmaster.description,
					stockmaster.units
				FROM stockmaster INNER JOIN stockcategory
				ON stockmaster.categoryid=stockcategory.categoryid
				WHERE stockmaster.mbflag!='."'D'".'
				and stockmaster.mbflag!='."'A'".'
				and stockmaster.mbflag!='."'K'".'
				and stockmaster.discontinued!=1
				ORDER BY stockmaster.stockid';
		} else {
			$sql = 'SELECT stockmaster.stockid,
					stockmaster.description,
					stockmaster.units
				FROM stockmaster INNER JOIN stockcategory
				ON stockmaster.categoryid=stockcategory.categoryid
				WHERE stockmaster.mbflag!='."'D'".'
				and stockmaster.mbflag!='."'A'".'
				and stockmaster.mbflag!='."'K'".'
				and stockmaster.discontinued!=1
				and stockmaster.categoryid='."'" . $_POST['StockCat'] . "'".'
				ORDER BY stockmaster.stockid';
		}
	}

	$ErrMsg = _('There is a problem selecting the part records to display because');
	$DbgMsg = _('The SQL statement that failed was');
	$SearchResult = DB_query($sql,$db,$ErrMsg,$DbgMsg);

	if (DB_num_rows($SearchResult)==0 && $debug==1){
		prnMsg( _('There are no products to display matching the criteria provided'),'warn');
	}
	if (DB_num_rows($SearchResult)==1){

		$myrow=DB_fetch_array($SearchResult);
		$_GET['NewItem'] = $myrow['stockid'];
		DB_data_seek($SearchResult,0);
	}

} //end of if search

/* This is where the order as selected should be displayed  reflecting any deletions or insertions*/

echo '<form action="' . $_SERVER['PHP_SELF'] . '?' . SID . '" method=post>';

//echo '<center>' . _('Purchase Order') . ': <font color=blue size=4><b>' . $_SESSION['PO']->OrderNo . ' ' . $_SESSION['PO']->SupplierName . ' </b></font> - ' . _('All amounts stated in') . ' ' . $_SESSION['PO']->CurrCode . '<br>';
//echo '<center><b>' . _('Order Summary') . '</b>';
echo '<table class="commontable">';


/* Now show the stock item selection search stuff below */
$sql='SELECT categoryid,
		categorydescription
	FROM stockcategory
	WHERE stocktype<>'."'".'L'."'".'
	AND stocktype<>'."'".'D'."'".'
	ORDER BY categorydescription';
$ErrMsg = _('The supplier category details could not be retrieved because');
$DbgMsg = _('The SQL used to retrieve the category details but failed was');
$result1 = DB_query($sql,$db,$ErrMsg,$DbgMsg);

echo '<b>' . _('Search For Stock Items') . '</b>
	<table class="commontable"><tr>
		<td><font size=2>' . _('Enter text extracts in the description') . ':</font></td>
		<td><input type="text" name="Keywords" size=20 maxlength=25 value="' . $_POST['Keywords'] . '"></td>
		<td><font size=2>' . _('Select a stock category') . ':</font><select name="StockCat">';

echo '<option selected value="All">' . _('All');
while ($myrow1 = DB_fetch_array($result1)) {
	if (isset($_POST['StockCat']) and $_POST['StockCat']==$myrow1['categoryid']){
		echo '<option selected value='. $myrow1['categoryid'] . '>' . $myrow1['categorydescription'];
	} else {
		echo '<option value='. $myrow1['categoryid'] . '>' . $myrow1['categorydescription'];
	}
}

if (!isset($_POST['Keywords'])) {
	$_POST['Keywords']='';
}

if (!isset($_POST['StockCode'])) {
	$_POST['StockCode']='';
}

echo '</select></tr>
	<tr>
	<td><font size 3><b>' . _('or') . ' </b></font><font size=2>' . _('Enter extract of the Stock Code') . ':</font></td>
	<td><input type="text" name="StockCode" size=15 maxlength=18 value="' . $_POST['StockCode'] . '"></td>
	<td><input type=submit name="Search" VALUE="' . _('Search Now') . '"></td></tr>
	</table>';

//unset($_SESSION['PO_NewItem']);
//$_SESSION['PO_NewItem']='SRT3210DICE';
//echo $_SESSION['PO_NewItem'];
$PartsDisplayed =0;

if (isset($SearchResult)) {

	echo "<CENTER><TABLE CELLPADDING=1 COLSPAN=7 BorDER=1>";

	$tableheader = '<tr>
			<th>' . _('Code')  . '</th>
			<th>' . _('Description') . '</th>
			<th>' . _('Units') . '</th>
			</tr>';
	echo $tableheader;

	$j = 1;
	$k = 0; //row colour counter

	while ($myrow=DB_fetch_array($SearchResult)) {

		if ($k==1){
			echo '<tr class="EvenTableRows">';
			$k=0;
		} else {
			echo '<tr class="OddTableRows">';
			$k=1;
		}

		$filename = $myrow['stockid'] . '.jpg';
		if (file_exists( $_SESSION['part_pics_dir'] . '/' . $filename) ) {

			$ImageSource = '<img src="'.$rootpath . '/' . $_SESSION['part_pics_dir'] . '/' . $myrow['stockid'] . '.jpg" width="50" height="50">';

		} else {
			$ImageSource = '<i>'._('No Image').'</i>';
		}
		
		printf('<td><input type="submit" name="Select", value=%s ></td>
			<td>%s</td>
			<td>%s</td>
			<td ALIGN=CENTER>%s</td>
			<td><a href="%s/PO_Items.php?%s&NewItem=%s">' . _('Add to Order') . '</a></td>
			</tr>',
			$myrow['stockid'],
			$myrow['description'],
			$myrow['units'],
			$ImageSource,
			$rootpath,
			SID,
			$myrow['stockid']);


		$PartsDisplayed++;
		if ($PartsDisplayed == $Maximum_Number_Of_Parts_To_Show){
			break;
		}
#end of page full new headings if
	}
#end of while loop
	echo '</table>';
	if ($PartsDisplayed == $Maximum_Number_Of_Parts_To_Show){
	/*$Maximum_Number_Of_Parts_To_Show defined in config.php */
		prnMsg( _('Only the first') . ' ' . $Maximum_Number_Of_Parts_To_Show . ' ' . _('can be displayed') . '. ' . _('Please restrict your search to only the parts required'),'info');
	}
}#end if SearchResults to show
echo '</center>';

echo '</form>';


//include('includes/footer.inc');
?>
