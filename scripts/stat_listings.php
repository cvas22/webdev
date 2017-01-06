<?php
/**
 * Created by PhpStorm.
 * User: Srinivas
 * Date: 11/15/2016
 * Time: 1:57 AM
 *
 * This script generates price statistics
 */

 
  $param = "city";
 
  if ($_GET)
  {
  if ( $_GET['arg'] == "city")
  {
	   $param = "city";
  }
  
  if ( $_GET['arg'] == "zip" )
  {
	  $param = "zip";
  }
  
  // Only two states
  if ( $_GET['arg'] == "state" )
  {
  	  $param = "state";
  }
 
  if ( $_GET['arg'] == "county" )
  {
	  $param = "county";
  }
  
  if ( $_GET['arg'] == "schooldistrict" )
  {
	  $param = "schooldistrict";
  }
  }

  
 //Database connection
 include('/tools/connect.php');

 /*
  * Section to generate total listing per city
  */
  
 $SQL = "SELECT DISTINCT $param from residential";
 $result_city = $conn->query($SQL);
 
 while($data=mysqli_fetch_array($result_city, MYSQLI_NUM)) 
 {
	 $SelectQ = "SELECT count(*) as number FROM residential WHERE $param='$data[0]'";
	 $resSelect = $conn->query($SelectQ);
	 
	 $resultS = mysqli_fetch_array($resSelect, MYSQLI_NUM);
	 $count = $resultS[0];
	 
	$tableName = "stat_" . $param . "_listing";

	 if($resSelect) {	
		$InsertQ = "INSERT INTO $tableName (`$param`,`listings`,`status`,`date`) VALUES
					('$data[0]', '$count', 'residential', now());";
		
		$resInsert = $conn->query($InsertQ);
		if(!$resInsert)
			echo "InsertQ Query Failed" . $InsertQ . "<br>";
	        }
		else {
		echo "SelectQ Query Failed". "<br>" . '$resSelect';
		}
 }
 
 echo "Finished!" . '<br> <br>'; 

 