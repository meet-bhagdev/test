<properties
	pageTitle="PHP on Windows to SQL DB | Microsoft Azure"
	description="Presents a sample PHP program that connects to Azure SQL Database from a Windows client, and provides links to the necessary software components needed by the client."
	services="sql-database"
	documentationCenter=""
	authors="meet-bhagdev"
	manager="jeffreyg"
	editor=""/>


<tags
	ms.service="sql-database"
	ms.workload="data-management"
	ms.tgt_pltfrm="na"
	ms.devlang="php"
	ms.topic="article"
	ms.date="07/20/2015"
	ms.author="mebha"/>


# Connect to SQL Database by using PHP on Windows


[AZURE.INCLUDE [sql-database-develop-includes-selector-language-platform-depth](../../includes/sql-database-develop-includes-selector-language-platform-depth.md)]


This topic illustrates how you can connect to Azure SQL Database from a client application written in PHP that runs on Windows.


[AZURE.INCLUDE [sql-database-develop-includes-prerequisites-php-windows](../../includes/sql-database-develop-includes-prerequisites-php-windows.md)]


## Create a database and retrieve your connection string


See the [Getting Started Topic](sql-database-get-started.md) to learn how to create a sample database and retrieve your connection string. It is important you follow the guide to create an **AdventureWorks database template**. The samples shown below only work with the **AdventureWorks schema**. 


## Connect to your SQL Database database


This **OpenConnection** function is called near the top in all of the functions that follow.


	function OpenConnection()
	{
		try
		{
			$serverName = "tcp:myserver.database.windows.net,1433";
			$connectionOptions = array("Database"=>"AdventureWorks",
				"Uid"=>"MyUser", "PWD"=>"MyPassword");
			$conn = sqlsrv_connect($serverName, $connectionOptions);
			if($conn == false)
				die(FormatErrors(sqlsrv_errors()));
		}
		catch(Exception $e)
		{
			echo("Error!");
		}
	}


## Execute a query and retrieve the result set

