
<?php

// contains various constants used throughout code
class Cst 
{
    public const SUBMIT_BTN_NAME = "clockin";
    public const NAME_FIELD_NAME = "name_field";
    public const MESSAGE_FIELD_NAME = "msg_field";
    public const STUDENT_LOG_FILE = "studenti.json";
    public const ARRIVALS_FILE = "prichody.json";

    // up to what time is the clock-in not considered being late
    public const MAX_HRS = 8;
   public const MAX_MINUTES = 00;
    // between what hours can the clock-in not be accepted (so the website dies)
    public const DIE_MIN_HRS = 1;
    public const DIE_MAX_HRS = 5; 

}

class Clockin
{
    public $time;
    public $date;
    public $message;

    public function __construct($time, $date, $message) {
        $this->time = $time;
        $this->date = $date;
        $this->message = $message;
    }
}

// represents a single student's clockins.
// Note that this class contains no $name, therefore by just looking at
// this object, one doesnt know to which student it belongs.
// The reason for that is:
// All StudentLog instances are stored in an associative array, and the
// keys of that array are all unique student names.
class StudentLog
{
    public $clockinCount;
    public $clockinArr; 

    public function __construct($name, $firstClockin) {
        $this->clockinCount = 1;
        $this->clockinArr = array($firstClockin);
    }
}

class StudentLogger 
{

    public static function appendLog($filename, $name, $message) {

        // the student log file contains an associative array,
        // where every element key is a unique student name
        // and every corresponding value a StudentLog object
        $jsonStr = file_get_contents($filename);
        $studentLogArr = json_decode($jsonStr, true); 

        // the problem now is, that studentLogArr isnt an associative array
        // filled with objects. Rather, it is filled with associative arrays.
        // This is a quirk of json_decode, as unfortunately, objects
        // and associative arrays are represented in the exact same way in json.

        $newClockin = new Clockin(date("H:i:s"), date("d.m.Y"), $message);

        // if a student with the given name already exists, append a log 
        // to their clockins array
        if (key_exists($name, $studentLogArr))
        {
            $studentLog = $studentLogArr[$name];

            // studentLog is an associative array, lets convert it into an object
            $studentLog = assocArrayToObject($studentLog);

            $studentLog -> clockinCount++;
            array_push($studentLog->clockinArr, $newClockin);
            $studentLogArr[$name] = $studentLog;
        }
        // if a student with this name doesnt exist, create them
        else
        {
            $newStudentLog = new StudentLog($name, $newClockin);
            $studentLogArr[$name] = $newStudentLog;
        }

        $newJsonStr = json_encode($studentLogArr);
        file_put_contents(Cst::STUDENT_LOG_FILE, $newJsonStr);
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
        $arrivalsArr = json_decode($jsonStr); 
        
        array_push($arrivalsArr, date("d.m.Y H:i:s"));

        $newJsonStr = json_encode($arrivalsArr);
        file_put_contents($this->arrivalsFile, $newJsonStr);
    }

    public function tagLateClockins($max_hrs, $max_mins) {
        $jsonStr = file_get_contents($this -> arrivalsFile);
        $arrivalsArr = json_decode($jsonStr); 
        
        foreach ($arrivalsArr as &$arrival) 
        {
            if (str_contains($arrival, "meskanie")) 
            {
                continue;
            }
            
            // every arrival is in the format "DD.MM.YYYY HH:MM:SS"
            $isLate = $this -> isLate($arrival, $max_hrs, $max_mins);
            if ($isLate) 
            {
                $arrival = $arrival . " meskanie";
            }
        }

        $newJsonStr = json_encode($arrivalsArr);
        file_put_contents($this->arrivalsFile, $newJsonStr);
    }

    private function isLate($dateTimeStr, $max_hrs, $max_mins) {
        
        $timestamp = strtotime($dateTimeStr); 
        $hours = (int) date("H", $timestamp); 
        $minutes = (int) date("i", $timestamp); 

        $clockinMinutes = $hours * 60 + $minutes;
        $isLate = $clockinMinutes > ($max_hrs * 60 + $max_mins);
        return $isLate;
    }

    public function printLogs() {
        $jsonStr = file_get_contents($this -> arrivalsFile);
        $arrivalsArr = json_decode($jsonStr, false); 

        echo print_r($arrivalsArr);
    }

}
    
?>