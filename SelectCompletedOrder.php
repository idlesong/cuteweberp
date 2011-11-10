<?php

/* $Revision: 1.15 $ */

$PageSecurity = 1;

include('includes/session.inc');

$title = _('Search All Sales Orders');

include('includes/header.inc');

echo '<P CLASS="page_title_text"><img src="'.$rootpath.'/css/'.$theme.'/images/magnifier.png" TITLE="' . _('Search') . '" ALT="">' . ' ' . _('Search Sales Orders') . '</P>';//<CENTER>';


echo "<FORM ACTION='" . $_SERVER['PHP_SELF'] . '?' . SID ."' METHOD=POST>";

if (isset($_GET['SelectedStockItem'])){
	$SelectedStockItem = $_GET['SelectedStockItem'];
} elseif (isset($_POST['SelectedStockItem'])){
	$SelectedStockItem = $_POST['SelectedStockItem'];
}
if (isset($_GET['OrderNumber'])){
	$OrderNumber = $_GET['OrderNumber'];
} elseif (isset($_POST['OrderNumber'])){
	$OrderNumber = $_POST['OrderNumber'];
}
if (isset($_GET['CustomerRef'])){
	$CustomerRef = $_GET['CustomerRef'];
} elseif (isset($_POST['CustomerRef'])){
	$CustomerRef = $_POST['CustomerRef'];
}
if (isset($_GET['SelectedCustomer'])){
	$SelectedCustomer = $_GET['SelectedCustomer'];
} elseif (isset($_POST['SelectedCustomer'])){
	$SelectedCustomer = $_POST['SelectedCustomer'];
}

if (isset($SelectedStockItem) and $SelectedStockItem==''){
	unset($SelectedStockItem);
}
if (isset($OrderNumber) and $OrderNumber==''){
	unset($OrderNumber);
}
if (isset($CustomerRef) and $CustomerRef==''){
	unset($CustomerRef);
}
if (isset($SelectedCustomer) and $SelectedCustomer==''){
	unset($SelectedCustomer);
}
If (isset($_POST['ResetPart'])) {
		unset($SelectedStockItem);
}

If (isset($OrderNumber)) {
	echo '<P CLASS="page_title_text"><img src="'.$rootpath.'/css/'.$theme.'/images/sales.png" TITLE="' . _('Sales Order') . '" ALT="">' . ' ' . _('Order Number') . ' - ' . $OrderNumber . '</P>';
} elseif (isset($CustomerRef)) {
	echo _('Customer Ref') . ' - ' . $CustomerRef;
} else {
	If (isset($SelectedCustomer)) {
		echo _('For customer') . ': ' . $SelectedCustomer .' ' . _('and') . ' ';
		echo "<input type=hidden name='SelectedCustomer' value='$SelectedCustomer'>";
	}

	If (isset($SelectedStockItem)) {

		echo _('for the part') . ': ' . $SelectedStockItem . ' ' . _('and') . ' ' ."<input type=hidden name='SelectedStockItem' value='$SelectedStockItem'>";

	}
}


