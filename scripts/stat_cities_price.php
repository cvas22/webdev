<?php
/**
 * Created by PhpStorm.
 * User: Srinivas
 * Date: 11/15/2016
 * Time: 1:57 AM
 *
 * This script generates price statistics per city
 *    *** Please note - need to update the logic for "price per finished square feet"
 */

 //Database connection
 include('/tools/connect.php');

 /*
  * Section to generate average prices
  */
 
 $SQL = "SELECT DISTINCT city from residential";
 $result_city = $conn->query($SQL);
 

 while($data=mysqli_fetch_array($result_city, MYSQLI_NUM)) 
	{
	//For each result in city, fetch the average, min, max values of list price
	$SelectQ = "SELECT min(listprice), ceil(avg(listprice)), max(listprice) from residential where city='$data[0]'";
	//echo $SelectQ . '<br>';
	$resSelect = $conn->query($SelectQ);
	if(!$resSelect)
		echo "SelectQ Query Failed". "<br>";
	
	$resultS = mysqli_fetch_array($resSelect, MYSQLI_NUM);
	
	$minPrice = $resultS[0];
	$avgPrice = $resultS[1];
	$maxPrice = $resultS[2];
	
	
	//Get the median price of homes in that city
	$MedianSQL = "SELECT ceil(avg(t1.listprice)) as median_val FROM (
	SELECT @rownum:=@rownum+1 as `row_number`, d.listprice
	FROM residential d,  (SELECT @rownum:=0) r
	WHERE city = '$data[0]'
	ORDER BY d.listprice
	) as t1, 
	(
		SELECT count(*) as total_rows
		FROM residential d
		WHERE city = '$data[0]'
	) as t2
	WHERE 1
	AND t1.row_number in ( floor((total_rows+1)/2), floor((total_rows+2)/2) );";
	
	//echo $MedianSQL . '<br>';
	
	$resMedian = $conn->query($MedianSQL);
	
	if(!$resMedian)
	echo "MedianQ Query Failed". "<br>";

	$resultS = mysqli_fetch_array($resMedian, MYSQLI_NUM);
	$medianPrice = $resultS[0];

    //Insert into stats_price
	$InsertQuery = "INSERT INTO stat_city_price
	(
	`city`,
	`proptype`,
	`low`,
	`average`,
	`median`,
	`high`,
	`low_finished`,
	`avg_finished`,
	`median_finished`,
	`high_finished`,
	`date`)
     VALUES 
	('$data[0]', 'residential', '$minPrice', '$avgPrice', '$medianPrice', '$maxPrice',
	 '$minPrice', '$avgPrice', '$medianPrice', '$maxPrice', now())";
	
	//echo $InsertQuery . '<br>';
		
	$resi = $conn->query($InsertQuery);
	if(!$resi)
		echo "Insert Query Failed or Record aleady present" . "<br>";  
	}

$conn->close();
echo "Finished!" . '<br> <br>'; 
