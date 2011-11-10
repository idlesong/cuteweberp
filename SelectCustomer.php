<?php
/* $Revision: 1.48 $ */

$PageSecurity = 2;

include('includes/session.inc');
$title = _('Search Customers');
include('includes/header.inc');
include('includes/Wiki.php');
include('includes/SQL_CommonFunctions.inc');

if (isset($_GET['Select'])) {
	$_SESSION['CustomerID']=$_GET['Select'];
}
if (!isset($_SESSION['CustomerID'])){ //initialise if not already done
	$_SESSION['CustomerID']="";
}

if ($_SESSION['CustomerID'] ==""){
//echo '<P CLASS="page_title_text"><img src="'.$rootpath.'/css/'.$theme.'/images/customer.png" TITLE="' . _('Customer') . '" ALT="">' . ' ' . _('Customers:Home') . '';
echo '<P CLASS="page_title_text">' . ' ' . _('Customers:Home') . '';
}

if (!isset($_SESSION['CustomerType'])){ //initialise if not already done
	$_SESSION['CustomerType']="";
}

$msg="";

if (isset($_POST['Go1']) or isset($_POST['Go2'])) {
  $_POST['PageOffset'] = (isset($_POST['Go1']) ? $_POST['PageOffset1'] : $_POST['PageOffset2']);
  $_POST['Go'] = '';
}

if (!isset($_POST['PageOffset'])) {
  $_POST['PageOffset'] = 1;
} else {
  if ($_POST['PageOffset']==0) {
    $_POST['PageOffset'] = 1;
  }
}

//Default setting: customer type=default
$_POST['Search']='%';
unset($_SESSION['CustomerID']);

