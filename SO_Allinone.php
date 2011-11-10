<?php
/* $Revision: 1.91 $ */

include('includes/DefineCartClass.php');
$PageSecurity = 1;
/* Session started in session.inc for password checking and authorisation level check
config.php is in turn included in session.inc*/

include('includes/session.inc');

if (isset($_GET['ModifyOrderNumber'])) {
	$title = _('Modifying Order') . ' ' . $_GET['ModifyOrderNumber'];
} else {
	$title = _('Select Order Items');
}

include('includes/header.inc');
include('includes/GetPrice.inc');
include('includes/SQL_CommonFunctions.inc');

//Set $_SESSION['RequireOrderInitalize']=false, when NewOrder or change supplier
if (isset($_GET['NewOrder']) or $_SESSION['RequireOrderInitalize'] != false ){
  /*New order entry - clear any existing order details from the Items object and initiate a newy*/
	 if (isset($_SESSION['Items'])){
		unset ($_SESSION['Items']->LineItems);
		$_SESSION['Items']->ItemsOrdered=0;
		unset ($_SESSION['Items']);
	}

	$_SESSION['ExistingOrder']=0;
	$_SESSION['Items'] = new cart;

	if (count($_SESSION['AllowedPageSecurityTokens'])==1){ //its a customer logon
		$_SESSION['Items']->DebtorNo=$_SESSION['CustomerID'];
		$_SESSION['RequireCustomerSelection']=0;
	} else {
		$_SESSION['Items']->DebtorNo='';
	}
	$_SESSION['RequireOrderInitalize']= false;	
}

echo '<form action="' . $_SERVER['PHP_SELF'] . '?' . SID . '" method=post>';
/*
* always display a customer order header info 
*/
echo '<p class=page_title_text> Customer Order</p>';
echo '<hr>';

// message dispatch & clear message
$NewItem = $_SESSION['msg_SelectedItem'];
if( isset( $_SESSION['msg_SelectedItem']) and $_SESSION['msg_SelectedItem'] != ''){
	unset($_SESSION['msg_SelectedItem']);
}


if( isset( $_SESSION['msg_SelectedCustomer']) and $_SESSION['msg_SelectedCustomer'] != ''){
	$CustomerInfo = $_SESSION['msg_SelectedCustomer'];

	$_POST['NewCustomer'] = substr($CustomerInfo,0,strpos($CustomerInfo,' - '));	
	$_SESSION['Items']->Branch = substr($CustomerInfo, strpos($CustomerInfo,' - ')+3);
		
	unset($_SESSION['msg_SelectedCustomer']);
}

