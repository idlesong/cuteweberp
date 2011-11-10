<?php

$PageSecurity = 2;

include('includes/session.inc');

$title = _('All Stock Movements By Location');

include('includes/header.inc');

echo '<P CLASS="page_title_text">  Manufacture:Home';
echo '<HR>';
//echo '<P CLASS="page_subtitle_text">  >>Product List </P>';

echo '<FORM ACTION="' . $_SERVER['PHP_SELF'] . '?' . SID . '" METHOD=POST>';

echo ' <TABLE class="searchtable">  <TR><TD>' . _('From Stock Location') . ':</TD><TD><SELECT name="StockLocation"> ';

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

echo '</SELECT></TD>';

if (!isset($_POST['BeforeDate']) OR !Is_Date($_POST['BeforeDate'])){
   $_POST['BeforeDate'] = Date($_SESSION['DefaultDateFormat']);
}
if (!isset($_POST['AfterDate']) OR !Is_Date($_POST['AfterDate'])){
   //$_POST['AfterDate'] = Date($_SESSION['DefaultDateFormat'], Mktime(0,0,0,Date('m')-1,Date('d'),Date('y')));
   $_POST['AfterDate'] = Date($_SESSION['DefaultDateFormat'], Mktime(0,0,0,1,1,Date('y')));
}
echo ' <TD>' . _('Date from') . ' </TD> <TD> <INPUT TYPE=TEXT NAME="AfterDate" SIZE=12 MAXLENGTH=12 Value="' . $_POST['AfterDate'] . '"></TD>';
echo ' <TD>' . _('and to') . '</TD><TD> <INPUT TYPE=TEXT NAME="BeforeDate" SIZE=12 MAXLENGTH=12 Value="' . $_POST['BeforeDate'] . '">';
echo ' <TR><TD>' . _('Stock Wafer Types') . ':</TD><TD><SELECT name="StockType"> ';

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
		 echo '<OPTION SELECTED Value="' . $myrow['component'] . '">' . $myrow['component'];
		 $_POST['StockType']=$myrow['component'];
	} else {
		 echo '<OPTION Value="' . $myrow['component'] . '">' . $myrow['component'];
	}
}

echo '</SELECT></TD>';
echo '<TD></TD><TD></TD><TD></TD><TD><INPUT TYPE=SUBMIT NAME="ShowMoves" VALUE="' . _('Show Stock Move History') . '"></TD></TR></TABLE>';

echo "<input onclick=\"javascript:window.location.href='$rootpath/PO_AllInOne.php?&NewOrder=Yes'\"  type='button' value='New Wafer PO' />";
//echo "<input onclick=\"javascript:window.location.href='$rootpath/PO_Header.php?&NewOrder=Yes'\"  type='button' value='New Wafer PO' />";
echo "<input onclick=\"javascript:window.location.href='$rootpath/PackingPO.php?'\"  type='button' value='New Packing PO' />";
echo "<input onclick=\"javascript:window.location.href='$rootpath/SelectWorkOrder.php?'\"  type='button' value='Search Packing PO' />";

echo "--<input onclick=\"javascript:window.location.href='$rootpath/h5ItemsTestFail.php?'\"  type='button' value='Test Failure' />";
echo "<input onclick=\"javascript:window.location.href='$rootpath/h5SalesExchange.php?'\"  type='button' value='Sales Exchange' />";

$SQLBeforeDate = FormatDateForSQL($_POST['BeforeDate']);
$SQLAfterDate = FormatDateForSQL($_POST['AfterDate']);

/* 1.Wafer P/O No. 2.Wafer PO Date 3.Wafer Ship Date 4.Wafer Qty 5.PKG PO No. 6.PKG Vender 7.PKG Lot# 8.PKG PO Date 9.PKG PO Qty10.PKG Recd Qty*/

