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
//echo '<P CLASS="page_subtitle_text">' . ' No Customers';
}

if (!isset($_SESSION['CustomerType'])){ //initialise if not already done
	$_SESSION['CustomerType']="";
}

if (isset($_POST['DebtorNo'])){
	$DebtorNo = $_POST['DebtorNo'];
} elseif (isset($_GET['DebtorNo'])){
	$DebtorNo = $_GET['DebtorNo'];
}

$_SESSION['CustomerID']=$DebtorNo ;

$msg="";

if (isset($Errors)) {
	unset($Errors);
}
$Errors = array();

echo '<P CLASS="page_title_text">' . ' ' . _('Customer') . ' : ' . $DebtorNo . ' </P>';
echo '<HR>';
//----------------------CustomerOverview begin --------------------//
//DebtorNo exists - either passed when calling the form or from the form itself

//echo '>>Customer Overview';
	echo "<FORM METHOD='post' action='" . $_SERVER['PHP_SELF'] . '?' . SID ."'>";


	if (!isset($_POST['New'])) {
		$sql = "SELECT debtorno,
				name,
				address1,
				address2,
				address3,
				address4,
				address5,
				address6,
				currcode,
				salestype,
				clientsince,
				holdreason,
				paymentterms,
				discount,
				discountcode,
				pymtdiscount,
				creditlimit,
				invaddrbranch,
				taxref,
				customerpoline,
				typeid
				FROM debtorsmaster
			WHERE debtorno = '" . $DebtorNo . "'";

		$ErrMsg = _('The customer details could not be retrieved because');
		$result = DB_query($sql,$db,$ErrMsg);


		$myrow = DB_fetch_array($result);

		// if $AutoDebtorNo in config.php has not been set or if it has been set to a number less than one,
		//then display the DebtorNo //
		//if ($_SESSION['AutoDebtorNo']== 0 )  {
		//	echo '<TR><TD>' . _('Customer Code') . ":</TD>
		//		<TD>" . $DebtorNo . "</TD></TR>";
		//}
		$_POST['CustName'] = $myrow['name'];
		$_POST['Address1']  = $myrow['address1'];
		$_POST['Address2']  = $myrow['address2'];
		$_POST['Address3']  = $myrow['address3'];
		$_POST['Address4']  = $myrow['address4'];
		$_POST['Address5']  = $myrow['address5'];
		$_POST['Address6']  = $myrow['address6'];
		$_POST['SalesType'] = $myrow['salestype'];
		$_POST['CurrCode']  = $myrow['currcode'];
		$_POST['ClientSince'] = ConvertSQLDate($myrow['clientsince']);
		$_POST['HoldReason']  = $myrow['holdreason'];
		$_POST['PaymentTerms']  = $myrow['paymentterms'];
		$_POST['Discount']  = $myrow['discount'] * 100; // Sherifoz 21.6.03 convert to displayable percentage
		$_POST['DiscountCode']  = $myrow['discountcode'];
		$_POST['PymtDiscount']  = $myrow['pymtdiscount'] * 100; // Sherifoz 21.6.03 convert to displayable percentage
		$_POST['CreditLimit']	= $myrow['creditlimit'];
		$_POST['InvAddrBranch'] = $myrow['invaddrbranch'];
		$_POST['TaxRef'] = $myrow['taxref'];
		$_POST['CustomerPOLine'] = $myrow['customerpoline'];
		$_POST['typeid'] = $myrow['typeid'];

		//echo "<INPUT TYPE=HIDDEN NAME='DebtorNo' VALUE='" . $DebtorNo . "'>";
		//$EditImg =$rootpath.'/css/'.$theme.'/images/edit_inline.gif';
		//$InquiryImg =$rootpath.'/css/'.$theme.'/images/view_inline.gif';	
		
		//$ItemDetailEditLink = $rootpath . '/Stocks.php?' . SID . 'StockID=' .$StockID;			

		echo '<TABLE width="100%">';
		echo '	<TR><Th>Customer Code</Th> <TD>'.$DebtorNo.'</TD> <Th>Sales Type/Price List:</TD> <TD> '.$_POST['SalesType'].'</TD></TR> 
				<TR><Th>Customer Name</Th> <TD>'.$myrow['name'].'</TD> <Th>Discount Percent(code)</TD> <TD> '.$_POST['Discount'].'-'.$_POST['DiscountCode'].' </TD></TR> 
				<TR><Th>Address1,2</Th> <TD>'.$_POST['Address1'].$_POST['Address2'].' </TD> <Th>Payment Discount%</TD> <TD>'.$_POST['PymtDiscount'].'</TD></TR> 
				<TR><Th>Address3,4</Th> <TD>'.$_POST['Address3'].$_POST['Address4'].'</TD> <Th>Credit Limit</TD> <TD>'.$_POST['CreditLimit'].'</TD></TR> 
				<TR><Th>Address5,6</Th> <TD>'.$_POST['Address5'].$_POST['Address6'].'</TD> <Th>Payment Terms</TD> <TD>'.$_POST['PaymentTerms'].' </TD></TR> 
				<TR><Th>Since (Y/m/d)</Th> <TD>'.$_POST['ClientSince'].'</TD> <Th>Credit Status</TD> <TD> '.$_POST['HoldReason'].'</TD></TR>
				<TR><Th>Customer Type</Th> <TD>'.$_POST['Address5'].$_POST['Address6'].'</TD> <Th>Customers Currency</TD> <TD> '.$_POST['CurrCode'].'</TD></TR>				
				<TR><Th>Tax Reference</Th> <TD>'.$_POST['TaxRef'].'</TD> <Th>Invoice Addressing</TD> <TD> $DebtorNo </TD></TR>			
				</TABLE>';		
				
		echo "<input onclick=\"javascript:window.location.href='Customers.php?DebtorNo=$DebtorNo'\"  type='button' value='Edit Customer Detail' />";		
		echo "<input onclick=\"javascript:window.location.href='CustomerBranches.php?DebtorNo=$DebtorNo'\"  type='button' value='Edit Customer Branch' />";	
        
        echo "<input onclick=\"javascript:window.location.href='CustomerInquiry.php?CustomerID=$DebtorNo'\"  type='button' value='All Transactions' />";			
		}