/* The delivery check screen is where the details of the order are either updated or inserted depending on the value of ExistingOrder */
if (isset($_GET['ModifyOrderNumber']) AND $_GET['ModifyOrderNumber']!=''){

	if (isset($_SESSION['Items'])){
		unset ($_SESSION['Items']->LineItems);
		unset ($_SESSION['Items']);
	}
	$_SESSION['ExistingOrder']=$_GET['ModifyOrderNumber'];
	$_SESSION['RequireCustomerSelection'] = 0;
	$_SESSION['Items'] = new cart;

/*read in all the guff from the selected order into the Items cart  */

	$OrderHeaderSQL = 'SELECT salesorders.debtorno,
								debtorsmaster.name,
								salesorders.branchcode,
								salesorders.customerref,
								salesorders.comments,
								salesorders.orddate,
								salesorders.ordertype,
								salestypes.sales_type,
								salesorders.shipvia,
								salesorders.deliverto,
								salesorders.deladd1,
								salesorders.deladd2,
								salesorders.deladd3,
								salesorders.deladd4,
								salesorders.deladd5,
								salesorders.deladd6,
								salesorders.contactphone,
								salesorders.contactemail,
								salesorders.freightcost,
								salesorders.deliverydate,
								debtorsmaster.currcode,
								paymentterms.terms,
								salesorders.fromstkloc,
								salesorders.printedpackingslip,
								salesorders.datepackingslipprinted,
								salesorders.quotation,
								salesorders.deliverblind,
								debtorsmaster.customerpoline,
								locations.locationname,
								custbranch.estdeliverydays
							FROM salesorders,
								debtorsmaster,
								salestypes,
								custbranch,
								paymentterms,
								locations
							WHERE salesorders.ordertype=salestypes.typeabbrev
							AND salesorders.debtorno = debtorsmaster.debtorno
							AND salesorders.debtorno = custbranch.debtorno
							AND salesorders.branchcode = custbranch.branchcode
							AND debtorsmaster.paymentterms=paymentterms.termsindicator
							AND locations.loccode=salesorders.fromstkloc
							AND salesorders.orderno = ' . $_GET['ModifyOrderNumber'];


	$ErrMsg =  _('The order cannot be retrieved because');
	$GetOrdHdrResult = DB_query($OrderHeaderSQL,$db,$ErrMsg);

	if (DB_num_rows($GetOrdHdrResult)==1) {

		$myrow = DB_fetch_array($GetOrdHdrResult);
		$_SESSION['Items']->OrderNo = $_GET['ModifyOrderNumber'];
		$_SESSION['Items']->DebtorNo = $myrow['debtorno'];
/*CustomerID defined in header.inc */
		$_SESSION['Items']->Branch = $myrow['branchcode'];
		$_SESSION['Items']->CustomerName = $myrow['name'];
		$_SESSION['Items']->CustRef = $myrow['customerref'];
		$_SESSION['Items']->Comments = stripcslashes($myrow['comments']);
		$_SESSION['Items']->PaymentTerms =$myrow['terms'];
		$_SESSION['Items']->DefaultSalesType =$myrow['ordertype'];
		$_SESSION['Items']->SalesTypeName =$myrow['sales_type'];
		$_SESSION['Items']->DefaultCurrency = $myrow['currcode'];
		$_SESSION['Items']->ShipVia = $myrow['shipvia'];
		$BestShipper = $myrow['shipvia'];
		$_SESSION['Items']->DeliverTo = $myrow['deliverto'];
		$_SESSION['Items']->DeliveryDate = ConvertSQLDate($myrow['deliverydate']);
		$_SESSION['Items']->DelAdd1 = $myrow['deladd1'];
		$_SESSION['Items']->DelAdd2 = $myrow['deladd2'];
		$_SESSION['Items']->DelAdd3 = $myrow['deladd3'];
		$_SESSION['Items']->DelAdd4 = $myrow['deladd4'];
		$_SESSION['Items']->DelAdd5 = $myrow['deladd5'];
		$_SESSION['Items']->DelAdd6 = $myrow['deladd6'];
		$_SESSION['Items']->PhoneNo = $myrow['contactphone'];
		$_SESSION['Items']->Email = $myrow['contactemail'];
		$_SESSION['Items']->Location = $myrow['fromstkloc'];
		$_SESSION['Items']->LocationName = $myrow['locationname'];
		$_SESSION['Items']->Quotation = $myrow['quotation'];
		$_SESSION['Items']->FreightCost = $myrow['freightcost'];
		$_SESSION['Items']->Orig_OrderDate = $myrow['orddate'];
		$_SESSION['PrintedPackingSlip'] = $myrow['printedpackingslip'];
		$_SESSION['DatePackingSlipPrinted'] = $myrow['datepackingslipprinted'];
		$_SESSION['Items']->DeliverBlind = $myrow['deliverblind'];
		$_SESSION['Items']->DefaultPOLine = $myrow['customerpoline'];
		$_SESSION['Items']->DeliveryDays = $myrow['estdeliverydays'];

/*need to look up customer name from debtors master then populate the line items array with the sales order details records */

			$LineItemsSQL = "SELECT salesorderdetails.orderlineno,
									salesorderdetails.stkcode,
									stockmaster.description,
									stockmaster.volume,
									stockmaster.kgs,
									stockmaster.units,
									salesorderdetails.unitprice,
									salesorderdetails.quantity,
									salesorderdetails.discountpercent,
									salesorderdetails.actualdispatchdate,
									salesorderdetails.qtyinvoiced,
									salesorderdetails.narrative,
									salesorderdetails.itemdue,
									salesorderdetails.poline,
									locstock.quantity as qohatloc,
									stockmaster.mbflag,
									stockmaster.discountcategory,
									stockmaster.decimalplaces,
									stockmaster.materialcost+stockmaster.labourcost+stockmaster.overheadcost AS standardcost,
									salesorderdetails.completed
									FROM salesorderdetails INNER JOIN stockmaster
									ON salesorderdetails.stkcode = stockmaster.stockid
									INNER JOIN locstock ON locstock.stockid = stockmaster.stockid
									WHERE  locstock.loccode = '" . $myrow['fromstkloc'] . "'
									AND salesorderdetails.orderno =" . $_GET['ModifyOrderNumber'] . "
									ORDER BY salesorderdetails.orderlineno";

		$ErrMsg = _('The line items of the order cannot be retrieved because');
		$LineItemsResult = db_query($LineItemsSQL,$db,$ErrMsg);
		if (db_num_rows($LineItemsResult)>0) {

			while ($myrow=db_fetch_array($LineItemsResult)) {
					if ($myrow['completed']==0){
						$_SESSION['Items']->add_to_cart($myrow['stkcode'],
														$myrow['quantity'],
														$myrow['description'],
														$myrow['unitprice'],
														$myrow['discountpercent'],
														$myrow['units'],
														$myrow['volume'],
														$myrow['kgs'],
														$myrow['qohatloc'],
														$myrow['mbflag'],
														$myrow['actualdispatchdate'],
														$myrow['qtyinvoiced'],
														$myrow['discountcategory'],
														0,	/*Controlled*/
														0,	/*Serialised */
														$myrow['decimalplaces'],
														$myrow['narrative'],
														-1,
														'No', /* Update DB */
														$myrow['orderlineno'],
						//								ConvertSQLDate($myrow['itemdue']),
														0,
														'',
														$myrow['itemdue'],
														$myrow['poline'],
														$myrow['standardcost']
														);
								
				/*Just populating with existing order - no DBUpdates */
					}
					$LastLineNo = $myrow['orderlineno'];
			} /* line items from sales order details */
			 $_SESSION['Items']->LineCounter = $LastLineNo+1;
		} //end of checks on returned data set
	}
}
/*
*  will only be true if page called from customer selection form or set because only one customer
*  record returned from a search so parse the $Select string into customer code and branch code 
*/
if (isset($_POST['NewCustomer']) AND $_POST['NewCustomer']!='') {

	// Now check to ensure this account is not on hold */
	$sql = "SELECT debtorsmaster.name,
					holdreasons.dissallowinvoices,
					debtorsmaster.salestype,
					salestypes.sales_type,
					debtorsmaster.currcode,
					debtorsmaster.customerpoline,
					paymentterms.terms
			FROM debtorsmaster,
				holdreasons,
				salestypes,
				paymentterms
			WHERE debtorsmaster.salestype=salestypes.typeabbrev
			AND debtorsmaster.holdreason=holdreasons.reasoncode
			AND debtorsmaster.paymentterms=paymentterms.termsindicator
			AND debtorsmaster.debtorno = '" . $_POST['NewCustomer'] . "'";

	$ErrMsg = _('The details of the customer selected') . ': ' .  $_POST['NewCustomer'] . ' ' . _('cannot be retrieved because');
	$DbgMsg = _('The SQL used to retrieve the customer details and failed was') . ':';
	$result =DB_query($sql,$db,$ErrMsg,$DbgMsg);

	$myrow = DB_fetch_row($result);
	if ($myrow[1] != 1){
		if ($myrow[1]==2){
			prnMsg(_('The') . ' ' . $myrow[0] . ' ' . _('account is currently flagged as an account that needs to be watched. Please contact the credit control personnel to discuss'),'warn');
		}

		$_SESSION['Items']->DebtorNo=$_POST['NewCustomer'];
		$_SESSION['RequireCustomerSelection']=0;
		$_SESSION['Items']->CustomerName = $myrow[0];

# the sales type determines the price list to be used by default the customer of the user is
# defaulted from the entry of the userid and password.

		$_SESSION['Items']->DefaultSalesType = $myrow[2];
		$_SESSION['Items']->SalesTypeName = $myrow[3];
		$_SESSION['Items']->DefaultCurrency = $myrow[4];
		$_SESSION['Items']->DefaultPOLine = $myrow[5];
		$_SESSION['Items']->PaymentTerms = $myrow[6];

# the branch was also selected from the customer selection so default the delivery details from the customer branches table CustBranch. The order process will ask for branch details later anyway

		$sql = "SELECT custbranch.brname,
				custbranch.braddress1,
				custbranch.braddress2,
				custbranch.braddress3,
				custbranch.braddress4,
				custbranch.braddress5,
				custbranch.braddress6,
				custbranch.phoneno,
				custbranch.email,
				custbranch.defaultlocation,
				custbranch.defaultshipvia,
				custbranch.deliverblind,
                custbranch.specialinstructions,
                custbranch.estdeliverydays,
                locations.locationname
			FROM custbranch
			INNER JOIN locations
			ON custbranch.defaultlocation=locations.loccode
			WHERE custbranch.branchcode='" . $_SESSION['Items']->Branch . "'
			AND custbranch.debtorno = '" . $_POST['NewCustomer'] . "'";

		$ErrMsg = _('The customer branch record of the customer selected') . ': ' . $_POST['NewCustomer'] . ' ' . _('cannot be retrieved because');
		$DbgMsg = _('SQL used to retrieve the branch details was') . ':';
		$result =DB_query($sql,$db,$ErrMsg,$DbgMsg);

		if (DB_num_rows($result)==0){

			prnMsg(_('The branch details for branch code') . ': ' . $_SESSION['Items']->Branch . ' ' . _('against customer code') . ': ' . $_POST['NewCustomer'] . ' ' . _('could not be retrieved') . '. ' . _('Check the set up of the customer and branch'),'error');

			if ($debug==1){
				echo '<BR>' . _('The SQL that failed to get the branch details was') . ':<BR>' . $sql;
			}
			include('includes/footer.inc');
			exit;
		}

		$myrow = DB_fetch_row($result);
		$_SESSION['Items']->DeliverTo = $myrow[0];
		$_SESSION['Items']->DelAdd1 = $myrow[1];
		$_SESSION['Items']->DelAdd2 = $myrow[2];
		$_SESSION['Items']->DelAdd3 = $myrow[3];
		$_SESSION['Items']->DelAdd4 = $myrow[4];
		$_SESSION['Items']->DelAdd5 = $myrow[5];
		$_SESSION['Items']->DelAdd6 = $myrow[6];
		$_SESSION['Items']->PhoneNo = $myrow[7];
		$_SESSION['Items']->Email = $myrow[8];
		$_SESSION['Items']->Location = $myrow[9];
		$_SESSION['Items']->ShipVia = $myrow[10];
		$_SESSION['Items']->DeliverBlind = $myrow[11];
		$_SESSION['Items']->SpecialInstructions = $myrow[12];
		$_SESSION['Items']->DeliveryDays = $myrow[13];
		$_SESSION['Items']->LocationName = $myrow[14];

		if ($_SESSION['Items']->SpecialInstructions)
		  prnMsg($_SESSION['Items']->SpecialInstructions,'warn');

		if ($_SESSION['CheckCreditLimits'] > 0){  /*Check credit limits is 1 for warn and 2 for prohibit sales */
			$_SESSION['Items']->CreditAvailable = GetCreditAvailable($_POST['NewCustomer'],$db);

			if ($_SESSION['CheckCreditLimits']==1 AND $_SESSION['Items']->CreditAvailable <=0){
				prnMsg(_('The') . ' ' . $myrow[0] . ' ' . _('account is currently at or over their credit limit'),'warn');
			} elseif ($_SESSION['CheckCreditLimits']==2 AND $_SESSION['Items']->CreditAvailable <=0){
				prnMsg(_('No more orders can be placed by') . ' ' . $myrow[0] . ' ' . _(' their account is currently at or over their credit limit'),'warn');
				include('includes/footer.inc');
				exit;
			}
		}

	} else {
		prnMsg(_('The') . ' ' . $myrow[0] . ' ' . _('account is currently on hold please contact the credit control personnel to discuss'),'warn');
	}
}


