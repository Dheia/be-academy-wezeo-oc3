
<?php

require_once "classes.php";
require_once "functions.php";

// make sure the files exists and if needed, create new ones
checkJsonFile(Cst::$STUDENT_LOG_FILE, "{}"); // studenti.json
checkJsonFile(Cst::$ARRIVALS_FILE, "[]"); // prichody.json

$arrivalsLogger = new ArrivalsLogger(Cst::$ARRIVALS_FILE);

// if the website wasnt loaded for the first time
if (isset($_POST[Cst::$SUBMIT_BTN_NAME]) or isset($_GET["meno"])) 
{
    // if the clockin time is in a certain range, the website is supposed to die
    dieOnBadClockin();

    // if the clock-in button was pressed
    if (isset($_POST[Cst::$SUBMIT_BTN_NAME])) 
    {
        StudentLogger::appendLog(Cst::$STUDENT_LOG_FILE, $_POST[Cst::$NAME_FIELD_NAME], $_POST[Cst::$MESSAGE_FIELD_NAME]);
    }

    // if the name was sent as part of the url (?meno=jozko)
    else if (isset($_GET["meno"])) 
    {
        StudentLogger::appendLog(Cst::$STUDENT_LOG_FILE, $_GET["meno"], "");
    }

    $arrivalsLogger -> appendArrival();
    $arrivalsLogger -> tagLateClockins(Cst::$MAX_HRS, Cst::$MAX_MINUTES);

}

displayLogs($arrivalsLogger);

?>  