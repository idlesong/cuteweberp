<?php

$PageSecurity = 4;
include('includes/DefinePOClass.php');
include('includes/session.inc');

if (isset($_GET['ModifyOrderNumber'])) {
	$title = _('Modify Purchase Order') . ' ' . $_GET['ModifyOrderNumber'];
} else {
	$title = _('Purchase Order Entry');
}

include('includes/header.inc');
include('includes/SQL_CommonFunctions.inc');
/* Globle var Session & message */ 

if ( isset($_SESSION['SupplierSelected']) and $_SESSION['SupplierSelected'] == true ){
	$_POST['ChangeSupplier'] = $_SESSION['SupplierSelected']; // treat message as post
	unset($_SESSION['SupplierSelected']);
}

if ( isset($_SESSION['msg_SelectedItem']) and $_SESSION['msg_SelectedItem'] != '' ){
	$_POST['NewItemSelected'] = $_SESSION['msg_SelectedItem']; // treat message as post
	unset($_SESSION['msg_SelectedItem']);
}

if ( isset($_SESSION['TempItemDes']) and  $_SESSION['TempItemDes'] != ''){ //
	$_POST['ItemDescription'] = $_SESSION['TempItemDes'];
	unset($_SESSION['TempItemDes']);
}

/*
* initialize the order, when $_GET['NewOrder'] 
*/
if (isset($_GET['NewOrder'])) {
	/**
	 * initialize a new order
	 */
	$_SESSION['ExistingOrder']=0;
	unset($_SESSION['PO']);
	/* initialize new class object */
	$_SESSION['PO'] = new PurchOrder;
	/**
	 * and fill it with essential data
	 */
	$_SESSION['PO']->AllowPrintPO = 1; /* Of course cos the
	* order aint even started !!*/
	$_SESSION['PO']->GLLink = $_SESSION['CompanyRecord']['gllink_stock'];
	/* SupplierID needed select first */
	$_SESSION['RequireSupplierSelection'] = 1;
}

//echo '<br>StoBefSave0AA:'.$_SESSION['PO']->LineItems[1]->StockID;
/*
*  initialize the order items, when the supplier changed
*/

if (isset($_GET['ModifyOrderNumber'])) {
      include ('includes/PO_ReadInOrder.inc');
}

if (isset($_POST['CancelOrder']) ) {
	/* The cancel button on the header screen - to delete order */
	$OK_to_delete = 1;	 //assume this in the first instance

	if(!isset($_SESSION['ExistingOrder']) OR $_SESSION['ExistingOrder']!=0) {
		/* need to check that not already dispatched or invoiced
		 * by the supplier */

		if($_SESSION['PO']->Any_Already_Received()==1){
			$OK_to_delete =0;
			prnMsg( _('This order cannot be cancelled because some of it has already been received') . '. ' . _('The line item quantities may be modified to quantities more than already received') . '. ' . _('Prices cannot be altered for lines that have already been received and quantities cannot be reduced below the quantity already received'),'warn');
		}
	}

	if ($OK_to_delete == 1) {
		unset($_SESSION['PO']->LineItems);
		unset($_SESSION['PO']);
		$_SESSION['PO'] = new PurchOrder;
		$_SESSION['RequireSupplierSelection'] = 1;

		if($_SESSION['ExistingOrder'] != 0) {

			$SQL = 'DELETE FROM purchorderdetails WHERE purchorderdetails.orderno =' . $_SESSION['ExistingOrder'];
			$ErrMsg = _('The order detail lines could not be deleted because');
			$DelResult=DB_query($SQL,$db,$ErrMsg);

			$SQL = 'DELETE FROM purchorders WHERE purchorders.orderno=' . $_SESSION['ExistingOrder'];
			$ErrMsg = _('The order header could not be deleted because');
			$DelResult=DB_query($SQL,$db,$ErrMsg);
		 }
	}
}


if (isset($_POST['ChangeSupplier'])) {
	/* change supplier only allowed with appropriate permissions -
	 * button only displayed to modify is AccessLevel >10
	 *  (see below) */
	if ($_SESSION['PO']->Any_Already_Received() == 0) {
		unset($_SESSION['PO']->LineItems);
		$_SESSION['RequireSupplierSelection'] = 0;
	} else {
		echo '<BR><BR>';
		prnMsg(_('Cannot modify the supplier of the order once some of the order has been received'),'warn');
	}
}


