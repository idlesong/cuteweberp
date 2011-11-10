<?php

$PageSecurity = 2;

include('includes/session.inc');
$title = _('Search Customers');
//include('includes/header.inc');
include('includes/Wiki.php');
include('includes/SQL_CommonFunctions.inc');

echo '<link href="/weberp/css/silverwolf/default.css" rel="stylesheet" type="text/css">';
echo '<meta charset="utf-8">';

if (isset($_POST['Select'])){

	$_SESSION['msg_SelectedCustomer']=$_POST['Select'];
	$_SESSION['RequireCustomerSelection'] = 0;	
	$_SESSION['RequireOrderInitalize']= true;	//when change the customer, need Initialize the order
	//echo $_POST['Select'];
	//echo $_SESSION['msg_DefaultLocation'];
	echo '<script type="text/javascript">
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

	exit;
}

$_POST['Search']='%';
$_POST['Keywords']='%';

unset($_SESSION['CustomerID']);

if( $_POST['Search'] ){
	if (strlen($_POST['Keywords'])>0) {

		$_POST['Keywords'] = strtoupper(trim($_POST['Keywords']));

		$i = 0;
		//$SearchString = "%";

		while (strpos($_POST['Keywords'], " ", $i)) {
			$wrdlen = strpos($_POST['Keywords']," ",$i) - $i;
			$SearchString = $SearchString . substr($_POST['Keywords'],$i,$wrdlen) . "%";
			$i=strpos($_POST['Keywords']," ",$i) +1;
		}
		$SearchString = $SearchString . substr($_POST['Keywords'],$i)."%";

			$SQL = "SELECT debtorsmaster.debtorno,
			debtorsmaster.name,
			debtorsmaster.address1,
			debtorsmaster.address2,
			debtorsmaster.address3,
			debtorsmaster.address4,	
			debtorsmaster.currcode,
			custbranch.branchcode,			
			custbranch.brname,
			custbranch.contactname,
			debtortype.typename,
			custbranch.defaultlocation,
			custbranch.phoneno,
			custbranch.faxno
		FROM debtorsmaster LEFT JOIN custbranch
			ON debtorsmaster.debtorno = custbranch.debtorno, debtortype
		WHERE debtorsmaster.name " . LIKE . " '$SearchString'
		AND debtorsmaster.typeid = debtortype.typeid
		GROUP BY debtorsmaster.debtorno
		ORDER BY debtorsmaster.debtorno";

	} 
	
	$ErrMsg = _('The searched customer records requested cannot be retrieved because');
	$result = DB_query($SQL,$db,$ErrMsg);
	if (DB_num_rows($result)==1){
		$myrow=DB_fetch_array($result);
		$_POST['Select'] = $myrow['debtorno'];
		$_SESSION['msg_DefaultLocation'] = $myrow['defaultlocation'];
		unset($result);
	} elseif (DB_num_rows($result)==0){
		prnMsg(_('No customer records contain the selected text') . ' - ' . _('please alter your search criteria and try again'),'info');
	}
}
	
echo '<FORM ACTION="' . $_SERVER['PHP_SELF'] . '?' . SID . '" METHOD=POST>';	
echo $msg;

/*
* Customer search table
*/
$keywords = $_POST['Keywords'];
echo "<table class='commontable'>
<tr> <td>Customer Name&Code</td> <td><input type='text', name='keywords' value=\"$keywords\" size=20 maxlenght=25/> </td><td>customer type</td>";

if (isset($_POST['CustType'])) {
	// Show Customer Type drop down list
	$result2=DB_query('SELECT typeid, typename FROM debtortype ',$db);
	// Error if no customer types setup
	if (DB_num_rows($result2)==0){
		$DataError =1;
		echo '<TD COLSPAN=2>' . prnMsg(_('No Customer types defined'),'error') . '</TD>';
	} else {
		// If OK show select box with option selected
		echo '<TD><SELECT NAME="CustType">';
		while ($myrow = DB_fetch_array($result2)) {
			if ($_POST['CustType']==$myrow['typename']){
            	echo "<OPTION SELECTED VALUE='". $myrow['typename'] . "'>" . $myrow['typename'];
            } else {
				echo "<OPTION VALUE='". $myrow['typename'] . "'>" . $myrow['typename'];
			}
		} //end while loop
		DB_data_seek($result2,0);
		echo '</SELECT></TD>';
	}
} else {
	// No option selected yet, so show Customer Type drop down list
	$result2=DB_query('SELECT typeid, typename FROM debtortype ',$db);
	// Error if no customer types setup
	if (DB_num_rows($result2)==0){
		$DataError =1;
		echo '<TD COLSPAN=2>' . prnMsg(_('No Customer types defined'),'error') . '</TD>';
	} else {
		// if OK show select box with available options to choose
		echo '<TD><SELECT NAME="CustType">';
		while ($myrow = DB_fetch_array($result2)) {
			echo "<OPTION VALUE='". $myrow['typename'] . "'>" . $myrow['typename'];
		} //end while loop
		DB_data_seek($result2,0);
		echo '</SELECT></TD></tr>';
	}
}