$sql = "SELECT	purchorders.orderno, 
		purchorders.orddate,
		purchorderdetails.deliverydate, 
		purchorderdetails.itemcode,
		purchorderdetails.quantityord,
		purchorderdetails.quantityrecd, 		
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
/*echo '<TABLE CELLPADDING=5 CELLSPACING=4 BORDER=0>';*/
echo '<TABLE class="commontable">';
$tableheader = '<TR>
		<TH colspan="3" > Wafer PO </TH>
		<TH colspan="8" > Packing PO </TH>
		</TR>
		<TR>
		<TH>' . _('PO No.') . '</TH>
		<TH>' . _('PO Date') . '</TH>
		<TH>' . _('Recv Date') . '</TH>
		<TH>' . _('Wf Qty') . '</TH>
		<TH>' . _('PO No.') . '</TH>
		<TH>' . _('Vender') . '</TH>		
		<TH>' . _('PKG Lot#') . '</TH>
		<TH>' . _('PO Date') . '</TH>
		<TH>' . _('Recv Date') . '</TH>
		<TH>' . _('Qty') . '</TH>
		<TH>' . _('Recd Qty') . '</TH>
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
	//$FirstRecvDate = ConvertSQLDate($myrow['maincomprecv']);
	$FirstRecvDate = substr($myrow['maincomprecv'],5,5);
	$WOStartDate = ConvertSQLDate($myrow['startdate']);
	$WOStartRecvDate = ConvertSQLDate($myrow['recvdatestart']);	
	
	$WaferQty = '('.$myrow['quantityrecd'].'/'.$myrow['quantityord'].')';
	$Receive_WO = $rootpath . '/WorkOrderReceive.php?' . SID . '&WO=' .$myrow['wo'] . '&StockID=' . $myrow['stockid'];	
	
	$ModifyLink = $rootpath . '/WorkOrderReceive.php?' . SID . '&WO=' .$myrow['wo']. SID .'&StockID=' .$myrow['stockid'];
	
	$ModifyImg =$rootpath.'/css/'.$theme.'/images/buy_inline.jpg';		
	
	if( $NewWaferPO != $myrow['orderno'] ){
		$NewWaferPO = $myrow['orderno'];
		$DisplayWaferPO = 'W'.$NewWaferPO;
		$ReceivePOImgLink = "<a target='_blank' href='GoodsReceived.php?" . SID . "&PONumber=".$NewWaferPO."'><img src='".$ModifyImg."'/></a>";			
	}else{
		$DisplayWaferPO = '';
		$POOrderDate = '';
		$FirstRecvDate = '';
		$ReceivePOImgLink = '';		
		$WaferQty = '';		
	}
	
	printf("<td><a target='_blank' href='PO_AllInOne.php?" . SID . "&ModifyOrderNumber=%s'>%s</a></td>
		<td>%s</td>
		<td><FONT COLOR=GREEN>%s %s %s</FONT></td>
		<td>%s</td>
		<td><a target='_blank' href='PackingPO.php?&WO=%s"."&StockID=%s'>%s</td>
		<td>%s</td>
		<td>%s</td>
		<td>%s</td>
		<td><FONT COLOR=GREEN>%s</FONT></td>
		<td align=right>%s</td>
		<td align=right>%s<A target='_self' HREF='%s'>  recv<img src=%s></A></td>
		</tr>",
		$myrow['orderno'],			
		$DisplayWaferPO,
		$POOrderDate,
		$FirstRecvDate,
		$ReceivePOImgLink,$WaferQty,			
		$myrow['keyqtyissued'],
		$myrow['wo'],
		$myrow['stockid'],
		'P'.$myrow['wo'],
		$myrow['vendorid'],			
		$myrow['stockid'],
		$WOStartDate,
		$WOStartRecvDate,
		$myrow['qtyreqd'],
		$myrow['qtyrecd'],$ModifyLink,$ModifyImg);
			
	$j++;
	If ($j == 16){
		$j=1;
		//echo '<hr>';//$tableheader;
	}
//end of page full new headings if
}
//end of while loop

//echo '</TABLE><HR>';


/* 
* Display Wafer POs that haven't been issued. (Quincy)
*/
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
$LeftMovtsResult = DB_query($sql, $db,$ErrMsg);	

$j = 1;
$k=0; //row colour counter

