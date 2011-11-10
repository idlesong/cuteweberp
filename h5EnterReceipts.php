<?php

include('includes/DefineReceiptClass.php');

$PageSecurity = 3;
include('includes/session.inc');

$title = _('Receipt Entry');

//include('includes/header.inc');
include('includes/SQL_CommonFunctions.inc');

echo '<link href="/weberp/css/silverwolf/default.css" rel="stylesheet" type="text/css">';
echo '<meta charset="utf-8">';

$msg='';

echo '<p class=page_title_text>Enter receipts</p> <hr>';

//printf("<td><a href='A_CustomerOverview.php?" . SID . "&DebtorNo=%s'>%s</td>
if (isset($_GET['DebtorNo']) AND $_GET['DebtorNo']!=''){
	$_POST['NewSelectedCustomer']=$_GET['DebtorNo'];
	unset($_SESSION['Receipt']);
	
	//$_SESSION['Receipt'] = new Receipt($Amount, $Customer, $Discount, $Narrative, $this->ItemCounter, $GLCode, $PayeeBankDetail, $CustomerName, $tag);
}

if(isset($_POST['Confirm'])){
//echo 'CustID:'.$_POST['CustomerID'].'Amount:'.$_POST['Amount'].'CustomerName:'.$_POST['CustomerName'];

	if(isset($_SESSION['Receipt']) and $_SESSION['Receipt'] != ''){ //its not a GL item - its a customer receipt then
 		   /*Accumulate the total debtors credit including discount */
		   //$BatchDebtorTotal += (($ReceiptItem->Discount + $ReceiptItem->Amount)/$_SESSION['ReceiptBatch']->ExRate/$_SESSION['ReceiptBatch']->FunctionalExRate);
		   /*Create a DebtorTrans entry for each customer deposit */
		   
		   $ReceiptTransNo = GetNextTransNo(12,$db);
		   $ReceiptRecvDate = FormatDateForSQL(Date($_SESSION['DefaultDateFormat']));
		   $PeriodNo = GetPeriod($ReceiptRecvDate,$db);
		   
			include('includes/GetPaymentMethods.php');
			/* The array ReceiptTypes is defined from the setup tab of the main menu under payment methods - 
			the array is populated from the include file GetPaymentMethods.php */
			$ReceiptType = $ReceiptTypes[0];
			
			$ReceiptCurrency = $_SESSION['CompanyRecord']['currencydefault'];
		   

		   $SQL = 'INSERT INTO debtortrans (transno,
		   					type,
							debtorno,
							branchcode,
							trandate,
							prd,
							reference,
							tpe,
							rate,
							ovamount,
							ovdiscount,
							invtext)
		   			VALUES (' . $ReceiptTransNo . ",
		   				12,
						'" . $_SESSION['Receipt']->Customer . "',
						'',
						'" . $ReceiptRecvDate . "',
						'" . $PeriodNo . "',
						'" . $ReceiptType  . ' ' . $_SESSION['Receipt']->Narrative . "',
						'',
						1,
						" . -$_SESSION['Receipt']->Amount . ",
						" . -$_SESSION['Receipt']->Discount . ",
						'" . $_SESSION['Receipt']->PayeeBankDetail . "'
					)";
			$DbgMsg = _('The SQL that failed to insert the customer receipt transaction was');
			$ErrMsg = _('Cannot insert a receipt transaction against the customer because') ;
			$result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);

			$SQL = "UPDATE debtorsmaster SET lastpaiddate = '" . $ReceiptRecvDate . "',
											lastpaid=" . $_SESSION['Receipt']->Amount ."
									WHERE debtorsmaster.debtorno='" . $_SESSION['Receipt']->Customer . "'";

			$DbgMsg = _('The SQL that failed to update the date of the last payment received was');
			$ErrMsg = _('Cannot update the customer record for the date of the last payment received because');
			$result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);
			
			// refresh parant window and close window
			echo '<script type="text/javascript">	
			window.opener.location.href = window.opener.location.href;
			if (window.opener.progressWindow)
			{
				window.opener.progressWindow.close();
			}
			window.close();
			</script>';
			exit;
			

	} //end of if its a customer receipt
										
										
}

