# Connect to your SQL Database using Python

##Requirements

- [Python 2.7.6](https://www.python.org/download/releases/2.7.6/)

    

## Install the required modules

### On Windows 


Open cmd.exe as administrator. Navigate to **C:\ -> Python27 -> Scripts** and run the following command.
<br>

    pip install --allow-external pyodbc --allow-unverified pyodbc pyodbc

### On Ubunutu

Open your terminal and enter the following commands.

	
    sudo apt-get install unixodbc unixodbc-dev freetds-dev freetds-bin tdsodbc
    pip install --allow-external pyodbc --allow-unverified pyodbc pyodbc


##Connect to your SQL-DB     
On **Windows**, create a new file called **test.py** and place it in the **C:\ -> Python27** directory. Paste the following code inside it.

On **Ubuntu**, you can place this place in the directory of your choice.

	import pyodbc
	server = 'tcp:yourservername.database.windows.net'
	database = 'yourdatabasename'
	username = 'yourusername'
	password = 'yourpassword'
	cnxn = pyodbc.connect('DRIVER={SQL Server};SERVER='+server+';DATABASE='+database+';UID='+username+';PWD=' + password)

	

##Create your first table on the cloud


	cursor = cnxn.cursor()
	cursor.execute("""
	IF OBJECT_ID('test', 'U') IS NOT NULL
	    DROP TABLE test
	CREATE TABLE test (
	    name VARCHAR(100),
	    value INT NOT NULL,
	    PRIMARY KEY(name)
	)
	""")

##Insert values in your table


	cursor.execute("INSERT INTO test (name, value)  VALUES ('NodeJS', 3), ( 'Python' , 1), ('C#', 9)")
	# you must call commit() to persist your data if you don't set autocommit to True
	cnxn.commit()

##Delete values in your table


	cursor.execute("DELETE FROM test WHERE value = 3;")
	cnxn.commit()



##Select values from your table

	cursor.execute("SELECT * FROM test")
	row = cursor.fetchone()
	while row:
    	print row
    	row = cursor.fetchone()

##Transactions


	cursor.execute("BEGIN TRANSACTION")
	cursor.execute("DELETE FROM test WHERE value = 1;")
	cnxn.rollback()


##Stored Procedures


> [AZURE.NOTE] We are using pyodbc to connect to SQL which currently has a limitation and does not allow the use of output parameters. Since we can't use output parameters at this point, you'll need to return results in a result set. Usually this means just ending your stored procedure with a SELECT statement.

	cursor.execute("exec sp_dosomething(123, 'abc')")

   	