if (isset($_POST['Commit'])){ /*User wishes to commit the order to the database */

 /*First do some validation
	  Is the delivery information all entered*/
	$InputError=0; /*Start off assuming the best */
	if ($_SESSION['PO']->DelAdd1=='' or strlen($_SESSION['PO']->DelAdd1)<3){
	      prnMsg( _('The purchase order can not be committed to the database because there is no delivery steet address specified'),'error');
	      $InputError=1;
//	} elseif ($_SESSION['PO']->DelAdd2=='' or strlen($_SESSION['PO']->DelAdd2)<3){
//	      prnMsg( _('The purchase order can not be committed to the database because there is no suburb address specified'),'error');
//	      $InputError=1;
	} elseif ($_SESSION['PO']->Location=='' or ! isset($_SESSION['PO']->Location)){
	      prnMsg( _('The purchase order can not be committed to the database because there is no location specified to book any stock items into'),'error');
	      $InputError=1;
	} elseif ($_SESSION['PO']->LinesOnOrder <=0){
	     prnMsg( _('The purchase order can not be committed to the database because there are no lines entered on this order'),'error');
	     $InputError=1;
	}

	if ($InputError!=1){
		 $sql = 'BEGIN';
		 $result = DB_query($sql,$db);

		 if ($_SESSION['ExistingOrder']==0){ /*its a new order to be inserted */
		 //Quincy:Get the auto increment value of the order number created from the SQL above 
		     $_SESSION['PO']->OrderNo =  GetNextTransNo(18, $db);		     
		     //echo $_SESSION['PO']->OrderNo;
		     //echo 'Hello world!<br>'; // Quincy
		     /*Insert to purchase order header record */
		     $sql = 'INSERT INTO purchorders (orderno,
		     					supplierno,
		     					comments,
							orddate,
							rate,
							initiator,
							requisitionno,
							intostocklocation,
							deladd1,
							deladd2,
							deladd3,
							deladd4,
							deladd5,
							deladd6)
				VALUES(
				'."'" . $_SESSION['PO']->OrderNo . "'".',
				'."'" . $_SESSION['PO']->SupplierID . "'".',
				'."'" . $_SESSION['PO']->Comments . "'".',
				'."'" . Date("Y-m-d") . "'".',
				' . $_SESSION['PO']->ExRate . ',
				'."'" . $_SESSION['PO']->Initiator . "'".',
				'."'" . $_SESSION['PO']->RequisitionNo . "'".',
				'."'" . $_SESSION['PO']->Location . "'".',
				'."'" . $_SESSION['PO']->DelAdd1 . "'".',
				'."'" . $_SESSION['PO']->DelAdd2 . "'".',
				'."'" . $_SESSION['PO']->DelAdd3 . "'".',
				'."'" . $_SESSION['PO']->DelAdd4 . "'".',
				'."'" . $_SESSION['PO']->DelAdd5 . "'".',
				'."'" . $_SESSION['PO']->DelAdd6 . "'".'
				)';

			$ErrMsg =  _('The purchase order header record could not be inserted into the database because');
			$DbgMsg = _('The SQL statement used to insert the purchase order header record and failed was');
			$result = DB_query($sql,$db,$ErrMsg,$DbgMsg,true);

		     /*Get the auto increment value of the order number created from the SQL above */
		     //$_SESSION['PO']->OrderNo =  GetNextTransNo(18, $db);
		     //echo $_SESSION['PO']->OrderNo;

		     /*Insert the purchase order detail records */
		     foreach ($_SESSION['PO']->LineItems as $POLine) {
				//$POLine->GLCode  = 0;
			 	if ($POLine->Deleted==false) {
					$sql = 'INSERT INTO purchorderdetails (
							orderno,
							itemcode,
							deliverydate,
							itemdescription,
							glcode,
							unitprice,
							quantityord,
							shiptref,
							jobref
							)
						VALUES (
							' . $_SESSION['PO']->OrderNo . ',
							'."'" . $POLine->StockID . "'".',
							'."'" . FormatDateForSQL($POLine->ReqDelDate) . "'".',
							'."'" . $POLine->ItemDescription . "'".',
							' . $POLine->GLCode . ',
							' . $POLine->Price . ',
							' . $POLine->Quantity . ',
							'."'" . $POLine->ShiptRef . "'".',
							'."'" . $POLine->JobRef . "'".'
						)';
					$ErrMsg =_('One of the purchase order detail records could not be inserted into the database because');
					$DbgMsg =_('The SQL statement used to insert the purchase order detail record and failed was');
					$result =DB_query($sql,$db,$ErrMsg,$DbgMsg);
				}
		     } /* end of the loop round the detail line items on the order */
		     echo '<p>';
		     prnMsg(_('Purchase order') . ' ' . $_SESSION['PO']->OrderNo . ' ' . _('on') . ' ' . $_SESSION['PO']->SupplierName . ' ' . _('has been created'),'success');
		     echo '<br><a target="_blank" href="'.$rootpath.'/PO_PDFPurchOrder.php?' . SID . '&OrderNo=' . $_SESSION['PO']->OrderNo . '">' . _('Print Purchase Order') . '</a>';
		 } else { /*its an existing order need to update the old order info */

		     /*Update the purchase order header with any changes */
			$sql = 'UPDATE purchorders SET
		     			supplierno = '."'" . $_SESSION['PO']->SupplierID . "'".',
					comments='."'" . $_SESSION['PO']->Comments . "'".',
					rate=' . $_SESSION['PO']->ExRate . ',
					initiator='."'" . $_SESSION['PO']->Initiator . "'".',
					requisitionno= '."'" . $_SESSION['PO']->RequisitionNo . "'".',
					intostocklocation='."'" . $_SESSION['PO']->Location . "'".',
					deladd1='."'" . $_SESSION['PO']->DelAdd1 . "'".',
					deladd2='."'" . $_SESSION['PO']->DelAdd2 . "'".',
					deladd3='."'" . $_SESSION['PO']->DelAdd3 . "'".',
					deladd4='."'" . $_SESSION['PO']->DelAdd4 . "'".',
					deladd5='."'" . $_SESSION['PO']->DelAdd5 . "'".',
					deladd6='."'" . $_SESSION['PO']->DelAdd6 . "'".',
					allowprint=' . $_SESSION['PO']->AllowPrintPO . '
		     		WHERE orderno = ' . $_SESSION['PO']->OrderNo;

			$ErrMsg =  _('The purchase order could not be updated because');
			$DbgMsg = _('The SQL statement used to update the purchase order header record, that failed was');
			$result =DB_query($sql,$db,$ErrMsg,$DbgMsg,true);

		     /*Now Update the purchase order detail records */
		     foreach ($_SESSION['PO']->LineItems as $POLine) {

				if ($POLine->Deleted==true) {
					if ($POLine->PODetailRec!='') {
						$sql='DELETE FROM purchorderdetails WHERE podetailitem="' . $POLine->PODetailRec . '"';
						$ErrMsg =  _('The purchase order could not be deleted because');
						$DbgMsg = _('The SQL statement used to delete the purchase order header record, that failed was');
						$result = DB_query($sql,$db,$ErrMsg,$DbgMsg);
					}
				} else if ($POLine->PODetailRec=='') {

					$sql = 'INSERT INTO purchorderdetails (
									orderno,
									itemcode,
									deliverydate,
									itemdescription,
									glcode,
									unitprice,
									quantityord,
									shiptref,
									jobref
									)
								VALUES ('
									. $_SESSION['PO']->OrderNo . ',
									'."'" . $POLine->StockID . "'".',
									'."'" . FormatDateForSQL($POLine->ReqDelDate) . "'".',
									'."'" . $POLine->ItemDescription . "'".',
									' . $POLine->GLCode . ',
									' . $POLine->Price . ',
									' . $POLine->Quantity . ',
									'."'" . $POLine->ShiptRef . "'".',
									'."'" . $POLine->JobRef . "'".'
								)';
				} else {
					if ($POLine->Quantity==$POLine->QtyReceived){
						$sql = 'UPDATE purchorderdetails SET
								itemcode='."'" . $POLine->StockID . "'".',
								deliverydate ='."'" . FormatDateForSQL($POLine->ReqDelDate) . "'".',
								itemdescription='."'" . $POLine->ItemDescription . "'".',
								glcode=' . $POLine->GLCode . ',
								unitprice=' . $POLine->Price . ',
								quantityord=' . $POLine->Quantity . ',
								shiptref='."'" . $POLine->ShiptRef . "'".',
								jobref='."'" . $POLine->JobRef . "'".',
								completed=1
							WHERE podetailitem=' . $POLine->PODetailRec;
					} else {
						$sql = 'UPDATE purchorderdetails SET
								itemcode='."'" . $POLine->StockID . "'".',
								deliverydate ='."'" . FormatDateForSQL($POLine->ReqDelDate) . "'".',
								itemdescription='."'" . $POLine->ItemDescription . "'".',
								glcode=' . $POLine->GLCode . ',
								unitprice=' . $POLine->Price . ',
								quantityord=' . $POLine->Quantity . ',
								shiptref='."'" . $POLine->ShiptRef . "'".',
								jobref='."'" . $POLine->JobRef . "'".'
								WHERE podetailitem=' . $POLine->PODetailRec;
					}
				}

				$ErrMsg = _('One of the purchase order detail records could not be updated because');
				$DbgMsg = _('The SQL statement used to update the purchase order detail record that failed was');
				$result =DB_query($sql,$db,$ErrMsg,$DbgMsg,true);
		     } /* end of the loop round the detail line items on the order */
		     echo '<br><br>';
		     prnMsg(_('Purchase order') . ' ' . $_SESSION['PO']->OrderNo . ' ' . _('has been updated'),'success');
		     if ($_SESSION['PO']->AllowPrintPO==1){
			     echo '<br><a target="_blank" href="'.$rootpath.'/PO_PDFPurchOrder.php?' . SID . '&OrderNo=' . $_SESSION['PO']->OrderNo . '">' . _('Re-Print Order') . '</a>';
		     }
		 } /*end of if its a new order or an existing one */

		 $sql = 'COMMIT';
		 $Result = DB_query($sql,$db);

		 $ThisOrderNo = $_SESSION['PO']->OrderNo;
		 unset($_SESSION['PO']); /*Clear the PO data to allow a newy to be input*/
		 //echo '<br><br><a href="'.$rootpath.'/PO_PDFPurchOrder.php?' . SID . '&OrderNo='. $ThisOrderNo.'">' . _('Print this Purchase Order') . '</a>';
		 //echo '<br><br><a href="'.$rootpath.'/PO_Header.php?' . SID . '&NewOrder=Yes">' . _('Enter A New Purchase Order') . '</a>';
		 //echo '<br><a href="'.$rootpath.'/PO_SelectOSPurchOrder.php?' . SID . '">' . _('Select An Outstanding Purchase Order') . '</a>';
		 exit;
	} /*end if there were no input errors trapped */
} /* end of the code to do transfer the PO object to the database  - user hit the place PO*/


