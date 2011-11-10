<?php

/* $Revision: 1.32 $ */
//echo '<head>
//<link href="/weberp/css/silverwolf/default.css" rel="stylesheet" type="text/css">
//</head>';

$PageSecurity = 4;

//include('includes/DefinePOClass.php');
include('includes/DefineStockAdjustment.php');
include('includes/SQL_CommonFunctions.inc');

/* Session started in header.inc for password checking and authorisation level check */
include('includes/session.inc');
$title = _('Sales Exchange');

include('includes/header.inc');

echo '<P CLASS="page_title_text">  SalesExchange';
echo '<HR>';

// Set the $_SESSION['SalesEx'] by default;
if($_POST['Refresh'] ){
	echo "Refresh .....................";
	unset( $_SESSION['SalesEx'] );
	$_SESSION['SalesEx'] = new SalesExchange;
	
	if( !isset( $_SESSION['SalesEx']->ActiveLine) ){
		echo "Pls filllllllll Active";
	}	
}

if($_POST['Cancel']){
	$_SESSION['SalesEx']->NewLineEdit = false;
}

if( !isset($_SESSION['SalesEx'])){
	$_SESSION['SalesEx'] = new SalesExchange;
}

/* Can't treat messages as POST, save in the SESSION */
if(isset( $_SESSION['msg_SelectedCustomer']) and $_SESSION['msg_SelectedCustomer'] != ''){
	if( isset($_SESSION['SalesEx']->ActiveLine) ){
		$CustomerInfo = $_SESSION['msg_SelectedCustomer'];
		$_SESSION['SalesEx']->ActiveLine->CustomerID  = substr($CustomerInfo,0,strpos($CustomerInfo,' - '));

		//echo 'Seeeeeeeeee'.$_SESSION['msg_SelectedCustomer'].''.$_SESSION['SalesEx']->ActiveLine->CustomerID;
		unset( $_SESSION['msg_SelectedCustomer'] );
	}
}

if(isset( $_SESSION['msg_SelectedItem']) and $_SESSION['msg_SelectedItem'] != ''){
    $_SESSION['SalesEx']->ActiveLine->StkCode = $_SESSION['msg_SelectedItem'];
	unset( $_SESSION['msg_SelectedItem'] );
}

if(isset( $_POST['AddLine'] )){ // Blank the Active the CustomerID&StkCode when Add a Line
	//echo "Addddddddddddd new Line";
	$_SESSION['SalesEx']->ActiveLine->CustomerID = '';
	$_SESSION['SalesEx']->ActiveLine->StkCode = '';
	$_SESSION['SalesEx']->NewLineEdit = true;
}

if(isset($_POST['Update']) and $_POST['Update'] != '' and $_POST['EditRtnQty']>0 and Is_Date($_POST['ReturnDate'])){
	//echo 'LineNub'.$_POST['Update'];
	$_SESSION['SalesEx']->updateSalesExQty($_POST['Update'] , $_POST['ReturnDate'], $_POST['EditRtnQty']);
}

if(isset($_POST['UpdateEx']) and $_POST['UpdateEx'] != '' and $_POST['EditExQty']>0){
	//echo 'LineNub'.$_POST['UpdateEx'].'Qty:'.$_POST['EditExQty'];
	
	$_SESSION['SalesEx']->getSalesExItemDetails( $_POST['UpdateEx'] ); //get SalesEx LineDetails to ActiveLine
	$ExStkCode = $_SESSION['SalesEx']->ActiveLine->StkCode;
	$ExCustID = $_SESSION['SalesEx']->ActiveLine->CustomerID;
	$QtyReturned = $_SESSION['SalesEx']->ActiveLine->Qty;
	$ExRtnDate = $_SESSION['SalesEx']->ActiveLine->ReturnDate;
	$QtyTotalEx = $_SESSION['SalesEx']->ActiveLine->TotalQtyExchanged;
	
	if( $QtyTotalEx + $_POST['EditExQty'] <= $QtyReturned ){
		$SalesExAdjust = new StockAdjustment;
		//$_POST['EditExQty'] ==> -$_POST['EditExQty']
		$SalesExAdjust->processStockAdjustment($ExStkCode, -$_POST['EditExQty'], 'HZ', 'SalesEx 4 '.$ExCustID.' return@'.$ExRtnDate, 1, '');	
		
		// Update the SalesExchange table
		$_SESSION['SalesEx']->updateSalesExExchangeQty( $_POST['UpdateEx'], $_POST['EditExQty'], $_POST['Note'], 0 );
		unset( $SalesExAdjust );
	}else{
		//Warning
		prnMsg( _('Warning: The total Quantity cannot over the return!'),'warn');
	}
}