$dispLocation = $_SESSION['Items']->DeliverTo;
$dispDelAddrFirst = $_SESSION['Items']->DelAdd1.' '.$_SESSION['Items']->DelAdd2.' '.$_SESSION['Items']->DelAdd3;
$dispDelAddrLast  = $_SESSION['Items']->DelAdd4.' '.$_SESSION['Items']->DelAdd5.' '.$_SESSION['Items']->DelAdd6;
$dispContact = $_SESSION['Items']->Initiator;
$dispRequistion = $_SESSION['Items']->RequisitionNo;
$dispExRate = $_SESSION['Items']->ExRate;
$dispComments = $_SESSION['Items']->Comments;

if( $_SESSION['ExistingOrder'] and $_SESSION['ExistingOrder']!=''){
	$dispOrderNo = $_SESSION['ExistingOrder'];
	$dispCustomer = $_SESSION['Items']->CustomerName;
}else{
	$dispOrderNo = "";
	$dispCustomer = $_SESSION['Items']->CustomerName."<input onclick=openSubpage(\"h5CustomerSearch.php\")  type='button' value='Customer' />";	
}
$dispCompanyName=stripslashes($_SESSION['CompanyRecord']['coyname']);
//$dispCustomerID = $_SESSION['msg_SelectedCustomer'];
//$dispOrderDate = Date($_SESSION['DefaultDateFormat']);