/* Always do the stuff below if not looking for a supplierid */
if(isset($_POST['Delete'])){	
	if($_SESSION['PO']->Some_Already_Received($_POST['LineNo'])==0){
		$_SESSION['PO']->remove_from_order($_POST['LineNo']);
		include ('includes/PO_UnsetFormVbls.php');
	} else {
		prnMsg( _('This item cannot be deleted because some of it has already been received'),'warn');
	}
}

if (isset($_POST['LookupPrice']) and $_POST['StockID']!=''){
	$sql = 'SELECT purchdata.price,
			purchdata.conversionfactor,
			purchdata.supplierdescription
		FROM purchdata
		WHERE  purchdata.supplierno = '."'" . $_SESSION['PO']->SupplierID .  "'".'
		and purchdata.stockid = '."'" . strtoupper($_POST['StockID']) . "'";

	$ErrMsg = _('The supplier pricing details for') . ' ' . strtoupper($_POST['StockID']) . ' ' . _('could not be retrieved because');
	$DbgMsg = _('The SQL used to retrieve the pricing details but failed was');
	$LookupResult = DB_query($sql,$db,$ErrMsg,$DbgMsg);

	if (DB_num_rows($LookupResult)==1){
		$myrow = DB_fetch_array($LookupResult);
		$_POST['Price'] = $myrow['price']/$myrow['conversionfactor'];
	} else {
		prnMsg(_('Sorry') . ' ... ' . _('there is no purchasing data set up for this supplier') . '  - ' . $_SESSION['PO']->SupplierID . ' ' . _('and item') . ' ' . strtoupper($_POST['StockID']),'warn');
	}
}

