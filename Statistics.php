<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>statistics</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/mdb.css">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
</head>

<body>
<script type="text/javascript">

    // Safari 3.0+ "[object HTMLElementConstructor]"
    var isSafari = /constructor/i.test(window.HTMLElement) || (function (p) { return p.toString() === "[object SafariRemoteNotification]"; })(!window['safari'] || (typeof safari !== 'undefined' && safari.pushNotification));

    // Internet Explorer 6-11
    var isIE = /*@cc_on!@*/false || !!document.documentMode;


    if(isSafari){
        alert("This browser can't support the chart, please use, Google Chrome, Mozilla Firefox, Microsoft edge or Opera");
    }else if(isIE){
        alert("This browser can't support the chart, please use, Google Chrome, Mozilla Firefox, Microsoft edge or Opera");
    }
</script>
<div class="container">
        <img id="InnoviramLogo" src="assets/img/logo.jpg">
    </div>
    <div class="container">

        <canvas id="lineChart"></canvas>

        <form onsubmit="prepareImg()" action="CreatePDF.php" method="POST" target="_blank">

            <p class="help-block">Od </p>
            <input class="form-control input-lg" type="date" id="From">
            <p class="help-block">Do </p>
            <input class="form-control input-lg" type="date" id="To" min="1970-01-01" max="2000-13-13">

            <div class="checkbox">
                <label>
                    <input type="checkbox" checked="" id="CheckRegister">Prijavljene</label>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" checked="" id="CheckApproved">Potrjene</label>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" checked="" id="CheckTested">Testirane</label>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" checked="" id="CheckImplemented">Uvedene</label>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" checked="" id="CheckDiscarded">Zavrnjene</label>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" checked="" id="CheckTotal">Skupaj</label>
            </div>

            <p class="help-block">Filter obdobja</p>
            <select id="FilterDate">
                <option value="Auto">Avtomatsko</option>
                <option value="Years">Leta</option>
                <option value="Months">Meseci</option>
                <option value="Days">Dnevi</option>
            </select><br><br>

            <!--
                The value of this fields are the ID for each departments
            -->
            <p class="help-block">Filter po PE</p>
            <select id="FilterDept">
                <option value="AutoDept">Avtomatsko</option>
                <option value="6">PE Kondenzatorji</option>
                <option value="7">PE Stikala</option>
                <option value="8">PE Instrumenti</option>
                <option value="9">PE Baterije</option>
                <option value="10">PE Galvanotehnika</option>
                <option value="11">DIV Sistemi</option>
                <option value="12">DIV FMS</option>
                <option value="13">Skupne službe</option>
                <option value="14">Uprava</option>
            </select><br><br>

            <button class="btn btn-primary" type="button" onclick="filterByDate()">Osveži</button>
            <input id="inp_img" name="img" type="hidden" value="">
            <input id="inp_img_total" name="imgTotal" type="hidden" value="">
            <input id="inp_table_dept" name="tableOfDepts" type="hidden" value="">
            <input id="inp_table_idea" name="tableOfIdeas" type="hidden" value="">
            <input id="inp_idea_bool" name="ideaBool" type="hidden" value="">
            <button class="btn btn-primary" type="submit">Ustvari PDF</button>

        </form>

        <canvas id="lineChartTotal"></canvas>
    </div>

    <div class="container" id="tableOfDepartments">
        <div class="table-responsive">
            <table class="table" id="myTable">
                <thead id="labels">

                </thead>
                <tbody id="dept">

                </tbody>
            </table>
        </div>
    </div>

    <div class="table-responsive" id="ideaTable">
        <table class="table" id="myTableIdea">
            <thead id="IdeaLabels">

            </thead>
            <tbody id="IdeaValues">

            </tbody>
        </table>
    </div>
    <form id="IdeaForm">
        <input class="form-control" type="text" placeholder="Vnesi številko ideje" id="IdeaNumber">
        <button class="btn btn-primary btn-block" type="button" onclick="filterByIdea()" id="buttonIdea">Prikaži idejo</button>
    </form>


    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.js"></script>
    <script src="assets/js/canvas.js"></script>
    <script src="assets/js/jquery-3.3.1.min.js"></script>
    <script src="assets/js/mdb.js"></script>
    <script src="assets/js/jquery.easing.js"></script>

<!--these are the js who work with the chart, the json and to sort the tables-->
    <script src="assets/js/chart.js"></script>
    <script src="assets/js/json.js"></script>
    <script src="assets/js/sortTable.js"></script>

   <script>

        var today = new Date();
        var dd = today.getDate();
        var mm = today.getMonth()+1; //January is 0!
        var yyyy = today.getFullYear();
        if(dd<10){
            dd='0'+dd
        }
        if(mm<10){
            mm='0'+mm
        }

        today = yyyy+'-'+mm+'-'+dd;

        document.getElementById("To").setAttribute("max", today);
        document.getElementById("From").setAttribute("max", today);

    </script>

    <script>

        function prepareImg() {

            var canvas = document.getElementById('lineChart');
            var canvasTotal = document.getElementById('lineChartTotal');
            var tableOfDepts = document.getElementById('tableOfDepartments');
            document.getElementById('inp_img').value = canvas.toDataURL();
            document.getElementById('inp_img_total').value = canvasTotal.toDataURL();
            document.getElementById('inp_table_dept').value = tableOfDepts.innerHTML;
        }

    </script>

    <script>
        $("#IdeaForm").submit(function (e) {
            e.preventDefault();
            filterByIdea();
        });
    </script>

</body>

</html>