while ($myrow=DB_fetch_array($LeftMovtsResult)) {

	if ($k==1){
		echo '<TR class="OddTableRows">';
		$k=0;
	} else {
		echo '<TR class="EvenTableRows">';
		$k=1;
	}
		$NewWaferPO = $myrow['orderno'];
 		$POOrderDate = ConvertSQLDate($myrow['orddate']);
 		//$PODeliveryDate = ConvertSQLDate($myrow['deliverydate']);
		$PODeliveryDate = substr($myrow['deliverydate'],5,5);
 		$WOStartDate = ConvertSQLDate($myrow['startdate']);
		$WaferQty = $myrow['quantityrecd'].'/'.$myrow['quantityord'];
		$NewPackingPO = $rootpath . '/PackingPO.php?';
 		$OutputBlank = '';
		
		$ModifyImg =$rootpath.'/css/'.$theme.'/images/buy_inline.jpg';
		$ReceivePOImgLink = "<a target='_blank' href='GoodsReceived.php?" . SID . "&PONumber=".$NewWaferPO."'><img src='".$ModifyImg."'/></a>";	
		
		printf("<td><a target='_blank' href='PO_AllInOne.php?" . SID . "&ModifyOrderNumber=%s'>%s</a></td>
			<td>%s</td>
			<td><FONT COLOR=GREEN>%s %s</FONT></td>
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
			$ReceivePOImgLink,
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

/* 
*Display Packing POs that haven't been issued. (Quincy)
*/
$SQL = "SELECT workorders.wo,
	woitems.stockid,
	woitems.qtyreqd,
	woitems.qtyrecd,
	woitems.recvdatestart,
	workorders.vendorid,
	workorders.startdate,	
	workorders.requiredby
	FROM workorders
	INNER JOIN woitems ON workorders.wo=woitems.wo
	INNER JOIN bom ON bom.parent=woitems.stockid
	WHERE workorders.closed='Open_Only'
	AND bom.component='" .$_POST['StockType']. "'
	AND workorders.keyqtyissued = 0
	ORDER BY workorders.wo, woitems.stockid";
	
	$ErrMsg = _('No works orders were returned by the SQL because');
	$UnIssueWorkOrdersResult = DB_query($SQL,$db,$ErrMsg);	
	
	$j = 1;
	$k=0; //row colour counter

while ($myrow=DB_fetch_array($UnIssueWorkOrdersResult)) {
	///////////////////////////////
	if ($k==1){
		echo '<TR class="OddTableRows">';
		$k=0;
	} else {
		echo '<TR class="EvenTableRows">';
		$k=1;
	}

	$WOStartDate = ConvertSQLDate($myrow['startdate']);
	$WOStartRecvDate = ConvertSQLDate($myrow['recvdatestart']);	
	//$Receive_WO = $rootpath . '/WorkOrderReceive.php?' . SID . '&WO=' .$myrow['wo'] . '&StockID=' . $myrow['stockid'];
	$Issue_WO = $rootpath . '/WorkOrderIssue.php?' . SID . '&WO=' .$myrow['wo'] . SID .'&StockID=' . $myrow['stockid'];
	//<td><A HREF='%s'>" . _('Issue To') . "</A></td>
	$ModifyLink = $rootpath . '/WorkOrderReceive.php?' . SID . '&WO=' .$myrow['wo']. SID .'&StockID=' .$myrow['stockid'];
	
	$ModifyImg =$rootpath.'/css/'.$theme.'/images/buy_inline.jpg';		
	
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
		<td><a href='%s'>" . _('Issue') . "</a></td>
		<td><a href='PackingPO.php?&WO=%s"."&StockID=%s'>%s</td>
		<td>%s</td>
		<td>%s</td>
		<td>%s</td>
		<td><FONT COLOR=GREEN>%s</FONT></td>
		<td align=right>%s</td>
		<td align=right>%s<A target='_self' HREF='%s'>  recv<img src=%s></A></td>
		</tr>",
		$OutputBlank,
		$OutputBlank,
		$OutputBlank,
		$OutputBlank,
		$Issue_WO,
		$myrow['wo'],
		$myrow['stockid'],
		'P'.$myrow['wo'],
		$myrow['vendorid'],			
		$myrow['stockid'],
		$WOStartDate,
		$WOStartRecvDate,
		$myrow['qtyreqd'],
		$myrow['qtyrecd'],$ModifyLink,$ModifyImg);
			
	$j++;
	If ($j == 16){
		$j=1;
		//echo '<hr>';//$tableheader;
	}	
//end of page full new headings if
}
//end of while loop	

echo '</TABLE><HR>';

echo '</form>';

include('includes/footer.inc');

?>