/*		
	echo '<TR><TD>' . _('Customer Name') . ':</TD>
		<TD><input ' . (in_array('CustName',$Errors) ?  'class="inputerror"' : '' ) .' type="Text" name="CustName" value="' . $_POST['CustName'] . '" SIZE=42 MAXLENGTH=40></TD></TR>';
	echo '<TR><TD>' . _('Address Line 1 (Street)') . ':</TD>
		<TD><input ' . (in_array('Address1',$Errors) ?  'class="inputerror"' : '' ) .' type="Text" name="Address1" SIZE=42 MAXLENGTH=40 value="' . $_POST['Address1'] . '"></TD></TR>';
	echo '<TR><TD>' . _('Address Line 2 (Suburb/City)') . ':</TD>
		<TD><input ' . (in_array('Address2',$Errors) ?  'class="inputerror"' : '' ) .' type="Text" name="Address2" SIZE=42 MAXLENGTH=40 value="' . $_POST['Address2'] . '"></TD></TR>';
	echo '<TR><TD>' . _('Address Line 3 (State/Province)') . ':</TD>
		<TD><input ' . (in_array('Address3',$Errors) ?  'class="inputerror"' : '' ) .' type="Text" name="Address3" SIZE=42 MAXLENGTH=40 value="' . $_POST['Address3'] . '"></TD></TR>';
	echo '<TR><TD>' . _('Address Line 4 (Postal Code)') . ':</TD>
		<TD><input ' . (in_array('Address4',$Errors) ?  'class="inputerror"' : '' ) .' type="Text" name="Address4" SIZE=42 MAXLENGTH=40 value="' . $_POST['Address4'] . '"></TD></TR>';
	echo '<TR><TD>' . _('Address Line 5') . ':</TD>
		<TD><input ' . (in_array('Address5',$Errors) ?  'class="inputerror"' : '' ) .' type="Text" name="Address5" SIZE=42 MAXLENGTH=40 value="' . $_POST['Address5'] . '"></TD></TR>';
	echo '<TR><TD>' . _('Address Line 6') . ':</TD>
		<TD><input ' . (in_array('Address6',$Errors) ?  'class="inputerror"' : '' ) .' type="Text" name="Address6" SIZE=42 MAXLENGTH=40 value="' . $_POST['Address6'] . '"></TD></TR>';	
 
// Show Sales Type drop down list

	$result=DB_query('SELECT typeabbrev, sales_type FROM salestypes ',$db);
	if (DB_num_rows($result)==0){
		$DataError =1;
		echo '<TR><TD COLSPAN=2>' . prnMsg(_('No sales types/price lists defined'),'error') . '</TD></TR>';
	} else {
		echo '<TR><TD>' . _('Sales Type/Price List') . ":</TD>
			<TD><SELECT tabindex=9 name='SalesType'>";

		while ($myrow = DB_fetch_array($result)) {
			echo "<OPTION VALUE='". $myrow['typeabbrev'] . "'>" . $myrow['sales_type'];
		} //end while loop
		DB_data_seek($result,0);
		echo '</SELECT></TD></TR>';
	}
// Show Customer Type drop down list
        $result=DB_query('SELECT typeid, typename FROM debtortype ',$db);
        if (DB_num_rows($result)==0){
                $DataError =1;
                echo '<TR><TD COLSPAN=2>' . prnMsg(_('No Customer types/price lists defined'),'error') . '</TD></TR>';
        } else {
                echo '<TR><TD>' . _('Customer Type') . ":</TD>
                        <TD><SELECT tabindex=9 name='typeid'>";

                while ($myrow = DB_fetch_array($result)) {
                        echo "<OPTION VALUE='". $myrow['typeid'] . "'>" . $myrow['typename'];
                } //end while loop
                DB_data_seek($result,0);
                echo '</SELECT></TD></TR>';
        }

	$DateString = Date($_SESSION['DefaultDateFormat']);
	echo '<TR><TD>' . _('Customer Since') . ' (' . $_SESSION['DefaultDateFormat'] . "):</TD><TD><input tabindex=10 type='Text' name='ClientSince' value=$DateString SIZE=12 MAXLENGTH=10></TD></TR>";
	echo '<TR><TD>' . _('Discount Percent') . ":</TD>
		<TD><input tabindex=11 type='Text' name='Discount' value=0 SIZE=5 MAXLENGTH=4></TD></TR>";
	echo '<TR><TD>' . _('Discount Code') . ":</TD>
		<TD><input tabindex=12 type='Text' name='DiscountCode' SIZE=3 MAXLENGTH=2></TD></TR>";
	echo '<TR><TD>' . _('Payment Discount Percent') . ":</TD>
		<TD><input tabindex=13 type='Text' name='PymtDiscount' value=0 SIZE=5 MAXLENGTH=4></TD></TR>";
	echo '<TR><TD>' . _('Credit Limit') . ":</TD>
		<TD><input tabindex=14 type='Text' name='CreditLimit' value=" . $_SESSION['DefaultCreditLimit'] . " SIZE=16 MAXLENGTH=14></TD></TR>";
	echo '<TR><TD>' . _('Tax Reference') . ":</TD>
		<TD><input tabindex=15 type='Text' name='TaxRef' SIZE=22 MAXLENGTH=20></TD></TR>";

	$result=DB_query('SELECT terms, termsindicator FROM paymentterms',$db);
	if (DB_num_rows($result)==0){
		$DataError =1;
		echo '<TR><TD COLSPAN=2>' . prnMsg(_('There are no payment terms currently defined - go to the setup tab of the main menu and set at least one up first'),'error') . '</TD></TR>';
	} else {

		echo '<TR><TD>' . _('Payment Terms') . ":</TD>
			<TD><SELECT tabindex=15 name='PaymentTerms'>";

		while ($myrow = DB_fetch_array($result)) {
			echo "<OPTION VALUE='". $myrow['termsindicator'] . "'>" . $myrow['terms'];
		} //end while loop
		DB_data_seek($result,0);

		echo '</SELECT></TD></TR>';
	}
	echo '<TR><TD>' . _('Credit Status') . ":</TD><TD><SELECT tabindex=16 name='HoldReason'>";

	$result=DB_query('SELECT reasoncode, reasondescription FROM holdreasons',$db);
	if (DB_num_rows($result)==0){
		$DataError =1;
		echo '<TR><TD COLSPAN=2>' . prnMsg(_('There are no credit statuses currently defined - go to the setup tab of the main menu and set at least one up first'),'error') . '</TD></TR>';
	} else {
		while ($myrow = DB_fetch_array($result)) {
			echo "<OPTION VALUE='". $myrow['reasoncode'] . "'>" . $myrow['reasondescription'];
		} //end while loop
		DB_data_seek($result,0);
		echo '</SELECT></TD></TR>';
	}

	$result=DB_query('SELECT currency, currabrev FROM currencies',$db);
	if (DB_num_rows($result)==0){
		$DataError =1;
		echo '<TR><TD COLSPAN=2>' . prnMsg(_('There are no currencies currently defined - go to the setup tab of the main menu and set at least one up first'),'error') . '</TD></TR>';
	} else {
		if (!isset($_POST['CurrCode'])){
			$CurrResult = DB_query('SELECT currencydefault FROM companies WHERE coycode=1',$db);
			$myrow = DB_fetch_row($CurrResult);
			$_POST['CurrCode'] = $myrow[0];
		}
		echo '<TR><TD>' . _('Customer Currency') . ":</TD><TD><SELECT tabindex=17 name='CurrCode'>";
		while ($myrow = DB_fetch_array($result)) {
			if ($_POST['CurrCode']==$myrow['currabrev']){
				echo '<OPTION SELECTED VALUE='. $myrow['currabrev'] . '>' . $myrow['currency'];
			} else {
				echo '<OPTION VALUE='. $myrow['currabrev'] . '>' . $myrow['currency'];
			}
		} //end while loop
		DB_data_seek($result,0);

		echo '</SELECT></TD></TR></TABLE>';
	}
	*/
  