if(isset($_POST['UpdateLine'])){
	$AllowUpdate=true; /*Start assuming the best ... now look for the worst*/	

	if ($_POST['Qty']==0 or $_POST['Price'] < 0){
		$AllowUpdate = false;
		prnMsg( _('The Update Could Not Be Processed') . '<br>' . _('You are attempting to set the quantity ordered to zero, or the price is set to an amount less than 0'),'error');
	}

	if ($_SESSION['PO']->LineItems[$_POST['LineNo']]->QtyInv > $_POST['Qty'] or $_SESSION['PO']->LineItems[$_POST['LineNo']]->QtyReceived > $_POST['Qty']){
		$AllowUpdate = false;
		prnMsg( _('The Update Could Not Be Processed') . '<br>' . _('You are attempting to make the quantity ordered a quantity less than has already been invoiced or received this is of course prohibited') . '. ' . _('The quantity received can only be modified by entering a negative receipt and the quantity invoiced can only be reduced by entering a credit note against this item'),'error');
	}

	if ($_SESSION['PO']->GLLink==1) {
	/*Check for existance of GL Code selected */
		$sql = 'SELECT accountname
				FROM chartmaster
				WHERE accountcode =' .  $_POST['GLCode'];
		$ErrMsg = _('The account name for') . ' ' . $_POST['GLCode'] . ' ' . _('could not be retrieved because');
		$DbgMsg = _('The SQL used to retrieve the account details but failed was');
		$GLActResult = DB_query($sql,$db,$ErrMsg,$DbgMsg);
		if (DB_error_no($db)!=0 or DB_num_rows($GLActResult)==0){
			 $AllowUpdate = false;
			 prnMsg( _('The Update Could Not Be Processed') . '<br>' . _('The GL account code selected does not exist in the database see the listing of GL Account Codes to ensure a valid account is selected'),'error');
		} else {
			$GLActRow = DB_fetch_row($GLActResult);
			$GLAccountName = $GLActRow[0];
		}
	}

	//include ('PO_Chk_ShiptRef_JobRef.php');
	if (!isset($_POST['JobRef'])) {
		$_POST['JobRef']='';
	}
	
	//if (!isset($_POST['ShiptRef'])) {
		$_POST['ShiptRef']='';
	//}
	
	//echo "Updated line no is:".$_POST['LineNo'].'+'.$_SESSION['PO']->LineItems[1]->StockID;
	//echo "</br>Total LineOnOrder is:".$_SESSION['PO']->LinesOnOrder;
	//echo '<br>'. $_POST['ItemDescription'];

	if ($AllowUpdate == true) {

	      $_SESSION['PO']->update_order_item(
	      				$_POST['LineNo'],
					$_POST['Qty'],
					$_POST['Price'],
					$_POST['ItemDescription'],
					$_POST['GLCode'],
					$GLAccountName,
					$_POST['ReqDelDate'],
					$_POST['ShiptRef'],
					$_POST['JobRef'] );

	      include ('includes/PO_UnsetFormVbls.php');

	}
}


