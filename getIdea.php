<?php

    include "Connection.php";

    //initialize the variables
    $jsonData= [""];

    $lastTimestamp= "";

    $newLine = [];

    $bodyValues = [];

    $i = 0;

    $ideaNumber = $_GET['IdeaNumber'];

    //query to grab most of the values from the database
    $sql="SELECT ost_ticket.number, ost_department.name, ost_user.name , state, data, null, timestamp FROM `ost_thread_event` LEFT JOIN ost_thread ON ost_thread_event.thread_id= ost_thread.id LEFT JOIN ost_ticket ON ost_thread.object_id=ost_ticket.ticket_id LEFT JOIN ost_department ON ost_thread_event.dept_id=ost_department.id LEFT JOIN ost_staff ON ost_thread_event.staff_id=ost_staff.staff_id LEFT JOIN ost_user_account on ost_thread_event.username=ost_user_account.username LEFT JOIN ost_user ON ost_user.id=ost_user_account.user_id WHERE ost_ticket.number=$ideaNumber ORDER BY `ost_thread_event`.`timestamp` ASC";

    $result = mysqli_query($con, $sql) or die(mysqli_error($con));

    //query to grab the messages of the idea
    $sql2="SELECT DISTINCT body, ost_thread_entry.created, poster FROM `ost_thread_entry`, ost_thread_event, ost_thread, ost_ticket WHERE ost_thread_entry.thread_id=ost_thread_event.thread_id AND ost_thread_event.thread_id=ost_thread.id AND ost_thread.object_id=ost_ticket.ticket_id AND ost_ticket.number=$ideaNumber ORDER BY `ost_thread_entry`.`created` ASC";

    $resultBody= mysqli_query($con, $sql2) or die(mysqli_error($con));

//insert the message of the ideas on an array
    while($newLineValues = mysqli_fetch_row($resultBody)){
        $bodyValues[$i] = $newLineValues;
        $i++;

    }
    //This variable is going to be used in the $jsonData to iterate
    $j = 0;

    while($row = mysqli_fetch_row($result)){

        //translate the values of the array
        if($row[3]=="created"){
            $row[3]="Prijava";
        }
        if($row[3]=="closed"){
            $row[3]="Zaključeno";
        }
        if($row[3]=="reopened"){
            $row[3]="Ponovno odprto";
        }
        if($row[3]=="assigned"){
            $row[3]="Dodeljen MI";
        }
        if($row[3]=="overdue"){
            $row[3]="V zamudi";
        }
        if($row[3]=="edited"){
            $row[3]="Dopolnjeno";
        }
        if($row[3]=="collab"){
            $row[3]="Dodan sodelavec";
        }

        //split the array  to take some string to work with doing another queries on the database to extract values
        $rowNumber = preg_replace("/[^0-9]/", '', $row[4]);

        $rowType = explode('"', $row[4]);

        if(isset($rowType[1]) && strtolower($rowType[1]) != null) {
            if (strtolower($rowType[1]) == "status") {

                $sql = "SELECT name FROM ost_ticket_status WHERE id='$rowNumber'";

                $result2 = mysqli_query($con, $sql) or die (mysqli_error($con));

                while ($value = mysqli_fetch_row($result2)) {

                    $row[4] = "Nov status: " . $value[0];

                }

            } elseif (strtolower($rowType[1]) == "staff") {

                $sql = "SELECT username, firstname, lastname FROM `ost_staff` WHERE staff_id='$rowNumber'";

                $result2 = mysqli_query($con, $sql) or die (mysqli_error($con));

                while ($value = mysqli_fetch_row($result2)) {

                    if ($value[0] == "Guest") {
                        $row[4] = "Manager idej: " . $value[0];
                    } else {
                        $row[4] = "Manager idej: " . $value[0] . " " . $value[1] . " " . $value[2];
                    }

                }

            }elseif(strtolower($rowType[1]) == "add") {

                $sql = "SELECT name FROM `ost_user` WHERE id='$rowType[3]'";

                $result2 = mysqli_query($con, $sql) or die (mysqli_error($con));

                while ($value = mysqli_fetch_row($result2)) {

                    $row[4] = "Dodan sodelavec= " . $value[0];

                }

            }elseif(strtolower($rowType[1]) == "claim") {

                $row[4] = "Interni proces";

            }elseif(strtolower($rowType[1]) == "del") {

                $row[4] = "Sodelavec odstranjen= " . $rowType[7];

            }elseif(strtolower($rowType[1]) == "fields") {

                $row[4] = "Sprememba naslova: $rowType[5]";

            }elseif(strtolower($rowType[1]) == "sla_id" || strtolower($rowType[1]) == "team" || strtolower($rowType[1]) == "topic_id") {

                $row[4] = "Interni proces";

            }
        }

        //if the time of bodyvalues and row are the same, change row to insert bodyvalues message on it
        for($i=0;$i<sizeof($bodyValues);$i++) {
            if ($bodyValues[$i][1] == $row[6]) {

                $row[5] = $bodyValues[$i][0];

                array_shift($bodyValues);

                $i--;

            } else if ($bodyValues[$i][1] > $lastTimestamp && $bodyValues[$i][1] < $row[6]) {

                $newLine[0] = $row[0];
                $newLine[1] = $row[1];
                $newLine[2] = $bodyValues[$i][2];
                $newLine[3] = "Dopolnjeno";
                $newLine[4] = "Nova objava";
                $newLine[5] = $bodyValues[$i][0];
                $newLine[6] = $bodyValues[$i][1];

                $jsonData[$j] = $newLine;
                $j++;

                array_shift($bodyValues);
                $newLine = [];

                $i--;

            }
        }
        $lastTimestamp = $row[6];

        if($row[2]==null){
           $row[2] = "database problem";
        }

        $jsonData[$j] = $row;
        $j++;

    }

    //extract the name of the creator of the Idea and insert it on the first line of the array to force it to show it
    $sql = "SELECT name FROM ost_user, ost_ticket WHERE id=ost_ticket.user_id and ost_ticket.number='$ideaNumber'";

    $result = mysqli_query($con, $sql) or die($con);
    while($creator = mysqli_fetch_row($result)){

        $jsonData[0][2] = $creator[0];

    }

        for($i=0;$i<sizeof($bodyValues);$i++){

            $newLine[0] = $jsonData[0][0];
            $newLine[1] = $jsonData[0][1];
            $newLine[2] = $bodyValues[0][2];
            $newLine[3] = "Dopolnjeno";
            $newLine[4] = "Nova objava";
            $newLine[5] = $bodyValues[$i][0];
            $newLine[6] = $bodyValues[$i][1];

            $jsonData[$j] = $newLine;
            $j++;

        }

        //send the data to a JSON
        $json = json_encode($jsonData);

        //Send the JSON to the main page
        echo $json;

?>