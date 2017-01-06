<?php
/**
 * Created by PhpStorm.
 * User: Srinivas
 * Date: 11/15/2016
 * Time: 1:57 AM
 *
 * This scripts computes the avg_bed,avg_bath, avg_garage, avg_lotsize metrics
 * Need to update the avg_garage
 */
 
  //Database connection
  include('/tools/connect.php');

 /*
  * Section to generate total listing per city
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
  
 $SQL = "SELECT DISTINCT $param from residential";
 $result_city = $conn->query($SQL);
 
 while($data=mysqli_fetch_array($result_city, MYSQLI_NUM)) 
 {
	 $SelectQ = "SELECT round(avg(totbed), 2), round(avg(totbathfull),1), round(avg(dimacres),2) FROM residential WHERE $param='$data[0]'";
	 $resSelect = $conn->query($SelectQ);
	 
	 $resultS = mysqli_fetch_array($resSelect, MYSQLI_NUM);
	 $avgBed = $resultS[0];
	 $avgBath = $resultS[1];
	 $avgAcres = $resultS[2];
	 
	 $tableName = "stat_" . $param . "_misc";
	 
	 if($resSelect) {
		 
		$InsertQ = "INSERT INTO $tableName (`$param`,`avg_bed`,`avg_bath`,`avg_garage`,`avg_lotsize`,`date`) VALUES
					('$data[0]','$avgBed', '$avgBath', 1, '$avgAcres', now());";
		
		$resInsert = $conn->query($InsertQ);
		if(!$resInsert)
			echo "InsertQ Query Failed" . $InsertQ . "<br>";
	        }
		else {
		echo "SelectQ Query Failed". "<br>" . '$resSelect';
		}
 }
 
 echo "Finished inserting total listings...." . "<br>";
