<?php

/* $Revision: 1.33 $ */

include('includes/DefineReceiptClass.php');

$PageSecurity = 3;
include('includes/session.inc');

$title = _('Receipt Entry');

include('includes/header.inc');
include('includes/SQL_CommonFunctions.inc');

$msg='';

echo '<p class=page_title_text>Enter receipts</p> <hr>';
/*
* msgs and vars
*/
if (isset($_GET['NewOrder'])){
	unset($_SESSION['ReceiptBatch']->Items);
	unset($_SESSION['ReceiptBatch']);
	unset($_SESSION['CustomerRecord']); 
	$_SESSION['RequireReceiptHeader'] = 1;
}

if( isset($_SESSION['msg_SelectedCustomer']) and $_SESSION['msg_SelectedCustomer'] != ''){
	//$_POST['NewSelectedCustomer'] = $_SESSION['msg_SelectedCustomer'];
	$_POST['NewSelectedCustomer'] = substr($_SESSION['msg_SelectedCustomer'],0,strpos($_SESSION['msg_SelectedCustomer'],' - '));
	unset ($_SESSION['msg_SelectedCustomer']);
}

/*
*   POST & GET refections
*/
if (isset($_POST['SaveBankInfo'])){

	$_SESSION['ReceiptBatch']->Account = $_POST['BankAccount'];
	/*Get the bank account currency and set that too */

	$SQL = 'SELECT bankaccountname, currcode FROM bankaccounts WHERE accountcode=' . $_POST['BankAccount'];
	$ErrMsg =_('The bank account name cannot be retrieved because');
	$result= DB_query($SQL,$db,$ErrMsg);

	if (DB_num_rows($result)==1){
		$myrow = DB_fetch_row($result);
		$_SESSION['ReceiptBatch']->BankAccountName = $myrow[0];
		$_SESSION['ReceiptBatch']->AccountCurrency=$myrow[1];
		unset($result);
	} elseif (DB_num_rows($result)==0){
		prnMsg( _('The bank account number') . ' ' . $_POST['BankAccount'] . ' ' . _('is not set up as a bank account'),'error');
		include ('includes/footer.inc');
		exit;
	}

	if (!Is_Date($_POST['DateBanked'])){
		$_POST['DateBanked'] = Date($_SESSION['DefaultDateFormat']);
	}
	$_SESSION['ReceiptBatch']->DateBanked = $_POST['DateBanked'];
	if (isset($_POST['ExRate']) and $_POST['ExRate']!=''){
		if (is_numeric($_POST['ExRate'])){
			$_SESSION['ReceiptBatch']->ExRate = $_POST['ExRate'];
		} else {
			prnMsg(_('The exchange rate entered should be numeric'),'warn');
		}
	}
	if (isset($_POST['FunctionalExRate']) and $_POST['FunctionalExRate']!=''){
		if (is_numeric($_POST['FunctionalExRate'])){
			$_SESSION['ReceiptBatch']->FunctionalExRate=$_POST['FunctionalExRate']; //ex rate between receipt currency and account currency
		} else {
			prnMsg(_('The functional exchange rate entered should be numeric'),'warn');
		}
	}
	$_SESSION['ReceiptBatch']->ReceiptType = $_POST['ReceiptType'];

	if (!isset($_POST['Currency'])){
		$_POST['Currency']=$_SESSION['CompanyRecord']['currencydefault'];
	}

	if ($_SESSION['ReceiptBatch']->Currency!=$_POST['Currency']){

		$_SESSION['ReceiptBatch']->Currency=$_POST['Currency']; //receipt currency
		/*Now customer receipts entered using the previous currency need to be ditched
		and a warning message displayed if there were some customer receipted entered */
		if (count($_SESSION['ReceiptBatch']->Items)>0){
			unset($_SESSION['ReceiptBatch']->Items);
			prnMsg(_('Changing the currency of the receipt means that existing entries need to be re-done - only customers trading in the selected currency can be selected'),'warn');
		}

	}

	/*Get the exchange rate between the functional currecny and the receipt currency*/
	$result = DB_query("SELECT rate FROM currencies WHERE currabrev='" . $_SESSION['ReceiptBatch']->Currency . "'",$db);
	$myrow = DB_fetch_row($result);
	$tableExRate = $myrow[0]; //this is the rate of exchange between the functional currency and the receipt currency

	if ($_POST['Currency']==$_SESSION['ReceiptBatch']->AccountCurrency){
		$_SESSION['ReceiptBatch']->ExRate = 1; //ex rate between receipt currency and account currency
		$SuggestedExRate=1;
	}
	if ($_SESSION['ReceiptBatch']->AccountCurrency==$_SESSION['CompanyRecord']['currencydefault']){
		$_SESSION['ReceiptBatch']->FunctionalExRate = 1;
		$SuggestedFunctionalExRate =1;
		$SuggestedExRate = $tableExRate;

	} else {
		/*To illustrate the rates required
			Take an example functional currency NZD receipt in USD from an AUD bank account
			1 NZD = 0.80 USD
			1 NZD = 0.90 AUD
			The FunctionalExRate = 0.90 - the rate between the functional currency and the bank account currency
			The receipt ex rate is the rate at which one can sell the received currency and purchase the bank account currency
			or 0.8/0.9 = 0.88889
		*/

		/*Get suggested FunctionalExRate */
		$result = DB_query("SELECT rate FROM currencies WHERE currabrev='" . $_SESSION['ReceiptBatch']->AccountCurrency . "'",$db);
		$myrow = DB_fetch_row($result);
		$SuggestedFunctionalExRate = $myrow[0];

		/*Get the exchange rate between the functional currecny and the receipt currency*/
		$result = DB_query("select rate FROM currencies WHERE currabrev='" . $_SESSION['ReceiptBatch']->Currency . "'",$db);
		$myrow = DB_fetch_row($result);
		$tableExRate = $myrow[0]; //this is the rate of exchange between the functional currency and the receipt currency
		/*Calculate cross rate to suggest appropriate exchange rate between receipt currency and account currency */
		$SuggestedExRate = $tableExRate/$SuggestedFunctionalExRate;
	} //end else account currency != functional currency

	$_SESSION['ReceiptBatch']->Narrative = $_POST['BatchNarrative'];	

	$_SESSION['RequireReceiptHeader'] = 0;

}