if (isset($_POST['SearchParts']) and $_POST['SearchParts']!=''){

	If ($_POST['Keywords']!='' AND $_POST['StockCode']!='') {
		echo _('Stock description keywords have been used in preference to the Stock code extract entered');
	}
	If ($_POST['Keywords']!='') {
		//insert wildcard characters in spaces

		$i=0;
		$SearchString = '%';
		while (strpos($_POST['Keywords'], ' ', $i)) {
			$wrdlen=strpos($_POST['Keywords'],' ',$i) - $i;
			$SearchString=$SearchString . substr($_POST['Keywords'],$i,$wrdlen) . '%';
			$i=strpos($_POST['Keywords'],' ',$i) +1;
		}
		$SearchString = $SearchString. substr($_POST['Keywords'],$i).'%';

		$SQL = "SELECT stockmaster.stockid, 
				stockmaster.description, 
				SUM(locstock.quantity) AS qoh,  
				SUM(purchorderdetails.quantityord-purchorderdetails.quantityrecd) AS qoo,  
				stockmaster.units, 
				SUM(salesorderdetails.quantity - salesorderdetails.qtyinvoiced) AS qdem 
			FROM (((stockmaster LEFT JOIN salesorderdetails on stockmaster.stockid = salesorderdetails.stkcode) 
				 LEFT JOIN locstock ON stockmaster.stockid=locstock.stockid)
				 LEFT JOIN purchorderdetails on stockmaster.stockid = purchorderdetails.itemcode) 
			WHERE salesorderdetails.completed =1 
			AND stockmaster.description " . LIKE . "'$SearchString' 
			AND stockmaster.categoryid='" . $_POST['StockCat'] . "' 
			GROUP BY stockmaster.stockid, 
				stockmaster.description, 
				stockmaster.units 
			ORDER BY stockmaster.stockid";

	} elseif ($_POST['StockCode']!=''){

		$SQL = "SELECT stockmaster.stockid, 
				stockmaster.description, 
				SUM(locstock.quantity) AS qoh, 
				SUM(purchorderdetails.quantityord-purchorderdetails.quantityrecd) AS qoo,  
				SUM(salesorderdetails.quantity - salesorderdetails.qtyinvoiced) AS qdem, 
				stockmaster.units 
			FROM (((stockmaster LEFT JOIN salesorderdetails on stockmaster.stockid = salesorderdetails.stkcode) 
				 LEFT JOIN locstock ON stockmaster.stockid=locstock.stockid)
				 LEFT JOIN purchorderdetails on stockmaster.stockid = purchorderdetails.itemcode) 
			WHERE salesorderdetails.completed =1 
			AND stockmaster.stockid " . LIKE . " '%" . $_POST['StockCode'] . "%' 
			AND stockmaster.categoryid='" . $_POST['StockCat'] . "' 
			GROUP BY stockmaster.stockid, 
				stockmaster.description, 
				stockmaster.units 
			ORDER BY stockmaster.stockid";

	} elseif ($_POST['StockCode']=='' AND $_POST['Keywords']=='' AND $_POST['StockCat']!='') {
		
		$SQL = "SELECT stockmaster.stockid, 
				stockmaster.description, 
				SUM(locstock.quantity) AS qoh, 
				SUM(purchorderdetails.quantityord-purchorderdetails.quantityrecd) AS qoo,  
				SUM(salesorderdetails.quantity - salesorderdetails.qtyinvoiced) AS qdem, 
				stockmaster.units 
			FROM (((stockmaster LEFT JOIN salesorderdetails on stockmaster.stockid = salesorderdetails.stkcode) 
				 LEFT JOIN locstock ON stockmaster.stockid=locstock.stockid)
				 LEFT JOIN purchorderdetails on stockmaster.stockid = purchorderdetails.itemcode) 
			WHERE salesorderdetails.completed =1 
			AND stockmaster.categoryid='" . $_POST['StockCat'] . "' 
			GROUP BY stockmaster.stockid, 
				stockmaster.description, 
				stockmaster.units 
			ORDER BY stockmaster.stockid";

	}

	if (strlen($SQL)<2){
		prnMsg(_('No selections have been made to search for parts') . ' - ' . _('choose a stock category or enter some characters of the code or description then try again'),'warn');
	} else {
		
		$ErrMsg = _('No stock items were returned by the SQL because');
		$DbgMsg = _('The SQL used to retrieve the searched parts was');
		$StockItemsResult = DB_query($SQL,$db,$ErrMsg,$DbgMsg);
		
		if (DB_num_rows($StockItemsResult)==1){
		  	$myrow = DB_fetch_row($StockItemsResult);
		  	$SelectedStockItem = $myrow[0];
			$_POST['SearchOrders']='True';
		  	unset($StockItemsResult);
		  	echo '<BR>' . _('For the part') . ': ' . $SelectedStockItem . ' ' . _('and') . " <input type=hidden name='SelectedStockItem' value='$SelectedStockItem'>";
		}
	}
} else if (isset($_POST['SearchOrders']) AND Is_Date($_POST['OrdersAfterDate'])==1) {

	//figure out the SQL required from the inputs available
	if (isset($OrderNumber)) {
			$SQL = "SELECT salesorders.orderno, 
					debtorsmaster.name, 
					custbranch.brname, 
					salesorders.customerref, 
					salesorders.orddate, 
					salesorders.deliverydate,  
					salesorders.deliverto, SUM(salesorderdetails.unitprice*salesorderdetails.quantity*(1-salesorderdetails.discountpercent)) AS ordervalue 
				FROM salesorders, 
					salesorderdetails, 
					debtorsmaster, 
					custbranch 
				WHERE salesorders.orderno = salesorderdetails.orderno 
				AND salesorders.branchcode = custbranch.branchcode 
				AND salesorders.debtorno = debtorsmaster.debtorno 
				AND debtorsmaster.debtorno = custbranch.debtorno 
				AND salesorders.orderno=". $OrderNumber ." 
				AND salesorders.quotation=0 
				GROUP BY salesorders.orderno,
					debtorsmaster.name, 
					custbranch.brname, 
					salesorders.customerref, 
					salesorders.orddate, 
					salesorders.deliverydate,  
					salesorders.deliverto
				ORDER BY salesorders.orderno";
	} elseif (isset($CustomerRef)) {
			$SQL = "SELECT salesorders.orderno, 
					debtorsmaster.name, 
					custbranch.brname, 
					salesorders.customerref, 
					salesorders.orddate, 
					salesorders.deliverydate,  
					salesorders.deliverto, SUM(salesorderdetails.unitprice*salesorderdetails.quantity*(1-salesorderdetails.discountpercent)) AS ordervalue 
				FROM salesorders, 
					salesorderdetails, 
					debtorsmaster, 
					custbranch 
				WHERE salesorders.orderno = salesorderdetails.orderno 
				AND salesorders.branchcode = custbranch.branchcode 
				AND salesorders.debtorno = debtorsmaster.debtorno 
				AND debtorsmaster.debtorno = custbranch.debtorno 
				AND salesorders.customerref like '%". $CustomerRef."%'
				AND salesorders.quotation=0 
				GROUP BY salesorders.orderno,
					debtorsmaster.name, 
					custbranch.brname, 
					salesorders.customerref, 
					salesorders.orddate, 
					salesorders.deliverydate,  
					salesorders.deliverto
				ORDER BY salesorders.orderno";
	
	} else {
		$DateAfterCriteria = FormatDateforSQL($_POST['OrdersAfterDate']);
        $DateBeforeCriteria = FormatDateforSQL($_POST['OrdersBeforeDate']);
		
		if (isset($SelectedCustomer) AND !isset($OrderNumber) AND !isset($CustomerRef)) {

			if (isset($SelectedStockItem)) {
				$SQL = "SELECT salesorders.orderno, 
						debtorsmaster.name, 
						custbranch.brname, 
						salesorders.customerref, 
						salesorders.orddate, 
						salesorders.deliverydate,  
						salesorders.deliverto, SUM(salesorderdetails.unitprice*salesorderdetails.quantity*(1-salesorderdetails.discountpercent)) AS ordervalue 
					FROM salesorders, 
						salesorderdetails, 
						debtorsmaster, 
						custbranch 
					WHERE salesorders.orderno = salesorderdetails.orderno 
					AND salesorders.branchcode = custbranch.branchcode 
					AND salesorders.debtorno = debtorsmaster.debtorno 
					AND debtorsmaster.debtorno = custbranch.debtorno 
					AND salesorderdetails.stkcode='". $SelectedStockItem ."' 
					AND salesorders.debtorno='" . $SelectedCustomer ."' 
					AND salesorders.orddate >= '" . $DateAfterCriteria ."' 
					AND salesorders.quotation=0 
					GROUP BY salesorders.orderno, 
						debtorsmaster.name, 
						custbranch.brname, 
						salesorders.customerref, 
						salesorders.orddate, 
						salesorders.deliverydate,  
						salesorders.deliverto
					ORDER BY salesorders.orderno";
			} else {
				$SQL = "SELECT salesorders.orderno, 
						debtorsmaster.name, 
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
					AND salesorders.debtorno='" . $SelectedCustomer . "' 
					AND salesorders.orddate >= '" . $DateAfterCriteria . "' 
					AND salesorders.quotation=0 
					GROUP BY salesorders.orderno, 
						debtorsmaster.name, 
						custbranch.brname, 
						salesorders.customerref, 
						salesorders.orddate, 
						salesorders.deliverydate,  
						salesorders.deliverto
					ORDER BY salesorders.orderno";
			}
		} else { //no customer selected
			if (isset($SelectedStockItem)) {
/*Quincy talbe: 1.Order(Inv),2.Customer,3.Cust PO,4.Product,5.Qty,6.Price, 7.Payment,7B. C,8.Del Date,9.Delivery No.10.Remark,*/  
				$SQL = "SELECT	salesorders.orderno, 
						salesorderdetails.orderlineno,
						debtorsmaster.name, 
						salesorders.customerref, 
						salesorderdetails.stkcode,
						salesorderdetails.quantity, 
						salesorderdetails.unitprice,
						SUM(salesorderdetails.unitprice*salesorderdetails.quantity*(1-salesorderdetails.discountpercent)) AS ordervalue,
						salesorders.deliverydate, 
						salesorders.comments, 
					FROM salesorders, 
						salesorderdetails, 
						debtorsmaster, 
						custbranch 
					WHERE salesorders.orderno = salesorderdetails.orderno 
					AND salesorders.debtorno = debtorsmaster.debtorno 
					AND salesorders.branchcode = custbranch.branchcode 
					AND debtorsmaster.debtorno = custbranch.debtorno 
					AND salesorderdetails.stkcode='". $SelectedStockItem ."'  
					AND salesorders.orddate >= '" . $DateAfterCriteria . "' 
					AND salesorders.quotation=0 
					GROUP BY salesorders.orderno, 
						debtorsmaster.name, 
						custbranch.brname, 
						salesorders.customerref, 
						salesorders.orddate, 
						salesorders.deliverydate,  
						salesorders.deliverto
					ORDER BY salesorders.orderno";
					
			} else {
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
					AND stockmoves.trandate <= '$DateBeforeCriteria'
					AND stockmoves.type = 10					
					AND salesorders.quotation=0 	
					GROUP BY stockmoves.stkmoveno					
					ORDER BY stockmoves.debtorno,stockmoves.transno";						
			}

		} //end selected customer
	} //end not order number selected

	$SalesOrdersResult = DB_query($SQL,$db);

	if (DB_error_no($db) !=0) {
		echo '<BR>' . _('No orders were returned by the SQL because') . ' ' . DB_error_msg($db);
		echo "<BR>$SQL";
	}

}//end of which button clicked options