if (isset($_POST['NewItemSelected'])){ /* NewItem is set from the part selection list as the part code selected */
/* take the form entries and enter the data from the form into the PurchOrder class variable */
	$AlreadyOnThisOrder = 0;
	$selectedItem = $_POST['NewItemSelected'];
	
	if ($_SESSION['PO_AllowSameItemMultipleTimes'] ==false){
		if (count($_SESSION['PO']->LineItems)!=0){

			foreach ($_SESSION['PO']->LineItems AS $OrderItem) {

			/* do a loop round the items on the order to see that the item
			is not already on this order */
				if (($OrderItem->StockID == $selectedItem)  and ($OrderItem->Deleted==false)) {
					$AlreadyOnThisOrder = 1;
					prnMsg( _('The item') . ' ' . $selectedItem . ' ' . _('is already on this order') . '. ' . _('The system will not allow the same item on the order more than once') . '. ' . _('However you can change the quantity ordered of the existing line if necessary'),'error');
			    }
			}
		}	
	}
	
	if ($AlreadyOnThisOrder!=1){

	    $sql = 'SELECT stockmaster.description,
	    			stockmaster.stockid,
				stockmaster.units,
				stockmaster.decimalplaces,
				stockcategory.stockact,
				chartmaster.accountname,
				purchdata.price,
				purchdata.conversionfactor,
				purchdata.supplierdescription
			FROM stockcategory,
				chartmaster,
				stockmaster LEFT JOIN purchdata
				ON stockmaster.stockid = purchdata.stockid
				and purchdata.supplierno = '."'" . $_SESSION['PO']->SupplierID . "'".'
			WHERE chartmaster.accountcode = stockcategory.stockact
			and stockcategory.categoryid = stockmaster.categoryid
			and stockmaster.stockid = '."'". $selectedItem . "'";

	    $ErrMsg = _('The supplier pricing details for') . ' ' . $selectedItem . ' ' . _('could not be retrieved because');
	    $DbgMsg = _('The SQL used to retrieve the pricing details but failed was');
	    $result1 = DB_query($sql,$db,$ErrMsg,$DbgMsg);
		
		//if ($_SESSION['PO']->GLLink !=1){
		//	$myrow['stockact'] = 0;
		//}
		$myrow['stockact'] = 1;

	   if ($myrow = DB_fetch_array($result1)){
		      if (is_numeric($myrow['price'])){

			     $_SESSION['PO']->add_to_order ($_SESSION['PO']->LinesOnOrder+1,
			     			$selectedItem,
							0, /*Serialised */
							0, /*Controlled */
							1, /* Qty */
							$myrow['description'],
							$myrow['price'],
							$myrow['units'],
							$myrow['stockact'],
							Date($_SESSION['DefaultDateFormat']),
							0,
							0,
							0,
							0,
							$myrow['accountname'],
							$myrow['decimalplaces']);
		      } else { /*There was no supplier purchasing data for the item selected so enter a purchase order line with zero price */

			     $_SESSION['PO']->add_to_order ($_SESSION['PO']->LinesOnOrder+1,
			     			$selectedItem,
							0, /*Serialised */
							0, /*Controlled */
							1, /* Qty */
							$myrow['description'],
							0,
							$myrow['units'],
							$myrow['stockact'],
							Date($_SESSION['DefaultDateFormat']),
							0,
							0,
							0,
							0,
							$myrow['accountname'],
							$myrow['decimalplaces']);
		      }
		      /*Make sure the line is also available for editing by default without additional clicks */
		      $_POST['Edit'] = $_SESSION['PO']->LinesOnOrder; /* this is a bit confusing but it was incremented by the add_to_order function */
	   } else {
		      prnMsg (_('The item code') . ' ' . $selectedItem . ' ' . _('does not exist in the database and therefore cannot be added to the order'),'error');
		      if ($debug==1){
		      		echo "<br>$sql";
		      }
		      include('includes/footer.inc');
		      exit;
	   }

	} /* end of if not already on the order */

} /* end of if its a new item */

