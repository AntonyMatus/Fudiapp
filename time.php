<?php 
date_default_timezone_set("America/Chihuahua");
$open = 0;
$time  = date('H:i:s');
$openTime  = date("H:i:s",strtotime("18:00:00"));
$closeTime = date("H:i:s",strtotime("2:00:00"));


echo $time.'=> '.$openTime.'<br />';

if($time >= $openTime )
{
    if ($closeTime > "12:00:00") {
        
        // El horario es de madrugada
        if ($time <= $closeTime) {
            
            if($open == 0)
            {
                $open = true;
            }
            else
            {
                $open = false;
            }
        }else {
            $open = false;
        }
    }else {
        if ($time >= $closeTime) {
            if($open == 0)
            {
                $open = true;
            }
            else
            {
                $open = false;
            }
        }else {
            $open = false;
        }
    }
    
}
else
{
    if ($time >= $closeTime) {
        $open = false;
    }else {
        $open = true;
    }
}


if ($open) {
    echo 'abierto';
}else {
    echo 'cerrado';
}

?>