if (!isset($_POST['OrdersAfterDate']) OR $_POST['OrdersAfterDate'] == '' OR ! Is_Date($_POST['OrdersAfterDate'])){
	//$_POST['OrdersAfterDate'] = Date($_SESSION['DefaultDateFormat'],Mktime(0,0,0,Date('m')-2,Date('d'),Date('Y')));
	$_POST['OrdersAfterDate'] = Date($_SESSION['DefaultDateFormat'],Mktime(0,0,0,1,1,Date('Y')));
    $_POST['OrdersBeforeDate'] = Date($_SESSION['DefaultDateFormat'],Mktime(23,59,59,Date('m'),Date('d'),Date('Y')));
}
echo "<TABLE class='searchtable'>";
if (!isset($OrderNumber) or $OrderNumber==''){
	echo '<TR><TD>' . _('Order Number') . ':</TD><TD>' . "<INPUT type=text name='OrderNumber' MAXLENGTH =8 SIZE=9></TD><TD>" . _('for all orders placed after') .
			": </TD><TD><INPUT type=text name='OrdersAfterDate' MAXLENGTH =10 SIZE=11 value=" . $_POST['OrdersAfterDate'] . "></td><td rowspan=2>" .
			"<INPUT TYPE=SUBMIT NAME='SearchOrders' VALUE='" . _('Search Orders') . "'></TD></TR>";
	echo '<TR><TD>' . _('Customer Ref') . ':</TD><TD>' . "<INPUT type=text name='CustomerRef' MAXLENGTH =8 SIZE=9></TD><TD>" . _('for all orders placed after') .
			": </TD><TD><INPUT type=text name='OrdersBeforeDate' MAXLENGTH =10 SIZE=11 value=" . $_POST['OrdersBeforeDate'] . "></td></TR>";
}
echo '</TABLE>';