echo "<table class='commontable' > 
<caption> Customer Order Header</caption>";
echo "<tr><td>PO NO.</td><td>SO".$dispOrderNo."</td><td>Order Date</td><td>".$dispOrderDate."</td></tr>";
echo "<tr><td>Customer*:</td><td>".$dispCustomer."</td><td>Vender:</td><td>".$dispCompanyName."</td></tr>";
echo "<tr><td>Address1-3:</td><td>". $dispDelAddrFirst ."</td><td>Address1-3:<td></td></tr>
<tr><td>Address4-6:</td><td>". $dispDelAddrLast."</td><td>Address4-6:<td></td></tr>
<tr><td>Contact:</td><td>".$dispContact."</td><td>Contact:<td></td></tr>
<tr><td>Telephone:</td><td>". $dispContact ."</td><td>Telephone:<td></td></tr>
<tr><td>Email:</td><td></td><td>Email:<td></td></tr>
</table>";

If ((isset($_SESSION['Items'])) OR isset($NewItem)){// Delete or Inserts edits ,Recaculte with Update

	If(isset($_POST['Delete'])){
		//page called attempting to delete a line - GET['Delete'] = the line number to delete
		if($_SESSION['Items']->Some_Already_Delivered($_POST['Delete'])==0){
			$_SESSION['Items']->remove_from_cart($_POST['Delete'], 'Yes');  /*Do update DB */
		} else {
			prnMsg( _('This item cannot be deleted because some of it has already been invoiced'),'warn');
		}
	}
	
	if(isset($_POST['UpdateLine'])){
		if($_SESSION['Items']->Some_Already_Delivered($_POST['UpdateLine'])==0){
			$UpdateOrderLine = $_SESSION['Items']->LineItems[$_POST['UpdateLine']];
			$Quantity = $_POST['Qty_'];
			
			if ( 0 ){//is_numeric($_POST['GPPercent_']) AND $_POST['GPPercent_']<100 AND $_POST['GPPercent_']>0) {

				if ($_SESSION['Items']->DefaultCurrency != $_SESSION['CompanyRecord']['currencydefault']){
						$ExRateResult = DB_query("SELECT rate FROM currencies WHERE currabrev='" . $_SESSION['Items']->DefaultCurrency . "'",$db);
						if (DB_num_rows($ExRateResult)>0){
							$ExRateRow = DB_fetch_row($ExRateResult);
							$ExRate = $ExRateRow[0];
						} else {
							$ExRate =1;
						}
				} else {
					$ExRate = 1;
				}
				$Price = round(($UpdateOrderLine->StandardCost*$ExRate)/(1 -(($_POST['GPPercent_' . $UpdateOrderLine->LineNumber]+$_POST['Discount_' . $UpdateOrderLine->LineNumber])/100)),3);

			} else {
				$Price = $_POST['Price_'];
				//$Price = 500;
			}
			
			$DiscountPercentage = $_POST['Discount_'];
			if ($_SESSION['AllowOrderLineItemNarrative'] == 1) {
				$Narrative = $_POST['Narrative_' . $UpdateOrderLine->LineNumber];
			} else {
				$Narrative = '';
			}
			$ItemDue = $_POST['ItemDue_'];
			$POLine = $_POST['UpdateLine'];

			if (!isset( $DiscountPercentage )) {
				$DiscountPercentage = 0;
			}

			if(!Is_Date($ItemDue)) {
				prnMsg(_('An invalid date entry was made for ') . ' ' . $NewItem . ' ' . _('The date entry') . ' ' . $ItemDue . ' ' . ('must be in the format') . ' ' . $_SESSION['DefaultDateFormat'],'warn');
				//Attempt to default the due date to something sensible?
				$ItemDue = DateAdd (Date($_SESSION['DefaultDateFormat']),'d', $_SESSION['Items']->DeliveryDays);
			}
			If ($Quantity<0 OR $Price <0 OR $DiscountPercentage >100 OR $DiscountPercentage <0){
				prnMsg(_('The item could not be updated because you are attempting to set the quantity ordered to less than 0 or the price less than 0 or the discount more than 100% or less than 0%'),'warn');

			} elseif($_SESSION['Items']->Some_Already_Delivered($UpdateOrderLine->LineNumber)!=0 AND $_SESSION['Items']->LineItems[$UpdateOrderLine->LineNumber]->Price != $Price) {

				prnMsg(_('The item you attempting to modify the price for has already had some quantity invoiced at the old price the items unit price cannot be modified retrospectively'),'warn');

			} elseif($_SESSION['Items']->Some_Already_Delivered($UpdateOrderLine->LineNumber)!=0 AND $_SESSION['Items']->LineItems[$UpdateOrderLine->LineNumber]->DiscountPercent != ($DiscountPercentage/100)) {

				prnMsg(_('The item you attempting to modify has had some quantity invoiced at the old discount percent the items discount cannot be modified retrospectively'),'warn');

			} elseif ($_SESSION['Items']->LineItems[$UpdateOrderLine->LineNumber]->QtyInv > $Quantity){
				prnMsg( _('You are attempting to make the quantity ordered a quantity less than has already been invoiced') . '. ' . _('The quantity delivered and invoiced cannot be modified retrospectively'),'warn');
			} elseif ($UpdateOrderLine->Quantity !=$Quantity OR $UpdateOrderLine->Price != $Price OR ABS($UpdateOrderLine->DiscountPercent -$DiscountPercentage/100) >0.001 OR $OrderLine->Narrative != $Narrative OR $UpdateOrderLine->ItemDue != $ItemDue OR $UpdateOrderLine->POLine != $POLine) {
				$_SESSION['Items']->update_cart_item($UpdateOrderLine->LineNumber,
									$Quantity,
									$Price,
									($DiscountPercentage/100),
									$Narrative,
									'Yes', /*Update DB */
									$ItemDue, /*added line 8/23/2007 by Morris Kelly to get line item due date*/
									$POLine);
			}		
			
		} else {
			prnMsg( _('This item cannot be updated because some of it has already been invoiced'),'warn');
		}
	}

}
if (isset($_POST['DeliveryDetails'])){
	echo '<META HTTP-EQUIV="Refresh" CONTENT="0; URL=' . $rootpath . '/DeliveryDetails.php?' . SID . '">';
	prnMsg(_('You should automatically be forwarded to the entry of the delivery details page') . '. ' . _('If this does not happen') . ' (' . _('if the browser does not support META Refresh') . ') ' .
	   '<a href="' . $rootpath . '/DeliveryDetails.php?' . SID . '">' . _('click here') . '</a> ' . _('to continue') . 'info');
	exit;
}

