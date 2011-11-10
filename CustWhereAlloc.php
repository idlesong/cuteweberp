<?php
/* $Revision: 1.9 $ */
$PageSecurity = 2;

include('includes/session.inc');
$title = _('Customer How Paid Inquiry');
include('includes/header.inc');

echo "<FORM ACTION='" . $_SERVER['PHP_SELF'] . "' METHOD=POST>";

echo '<CENTER><TABLE CELLPADDING=2><TR>';

echo '<TD>' . _('Type') . ":</TD><TD><SELECT tabindex=1 name='TransType'> ";

$sql = 'SELECT typeid, typename FROM systypes WHERE typeid = 10 OR typeid=12';
$resultTypes = DB_query($sql,$db);
while ($myrow=DB_fetch_array($resultTypes)){
    if (isset($_POST['TransType'])){
        if ($myrow['typeid'] == $_POST['TransType']){
             echo "<OPTION SELECTED Value='" . $myrow['typeid'] . "'>" . $myrow['typename'];
        } else {
             echo "<OPTION Value='" . $myrow['typeid'] . "'>" . $myrow['typename'];
        }
    } else {
             echo "<OPTION Value='" . $myrow['typeid'] . "'>" . $myrow['typename'];
    }
}
echo '</SELECT></TD>';

if (!isset($_POST['TransNo'])) {$_POST['TransNo']='';}
echo '<TD>'._('Transaction Number').":</TD>
	<TD><INPUT tabindex=2 TYPE=TEXT NAME='TransNo' MAXLENGTH=10 SIZE=10 VALUE=". $_POST['TransNo'] . '></TD>';

echo "</TR></TABLE>
	<INPUT tabindex=3 TYPE=SUBMIT NAME='ShowResults' VALUE="._('Show How Allocated').'>';
echo '<HR>';

if (isset($_POST['ShowResults']) AND  $_POST['TransNo']==''){
	prnMsg(_('The transaction number to be queried must be entered first'),'warn');
}

if (isset($_POST['ShowResults']) AND  $_POST['TransNo']!=''){


/*First off get the DebtorTransID of the transaction (invoice normally) selected */
    $sql = 'SELECT id,
    		ovamount+ovgst AS totamt
		FROM debtortrans
		WHERE type=' . $_POST['TransType'] . ' AND transno = ' . $_POST['TransNo'];

    $result = DB_query($sql , $db);

    if (DB_num_rows($result)==1){
        $myrow = DB_fetch_array($result);
        $AllocToID = $myrow['id'];

        echo '<CENTER><FONT SIZE=3><B><BR>'._('Allocations made against invoice number') . ' ' . $_POST['TransNo'] . ' '._('Transaction Total').': '. number_format($myrow['totamt'],2) . '</FONT></B>';

        $sql = "SELECT type,
			transno,
			trandate,
			debtortrans.debtorno,
			reference,
			rate,
			ovamount+ovgst+ovfreight+ovdiscount as totalamt,
			custallocns.amt
		FROM debtortrans
			INNER JOIN custallocns ON debtortrans.id=custallocns.transid_allocfrom
		WHERE custallocns.transid_allocto=". $AllocToID;

        $ErrMsg = _('The customer transactions for the selected criteria could not be retrieved because');

        $TransResult = DB_query($sql, $db, $ErrMsg);
	
	if (DB_num_rows($TransResult)==0){
		prnMsg(_('There are no allocations made against this transaction'),'info');
	} else {
		echo '<TABLE CELLPADDING=2 BORDER=2>';
	
		$tableheader = "<TR><TH>"._('Type')."</TH>
					<TH>"._('Number')."</TH>
					<TH>"._('Reference')."</TH>
					<TH>"._('Ex Rate')."</TH>
					<TH>"._('Amount')."</TH>
					<TH>"._('Alloc').'</TH>
				</TR>';
		echo $tableheader;
	
		$RowCounter = 1;
		$k = 0; //row colour counter
		$AllocsTotal = 0;
	
		while ($myrow=DB_fetch_array($TransResult)) {
	
		if ($k==1){
			echo '<tr class="EvenTableRows">';
			$k=0;
		} else {
			echo '<tr class="OddTableRows">';
			$k++;
		}
	
		if ($myrow['type']==11){
			$TransType = _('Credit Note');
		} else {
			$TransType = _('Receipt');
		}
		printf( "<td>%s</td>
				<td>%s</td>
				<td>%s</td>
				<td>%s</td>
				<td ALIGN=RIGHT>%s</td>
				<td ALIGN=RIGHT>%s</td>
				</tr>",
				$TransType,
				$myrow['transno'],
				$myrow['reference'],
				$myrow['rate'],
				$myrow['totalamt'],
				$myrow['amt']);
	
		$RowCounter++;
		If ($RowCounter == 12){
			$RowCounter=1;
			echo $tableheader;
		}
		//end of page full new headings if
		$AllocsTotal +=$myrow['amt'];
		}
		//end of while loop
		echo '<TR><TD COLSPAN = 6 ALIGN=RIGHT>' . number_format($AllocsTotal,2) . '</TD></TR>';
		echo '</TABLE>';
	}
    }
}

echo '</FORM></CENTER>';
include('includes/footer.inc');

?>