
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

        file_put_contents(Cst::STUDENT_LOG_FILE, $newJsonStr);

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
        
        array_push($arrivalsArr, date("d.m.Y H:i:s"));

        $newJsonStr = json_encode($arrivalsArr);
        file_put_contents($this->arrivalsFile, $newJsonStr);
    }

    public function tagLateClockins($max_hrs, $max_mins) {
        $jsonStr = file_get_contents($this -> arrivalsFile);
        $arrivalsArr = json_decode($jsonStr, false); 
        
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