if (isset($_POST['editBankInfo'])){
	$_SESSION['RequireReceiptHeader'] = 1;
}

if (isset($_POST['Process'])){ //user hit submit a new entry to the receipt batch

	if (!isset($_POST['GLCode'])) {
		$_POST['GLCode']='';
	}
	if (!isset($_POST['tag'])) {
		$_POST['tag']='';
	}

	if (!isset($_POST['CustomerID'])) {
		$_POST['CustomerID']='';
	}
	
	$_POST['CustomerName'] = $_SESSION['CustomerRecord']['name'];	
	if (!isset($_POST['CustomerName'])) {
		$_POST['CustomerName']='';
	}
	
	//echo 'CustID:'.$_POST['CustomerID'].'Amount:'.$_POST['Amount'].'CustomerName:'.$_POST['CustomerName'];
	
	$_SESSION['ReceiptBatch']->add_to_batch($_POST['Amount'],
   											$_POST['CustomerID'],
											$_POST['Discount'],
											$_POST['Narrative'],
											$_POST['GLCode'],
											$_POST['PayeeBankDetail'],
											$_POST['CustomerName'],
											$_POST['tag']);

   /*Make sure the same receipt is not double processed by a page refresh */
   $Cancel = 1;
}

if (isset($_POST['Cancel'])){ //user hit the Cancel button
   $Cancel = 1;
   $_SESSION['RequireReceiptHeader'] = 1;
}

if (isset($Cancel)){
   unset($_SESSION['CustomerRecord']);
   unset($_POST['CustomerID']);
   unset($_POST['CustomerName']);
   unset($_POST['Amount']);
   unset($_POST['Discount']);
   unset($_POST['Narrative']);
   unset($_POST['PayeeBankDetail']);
}

