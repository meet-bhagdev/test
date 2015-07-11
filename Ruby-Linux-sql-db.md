<properties 
	pageTitle="Connect to SQL Database by using Ruby with TinyTDS on Ubuntu" 
	description="Give a code sample you can use to connect to Azure SQL Database."
	services="sql-database" 
	documentationCenter="" 
	authors="ajlam" 
	manager="jeffreyg" 
	editor=""/>


<tags 
	ms.service="sql-database" 
	ms.workload="sql-database" 
	ms.tgt_pltfrm="na" 
	ms.devlang="ruby" 
	ms.topic="article" 
	ms.date="07/10/2015" 
	ms.author="andrela"/>


# Using SQL Database with Ruby on Ubuntu




## Install the required modules
Open your terminal and install FreeTDS if you do not have it on your machine
	
    sudo apt-get --assume-yes update   
    sudo apt-get --assume-yes install freetds-dev freetds-bin


Once your machine is configured with FreeTDS, install Ruby if you do not have it on your machine
    
    sudo apt-get install libgdbm-dev libncurses5-dev automake libtool bison libffi-dev 
    curl -L https://get.rvm.io | bash -s stable

If you have any issues with signatures, run the following command

    command curl -sSL https://rvm.io/mpapis.asc | gph --import -  

Otherwise,

    source ~/.rvm/scripts/rvm 
    rvm install 2.1.2 
    rvm use 2.1.2 --default 
    ruby -v 

Please ensure that you are running version 2.1.2. 

Now, install TinyTDS

    gem install tiny_tds

## Create a database and retrieve your connection string
 
See the [getting started page](http://example.com/) to learn how to create a sample database and retrieve your connection string. It is important you follow the guide to create an **AdventureWorks database template**. The examples shown below will only work with the **AdventureWorks schema**. 
 

## Connect to your SQL Database
The [TinyTDS::Client](https://github.com/rails-sqlserver/tiny_tds) function is used to connect to SQL Database.

    require 'tiny_tds'     
    
    print 'test'     
    
    client = TinyTds::Client.new username: 'yourusername@yourserver', password: 'yourpassword', host: 'yourserver.database.windows.net', port: 1433, database: 'AdventureWorks', azure:true 

## Execute a query and retrieve the result set
The [TinyTds::Result](https://github.com/rails-sqlserver/tiny_tds) function is used to retrieve a result set from a query against SQL Database. This function accepts a query and returns a result set, iterated over by using [result.each do |row|](https://github.com/rails-sqlserver/tiny_tds).

	require 'tiny_tds'     
    
    print 'test'     
    
    client = TinyTds::Client.new username: 'yourusername@yourserver', password: 'yourpassword', host: 'yourserver.database.windows.net', port: 1433, database: 'AdventureWorks', azure:true 
        
    results = client.execute("select * from SalesLT.Product") 
        
    results.each do |row| 
        
    puts row 
       
    end 
    

## Inserting a row, passing parameters, and retrieving the generated primary key value
In SQL Database the [IDENTITY](https://msdn.microsoft.com/library/ms186775.aspx) property and the [SEQUENCE](https://msdn.microsoft.com/library/ff878058.aspx) object can be used to auto-generate [primary key values](https://msdn.microsoft.com/library/ms179610.aspx). 

To align with the SQL Server [datetime](https://msdn.microsoft.com/en-us/library/ms187819.aspx) format, use the [strftime](http://ruby-doc.org/core-2.2.0/Time.html#method-i-strftime) function to cast the to the corresponding datetime format. 

    require 'tiny_tds'     
    require 'date'

    t = Time.now
    curr_date = t.strftime("%Y-%m-%d %H:%M:%S.%L")
    
	print 'test'     
    
    client = TinyTds::Client.new username: 'yourusername@yourserver', password: 'yourpassword', host: 'yourserver.database.windows.net', port: 1433, database: 'AdventureWorks', azure:true 
          
    results = client.execute("SET ANSI_NULLS ON")
    results = client.execute("SET CURSOR_CLOSE_ON_COMMIT OFF")
    results = client.execute("SET ANSI_NULL_DFLT_ON ON")
    results = client.execute("SET IMPLICIT_TRANSACTIONS OFF")
    results = client.execute("SET ANSI_PADDING ON")
    results = client.execute("SET QUOTED_IDENTIFIER ON")
    results = client.execute("SET ANSI_WARNINGS ON")
    results = client.execute("SET CONCAT_NULL_YIELDS_NULL ON")
    
    results = client.execute("INSERT SalesLT.Product (Name, ProductNumber, StandardCost, ListPrice, SellStartDate) OUTPUT INSERTED.ProductID VALUES ('Essadaadxpress', 'fadsasdsadsafsa', 0, 0, '#{curr_date}' )")
    
    results.each do |row| 
    
    puts row
    
    end 
