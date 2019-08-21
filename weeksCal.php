<?php

	$weeklyPrice = 200;
	$weeklyHours = 1;

	$weekendPrice = 240;
	$weekendHours = 1;

	$startDate = "08/23/2019";
	$startTime = "04:00 PM";

	$endDate = "08/26/2019";
	$endTime = "10:00 PM";

	echo $startDate."***startdate**<br>";
	echo $endDate."***endDate***<br>";

	echo $startTime."****startTime***<br>";
	echo $endTime."****end Time***<br>";

	$enddt = date('m/d/Y',strtotime($endDate."1 day"));

	$totalAmount = 0;

	$period = new DatePeriod(
     new DateTime($startDate),
     new DateInterval('P1D'),
     new DateTime($enddt)
	);

	$dayArr = array('Sun','Sat');

	$weekDayCount = 0;
	$weekOffCount = 0;

	$weekHoursCount = 0;
	$weekendHoursCount = 0;

	//print_r($dayArr);
	$endingTime = strtotime("11:59:59 PM");
	$endingTime = $endingTime + 1;
	 $startingTime = strtotime("12:00:00 AM");
	//echo strtotime("04:00 PM")."<br>";

	//print_r($period);
	//echo iterator_count($period);
	$totCount = iterator_count($period);
	$i = 1;
	foreach ($period as $key => $value) {
    //echo $value->format('m/d/Y')."<br>";       
		$btwDate = $value->format('m/d/Y');

		$day = date('D',strtotime($btwDate));

		if(in_array($day,$dayArr))
		{
			$weekOffCount++;
			if($i == '1')
			{
				$timeDiff = $endingTime - strtotime($startTime)	;
				$weekendHoursCount = $weekendHoursCount+(($timeDiff)/3600);
			}
			else
			{
				if($i == $totCount)
				{
					$endDiff = strtotime($endTime) - $startingTime;
					$weekendHoursCount = $weekendHoursCount+(($endDiff)/3600);
				}
				else
				{
					$weekendHoursCount = $weekendHoursCount+24;
				}
			}
		}
		else
		{
			$weekDayCount++;
			if($i == '1')
			{
				
				$timeDiff = $endingTime - strtotime($startTime)	;
				$weekHoursCount = $weekHoursCount+(($timeDiff)/3600);	
			}
			else
			{
				if($i == $totCount)
				{
					$endDiff = strtotime($endTime) - $startingTime;
					$weekHoursCount = $weekHoursCount+(($endDiff)/3600);
				}
				else
				{
					$weekHoursCount = $weekHoursCount+24;
				}
			}
		}


		$i++;

	}	


	echo $weekDayCount."***weekDayCount***<br>";
	echo $weekOffCount."***weekoffcount***<br>";



	echo $weekendHoursCount."****weekEndHoursCount**<br>";
	echo $weekHoursCount."***weekHoursCount***<br>";


	$weekAmount = $weekHoursCount*($weeklyPrice/$weeklyHours);
	$weekEndAmount = $weekendHoursCount*($weekendPrice/$weekendHours);

	echo $weekAmount."***weekAmount***<br>";
	echo $weekEndAmount."***weekEndAmount***<br>";


	$totalAmount = $weekAmount + $weekEndAmount;
	echo "*********************************<br>";
	echo $totalAmount."***Total Amount***<br>";
	echo "*********************************<br>";
?>