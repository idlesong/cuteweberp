<?php

$PageSecurity = 2;

include('includes/session.inc');

$title = _('All Stock Movements By Location');

include('includes/header.inc');

echo '<FORM ACTION="' . $_SERVER['PHP_SELF'] . '?' . SID . '" METHOD=POST>';

echo '  ' . _('From Stock Location') . ':<SELECT name="StockLocation"> ';

$sql = 'SELECT loccode, locationname FROM locations';
$resultStkLocs = DB_query($sql,$db);
while ($myrow=DB_fetch_array($resultStkLocs)){
	if (isset($_POST['StockLocation']) AND $_POST['StockLocation']!='All'){
		if ($myrow['loccode'] == $_POST['StockLocation']){
		     echo '<OPTION SELECTED Value="' . $myrow['loccode'] . '">' . $myrow['locationname'];
		} else {
		     echo '<OPTION Value="' . $myrow['loccode'] . '">' . $myrow['locationname'];
		}
	} elseif ($myrow['loccode']==$_SESSION['UserStockLocation']){
		 echo '<OPTION SELECTED Value="' . $myrow['loccode'] . '">' . $myrow['locationname'];
		 $_POST['StockLocation']=$myrow['loccode'];
	} else {
		 echo '<OPTION Value="' . $myrow['loccode'] . '">' . $myrow['locationname'];
	}
}

echo '</SELECT><BR>';

////////////// Item Catalogs /////////////////
echo '  ' . _('Stock Types') . ':<SELECT name="StockType"> ';

$sql = 'SELECT component, parent FROM bom GROUP BY component';
$resultStkTypes = DB_query($sql,$db);
while ($myrow=DB_fetch_array($resultStkTypes)){

	if (isset($_POST['StockType']) ){
		if ($myrow['component'] == $_POST['StockType']){
		     echo '<OPTION SELECTED Value="' . $myrow['component'] . '">' . $myrow['component'];
		} else {
		     echo '<OPTION Value="' . $myrow['component'] . '">' . $myrow['component'];
		}
	} elseif ($myrow['component']==$_SESSION['UserStockType']){
		 echo '<OPTION SELECTED Value="' . $myrow['StockType'] . '">' . $myrow['StockType'];
		 $_POST['StockType']=$myrow['component'];
	} else {
		 echo '<OPTION Value="' . $myrow['StockType'] . '">' . $myrow['StockType'];
	}
}

echo '</SELECT><BR>';

if (!isset($_POST['BeforeDate']) OR !Is_Date($_POST['BeforeDate'])){
   $_POST['BeforeDate'] = Date($_SESSION['DefaultDateFormat']);
}
if (!isset($_POST['AfterDate']) OR !Is_Date($_POST['AfterDate'])){
   //$_POST['AfterDate'] = Date($_SESSION['DefaultDateFormat'], Mktime(0,0,0,Date('m')-1,Date('d'),Date('y')));
   $_POST['AfterDate'] = Date($_SESSION['DefaultDateFormat'], Mktime(0,0,0,1,1,2008));
}
echo ' ' . _('Show Movements before') . ': <INPUT TYPE=TEXT NAME="BeforeDate" SIZE=12 MAXLENGTH=12 Value="' . $_POST['BeforeDate'] . '">';
echo ' ' . _('But after') . ': <INPUT TYPE=TEXT NAME="AfterDate" SIZE=12 MAXLENGTH=12 Value="' . $_POST['AfterDate'] . '">';
echo ' <INPUT TYPE=SUBMIT NAME="ShowMoves" VALUE="' . _('Show Stock Move History') . '">';
echo "<A HREF='" . $rootpath . '/PO_Header.php?&NewOrder=Yes' . SID . "'>" . _('Add a Wafer PO') . '</A>';
echo "<A HREF='" . $rootpath . '/WorkOrderEntry.php?' . SID . "'>" . _('   |  Add a Packing PO') . '</A>';
echo "<A HREF='" . $rootpath . '/SelectWorkOrder.php?' . SID . "'>" . _('   |  Packing PO Inquiry') . '</A>';
echo '<HR>';


$SQLBeforeDate = FormatDateForSQL($_POST['BeforeDate']);
$SQLAfterDate = FormatDateForSQL($_POST['AfterDate']);

//$SQLAfterDate