// Update the deliver address
If (isset($_POST['UpdateDeliverAddress'])) {
/* User hit the button to enter line items -
 * ensure session variables updated then meta refresh to PO_Items.php */

	$_SESSION['PO']->Location = $_POST['StkLocation'];
	$_SESSION['PO']->DelAdd1 = $_POST['DelAdd1'];
	$_SESSION['PO']->DelAdd2 = $_POST['DelAdd2'];
	$_SESSION['PO']->DelAdd3 = $_POST['DelAdd3'];
	$_SESSION['PO']->DelAdd4 = $_POST['DelAdd4'];
	$_SESSION['PO']->DelAdd5 = $_POST['DelAdd5'];
	$_SESSION['PO']->DelAdd6 = $_POST['DelAdd6'];
	$_SESSION['PO']->Initiator = $_POST['Initiator'];
	$_SESSION['PO']->RequisitionNo = $_POST['Requisition'];
	$_SESSION['PO']->ExRate = $_POST['ExRate'];
	$_SESSION['PO']->Comments = $_POST['Comments'];

	if (isset($_POST['RePrint']) and $_POST['RePrint'] == 1) {

		$_SESSION['PO']->AllowPrintPO = 1;

		$sql = 'UPDATE purchorders
			SET purchorders.allowprint=1
			WHERE purchorders.orderno=' . $_SESSION['PO']->OrderNo;

		$ErrMsg = _('An error occurred updating the purchase order to allow reprints') . '. ' . _('The error says');
		$updateResult = DB_query($sql,$db,$ErrMsg);

	}

	echo "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=" . $rootpath . '/PO_Items.php?' . SID . "'>";
	echo '<P>';
	prnMsg(_('You should automatically be forwarded to the entry of the purchase order line items page') . '. ' . _('If this does not happen') . ' (' . _('if the browser does not support META Refresh') . ') ' . "<a href='$rootpath/PO_Items.php?" . SID . "'>" . _('click here') . '</a> ' . _('to continue'),'info');

} /* end of if isset _POST'UpdateDeliverAddress' */

/* 
* This is where the order as selected should be displayed  reflecting any deletions or insertions  
* Display the purchase order header
*/
echo '<p class=page_title_text> Purchase Order</p>';
echo '<hr>';

echo '<form action="' . $_SERVER['PHP_SELF'] . '?' . SID . '" method=post>';

if (!isset($defaultStkLoc) OR $defaultStkLoc == '') {
	$defaultStkLoc = $_SESSION['UserStockLocation'];
	$sql = "SELECT deladd1,
			deladd2,
			deladd3,
			deladd4,
			deladd5,
			deladd6,
			tel
		FROM locations
		WHERE loccode='" . $defaultStkLoc . "'";
		
	$LocnAddrResult = DB_query($sql,$db);
	
	if (DB_num_rows($LocnAddrResult) == 1) {
		$LocnRow = DB_fetch_row($LocnAddrResult);
		$_SESSION['PO']->Location= $defaultStkLoc;
		$_SESSION['PO']->DelAdd1 = $LocnRow[0];
		$_SESSION['PO']->DelAdd2 = $LocnRow[1];
		$_SESSION['PO']->DelAdd3 = $LocnRow[2];
		$_SESSION['PO']->DelAdd4 = $LocnRow[3];
		$_SESSION['PO']->DelAdd5 = $LocnRow[4];
		$_SESSION['PO']->DelAdd6 = $LocnRow[5];
		$_SESSION['PO']->tel = $LocnRow[6];

	} else {
		// The default location of the user is crook //
		prnMsg(_('The default stock location set up for this user is not a currently defined stock location') . '. ' . _('Your system administrator needs to amend your user record'),'error');
	}
}

$dispCompanyName=stripslashes($_SESSION['CompanyRecord']['coyname']);
//$dispCompanyAddr1=
//$dispCompanyAddr2=
//$dispContactName=
//$dispContactTel=
//$dispContactEmail=