echo '<tr> <td></td><td></td><td> CSV Format</td><td> <input type ="submit" name="Search" value="Search Now"</td></tr> 
</table>';

?>
	

<?php
if (isset($_SESSION['SalesmanLogin']) and $_SESSION['SalesmanLogin']!=''){
	prnMsg(_('Your account enables you to see only customers allocated to you'),'warn',_('Note: Sales-person Login'));
}

if (isset($result)) {
	unset($_SESSION['CustomerID']);
	$ListCount=DB_num_rows($result);
	$ListPageMax=ceil($ListCount/$_SESSION['DisplayRecordsMax']);
	if (!isset($_POST['CSV'])) {
		echo '<CENTER><TABLE class="commontable">';
		$TableHeader = '<TR>
				<TH>' . _('Code') . '</TH>
				<TH>' . _('Customer Name') . '</TH>
				<TH>' . _('Balance') . '</TH>
				<TH>' . _('$') . '</TH>
				<TH>' . _('Contact') . '</TH>
				<TH>' . _('Type') . '</TH>
				<TH>' . _('Phone') . '</TH>
				<TH>' . _('Fax') . '</TH>
			</TR>';

		echo $TableHeader;
		$j = 1;
		$k = 0; //row counter to determine background colour
  		$RowIndex = 0;
	}
	if (DB_num_rows($result)<>0){

		if (isset($_POST['CSV'])) {
			echo '<BR><P class="page_title_text">' . _('Comma Seperated Values (CSV) Search Result') . '</P>';
			echo '<DIV class="page_help_text">' . _('CSV data can be copied and used to import data into software such as a spreadsheet.') . '</DIV><BR>';
			printf("<DIV class=csv>Code, Customer Name, Address1, Address2, Address3, Address4, Contact, Type, Phone, Fax");
			while ($myrow2=DB_fetch_array($result)) {
				printf("<br>%s,
                        %s,
                        %s,
                        %s,
                        %s,
                        %s,
                        %s,
                        %s,
                        %s,
                        %s",
                        $myrow2['debtorno'],
                        str_replace(',', '',$myrow2['name']),
                        str_replace(',', '',$myrow2['address1']),
                        str_replace(',', '',$myrow2['address2']),
						str_replace(',', '',$myrow2['address3']),
						str_replace(',', '',$myrow2['address4']),
                        str_replace(',', '',$myrow2['contactname']),
                        str_replace(',', '',$myrow2['typename']),
                        $myrow2['phoneno'],
                        $myrow2['faxno']);

			}
		echo '</DIV>';
		}
		if (!isset($_POST['CSV'])) {
  			DB_data_seek($result, ($_POST['PageOffset']-1)*$_SESSION['DisplayRecordsMax']);
  		}

		while ($myrow=DB_fetch_array($result)) {
			//Blance
			$Blance = 0;
			$DebNo = $myrow['debtorno'];
			$BlanceResult = DB_query("SELECT SUM(debtortrans.ovamount + debtortrans.ovgst 
				+ debtortrans.ovfreight + debtortrans.ovdiscount- debtortrans.alloc) AS balance
									FROM debtortrans
									WHERE debtortrans.debtorno='" . $DebNo . "' 
									ORDER BY '".$DebNo."'",
									$db);
			if (DB_num_rows($BlanceResult) == 0){
				$Blance = 0;
			} else {
				$BlanceRow = DB_fetch_row($BlanceResult);
				$Blance = $BlanceRow[0];
			}			
			if($Blance > 0){
				$BalanceColor = 'RED';
			}else{
				$BalanceColor = '#666666';
			}

			//<input type="submit" name="Select", value=%s >
			//printf("<td><a href='A_CustomerOverview.php?" . SID . "&DebtorNo=%s'>%s</td>
			printf("<td><input type='submit' name='Select', value='%s - %s'></td>
				<td>%s</td>
				<td ALIGN=RIGHT><FONT COLOR=$BalanceColor>%10.2f</FONT></td>
				<td>%s</td>
				<td>%s</td>
				<td>%s</FONT></td>
				<td>%s</FONT></td>
				<td>%s</FONT></td></tr>",
				$myrow['debtorno'],
				$myrow['branchcode'],
				$myrow['name'],
				$Blance,$myrow['currcode'],
				$myrow['contactname'],
				$myrow['typename'],
				$myrow['phoneno'],
				$myrow['faxno']);

			$j++;

    		$RowIndex++;
//end of page full new headings if
		}
		//end of while loop
		echo '</TABLE>';
	}
}


//end if results to show


?>