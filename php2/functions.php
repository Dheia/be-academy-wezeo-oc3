
<?php

function isClockinPossible($clockinHour)
{
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

function dieOnBadClockin() 
{
    $hours = intval(date("H"));
    if (!isClockinPossible($hours))
    {
        die("Cant clock in between " . Cst::$DIE_MIN_HRS . " and " . Cst::$DIE_MAX_HRS);
    }
} 

function checkJsonFile($name, $emptyString)
{

    if (!file_exists($name)) 
    {    
        // this creates a file if it doesnt exists
        $file = fopen($name, "w");
        fclose($file);  
        file_put_contents($name, $emptyString);
    }
}

function displayLogs($arrivalsInstance) 
{
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

?>