$dispPONo = $_SESSION['PO']->OrderNo;
$dispLocation = $_SESSION['PO']->Location;
$dispDelAddrFirst = $_SESSION['PO']->DelAdd1.' '.$_SESSION['PO']->DelAdd2.' '.$_SESSION['PO']->DelAdd3;
$dispDelAddrLast  = $_SESSION['PO']->DelAdd4.' '.$_SESSION['PO']->DelAdd5.' '.$_SESSION['PO']->DelAdd6;
$dispContact = $_SESSION['PO']->Initiator;
$dispRequistion = $_SESSION['PO']->RequisitionNo;
$dispExRate = $_SESSION['PO']->ExRate;
$dispComments = $_SESSION['PO']->Comments;

$dispCurr = $_SESSION['PO']->CurrCode;

if( $_SESSION['PO']->Orig_OrderDate==''){
	$_SESSION['PO']->Orig_OrderDate = Date($_SESSION['DefaultDateFormat'],Mktime(0,0,0,Date('m'),Date('d')+1,Date('y')));
}
$dispOrderDate = $_SESSION['PO']->Orig_OrderDate;

$dispSupplierID = $_SESSION['PO']->SupplierID;	
$dispSupplierAddr1_3 = $_SESSION['PO']->DelAdd1.' '.$_SESSION['PO']->DelAdd2.' '.$_SESSION['PO']->DelAdd3;
$dispSupplierAddr4_6 = $_SESSION['PO']->DelAdd4.' '.$_SESSION['PO']->DelAdd5.' '.$_SESSION['PO']->DelAdd6;

if(isset($_SESSION['ExistingOrder']) and $_SESSION['ExistingOrder']!=''){
	$dispSupplierName = $_SESSION['PO']->SupplierName.'('.$_SESSION['PO']->SupplierID.')';
}else{
	$dispSupplierName = $_SESSION['PO']->SupplierName.'('.$_SESSION['PO']->SupplierID.')'.'<input type="button" value="Supplier" onclick=openSubpage("h5SupplierSearch.php") />';
}

echo '<table class="commontable" > 
<caption> Wafer PO header</caption>
<tr><td>PO NO.</td><td>WO'.$dispPONo.'</td><td>Order Date</td><td>'.$dispOrderDate.'</td></tr>
<tr><td>Buyer:</td><td>'. $dispCompanyName .'</td><td>Supplier*:</td><td>'.$dispSupplierName.'</td></tr>
<tr><td>Address1-3:</td><td>'. $dispDelAddrFirst .'</td><td>Address1-3:<td>'.$dispSupplierAddr1_3.'</td></tr>
<tr><td>Address4-6:</td><td>'. $dispDelAddrLast.'</td><td>Address4-6:<td>'.$dispSupplierAddr4_6.'</td></tr>
<tr><td>Contact:</td><td>'.$dispContact.'</td><td>Contact:<td></td></tr>
<tr><td>Telephone:</td><td>'. $dispContact .'</td><td>Telephone:<td></td></tr>
<tr><td>Email:</td><td></td><td>Email:<td></td></tr>
</table>';



