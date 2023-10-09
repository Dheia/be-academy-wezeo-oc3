
<style>
    .records { grid-area: records; }
    .print_r { grid-area: print_r; }
    .arrivals { grid-area: arrivals; }

    .grid-container {
        display: grid;
        grid-template-areas:
            'records print_r'
            'records arrivals';
        gap: 30px;
        padding: 10px;
        }
</style>
<html>
<body>

    <h1>Logger 2</h1>

    <form method="post" action="index.php">
        
        <label for="name_input_field">Name: </label>
        <input type="text" name="name_field" id="name_input_field" pattern="[A-Za-z0-9 ]{1,}" title="At least 1 character" required="required">
        <label for="msg_field">   Message: </label>
        <input type="text" name="msg_field" id="msg_field" pattern="[a-z]*[A-Z]*[\ .]*" title="Only letters, numbers, spaces and dots">
        <input type="submit" value="submit" name="clockin">

    </form>

</body>
<html>

<?php

    //----------- logic ---------------

    checkJsonFile(Cst::$STUDENT_LOG_FILE, "{}"); // studenti.json
    checkJsonFile(Cst::$ARRIVALS_FILE, "[]"); // prichody.json

    $arrivalsLogger = new ArrivalsLogger(Cst::$ARRIVALS_FILE);

    // if the website wasnt loaded for the first time
    if (isset($_POST[Cst::$SUBMIT_BTN_NAME]) or isset($_GET["meno"])) 
    {
        // if the clock-in button was pressed
        if (isset($_POST[Cst::$SUBMIT_BTN_NAME])) 
        {
            $hours = intval(date("H"));
            if (!isClockinPossible($hours))
            {
                die("Cant clock in between " . $DIE_MIN_HRS . " and " . $DIE_MAX_HRS);
            }

            StudentLogger::appendLog(Cst::$STUDENT_LOG_FILE, $_POST[Cst::$NAME_FIELD_NAME], $_POST[Cst::$MESSAGE_FIELD_NAME]);
        }
        // if the name was sent as part of the url (?meno=john)
        else if (isset($_GET["meno"])) 
        {
            StudentLogger::appendLog(Cst::$STUDENT_LOG_FILE, $_GET["meno"], "");
        }

        $arrivalsLogger -> appendArrival();
        $arrivalsLogger -> tagLateClockins(Cst::$MAX_HRS, Cst::$MAX_MINUTES);

    }
    printLogs($arrivalsLogger);

    //-------------- classes and functions ---------------

    class StudentLogger 
    {

        public static function appendLog($filename, $name, $message) {

            $jsonStr = file_get_contents($filename);
            $studentLogArr = json_decode($jsonStr, true); 
            
            // the studentLog is an associative array with
            // the keys being names of students, where each key
            // maps to a string that contains every 
            // arrival date+time and message of that student

            if (key_exists($name, $studentLogArr))
            {
                $str = $studentLogArr[$name];

                $clockinCount = StudentLogger::getClockinCount($str);
                $clockinCount++;
                $digitCount = strlen((string)$clockinCount);
                $str = substr($str, $digitCount);

                $str = $clockinCount . $str . date("d.m.Y-H:i:s: ") . $message . "_";
                $studentLogArr[$name] = $str;
            }
            else
            {
                $studentLogArr[$name] = "1 " . date("d.m.Y-H:i:s: ") . $message . "_";
            }

            $newJsonStr = json_encode($studentLogArr);

            file_put_contents(Cst::$STUDENT_LOG_FILE, $newJsonStr);

        }

        public static function getClockinCount($log) 
        {
            // the clockin count for the given log (a single value in the associative array)
            // is at the start of the string and is separated from the rest of the log
            // by a space.

            $digitCount = iconv_strpos($log, " ");
            $clockinCount = intval(substr($log, 0, $digitCount));
            return $clockinCount;
        }

        public static function chopLog($log) 
        {
            // returns an array containing every clockin time&date + message for
            // the given log (a single value in the associative array) 

            // first remove the clockin count at the beginning
            $digitCount = strlen((string)StudentLogger::getClockinCount($log));
            
            $truncated = substr($log, $digitCount+1);

            $arr = explode("_", $truncated);
            array_pop($arr); // last element is empty

            return $arr;
        }

    }

    class ArrivalsLogger
    {
        private $arrivalsFile;

        public function __construct($arrivalsFile) {
            $this->arrivalsFile = $arrivalsFile;
        }

        public function appendArrival() {
            $jsonStr = file_get_contents($this -> arrivalsFile);
            $arrivalsArr = json_decode($jsonStr, false); 
            
            array_push($arrivalsArr, date("d.m.Y-H:i:s"));

            $newJsonStr = json_encode($arrivalsArr);
            file_put_contents($this->arrivalsFile, $newJsonStr);
        }

        public function tagLateClockins($max_hrs, $max_mins) {
            $jsonStr = file_get_contents($this -> arrivalsFile);
            $arrivalsArr = json_decode($jsonStr, false); 
            
            foreach ($arrivalsArr as &$arrival) 
            {
                $isLate = $this -> isLate($arrival, $max_hrs, $max_mins);
                if ($isLate and !str_contains($arrival, "meskanie")) 
                {
                    $arrival = $arrival . " meskanie";
                }
            }

            $newJsonStr = json_encode($arrivalsArr);
            file_put_contents($this->arrivalsFile, $newJsonStr);
        }

        private function isLate($dateTimeStr, $max_hrs, $max_mins) {
            
            // the hours and minutes are at indices 11 and 14 in the string
            $hours = intval(substr($dateTimeStr, 11, 2));
            $minutes = intval(substr($dateTimeStr, 14, 2));

            $clockinMinutes = ($hours * 60) + $minutes;
            $isLate = $clockinMinutes > ($max_hrs * 60 + $max_mins);
            return $isLate;
        }

        public function printLogs() {
            $jsonStr = file_get_contents($this -> arrivalsFile);
            $arrivalsArr = json_decode($jsonStr, false); 

            echo print_r($arrivalsArr);
        }

    }

    class Cst {

        public static $SUBMIT_BTN_NAME = "clockin";
        public static $NAME_FIELD_NAME = "name_field";
        public static $MESSAGE_FIELD_NAME = "msg_field";
        public static $STUDENT_LOG_FILE = "studenti.json";
        public static $ARRIVALS_FILE = "prichody.json";
    
        // up to what time is the clock-in not considered being late
        public static $MAX_HRS = 8;
        public static $MAX_MINUTES = 00;
    
        // between what hours can the clock-in not be accepted (so the website dies)
        public static $DIE_MIN_HRS = 00;
        public static $DIE_MAX_HRS = 5; 

    }

    function printLogs($arrivalsInstance) 
    {
        global $STUDENT_LOG_FILE;
        $jsonStr = file_get_contents(Cst::$STUDENT_LOG_FILE);
        $studentLogArr = json_decode($jsonStr, true); 


        echo "<div class=\"grid-container\">";
        
        echo "<ul class=\"records\">";
        foreach ($studentLogArr as $name => $log) {
            
            $clockins = StudentLogger::chopLog($log);
            
            echo "<li style=\"margin: 5px; background: whitesmoke;\">";
            echo "<b>" . $name . "</b> - " . StudentLogger::getClockinCount($log) . " arrivals"; 
            echo "<ul>";
            
            // nested list of every individual clockin
            foreach ($clockins as $clockin) 
            {
                echo "<li>";
                echo $clockin;
                echo "</li>";
            }

            echo "</ul>";
            echo "</li>";
        }
        echo "</ul>";

        echo "<div class=\"print_r\">";
        echo "<h2>print_r(studenti.json):</h2>";
        echo print_r($studentLogArr);
        echo "</div>";

        echo "<div class=\"arrivals\">";
        echo "<h2>print_r(prichody.json):</h2>";
        $arrivalsInstance -> printLogs();
        echo "</div>";

        echo "</div>";
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

    function checkJsonFile($name, $emptyString) {

        if (!file_exists($name)) 
        {    
            // this creates a file if it doesnt exists
            $file = fopen($name, "w");
            fclose($file);  
            file_put_contents($name, $emptyString);
        }
    }
?>  