//This function filter the chart by date
var From= "";
var To= "";

var CheckRegister= "";
var CheckApproved= "";
var CheckTested= "";
var CheckImplemented= "";
var CheckDiscarded= "";
var CheckTotal= "";

var TotalRegister= 0;
var TotalApproved= 0;
var TotalTested= 0;
var TotalImplemented= 0;
var TotalDiscarded= 0;
var TotalTotal= 0;

var FilterDate = "";
var FilterDept = "";
var IdeaNumber = "";
var idCollapse = "";
var targetCollapse = "";
var threadBody = "";
var ideaTable = "";
var tablecolor = true;

function filterByDate(){

    //Take the values From the form
    From= document.getElementById("From").value;
    To= document.getElementById("To").value;
    CheckTotal= document.getElementById("CheckTotal").checked;
    CheckRegister= document.getElementById("CheckRegister").checked;
    CheckApproved= document.getElementById("CheckApproved").checked;
    CheckTested= document.getElementById("CheckTested").checked;
    CheckImplemented= document.getElementById("CheckImplemented").checked;
    CheckDiscarded= document.getElementById("CheckDiscarded").checked;
    CheckTotal= document.getElementById("CheckTotal").checked;
    FilterDate = document.getElementById("FilterDate").value;
    FilterDept = document.getElementById("FilterDept").value;

    //Request a JSON for the chart
    JSONrequest.open("GET", "GetDate.php?From=" + From +
        "&To=" + To +
        "&Total=" + CheckTotal +
        "&Register=" + CheckRegister +
        "&Approved=" + CheckApproved +
        "&Tested=" + CheckTested +
        "&Implemented=" + CheckImplemented +
        "&Discarded=" + CheckDiscarded +
        "&FilterDate=" + FilterDate +
        "&FilterDept=" + FilterDept + "", true);

    JSONrequest.send(null);

    //Request a JSON for the total chart
    JSONrequestTotal.open("GET", "GetDateTotal.php?From=" + From +
        "&To=" + To +
        "&Total=" + CheckTotal +
        "&Register=" + CheckRegister +
        "&Approved=" + CheckApproved +
        "&Tested=" + CheckTested +
        "&Implemented=" + CheckImplemented +
        "&Discarded=" + CheckDiscarded +
        "&FilterDate=" + FilterDate +
        "&FilterDept=" + FilterDept + "", true);

    JSONrequestTotal.send(null);

    //Request a JSON for the department table
    JSONDeptTable.open("GET", "GetDeptTables.php?From=" + From +
        "&To=" + To +
        "&Total=" + CheckTotal +
        "&Register=" + CheckRegister +
        "&Approved=" + CheckApproved +
        "&Tested=" + CheckTested +
        "&Implemented=" + CheckImplemented +
        "&Discarded=" + CheckDiscarded +
        "&FilterDate=" + FilterDate + "", true);

    JSONDeptTable.send(null);

}

function filterByIdea() {

    //get the idea written
    IdeaNumber = document.getElementById("IdeaNumber").value;

    JSONIdeaTable.open("GET", "getIdea.php?IdeaNumber=" + IdeaNumber + "", true);
    JSONIdeaTable.send(null);

}

//This function handle the JSON response
function handleReply() {

    //If the response is correct
    if (JSONrequest.readyState == 4) {
        //Save the response on a variable
        data1 = JSON.parse(this.responseText);
        //Call the function removeData from canvas.js
        removeData(myLine);

        //Call the function assData from canvas.js
        for (var i = 0; i < data1.length - 1; i++) {

            addData(myLine, data1[0][i], data1[i + 1]);

        }
    }
}

function handleReplyTotal() {

    //If the response is correct
    if (JSONrequestTotal.readyState == 4) {
        //Save the response on a variable
        data2 = JSON.parse(this.responseText);

        //Call the function removeData from canvas.js
        removeData(myLineTotal);
        //Call the function assData from canvas.js
        for (var i = 0; i < data2.length - 1; i++) {

            addDataTotal(myLineTotal, data2[0][i], data2[i + 1]);

        }
    }
}

