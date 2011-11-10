<?php

$PageSecurity = 3;


include('includes/session.inc');
$title = _('Customer Receipt') . '/' . _('Credit Note Allocations');
include('includes/header.inc');

if (!isset($_POST['AfterDate']) OR !Is_Date($_POST['AfterDate'])){
   //$_POST['AfterDate'] = Date($_SESSION['DefaultDateFormat'], Mktime(0,0,0,1,1,Date('y')));
   $_POST['AfterDate'] = Date($_SESSION['DefaultDateFormat'], Mktime(0,0,0,1,1,Date('y')));
}
$SQLAfterDate = FormatDateForSQL($_POST['AfterDate']);
$settledFlag = 1;

echo '<P CLASS="page_title_text"> Fiancial Affiar:Home </P>';
echo '<HR>';
//-- Print Receipts didn't Allocated  --

	$TableHeader = "<tr>
		     		<th> " . _('Date') . "</th>	
		     		<th>" . _('Cust No') . "</th>
		     		<th>" . _('Customer') . "</th>					
		     		<th>" . _('Trans Type') . "</th>
		     		<th>" . _('Number') . "</th>
		     		<th>" . _('Total') . "</th>
		     		<th>" . _('To Alloc') . "</th>
		     		<th>" . _('Action') . "</th>
		     	</tr>";

		//unset($_SESSION['Alloc']->Allocs);
		//unset($_SESSION['Alloc']);
		$curDebtor = 1;

		$SQL = "SELECT debtortrans.id,
				debtortrans.transno,
				systypes.typename,
				debtortrans.type,
				debtortrans.debtorno,
				debtorsmaster.name,
				debtortrans.trandate,
				debtortrans.reference,
				debtortrans.rate,
				debtortrans.ovamount+debtortrans.ovgst+debtortrans.ovdiscount+debtortrans.ovfreight as total,
				debtortrans.alloc,
				debtortrans.settled
				FROM debtortrans,
				debtorsmaster,
				systypes
				WHERE debtortrans.type=systypes.typeid AND
				debtortrans.debtorno=debtorsmaster.debtorno AND
				(type=12 or type=11) AND
				(settled=0 or settled ='" .$settledFlag. "') AND
				debtortrans.trandate  >= '". $SQLAfterDate ."' AND
				debtortrans.ovamount<0
				ORDER BY debtorsmaster.name";
		$result = DB_query($SQL,$db);
		$trans = DB_num_rows($result);
		$curTrans = 1;
		
		echo '<P CLASS="page_subtitle_text"> >>All Customer Receipts and Credit Note </P>';		
		//echo '<table border=1>';
		echo "<input onclick=\"javascript:window.location.href='SelectCustomer.php?'\"  type='button' value='Enter Receipts' />";		
		echo '<table width="100%">';
		echo $TableHeader;

		$z=0;
		while ($myrow = DB_fetch_array($result))
		{
			$allocate = '<a href=' . 'CustomerAllocations.php?' . SID . '&AllocTrans=' . $myrow['id'] . '>' . _('Allocate') . '</a>';

			if ( $curDebtor != $myrow['debtorno'] )
			{
			/*
				if ( $curTrans > 1 )
				{
					echo "<tr class='OddTableRows'><td colspan=7 align=right>" . number_format($balance,2) . "</td><td><b>Balance</b></td></tr>";
				}
			*/
				$balance = 0;
				$curDebtor = $myrow['debtorno'];

				$balSQL= "SELECT ovamount+ovgst+ovfreight+ovdiscount as total
					FROM debtortrans
					WHERE debtortrans.settled=0 AND
					debtorno='" . $myrow['debtorno'] . "'
					ORDER BY ovamount";
				$balResult = DB_query($balSQL,$db);

				while ($balRow = DB_fetch_array($balResult))
				{
					$balance += $balRow['total'];
				}
				DB_free_result($balResult);
			}
			$curTrans ++;

			if ( isset($balance) and $balance < -0.01 )
			{
				$allocate = '&nbsp;';
			}
			
			if ( $myrow['settled'] == 1 ){
				$allocate = '&nbsp;';
			}
			
			echo "
					<td>" . ConvertSQLDate($myrow['trandate']) . "</td>	
					<td>" . $myrow['debtorno'] . "</td>
					<td>" . $myrow['name'] . "</td>
					<td>" . $myrow['typename'] ."</td>
					<td>" . $myrow['transno'] . "</td>
					<td align=right>" . number_format($myrow['total'],2) . "</td>
					<td align=right>" . number_format($myrow['total']-$myrow['alloc'],2) . "</td>";
			echo '<td>' . $allocate . '</td></tr>';
/*
			if ( $curTrans > $trans )
			{
				if (!isset($balance)) {
					$balance=0;
				}
				echo "<tr class='OddTableRows'><td colspan=7 align=right>" . number_format($balance,2) . "</td><td><b>Balance</b></td></tr>";
			}
*/			
		}
		DB_free_result($result);
		echo '</table><p>';

		if ($trans == 0)
		{
			prnMsg(_('There are no allocations to be done'),'info');
		}

include('includes/footer.inc');

?>