/* 1.Wafer P/O No. 2.Wafer PO Date 3.Wafer Ship Date 4.Wafer Qty 5.PKG PO No. 6.PKG Vender 7.PKG Lot# 8.PKG PO Date 9.PKG PO Qty10.PKG Recd Qty*/
//purchorderdetails.itemcode
$sql = "SELECT	purchorders.orderno, 
		purchorders.orddate,
		purchorderdetails.deliverydate, 
		purchorderdetails.itemcode,
		woitems.wo,
		workorders.keyqtyissued,
		workorders.loccode,		
		woitems.nextlotsnref, 
		workorders.startdate,
		workorders.requiredby,
		woitems.qtyreqd,
		woitems.qtyrecd,
		woitems.maincomprecv,
		woitems.recvdatestart,
		woitems.stockid,
		workorders.vendorid 
	FROM purchorders, 
		purchorderdetails, 
		woitems, 
		workorders,
		bom 
	WHERE purchorders.orderno = purchorderdetails.orderno 
	AND purchorders.orderno = woitems.maincomppo
	AND workorders.wo = woitems.wo 
	AND bom.parent = woitems.stockid
	AND purchorders.orddate  >= '". $SQLAfterDate . "'
	AND purchorders.orddate  <= '" . $SQLBeforeDate . "'
	AND purchorderdetails.itemcode = '".$_POST['StockType']."'
	GROUP BY purchorders.orderno, 
		woitems.wo
	ORDER BY purchorders.orderno";
	
$ErrMsg = _('The stock movements for the selected criteria could not be retrieved because');
$MovtsResult = DB_query($sql, $db,$ErrMsg);	

/* 1.Wafer P/O No. 2.Wafer PO Date 3.Wafer Ship Date 4.Wafer Qty 5.PKG PO No. 6.PKG Vender 7.PKG Lot# 8.PKG PO Date 9.PKG PO Qty10.PKG Recd Qty*/
echo '<TABLE CELLPADDING=5 CELLSPACING=4 BORDER=0>';
$tableheader = '<TR>
		<TH>' . _('Wafer P/O No.') . '</TH>
		<TH>' . _('Wafer PO Date') . '</TH>
		<TH>' . _('1st Recv Date') . '</TH>
		<TH>' . _('Wafer Qty') . '</TH>
		<TH>' . _('PKG PO No.') . '</TH>
		<TH>' . _('PKG Vender') . '</TH>		
		<TH>' . _('PKG Lot#') . '</TH>
		<TH>' . _('PKG PO Date') . '</TH>
		<TH>' . _('1st Recv Date') . '</TH>
		<TH>' . _('PKG Order Qty') . '</TH>
		<TH>' . _('PKG Recd Qty') . '</TH>
		</TR>';
echo $tableheader;

$j = 1;
$k=0; //row colour counter

while ($myrow=DB_fetch_array($MovtsResult)) {

	if ($k==1){
		echo '<TR class="OddTableRows">';
		$k=0;
	} else {
		echo '<TR class="EvenTableRows">';
		$k=1;
	}
	
 		$POOrderDate = ConvertSQLDate($myrow['orddate']);
 		$PODeliveryDate = ConvertSQLDate($myrow['deliverydate']);
 		$FirstRecvDate = ConvertSQLDate($myrow['maincomprecv']);
 		$WOStartDate = ConvertSQLDate($myrow['startdate']);
 		$WOStartRecvDate = ConvertSQLDate($myrow['recvdatestart']);	
 		$Receive_WO = $rootpath . '/WorkOrderReceive.php?' . SID . '&WO=' .$myrow['wo'] . '&StockID=' . $myrow['stockid'];	
		
		if( $NewWaferPO != $myrow['orderno'] ){
			$NewWaferPO = $myrow['orderno'];
			$DisplayWaferPO = 'W'.$NewWaferPO;
		}else{
			$DisplayWaferPO = '';
			$POOrderDate = '';
			$FirstRecvDate = '';			
		}
		printf("<td><a target='_blank' href='GoodsReceived.php?" . SID . "&PONumber=%s'>%s</td>
			<td>%s</td>
			<td><FONT COLOR=GREEN>%s</FONT></td>
			<td>%s</td>
			<td><a target='_blank' href='WorkOrderReceive.php?" . SID . "&WO=%s"."&StockID=%s'>%s</td>
			<td>%s</td>
			<td>%s</td>
			<td>%s</td>
			<td><FONT COLOR=GREEN>%s</FONT></td>
			<td>%s</td>
			<td>%s</td>
			</tr>",
			$myrow['orderno'],
			$DisplayWaferPO,
			$POOrderDate,
			$FirstRecvDate,
			$myrow['keyqtyissued'],
			$myrow['wo'],
			$myrow['stockid'],
			'P'.$myrow['wo'],
			$myrow['vendorid'],			
			$myrow['stockid'],
			$WOStartDate,
			$WOStartRecvDate,
			$myrow['qtyreqd'],
			$myrow['qtyrecd']);
			
	$j++;
	If ($j == 16){
		$j=1;
		//echo '<hr>';//$tableheader;
	}
//end of page full new headings if
}
//end of while loop

