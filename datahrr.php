<?php 
defined ('BASEPATH') OR exit('No direct script access allowed');
class Products_model extends CI_Model
{
  function __construct()
  {
    parent::__construct();
    $this->load->database();
    $this->load->library('session');
  }
  function saveproduct($save,$product_id=null)
  {
    if(!empty($product_id)){
      $this->db->where('product_id',$product_id);
      $update=$this->db->update('products_master',$save);
      return $update;
    }else{
      //echo "insert";exit;
      $query=$this->db->insert('products_master',$save);
      return $query;
    }
  }
  function product_list($product_id=null)
  {
    if(!empty($product_id)){
      $this->db->select('*');
      $this->db->from('products_master');
      $this->db->where('product_id',$product_id);
      $this->db->order_by("product_id","desc");
      $data=$this->db->get();       
      return $data->result();
    }else{
      $this->db->select('*');
      $this->db->from('products_master');
      $this->db->order_by("product_id","desc");
      $data=$this->db->get();       
      return $data->result();
    }
    
  }
  function getProductData($startDate,$startTime,$endDate,$endTime,$place=null)
  {
    $queryresultData = array();
      $this->db->select('*');
      $this->db->from('products_master');
       // $this->db->join('size_master s','s.product_id=p.product_id');
if(!empty($place))
{
       $this->db->where('location_name',$place);
     }
      $this->db->order_by("product_id","desc");
      $data=$this->db->get();       
      $result = $data->result();
      // print_r($result);exit;
        $date1 = $startDate ." ".$startTime;
            $date2 = $endDate ." ".$endTime;
            // $datea = new DateTime(trim($date1, '"'));
            $datea = new DateTime($date1);
          $dateb = new DateTime($date2);
          $diff = $dateb->diff($datea);
          $hours = $diff->h;
          $hours = $hours + ($diff->days*24);
      $i=0;
      foreach ($result as $bikedata) {
       $resultData['id'] = $bikedata->product_id;
        $resultData['image'] = $bikedata->image1;
        $kmshour=($bikedata->kms/$bikedata->hours);
        $resultData['kms'] = $this->getkms($startDate,$startTime,$endDate,$endTime,$bikedata->product_id);
        // $resultData['kms'] = $kmshour*$hours;
        $resultData['excess'] = $bikedata->excess;
        $resultData['bikename'] = $bikedata->bikename;
        $priceperhour = ($bikedata->price / $bikedata->hours);
        
    //    $resultData['price'] = $priceperhour*$hours;
      $resultData['price'] = $this->getWeekEndPrice($startDate,$startTime,$endDate,$endTime,$bikedata->product_id);
   
        $resultData['status']=$bikedata->status;
        // $resultData['weekofprice'] =$bikedata->weekofprice;
        
        // $weekendHours = $hours;
        $queryresultData[$i] = $resultData;
        $i++;
      }
      return $queryresultData;
  }
  function product_delete($product_id)
  {
     $this->db->where('product_id',$product_id);
     $this->db->delete('products_master');
  }
  function savefleetandprice($save,$id=null)
  {
    if(!empty($id)){
      $this->db->where('id',$id);
      $update=$this->db->update('fleetandprice_master',$save);
      return $update;
    }else{
      //echo "insert";exit;
      $query=$this->db->insert('fleetandprice_master',$save);
      return $query;
    }
  }
  function getProUser($c_id){
   
      $this->db->select('*');
      $this->db->from('products_master');
      $this->db->where('product_id',$c_id);
      $data=$this->db->get();       
      return $data->result();
    }
     
  
  function fleetandprice_list($id=null)
  {
    if(!empty($id)){
      $this->db->select('*');
      $this->db->from('fleetandprice_master');
      $this->db->where('id',$id);
      $this->db->order_by("id","desc");
      $data=$this->db->get();       
      return $data->result();
    }else{
      $this->db->select('*');
      $this->db->from('fleetandprice_master');
      $this->db->order_by("id","desc");
      $data=$this->db->get();       
      return $data->result();
    }
    
  }
   function fleetandprice_delete($id)
  {
     $this->db->where('id',$id);
     $this->db->delete('fleetandprice_master');
  }
  function getkms($startDate,$startTime,$endDate,$endTime,$productId){
    
    $this->db->from('products_master');
    // $this->db->join('size_master s','s.product_id=p.product_id');
    $this->db->where('product_id',$productId);
    // $this->db->where('s.product_id',$productId);
    $bikeResult = $this->db->get();
    $bikePriceData = $bikeResult->row();
    $kms_hourly=($bikePriceData->kms/$bikePriceData->hours);
    $strStartDate = strtotime($startDate);
    $strEndDate = strtotime($endDate);
if($strStartDate == $strEndDate)
{
  $hoursDiff = strtotime($endTime) - strtotime($startTime);
  $hoursDiff = ($hoursDiff/3600);
  $dateDay = date('D',strtotime($startDate));
  if($dateDay == 'Sat' || $dateDay == 'Sun')
  {
      $kms = $bikePriceData->kms;
    
     
  }
  else{
     if($hoursDiff>=24)
      {
       
        $graterhours = $hoursDiff-24;
   //     $decPrice = $graterhours*($bikePriceData->decrease_price/$bikePriceData->decrease_hours);
        $greaterPrice = $graterhours*$kms_hourly;
        
        //$totalAmount =  ($hoursDiff*$weeklyPrice)-$decPrice;
        $kms = $bikePriceData->kms+$greaterPrice;
      }
      else
      {
           if($hoursDiff>5){
             $kms = $hoursDiff*$kms_hourly;
        }else{
            $kms = 5*$kms_hourly;
        }
     
     
      }
  //  $totalAmount = $hoursDiff * $weeklyPrice;
  }
      
        // $kms_hourly=($bikePriceData->kms/$bikePriceData->hours);
    
}else{
  $enddt = date('m/d/Y',strtotime($endDate."1 day"));
  $kms = 0;
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
  $endingTime = strtotime("11:59:59 PM");
  $endingTime = $endingTime + 1;
   $startingTime = strtotime("12:00:00 AM");
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
        $timeDiff = $endingTime - strtotime($startTime) ;
    
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
        
        $timeDiff = $endingTime - strtotime($startTime);
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
        //   print_r($weekHoursCount);exit;
        }
      }
    }
    $i++;
  }
  if($weekendHoursCount > 24)
     {
     
        $greaterWeek = $weekendHoursCount-24;
        $weekdecAmount = $greaterWeek*$kms_hourly;
        
        $weekEndAmount = $bikePriceData->kms+$weekdecAmount;
       
    }else{
      if(!empty($weekendHoursCount))
        {  
        
        $weekEndAmount = $bikePriceData->kms;
     
        }
        else
        {
          $weekEndAmount = 0;
        }
    }if($weekHoursCount>24)
     {
   
      $greaterwkhr = $weekHoursCount-24;
   
      $weekAmount = $greaterwkhr*$kms_hourly;
    
      if($greaterwkhr > 0)
      {
        
          $extraAmount = $bikePriceData->kms+$kms_hourly;
        
        //   $extraAmount = $greaterwkhr*$weeklyPrice;
          $weekAmount = $weekAmount+$extraAmount;
      }
      else
      {
      } 
     }else{
     if(!empty($weekHoursCount))
      {
      $weekAmount = $weekHoursCount*$kms_hourly;
      }
      else
      {
        $weekAmount = 0;
      }
     }
     
 $kms = $weekAmount + $weekEndAmount;
     }
 $kms=round($kms);
  return $kms;
}
  function getWeekEndPrice($startDate,$startTime,$endDate,$endTime,$productId)
  {
    $this->db->from('products_master');
    
    // $this->db->join('size_master s','s.product_id=p.product_id');
    $this->db->where('product_id',$productId);
    // $this->db->where('s.product_id',$productId);
    $bikeResult = $this->db->get();
    $bikePriceData = $bikeResult->row();
    // print_r($bikePriceData);exit;
    $weekendPrice = ($bikePriceData->weekendPrice/$bikePriceData->hours);
  //$weeklyHours = 1;
    $weeklyPrice = ($bikePriceData->hoursbelowprice/$bikePriceData->hours);
    $normalPrice = ($bikePriceData->price/$bikePriceData->hours);
    // print_r($weeklyPrice);exit;
 //$weeklyPrice_decrease = ($bikePriceData->decrease_price/$bikePriceData->decrease_hours);
 //$weekendPrice_decrease = ($bikePriceData->decrease_weekendprice/$bikePriceData->decrease_hours);
 //$weekendHours = 1;
 
  $strStartDate = strtotime($startDate);
  $strEndDate = strtotime($endDate);
if($strStartDate == $strEndDate)
{
  $hoursDiff = strtotime($endTime) - strtotime($startTime);
  $hoursDiff = ($hoursDiff/3600);
  $dateDay = date('D',strtotime($startDate));
  if($dateDay == 'Sat' || $dateDay == 'Sun')
  {
 //   $totalAmount = $hoursDiff*$weekendPrice;
  
   /* if($hoursDiff>=$bikePriceData->decrease_hours){
         $totalAmount = $hoursDiff*$weekendPrice_decrease;
      }else{
       $totalAmount = $hoursDiff*$weekendPrice;
      }
*/
      $totalAmount = $bikePriceData->weekendPrice;
  }
  else
  {
     /* if($hoursDiff>=$bikePriceData->decrease_hours){
         $totalAmount = $hoursDiff*$weeklyPrice_decrease;
       
      }else{
       $totalAmount = $hoursDiff*$weeklyPrice;
    
      }
      */
      if($hoursDiff>=24)
      {
  echo "haii";exit;
        $graterhours = $hoursDiff-24;
   //     $decPrice = $graterhours*($bikePriceData->decrease_price/$bikePriceData->decrease_hours);
        $greaterPrice = $graterhours*$weeklyPrice;
        //$totalAmount =  ($hoursDiff*$weeklyPrice)-$decPrice;
        $totalAmount = $bikePriceData->price+$greaterPrice;
      }
      else
      {
       
        if($hoursDiff>5){
           $totalAmount = $hoursDiff*$weeklyPrice;
         }else{
        
        $totalAmount = 5*$weeklyPrice;
         }
       
      }
  //  $totalAmount = $hoursDiff * $weeklyPrice;
  }
}
else
{
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
  
  
  
  
    $startingDay = date('D',strtotime($startDate));
    $endingDay = date('D',strtotime($endDate));
  
  
    $weekendStartFlag = 0;
    $weekendEndFlag = 0;
    if($startingDay == 'Fri')
    {
    $weekendStartFlag = 1;
    }
   
    if($endingDay == 'Mon')
    {
    $weekendEndFlag = 1;
    }
	
  $totCount = iterator_count($period);
  $i = 1;
  
  foreach ($period as $key => $value) {
	  
  
    $btwDate = $value->format('m/d/Y');
    $day = date('D',strtotime($btwDate));
    if(in_array($day,$dayArr))
    {
		 $weekOffCount++;
      if($i == '1')
      {
        $timeDiff = $endingTime - strtotime($startTime) ;
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
		
		if((($day == 'Fri')&&($i != $totCount)) || (($day == 'Mon') && ($i>1)))
		{
			
			 if(($day == 'Fri')&&($i != $totCount))
			 {
			    $timeDiff = $endingTime - strtotime($startTime) ;
				$weekendHoursCount =  $weekendHoursCount+(($timeDiff)/3600);
			 }
			 else{
				    $endDiff = strtotime($endTime) - $startingTime;
					$weekendHoursCount =  $weekendHoursCount+(($endDiff)/3600);
			 }
				
		}
		else   if((($weekendStartFlag == '1')&&($day = 'Fri') )||(($weekendEndFlag == '1')&&($day = 'Mon')))
      {
        if(($weekendStartFlag == '1')&&($day = 'Fri') )
        {
          $timeDiff = $endingTime - strtotime($startTime) ;
          $weekendHoursCount =  $weekendHoursCount+(($timeDiff)/3600);
        }
        else{
          $endDiff = strtotime($endTime) - $startingTime;
          $weekendHoursCount =  $weekendHoursCount+(($endDiff)/3600);
        }
      
          
      }
		else
		{
			 if($i == '1')
        {
          
          $timeDiff = $endingTime - strtotime($startTime) ;
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
		
		
		
	}
	
	  
  }
  
		if($weekHoursCount >= 24)
		{
			$extrahrsdays = $weekHoursCount/24;
			$extrahrs =  $weekHoursCount -24;
		    $extrahrsAmount = $extrahrs*$weeklyPrice;
		    $weekAmount = ($extrahrsdays*$bikePriceData->price)+$extrahrsAmount;
		}
		else{
			$weekAmount = $weekHoursCount*$weeklyPrice; 
		}
		
		if($weekendHoursCount >=24)
		{
		  $weekendextrahrsdays = $weekendHoursCount / 24;
		  $weekendextrahrs =  $weekendHoursCount-24;
		  $weekendextrahrsAmount = $weekendextrahrs*$weeklyPrice;
		  $weekEndAmount = ($weekendextrahrsdays*$bikePriceData->weekendPrice)+$weekendextrahrsAmount;
		  
		}
		else{
		  $weekEndAmount = $bikePriceData->weekendPrice;
		  // $weekEndAmount = $weekendHoursCount*$weekendPrice;
		}
		
		if(!empty($weekHoursCount))
      {
			$totalAmount = $weekAmount;
      }
      else
      {
			$totalAmount = $weekAmount + $weekEndAmount;
      }
  
}
//echo $totalAmount."<br>";
  $totalAmount=round($totalAmount);
  // echo $totalAmount."<br>";
  $totalAmount = $this->test($totalAmount);
  //echo $totalAmount."<br>";
 //echo $totalAmount;
//  $totalAmount=round($totalAmount);
//  $totalAmount=str_replace(substr($totalAmount, -1), 9, $totalAmount);
  return $totalAmount;
  
  
  }
  
function test($totalAmount){
  $variable=$totalAmount;
 $len=strlen ( $variable );
 $last_2nd=substr($variable,$len-2,1);
 if ($last_2nd==1)
 $last_2nd=2;
 $last=substr($variable,$len-1,1);
 $last=$last+(9-$last);
 $variable=substr($variable,0,$len-2).$last_2nd.$last;
 return $variable;
  }
  
}