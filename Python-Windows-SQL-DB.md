# Using SQL Database with Python on Windows

##Requirements

- [Python 2.7.6](https://www.python.org/download/releases/2.7.6/)

    

## Install the required modules

Navigate to **C:\ -> Python27 -> Scripts** and run the following command.
<br>

    pip install --allow-external pyodbc --allow-unverified pyodbc pyodbc


## Create a database and retrieve your connection string
 
See the [getting started page](http://example.com/) to learn how to create a sample database and retrieve your connection string. It is important you follow the guide to create an **AdventureWorks database template**. The examples shown below will only work with the **AdventureWorks schema**. 
 

## Connect to your SQL Database


	import pyodbc
	cnxn = pyodbc.connect('DRIVER={SQL Server};SERVER=tcp:yourservername.database.windows.net;DATABASE=AdventureWorks;UID=yourusername;PWD=yourpassword')
	cursor = cnxn.cursor())


## Execute a query and retrieve the result set

	import pyodbc
	cnxn = pyodbc.connect('DRIVER={SQL Server};SERVER=tcp:yourserver.database.windows.net;DATABASE=AdventureWorks;UID=yourusername;PWD=yourpassword')
	cursor = cnxn.cursor()
	cursor.execute('SELECT c.CustomerID, c.CompanyName,COUNT(soh.SalesOrderID) AS OrderCount FROM SalesLT.Customer AS c LEFT OUTER JOIN SalesLT.SalesOrderHeader AS soh ON c.CustomerID = soh.CustomerID GROUP BY c.CustomerID, c.CompanyName ORDER BY OrderCount DESC;')
	row = cursor.fetchone()
	while row:
	    print str(row[0]) + " " + str(row[1]) + " " + str(row[2]) 	
	    row = cursor.fetchone()

    

## Inserting a row, passing parameters, and retrieving the generated primary key value

	import pyodbc
	cnxn = pyodbc.connect('DRIVER={SQL Server};SERVER=tcp:yourserver.database.windows.net;DATABASE=AdventureWorks;UID=yourusername;PWD=yourpassword')
	cursor = cnxn.cursor()
	cursor.execute("INSERT SalesLT.Product (Name, ProductNumber, StandardCost, ListPrice, SellStartDate) OUTPUT INSERTED.ProductID VALUES ('SQL Server Express', 'SQLEXPRESS', 0, 0, CURRENT_TIMESTAMP)")
	row = cursor.fetchone()
	while row:
	    print "Inserted Product ID : " +str(row[0])
	    row = cursor.fetchone()


##Transactions

	import pyodbc
	cnxn = pyodbc.connect('DRIVER={SQL Server};SERVER=tcp:yourserver.database.windows.net;DATABASE=AdventureWorks;UID=yourusername;PWD=yourpassword')
	cursor = cnxn.cursor()
	cursor.execute("BEGIN TRANSACTION")
	cursor.execute("DELETE FROM test WHERE value = 1;")
	cnxn.rollback()


##Stored Procedures


> [AZURE.NOTE] We are using pyodbc to connect to SQL which currently has a limitation and does not allow the use of output parameters. Since we can't use output parameters at this point, you'll need to return results in a result set. Usually this means just ending your stored procedure with a SELECT statement. Additionally you will have to use a DBA tool such as SSMS to create your stored procedure. There is no way to create a stored procedure using pyodbc.

	import pyodbc
	cnxn = pyodbc.connect('DRIVER={SQL Server};SERVER=tcp:yourserver.database.windows.net;DATABASE=AdventureWorks;UID=yourusername;PWD=yourpassword')
	cursor = cnxn.cursor()
	cursor.execute("exec sp_dosomething(123, 'abc')")

   	