The [sqlsrv_query()](http://php.net/manual/en/function.sqlsrv-query.php) function can be used to retrieve a result set from a query against SQL Database. This function essentially accepts any query and the connection object and returns a result set which can be iterated over with the use of [sqlsrv_fetch_array()](http://php.net/manual/en/function.sqlsrv-fetch-array.php).

	<?php
		// Variables to tune the retry logic.  
		$connectionTimeoutSeconds = 30;  // Default of 15 seconds is too short over the Internet, sometimes.
		$maxCountTriesConnectAndQuery = 3;  // You can adjust the various retry count values.
		$secondsBetweenRetries = 4;  // Simple retry strategy.
		$errNo = 0;
		$serverName = "tcp:yourdatabase.database.windows.net,1433";
		$connectionOptions = array("Database"=>"AdventureWorks",
		   "Uid"=>"yourusername", "PWD"=>"yourpassword", "LoginTimeout" => $connectionTimeoutSeconds);
		$conn;
		$errorArr = array();
		for ($cc = 1; $cc <= $maxCountTriesConnectAndQuery; $cc++)
		{
		    $errorArr = array();
		    $ctr = 0;
		    // [A.2] Connect, which proceeds to issue a query command. 
		    $conn = sqlsrv_connect($serverName, $connectionOptions);  
		    if( $conn == true)
		    {
		        echo "Connection was established"; 
		        echo "<br>";
		 
		        $tsql = "SELECT [CompanyName] FROM SalesLT.Customer";
		        $getProducts = sqlsrv_query($conn, $tsql);
		        if ($getProducts == FALSE)
		            die(FormatErrors(sqlsrv_errors()));
		        $productCount = 0;
		        $ctr = 0;
		        while($row = sqlsrv_fetch_array($getProducts, SQLSRV_FETCH_ASSOC))
		        {   
		            $ctr++;
		            echo($row['CompanyName']);
		            echo("<br/>");
		            $productCount++;
		            if($ctr>10)
		                break;
		        }
		        sqlsrv_free_stmt($getProducts);
		        break;
		    }
		    // Adds any the error codes from the SQL Exception to an array.
		    else {  
		        if( ($errors = sqlsrv_errors() ) != null) {
		            foreach( $errors as $error ) {
		                $errorArr[$ctr] = $error['code'];
		                $ctr = $ctr + 1;
		            }
		        }
		        $isTransientError = TRUE;
		        // [A.4] Check whether sqlExc.Number is on the whitelist of transients.
		        $isTransientError = IsTransientStatic($errorArr);
		        if ($isTransientError == TRUE)  // Is a static persistent error...
		        { 
		            echo("Persistent error suffered, SqlException.Number==". $errorArr[0].". Program Will terminate."); 
		            echo "<br>";
		            // [A.5] Either the connection attempt or the query command attempt suffered a persistent SqlException.
		            // Break the loop, let the hopeless program end.
		            exit(0);
		        }
		        // [A.6] The SqlException identified a transient error from an attempt to issue a query command.
		        // So let this method reloop and try again. However, we recommend that the new query
		        // attempt should start at the beginning and establish a new connection.
		        if ($cc >= $maxCountTriesConnectAndQuery)
		        {
		            echo "Transient errors suffered in too many retries - " . $cc ." Program will terminate.";
		            echo "<br>";
		            exit(0);
		        }
		        echo("Transient error encountered.  SqlException.Number==". $errorArr[0]. " . Program might retry by itself.");  
		        echo "<br>";
		        echo $cc . " Attempts so far. Might retry.";
		        echo "<br>";
		        // A very simple retry strategy, a brief pause before looping. This could be changed to exponential if you want.
		        sleep(1*$secondsBetweenRetries);
		    }
		    // [A.3] All has gone well, so let the program end.
		}
		function IsTransientStatic($errorArr) {
		    $arrayOfTransientErrorNumbers = array(4060, 10928, 10929, 40197, 40501, 40613);
		    $result = array_intersect($arrayOfTransientErrorNumber, $errorArr);
		    $count = count($result);
		    if($count >= 0) //change to > 0 later.
		        return TRUE;
		}
	?>
	

## Insert a row, pass parameters, and retrieve the generated primary key


In SQL Database the [IDENTITY](https://msdn.microsoft.com/library/ms186775.aspx) property and the [SEQUENCE](https://msdn.microsoft.com/library/ff878058.aspx) object can be used to auto-generate [primary key](https://msdn.microsoft.com/library/ms179610.aspx) values. 


	function InsertData()
	{
		try
		{
			$conn = OpenConnection();

			$tsql = "INSERT SalesLT.Product (Name, ProductNumber, StandardCost, ListPrice, SellStartDate) OUTPUT 			INSERTED.ProductID VALUES ('SQL Server 1', 'SQL Server 2', 0, 0, getdate())";
			//Insert query
			$insertReview = sqlsrv_query($conn, $tsql);
			if($insertReview == FALSE)
				die(FormatErrors( sqlsrv_errors()));
			echo "Product Key inserted is :";	
			while($row = sqlsrv_fetch_array($insertReview, SQLSRV_FETCH_ASSOC))
			{   
				echo($row['ProductID']);
			}
			sqlsrv_free_stmt($insertReview);
			sqlsrv_close($conn);
		}
		catch(Exception $e)
		{
			echo("Error!");
		}
	}

## Transactions


This code example demonstrates the use of transactions in which you:

-Begin a transaction

-Insert a row of data, Update another row of data

-Commit your transaction if the insert and update were successful and rollback the transaction if one of them was not


	function Transactions()
	{
		try
		{
			$conn = OpenConnection();

			if (sqlsrv_begin_transaction($conn) == FALSE)
				die(FormatErrors(sqlsrv_errors()));

			$tsql1 = "INSERT INTO SalesLT.SalesOrderDetail (SalesOrderID,OrderQty,ProductID,UnitPrice) 
			VALUES (71774, 22, 709, 33)";
			$stmt1 = sqlsrv_query($conn, $tsql1);
			
			/* Set up and execute the second query. */
			$tsql2 = "UPDATE SalesLT.SalesOrderDetail SET OrderQty = (OrderQty + 1) WHERE ProductID = 709";
			$stmt2 = sqlsrv_query( $conn, $tsql2);
			
			/* If both queries were successful, commit the transaction. */
			/* Otherwise, rollback the transaction. */
			if($stmt1 && $stmt2)
			{
			       sqlsrv_commit($conn);
			       echo("Transaction was commited");
			}
			else
			{
			    sqlsrv_rollback($conn);
			    echo "Transaction was rolled back.\n";
			}
			/* Free statement and connection resources. */
			sqlsrv_free_stmt( $stmt1);
			sqlsrv_free_stmt( $stmt2);
		}
		catch(Exception $e)
		{
			echo("Error!");
		}
	}


## Further reading


For more information regarding PHP installation and usage, see [Accessing SQL Server Databases with PHP](http://technet.microsoft.com/library/cc793139.aspx).

 