if (isset($_POST['CommitBatch'])){

 /* once all receipts items entered, process all the data in the
  session cookie into the DB creating a single banktrans for the whole amount
  of all receipts in the batch and DebtorTrans records for each receipt item
  all DebtorTrans will refer to a single banktrans. A GL entry is created for
  each GL receipt entry and one for the debtors entry and one for the bank
  account debit

  NB allocations against debtor receipts are a seperate exercice

  first off run through the array of receipt items $_SESSION['ReceiptBatch']->Items and
  if GL integrated then create GL Entries for the GL Receipt items
  and add up the non-GL ones for posting to debtors later,
  also add the total discount total receipts*/

   $PeriodNo = GetPeriod($_SESSION['ReceiptBatch']->DateBanked,$db);

   if ($_SESSION['CompanyRecord']==0){
		prnMsg(_('The company has not yet been set up properly') . ' - ' . _('this information is needed to process the batch') . '. ' . _('Processing has been cancelled'),'error');
		include('includes/footer.inc');
		exit;
   }

   /*Make an array of the defined bank accounts */
   $SQL = "SELECT accountcode FROM bankaccounts";
   $result = DB_query($SQL,$db);
   $BankAccounts = array();
   $i=0;
   while ($Act = DB_fetch_row($result)){
 		$BankAccounts[$i]= $Act[0];
		$i++;
   }

	$_SESSION['ReceiptBatch']->BatchNo = GetNextTransNo(12,$db);
   /*Start a transaction to do the whole lot inside */
   $result = DB_Txn_Begin($db);

   $BatchReceiptsTotal = 0; //in functional currency
   $BatchDiscount = 0; //in functional currency
   $BatchDebtorTotal = 0; //in functional currency

   foreach ($_SESSION['ReceiptBatch']->Items as $ReceiptItem) {

	    if ($ReceiptItem->GLCode !=''){
			if ($_SESSION['CompanyRecord']['gllink_debtors']==1){ /* then enter a GLTrans record */
				 $SQL = 'INSERT INTO gltrans (type,
			 			typeno,
						trandate,
						periodno,
						account,
						narrative,
						amount,
						tag)
			 		  VALUES (12,
			 			' . $_SESSION['ReceiptBatch']->BatchNo . ",
						'" . FormatDateForSQL($_SESSION['ReceiptBatch']->DateBanked) . "',
						" . $PeriodNo . ',
						' . $ReceiptItem->GLCode . ",
						'" . $ReceiptItem->Narrative . "',
						" . -($ReceiptItem->Amount/$_SESSION['ReceiptBatch']->ExRate/$_SESSION['ReceiptBatch']->FunctionalExRate) . ",
						'" . $ReceiptItem->tag . "'" . ')';
			 	$ErrMsg = _('Cannot insert a GL entry for the receipt because');
			 	$DbgMsg = _('The SQL that failed to insert the receipt GL entry was');
			 	$result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);
			}

			/*check to see if this is a GL posting to another bank account (or the same one)
			if it is then a matching payment needs to be created for this account too */

			if (in_array($ReceiptItem->GLCode, $BankAccounts)) {

			/*Need to deal with the case where the payment from one bank account could be to a bank account in another currency */

				/*Get the currency and rate of the bank account transferring to*/
				$SQL = 'SELECT currcode, rate
							FROM bankaccounts INNER JOIN currencies
							ON bankaccounts.currcode = currencies.currabrev
							WHERE accountcode=' . $ReceiptItem->GLCode;
				$TrfFromAccountResult = DB_query($SQL,$db);
				$TrfFromBankRow = DB_fetch_array($TrfFromAccountResult) ;
				$TrfFromBankCurrCode = $TrfFromBankRow['currcode'];
				$TrfFromBankExRate = $TrfFromBankRow['rate'];

				if ($_SESSION['ReceiptBatch']->AccountCurrency == $TrfFromBankCurrCode){
					/*Make sure to use the same rate if the transfer is between two bank accounts in the same currency */
					$TrfFromBankExRate = $_SESSION['ReceiptBatch']->FunctionalExRate;
				}

				/*Consider an example - had to be currencies I am familar with sorry so I could figure it out!!
					 functional currency NZD
					 bank account in AUD - 1 NZD = 0.90 AUD (FunctionalExRate)
					 receiving USD - 1 AUD = 0.85 USD  (ExRate)
					 from a bank account in EUR - 1 NZD = 0.52 EUR

					 oh yeah - now we are getting tricky!
					 Lets say we received USD 100 to the AUD bank account from the EUR bank account

					 To get the ExRate for the bank account we are transferring money from
					 we need to use the cross rate between the NZD-AUD/NZD-EUR
					 and apply this to the

					 the receipt record will read
					 exrate = 0.85 (1 AUD = USD 0.85)
					 amount = 100 (USD)
					 functionalexrate = 0.90 (1 NZD = AUD 0.90)

					 the payment record will read

					 amount 100 (USD)
					 exrate    (1 EUR =  (0.85 x 0.90)/0.52 USD  ~ 1.47
					  					(ExRate x FunctionalExRate) / USD Functional ExRate
					 Check this is 1 EUR = 1.47 USD
					 functionalexrate =  (1NZD = EUR 0.52)

				*/

				$PaymentTransNo = GetNextTransNo( 1, $db);
				$SQL='INSERT INTO banktrans (transno,
							type,
							bankact,
							ref,
							exrate,
							functionalexrate,
							transdate,
							banktranstype,
							amount,
							currcode)
			      VALUES (' . $PaymentTransNo . ',
						1,
						' . $ReceiptItem->GLCode . ",
						'" . _('Act Transfer') .' - ' . $ReceiptItem->Narrative . "',
						" . (($_SESSION['ReceiptBatch']->ExRate * $_SESSION['ReceiptBatch']->FunctionalExRate)/$TrfFromBankExRate). ',
						' . $TrfFromBankExRate . ",
						'" . FormatDateForSQL($_SESSION['ReceiptBatch']->DateBanked) . "',
						'" . $_SESSION['ReceiptBatch']->ReceiptType . "',
						" . -$ReceiptItem->Amount . ",
						'" . $_SESSION['ReceiptBatch']->Currency . "'
					)";

				$DbgMsg = _('The SQL that failed to insert the bank transaction was');
				$ErrMsg = _('Cannot insert a bank transaction using the SQL');
				$result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);
			} //end if an item is a transfer between bank accounts

	    } else { //its not a GL item - its a customer receipt then
 		   /*Accumulate the total debtors credit including discount */
		   $BatchDebtorTotal += (($ReceiptItem->Discount + $ReceiptItem->Amount)/$_SESSION['ReceiptBatch']->ExRate/$_SESSION['ReceiptBatch']->FunctionalExRate);
		   /*Create a DebtorTrans entry for each customer deposit */

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
		   			VALUES (' . $_SESSION['ReceiptBatch']->BatchNo . ",
		   				12,
						'" . $ReceiptItem->Customer . "',
						'',
						'" . FormatDateForSQL($_SESSION['ReceiptBatch']->DateBanked) . "',
						'" . $PeriodNo . "',
						'" . $_SESSION['ReceiptBatch']->ReceiptType  . ' ' . $ReceiptItem->Narrative . "',
						'',
						" . ($_SESSION['ReceiptBatch']->ExRate/$_SESSION['ReceiptBatch']->FunctionalExRate) . ",
						" . -$ReceiptItem->Amount . ",
						" . -$ReceiptItem->Discount . ",
						'" . $ReceiptItem->PayeeBankDetail . "'
					)";
			$DbgMsg = _('The SQL that failed to insert the customer receipt transaction was');
			$ErrMsg = _('Cannot insert a receipt transaction against the customer because') ;
			$result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);

			$SQL = "UPDATE debtorsmaster SET lastpaiddate = '" . FormatDateForSQL($_SESSION['ReceiptBatch']->DateBanked) . "',
											lastpaid=" . $ReceiptItem->Amount ."
									WHERE debtorsmaster.debtorno='" . $ReceiptItem->Customer . "'";

			$DbgMsg = _('The SQL that failed to update the date of the last payment received was');
			$ErrMsg = _('Cannot update the customer record for the date of the last payment received because');
			$result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);

	    } //end of if its a customer receipt
	    $BatchDiscount += ($ReceiptItem->Discount/$_SESSION['ReceiptBatch']->ExRate/$_SESSION['ReceiptBatch']->FunctionalExRate);
	    $BatchReceiptsTotal += ($ReceiptItem->Amount/$_SESSION['ReceiptBatch']->ExRate/$_SESSION['ReceiptBatch']->FunctionalExRate);

   } /*end foreach $ReceiptItem */

   if ($_SESSION['CompanyRecord']['gllink_debtors']==1){ /* then enter GLTrans records for discount, bank and debtors */

	if ($BatchReceiptsTotal!=0){
		/* Bank account entry first */
		$SQL="INSERT INTO gltrans (type,
					typeno,
					trandate,
					periodno,
					account,
					narrative,
					amount)
			VALUES (12,
				" . $_SESSION['ReceiptBatch']->BatchNo . ",
				'" . FormatDateForSQL($_SESSION['ReceiptBatch']->DateBanked) . "',
				" . $PeriodNo . ",
				" . $_SESSION['ReceiptBatch']->Account . ",
				'" . $_SESSION['ReceiptBatch']->Narrative . "',
				" . $BatchReceiptsTotal . ')';
		$DbgMsg = _('The SQL that failed to insert the GL transaction fro the bank account debit was');
		$ErrMsg = _('Cannot insert a GL transaction for the bank account debit');
		$result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);

                /*now enter the BankTrans entry */

                $SQL='INSERT INTO banktrans (type,
   						transno,
						bankact,
						ref,
						exrate,
						functionalexrate,
						transdate,
						banktranstype,
						amount,
						currcode)
                		VALUES (12,
                      ' . $_SESSION['ReceiptBatch']->BatchNo . ',
                      		' . $_SESSION['ReceiptBatch']->Account . ",
                      		'" . $_SESSION['ReceiptBatch']->Narrative . "',
                      		" . $_SESSION['ReceiptBatch']->ExRate . ",
                      		" . $_SESSION['ReceiptBatch']->FunctionalExRate . ",
                      		'" . FormatDateForSQL($_SESSION['ReceiptBatch']->DateBanked) . "',
                      		'" . $_SESSION['ReceiptBatch']->ReceiptType . "',
                      		" . ($BatchReceiptsTotal * $_SESSION['ReceiptBatch']->FunctionalExRate * $_SESSION['ReceiptBatch']->ExRate) . ",
                      		'" . $_SESSION['ReceiptBatch']->Currency . "'
                        )";
              $DbgMsg = _('The SQL that failed to insert the bank account transaction was');
              $ErrMsg = _('Cannot insert a bank transaction');
              $result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);
      }
      if ($BatchDebtorTotal!=0){
		/* Now Credit Debtors account with receipts + discounts */
		$SQL='INSERT INTO gltrans ( type,
					typeno,
					trandate,
					periodno,
					account,
					narrative,
					amount)
			VALUES (12,
				' . $_SESSION['ReceiptBatch']->BatchNo . ",
				'" . FormatDateForSQL($_SESSION['ReceiptBatch']->DateBanked) . "',
				" . $PeriodNo . ',
				' . $_SESSION['CompanyRecord']['debtorsact'] . ",
					'" . $_SESSION['ReceiptBatch']->Narrative . "',
					" . -$BatchDebtorTotal . '
				)';
			$DbgMsg = _('The SQL that failed to insert the GL transaction for the debtors account credit was');
			$ErrMsg = _('Cannot insert a GL transaction for the debtors account credit');
			$result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);

      } //end if there are some customer deposits in this batch

      if ($BatchDiscount!=0){
			/* Now Debit Discount account with discounts allowed*/
		$SQL='INSERT INTO gltrans ( type,
					typeno,
					trandate,
					periodno,
					account,
					narrative,
					amount)
			VALUES (12,
				' . $_SESSION['ReceiptBatch']->BatchNo . ",
				'" . FormatDateForSQL($_SESSION['ReceiptBatch']->DateBanked) . "',
				" . $PeriodNo . ',
				' . $_SESSION['CompanyRecord']['pytdiscountact'] . ",
				'" . $_SESSION['ReceiptBatch']->Narrative . "',
				" . $BatchDiscount . ')';
		$DbgMsg = _('The SQL that failed to insert the GL transaction for the payment discount debit was');
		$ErrMsg = _('Cannot insert a GL transaction for the payment discount debit');
		$result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);
	  } //end if there is some discount
   } //end if there is GL work to be done - ie config is to link to GL


   $ErrMsg = _('Cannot commit the changes');
   $DbgMsg = _('The SQL that failed was');
   $result = DB_Txn_Commit($db);

   echo '<P>';

   prnMsg( _('Receipt batch') . ' ' . $_SESSION['ReceiptBatch']->BatchNo . ' ' . _('has been successfully entered into the database'),'success');

   echo '<P CLASS="page_title_text"><img src="'.$rootpath.'/css/'.$theme.'/images/printer.png" TITLE="' . _('Print') . '" ALT="">' . ' ' . '<A HREF="' . $rootpath . '/PDFBankingSummary.php?BatchNo=' . $_SESSION['ReceiptBatch']->BatchNo . '">' . _('Print PDF Batch Summary') . '</A></P>';
   unset($_SESSION['ReceiptBatch']);
   include('includes/footer.inc');
   exit;

} /* End of commit batch */