/* get the item details from the database and hold them in the cart object make the quantity 1 by default then add it to the cart */
/* unset the $NewItem */
/*Now figure out if the item is a kit set - the field MBFlag='K'*/
If (isset($NewItem)){
	$sql = "SELECT stockmaster.mbflag
			FROM stockmaster
			WHERE stockmaster.stockid='". $NewItem ."'";

	$ErrMsg =  _('Could not determine if the part being ordered was a kitset or not because');

	$KitResult = DB_query($sql, $db,$ErrMsg);

	$NewItemQty = 1; /*By Default */
	$Discount = 0; /*By default - can change later or discount category overide */

	if ($myrow=DB_fetch_array($KitResult)){
		if ($myrow['mbflag']=='K'){	/*It is a kit set item */
			$sql = "SELECT bom.component,
						bom.quantity
					FROM bom
					WHERE bom.parent='" . $NewItem . "'
					AND bom.effectiveto > '" . Date('Y-m-d') . "'
					AND bom.effectiveafter < '" . Date('Y-m-d') . "'";

			$ErrMsg = _('Could not retrieve kitset components from the database because');
			$KitResult = DB_query($sql,$db,$ErrMsg);

			$ParentQty = $NewItemQty;
			while ($KitParts = DB_fetch_array($KitResult,$db)){
				$NewItem = $KitParts['component'];
				$NewItemQty = $KitParts['quantity'] * $ParentQty;
				$NewPOLine = 0;
				$NewItemDue = date($_SESSION['DefaultDateFormat']);
				include('includes/SelectOrderItems_IntoCart.inc');
			}

		} else { /*Its not a kit set item*/
			$NewItemDue = date($_SESSION['DefaultDateFormat']);
			$NewPOLine = 0;
			include('includes/SelectOrderItems_IntoCart.inc');
		}
		// The new item line is editable by default
		$_POST['Edit'] =$_SESSION['Items']->LineCounter -1;	
		
		// clear var $NewItem
		unset($NewItem);
	} 
	
} /*end of if its a new item */