if (isset($_POST['SaveReceipt'])){

	//if (!Is_Date($_POST['DateBanked'])){
	//	$_POST['DateBanked'] = Date($_SESSION['DefaultDateFormat']);
	//}

   // Check Null
   if( $_SESSION['CustomerRecord']['debtorno'] != '' and $_POST['Amount'] != ''){
	   unset( $_SESSION['Receipt'] );
	   if($_POST['Discount'] == ''){ //Set 0 by default
		  $_POST['Discount'] = 0;
	   }
	   $_SESSION['Receipt'] = new Receipt($_POST['Amount'],
										$_SESSION['CustomerRecord']['debtorno'],//$Customer, 
										$_POST['Discount'], 
										$_POST['Narrative'], 
										'',//$this->ItemCounter, 
										'',//$GLCode, 
										'',//$PayeeBankDetail, 
										$_SESSION['CustomerRecord']['name'],//$CustomerName, 
										''//$tag
										);
	}
}

if (isset($_POST['Cancel'])){

	// refresh parant window and close window
	echo '<script type="text/javascript">	
	window.opener.location.href = window.opener.location.href;
	if (window.opener.progressWindow)
	{
		window.opener.progressWindow.close();
	}
	window.close();
	</script>';
	exit;
}

//a customer has been selected, then get _SESSION['CustomerRecord']	
If (isset($_POST['NewSelectedCustomer'])) { 
	//$_POST['CustomerID']=$_POST['NewSelectedCustomer'];
	/*need to get currency sales type - payment discount percent and GL code
	as well as payment terms and credit status and hold the lot as session variables
	the receipt held entirely as session variables until the button clicked to process*/

	if (isset($_SESSION['CustomerRecord'])){
	   unset($_SESSION['CustomerRecord']);
	}

	$SQL = 'SELECT debtorsmaster.name,
			debtorsmaster.debtorno,
			debtorsmaster.pymtdiscount,
			debtorsmaster.currcode,
			currencies.currency,
			currencies.rate,
			paymentterms.terms,
			debtorsmaster.creditlimit,
			holdreasons.dissallowinvoices,
			holdreasons.reasondescription,
			SUM(debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc) AS balance,
			SUM(CASE WHEN paymentterms.daysbeforedue > 0  THEN
				CASE WHEN (TO_DAYS(Now()) - TO_DAYS(debtortrans.trandate)) >= paymentterms.daysbeforedue  THEN debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc ELSE 0 END
			ELSE
				CASE WHEN TO_DAYS(Now()) - TO_DAYS(DATE_ADD(DATE_ADD(debtortrans.trandate, ' . INTERVAL('1','MONTH') . '), ' . INTERVAL('(paymentterms.dayinfollowingmonth - DAYOFMONTH(debtortrans.trandate))','DAY') . ')) >= 0 THEN debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc ELSE 0 END
			END) AS due,
			SUM(CASE WHEN paymentterms.daysbeforedue > 0 THEN
				CASE WHEN TO_DAYS(Now()) - TO_DAYS(debtortrans.trandate) > paymentterms.daysbeforedue	AND TO_DAYS(Now()) - TO_DAYS(debtortrans.trandate) >= (paymentterms.daysbeforedue + ' . $_SESSION['PastDueDays1'] . ') THEN debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight - debtortrans.ovdiscount - debtortrans.alloc ELSE 0 END
			ELSE
				CASE WHEN (TO_DAYS(Now()) - TO_DAYS(DATE_ADD(DATE_ADD(debtortrans.trandate, ' . INTERVAL('1', 'MONTH') .'), ' . INTERVAL('(paymentterms.dayinfollowingmonth - DAYOFMONTH(debtortrans.trandate))', 'DAY') . ')) >= ' . $_SESSION['PastDueDays1'] . ') THEN debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc ELSE 0 END
			END) AS overdue1,
			SUM(CASE WHEN paymentterms.daysbeforedue > 0 THEN
				CASE WHEN TO_DAYS(Now()) - TO_DAYS(debtortrans.trandate) > paymentterms.daysbeforedue AND TO_DAYS(Now()) - TO_DAYS(debtortrans.trandate) >= (paymentterms.daysbeforedue + ' . $_SESSION['PastDueDays2'] . ') THEN debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc ELSE 0 END
			ELSE
				CASE WHEN (TO_DAYS(Now()) - TO_DAYS(DATE_ADD(DATE_ADD(debtortrans.trandate, ' . INTERVAL('1','MONTH') . '), ' . INTERVAL('(paymentterms.dayinfollowingmonth - DAYOFMONTH(debtortrans.trandate))','DAY') . ')) >= ' . $_SESSION['PastDueDays2'] . ") THEN debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc ELSE 0 END
			END) AS overdue2
			FROM debtorsmaster,
				paymentterms,
				holdreasons,
				currencies,
				debtortrans
			WHERE debtorsmaster.paymentterms = paymentterms.termsindicator
			AND debtorsmaster.currcode = currencies.currabrev
			AND debtorsmaster.holdreason = holdreasons.reasoncode
			AND debtorsmaster.debtorno = '" . $_POST['NewSelectedCustomer'] . "'
			AND debtorsmaster.debtorno = debtortrans.debtorno
			GROUP BY debtorsmaster.name,
				debtorsmaster.pymtdiscount,
				debtorsmaster.currcode,
				currencies.currency,
				currencies.rate,
				paymentterms.terms,
				debtorsmaster.creditlimit,
				paymentterms.daysbeforedue,
				paymentterms.dayinfollowingmonth,
				debtorsmaster.creditlimit,
				holdreasons.dissallowinvoices,
				holdreasons.reasondescription";

	$ErrMsg = _('The customer details could not be retrieved because');
	$DbgMsg = _('The SQL that failed was');
	$CustomerResult = DB_query($SQL,$db,$ErrMsg, $DbgMsg);

	$_SESSION['CustomerRecord'] = DB_fetch_array($CustomerResult);
	
	//echo 'CustID:'.$_SESSION['CustomerRecord']['debtorno']
	//     .'-Cust CurCode:'.$_SESSION['CustomerRecord']['currcode']
	//	 .'-Balance:'.$_SESSION['CustomerRecord']['balance'];
	
} /*end of if customer has just been selected  all info required read into $_SESSION['CustomerRecord']*/

echo '<form action="' . $_SERVER['PHP_SELF'] . '?' . SID . '" method=post>';

if(isset($_SESSION['CustomerRecord']) and $_SESSION['CustomerRecord'] != ''){
	$CustRec = $_SESSION['CustomerRecord'];
	echo '<table class="commontable">
			<caption>Enter Customer Receipts</caption><tr>
			<th>' . _('Customer') . '</th>
			<th>'.$CustRec['debtorno']. '</th><th>' .$CustRec['name']. '</th></tr>
			
			<tr><td>' . _('Balance/Overdue($)') . '</td>	
			<td>' .$CustRec['balance'].'('.$CustRec['currcode']. ')</td><td>'.$CustRec['due']. '('.$CustRec['currcode'].')</td>
			</tr>';

    if( isset($_SESSION['Receipt'] )){//Customer Selected, Enter the Receipt Amount	
		echo '<td>Amount Received</td><td>'.$_SESSION['Receipt']->Amount.'</td>
			  <td>Discount:'.$_SESSION['Receipt']->Discount.'</td></tr>
			  <tr><td>Remark</td><td colspan="2">'.$_SESSION['Receipt']->Narrative.'</td>';
	}else{
		echo '<td>Amount Received</td><td><input type="text" name="Amount" onKeyPress="return restrictToNumbers(this, event)"  onChange="numberFormat(this,2)" onFocus="return setTextAlign(this, '."'".'right'."'".')" value="' . $_POST['Amount'] . '"></td>';
		echo '<td>Discount<input type="text" name="Discount" onKeyPress="return restrictToNumbers(this, event)"  onChange="numberFormat(this,2)" onFocus="return setTextAlign(this, '."'".'right'."'".')" value="' . $_POST['Discount'] . '"></td></tr>';
		echo '<tr><td>Remark</td><td colspan="2"><input type="text" name="Narrative" value="' . $_POST['Narrative'] . '"></td>';
	}
	echo '</tr></table>';

	
	if( isset($_SESSION['Receipt'] )){
		echo '<p align=right> <input type=submit name="Cancel" VALUE="' . _('Cancel & Close') . '"><input type=submit name="Confirm" VALUE="' . _('Confirm to Enter Receipts') . '"></p>';
	}else{
		echo '<p align=right> <input type=submit name="Cancel" VALUE="' . _('Cancel & Close') . '"><input type=submit name="SaveReceipt" VALUE="' . _('Save Receipt') . '"></p>';
	}
}

echo '</form>';


//include('includes/footer.inc');
?>