function getIdeaTable(){

    tablecolor=true;

    document.getElementById("IdeaValues").innerHTML = "";

    if (JSONIdeaTable.readyState == 4){

        //if the JSON has a response write it on the variable
        ideaTable = JSON.parse(this.responseText);

        //create the idea header
        document.getElementById("IdeaLabels").innerHTML =
            "<tr>" +
                "<th style='cursor: pointer; background-color: #bebabe'><h4>Številka ideje</h4></th>" +
                "<th style='cursor: pointer; background-color: #807c80'><h4>PE</h4></th>" +
                "<th style='cursor: pointer; background-color: #bebabe'><h4>Oseba</h4></th>" +
                "<th style='cursor: pointer; background-color: #807c80'><h4>Status</h4></th>" +
                "<th style='cursor: pointer; background-color: #bebabe'><h4>Tip sprememb</h4></th>" +
                "<th style='cursor: pointer; background-color: #807c80'><h4>Vsebina sprememb</h4></th>" +
                "<th style='cursor: pointer; background-color: #bebabe'><h4>Čas spremembe</h4></th>" +
            "</tr>";

            for (var i= 0; i<ideaTable.length;i++) {

                //these two variable are used to change the id and target of the collapse
                idCollapse = "demo" + i;
                targetCollapse = "#demo" + i;

                //this variable is going to be used to store the HTML to shown, we erase it at every iteration to avoid duplicates
                threadBody = "";

                if(ideaTable[i][2]==null){

                    ideaTable[i][2] = "Ni dodeljeno"

                }
                if(ideaTable[i][4]==null){

                    ideaTable[i][4] = "Brez sprememb";

                }

                //if the message from the idea is longer than 100 characters it will be replaced with a button to show and hide the message
                if(ideaTable[i][5]!=null && ideaTable[i][5].length>100){

                    threadBody = '<button type="button" class="btn btn-info" data-toggle="collapse" data-target=' + targetCollapse + '>Prikaži ali skrij tekst</button>' +
                    '<div id=' + idCollapse + ' class="collapse">' +
                    ideaTable[i][5] +
                    '</div>';

                }else if(ideaTable[i][5]==null){

                    threadBody = "Ni sprememb";

                }else{
                    threadBody = ideaTable[i][5];
                }

                //insert data in the table based on this variable to change the color of every two lines
                if(tablecolor==true) {
                    document.getElementById("IdeaValues").innerHTML +=
                        "<tr>" +
                        "<td>" + ideaTable[i][0] + "</td>" +
                        "<td>" + ideaTable[i][1] + "</td>" +
                        "<td>" + ideaTable[i][2] + "</td>" +
                        "<td>" + ideaTable[i][3] + "</td>" +
                        "<td>" + ideaTable[i][4] + "</td>" +
                        "<td>" + threadBody + "</td>" +
                        "<td>" + ideaTable[i][6] + "</td>" +
                        "</tr>";
                    tablecolor=false;
                }else{
                    document.getElementById("IdeaValues").innerHTML +=
                        "<tr bgcolor='#DDDDDD'>" +
                        "<td>" + ideaTable[i][0] + "</td>" +
                        "<td>" + ideaTable[i][1] + "</td>" +
                        "<td>" + ideaTable[i][2] + "</td>" +
                        "<td>" + ideaTable[i][3] + "</td>" +
                        "<td>" + ideaTable[i][4] + "</td>" +
                        "<td>" + threadBody + "</td>" +
                        "<td>" + ideaTable[i][6] + "</td>" +
                        "</tr>";
                    tablecolor=true;
                }
            }

    }

}