if (!isset($SelectedStockItem)) {
	$SQL='SELECT categoryid, categorydescription FROM stockcategory ORDER BY categorydescription';
	$result1 = DB_query($SQL,$db);

   echo '<HR>';
   echo '<FONT SIZE=2>' . _('To search for sales orders for a specific part use the part selection facilities below') . '</FONT>';
   //echo '<INPUT TYPE=SUBMIT NAME="SearchParts" VALUE="' . _('Search Parts Now') . '">';
   
   //if (count($_SESSION['AllowedPageSecurityTokens'])>1){
   //	echo '<INPUT TYPE=SUBMIT NAME="ResetPart" VALUE="' . _('Show All') . '">';
   //}
   echo '<TABLE class="searchtable">';
   echo '<TR><TD>' . _('Select a stock category') . ':</FONT>';
   echo '<SELECT NAME="StockCat">';

	while ($myrow1 = DB_fetch_array($result1)) {
		if (isset($_POST['StockCat']) and $myrow1['categoryid'] == $_POST['StockCat']){
			echo "<OPTION SELECTED VALUE='". $myrow1['categoryid'] . "'>" . $myrow1['categorydescription'];
		} else {
			echo "<OPTION VALUE='". $myrow1['categoryid'] . "'>" . $myrow1['categorydescription'];
		}
	}

   echo '</SELECT>';
   echo '<TD>' . _('Enter text extracts in the description') . ':</FONT></TD>';
   echo '<TD><INPUT TYPE="Text" NAME="Keywords" SIZE=20 MAXLENGTH=25></TD></TR>';
   echo '<TR><TD></TD>';
   echo '<TD><FONT SIZE 3><B> ' ._('OR') . ' </B></FONT><FONT SIZE=1>' . _('Enter extract of the Stock Code') . ':</FONT></TD>';
   echo '<TD><INPUT TYPE="Text" NAME="StockCode" SIZE=15 MAXLENGTH=18></TD>';
   echo '</TR>';
   echo '</TABLE>';
   
   echo '<INPUT TYPE=SUBMIT NAME="SearchParts" VALUE="' . _('Search Parts Now') . '"> ';
   if (count($_SESSION['AllowedPageSecurityTokens'])>1){
		echo '<INPUT TYPE=SUBMIT NAME="ResetPart" VALUE="' . _('Show All') . '">';
   }


   echo '<HR>';

}