if( 1 ) {
	/* Run through each line of the order and work out the appropriate discount from the discount matrix */
	$DiscCatsDone = array();
	$counter =0;
	foreach ($_SESSION['Items']->LineItems as $OrderLine) {

		if ($OrderLine->DiscCat !="" AND ! in_array($OrderLine->DiscCat,$DiscCatsDone)){
			$DiscCatsDone[$counter]=$OrderLine->DiscCat;
			$QuantityOfDiscCat =0;

			foreach ($_SESSION['Items']->LineItems as $StkItems_2) {
				/* add up total quantity of all lines of this DiscCat */
				if ($StkItems_2->DiscCat==$OrderLine->DiscCat){
					$QuantityOfDiscCat += $StkItems_2->Quantity;
				}
			}
			$result = DB_query("SELECT MAX(discountrate) AS discount
									FROM discountmatrix
									WHERE salestype='" .  $_SESSION['Items']->DefaultSalesType . "'
									AND discountcategory ='" . $OrderLine->DiscCat . "'
									AND quantitybreak <" . $QuantityOfDiscCat,$db);
			$myrow = DB_fetch_row($result);
			if ($myrow[0]!=0){ /* need to update the lines affected */
				foreach ($_SESSION['Items']->LineItems as $StkItems_2) {
					/* add up total quantity of all lines of this DiscCat */
					if ($StkItems_2->DiscCat==$OrderLine->DiscCat AND $StkItems_2->DiscountPercent == 0){
						$_SESSION['Items']->LineItems[$StkItems_2->LineNumber]->DiscountPercent = $myrow[0];
					}
				}
			}
		}
	} /* end of discount matrix lookup code */
}

/*
*  Display Order Summary
*/
if(  $_SESSION['RequireCustomerSelection'] == 0 and $_SESSION['Items']->DebtorNo != '' ){ //After Selected customer, do items
	/* This is where the order as selected should be displayed  reflecting any deletions or insertions*/
		echo '<br>
			<table class="commontable">
			<caption>Order Summary</caption>
			<tr bgcolor=#800000>';
		if($_SESSION['Items']->DefaultPOLine == 1){
			echo '<th>' . _('PO Line') . '</th>';
		}
		//echo '<DIV CLASS="page_help_text">' . _('Quantity (required) - Enter the number of units ordered.  Price (required) - Enter the unit price.  Discount (optional) - Enter a percentage discount.  GP% (optional) - Enter a percentage Gross Profit (GP) to add to the unit cost.  Due Date (optional) - Enter a date for delivery.') . '</DIV><BR>';
		echo '<th>' . _('Item Code*') . '</th>
			<th>' . _('Item Description') . '</th>
			<th>' . _('Quantity*') . '</th>
			<th>' . _('QOH') . '</th>
			<th>' . _('Unit') . '</th>
			<th>' . _('Price*') . '</th>';
		if (in_array(2,$_SESSION['AllowedPageSecurityTokens'])){	
			echo '<th>' . _('Discount') . '</th>
				  <th>' . _('GP %') . '</th>';
			if (!isset($ExRate)){
				if ($_SESSION['Items']->DefaultCurrency != $_SESSION['CompanyRecord']['currencydefault']){
					$ExRateResult = DB_query("SELECT rate FROM currencies WHERE currabrev='" . $_SESSION['Items']->DefaultCurrency . "'",$db);
					if (DB_num_rows($ExRateResult)>0){
						$ExRateRow = DB_fetch_row($ExRateResult);
						$ExRate = $ExRateRow[0];
					} else {
						$ExRate =1;
					}
				} else {
					$ExRate = 1;
				}
			}
		}
		echo '<th>' . _('Total') . '</th>
			  <th>' . _('Due Date') . '</th>
			  <th>' . _('Menu') . '</th>
			  </tr>';

		$_SESSION['Items']->total = 0;
		$_SESSION['Items']->totalVolume = 0;
		$_SESSION['Items']->totalWeight = 0;
		$k =0;  //row colour counter
		$j =0;
		
	if (count($_SESSION['Items']->LineItems)>0){ /*only show order lines if there are any */		
	
		foreach ($_SESSION['Items']->LineItems as $OrderLine) {
			if ($OrderLine->Price !=0){
				$GPPercent = number_format( (($OrderLine->Price * (1 - $OrderLine->DiscountPercent)) - ($OrderLine->StandardCost * $ExRate))*100/$OrderLine->Price,2);
			} else {
				$GPPercent = 0;
			}
			$LineTotal = $OrderLine->Quantity * $OrderLine->Price * (1 - $OrderLine->DiscountPercent);
			$DisplayLineTotal = number_format($LineTotal,2);
			$DisplayDiscount = number_format(($OrderLine->DiscountPercent * 100),2);
			$QtyOrdered = $OrderLine->Quantity;
			$QtyRemain = $QtyOrdered - $OrderLine->QtyInv;
			
			if ($QtyRemain != $QtyOrdered){
				$dispInvoicedQty = 'Invoiced';
			}else{
				$dispInvoicedQty = '';
			}
			
			$LineDueDate = $OrderLine->ItemDue;
			if (!Is_Date($OrderLine->ItemDue)){
				$LineDueDate = DateAdd (Date($_SESSION['DefaultDateFormat']),'d', $_SESSION['Items']->DeliveryDays);
				$_SESSION['Items']->LineItems[$OrderLine->LineNumber]->ItemDue= $LineDueDate;
			}			
			
			//$dispLineNumber = $OrderLine->LineNumber+1;			
			if ($OrderLine->Deleted==false) {
				if (isset($_POST['Edit']) and ($_POST['Edit']) == $OrderLine->LineNumber){			
					//echo 'HeeeeeeeeeeeeeeEdit:'.$_GET['Edit'].'J:'.$j;
					//echo '<input type="hidden" name="LineNo" value=' . $_GET['Edit'] .'>';					
					
					echo '<tr>
					<td>' . $OrderLine->StockID . '</td>
					<td>' . $OrderLine->ItemDescription . '</td>
					<td><input type="text" name="Qty_" value=' . $OrderLine->Quantity . '></td>
					<td>' . $OrderLine->QOHatLoc . '</td>
					<td>' . $OrderLine->Units . '</td>';
					
					if (in_array(2,$_SESSION['AllowedPageSecurityTokens'])){
						/*OK to display with discount if it is an internal user with appropriate permissions */
						echo '<td><input class="number" onKeyPress="return restrictToNumbers(this, event)"  type=text name="Price_" size=16 maxlength=16 value=' . $OrderLine->Price . '></td>
							<td><input class="number" onKeyPress="return restrictToNumbers(this, event)"  type=text name="Discount_" size=5 maxlength=4 value=' . ($OrderLine->DiscountPercent * 100) . '>%</td>
							<td><input class="number" onKeyPress="return restrictToNumbers(this, event)"  type=text name="GPPercent_" size=5 maxlength=4 value=' . $GPPercent . '>%</td>';	
					} else {
						echo '<td align=right>' . $OrderLine->Price . '</td><td></td>';
						echo '<input type=hidden name="Price_' . $OrderLine->LineNumber . '" value=' . $OrderLine->Price . '>';
					}	

					echo '</td><td align=right>' . $DisplayLineTotal . '</td>';

					echo '<td><input onChange="return isDate(this, this.value, '."'".$_SESSION['DefaultDateFormat']."'".')"  type=text name="ItemDue_" size=10 maxlength=10 value=' . $LineDueDate . '></td>';
					echo '<td class="menu"><img src="editgray.jpg" alt="edit"> <input type=image name="UpdateLine" value="' . $OrderLine->LineNumber . '" src="save.png"> <input type=image name="Delete" value="' . $OrderLine->LineNumber . '" src="delete.jpg">  </td></tr>';								
				}else{
					echo "<td>$OrderLine->StockID</td><td>$OrderLine->ItemDescription</td><td>$QtyOrdered</td><td>$OrderLine->QOHatLoc</td><td>$OrderLine->Units</td>
					<td> $OrderLine->Price </td><td>$OrderLine->DiscountPercent</td><td>$GPPercent</td><td>$DisplayLineTotal	</td>
					<td>$LineDueDate</td><td><input type=image name=Edit value=$OrderLine->LineNumber  src='edit.jpg'>
					<img src='savegray.png' alt='save'>
					<img src='deletegray.jpg' alt='delete'>
					</td></tr>";
				}				
				$_SESSION['PO']->total = $_SESSION['PO']->total + $LineTotal;
			}	
		}		
	}
	
	if (isset($_POST['AddLine'])){ 	
		echo '<td><input onclick=openSubpage("h5ItemsSearch.php") type="button" value="Select products" /></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>';
	}	
	
	$DisplayTotal = number_format($_SESSION['Items']->total,2);
	echo '<tr>
	<td colspan="8"><b>' . _('TOTAL Excl Tax/Freight') . '</b></td>
	<td >' . $DisplayTotal . '</td><td></td>
	<td><input type=submit name="AddLine" value="' . _('Add Line') . '"></td></tr></table>';

	$DisplayVolume = number_format($_SESSION['Items']->totalVolume,2);
	$DisplayWeight = number_format($_SESSION['Items']->totalWeight,2);
	echo '<table border=1><tr><td>' . _('Total Weight') . ':</td>
					 <td>' . $DisplayWeight . '</td>
					 <td>' . _('Total Volume') . ':</td>
					 <td>' . $DisplayVolume . '</td>
				   </tr></table>';

	echo '<br><div class="centre">
			<input type=submit name="DeliveryDetails" value="' . _('Enter Delivery Details and Confirm Order') . '"></div><hr>';	
}

echo '</form>';
echo '</section>';
echo '</div>';
?>