//----------------------CustomerOverview end   --------------------//

//----------------------Transactions start ------------------------//
// always figure out the SQL required from the inputs available

if(!isset($_GET['CustomerID']) AND !isset($_SESSION['CustomerID'])){
	prnMsg(_('To display the enquiry a customer must first be selected from the customer selection screen'),'info');
	echo "<BR><CENTER><A HREF='". $rootpath . "/SelectCustomer.php?" . SID . "'>" . _('Select a Customer to Inquire On') . '</A><BR></CENTER>';
	include('includes/footer.inc');
	exit;
} else {
	if (isset($_GET['CustomerID'])){
		$_SESSION['CustomerID'] = $_GET['CustomerID'];
	}
	$CustomerID = $_SESSION['CustomerID'];
}

$CustomerID = $DebtorNo;
if (!isset($_POST['TransAfterDate'])) {
	$_POST['TransAfterDate'] = Date($_SESSION['DefaultDateFormat'],Mktime(0,0,0,Date('m')-6,Date('d'),Date('Y')));
}

$SQL = 'SELECT debtorsmaster.name,
		currencies.currency,
		paymentterms.terms,
		debtorsmaster.creditlimit,
		holdreasons.dissallowinvoices,
		holdreasons.reasondescription,
		SUM(debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount
- debtortrans.alloc) AS balance,
		SUM(CASE WHEN (paymentterms.daysbeforedue > 0) THEN
			CASE WHEN (TO_DAYS(Now()) - TO_DAYS(debtortrans.trandate)) >= paymentterms.daysbeforedue
			THEN debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc ELSE 0 END
		ELSE
			CASE WHEN TO_DAYS(Now()) - TO_DAYS(DATE_ADD(DATE_ADD(debtortrans.trandate, ' . INTERVAL('1', 'MONTH') . '), ' . INTERVAL('(paymentterms.dayinfollowingmonth - DAYOFMONTH(debtortrans.trandate))', 'DAY') . ')) >= 0 THEN debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc ELSE 0 END
		END) AS due,
		SUM(CASE WHEN (paymentterms.daysbeforedue > 0) THEN
			CASE WHEN TO_DAYS(Now()) - TO_DAYS(debtortrans.trandate) > paymentterms.daysbeforedue
			AND TO_DAYS(Now()) - TO_DAYS(debtortrans.trandate) >= (paymentterms.daysbeforedue + ' .
		$_SESSION['PastDueDays1'] . ')
			THEN debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc ELSE 0 END
		ELSE
			CASE WHEN (TO_DAYS(Now()) - TO_DAYS(DATE_ADD(DATE_ADD(debtortrans.trandate, ' . INTERVAL('1', 'MONTH') . '), ' . INTERVAL('(paymentterms.dayinfollowingmonth - DAYOFMONTH(debtortrans.trandate))','DAY') . ')) >= ' . $_SESSION['PastDueDays1'] . ')
			THEN debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount
			- debtortrans.alloc ELSE 0 END
		END) AS overdue1,
		SUM(CASE WHEN (paymentterms.daysbeforedue > 0) THEN
			CASE WHEN TO_DAYS(Now()) - TO_DAYS(debtortrans.trandate) > paymentterms.daysbeforedue
			AND TO_DAYS(Now()) - TO_DAYS(debtortrans.trandate) >= (paymentterms.daysbeforedue + ' . $_SESSION['PastDueDays2'] . ') THEN debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc ELSE 0 END
		ELSE
			CASE WHEN (TO_DAYS(Now()) - TO_DAYS(DATE_ADD(DATE_ADD(debtortrans.trandate, ' . INTERVAL('1','MONTH') . '), ' . INTERVAL('(paymentterms.dayinfollowingmonth - DAYOFMONTH(debtortrans.trandate))','DAY') . ')) >= ' . $_SESSION['PastDueDays2'] . ") THEN debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc ELSE 0 END
		END) AS overdue2
		FROM debtorsmaster,
     			paymentterms,
     			holdreasons,
     			currencies,
     			debtortrans
		WHERE  debtorsmaster.paymentterms = paymentterms.termsindicator
     		AND debtorsmaster.currcode = currencies.currabrev
     		AND debtorsmaster.holdreason = holdreasons.reasoncode
     		AND debtorsmaster.debtorno = '" . $CustomerID . "'
     		AND debtorsmaster.debtorno = debtortrans.debtorno
		GROUP BY debtorsmaster.name,
			currencies.currency,
			paymentterms.terms,
			paymentterms.daysbeforedue,
			paymentterms.dayinfollowingmonth,
			debtorsmaster.creditlimit,
			holdreasons.dissallowinvoices,
			holdreasons.reasondescription";

$ErrMsg = _('The customer details could not be retrieved by the SQL because');
$CustomerResult = DB_query($SQL,$db,$ErrMsg);

if (DB_num_rows($CustomerResult)==0){

	/*Because there is no balance - so just retrieve the header information about the customer - the choice is do one query to get the balance and transactions for those customers who have a balance and two queries for those who don't have a balance OR always do two queries - I opted for the former */

	$NIL_BALANCE = True;

	$SQL = "SELECT debtorsmaster.name, currencies.currency, paymentterms.terms,
	debtorsmaster.creditlimit, holdreasons.dissallowinvoices, holdreasons.reasondescription
	FROM debtorsmaster,
	     paymentterms,
	     holdreasons,
	     currencies
	WHERE
	     debtorsmaster.paymentterms = paymentterms.termsindicator
	     AND debtorsmaster.currcode = currencies.currabrev
	     AND debtorsmaster.holdreason = holdreasons.reasoncode
	     AND debtorsmaster.debtorno = '" . $CustomerID . "'";

	$ErrMsg =_('The customer details could not be retrieved by the SQL because');
	$CustomerResult = DB_query($SQL,$db,$ErrMsg);

} else {
	$NIL_BALANCE = False;
}

$CustomerRecord = DB_fetch_array($CustomerResult);

if ($NIL_BALANCE==True){
	$CustomerRecord['balance']=0;
	$CustomerRecord['due']=0;
	$CustomerRecord['overdue1']=0;
	$CustomerRecord['overdue2']=0;
}
echo '<P CLASS="page_subtitle_text">' . ' >>Status';
/*
	echo '<P CLASS="page_title_text"><img src="'.$rootpath.'/css/'.$theme.'/images/customer.png" TITLE="' . 
	_('Customer') . '" ALT="">' . ' ' . _('Customer') . ' : ' . $CustomerRecord['name'] . ' - (' . _('All amounts stated in') . 
	' ' . $CustomerRecord['currency'] . ')<BR><BR>' . _('Terms') . ' : ' . $CustomerRecord['terms'] . '<BR>' . _('Credit Limit') . 
	': ' . number_format($CustomerRecord['creditlimit'],0) . ' ' . _('Credit Status') . ': ' . $CustomerRecord['reasondescription'] . '';
*/
if ($CustomerRecord['dissallowinvoices']!=0){
	echo '<BR><FONT COLOR=RED SIZE=4><B>' . _('ACCOUNT ON HOLD') . '</FONT></B><BR>';
}

echo "<TABLE width='100%'>
	<TR>
		<th>" . _('Total Balance') . "</th>
		<th>" . _('Current') . "</th>
		<th>" . _('Now Due') . "</th>
		<th>" . $_SESSION['PastDueDays1'] . "-" . $_SESSION['PastDueDays2'] . ' ' . _('Days Overdue') . "</th>
		<th>" . _('Over') . ' ' . $_SESSION['PastDueDays2'] . ' ' . _('Days Overdue') . '</th></TR>';

echo '<TR><TD ALIGN=RIGHT>' . number_format($CustomerRecord['balance'],2) . '</TD>
	<TD ALIGN=RIGHT>' . number_format(($CustomerRecord['balance'] - $CustomerRecord['due']),2) . '</TD>
	<TD ALIGN=RIGHT>' . number_format(($CustomerRecord['due']-$CustomerRecord['overdue1']),2) . '</TD>
	<TD ALIGN=RIGHT>' . number_format(($CustomerRecord['overdue1']-$CustomerRecord['overdue2']) ,2) . '</TD>
	<TD ALIGN=RIGHT>' . number_format($CustomerRecord['overdue2'],2) . '</TD>
	</TR>
	</TABLE>';

echo '<P CLASS="page_subtitle_text">' . ' >>Transactions(Month)'.'</P>';
/*	
echo "<BR><CENTER><FORM ACTION='" . $_SERVER['PHP_SELF'] . "' METHOD=POST>";

echo _('Show all transactions after') . ": <INPUT tabindex=1 type=text name='TransAfterDate' Value='" . $_POST['TransAfterDate'] . "' MAXLENGTH =10 SIZE=12>" .
		"	<INPUT tabindex=2 TYPE=SUBMIT NAME='Refresh Inquiry' VALUE='" . _('Refresh Inquiry') . "'></FORM><BR>";
*/

//$DateAfterCriteria = FormatDateForSQL($_POST['TransAfterDate']);
$DateAfterCriteria = Date($_SESSION['DefaultDateFormat'],Mktime(0,0,0,Date('m'),1,Date('Y')));

$SQL = "SELECT systypes.typename,
		debtortrans.id,
		debtortrans.type,
		debtortrans.transno,
		debtortrans.branchcode,
		debtortrans.trandate,
		debtortrans.reference,
		debtortrans.invtext,
		debtortrans.order_,
		debtortrans.rate,
		(debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount) AS totalamount,
		debtortrans.alloc AS allocated
	FROM debtortrans,
		systypes
	WHERE debtortrans.type = systypes.typeid
	AND debtortrans.debtorno = '" . $CustomerID . "'
	AND debtortrans.trandate >= '$DateAfterCriteria'
	ORDER BY debtortrans.id";

$ErrMsg = _('No transactions were returned by the SQL because');
$TransResult = DB_query($SQL,$db,$ErrMsg);

if (DB_num_rows($TransResult)==0){
	echo _('There are no transactions to display since') . ' ' . $_POST['TransAfterDate'];
	include('includes/footer.inc');
	exit;
}
/*show a table of the invoices returned by the SQL */

echo '<TABLE width="100%">';

$tableheader = "<TR BGCOLOR =#800000>
		<TH>" . _('Type') . "</TH>
		<TH>" . _('Number') . "</TH>
		<TH>" . _('Date') . "</TH>
		<TH>" . _('Branch') . "</TH>
		<TH>" . _('Reference') . "</TH>
		<TH>" . _('Comments') . "</TH>
		<TH>" . _('Order') . "</TH>
		<TH>" . _('Total') . "</TH>
		<TH>" . _('Allocated') . "</TH>
		<TH>" . _('Balance') . "</TH>
		<TH>" . _('More Info') . "</TH>
		<TH>" . _('More Info') . "</TH>
		<TH>" . _('More Info') . "</TH></TR>";

echo $tableheader;


$j = 1;
$k=0; //row colour counter
while ($myrow=DB_fetch_array($TransResult)) {

	if ($k==1){
		echo '<tr class="EvenTableRows">';
		$k=0;
	} else {
		echo '<tr class="OddTableRows">';
		$k=1;
	}

	$FormatedTranDate = ConvertSQLDate($myrow['trandate']);

	$base_formatstr = "<td>%s</td>
				<td>%s</td>
				<td>%s</td>
				<td>%s</td>
				<td>%s</td>
				<td width='200'>%s</td>
				<td>%s</td>
				<td ALIGN=RIGHT>%s</td>
				<td ALIGN=RIGHT>%s</td>
				<td ALIGN=RIGHT>%s</td>";
	$credit_invoice_str = "<td><a href='%s/Credit_Invoice.php?InvoiceNumber=%s'>" . _('Credit ') ."<IMG SRC='%s/credit.gif' TITLE='" . _('Click to credit the invoice') . "'></a></td>";
	$preview_invoice_str = "<td><a target='_blank' href='%s/PrintCustTrans.php?FromTransNo=%s&InvOrCredit=Invoice'>" . _('Preview ') . "<IMG SRC='%s/preview.gif' TITLE='" . _('Click to preview the invoice') . "'></a></td>
		<td><a target='_blank' href='%s/EmailCustTrans.php?FromTransNo=%s&InvOrCredit=Invoice'>" . _('Email ') . "<IMG SRC='%s/email.gif' TITLE='" . _('Click to email the invoice') . "'></a></td>";
	$preview_credit_str = "<td><a target='_blank' href='%s/PrintCustTrans.php?FromTransNo=%s&InvOrCredit=Credit'>" . _('Preview') . " <IMG SRC='%s/preview.gif' TITLE='" . _('Click to preview the credit note') . "'></a></td>
				<td><a target='_blank' href='%s/EmailCustTrans.php?FromTransNo=%s&InvOrCredit=Credit'>" . _('Email') . " <IMG SRC='%s/email.gif' TITLE='" . _('Click to email the credit note') . "'></a></td>";

	if (in_array(3,$_SESSION['AllowedPageSecurityTokens']) && $myrow['type']==10){ /*Show a link to allow an invoice to be credited */

		if ($_SESSION['CompanyRecord']['gllink_debtors']== 1 AND in_array(8,$_SESSION['AllowedPageSecurityTokens'])){
			printf($base_formatstr .
				$credit_invoice_str .
				$preview_invoice_str .
				"<td><A HREF='%s/GLTransInquiry.php?%s&TypeID=%s&TransNo=%s'>" . _('View GL Entries') . "<A></td>
				</tr>",
				$myrow['typename'],
				$myrow['transno'],
				ConvertSQLDate($myrow['trandate']),
				$myrow['branchcode'],
				$myrow['reference'],
				$myrow['invtext'],
				$myrow['order_'],
				number_format($myrow['totalamount'],2),
				number_format($myrow['allocated'],2),
				number_format($myrow['totalamount']-$myrow['allocated'],2),
				$rootpath,
				$myrow['transno'],
				$rootpath.'/css/'.$theme.'/images',
				$rootpath,
				$myrow['transno'],
				$rootpath.'/css/'.$theme.'/images',
				$rootpath,
				$myrow['transno'],
				$rootpath.'/css/'.$theme.'/images',
				$rootpath,
				SID,
				$myrow['type'],
				$myrow['transno']);
		} else {
			printf($base_formatstr .
				$credit_invoice_str .
				$preview_invoice_str .
				'</tr>',
				$myrow['typename'],
				$myrow['transno'],
				ConvertSQLDate($myrow['trandate']),
				$myrow['branchcode'],
				$myrow['reference'],
				$myrow['invtext'],
				$myrow['order_'],
				number_format($myrow['totalamount'],2),
				number_format($myrow['allocated'],2),
				number_format($myrow['totalamount']-$myrow['allocated'],2),
				$rootpath,
				$myrow['transno'],
				$rootpath.'/css/'.$theme.'/images',
				$rootpath, $myrow['transno'],
				$rootpath.'/css/'.$theme.'/images',
				$rootpath,
				$myrow['transno'],
				$rootpath.'/css/'.$theme.'/images');
		}

	} elseif($myrow['type']==10) { /*its an invoice but not high enough priveliges to credit it */

		printf($base_formatstr .
			$preview_invoice_str .
			'</tr>',
			$myrow['typename'],
			$myrow['transno'],
			ConvertSQLDate($myrow['trandate']),
			$myrow['branchcode'],
			$myrow['reference'],
			$myrow['invtext'],
			$myrow['order_'],
			number_format($myrow['totalamount'],2),
			number_format($myrow['allocated'],2),
			number_format($myrow['totalamount']-$myrow['allocated'],2),
			$rootpath,
			$myrow['transno'],
			$rootpath.'/css/'.$theme.'/images',
			$rootpath,
			$myrow['transno'],
			$rootpath.'/css/'.$theme.'/images');

	} elseif ($myrow['type']==11) { /*its a credit note */
		if ($_SESSION['CompanyRecord']['gllink_debtors']== 1 AND in_array(8,$_SESSION['AllowedPageSecurityTokens'])){
			printf($base_formatstr .
				$preview_credit_str .
				"<td><a href='%s/CustomerAllocations.php?AllocTrans=%s'>" . _('Allocation') . "<IMG SRC='%s/allocation.gif' TITLE='" . _('Click to allocate funds') . "'></a></td>
				<td><A HREF='%s/GLTransInquiry.php?%sTypeID=%s&TransNo=%s'>" . _('View GL Entries') . '<A></td></tr>',
				$myrow['typename'],
				$myrow['transno'],
				ConvertSQLDate($myrow['trandate']),
				$myrow['branchcode'],
				$myrow['reference'],
				$myrow['invtext'],
				$myrow['order_'],
				number_format($myrow['totalamount'],2),
				number_format($myrow['allocated'],2),
				number_format($myrow['totalamount']-$myrow['allocated'],2),
				$rootpath,
				$myrow['transno'],
				$rootpath.'/css/'.$theme.'/images',
				$rootpath,
				$myrow['transno'],
				$rootpath.'/css/'.$theme.'/images',
				$rootpath,
				$myrow['id'],
				$rootpath.'/css/'.$theme.'/images',
				$rootpath,
				SID,
				$myrow['type'],
				$myrow['transno']);
		} else {
			printf($base_formatstr .
				$preview_credit_str .
				"<td><a href='%s/CustomerAllocations.php?AllocTrans=%s'>" . _('Allocation') . "<IMG SRC='%s/allocation.gif' TITLE='" . _('Click to allocate funds') . "'></a></td>
				</tr>",
				$myrow['typename'],
				$myrow['transno'],
				ConvertSQLDate($myrow['trandate']),
				$myrow['branchcode'],
				$myrow['reference'],
				$myrow['invtext'],
				$myrow['order_'],
				number_format($myrow['totalamount'],2),
				number_format($myrow['allocated'],2),
				number_format($myrow['totalamount']-$myrow['allocated'],2),
				$rootpath,
				$myrow['transno'],
				$rootpath.'/css/'.$theme.'/images',
				$rootpath,
				$myrow['transno'],
				$rootpath.'/css/'.$theme.'/images',
				$rootpath,
				$myrow['id'],
				$rootpath.'/css/'.$theme.'/images');
		}
	} elseif ($myrow['type']==12 AND $myrow['totalamount']<0) { /*its a receipt  which could have an allocation*/
		if ($_SESSION['CompanyRecord']['gllink_debtors']== 1 AND in_array(8,$_SESSION['AllowedPageSecurityTokens'])){
			printf($base_formatstr .
				"<td><a href='%s/CustomerAllocations.php?AllocTrans=%s'>" . _('Allocation') . "</a></td>
				<td><A HREF='%s/GLTransInquiry.php?%s&TypeID=%s&TransNo=%s'>" . _('View GL Entries') . "<A></td>
				</tr>",
				$myrow['typename'],
				$myrow['transno'],
				ConvertSQLDate($myrow['trandate']),
				$myrow['branchcode'],
				$myrow['reference'],
				$myrow['invtext'],
				$myrow['order_'],
				number_format($myrow['totalamount'],2),
				number_format($myrow['allocated'],2),
				number_format($myrow['totalamount']-$myrow['allocated'],2),
				$rootpath,
				$myrow['id'],
				$rootpath,
				SID,
				$myrow['type'],
				$myrow['transno']);
		} else {
			printf($base_formatstr .
				"<td><a href='%s/CustomerAllocations.php?AllocTrans=%s'>" . _('Allocation') . "</a></td>
				</tr>",
				$myrow['typename'],
				$myrow['transno'],
				ConvertSQLDate($myrow['trandate']),
				$myrow['branchcode'],
				$myrow['reference'],
				$myrow['invtext'],
				$myrow['order_'],
				number_format($myrow['totalamount'],2),
				number_format($myrow['allocated'],2),
				number_format($myrow['totalamount']-$myrow['allocated'],2),
				$rootpath,
				$myrow['id']);
		}
	} elseif ($myrow['type']==12 AND $myrow['totalamount']>0) { /*its a negative receipt */
		if ($_SESSION['CompanyRecord']['gllink_debtors']== 1 AND in_array(8,$_SESSION['AllowedPageSecurityTokens'])){
			printf($base_formatstr .
				"<td><A HREF='%s/GLTransInquiry.php?%s&TypeID=%s&TransNo=%s'>" . _('View GL Entries') . '<A></td></tr>',
				$myrow['typename'],
				$myrow['transno'],
				ConvertSQLDate($myrow['trandate']),
				$myrow['branchcode'],
				$myrow['reference'],
				$myrow['invtext'],
				$myrow['order_'],
				number_format($myrow['totalamount'],2),
				number_format($myrow['allocated'],2),
				number_format($myrow['totalamount']-$myrow['allocated'],2),
				$rootpath,
				SID,
				$myrow['type'],
				$myrow['transno']);
		} else {
			printf($base_formatstr . '<td></tr>',
				$myrow['typename'],
				$myrow['transno'],
				ConvertSQLDate($myrow['trandate']),
				$myrow['branchcode'],
				$myrow['reference'],
				$myrow['invtext'],
				$myrow['order_'],
				number_format($myrow['totalamount'],2),
				number_format($myrow['allocated'],2),
				number_format($myrow['totalamount']-$myrow['allocated'],2));
		}
	} else {
		if ($_SESSION['CompanyRecord']['gllink_debtors']== 1 AND in_array(8,$_SESSION['AllowedPageSecurityTokens'])){
			printf($base_formatstr .
				"<td><A HREF='%s/GLTransInquiry.php?%s&TypeID=%s&TransNo=%s'>" . _('View GL Entries') . '<A></td></tr>',
				$myrow['typename'],
				$myrow['transno'],
				ConvertSQLDate($myrow['trandate']),
				$myrow['branchcode'],
				$myrow['reference'],
				$myrow['invtext'],
				$myrow['order_'],
				number_format($myrow['totalamount'],2),
				number_format($myrow['allocated'],2),
				number_format($myrow['totalamount']-$myrow['allocated'],2),
				$rootpath,
				SID,
				$myrow['type'],
				$myrow['transno']);
		} else {
			printf($base_formatstr . '</tr>',
				$myrow['typename'],
				$myrow['transno'],
				ConvertSQLDate($myrow['trandate']),
				$myrow['branchcode'],
				$myrow['reference'],
				$myrow['invtext'],
				$myrow['order_'],
				number_format($myrow['totalamount'],2),
				number_format($myrow['allocated'],2),
				number_format($myrow['totalamount']-$myrow['allocated'],2));
		}
	}

}
//end of while loop
echo '</table>';
//----------------------Transactions end --------------------------//

echo '<P class="page_subtitle_text"> >>Outstanding Orders </P>';
//-- Outstanging Customer orders --///////////////////////////////////
$_POST['SearchOrders'] = '%';
	if (isset($_POST['SearchOrders'])){
			// Quincy: Add some attr to show, key attr is salesorderdetails.orderlineno 	
				$SQL = "SELECT salesorders.orderno, 
						salesorderdetails.unitprice,
						salesorderdetails.stkcode,
						salesorderdetails.quantity,
						salesorderdetails.orderlineno,
						salesorderdetails.qtyinvoiced,
						SUM(salesorderdetails.quantity - salesorderdetails.qtyinvoiced) AS qtyunsent,
						debtorsmaster.debtorno , 
						custbranch.brname, 
						salesorders.customerref, 
						salesorders.orddate, 
						salesorders.deliverto, 
						salesorders.deliverydate, SUM(salesorderdetails.unitprice*salesorderdetails.quantity*(1-salesorderdetails.discountpercent)) AS ordervalue 
					FROM salesorders, 
						salesorderdetails, 
						debtorsmaster, 
						custbranch 
					WHERE salesorders.orderno = salesorderdetails.orderno 
					AND salesorders.debtorno = debtorsmaster.debtorno 
					AND salesorders.branchcode = custbranch.branchcode 
					AND debtorsmaster.debtorno = custbranch.debtorno 					
					AND salesorders.quotation=0 
					AND salesorderdetails.quantity > salesorderdetails.qtyinvoiced
					AND debtorsmaster.debtorno = '" . $CustomerID . "'
					GROUP BY  salesorders.orddate, 
						salesorders.orderno,
						salesorderdetails.orderlineno,
						debtorsmaster.debtorno , 					
						salesorders.deliverydate,  
						salesorders.deliverto
					ORDER BY debtorsmaster.debtorno,salesorders.orddate";
					//ORDER BY salesorders.orddate";
					
		$SalesOrdersResult = DB_query($SQL,$db);

		if (DB_error_no($db) !=0) {
			echo '<BR>' . _('No orders were returned by the SQL because') . ' ' . DB_error_msg($db);
			echo "<BR>$SQL";
		}
	}	
	
	If (isset($SalesOrdersResult)) {
	//show a table of the orders returned by the SQL //	
	echo "<input onclick=\"javascript:window.location.href='SelectOrderItems.php?&NewOrder=Yes'\"  type='button' value='Add New' />";
	echo "<input onclick=\"javascript:window.location.href='SelectCompletedCustOrder.php?'\" type='button' value='All Customer Orders' />";
	//echo "<input onclick=\"javascript:window.location.href='SelectCompletedOrder.php?'\" type='button' value='All Transactions' />";	
	
		
		echo '<TABLE width="100%">';

		// Quincy: Sicomm Customer PO table format
		$tableheader = "<TR><TH>" . _('Order Date') . "</TH>
				<TH>" . _('Customer') . "</TH>
				<TH>" . _('Cust Order') . " #</TH> 
				<TH>" . _('Order Line') . "</TH>			
				<TH>" . _('Product') . "</TH>
				<TH>" . _('Price') . "</TH>
				<TH>" . _('Qty') . "</TH>	
				<TH>" . _('Req Del Date') . "</TH>								
				<TH>" . _('Qty Unsent') . "</TH> 
				<TH>" . _('Invoice') . " </TH></TR>";
		echo $tableheader;

		$j = 1;
		$k=0; //row colour counter
		while ($myrow=DB_fetch_array($SalesOrdersResult)) {


			if ($k==1){
				echo '<tr class="EvenTableRows">';
				$k=0;
			} else {
				echo '<tr class="OddTableRows">';
				$k=1;
			}

			// Quincy: Sicomm Customer PO table format
			//$ViewPage = $rootpath . '/OrderDetails.php?' .SID . '&OrderNumber=' . $myrow['orderno'];
			$FormatedDelDate = ConvertSQLDate($myrow['deliverydate']);
			$FormatedOrderDate = ConvertSQLDate($myrow['orddate']);
			$FormatedOrderValue = number_format($myrow['ordervalue'],2);
			$ViewPage = $rootpath . '/ConfirmDispatch_Invoice.php?' . SID . '&OrderNumber=' .$myrow['orderno'];	
			printf("<td>%s</td>
				<td>%s</td>		
				<td><FONT COLOR=GREEN>%s<FONT></td>
				<td>%s</td>
				<td>%s</td>
				<td>%s</td>
				<td>%s</td>
				<td>%s</td>	
				<td>%s</td>					
				<td><A HREF='%s'>%s</A></td>		
				</tr>", 
				$FormatedOrderDate,	
				$myrow['debtorno'],								
				$myrow['customerref'],		
				$myrow['orderlineno'], 
				$myrow['stkcode'], 
				$myrow['unitprice'], 
				$myrow['quantity'],
				$FormatedDelDate, 						
				$myrow['qtyunsent'],		
				$ViewPage, 
				$myrow['orderno']);
		//end of while loop
		}
		echo '</TABLE>';

	}
	
include('includes/footer.inc');	
?>

<script language="javascript" type="text/javascript">
        function addnew()
        {
            var   ret   =   prompt("Pls insert con","");   
			/*
              document.getElementById("TextBox1").value=ret;*/
        }
    </script>


<script language="JavaScript" type="text/javascript">
    //<![CDATA[
            <!--
            document.forms[0].CustCode.select();
            document.forms[0].CustCode.focus();
            //-->
    //]]>
</script>