If (isset($StockItemsResult)) {

	echo '<TABLE CELLPADDING=2 COLSPAN=7 BORDER=2>';

	$TableHeadings = "<TR><TH>" . _('Code') . "</TH>" .
				"<TH>" . _('Description') . "</TH>" .
				"<TH>" . _('On Hand') . '</TH>' .
				"<TH>" . _('Purchase Orders') . '</TH>' .
				"<TH>" . _('Sales Orders') . "</TH>" .
				"<TH>" . _('Units') . '</TH></TR>';

	echo $TableHeadings;

	$j = 1;
	$k=0; //row colour counter

	while ($myrow=DB_fetch_array($StockItemsResult)) {

		if ($k==1){
			echo '<tr class="EvenTableRows">';
			$k=0;
		} else {
			echo '<tr class="OddTableRows">';
			$k++;
		}

		printf("<td><FONT SIZE=1><INPUT TYPE=SUBMIT NAME='SelectedStockItem' VALUE='%s'</FONT></td>
			<td><FONT SIZE=1>%s</FONT></td>
			<td ALIGN=RIGHT><FONT SIZE=1>%s</FONT></td>
			<td ALIGN=RIGHT><FONT SIZE=1>%s</FONT></td>
			<td ALIGN=RIGHT><FONT SIZE=1>%s</FONT></td>
			<td><FONT SIZE=1>%s</FONT></td></tr>", 
			$myrow['stockid'], 
			$myrow['description'], 
			$myrow['qoh'], 
			$myrow['qoo'],
			$myrow['qdem'],
			$myrow['units']);

//end of page full new headings if
	}
//end of while loop

	echo '</TABLE>';

}
//end if stock search results to show

