<?php

/* $Revision: 1.15 $ */

$PageSecurity = 3;

//$_SESSION['Shortcuts']['Link1'] = '<a href="' .  $rootpath . '/SO_Allinone.php?&NewOrder=Yes">' . _('New customer order') . '</a></br>';
//$_SESSION['Shortcuts']['Link2'] = '<a href="' .  $rootpath . '/SelectCompletedCustOrder.php?">' . _('All customer orders') . '</a></br>';
//$_SESSION['Shortcuts']['Link3'] = '<a href="' .  $rootpath . '/h5SalesExchange.php?">' . _('New sales exchange') . '</a></br>';
//$_SESSION['Shortcuts']['Link4'] = '<a href="' .  $rootpath . '/SelectCustomer.php?">' . _('Customers') . '</a></br>';
//$_SESSION['Shortcuts']['Link5'] = '<a href="' .  $rootpath . '/SelectCustomer.php?">' . _('Customers') . '</a></br>';
//$_SESSION['Shortcuts']['Link6'] = '<a href="' .  $rootpath . '/SelectCustomer.php?">' . _('Customers') . '</a>';

include('includes/session.inc');

$title = _('Search All Sales Orders');

include('includes/header.inc');

//echo '<P CLASS="page_title_text"><img src="'.$rootpath.'/css/'.$theme.'/images/magnifier.png" TITLE="' . _('Search') . '" ALT="">' . ' ' . _('Search Sales Orders') . '</P><CENTER>';

echo '<P CLASS="page_title_text"> Sales:Home </P>';
echo '<HR>';
echo '<P CLASS="page_subtitle_text"> >>Orders&Inquires </P>';
echo "<FORM ACTION='" . $_SERVER['PHP_SELF'] . '?' . SID ."' METHOD=POST>";