if(isset( $_POST['SaveNewLine'] )){ // Update or save a new line
	
	if( $_SESSION['SalesEx']->ActiveLine->CustomerID != '' and $_SESSION['SalesEx']->ActiveLine->StkCode != '' and $_POST['ReturnQty'] != '' ){
		$SQLReturnDate = FormatDateForSQL($_POST['ReturnDate']);
		
		$SQL = "INSERT INTO salesexchange(
			customerid,
			stkcode,
			qty,
			returndate
			)
		  VALUES ( 
		    '" .$_SESSION['SalesEx']->ActiveLine->CustomerID. "',
			'" . $_SESSION['SalesEx']->ActiveLine->StkCode . "',
			'" .$_POST['ReturnQty'] ."',
			'" . $SQLReturnDate . "')";
			
		$ErrMsg = _('Cannot insert a entry for the SalesEx because');
		$DbgMsg = _('The SQL that failed to insert the SalesEx entry was');
		$result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);
	}
		
	$_SESSION['SalesEx']->NewLineEdit = false;	
}


/*
*	Search items by default
*/
if(1){
	$sql = 'SELECT salesexchange.exno,
			salesexchange.customerid,
			salesexchange.stkcode,
			salesexchange.qty,
			salesexchange.returndate,
			salesexchange.qtyexchanged,
			salesexchange.exchangedate,
			salesexchange.reference,
			salesexchange.remark,
			salesexchange.completed
		FROM salesexchange
		WHERE 1
		ORDER BY salesexchange.exno';
	
	$ErrMsg = _('There is a problem selecting the part records to display because');
	$DbgMsg = _('The SQL statement that failed was');
	$SearchResult = DB_query($sql,$db,$ErrMsg,$DbgMsg);

	if (DB_num_rows($SearchResult)==0 && $debug==1){
		prnMsg( _('There are no products to display matching the criteria provided'),'warn');
	}
	if (DB_num_rows($SearchResult)==1){

		$myrow=DB_fetch_array($SearchResult);
	}
}
/* This is where the order as selected should be displayed  reflecting any deletions or insertions*/
echo '<form action="' . $_SERVER['PHP_SELF'] . '?' . SID . '" method=post>';

if (!isset($_POST['ReturnDate']) OR !Is_Date($_POST['ReturnDate'])){
   $_POST['ReturnDate'] = Date($_SESSION['DefaultDateFormat']);
}

if (!isset($_POST['ExDate']) OR !Is_Date($_POST['ExDate'])){
   $_POST['ExDate'] = Date($_SESSION['DefaultDateFormat']);
}

