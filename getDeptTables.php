<?php
    //This php file work is to fill the chart

    include "Connection.php";

    //initialize the variables
    $jsonData= [""];
    $From= '';
    $To= '';
    $json = '';
    $dateDifference = '';
    $To2 = '';
    $interval = '';

    $CheckTotal = $_GET['Total'];
    $CheckRegister = $_GET['Register'];
    $CheckApproved = $_GET['Approved'];
    $CheckTested = $_GET['Tested'];
    $CheckImplemented = $_GET['Implemented'];
    $CheckDiscarded = $_GET['Discarded'];
    $FilterDate = $_GET['FilterDate'];

    //Check if get From is empty
    if(!$_GET['From']==null) {

        $From = $_GET['From'];

    }
    //Check if get To is empty
    if(!$_GET['To']==null){

        $To = $_GET['To'];

    }
//If From is empty, it fill be autofilled with the date 6 months before the actual date
    if($From==''){

        $sixMonthBefore = strtotime(date("Y-m-d") . '-6 months');
        $From = date("Y-m-d", $sixMonthBefore);

    }
    //If To is empty, it will be autofilled with the actual date
    if($To==''){


        $OneDayAfter = strtotime(date("Y-m-d") . '+1 day');
        $To = date("Y-m-d", $OneDayAfter);

    }

    $sql = "SELECT id,name FROM ost_department ORDER BY id";

    $result = mysqli_query($con, $sql) or die(mysqli_error($con));

        $j = 1;

        foreach ($result as $department){

            $jsonData[0][$j-1]=$department['name'];

            //These are the queries to take the values from the database
            $sqlRegisterPerDept = "SELECT count(state) from ost_thread_event WHERE state='created' AND (timestamp BETWEEN '$From' AND '$To') AND dept_id='$department[id]'";
            $sqlApprovedPerDept = "SELECT count(SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(data, ':', -1), '[', -1), ',', 1), '}', 1)) FROM ost_thread_event, ost_thread, ost_ticket WHERE SUBSTRING(data, 3,6)= 'status'  AND ost_thread_event.thread_id=ost_thread.id AND ost_thread.object_id=ost_ticket.ticket_id AND (timestamp BETWEEN '$From' AND '$To') AND (SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(data, ':', -1), '[', -1), ',', 1), '}', 1)) IN (8,11,12) AND ost_thread_event.dept_id='$department[id]'";
            $sqlTestedPerDept = "SELECT count(SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(data, ':', -1), '[', -1), ',', 1), '}', 1)) FROM ost_thread_event, ost_thread, ost_ticket WHERE SUBSTRING(data, 3,6)= 'status'  AND ost_thread_event.thread_id=ost_thread.id AND ost_thread.object_id=ost_ticket.ticket_id AND (timestamp BETWEEN '$From' AND '$To') AND (SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(data, ':', -1), '[', -1), ',', 1), '}', 1)) IN (13,16,17) AND ost_thread_event.dept_id='$department[id]'";
            $sqlImplementedPerDept = "SELECT count(SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(data, ':', -1), '[', -1), ',', 1), '}', 1)) FROM ost_thread_event, ost_thread, ost_ticket WHERE SUBSTRING(data, 3,6)= 'status'  AND ost_thread_event.thread_id=ost_thread.id AND ost_thread.object_id=ost_ticket.ticket_id AND (timestamp BETWEEN '$From' AND '$To') AND (SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(data, ':', -1), '[', -1), ',', 1), '}', 1)) IN (18,21,22) AND ost_thread_event.dept_id='$department[id]'";
            $sqlDiscardedPerDept = "SELECT count(SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(data, ':', -1), '[', -1), ',', 1), '}', 1)) FROM ost_thread_event, ost_thread, ost_ticket WHERE SUBSTRING(data, 3,6)= 'status'  AND ost_thread_event.thread_id=ost_thread.id AND ost_thread.object_id=ost_ticket.ticket_id AND (timestamp BETWEEN '$From' AND '$To') AND (SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(data, ':', -1), '[', -1), ',', 1), '}', 1)) IN (2,3,4,5,9,10,14,15,19,20) AND ost_thread_event.dept_id='$department[id]'";

            //These are the results of the queries
            $resultRegisterPerDept = mysqli_query($con, $sqlRegisterPerDept) or die(mysqli_error($con));
            $resultApprovedPerDept = mysqli_query($con, $sqlApprovedPerDept) or die(mysqli_error($con));
            $resultTestedPerDept = mysqli_query($con, $sqlTestedPerDept) or die(mysqli_error($con));
            $resultImplementedPerDept = mysqli_query($con, $sqlImplementedPerDept) or die(mysqli_error($con));
            $resultDiscardedPerDept = mysqli_query($con, $sqlDiscardedPerDept) or die(mysqli_error($con));

            //These are the values of the results of the queries
            $RegisterPerDept = mysqli_fetch_row($resultRegisterPerDept);
            $ApprovedPerDept = mysqli_fetch_row($resultApprovedPerDept);
            $TestedPerDept = mysqli_fetch_row($resultTestedPerDept);
            $ImplementedPerDept = mysqli_fetch_row($resultImplementedPerDept);
            $DiscardedPerDept = mysqli_fetch_row($resultDiscardedPerDept);

            //This is the total of all departments
            $TotalPerDept = $RegisterPerDept[0]+$ApprovedPerDept[0]+$TestedPerDept[0]+$ImplementedPerDept[0]+$DiscardedPerDept[0];

            $jsonData[$j][0] = $RegisterPerDept;
            $jsonData[$j][1] = $ApprovedPerDept;
            $jsonData[$j][2] = $TestedPerDept;
            $jsonData[$j][3] = $ImplementedPerDept;
            $jsonData[$j][4] = $DiscardedPerDept;
            $jsonData[$j][5] = $TotalPerDept;

            $j++;
        }
        //send the data to a JSON
        $json = json_encode($jsonData);

        //Send the JSON to the main page
        echo $json;

?>