//-- Outstanging Customer orders --//////////////////////////////////////////////////////
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
	echo "<input onclick=\"javascript:window.location.href='SO_Allinone.php?&NewOrder=Yes'\"  type='button' value='Add New' />";
	echo "<input onclick=\"javascript:window.location.href='SelectCompletedCustOrder.php?'\" type='button' value='All Customer Orders' />";
	echo "<input onclick=\"javascript:window.location.href='h5SalesExchange.php?'\" type='button' value='Customer Ex' />";	
	//echo "<input onclick=\"javascript:window.location.href='SO_Allinone.php?&NewOrder=Yes'\" type='button' value='New Customer Order' />";	
		
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
			$ModifyLink = $rootpath . '/SO_Allinone.php?' . SID . 'ModifyOrderNumber=' .$myrow['orderno'];
			$ModifyImg =$rootpath.'/css/'.$theme.'/images/edit_inline.gif';
			printf("<td>%s</td>
				<td>%s</td>		
				<td><FONT COLOR=GREEN><A target='_self' HREF='%s'><img src=%s></A>%s<FONT></td>
				<td>%s</td>
				<td>%s</td>
				<td ALIGN=RIGHT>%10.2f</td>
				<td ALIGN=RIGHT>%s</td>
				<td ALIGN=RIGHT>%s</td>	
				<td ALIGN=RIGHT>%s</td>					
				<td><A HREF='%s'>%s</A></td>		
				</tr>", 
				$FormatedOrderDate,	
				$myrow['debtorno'],
				$ModifyLink,$ModifyImg,
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

echo '<P CLASS="page_subtitle_text"> >>Transactions </P>';
echo "<input onclick=\"javascript:window.location.href='SelectCompletedOrder.php?'\" type='button' value='All Transactions' />";		
/////////////////////////////////////////////////////////////////////////////////////////
//-- Outstanging Sales orders --/////////////////////////////////////////////////////////
$_POST['SearchOrders'] = '%';
if (isset($_POST['SearchOrders'])) {

	//figure out the SQL required from the inputs available
		$DateAfterCriteria = FormatDateforSQL($_POST['OrdersAfterDate']);

			if (1) {
/*Quincy talbe: 1.Order(Inv),2.Customer,3.Cust PO,4.Product,5.Qty,6.Price, 7.Payment,8.Del Date,9.Delivery No.10.Remark,*/
/*              1.stockmoves.transno(Inv)3.stockmoves.reference   6.price 7.1debtorno=>curr 8.trandate
			     2.debtorsmaster.name 4.stockid 5.qty         7.price*qty=amount   9.debtortrans.consignment */
				
				$SQL = "SELECT  stockmoves.stkmoveno,
						stockmoves.transno, 						
						stockmoves.reference, 
						stockmoves.stockid, 
						stockmoves.qty,
						stockmoves.debtorno,						
						debtorsmaster.currcode,
						debtortrans.consignment, 
						debtortrans.ovamount,
						debtortrans.alloc,
						debtortrans.edisent,
						stockmoves.trandate, 
						salesorders.customerref,
						salesorders.orddate,
						salesorderdetails.unitprice,
						salesorderdetails.orderlineno, 
						salesorders.deliverto, -salesorderdetails.unitprice*stockmoves.qty*(1-salesorderdetails.discountpercent) AS ordervalue 
					FROM stockmoves, 
						debtortrans, 
						debtorsmaster, 
						salesorders,
						salesorderdetails 
					WHERE stockmoves.reference = salesorderdetails.orderno
					AND stockmoves.stockid = salesorderdetails.stkcode					
					AND stockmoves.transno = debtortrans.transno
					AND debtortrans.debtorno = debtorsmaster.debtorno 					
					AND salesorders.orderno = salesorderdetails.orderno 
					AND stockmoves.trandate >= '$DateAfterCriteria' 
					AND stockmoves.type = 10					
					AND salesorders.quotation=0 	
					GROUP BY stockmoves.stkmoveno					
					ORDER BY stockmoves.debtorno,stockmoves.transno";															
			}

	$SalesOrdersResult = DB_query($SQL,$db);

	if (DB_error_no($db) !=0) {
		echo '<BR>' . _('No orders were returned by the SQL because') . ' ' . DB_error_msg($db);
		echo "<BR>$SQL";
	}

}//end of searchorders

If (isset($SalesOrdersResult)) {

/*show a table of the orders returned by the SQL */
/*Quincy talbe: 1.Order(Inv),2.Customer,3.Cust PO,4.Product,5.Qty,6.Price, 7.Payment,8.Del Date,9.Delivery No.10.Remark,*/
/*              1.stockmoves.transno(Inv)3.stockmoves.reference   6.price 7.1debtorno=>curr 8.trandate
			     2.stockmoves.debtorno 4.stockid 5.qty         7.price*qty=amount   9.debtortrans.consignment */

	echo '<TABLE width="100%">';

	$tableheader = "<TR><TH>" . _('Order(Inv)') . " #</TH>
			<TH>" . _('Customer') . "</TH>
			<TH>" . _('Customer PO') . " #</TH>
			<TH>" . _('Product') . "</TH>
			<TH>" . _('Qty') . " </TH>
			<TH>" . _('Curr') . " </TH>			
			<TH>" . _('Price') . "</TH>
			<TH>" . _('Total') . "</TH>			
			<TH>" . _('Outstand') . "</TH>			
			<TH>" . _('Delivery Date') . "</TH>";
			//<TH>" . _('Delivery #') . "</TH>";
			//<TH>" . _('Remark') . "</TH></TR>						
			
	echo $tableheader;

	$TransLineno = 0;
	$ThisTransno = 0;
	$j = 1;
	$k=0; //row colour counter
	while ($myrow=DB_fetch_array($SalesOrdersResult)) {
	
		$outstanding = $myrow['ordervalue'];
			
		$PrevTransno = $ThisTransno;
		$ThisTransno = $myrow['transno'];
		if( $PrevTransno != 0 &&  $PrevTransno == $ThisTransno  ){
			$TransLineno++;
			
			$Balance = $Balance - $myrow['ordervalue'];			
		} else {
			$TransLineno = 0;
			$Balance = $myrow['alloc'] - $myrow['ordervalue'];
		}
		
		if( $Balance >= 0 || -$Balance < 0.01){
			$outstanding = 0;
		    continue;   // hide completed order
		}else if($Balance<0 && -$Balance< $myrow['ordervalue']){
			$outstanding = -$Balance;	
		}
		
		echo '<tr>';

		//PrintCustTrans.php?FromTransNo=200911008&InvOrCredit=Invoice
		//$EdiSentImage =$rootpath.'/css/'.$theme.'/images/customer.png';
		$EdiSentImage =$rootpath.'/css/'.$theme.'/images/'.SID.'sent'.$myrow['edisent'];		
		//$ViewPage = $rootpath . '/OrderDetails.php?' .SID . '&OrderNumber=' . $myrow['transno'];
		$ViewPage = $rootpath . '/PrintCustTrans.php?' .SID . 'FromTransNo=' . $myrow['transno'].SID.'&InvOrCredit='.'Invoice';
		$ViewPageWithOp = $rootpath . '/PrintCustTrans.php?' .SID . 'FromTransNo=' . $myrow['transno'].SID.'&InvOrCredit='.'Invoice'.SID.'&EdiSent='.'Done';
		$FormatedDelDate = ConvertSQLDate($myrow['trandate']);
		$FormatedOrderDate = ConvertSQLDate($myrow['orddate']);
		$FormatedOrderValue = number_format($myrow['ordervalue'],2,'.','');
		//$FormatedOrderValue = $myrow['ordervalue'];
		//$outstanding = $myrow['ordervalue'] + $myrow['alloc'];
		$FormatedOrderPrice = number_format($myrow['price'],2);
/*Quincy 0.Order(Inv),1. orderline no.2.Customer,3.Cust PO,4.Product,5.Qty,6.Price, 7.Payment,8.Del Date,9.Delivery No.10.Remark,*/					
		printf("<td><A target='_blank' HREF='%s'>%s</A></td>
			<td>%s</td>
			<td>%s</td>
			<td><FONT COLOR=Teal>%s</FONT></td>
			<td>%s</td>
			<td ALIGN=RIGHT>%s</td>
			<td ALIGN=RIGHT>%10.2f</td>			
			<td ALIGN=RIGHT>%10.2f</td>		
			<td ALIGN=RIGHT><FONT COLOR=RED>%10.2f</FONT></td>
			<td>%s<A target='_self' HREF='%s'><img src=%s></A></td>
			</tr>", 
			$ViewPage, 
			'SO'.$myrow['transno'].'-'.$TransLineno,
			$myrow['debtorno'], 
			$myrow['customerref'],
			$myrow['stockid'], 	 
			-$myrow['qty'],
			$myrow['currcode'],
			$myrow['unitprice'],			
			$FormatedOrderValue,						
			$outstanding,			
			$FormatedDelDate, $ViewPageWithOp,$EdiSentImage,	
			$myrow['consignment']);//,
			//$myrow['comments']); 

//end of page full new headings if
	}
//end of while loop

	echo '</TABLE>';
}

echo '</form>';
include('includes/footer.inc');

?>