if ($_SESSION['RequireSupplierSelection'] == 1 OR !isset($_SESSION['PO']->SupplierID) OR $_SESSION['PO']->SupplierID == '' ) {

//end if RequireSupplierSelection
} else{  //display order summary when the Supplier selected!
	echo '<table class="commontable"> <caption> Order Summary</caption>';
	/*need to set up entry for item description where not a stock item and GL Codes */
	   echo '<tr>
			<th>' . _('Item Code') . '</th>
			<th>' . _('Item Description') . '</th>
			<th>' . _('Quantity') . '</th>
			<th>' . _('Unit') . '</th>
			<th>' . _('Delivery') . '</th>
			<th>' . _('Price') . '</th>
			<th>' . _('Total') . '</th>
			<th> </th>
		</tr>';

	   $_SESSION['PO']->total = 0;
	   $k = 0;  //row colour counter
	   //$j = 0;
	if (count($_SESSION['PO']->LineItems)>0){	   
	
	   foreach ($_SESSION['PO']->LineItems as $POLine) {

			if ($POLine->Deleted==false) {
				$LineTotal =	$POLine->Quantity * $POLine->Price;
				$DisplayLineTotal = number_format($LineTotal,2);
				$DisplayPrice = number_format($POLine->Price,2);
				$DisplayQuantity = number_format($POLine->Quantity,$POLine->DecimalPlaces);
				
				if (isset($_POST['Edit']) and ($_POST['Edit']) == $POLine->LineNo){		
					
					if (!isset($_POST['ShiptRef'])) {
						$_POST['ShiptRef']='';
					}
					echo '<input type="hidden" name="LineNo" value=' . ($POLine->LineNo) .'>';		
					echo '<input type="hidden" name="GLCode" value=' . ($POLine->GLCode) .'>';
					//echo '<input type="hidden" name="ItemDescription" value='."$POLine->ItemDescription".' >';
					$_SESSION['TempItemDes'] = $POLine->ItemDescription;					
					//echo 'Des:'.$POLine->ItemDescription;
					
					echo '<tr>
					<td>' . $_SESSION['PO']->LineItems[$_POST['Edit']]->StockID . '</td>
					<td>' . $_SESSION['PO']->LineItems[$_POST['Edit']]->ItemDescription . '</td>
					<td><input class="price" type="text" name="Qty" value=' . $_SESSION['PO']->LineItems[$_POST['Edit']]->Quantity . '></td>
					<td>' . $_SESSION['PO']->LineItems[$_POST['Edit']]->Units . '</td>
					<td><input class="price" type="text" name="ReqDelDate" maxlength=2 value=' . $_SESSION['PO']->LineItems[$_POST['Edit']]->ReqDelDate . '></td>
					<td><input type="text" name="Price" value=' . $_SESSION['PO']->LineItems[$_POST['Edit']]->Price . '>'.$dispCurr.' <input type=image name="LookupPrice" value="lookp" src="search.png"></td>	
					<td></td>
					<td class="menu"><img src="editgray.jpg" alt="edit"> <input type=image name="UpdateLine" value=".$POLine->LineNo." src="save.png"> <input type=image name="Delete" value="del" src="delete.jpg">  </td>
					</tr>';			
				}else{
					echo "<td>$POLine->StockID</td><td>$POLine->ItemDescription</td><TD ALIGN=RIGHT>$DisplayQuantity</td><td>$POLine->Units</td><td>$POLine->ReqDelDate</td><TD ALIGN=RIGHT>$DisplayPrice $dispCurr</td><TD ALIGN=RIGHT>$DisplayLineTotal</FONT></td>";
					echo '<td><input type=image name="Edit" value="'.$POLine->LineNo.'" src="edit.jpg">';
					echo "<img src='savegray.png' alt='save'>
					<img src='deletegray.jpg' alt='delete'>
					</td></tr>";
				}				
				$_SESSION['PO']->total = $_SESSION['PO']->total + $LineTotal;
			}
		}
	}
	
	if (isset($_POST['AddLine'])){ 
	/*Add a new line form for putting in a new line item with default value*/
		$_POST['StockID']='';		
		$_POST['ItemDescription']='';
		$_POST['GLCode']='';			
		$_POST['Qty'] = 1;
		$_POST['Price'] = 0;
		$_POST['ReqDelDate'] = Date($_SESSION['DefaultDateFormat'],Mktime(0,0,0,Date('m'),Date('d')+1,Date('y')));			
		$_POST['ShiptRef']='';
		 
		//Add form in a new line		
		echo '<tr><td><input type="text" class="big" name="fake" value="Search Items"/><input onclick=openSubpage("h5ItemsSearch.php") type="image" src="search.png" value="Select products" /></td>';
		echo '<td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>';

	}
	
	$DisplayTotal = number_format($_SESSION['PO']->total,2);
	echo '<tr><td colspan=6 align=right>' . _('TOTAL Excl Tax') . '</td><td align=right><b>' . $DisplayTotal . '</b></td>
	<td><input type=submit name="AddLine" value="' . _('Add Line') . '"></td> 
	</tr></table>';


	echo '<table class="commontable">
		<caption> Delivery address </caption>
		<tr><td> Deliver to :</td><td>'.$dispLocation.'</td></tr>
		<tr><td> Address :</td><td>'.$dispDelAddrFirst.$dispDelAddrLast.'</td></tr>
		<tr><td> Remarks :</td><td><input type="text" name="Remarks" value='.$_POST['Remarks'].'></td></tr>
		<tr><td></td><td><input type=image name="Edit" value="editDeliveryAddr" src="edit.jpg"> </td></tr></table>';
		
	echo '<p align="right"><input type=submit name="CancelOrder" value="' . _('Cancel and Delete The Whole Order') . '">
			<input type=submit name="Commit" value="' . _('Confirm Delivery and Place Order') . '">
			<a href="'.$rootpath.'/PO_PDFPurchOrder.php?' . SID . '&OrderNo='. $_SESSION['PO']->OrderNo.'"><img src="'.$rootpath.'/css/'.$theme.'/images/printer.png" alt=Print /></a>
			</p>';		
}	

echo '</form>';
include('includes/footer.inc');
?>