//$PartsDisplayed =0;
if (isset($SearchResult)) {

	echo "<table class='commontable'>";

	$tableheader = '<tr>
			<th>' . _('#') . '</th>
			<th>' . _('CustomerID') . '</th>
			<th>' . _('Stk code') . '</th>
			<th>' . _('Qty') . '</th>			
			<th>' . _('Recv date') . '</th>			
			<th>' . _('QtyEx') . '</th>
			<th>' . _('ExDate') . '</th>
			<th>' . _('Remark') . '</th>		
			<th></th>
			</tr>';
	echo $tableheader;


	$ActLine= $_SESSION['SalesEx']->ActiveLine;	
		
	while ($myrow=DB_fetch_array($SearchResult)) {		
		$exchangeNo =  $myrow['exno'];
		echo '<input type="hidden" name="InputExNo" value="'.$exchangeNo.'">';		
		
		if( isset($_POST['Edit']) and $_POST['Edit'] == $exchangeNo ){// Input customer return info		
			$menuLink = '<img src="editgray.jpg" alt="Edit">
				<img src="'.$rootpath.'/css/'.$theme.'/images/supplier.png" alt="Exchange">
				<input type=image name="Update" value="'.$exchangeNo.'" src="save.png">
				<img src="deletegray.jpg" alt="Delete">';
		
			echo '<td>'.$myrow['exno'].'</td><td>'.$myrow['customerid'].'</td><td>'.$myrow['stkcode'].'</td>
			<td><input size="6" type="text" name="EditRtnQty" value='.$myrow['qty'].'></td>
			<td><input size="6" type="text" name="ReturnDate" value='.$myrow['returndate'].'></td>
			<td>'.$myrow['qtyexchanged'].'</td><td>'.$myrow['exchangedate'].'</td>
			<td>'.$myrow['remark'].'</td><td>'.$menuLink.'</td></tr>';
		}else if( isset($_POST['Exchange']) and $_POST['Exchange'] == $exchangeNo ){// Input Sales resend
			$menuLink = '<img src="editgray.jpg" alt="Edit">
				<img src="'.$rootpath.'/css/'.$theme.'/images/supplier.png" alt="Exchange">
				<input type=image name="UpdateEx" value="'.$exchangeNo.'" src="save.png">
				<img src="deletegray.jpg" alt="Delete">';
		
			echo '<td>'.$myrow['exno'].'</td><td>'.$myrow['customerid'].'</td><td>'.$myrow['stkcode'].'</td>
			<td>'.$myrow['qty'].'</td><td>'.$myrow['returndate'].'</td>
			<td><input size="6" type="text" name="EditExQty" value='.$myrow['qtyexchanged'].'></td>
			<td>'.$myrow['exchangedate'].'</td>
			<td><input size="6" type="text" name="Note" value='.$myrow['remark'].'></td>
			<td>'.$menuLink.'</td></tr>';				
		}else{
			if( $myrow['qtyexchanged'] > 0 ){ // exchanged 
				if( $myrow['qty'] >  $myrow['qtyexchanged']  ){ 
					$menuLink = '<img src="editgray.jpg" alt="Edit">
								<input type=image name="Exchange" value="'.$exchangeNo.'" src="'.$rootpath.'/css/'.$theme.'/images/supplier.png">
								<img src="savegray.png" alt="save">
								<img src="deletegray.jpg" alt="Delete">';		
				}else{ //compelete 
					$menuLink = '<img src="editgray.jpg" alt="Edit">
								<img src="'.$rootpath.'/css/'.$theme.'/images/suppliergray.png" alt="Exch">
								<img src="savegray.png" alt="save">
								<img src="deletegray.jpg" alt="Delete">';	
				}
			}else{ // unexchanged
				$menuLink = '<input type=image name="Edit"    value="'.$exchangeNo.'" src="edit.jpg">
							<input type=image name="Exchange" value="'.$exchangeNo.'" src="'.$rootpath.'/css/'.$theme.'/images/supplier.png">
							<img src="savegray.png" alt="save">
							<input type=image name="Delete" value="Unsave" src="delete.jpg">';	
			}							
			echo '<td>'.$myrow['exno'].'</td><td>'.$myrow['customerid'].'</td><td>'.$myrow['stkcode'].'</td>
			<td>'.$myrow['qty'].'</td><td>'.$myrow['returndate'].'</td><td>'.$myrow['qtyexchanged'].'</td><td>'.$myrow['exchangedate'].'</td>
			<td>'.$myrow['remark'].'</td><td>'.$menuLink.'</td></tr>';		
		}		
     #end of page full new headings if
	}
	
	//Display a input line form for Sales exchange
	if (!isset($_SESSION['SalesEx']->NewLineEdit)){
		echo "Unespect clear";
	}
	if(!isset($_SESSION['SalesEx'])){
		echo "SalesEx unsetteddddddddddddddd";
	}
	
	if($_SESSION['SalesEx']->NewLineEdit == true ){	
		//echo 'Custommmmmmmmmmmm:'.$ActLine->CustomerID.''.$_SESSION['SalesEx']->ActiveLine->CustomerID;
		echo '<td></td>
		<td>'.$ActLine->CustomerID.'<input onclick=openSubpage("h5CustomerSearch.php") type="image" src="search.png" value="Select products" /></td>
		<td>'.$ActLine->StkCode.'<input onclick=openSubpage("h5ItemsSearch.php") type="image" src="search.png" value="Select products" /></td>			 
		<td><input size="6" type="text" name="ReturnQty" value='.$_POST['ReturnQty'].' ></td>
		<td><input size="6" type="text" name="ReturnDate" value='.$_POST['ReturnDate'].'></td>		
		<td colspan="3"></td>
		<td class="menu"><img src="editgray.jpg" alt="edit">
		<input type=image name="SaveNewLine" value="Save" src="save.png">
		<input type=image name="Delete" value="Unsave" src="delete.jpg">  </td>
		</tr>';
	}	
	
	echo '<tr><td colspan="8"></td><td><input type=submit name="AddLine" value="Add Line" />';
	
	echo '</table>';
	echo '<p  align="right" ><input type=submit name="Cancel" value="Cancel" /></p>';
	

}

echo '</form>';


include('includes/footer.inc');
?>
