<?php
    //This php file work is to fill the chart

    include "Connection.php";

    //initialize the variables
    $jsonData= [""];
    $From= '';
    $FromTotal= '';
    $To= '';
    $json = '';
    $dateDifference = '';
    $To2 = '';
    $interval = '';

    $emptyFrom= '';

    $CheckTotal = $_GET['Total'];
    $CheckRegister = $_GET['Register'];
    $CheckApproved = $_GET['Approved'];
    $CheckTested = $_GET['Tested'];
    $CheckImplemented = $_GET['Implemented'];
    $CheckDiscarded = $_GET['Discarded'];
    $FilterDate = $_GET['FilterDate'];
    $FilterDept = $_GET['FilterDept'];

    //Check if get From is empty
    if(!$_GET['From']==null) {

        $From = $_GET['From'];

        $emptyFrom= $From;
    }
    //Check if get To is empty
    if(!$_GET['To']==null){

        $To = $_GET['To'];

    }

    //If From is empty, it fill be autofilled with the date 6 months before the actual date

    if($From==''){

        $sixMonthBefore = strtotime(date("Y-m-d") . '-6 months');
        $From = date("Y-m-d", $sixMonthBefore);

        $emptyFrom= $From;
    }
    //If To is empty, it will be autofilled with the actual date
    if($To==''){

        $OneDayAfter = strtotime(date("Y-m-d") . '+1 day');
        $To = date("Y-m-d", $OneDayAfter);

    }

        $days = (abs(strtotime($From) - strtotime($To))) / (60 * 60 * 24);
        //this if elseif block is used to autofilter the chart

            if ($FilterDate == 'Years') {

                $sql = "SELECT DATE_FORMAT(timestamp, \"%Y-%m-%d\") AS Years FROM ost_thread_event WHERE timestamp BETWEEN '$From' AND '$To' GROUP BY DATE_FORMAT(timestamp, \"%Y\")";

            } elseif ($FilterDate == 'Months') {

                $sql = "SELECT DATE_FORMAT(timestamp, \"%Y-%m\") AS Month FROM ost_thread_event WHERE timestamp BETWEEN '$From' AND '$To' GROUP BY DATE_FORMAT(timestamp, \"%Y-%m\")";

            } elseif ($FilterDate == 'Days') {

                $sql = "SELECT DATE_FORMAT(timestamp, \"%Y-%m-%d\") AS Days FROM ost_thread_event WHERE timestamp BETWEEN '$From' AND '$To' GROUP BY DATE_FORMAT(timestamp, \"%Y-%m-%d\")";

            } elseif ($days >= 1095) {

                $sql = "SELECT DATE_FORMAT(timestamp, \"%Y-%m-%d\") AS Years FROM ost_thread_event WHERE timestamp BETWEEN '$From' AND '$To' GROUP BY DATE_FORMAT(timestamp, \"%Y\")";

            } elseif ($days >= 90) {

                $sql = "SELECT DATE_FORMAT(timestamp, \"%Y-%m\") AS Month FROM ost_thread_event WHERE timestamp BETWEEN '$From' AND '$To' GROUP BY DATE_FORMAT(timestamp, \"%Y-%m\")";

            } elseif ($days < 90) {

                $sql = "SELECT DATE_FORMAT(timestamp, \"%Y-%m-%d\") AS Days FROM ost_thread_event WHERE timestamp BETWEEN '$From' AND '$To' GROUP BY DATE_FORMAT(timestamp, \"%Y-%m-%d\")";

            }

        $result = mysqli_query($con, $sql) or die(mysqli_error($con));

        //This variable is going to be used in the $jsonData to iterate
        $j = 1;
        //This variable is used to save the values for the chart
        $i = 0;

        while ($row = mysqli_fetch_row($result)) {

            //Split the date on Year-Month-Day to work with it in the autofilter
            $row[0] = explode('-', $row[0]);
            //Change the string to date to be able to add the date to the JSON properly

            //Auto filter
            //filter by year if there are more than 3 years
            //filter by month if there are less than 3 years and more than 3 months
            //filter by days if there are less than 3 months
            if ($days >= 1095 && $FilterDate != "Months" && $FilterDate != "Days" || $FilterDate == "Years") {

                $newYear = $row[0][0] . "-12-31";

                $datetime1 = date_create($emptyFrom);
                $datetime2 = date_create($newYear);
                $interval = date_diff($datetime1, $datetime2);
                $interval = intval(substr($interval->format('%R%y'),1));

                if($interval>1){

                    for($d=0;$d<$interval;$d++) {

                        $insertNewYear = $newYear - $interval + $d;
                        $jsonData[0][$i] = $insertNewYear;
                        $i++;

                        if(count($jsonData[$j-1])==1){
                            $insertEmptyData = [0, 0, 0, 0, 0, 0, 0, 0, 0];
                        }else {
                            $insertEmptyData = $jsonData[$j-1];
                        }

                        $jsonData[$j] = $insertEmptyData;

                        $j++;

                    }
                }

                //Create a new $To to every move and change it in the next query
                $To2 = $newYear;

                $jsonData[0][$i] = substr($newYear,0,-6);

                $emptyFrom = $jsonData[0][$i];

                $i++;

            } elseif ($days >= 90 && $FilterDate != "Years" && $FilterDate != "Days" || $FilterDate == "Months") {

                if($row[0][1]=='12' || $row[0][1]=='10' || $row[0][1]=='08' || $row[0][1]=='07' || $row[0][1]=='05' || $row[0][1]=='03' || $row[0][1]=='01'){
                    $newMonth = $row[0][0] . "-" . $row[0][1] . "-31";
                }elseif($row[0][1]=='11' || $row[0][1]=='09' || $row[0][1]=='06' || $row[0][1]=='04'){
                    $newMonth = $row[0][0] . "-" . $row[0][1] . "-30";
                }elseif($row[0][1]=='02' && $row[0][0]%4==0){
                    $newMonth = $row[0][0] . "-" . $row[0][1] . "-29";
                }elseif($row[0][1]=='02'){
                    $newMonth = $row[0][0] . "-" . $row[0][1] . "-28";
                }

                $datetime1 = date_create($emptyFrom);
                $datetime2 = date_create($newMonth);
                $interval = date_diff($datetime1, $datetime2);
                $interval = intval(substr($interval->format('%R%M'),1));

                if($interval>1){

                    for($d=0;$d<$interval;$d++) {

                        $changeMonth = $row[0][1] - $interval + $d;
                        $changeYear = $row[0][0];
                        while($changeMonth<=0){

                            $changeMonth+=12;
                            $changeYear-=1;

                        }

                        if($changeMonth<10){
                            $changeMonth = "0" . $changeMonth;
                        }

                        $insertNewMonth = $changeYear . "-" . $changeMonth;
                        $jsonData[0][$i] = $insertNewMonth;
                        $i++;

                        if(count($jsonData[$j-1])==1){
                            if($FilterDept=="AutoDept") {
                                $insertEmptyData = [0, 0, 0, 0, 0, 0, 0, 0];
                            }else{

                                $insertEmptyData = [0, 0, 0, 0, 0, 0, 0, 0, 0];
                            }
                        }else {
                            $insertEmptyData = $jsonData[$j-1];
                        }
                        $jsonData[$j] = $insertEmptyData;

                        $j++;

                    }
                }
                //Create a new $To to every move and change it in the next query
                $To2 = $newMonth;

                $jsonData[0][$i] = substr($newMonth, 0, -3);

                $emptyFrom = $To2;
                $i++;

            } elseif ($days < 90 && $FilterDate != "Years" && $FilterDate != "Month" || $FilterDate == "Days") {

                $newDay = $row[0][0] . "-" . $row[0][1] . "-" . $row[0][2];

                $datetime1 = date_create($emptyFrom);
                $datetime2 = date_create($newDay);
                $interval = date_diff($datetime1, $datetime2);
                $interval = intval(substr($interval->format('%R%a'),1));

                if($interval>1){

                    for($d=0;$d<$interval;$d++) {

                        $changeDay = $row[0][2] - $interval + $d;

                        $changeMonth = $row[0][1];
                        $changeYear = $row[0][0];

                        while($changeDay<=0){

                            $changeMonth -= 1;

                            if($changeMonth<=0) {

                                $changeMonth+=12;
                                $changeYear -= 1;

                            }

                            if($changeMonth=="01") {

                                $changeDay+=31;

                            }elseif($changeMonth=="02" && $changeYear%4==0) {

                                $changeDay+=29;

                            }elseif($changeMonth=="02") {

                                $changeDay+=28;

                            }elseif($changeMonth=="03") {

                                $changeDay+=31;

                            }elseif($changeMonth=="04") {

                                $changeDay+=30;

                            }elseif($changeMonth=="05") {

                                $changeDay+=31;

                            }elseif($changeMonth=="06") {

                                $changeDay+=30;

                            }elseif($changeMonth=="07") {

                                $changeDay+=31;

                            }elseif($changeMonth=="08") {

                                $changeDay+=31;

                            }elseif($changeMonth=="09") {

                                $changeDay+=30;

                            }elseif($changeMonth=="10") {

                                $changeDay+=31;

                            }elseif($changeMonth=="11") {

                                $changeDay+=30;

                            }elseif($changeMonth=="12") {

                                $changeDay+=31;

                            }

                            if($changeDay<10 &&$changeDay>0){

                                $changeDay= "0" . $changeDay;

                            }
                        }

                        $insertNewDay = $changeYear . "-" . $changeMonth . "-" . $changeDay;

                        $jsonData[0][$i] = $insertNewDay;
                        $i++;

                        if(count($jsonData[$j-1])==1){
                            if($FilterDept=="AutoDept") {
                                $insertEmptyData = [0, 0, 0, 0, 0, 0, 0, 0];
                            }else{

                                $insertEmptyData = [0, 0, 0, 0, 0, 0, 0, 0, 0];
                            }
                        }else {
                            $insertEmptyData = $jsonData[$j-1];
                        }

                        $jsonData[$j] = $insertEmptyData;

                        $j++;

                    }
                }

                //Create a new $To to every move and change it in the next query
                $To2 = $newDay;

                $jsonData[0][$i] = $newDay;

                $i++;
            }

            //Queries for the tickets count based on what is selected on the filter
            if($FilterDept=="AutoDept") {

                //If there are some changes on the department tables from the database correct the number on ost_thread_event.dept_id from the queries
                $sql = "SELECT
                        (SELECT count(state) from ost_thread_event WHERE state='created' && (timestamp BETWEEN '$From' AND '$To2')  AND ost_thread_event.dept_id IN (6,7,8,9,10,11,12,13,14)) as register,
                        (SELECT count(SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(data, ':', -1), '[', -1), ',', 1), '}', 1)) FROM ost_thread_event, ost_thread, ost_ticket WHERE SUBSTRING(data, 3,6)= 'status'  && ost_thread_event.thread_id=ost_thread.id && ost_thread.object_id=ost_ticket.ticket_id && (timestamp BETWEEN '$From' AND '$To2') && (SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(data, ':', -1), '[', -1), ',', 1), '}', 1)) IN (8,11,12)  AND ost_thread_event.dept_id IN (6,7,8,9,10,11,12,13,14)) as approved,
                        (SELECT count(SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(data, ':', -1), '[', -1), ',', 1), '}', 1)) FROM ost_thread_event, ost_thread, ost_ticket WHERE SUBSTRING(data, 3,6)= 'status'  && ost_thread_event.thread_id=ost_thread.id && ost_thread.object_id=ost_ticket.ticket_id && (timestamp BETWEEN '$From' AND '$To2') && (SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(data, ':', -1), '[', -1), ',', 1), '}', 1)) IN (13,16,17)  AND ost_thread_event.dept_id IN (6,7,8,9,10,11,12,13,14)) as tested,
                        (SELECT count(SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(data, ':', -1), '[', -1), ',', 1), '}', 1)) FROM ost_thread_event, ost_thread, ost_ticket WHERE SUBSTRING(data, 3,6)= 'status'  && ost_thread_event.thread_id=ost_thread.id && ost_thread.object_id=ost_ticket.ticket_id && (timestamp BETWEEN '$From' AND '$To2') && (SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(data, ':', -1), '[', -1), ',', 1), '}', 1)) IN (18,21,22)  AND ost_thread_event.dept_id IN (6,7,8,9,10,11,12,13,14)) as implemented,
                        (SELECT count(SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(data, ':', -1), '[', -1), ',', 1), '}', 1)) FROM ost_thread_event, ost_thread, ost_ticket WHERE SUBSTRING(data, 3,6)= 'status'  && ost_thread_event.thread_id=ost_thread.id && ost_thread.object_id=ost_ticket.ticket_id && (timestamp BETWEEN '$From' AND '$To2') && (SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(data, ':', -1), '[', -1), ',', 1), '}', 1)) IN (2,3,4,5,9,10,14,15,19,20)  AND ost_thread_event.dept_id IN (6,7,8,9,10,11,12,13,14)) as discarded,
                        (SELECT
                            (SELECT count(SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(data, ':', -1), '[', -1), ',', 1), '}', 1)) FROM ost_thread_event, ost_thread, ost_ticket WHERE SUBSTRING(data, 3,6)= 'status'  && ost_thread_event.thread_id=ost_thread.id && ost_thread.object_id=ost_ticket.ticket_id && (timestamp BETWEEN '$From' AND '$To2') && (SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(data, ':', -1), '[', -1), ',', 1), '}', 1)) IN (2,3,4,5,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22)  AND ost_thread_event.dept_id IN (6,7,8,9,10,11,12,13,14)) + (SELECT count(state) from ost_thread_event WHERE state='created' && (timestamp BETWEEN '$From' AND '$To2')  AND ost_thread_event.dept_id IN (6,7,8,9,10,11,12,13,14))) as total";

            }else{

                $sql = "SELECT  
                        (SELECT ost_department.name FROM ost_department WHERE ost_department.id='$FilterDept') as DeptName,
                        (SELECT count(state) from ost_thread_event WHERE state='created' && (timestamp BETWEEN '$From' AND '$To2') AND dept_id='$FilterDept') as register,
                        (SELECT count(SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(data, ':', -1), '[', -1), ',', 1), '}', 1)) FROM ost_thread_event, ost_thread, ost_ticket WHERE SUBSTRING(data, 3,6)= 'status'  && ost_thread_event.thread_id=ost_thread.id && ost_thread.object_id=ost_ticket.ticket_id && (timestamp BETWEEN '$From' AND '$To2') && (SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(data, ':', -1), '[', -1), ',', 1), '}', 1)) IN (8,11,12) AND dept_id='$FilterDept') as approved,
                        (SELECT count(SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(data, ':', -1), '[', -1), ',', 1), '}', 1)) FROM ost_thread_event, ost_thread, ost_ticket WHERE SUBSTRING(data, 3,6)= 'status'  && ost_thread_event.thread_id=ost_thread.id && ost_thread.object_id=ost_ticket.ticket_id && (timestamp BETWEEN '$From' AND '$To2') && (SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(data, ':', -1), '[', -1), ',', 1), '}', 1)) IN (13,16,17) AND dept_id='$FilterDept') as tested,
                        (SELECT count(SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(data, ':', -1), '[', -1), ',', 1), '}', 1)) FROM ost_thread_event, ost_thread, ost_ticket WHERE SUBSTRING(data, 3,6)= 'status'  && ost_thread_event.thread_id=ost_thread.id && ost_thread.object_id=ost_ticket.ticket_id && (timestamp BETWEEN '$From' AND '$To2') && (SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(data, ':', -1), '[', -1), ',', 1), '}', 1)) IN (18,21,22) AND dept_id='$FilterDept') as implemented,
                        (SELECT count(SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(data, ':', -1), '[', -1), ',', 1), '}', 1)) FROM ost_thread_event, ost_thread, ost_ticket WHERE SUBSTRING(data, 3,6)= 'status'  && ost_thread_event.thread_id=ost_thread.id && ost_thread.object_id=ost_ticket.ticket_id && (timestamp BETWEEN '$From' AND '$To2') && (SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(data, ':', -1), '[', -1), ',', 1), '}', 1)) IN (2,3,4,5,9,10,14,15,19,20) AND dept_id='$FilterDept') as discarded,
                        (SELECT
                            (SELECT count(SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(data, ':', -1), '[', -1), ',', 1), '}', 1)) FROM ost_thread_event, ost_thread, ost_ticket WHERE SUBSTRING(data, 3,6)= 'status'  && ost_thread_event.thread_id=ost_thread.id && ost_thread.object_id=ost_ticket.ticket_id && (timestamp BETWEEN '$From' AND '$To2') AND dept_id='$FilterDept') + (SELECT count(state) from ost_thread_event WHERE state='created' && (timestamp BETWEEN '$From' AND '$To2') AND dept_id='$FilterDept')) as total";

            }
            //Take the result of the query or send an error if something goes wrong
            $result2 = mysqli_query($con, $sql) or die(mysqli_error($con));

            $emptyFrom=$To2;
            //Iteration to fulfill the chart
            //check every row from the result
            //if some of the options are unchecked on the filter they won't appear in the chart
            while ($row2 = mysqli_fetch_row($result2)) {

                if($FilterDept=="AutoDept") {
                    if ($CheckRegister == "false") {

                        unset($row2[0]);

                    }
                    if ($CheckApproved == "false") {

                        unset($row2[1]);

                    }
                    if ($CheckTested == "false") {

                        unset($row2[2]);

                    }
                    if ($CheckImplemented == "false") {

                        unset($row2[3]);

                    }
                    if ($CheckDiscarded == "false") {

                        unset($row2[4]);

                    }
                    if ($CheckTotal == "false") {

                        unset($row2[5]);

                    }
                }

                $jsonData[$j] = $row2;

                $j++;
            }
        }

