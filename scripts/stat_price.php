<?php
/**
 * Created by PhpStorm.
 * User: Srinivas
 * Date: 11/15/2016
 * Time: 1:57 AM
 *
 * This script generates price statistics
 * 
 */
  
  //Default to city
  
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
  * Section to generate average prices
  */
 
 $SQL = "SELECT DISTINCT $param from residential";
 //echo "$SQL" . '<br>';
 $result_city = $conn->query($SQL);
 

 while($data=mysqli_fetch_array($result_city, MYSQLI_NUM)) 
	{
	//For each result in city, fetch the average, min, max values of list price
	$SelectQ = "SELECT min(listprice), ceil(avg(listprice)), max(listprice) , round(avg(listprice)/avg(totsqf))
	from residential where $param='$data[0]'";
	//echo $SelectQ . '<br>';
	$resSelect = $conn->query($SelectQ);
	if(!$resSelect)
		echo "SelectQ Query Failed". "<br>";
	
	$resultS = mysqli_fetch_array($resSelect, MYSQLI_NUM);
	
	$minPrice = $resultS[0];
	$avgPrice = $resultS[1];
	$maxPrice = $resultS[2];
	$price_sqft = $resultS[3];
	echo "$price_sqft" . '$price_sqft' . '<br>';

	$SelectQ = "SELECT round(avg(col)) FROM 
	(
	SELECT listprice/(totsqf-levbsqf) * (0.01 * ( 100 - basmntfin)) as col
	FROM clientopoly.residential 
	WHERE $param = '$data[0]'
	)  intermediate
	WHERE col > 0";
	
	//echo "$SelectQ" . '<br>';
	
	$resSelect = $conn->query($SelectQ);
	if(!$resSelect)
		echo "SelectQ Query Failed". "<br>";
	
	$resultS = mysqli_fetch_array($resSelect, MYSQLI_NUM);
	$price_fsqft = $resultS[0];
	//echo "$price_fsqft" . '$price_fsqft' . '<br>';
	
	//Get the median price of homes in that city
	$MedianSQL = "SELECT ceil(avg(t1.listprice)) as median_val FROM (
	SELECT @rownum:=@rownum+1 as `row_number`, d.listprice
	FROM residential d,  (SELECT @rownum:=0) r
	WHERE $param = '$data[0]'
	ORDER BY d.listprice
	) as t1, 
	(
		SELECT count(*) as total_rows
		FROM residential d
		WHERE '$param' = '$data[0]'
	) as t2
	WHERE 1
	AND t1.row_number in ( floor((total_rows+1)/2), floor((total_rows+2)/2) );";
	
	//echo $MedianSQL . '<br>';
	
	$resMedian = $conn->query($MedianSQL);
	
	if(!$resMedian)
	echo "MedianQ Query Failed". "<br>";

	$resultS = mysqli_fetch_array($resMedian, MYSQLI_NUM);
	$medianPrice = $resultS[0];
    
	$tableName = "stat_" . $param . "_price";
    
	//Insert into stats_price
	$InsertQuery = "INSERT INTO $tableName
	(
	`$param`,
	`proptype`,
	`low`,
	`average`,
	`median`,
	`high`,
	`avg_price_sqft`,
	`avg_price_fsqft`,
	`date`)
     VALUES 
	('$data[0]', 'residential', '$minPrice', '$avgPrice', '$medianPrice', '$maxPrice', '$price_sqft', '$price_fsqft', now())";
	
	//echo $InsertQuery . '<br>';
		
	$resi = $conn->query($InsertQuery);
	if(!$resi)
		echo "Insert Query Failed or Record aleady present" . "<br>";  
	}

$conn->close();
echo "Finished!" . '<br> <br>'; 