If (isset($_POST['NewSelectedCustomer'])) { //a customer been selected, then get _SESSION['CustomerRecord']
	$_POST['CustomerID']=$_POST['NewSelectedCustomer'];
	/*need to get currency sales type - payment discount percent and GL code
	as well as payment terms and credit status and hold the lot as session variables
	the receipt held entirely as session variables until the button clicked to process*/

	if (isset($_SESSION['CustomerRecord'])){
	   unset($_SESSION['CustomerRecord']);
	}

	$SQL = 'SELECT debtorsmaster.name,
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
	
	//echo 'CustID:'.$_SESSION['CustomerRecord']['debtorno'].'Amount:'.$_POST['Amount'].'CustomerName:'.$_SESSION['CustomerRecord']['name'];
	
} /*end of if customer has just been selected  all info required read into $_SESSION['CustomerRecord']*/

if (isset($_POST['Delete'])) {
  /* User hit delete the receipt entry from the batch */
   $_SESSION['ReceiptBatch']->remove_receipt_item($_POST['Delete']);
} 


echo '<form action="' . $_SERVER['PHP_SELF'] . '?' . SID . '" method=post>';
/*
* always display receipt header( account..)
*/
if($_SESSION['RequireReceiptHeader'] == 1){
	//Update Receipt Header Info(Bank Account..)
	$_SESSION['ReceiptBatch'] = new Receipt_Batch;
	
	// AccountsResults search first
	$SQL = 'SELECT bankaccountname,
				bankaccounts.accountcode,
				bankaccounts.currcode
			FROM bankaccounts,
				chartmaster
			WHERE bankaccounts.accountcode=chartmaster.accountcode';

	$ErrMsg = _('The bank accounts could not be retrieved because');
	$DbgMsg = _('The SQL used to retrieve the bank acconts was');
	$AccountsResults = DB_query($SQL,$db,$ErrMsg,$DbgMsg);	
	
	echo '<table class="commontable">
	<caption>Bank Info Header</caption>
	<tr><td>' . _('Bank Account') . ':</td>
					 <td><select tabindex=1 name="BankAccount">';

	if (DB_num_rows($AccountsResults)==0){
		echo '</select></td></table><p>';
		prnMsg(_('Bank Accounts have not yet been defined') . '. ' . _('You must first') . ' ' . '<A HREF="' . $rootpath . '/BankAccounts.php">' . _('define the bank accounts') . '</A>' . _('and general ledger accounts to be affected'),'info');
		include('includes/footer.inc');
		 exit;
	} else {
		while ($myrow=DB_fetch_array($AccountsResults)){
		  /*list the bank account names */
			if (!isset($_SESSION['ReceiptBatch']->Account) AND $myrow['currcode']==$_SESSION['CompanyRecord']['currencydefault']){
				//default to the first account in the functional currency of the business in the list of accounts returned
				$_SESSION['ReceiptBatch']->Account=$myrow['accountcode'];
			}
			if ($_SESSION['ReceiptBatch']->Account==$myrow['accountcode']){
				echo '<option selected value="' . $myrow['accountcode'] . '">' . $myrow['bankaccountname'] . ' - ' . $myrow['currcode'] . '</option>';
			} else {
				echo '<option value="' . $myrow['accountcode'] . '">' . $myrow['bankaccountname']. ' - ' . $myrow['currcode'] . '</option>';
			}
		}
		echo '</select></td>';
	}
	
	// Date banked
	if (!Is_Date($_SESSION['ReceiptBatch']->DateBanked)){
		$_SESSION['ReceiptBatch']->DateBanked = Date($_SESSION['DefaultDateFormat']);
	}
	
	echo '<td>' . _('Date Banked') . ':</td>
			<td><input tabindex=2 type="text" name="DateBanked" onChange="isDate(this, this.value, '."'".$_SESSION['DefaultDateFormat']."'".')" value="' . $_SESSION['ReceiptBatch']->DateBanked . '"></td></tr>';
	
	// Currency
	echo '<tr><td>' . _('Currency') . ':</td>
			<td><select tabindex=3 name="Currency">';

	if (!isset($_SESSION['ReceiptBatch']->Currency)){
	  $_SESSION['ReceiptBatch']->Currency=$_SESSION['CompanyRecord']['currencydefault'];
	}

	$SQL = 'SELECT currency, currabrev, rate FROM currencies';
	$result=DB_query($SQL,$db);
	if (DB_num_rows($result)==0){
	   echo '</SELECT></td>';
	   prnMsg(_('No currencies are defined yet') . '. ' . _('Receipts cannot be entered until a currency is defined'),'warn');

	} else {
		while ($myrow=DB_fetch_array($result)){
			if ($_SESSION['ReceiptBatch']->Currency==$myrow['currabrev']){
				echo '<option selected value="' . $myrow['currabrev'] . '">' . $myrow['currency'] . '</option>';
			} else {
				echo '<option value="' . $myrow['currabrev'] . '">' . $myrow['currency'] . '</option>';
			}
		}
		echo '</select></td>';
	}

	if (!isset($_SESSION['ReceiptBatch']->ExRate)){
		$_SESSION['ReceiptBatch']->ExRate=1;
	}

	if (!isset($_SESSION['ReceiptBatch']->FunctionalExRate)){
		$_SESSION['ReceiptBatch']->FunctionalExRate=1;
	}
	if ($_SESSION['ReceiptBatch']->AccountCurrency!=$_SESSION['ReceiptBatch']->Currency AND isset($_SESSION['ReceiptBatch']->AccountCurrency)){
		if (isset($SuggestedExRate)){
			$SuggestedExRateText = '<b>' . _('Suggested rate:') . ' ' . number_format($SuggestedExRate,4) . '</b>';
		} else {
			$SuggestedExRateText ='';
		}
		if ($_SESSION['ReceiptBatch']->ExRate==1 AND isset($SuggestedExRate)){
			$_SESSION['ReceiptBatch']->ExRate = $SuggestedExRate;
		}
		echo '<tr><td>' . _('Receipt Exchange Rate') . ':</td>
				<td><input tabindex=4 type="text" name="ExRate" maxlength=10 size=12  onKeyPress="return restrictToNumbers(this, event)" onChange="numberFormat(this,2)" onFocus="return setTextAlign(this, '."'".'right'."'".')" value="' . $_SESSION['ReceiptBatch']->ExRate . '"></td>
				<td>' . $SuggestedExRateText . ' <i>' . _('The exchange rate between the currency of the bank account currency and the currency of the receipt') . '. 1 ' . $_SESSION['ReceiptBatch']->AccountCurrency . ' = ? ' . $_SESSION['ReceiptBatch']->Currency . '</i></td></tr>';
	}

	if ($_SESSION['ReceiptBatch']->AccountCurrency!=$_SESSION['CompanyRecord']['currencydefault']
												AND isset($_SESSION['ReceiptBatch']->AccountCurrency)){
		if (isset($SuggestedFunctionalExRate)){
			$SuggestedFunctionalExRateText = '<b>' . _('Suggested rate:') . ' ' . number_format($SuggestedFunctionalExRate,4) . '</b>';
		} else {
			$SuggestedFunctionalExRateText ='';
		}
		if ($_SESSION['ReceiptBatch']->FunctionalExRate==1 AND isset($SuggestedFunctionalExRate)){
			$_SESSION['ReceiptBatch']->FunctionalExRate = $SuggestedFunctionalExRate;
		}
		echo '<tr><td>' . _('Functional Exchange Rate') . ':</td><td><input tabindex=5 type="text" name="FunctionalExRate" maxlength=10 size=12 value="' . $_SESSION['ReceiptBatch']->FunctionalExRate . '"></td>
				<td>' . ' ' . $SuggestedFunctionalExRateText . ' <i>' . _('The exchange rate between the currency of the business (the functional currency) and the currency of the bank account') .  '. 1 ' . $_SESSION['CompanyRecord']['currencydefault'] . ' = ? ' . $_SESSION['ReceiptBatch']->AccountCurrency . '</i></td></tr>';
	}
	
	// Receipt Type
	echo '<td>' . _('Receipt Type') . ":</td><TD><SELECT name=ReceiptType>";

	include('includes/GetPaymentMethods.php');
	/* The array ReceiptTypes is defined from the setup tab of the main menu under payment methods - the array is populated from the include file GetPaymentMethods.php */

	$_SESSION['ReceiptBatch']->ReceiptType = $_POST['ReceiptType'];
	echo 'RT:'.$_SESSION['ReceiptBatch']->ReceiptType;
	
	foreach ($ReceiptTypes as $RcptType) {
		 if (isset($_POST['ReceiptType']) and $_POST['ReceiptType']==$RcptType){
		   echo "<OPTION SELECTED Value='$RcptType'>$RcptType";
		   $_SESSION['ReceiptBatch']->ReceiptType = $RcptType;
		 } else {
		   echo "<OPTION Value='$RcptType'>$RcptType";
		 }	 
	}
	echo '</select></td></tr>';
	if (!isset($_SESSION['ReceiptBatch']->Narrative)) {
		$_SESSION['ReceiptBatch']->Narrative='';
	}
	echo '<tr><td>' . _('Narrative') . ':</td><td colspan=2><input type="text" name="BatchNarrative" maxlength=150 value="' . $_SESSION['ReceiptBatch']->Narrative . '"></td>';
	echo '<td ><input type=submit name="SaveBankInfo" Value="' . _('Save and go Next') . '"></td></tr>';
	echo '</table>';		
} else {
	echo "<table class='commontable' > 
	<caption> Bank Info Header</caption>
	<tr><td>Bank Account</td><td>".$_SESSION['ReceiptBatch']->Account."</td><td>Date Banked</td><td>".$_SESSION['ReceiptBatch']->DateBanked."</td></tr>
	<tr><td>Currency</td><td>".$_SESSION['ReceiptBatch']->Currency."</td><td>Receipt Type</td><td>".$_SESSION['ReceiptBatch']->ReceiptType."</td></tr>
	<tr><td>Narrative:</td><td colspan='2'></td><td><input type=submit name='editBankInfo' Value='Edit'></td></tr>
	</table>";
}