function getDeptTable(){

    tablecolor=true;

    var deptTable = "";

    //if the JSON have a response
    if (JSONDeptTable.readyState == 4){

        deptTable = JSON.parse(this.responseText);

        var resize = 1;
        //this is is going to help to resize the table, by increasing the resize value everytime a type of data is going to be use
        if (CheckRegister){
            resize++;
        }
        if (CheckApproved){
            resize++;
        }
        if (CheckTested){
            resize++;
        }
        if (CheckImplemented){
            resize++;
        }
        if (CheckDiscarded){
            resize++;
        }
        if (CheckTotal){
            resize++;
        }
        document.getElementById("tableOfDepartments").setAttribute("style","width:" + ((resize*100)/7) + "%");


        var deptlabels= "";
        //This if create the label for the department table unless it's unchecked
        if(CheckRegister || CheckApproved || CheckTested || CheckImplemented || CheckDiscarded || CheckTotal ) {

           if (deptTable[0][0] != null) {
               deptlabels += "<th onclick='sortTable(0, \"myTable\")' style='cursor: pointer;'><h4>Poslovne enote</h4></th>";
           }
           if (CheckRegister) {
               deptlabels += "<th onclick='sortTable(1, \"myTable\")' id='register'><h4>Prijavljene</h4></th>";
           }
           if (CheckApproved) {
               deptlabels += "<th onclick='sortTable(2, \"myTable\")' id='approved'><h4>Potrjene</h4></th>";
           }
           if (CheckTested) {
               deptlabels += "<th onclick='sortTable(3, \"myTable\")' id='tested'><h4>Testirane</h4></th>";
           }
           if (CheckImplemented) {
               deptlabels += "<th onclick='sortTable(4, \"myTable\")' id='implemented'><h4>Uvedene</h4></th>";
           }
           if (CheckDiscarded) {
               deptlabels += "<th onclick='sortTable(5, \"myTable\")' id='discarded'><h4>Zavrnjene</h4></th>";
           }
           if (CheckTotal) {
               deptlabels += "<th onclick='sortTable(6, \"myTable\")' id='total'><h4>Skupaj</h4></th>";
           }
       }
        document.getElementById("labels").innerHTML =
        "<tr>" +
        deptlabels +
        "</tr>";

        document.getElementById("dept").innerHTML="";

        for(var i=1;i<deptTable.length+1;i++){

            var deptvalues = "";
            var h=0;

//this is insert the data for each department unless it's unchecked
            if(CheckRegister || CheckApproved || CheckTested || CheckImplemented || CheckDiscarded ||CheckTotal ) {

                if(deptTable[0][i-1]=="Inoviram 123"){

                }else if (deptTable[0][i - 1] != null) {

                    deptvalues += "<td id='departments'><b>" + deptTable[0][i - 1] + "</b></td>";

                    if (CheckRegister == true) {
                        deptvalues += "<td>" + deptTable[i][h] + "</td>";
                        TotalRegister += parseInt(deptTable[i][h]);
                    }
                    h++;
                    if (CheckApproved == true) {
                        deptvalues += "<td>" + deptTable[i][h] + "</td>";
                        TotalApproved +=parseInt(deptTable[i][h]);
                    }
                    h++;
                    if (CheckTested == true) {
                        deptvalues += "<td>" + deptTable[i][h] + "</td>";
                        TotalTested +=parseInt(deptTable[i][h]);
                    }
                    h++;
                    if (CheckImplemented == true) {
                        deptvalues += "<td>" + deptTable[i][h] + "</td>";
                        TotalImplemented +=parseInt(deptTable[i][h]);
                    }
                    h++;
                    if (CheckDiscarded == true) {
                        deptvalues += "<td>" + deptTable[i][h] + "</td>";
                        TotalDiscarded +=parseInt(deptTable[i][h]);
                    }
                    h++;
                    if (CheckTotal == true) {
                        deptvalues += "<td>" + deptTable[i][h] + "</td>";
                        TotalTotal +=parseInt(deptTable[i][h]);
                    }
                }
            }

            if(deptvalues != "") {
                if(tablecolor==true) {
                    document.getElementById("dept").innerHTML += "<tr>" +
                        deptvalues +
                        "</tr>";

                    tablecolor=false;
                }else{
                    document.getElementById("dept").innerHTML += "<tr bgcolor='#DDDDDD'>" +
                        deptvalues +
                        "</tr>";
                    tablecolor=true;
                }
            }
        }
        document.getElementById("dept").innerHTML += "<tr id='totalrow'><td> Total </td>";

        if (CheckRegister == true) {
            document.getElementById("totalrow").innerHTML += "<td>" + TotalRegister + "</td>";
        }
        if (CheckApproved == true) {
            document.getElementById("totalrow").innerHTML += "<td>" + TotalApproved + "</td>";
        }
        if (CheckTested == true) {
            document.getElementById("totalrow").innerHTML += "<td>" + TotalTested + "</td>";
        }
        if (CheckImplemented == true) {
            document.getElementById("totalrow").innerHTML += "<td>" + TotalImplemented + "</td>"
        }
        if (CheckDiscarded == true) {
            document.getElementById("totalrow").innerHTML += "<td>" + TotalDiscarded + "</td>"
        }
        if (CheckTotal == true) {
            document.getElementById("totalrow").innerHTML += "<td>" + TotalTotal + "</td>";
        }

    }
}



var JSONrequest=new XMLHttpRequest();  // The variable that makes Ajax possible!
JSONrequest.onreadystatechange=handleReply;

var JSONrequestTotal=new XMLHttpRequest();  // The variable that makes Ajax possible!
JSONrequestTotal.onreadystatechange=handleReplyTotal;

var JSONDeptTable=new XMLHttpRequest();  // The variable that makes Ajax possible!
JSONDeptTable.onreadystatechange=getDeptTable;

var JSONIdeaTable=new XMLHttpRequest();  // The variable that makes Ajax possible!
JSONIdeaTable.onreadystatechange=getIdeaTable;