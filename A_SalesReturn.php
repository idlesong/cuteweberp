<?php

$PageSecurity = 4;


include('includes/session.inc');
include('includes/header.inc');
include('includes/SQL_CommonFunctions.inc');

if( isset($_SESSION['PO']->SupplierID) ){
	echo $_SESSION['PO']->SupplierID;
}else{
	echo "Hello world2";
}

$currentDate = Date($_SESSION['DefaultDateFormat']);

echo "<script>
function openSubpage(subpageURL){
window.open(subpageURL, 'location=no', 'scrollbars=yes', 'resizable=yes');
//window.alert(subpageURL);
}

function popupUpdate()
{
	var ret=prompt(\"Return Qty:\");
	if(ret == null){
		return false;
	}else if(ret.replace(/^\s+|\s+$/g, \"\") == null) {
		alert(\"Iq\");
		return false;
	}
	 document.getElementById(\"retQty\").innerHTML=ret;
	 //window.alert(ret);

}
</script>
";

echo '<h1> Sales exchange deals </h1><hr>';

//echo "<input onclick=\"javascript:openSubpage('$rootpath/PO_HeaderSearch.php');\"  type='button' value='Customer' />";
echo "<input onclick=openSubpage('$rootpath/PO_HeaderSearch.php'); type='button' value='Supplier' />";
echo "<input type='button' value='add' onclick='popupUpdate()'/>";

echo '<input type="text" name="TextBox1" id="TextBox1" />';
echo "<input onclick=openSubpage('$rootpath/h5SearchCustomers.php'); type='button' value='Customer' />";


echo "<table> 
<tr><td>Customr</td> <td>Data</td> <td> Stock </td> <td> Quantity </td> <td> Ex shipp date</td> <td> total quantity</td> <td> last date </td></tr>
<tr><td>HYT    </td> <td>".$currentDate."</td> <td> Stock </td> <td id='retQty'>  </td> <td> Ex shipp date</td> <td> total quantity</td> <td> last date </td></tr> </table>";

echo "<hr>";

echo '<FORM ACTION="' . $_SERVER['PHP_SELF'] . '?' . SID . '" METHOD=POST>';	
echo $msg;

$_POST['Search']='%';;
$_POST['Keywords']='%';
if (isset ($_POST['Search'])){
	if (strlen($_POST['Keywords'])>0) {

		$_POST['Keywords'] = strtoupper(trim($_POST['Keywords']));
		//insert wildcard characters in spaces

		$i=0;
		$SearchString = "%";

		while (strpos($_POST['Keywords'], " ", $i)) {
			$wrdlen=strpos($_POST['Keywords']," ",$i) - $i;
			$SearchString=$SearchString . substr($_POST['Keywords'],$i,$wrdlen) . "%";
			$i=strpos($_POST['Keywords']," ",$i) +1;
		}
		$SearchString = $SearchString . substr($_POST['Keywords'],$i)."%";

			$SQL = "SELECT faillog.id,
			faillog.debtorno,
			faillog.returndate,
			faillog.stkcode,
			faillog.quantity,
			faillog.lastshipdate,
			faillog.totalqtyshipped,
			faillog.consignment
			FROM faillog
			WHERE 1
			ORDER BY faillog.id";
			//faillog.debtorno " . LIKE . " '$searchString'
	} 
		
	$ErrMsg = _('The searched customer records requested cannot be retrieved because');
	$result = DB_query($SQL,$db,$ErrMsg);
	if (DB_num_rows($result)<>0){

		//$ListCount=DB_num_rows($result);
		echo "Hello world!!!!!";
		echo '<table>';
		$TableHeader = '<TR>
				<TH>' . _('debtorno') . '</TH>
				<TH>' . _('id') . '</TH>
				<TH>' . _('return date') . '</TH>
				<TH>' . _('stkcode') . '</TH>
				<TH>' . _('quantity') . '</TH>
				<TH>' . _('lastshipdate') . '</TH>
				<TH>' . _('totalqtyshipped') . '</TH>
				<TH>' . _('consignment') . '</TH>
			</TR>';

		echo $TableHeader;
		
        $rowNum = 0;
		$editRowNum = 0;
		while (($myrow=DB_fetch_array($result)))
		{	
			$rowNum++;
			printf("<tr><td><a href='A_CustomerOverview.php?" . SID . "&DebtorNo=%s'>%s</td>
				<td>%s</td>
				<td ALIGN=RIGHT></td>
				<td>%s</td>
				<td>%s</td>
				<td>%s</FONT></td>
				<td>%s</FONT></td>
				<td>%s</FONT></td></tr>",
				$myrow['debtorno'],
				$myrow['debtorno'],
				$myrow['id'],
				$myrow['return date'],
				$myrow['stkcode'],
				$myrow['quantity'],
				$myrow['lastshipdate'],
				$myrow['totalqtyshipped'],
				$myrow['consignment']);	
		}
		echo '</table>';
		
		unset($result);
	} elseif (DB_num_rows($result)==0){
		prnMsg(_('No customer records contain the selected text') . ' - ' . _('please alter your search criteria and try again'),'info');
	}

	
}	
echo '</FORM>';

echo '</body>';
//include('includes/footer.inc');

?>