/*
* display the batches items, when RequireReceiptHeader is false
*/
if( $_SESSION['RequireReceiptHeader'] == 0 ){

	echo '<table class="commontable">
			<caption>Receipt Batch</caption><tr>
			<th>' . _('Customer') . '</th>
			<th>' . _('Balance') . '</th>
			<th>' . _('Overdue') . '</th>
			<th>' . _('Amount') .'<br/>' . _('Received') . '</th>
			<th>' . _('Discount') . '</th>
			<th>' . _('Bank Info and Remark') . '</th>
			<th></th>
			</tr>';

	$BatchTotal = 0;

	if (isset($_SESSION['ReceiptBatch']) and isset($_SESSION['ReceiptBatch']->Items)){
		foreach ($_SESSION['ReceiptBatch']->Items as $ReceiptItem) {

			//<td>' . stripslashes($ReceiptItem->CustomerName) . '</td>
			echo '<tr>
					<td>' . $ReceiptItem->Customer. '</td>
					<td></td>
					<td></td>				
					<td align=right>' . number_format($ReceiptItem->Amount,2) . '</td>
					<td align=right>' . number_format($ReceiptItem->Discount,2) . '</td>
					<td>' . $ReceiptItem->GLCode . '</td>				
					<td><img src="editgray.jpg">
						<img src="savegray.png" alt="save">
						<input type=image name=Delete value='.$ReceiptItem->ID.'  src="delete.jpg">
						</td></tr>';
				
			$BatchTotal= $BatchTotal + $ReceiptItem->Amount;
		}
	}

	if(isset($_POST['NewSelectedCustomer'])){
		if( !isset($_POST['Discount']) ){
			$_POST['Discount']=0;
		}

		echo '<td>'.$_POST['NewSelectedCustomer'].'<input type="hidden" name="CustomerID" value='.$_POST['NewSelectedCustomer'].'></td>';
		echo '<td>' . number_format($_SESSION['CustomerRecord']['balance'],2) . '</td>';
		echo '<td>' . number_format($_SESSION['CustomerRecord']['due'],2) . '</td>';
		echo '<td><input type="text" name="Amount" onKeyPress="return restrictToNumbers(this, event)"  onChange="numberFormat(this,2)" onFocus="return setTextAlign(this, '."'".'right'."'".')" value="' . $_POST['Amount'] . '"></td>';
		echo '<td><input type="text" name="Discount" onKeyPress="return restrictToNumbers(this, event)"  onChange="numberFormat(this,2)" onFocus="return setTextAlign(this, '."'".'right'."'".')" value="' . $_POST['Discount'] . '"></td>';
		echo '<td><input type="text" name="Narrative" value="' . $_POST['Narrative'] . '"></td>';	
		echo '<td class="menu"><img src="editgray.jpg" alt="edit">
		<input type=image name="Process" value="Save" src="save.png">
		<input type=image name="Unsave" value="Unsave" src="delete.jpg">  </td></tr>';
	}

	if ($_POST['AddLine'])
	{
		echo '<tr>';
		echo "<td><input onclick=openSubpage('".$rootpath."/h5CustomerSearch.php'); type='button' value='Customer' /></td><td><td/><td></td><td><td/><td></td></tr>";
		unset($_POST['AddLine']);	
	}
	echo '<TR><td></td><td></td><td></td><td ALIGN=RIGHT><B>' . number_format($BatchTotal,2) . '</B></td></td>
	<td></td><td></td><td><input type=submit name="AddLine" value="Add Line" /></td>
	</TR></TABLE>';

	echo '<p align=right> <input type=submit name="Cancel" VALUE="' . _('Cancel and Delete Batch') . '"><input type=submit name="CommitBatch" VALUE="' . _('Accept and Process Batch') . '"></p>';
}

echo '</form>';

?>