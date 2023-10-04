
<html>
<body>

    <h1>Logger</h1>
    <br>

    <form method="post" action="index.php">
        
        <label for="name_input_field">Name: </label>
        <input type="text" name="name_field" id="name_input_field">
        <br><br>
        <label for="msg_field">Message: </label>
        <input type="text" name="msg_field" id="msg_field">

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

    checkFileExistence($STUDENT_LOG_FILE);

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
        
        checkFileExistence($STUDENT_LOG_FILE);

        StudentLogger::appendLog($STUDENT_LOG_FILE);
    }

    printLogs();

    //-------------- classes ---------------

    /*class StudentLog 
    {
        public $totalClockins;
        public $students;
    }
    class Student 
    {
        public $name;
        public $clockinCount;
        public $clockins; // array of all clockins
    }
    class Clockin 
    {
        public $dateAndTime;
        public $message;
    }*/
    class StudentLogger 
    {

        public static function appendLog($filename, $method="post") {
            
            $name = "";
            if ($method == "post") { $name = $_POST[NAME_FIELD_NAME]; }
            else if ($method == "get") { $name = $_GET["meno"]; }

            // read the file, decode it into a class, add an entry,
            // and then overwrite the file with this new content
            // (TODO: find a less wasteful way to do this)

            $jsonStr = file_get_contents($filename);
            echo $jsonStr . "<br>";
            $studentLog = json_decode($jsonStr, true); 
            var_dump($studentLog);
            echo $studentLog . "<br>"; // LEFT OFF 4.10: TOTO NEVYPISE - CHYBA PRI DEKODOVANI
            echo $name . "<br>";

            // the studentLog is an associative array with
            // the keys being names of students, where each key
            // maps to a string that contains every 
            // arrival date+time and message of that student

            if (key_exists($name, $studentLog))
            {
                $str = $studentLog[$name];
                $str = $str . "\n" . date("d.m.Y, H:i:s") . "\n" . $_POST[MESSAGE_FIELD_NAME];
            }
            else
            {
                $studentLog[$name] = date("d.m.Y, H:i:s") . "\n" . $_POST[MESSAGE_FIELD_NAME];
            }
            
            unset($_POST[NAME_FIELD_NAME]);
            unset($_POST[MESSAGE_FIELD_NAME]);
            unset($_GET["meno"]);

        }

    }

    function printLogs() 
    {
        $file = fopen("log.txt", "r");
        while (!feof($file)) 
        {
            echo fgets($file) . "<br>";
        }
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

    function checkFileExistence($filename) {

        if (!file_exists($filename)) 
        {    
            // this creates a file if it doesnt exists
            $file = fopen($filename, "w");
            fclose($file);  
        }
    }
?>