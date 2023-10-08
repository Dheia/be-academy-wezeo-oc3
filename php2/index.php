
<html>
<body>

    <h1>Logger</h1>
    <br>

    <form method="post" action="index.php">
        
        <label for="name_input_field">Name: </label>
        <input type="text" name="name_field" id="name_input_field" pattern="[A-Za-z0-9 ]{1,}" title="At least 1 character" required="required">
        <br><br>
        <label for="msg_field">Message: </label>
        <input type="text" name="msg_field" id="msg_field" pattern="[a-z]*[A-Z]*[\ .]*" title="Only letters, numbers, spaces and dots">

        <br><br>        
        <input type="submit" value="submit" name="clockin">

        <!-- TODO: add malicious string checking and inability to add a newline-->

    </form>

</body>
<html>

<?php

    //---------- constants -----------

    define("SUBMIT_BTN_NAME", "clockin");
    define("NAME_FIELD_NAME", "name_field");
    define("MESSAGE_FIELD_NAME", "msg_field");
    $STUDENT_LOG_FILE = "studenti.json";

    // up to what time is the clock-in not considered being late
    $MAX_HRS = 8;
    $MAX_MINUTES = 00;

    // between what hours can the clock-in not be accepted (so the website dies)
    $DIE_MIN_HRS = 23;
    $DIE_MAX_HRS = 5; 

    //----------- logic ---------------

    checkStudentLog();

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
        
        checkStudentLog();

        StudentLogger::appendLog($STUDENT_LOG_FILE, $_POST[NAME_FIELD_NAME], $_POST[MESSAGE_FIELD_NAME]);
    }
    // if the name was sent as part of the url (?meno=john)
    else if (isset($_GET["meno"])) 
    {
        StudentLogger::appendLog($STUDENT_LOG_FILE, $_GET["meno"], "");
    }

    printLogs();

    //-------------- classes ---------------

    class StudentLogger 
    {

        public static function appendLog($filename, $name, $message) {
            
            global $STUDENT_LOG_FILE;

            // read the file, decode it into an associative array, add an entry,
            // and then overwrite the file with this new content
            // (TODO: find a less wasteful way to do this)

            $jsonStr = file_get_contents($filename);
            $studentLogArr = json_decode($jsonStr, true); 
            
            // the studentLog is an associative array with
            // the keys being names of students, where each key
            // maps to a string that contains every 
            // arrival date+time and message of that student

            if (key_exists($name, $studentLogArr))
            {
                $str = $studentLogArr[$name];
                $str = $str . date("d.m.Y-H:i:s:") . $message . "_";
                $studentLogArr[$name] = $str;
            }
            else
            {
                $studentLogArr[$name] = date("d.m.Y-H:i:s:") . $message . "_";
            }

            $newJsonStr = json_encode($studentLogArr);

            file_put_contents($STUDENT_LOG_FILE, $newJsonStr);

        }

    }

    function printLogs() 
    {
        global $STUDENT_LOG_FILE;
        $jsonStr = file_get_contents($STUDENT_LOG_FILE);
        $studentLogArr = json_decode($jsonStr, true); 
        print_r($studentLogArr);
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

    function checkStudentLog() {

        global $STUDENT_LOG_FILE;

        if (!file_exists($STUDENT_LOG_FILE)) 
        {    
            // this creates a file if it doesnt exists
            $file = fopen($STUDENT_LOG_FILE, "w");
            fclose($file);  
            file_put_contents($STUDENT_LOG_FILE, "{}");
        }
    }
?>  