if (isset($_POST['Search']) OR isset($_POST['CSV']) OR isset($_POST['Go']) OR isset($_POST['Next']) OR isset($_POST['Previous'])){
	if (isset($_POST['Search'])){
		$_POST['PageOffset'] = 1;
	}
	if ($_POST['Keywords'] AND (($_POST['CustCode']) OR ($_POST['CustPhone']) OR ($_POST['CustType']))) {
		$msg=_('Search Result: Customer Name has been used in search') . '<br>';
		$_POST['Keywords'] = strtoupper($_POST['Keywords']);
	}
	if ($_POST['CustCode'] AND $_POST['CustPhone']=="" AND isset($_POST['CustType']) AND $_POST['Keywords']=="") {
		$msg=_('Search Result: Customer Code has been used in search') . '<br>';
	}
	if (($_POST['CustPhone']) AND ($_POST['CustType'])) {
		$msg=_('Search Result: Customer Phone has been used in search') . '<br>';
	}
	if ($_POST['CustType'] AND $_POST['CustPhone']=="" AND $_POST['CustCode']=="" AND $_POST['Keywords']==""){
		$msg=_('Search Result: Customer Type has been used in search') . '<br>';
	}
	if (($_POST['Keywords']=="") AND ($_POST['CustCode']=="") AND ($_POST['CustPhone']=="") AND ($_POST['CustType']=="")) {

		$SQL= "SELECT debtorsmaster.debtorno,
				debtorsmaster.name,
				debtorsmaster.address1,
                                debtorsmaster.address2,
                                debtorsmaster.address3,
                                debtorsmaster.address4,
				debtorsmaster.currcode,
				custbranch.brname,
				custbranch.contactname,
				debtortype.typename,
				custbranch.phoneno,
				custbranch.faxno
			FROM debtorsmaster LEFT JOIN custbranch
				ON debtorsmaster.debtorno = custbranch.debtorno, debtortype
			WHERE debtorsmaster.typeid = debtortype.typeid
			ORDER BY debtorsmaster.debtorno";
		
			//echo '----Look here';
			
	} else {
		if (strlen($_POST['Keywords'])>0) {

			$_POST['Keywords'] = strtoupper(trim($_POST['Keywords']));

			//insert wildcard characters in spaces

			$i=0;
			$SearchString = "%";

			while (strpos($_POST['Keywords'], " ", $i)) {
				$wrdlen=strpos($_POST['Keywords']," ",$i) - $i;
				$SearchString=$SearchString . substr($_POST['Keywords'],$i,$wrdlen) . "%";
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
				SUM(debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount
		- debtortrans.alloc) AS balance,								
				custbranch.brname,
				custbranch.contactname,
				debtortype.typename,
				custbranch.phoneno,
				custbranch.faxno
			FROM debtorsmaster LEFT JOIN custbranch
				ON debtorsmaster.debtorno = custbranch.debtorno, debtortype, debtortrans
			WHERE debtorsmaster.name " . LIKE . " '$SearchString'
			AND debtorsmaster.typeid = debtortype.typeid
			AND debtortrans.debtorno = debtorsmaster.debtorno 
			ORDER BY debtorsmaster.name";

		} elseif (strlen($_POST['CustCode'])>0){

			$_POST['CustCode'] = strtoupper(trim($_POST['CustCode']));
				$SQL = "SELECT debtorsmaster.debtorno,
				debtorsmaster.name,
				debtorsmaster.address1,
                                debtorsmaster.address2,
                                debtorsmaster.address3,
                                debtorsmaster.address4,
				debtorsmaster.currcode,
				custbranch.brname,
				custbranch.contactname,
				debtortype.typename,
				custbranch.phoneno,
				custbranch.faxno
			FROM debtorsmaster LEFT JOIN custbranch
				ON debtorsmaster.debtorno = custbranch.debtorno, debtortype
			WHERE debtorsmaster.debtorno " . LIKE  . " '%" . $_POST['CustCode'] . "%'
			AND debtorsmaster.typeid = debtortype.typeid
			ORDER BY debtorsmaster.debtorno";
			
			//echo '----Look there';
		} elseif (strlen($_POST['CustPhone'])>0){
			$SQL = "SELECT debtorsmaster.debtorno,
				debtorsmaster.name,
				debtorsmaster.address1,
                                debtorsmaster.address2,
                                debtorsmaster.address3,
                                debtorsmaster.address4,
				debtorsmaster.currcode,				
				custbranch.brname,
				custbranch.contactname,
				debtortype.typename,
				custbranch.phoneno,
				custbranch.faxno
			FROM debtorsmaster LEFT JOIN custbranch
				ON debtorsmaster.debtorno = custbranch.debtorno, debtortype
			WHERE custbranch.phoneno " . LIKE  . " '%" . $_POST['CustPhone'] . "%'
			AND debtorsmaster.typeid = debtortype.typeid
			ORDER BY debtorsmaster.debtorno";
			
		} elseif (strlen($_POST['CustType'])>0){
                        $SQL = "SELECT debtorsmaster.debtorno,
                                debtorsmaster.name,
                                debtorsmaster.address1,
                                debtorsmaster.address2,
                                debtorsmaster.address3,
                                debtorsmaster.address4,
								debtorsmaster.currcode,
                                custbranch.brname,
                                custbranch.contactname,
                                debtortype.typename,
                                custbranch.phoneno,
                                custbranch.faxno
                        FROM debtorsmaster LEFT JOIN custbranch
                                ON debtorsmaster.debtorno = custbranch.debtorno, debtortype
                        WHERE debtorsmaster.typeid LIKE debtortype.typeid
                        AND debtortype.typename = '" . $_POST['CustType'] . "'
                        ORDER BY debtorsmaster.debtorno";
		}
	} //one of keywords or custcode or custphone was more than a zero length string
	$ErrMsg = _('The searched customer records requested cannot be retrieved because');
	$result = DB_query($SQL,$db,$ErrMsg);
	if (DB_num_rows($result)==1){
		$myrow=DB_fetch_array($result);
		$_POST['Select'] = $myrow['debtorno'];
		unset($result);
	} elseif (DB_num_rows($result)==0){
		prnMsg(_('No customer records contain the selected text') . ' - ' . _('please alter your search criteria and try again'),'info');
	}
} //end of if search


if (!isset($_POST['Select'])){
	$_POST['Select']="";
}
?>

<FORM ACTION="<?php echo $_SERVER['PHP_SELF'] . '?' . SID; ?>" METHOD=POST>
<B><?php echo $msg; ?></B>

<?php 
echo '<HR>';
?>


<TABLE class="searchtable" >
<TR>
<TD><?php echo _('Customer Name'); ?>:</TD>
<TD>
<?php
if (isset($_POST['Keywords'])) {
	?>
	<INPUT TYPE="Text" NAME="Keywords" value="<?php echo $_POST['Keywords']?>" SIZE=20 MAXLENGTH=25>
	<?php
} else {
	?>
	<INPUT TYPE="Text" NAME="Keywords" SIZE=20 MAXLENGTH=25>
	<?php
}
?>
</TD>
<TD><?php echo _('Customer Code'); ?>:</TD>
<TD>
<?php
if (isset($_POST['CustCode'])) {
	?>
	<INPUT TYPE="Text" NAME="CustCode" value="<?php echo $_POST['CustCode'] ?>" SIZE=15 MAXLENGTH=18>
	<?php
} else {
	?>
	<INPUT TYPE="Text" NAME="CustCode" SIZE=15 MAXLENGTH=18>
	<?php
}
?>
</TD>
<TD><?php echo _('Customer Phone#'); ?>:</TD>
<TD>
<?php
if (isset($_POST['CustPhone'])) {
	?>
	<INPUT TYPE="Text" NAME="CustPhone" value="<?php echo $_POST['CustPhone'] ?>" SIZE=15 MAXLENGTH=18>
	<?php
} else {
	?>
	<INPUT TYPE="Text" NAME="CustPhone" SIZE=15 MAXLENGTH=18>
	<?php
}
?>
</TD>

</TR>
<TR>
<TD><?php echo _('Customer Type'); ?>:</TD>
<TD>

<?php
if (isset($_POST['CustType'])) {
	// Show Customer Type drop down list
	$result2=DB_query('SELECT typeid, typename FROM debtortype ',$db);
	// Error if no customer types setup
	if (DB_num_rows($result2)==0){
		$DataError =1;
		echo '<TR><TD COLSPAN=2>' . prnMsg(_('No Customer types defined'),'error') . '</TD></TR>';
	} else {
		// If OK show select box with option selected
		echo '<SELECT NAME="CustType">';
		while ($myrow = DB_fetch_array($result2)) {
			if ($_POST['CustType']==$myrow['typename']){
            	echo "<OPTION SELECTED VALUE='". $myrow['typename'] . "'>" . $myrow['typename'];
            } else {
				echo "<OPTION VALUE='". $myrow['typename'] . "'>" . $myrow['typename'];
			}
		} //end while loop
		DB_data_seek($result2,0);
		echo '</SELECT></TD></TR>';
	}
} else {
	// No option selected yet, so show Customer Type drop down list
	$result2=DB_query('SELECT typeid, typename FROM debtortype ',$db);
	// Error if no customer types setup
	if (DB_num_rows($result2)==0){
		$DataError =1;
		echo '<TR><TD COLSPAN=2>' . prnMsg(_('No Customer types defined'),'error') . '</TD></TR>';
	} else {
		// if OK show select box with available options to choose
		echo '<SELECT NAME="CustType">';
		while ($myrow = DB_fetch_array($result2)) {
			echo "<OPTION VALUE='". $myrow['typename'] . "'>" . $myrow['typename'];
		} //end while loop
		DB_data_seek($result2,0);
		echo '</SELECT></TD></TR>';
	}
}
?>
</TD>
</TR>
<TR>
<TD>
Basic Search
</TD>
<TD>
<INPUT TYPE=SUBMIT NAME="Search" VALUE="<?php echo _('Search Now'); ?>">
</TD>
<TD>
<INPUT TYPE=SUBMIT NAME="CSV" VALUE="<?php echo _('CSV Format'); ?>">
</TD>
</TR>
</TABLE>

<BR>

<?php
if (isset($_SESSION['SalesmanLogin']) and $_SESSION['SalesmanLogin']!=''){
	prnMsg(_('Your account enables you to see only customers allocated to you'),'warn',_('Note: Sales-person Login'));
}

if (isset($result)) {
	unset($_SESSION['CustomerID']);
	$ListCount=DB_num_rows($result);
	$ListPageMax=ceil($ListCount/$_SESSION['DisplayRecordsMax']);
	if (!isset($_POST['CSV'])) {
		if (isset($_POST['Next'])) {
			if ($_POST['PageOffset'] < $ListPageMax) {
				$_POST['PageOffset'] = $_POST['PageOffset'] + 1;
			}
		}

		if (isset($_POST['Previous'])) {
			if ($_POST['PageOffset'] > 1) {
				$_POST['PageOffset'] = $_POST['PageOffset'] - 1;
			}
		}

		echo "<INPUT TYPE=\"hidden\" NAME=\"PageOffset\" VALUE=\"". $_POST['PageOffset'] ."\"/>";

		if ($ListPageMax >1) {
			echo "<CENTER><P>&nbsp;&nbsp;" . $_POST['PageOffset'] . ' ' . _('of') . ' ' . $ListPageMax . ' ' . _('pages') . '. ' . _('Go to Page') . ': ';

			echo '<SELECT NAME="PageOffset1">';

			$ListPage=1;
			while($ListPage <= $ListPageMax) {
				if ($ListPage == $_POST['PageOffset']) {
					echo '<OPTION VALUE=' . $ListPage . ' SELECTED>' . $ListPage . '</OPTION>';
				} else {
					echo '<OPTION VALUE=' . $ListPage . '>' . $ListPage . '</OPTION>';
				}
				$ListPage++;
			}
			echo '</SELECT>
				<INPUT TYPE=SUBMIT NAME="Go1" VALUE="' . _('Go') . '">
				<INPUT TYPE=SUBMIT NAME="Previous" VALUE="' . _('Previous') . '">
				<INPUT TYPE=SUBMIT NAME="Next" VALUE="' . _('Next') . '">';
 			echo '<P>';
		}

		echo "<input onclick=\"javascript:window.location.href='Customers.php?'\"  type='button' value='Add Customer' />";
		//echo "<input onclick=\"javascript:window.location.href='CustomerReceipt.php?&NewReceipt=Yes'\"  type='button' value='Enter Receipts' />";
		//echo "<input onclick=\"javascript:window.location.href='CustomerAllocations.php'\"  type='button' value='Allocate Receipts' />";
		/*echo '<CENTER><BR><TABLE CELLPADDING=2 COLSPAN=7 BORDER=2>';*/
		echo '<CENTER><TABLE width="100%">';
		$TableHeader = '<TR>
				<TH>' . _('Code') . '</TH>
				<TH>' . _('Customer Name') . '</TH>
				<TH>' . _('Balance') . '</TH>
				<TH>' . _('$') . '</TH>
				<TH>' . _('Receipt') . '</TH>
				<TH>' . _('Contact') . '</TH>
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

		while (($myrow=DB_fetch_array($result)) AND ($RowIndex <> $_SESSION['DisplayRecordsMax'])) {
			//Blance
			$Blance = 0;
			$DebNo = $myrow['debtorno'];
			$BlanceResult = DB_query("SELECT SUM(debtortrans.ovamount + debtortrans.ovgst 
				+ debtortrans.ovfreight + debtortrans.ovdiscount- debtortrans.alloc) AS balance
									FROM debtortrans
									WHERE debtortrans.debtorno='" . $DebNo . "'",
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
			
			//Receipts not alloc yet
			$ReceiptsResult = DB_query("SELECT SUM(debtortrans.ovamount + debtortrans.ovgst 
				+ debtortrans.ovfreight + debtortrans.ovdiscount- debtortrans.alloc) AS receipts
									FROM debtortrans
									WHERE debtortrans.type = '12' AND debtortrans.debtorno='" . $DebNo . "'",
									$db);			
									
			if (DB_num_rows($ReceiptsResult) == 0){
				$Receipts = 0;
			} else {
				$ReceiptsRow = DB_fetch_row($ReceiptsResult);
				$Receipts = $ReceiptsRow[0];
			}									
									

			if ($k==1){
				echo '<tr class="EvenTableRows">';
				$k=0;
			} else {
				echo '<tr class="OddTableRows">';
				$k=1;
			}

			$recvReceiptImg = $rootpath.'/css/'.$theme.'/images/recvgold.gif';
			$ViewReceiptsImg =$rootpath.'/css/'.$theme.'/images/view_inline.gif';
			$AllocReceiptsLink = $rootpath.'/CustomerAllocations.php ';
			$paraCustomerID = $myrow['debtorno'];
			
			//<input onclick=openSubpage('h5ItemsSearch.php') type='image' src=$recvReceiptImg value='RecvReceipts' />
				
			printf("<td><a href='A_CustomerOverview.php?" . SID . "&DebtorNo=%s'>%s</td>
				<td>%s</td>
				<td ALIGN=RIGHT><FONT COLOR=$BalanceColor>%10.2f</FONT> <input onclick=openParaSubpage('h5EnterReceipts.php?&','DebtorNo=','$paraCustomerID') type='image' src=$recvReceiptImg value='RecvReceipts' /></td>
				<td>%s</td>
				<td ALIGN=RIGHT>%10.2f <A target='_self' HREF=$AllocReceiptsLink><img src=$ViewReceiptsImg></A></td>
				<td>%s</FONT></td>
				<td>%s</FONT></td>
				<td>%s</FONT></td></tr>",
				$myrow['debtorno'],
				$myrow['debtorno'],
				$myrow['name'],
				$Blance,$myrow['currcode'],
				$Receipts,			
				$myrow['contactname'],
				$myrow['phoneno'],
				$myrow['faxno']);

			$j++;
/*			
			if ($j == 11 AND ($RowIndex+1 != $_SESSION['DisplayRecordsMax'])){
				$j=1;
				echo $TableHeader;
			}
*/
    		$RowIndex++;
//end of page full new headings if
		}
		//end of while loop
		echo '</TABLE>';
	}
}
include('includes/footer.inc');
?>
<script language="JavaScript" type="text/javascript">
    //<![CDATA[
            <!--
            document.forms[0].CustCode.select();
            document.forms[0].CustCode.focus();
            //-->
    //]]>
</script>