If (isset($SalesOrdersResult)) {

/*show a table of the orders returned by the SQL */
/*Quincy talbe: 1.Order(Inv),2.Customer,3.Cust PO,4.Product,5.Qty,6.Price, 7.Payment,8.Del Date,9.Delivery No.10.Remark,*/
/*              1.stockmoves.transno(Inv)3.stockmoves.reference   6.price 7.1debtorno=>curr 8.trandate
			     2.stockmoves.debtorno 4.stockid 5.qty         7.price*qty=amount   9.debtortrans.consignment */

	echo "<TABLE class='commontable'>";

	$tableheader = "<TR><TH>" . _('Order(Inv)') . " #</TH>
			<TH>" . _('Customer') . "</TH>
			<TH>" . _('Customer PO') . " #</TH>
			<TH>" . _('Product') . "</TH>
			<TH>" . _('Qty') . " </TH>
			<TH>" . _('Curr') . " </TH>			
			<TH>" . _('Price') . "</TH>
			<TH>" . _('Total') . "</TH>			
			<TH>" . _('Outstanding') . "</TH>			
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
		    //continue;   // hide completed order
		}else if($Balance<0 && -$Balance< $myrow['ordervalue']){
			$outstanding = -$Balance;	
		}
		
		echo '<tr>';
		
		$EdiSentImage =$rootpath.'/css/'.$theme.'/images/'.SID.'sent'.$myrow['edisent'];
		$ViewPage = $rootpath . '/PrintCustTrans.php?' .SID . 'FromTransNo=' . $myrow['transno'].SID.'&InvOrCredit='.'Invoice';
		$FormatedDelDate = ConvertSQLDate($myrow['trandate']);
		$FormatedOrderDate = ConvertSQLDate($myrow['orddate']);

		$FormatedOrderValue = $myrow['ordervalue'];
		$FormatedOrderPrice = number_format($myrow['price'],2);

/*Quincy 0.Order(Inv),1. orderline no.2.Customer,3.Cust PO,4.Product,5.Qty,6.Price, 7.Payment,8.Del Date,9.Delivery No.10.Remark,*/					
		printf("<td><A target='_blank' HREF='%s'>%s-%s</A></td>
			<td>%s</td>
			<td><FONT COLOR=Teal>%s</FONT></td>
			<td>%s</td>
			<td>%s</td>
			<td>%s</td>
			<td>%10.2f</td>			
			<td>%10.2f</td>		
			<td><FONT COLOR=RED>%10.2f</FONT></td>
			<td>%s<A target='_self' HREF='%s'><img src=%s></A></td>
			</tr>", 
			$ViewPage, 
			'SO'.$myrow['transno'],
			$TransLineno,
			$myrow['debtorno'], 
			$myrow['customerref'],
			$myrow['stockid'], 	 
			-$myrow['qty'],
			$myrow['currcode'],
			$myrow['unitprice'],			
			$FormatedOrderValue,						
			$outstanding,					
			$FormatedDelDate,
			$ViewPageWithOp,$EdiSentImage);
			//$myrow['consignment']);//,
			//$myrow['comments']);

			$myrow['alloc'] = 0;
//end of page full new headings if
	}
//end of while loop

	echo '</TABLE>';

}

echo '</form>';
include('includes/footer.inc');

?>