/*
 *
 *  this elseif block and data at the end of the chart if there is no data from the database for those days
 *
 */

$toYear = substr($To,0,-6);
$toMonth[0] = substr($To,0,-6);
$toMonth[1] = substr($To,5,-3);
$toMonthTotal = $toMonth[0] . "-" . $toMonth[1];
$toDay[0] = substr($To, 0,-6);
$toDay[1] = substr($To, 5,-3);
$toDay[2] = substr($To, 8);

$toDayTotal = $toMonth[0] . "-" . $toMonth[1] . "-" . $toDay[2];

if(strlen($jsonData[0][$i-1])==4 && $jsonData[0][$i-1]<$toYear){

   $interval=substr($To,0,-6)-$jsonData[0][$i-1];

    for($d=0;$d<$interval;$d++){

        $jsonData[0][$i] = $jsonData[0][$i-1] + 1;
        $i++;

        $jsonData[$j] =  $jsonData[$j-1];

        $j++;

    }
}elseif(strlen($jsonData[0][$i-1])==7 && $jsonData[0][$i-1]<$toMonthTotal){

    $datetime1 = date_create($jsonData[0][$i-1]);
    $datetime2 = date_create($To);
    $interval = date_diff($datetime1, $datetime2);

    $interval = intval(substr($interval->format('%R%M'),1));

    for($d=0;$d<$interval;$d++){

        $jsonYear = substr($jsonData[0][$i-1],0,-3);
        $jsonMonth = substr($jsonData[0][$i-1],5);

        $jsonMonth +=1;

        if($jsonMonth>12){

            $jsonMonth = 1;
            $jsonYear +=1;

        }
        if($jsonMonth<10){

            $jsonMonth = "0".$jsonMonth;

        }

        $jsonData[0][$i] = $jsonYear . "-" . $jsonMonth;
        $i++;

        $jsonData[$j] =  $jsonData[$j-1];

        $j++;

    }
}elseif(strlen($jsonData[0][$i-1])==10 && $jsonData[0][$i-1]<$toDay){

    $datetime1 = date_create($jsonData[0][$i-1]);
    $datetime2 = date_create($To);
    $interval = date_diff($datetime1, $datetime2);
    $interval = intval(substr($interval->format('%R%a'),1));

    for($d=0;$d<$interval;$d++){

        $jsonYear = substr($jsonData[0][$i-1],0,-6);
        $jsonMonth = substr($jsonData[0][$i-1],5,-3);
        $jsonDay = substr($jsonData[0][$i-1],8);

        $jsonDay +=1;

        if($jsonMonth=="01" && $jsonDay>31){

            $jsonDay=1;
            $jsonMonth+=1;
            $jsonMonth= "0" . $jsonMonth;

        }elseif($jsonMonth=="02" && $jsonDay>28 && $jsonYear%4!=0){

            $jsonDay=1;
            $jsonMonth+=1;
            $jsonMonth= "0" . $jsonMonth;

        }elseif($jsonMonth=="02" && $jsonDay>29 && $jsonYear%4==0){

            $jsonDay=1;
            $jsonMonth+=1;
            $jsonMonth= "0" . $jsonMonth;

        }elseif($jsonMonth=="03" && $jsonDay>31){

            $jsonDay=1;
            $jsonMonth+=1;
            $jsonMonth= "0" . $jsonMonth;

        }elseif($jsonMonth=="04" && $jsonDay>30){

            $jsonDay=1;
            $jsonMonth+=1;
            $jsonMonth= "0" . $jsonMonth;

        }elseif($jsonMonth=="05" && $jsonDay>31){

            $jsonDay=1;
            $jsonMonth+=1;
            $jsonMonth= "0" . $jsonMonth;

        }elseif($jsonMonth=="06" && $jsonDay>30){

            $jsonDay=1;
            $jsonMonth+=1;
            $jsonMonth= "0" . $jsonMonth;

        }elseif($jsonMonth=="07" && $jsonDay>31){

            $jsonDay=1;
            $jsonMonth+=1;
            $jsonMonth= "0" . $jsonMonth;

        }elseif($jsonMonth=="08" && $jsonDay>31){

            $jsonDay=1;
            $jsonMonth+=1;
            $jsonMonth= "0" . $jsonMonth;

        }elseif($jsonMonth=="09" && $jsonDay>30){

            $jsonDay=1;
            $jsonMonth+=1;

        }elseif($jsonMonth=="10" && $jsonDay>31){

            $jsonDay=1;
            $jsonMonth+=1;

        }elseif($jsonMonth=="11" && $jsonDay>30){

            $jsonDay=1;
            $jsonMonth+=1;

        }elseif($jsonMonth=="12" && $jsonDay>31){

            $jsonDay=1;
            $jsonMonth=1;
            $jsonMonth= "0" . $jsonMonth;
            $jsonYear+=1;

        }

        if($jsonDay<10){

            $jsonDay= "0" . $jsonDay;

        }

        $jsonData[0][$i] = $jsonYear . "-" . $jsonMonth . "-" . $jsonDay;
        $i++;

        $jsonData[$j] = $jsonData[$j-1];

        $j++;

    }
}
        //send the data to a JSON
        $json = json_encode($jsonData);

        //Send the JSON to the main page
        echo $json;

?>