<properties 
	pageTitle="Connect to SQL Database by using NodeJS with msnodesql on Windows" 
	description="Give a code sample you can use to connect to Azure SQL Database."
	services="sql-database" 
	documentationCenter="" 
	authors="meet-bhagdev" 
	manager="jeffreyg" 
	editor=""/>


<tags 
	ms.service="sql-database" 
	ms.workload="sql-database" 
	ms.tgt_pltfrm="na" 
	ms.devlang="nodejs" 
	ms.topic="article" 
	ms.date="04/13/2015" 
	ms.author="mebha"/>


# Connect to SQL Database by using NodeJS with msnodesql on Windows


## Requirements


You might already have some of the following required installations. ??


-  Node.js – [Version 0.8.9 (32 bit version)](http://blog.nodejs.org/2012/09/11/node-v0-8-9-stable/).  Make sure you download the x86 version and not the x64 version.
- [Python 2.7.6](https://www.python.org/download/releases/2.7.6/).
- [Visual C++ 2010](https://app.vssps.visualstudio.com/profile/review?download=true&family=VisualStudioCExpress&release=VisualStudio2010&type=web&slcid=0x409&context=eyJwZSI6MSwicGMiOjEsImljIjoxLCJhbyI6MCwiYW0iOjEsIm9wIjpudWxsLCJhZCI6bnVsbCwiZmEiOjAsImF1IjpudWxsLCJjdiI6OTY4OTg2MzU1LCJmcyI6MCwic3UiOjAsImVyIjoxfQ2) - the Express edition is freely available from Microsoft.
- SQL Server Native Client 11.0 - available as Microsoft SQL Server 2012 Native Client found in the [SQL Server 2012 Feature Pack](http://www.microsoft.com/en-us/download/details.aspx?id=29065).



## Install the required modules
Run cmd.exe as administrator. Navigate to your directory and enter the following commands:


1. npm install msnodesql
2. npm install -g node-gyp


Once node-gyp is installed, run the following commands inside the *YourProjectDirectory* > node_modules > msnodesql directory:


1. node-gyp configure 
2. node-gyp build


You should now see a build folder inside msnodel. Navigate to build > release. Copy the sqlserver.node file and paste it in the msnodesql > lib folder. Replace the old file if needed.


## Connect to your SQL-DB


	var http = require('http');
	var result = ""
	var sql = require('msnodesql');
	var driver = 'SQL Server Native Client 11.0';
	var server = 'tcp:fejcz4m54q.database.windows.net';
	var user = 'meet_bhagdev';
	var pwd = '*********';
	var database = 'meet_bhagdev';
	var useTrustedConnection = false;
	
	var conn_str = "Driver={" + driver + "};Server=" + server + ";"
		+ (useTrustedConnection == true ? "Trusted_Connection={Yes};" : "UID="
		+ user + ";PWD=" + pwd + ";") + "Database={" + database + "};";
	sql.open(conn_str, function (err, conn) {
		if (err) {
    		console.log("Error opening the connection!");
    		return;
		}


## Create your first table on the cloud


	conn.queryRaw("IF OBJECT_ID('votes1', 'U') IS NOT NULL DROP TABLE votes1; CREATE TABLE votes1 ( name VARCHAR(100), value INT NOT NULL, PRIMARY KEY(name);)", function (err, results) 
	{
		if (err) 
		{
			console.log("Error running query1!");
			return;
		}
	});


## Insert values in your table


	conn.queryRaw("INSERT INTO votes1 (name, value) VALUES ('Python','1'),('NodeJS','1'),('C#','1'); ", function (err, results) {
		if (err) {
			console.log("Error running query2!");
			return;
		}
	});


## Delete values in your table


	conn.queryRaw("DELETE FROM votes1 WHERE value = 2 ;", function (err, results)
	{
		if (err) {
			console.log("Error running query2!");
			return;
		}
	});


## Select values from your table


	conn.queryRaw("SELECT * FROM votes1", function (err, results) {
		if (err) {
		console.log("Error running query!");
		return;
		}
		for (var i = 0; i < results.rows.length; i++) {
			result = result + results.rows[i][0] + " : " + results.rows[i][1] + " votes";
			result+= "\n";
		}
	});


## Transactions


> [AZURE.NOTE] The method conn.beginTransactions will not work in SQL Database. Please follow the code example to perform transactions in SQL Database.


	conn.query("BEGIN TRANSACTION", function (err, results) {
		if (err) {
			console.log("Error running query5!");
			return;
		}
	});
	conn.queryRaw("DELETE FROM votes1 WHERE value = 12 ; ", function (err, results) {
		if (err) {
			console.log("Error running query6!");
			return;
		}
	});
	conn.queryRaw("ROLLBACK TRANSACTION; ", function (err, results) {
		if (err) {
			console.log("Error running query7!");
			return;
		}
	});


## Stored procedures


> [AZURE.NOTE] For this code sample to work, you must first have or create a stored procedure that inputs no parameters. You can create a stored procedure with a tool such as SSMS.


	conn.query("exec NameOfStoredProcedure", function (err, results) {
		if (err) {
			console.log("Error running query5!");
			return;
		}
	});


## Configure your app


	http.createServer(function (req, res) {
		res.writeHead(200, {'Content-Type': 'text/html'});
		res.end('<h1> Sample Node JS applicaiton </h1><pre>' + result);
	}).listen(1337, "127.0.0.1");
	console.log('Server running at http://127.0.0.1:1337/');

