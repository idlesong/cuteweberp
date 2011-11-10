<?php

/* $Revision: 1.20 $ */

/*
 *      h5SupplierSearch.php
 *
 */

$PageSecurity = 4;
include('includes/DefinePOClass.php');
include('includes/session.inc');
include('includes/SQL_CommonFunctions.inc');
echo '<link href="'.$rootpath. '/css/'. $_SESSION['Theme'] .'/default.css" rel="stylesheet" type="text/css" />';
echo '<meta charset="utf-8">';

$title = _('Supplier Search Entry');

$msg='';
$_POST['SearchSuppliers']=1;
$_POST['Keywords']='%';
if (isset($_POST['SearchSuppliers'])) {

	If (strlen($_POST['Keywords']) > 0 AND strlen($_POST['SuppCode']) > 0) {
		$msg = _('Supplier name keywords have been used in preference to the supplier code extract entered');
	}
	If ($_POST['Keywords'] == '' AND $_POST['SuppCode'] == '') {
		$msg = _('At least one Supplier Name keyword OR an extract of a Supplier Code must be entered for the search');
	} else {
		If (strlen($_POST['Keywords']) > 0) {
		//insert wildcard characters in spaces
		
			$i = 0;
			$SearchString = '%';
			while (strpos($_POST['Keywords'], ' ', $i)) {
				$wrdlen = strpos($_POST['Keywords'], ' ', $i) - $i;
				$SearchString=$SearchString . substr($_POST['Keywords'],$i,$wrdlen) . '%';
				$i = strpos($_POST['Keywords'], ' ' ,$i) + 1;
			}
			$SearchString = $SearchString . substr($_POST['Keywords'],$i) . '%';
			$SQL = "SELECT suppliers.supplierid,
					suppliers.suppname,
					suppliers.currcode
				FROM suppliers
				WHERE suppliers.suppname " . LIKE . " '$SearchString'
				ORDER BY suppliers.suppname";

		} elseif (strlen($_POST['SuppCode']) > 0) {
			$SQL = "SELECT suppliers.supplierid,
					suppliers.suppname,
					suppliers.currcode
				FROM suppliers
				WHERE suppliers.supplierid " . LIKE . " '%" . $_POST['SuppCode'] . "%'
				ORDER BY suppliers.supplierid";
		}

		$ErrMsg = _('The searched supplier records requested cannot be retrieved because');
		$result_SuppSelect = DB_query($SQL,$db,$ErrMsg);

		if (DB_num_rows($result_SuppSelect) == 1) {
			$myrow = DB_fetch_array($result_SuppSelect);
			$_POST['Select'] = $myrow['supplierid'];
		} elseif (DB_num_rows($result_SuppSelect) == 0) {
			prnMsg( _('No supplier records contain the selected text') . ' - ' . _('please alter your search criteria and try again'),'info');
		}
	} /*one of keywords or SuppCode was more than a zero length string */
} /*end of if search for supplier codes/names */