echo '</TABLE><HR>';


/* Quincy: Add the PO that haven't been issued. */
$sql = "SELECT	purchorders.orderno, 
		purchorders.orddate,
		purchorderdetails.itemcode,
		purchorderdetails.deliverydate,	
		purchorderdetails.quantityord,
		purchorderdetails.quantityrecd  	
	FROM 	purchorders INNER JOIN purchorderdetails 
		ON purchorders.orderno = purchorderdetails.orderno, 
		woitems
	WHERE purchorders.orderno = purchorderdetails.orderno 
	AND purchorderdetails.itemcode = '". $_POST['StockType'] . "'
	AND purchorders.orddate  >= '". $SQLAfterDate . "'
	AND purchorders.orddate  <= '" . $SQLBeforeDate . "'
	AND purchorders.orderno not in ( SELECT woitems.maincomppo FROM woitems)
	GROUP BY purchorders.orderno
	ORDER BY purchorders.orderno";
	
$ErrMsg = _('The stock movements for the selected criteria could not be retrieved because');
$MovtsResult = DB_query($sql, $db,$ErrMsg);	

/* 1.Wafer P/O No. 2.Wafer PO Date 3.Wafer Ship Date 4.Wafer Qty 5.PKG PO No. 6.PKG Vender 7.PKG Lot# 8.PKG PO Date 9.PKG PO Qty10.PKG Recd Qty*/
echo '<TABLE CELLPADDING=5 CELLSPACING=4 BORDER=0>';
$tableheader = '<TR>
		<TH>' . _('Wafer P/O No.') . '</TH>
		<TH>' . _('Wafer PO Date') . '</TH>
		<TH>' . _('Confirm Date') . '</TH>
		<TH>' . _('Qty-Recv') . '</TH>
		<TH>' . _('PKG PO NO.') . '</TH>
		<TH>' . _('PKG Vender') . '</TH>		
		<TH>' . _('PKG Lot#') . '</TH>
		<TH>' . _('PKG PO Date') . '</TH>
		<TH>' . _('1st Recv Date') . '</TH>
		<TH>' . _('PKG Order Qty') . '</TH>
		<TH>' . _('PKG Recd Qty') . '</TH>
		</TR>';
echo $tableheader;

$j = 1;
$k=0; //row colour counter

while ($myrow=DB_fetch_array($MovtsResult)) {

	if ($k==1){
		echo '<TR class="OddTableRows">';
		$k=0;
	} else {
		echo '<TR class="EvenTableRows">';
		$k=1;
	}
 		$POOrderDate = ConvertSQLDate($myrow['orddate']);
 		$PODeliveryDate = ConvertSQLDate($myrow['deliverydate']);
 		$WOStartDate = ConvertSQLDate($myrow['startdate']);
		$WaferQty = $myrow['quantityrecd'].'/'.$myrow['quantityord'];
		$NewPackingPO = $rootpath . '/WorkOrderEntry.php?';
 		$OutputBlank = '';
		printf("<td><a target='_blank' href='GoodsReceived.php?" . SID . "&PONumber=%s'>%s</td>
			<td>%s</td>
			<td><FONT COLOR=GREEN>%s</FONT></td>
			<td>%s</td>
			<td><A HREF='%s'>" . _('AddPKGPO') . "</td>
			<td>%s</td>
			<td>%s</td>
			<td>%s</td>			
			<td>%s</td>
			<td>%s</td>
			<td>%s</td>
			</tr>",
			$myrow['orderno'],
			'W'.$myrow['orderno'],
			$POOrderDate,
			$PODeliveryDate,		
			$WaferQty,
			$NewPackingPO,	
			$OutputBlank,			
			$OutputBlank,
			$OutputBlank,
			$OutputBlank,			
			$OutputBlank,
			$OutputBlank);
			
	$j++;
	If ($j == 16){
		$j=1;
		echo $tableheader;
	}
//end of page full new headings if
}
//end of while loop

echo '</TABLE><HR>';

echo '</form>';

include('includes/footer.inc');

?>