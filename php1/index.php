
<html>
<body>

    <h1>Logger</h1>
    <br>

    <form method="post" action="index.php">
        <input type="submit" value="clock in" name="clockin">
    </form>

</body>
<html>

<?php

    //---------- constants -----------

    define("SUBMIT_BTN_NAME", "clockin");

    // up to what time is the clock-in not considered being late
    $MAX_HRS = 8;
    $MAX_MINUTES = 00;

    // between what hours can the clock-in not be accepted (so the website dies)
    $DIE_MIN_HRS = 20;
    $DIE_MAX_HRS = 0; 

    //----------- logic ---------------

    checkFileExistence();

    // if the clock-in button was pressed
    if (isset($_POST[SUBMIT_BTN_NAME])) 
    {
        $hours = intval(date("H"));
        $minutes = intval(date("i"));

        if (!isClockinPossible($hours))
        {
            die("Cant clock in between " . $DIE_MIN_HRS . " and " . $DIE_MAX_HRS);
        }

        $clockinMinutes = ($hours * 60) + $minutes;
        $isLate = $clockinMinutes > ($MAX_HRS * 60 + $MAX_MINUTES);
        
        appendLog($isLate);
    }

    printLogs();

    //-------------- functions ---------------

    function printLogs() 
    {
        $file = fopen("log.txt", "r");
        while (!feof($file)) 
        {
            echo fgets($file) . "<br>";
        }
        fclose($file);
    }

    function appendLog($isLate) 
    {
        $file = fopen("log.txt", "a+");
        fwrite($file, date("d.m.Y, H:i:s"));

        if ($isLate)
        {
            fwrite($file, " meskanie");
        }

        fwrite($file, "\n");
        fclose($file);
    }
    
    function isClockinPossible($clockinHour) {
        global $DIE_MIN_HRS;
        global $DIE_MAX_HRS;

        // if the range does not cross midnight, so for example 18 - 22:00
        if ($DIE_MAX_HRS >= $DIE_MIN_HRS) 
        {
            if ($DIE_MIN_HRS <= $clockinHour and $clockinHour <= $DIE_MAX_HRS) 
            {
                return false;
            }
            return true;
        }

        // if the range includes midnight, for example 22 to 2:00
        else 
        {
            // the way we check is simple: we split the clock into two intervals:
            // -min_hour til almost midnight
            // -midnight til max_hour
            // and then, we check the clockin hour in the appropriate interval

            if ($DIE_MIN_HRS <= $clockinHour) 
            {
                return false;
            }
            else if (0 <= $clockinHour and $clockinHour <= $DIE_MAX_HRS)
            {
                return false;
            }
            return true;
        }

    }

    function checkFileExistence() {
        
        if (!file_exists("log.txt")) 
        {    
            // this creates a file if it doesnt exists
            $file = fopen("log.txt", "w");
            fclose($file);  
        }
    }
?>