if (isset($_POST['Select'])) {
	/* will only be true if page called from supplier selection form
	 * or set because only one supplier record returned from a search
	 * so parse the $Select string into supplier code and branch code
	 * */
	$sql = "SELECT suppliers.supplierid, 
			suppliers.suppname,
			suppliers.currcode,
			suppliers.address1,
			suppliers.address2,
			suppliers.address3,
			suppliers.address4,
			suppliers.address5,
			suppliers.address6,
			currencies.rate
		FROM suppliers INNER JOIN currencies
		ON suppliers.currcode=currencies.currabrev
		WHERE supplierid='" . $_POST['Select'] . "'";

	$ErrMsg = _('The supplier record of the supplier selected') . ': ' . $_POST['Select'] . ' ' . _('cannot be retrieved because');
	$DbgMsg = _('The SQL used to retrieve the supplier details and failed was');
	$result = DB_query($sql,$db,$ErrMsg,$DbgMsg);
	
	if (DB_num_rows($result) == 1) {
		$myrow = DB_fetch_row($result);
	}else{
		echo 'ERrrrrrrrrrrrrrrrrrrr';
	}
	$_SESSION['SupplierSelected'] = $_POST['Select']; 
	
	//temp 
	$_SESSION['PO']->SupplierID = $_POST['Select'];
	$_SESSION['PO']->SupplierName = $myrow[1];
	$_SESSION['PO']->CurrCode = $myrow[2];
	$_SESSION['PO']->ExRate = $myrow[9];
	$_SESSION['PO']->DelAdd1 = $myrow['address1'];
	$_SESSION['PO']->DelAdd2 = $myrow['address2'];
	$_SESSION['PO']->DelAdd3 = $myrow['address3'];
	$_SESSION['PO']->DelAdd4 = $myrow['address4'];
	$_SESSION['PO']->DelAdd5 = $myrow['address5'];
	$_SESSION['PO']->DelAdd6 = $myrow['address6'];
	//$_POST['ExRate'] = $myrow[2];
	
	//$_SESSION['SupplierChanged'] = true;
	//$_SESSION['defaultSupplierID']= $_SESSION['PO']->SupplierID ;
	//$_SESSION['RequireSupplierSelection'] = 0;
	//echo 'SupID:'.'0'.$myrow[0].',1'.$myrow[1].',2'.$myrow[2];
	//echo "<br>SupID:".$myrow['supplierid']."SupName:".$myrow['suppname'];
	//echo 'SupName:'.$_SESSION['PO']->SupplierName; 
	//echo 'ExRate:'.$_SESSION['PO']->ExRate; 

	echo '<script>	
	var strOpenerLink = window.opener.location.href;
	var posSt = strOpenerLink.indexOf("?");
	var newLinkAddr = strOpenerLink.substring(0,posSt+1);
	//window.alert(newLinkAddr);
	
	window.opener.location.href = newLinkAddr;
    if (window.opener.progressWindow)
    {
        window.opener.progressWindow.close();
    }
    window.close();
	</script>';
	
}

/*
* Display the supplier select form
*/
if(1){

	echo '<BR><BR><FONT SIZE=3><B>' . _('Supplier Selection') . "</B></FONT><BR>
	<FORM ACTION='" . $_SERVER['PHP_SELF'] . '?' . SID . "' METHOD=POST>";
	if (strlen($msg)>1){
		prnMsg($msg,'warn');
	}

	echo '<TABLE class="searchtable">
	<TR>
	<TD>' . _('Supplier name') . ":</TD>
	<TD><INPUT TYPE='Text' NAME='Keywords' SIZE=5	MAXLENGTH=25></TD>
	<TD><FONT SIZE=3><B>" . _('OR') . '</B></FONT></TD>
	<TD>' . _('Supplier code') . ":</TD>
	<TD><INPUT TYPE='Text' NAME='SuppCode' SIZE=15	MAXLENGTH=18></TD>
	</TR>
	<TR><TD><INPUT TYPE=SUBMIT NAME='SearchSuppliers' VALUE=" . _('Search Now') . "></TD></TR>
	</TABLE>
	<CENTER>
	</CENTER>";

	If (isset($result_SuppSelect)) {

		echo '<BR><CENTER><TABLE class="commontable">';

		$tableheader = "<TR>
				<TH>" . _('Code') . "</TH>
				<TH>" . _('Supplier Name') . "</TH>
				<TH>" . _('Currency') . '</TH>
				</TR>';

		echo $tableheader;

		$j = 1;
		$k = 0; /* row counter to determine background colour */

		while ($myrow = DB_fetch_array($result_SuppSelect)) {

			if ($k == 1){
				echo '<tr class="EvenTableRows">';
				$k = 0;
			} else {
				echo '<tr class="OddTableRows">';
				$k++;
			}

			printf("<td><INPUT TYPE=SUBMIT NAME='Select' VALUE='%s'</td>
				<td>%s</td>
				<td>%s</td>
				</tr>",
				$myrow['supplierid'],
				$myrow['suppname'],
				$myrow['currcode']);

			$j++;
			If ($j == 11){
				$j = 1;
				echo $tableheader;
			}
			// end of page full new headings if
		}
		// end of while loop

		echo '</TABLE></CENTER>';

	}
}

echo '</form>';
//}
//include('includes/footer.inc');
?>
