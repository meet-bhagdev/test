<?php
    $connectionTimeoutSeconds = 30;  // Default of 15 seconds is too short over the Internet, sometimes.
    $maxCountTriesConnectAndQuery = 3;  // You can adjust the various retry count values.
    $secondsBetweenRetries = 4;  // Simple retry strategy.
    $errNo = 0;
    $serverName = "tcp:yourserlver.database.windows.net,1433";
    $connectionOptions = array("Database"=>"yourdb",
        "Uid"=>"youruid", "PWD"=>"yourpassword", "LoginTimeout" => $connectionTimeoutSeconds);

    $conn;
    for ($cc = 1; $cc <= $maxCountTriesConnectAndQuery; $cc++)
    {    
        $errorArr = array();
        $ctr = 0;
        // [A.2] Connect, which proceeds to issue a query command.

        $conn = sqlsrv_connect($serverName, $connectionOptions);
        //Adds all the error codes from the SQL Exception to an array
        if( $conn === false ) {
            if( ($errors = sqlsrv_errors() ) != null) {
                foreach( $errors as $error ) {
                    $errorArr[$ctr] = $error[ 'code'];
                    $ctr = $ctr + 1;
                }
            }
            $isTransientError = TRUE;
            // [A.4] Check whether sqlExc.Number is on the whitelist of transients.
            $isTransientError = IsTransientStatic($errorArr);
            if ($isTransientError == FALSE)  // Is a persistent error...
            {
                echo("Persistent error suffered, SqlException.Number=={0}.  Will terminate");                       
                // [A.5] Either the connection attempt or the query command attempt suffered a persistent SqlException.
                // Break the loop, let the hopeless program end.
                exit(0);
            }
            // [A.6] The SqlException identified a transient error from an attempt to issue a query command.
            // So let this method reloop and try again. However, we recommend that the new query
            // attempt should start at the beginning and establish a new connection.    
            echo $cc;
            if ($cc >= $maxCountTriesConnectAndQuery)
            {
                echo "Transient errors suffered in too many retries - " . $cc ." Program will terminate.";
                exit(0);   
            }               
            echo("Transient error encountered.  SqlException.Number=={0}.  Program might retry by itself.");
            echo("\n");
            echo $cc . " Attempts so far. Might retry.";   
            // A very simple retry strategy, a brief pause before looping. This could be changed to exponential
            sleep(1*$secondsBetweenRetries);
        }   
        // [A.3] All has gone well, so let the program end.
        else 
        {
            echo "Connection was established";
            break; 
        }          
    }
    function IsTransientStatic($errorArr) {
        $arrayOfTransientErrorNumbers = array(4060, 10928, 10929, 40197, 40501, 40544, 40549,
                    40550, 40551, 40552, 40553, 40613);
        $result = array_intersect($arrayOfTransientErrorNumber, $errorArr);
        $count = count($result);
        if($count >= 0) //change to > 0 later. 
            return TRUE;
    } 
?>    
    
