## NodeJS SQL-DB

###Requirements

-  Node.js – Version 0.8.9(32 bit version). This can be found here.  Make sure you download the x86 version and not the x64 version as the driver will not work with 64bit version.
- Python 2.7.6. This can be downloaded from here.
- Visual C++ 2010 - the Express edition is freely available from Microsoft
- SQL Server Native Client 11.0 - available as Microsoft SQL Server 2012 Native Client found in the SQL Server 2012 Feature Pack
    


### Install the required modules
    Run cmd.exe as administrator. Navigate to your directory enter the following commands:
    1. 'npm install msnodesql'
    2. 'npm install -g node-gyp'
    
    Once node-gyp is installed, run the following inside the ProjectDirectory->node_modules->msnodesql directory  
	1. 'node-gyp configure' 
    2. 'node-gyp build' 
     
    You should now see a build folder inside msnodel. Naviate to build->release. Copy the sqlserver.node file and paste it in the msnodesql->lib folder. Replace the old file if needed.
    




###Connect to your SQL-DB     
	var http = require('http');
	var result = ""
	var sql = require('msnodesql');
	var driver = 'SQL Server Native Client 11.0';
	var server = 'tcp:fejcz4m54q.database.windows.net';
	var user = 'meet_bhagdev';
	var pwd = '*********';
	var database = 'meet_bhagdev';
	var useTrustedConnection = false;
    
	var conn_str = "Driver={" + driver + "};Server=" + server + ";" + (useTrustedConnection == true ? "Trusted_Connection={Yes};" : "UID=" + user + ";PWD=" + pwd + ";") + "Database={" + database + "};";
	sql.open(conn_str, function (err, conn) {
		if (err) {
    		console.log("Error opening the connection!");
    		return;
		}

###Create your first table on the cloud
	conn.queryRaw("IF OBJECT_ID('votes1', 'U') IS NOT NULL DROP TABLE votes1 CREATE TABLE votes1 ( name VARCHAR(100), value INT NOT NULL, PRIMARY KEY(name))", function (err, results) 
	{
   		if (err) 
		{
            console.log("Error running query1!");
            return;
    	}
	});

###Insert values in your table
	conn.queryRaw("INSERT INTO votes1 (name, value) VALUES ('Python','1'),('NodeJS','1'),('C#','1'); ", function (err, results) {
    	if (err) {
    	    console.log("Error running query2!");
    	    return;
   		 }
	});

###Delete values in your table
	conn.queryRaw("DELETE FROM votes1 WHERE value = 2 ;", function (err, results) {
    	if (err) {
    	    console.log("Error running query2!");
    	    return;
   		 }
	});


###Select values from your table
	conn.queryRaw("SELECT * FROM votes1", function (err, results) {
    	if (err) {
    	    console.log("Error running query!");
    	    return;
    	}
    	for (var i = 0; i < results.rows.length; i++) {
      
             result = result + results.rows[i][0] + " : " + results.rows[i][1] + " votes";
             result+= "\n";
        }
	});});

###Transactions
	//Note : conn.beginTransactions() will not work in SQL Azure. 
	//Please follow the following example to perform transactions in SQL Azure

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

###Stored Procedures
	//Note : Your Stored Procedure should be created using a DBA tool like SSMS
	//This code block will not work if you do not have a stored procedure created
	//You can call your stored procedure in the following way.

	conn.query("exec NameOfStoredProcedure", function (err, results) {
        if (err) {
            console.log("Error running query5!");
            return;
        }
    });
   	
###Configure your app

	http.createServer(function (req, res) {
	res.writeHead(200, {'Content-Type': 'text/html'});
    res.end('<h1> Sample Node JS applicaiton </h1><pre>' + result);
    }).listen(1337, "127.0.0.1");
	console.log('Server running at http://127.0.0.1:1337/');

