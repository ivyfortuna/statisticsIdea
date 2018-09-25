
    //initialize the variables
    var ctx = '';
    var data1 = '';
    var data2 = '';
    var totalDataset = 0;

    //This variable holds all the information which is going to show on the chart
    var config = {

        //The type of the chart, check chart.js to see the types and how them works to change it
        type: 'line',
        data: {

            //This labels will be shown under the chart
            labels: [],

            //Here goes the data for each line
            datasets:
                [
                    {

						label: "Prijavljene",
                        fill:false,
						data: [0]

               		},{

						label: "Potrjene",
                        fill:false,
						data: [0]

                	},{

                        label: "Testirane",
                        fill:false,
                        data: [0]

                    },{

                        label: "Uvedene",
                        fill:false,
                        data: [0]

                    },{

                        label: "Zavrnjene",
                        fill:false,
                        data: [0]

                    }
                ]
        },

        //Configuration of the chart
        options: {
            //Responsive true or false
            responsive: true,
            //Display tooltip true or false, if true type the text
            title: {
                display: true,
                text: 'Statistika v obdobju'
            },

            legend:{
                display:true,
                labels:{
                    fontSize:14
                }
            },

            //Display tooltip true or false, check different modes on chart.js
            tooltips: {
                mode: 'index',
                intersect: false,
            },
            //Display hover true or false, check modes on chart.js (works pretty similar to tooltip)
            hover: {
                mode: 'nearest',
                intersect: true
            },

            //Configurations of the scales, display true or false, scaleLabel display true or false if true type the labelString
            scales: {

                //Scale X
                xAxes: [{
                    display: true,
                    scaleLabel: {
                        display: true,
                        labelString: 'Obdobje',
                        fontSize: 14
                    }
                }],
                //Scale Y
                yAxes: [{
                    display: true,
                    scaleLabel: {
                        display: true,
                        labelString: 'Število',
                        fontSize: 14
                    },
                    ticks: {
                        beginAtZero: true,
                        userCallback: function(label, index, labels) {
                            // when the floored value is the same as the value we have a whole number
                            if (Math.floor(label) === label) {
                                return label;
                            }

                        }
                    }

                }]
            }
        }
    };

    var ctxTotal = '';

    //This variable holds all the information which is going to show on the chart
    var configTotal = {

        //The type of the chart, check chart.js to see the types and how them works to change it
        type: 'line',
        data: {

            //This labels will be shown under the chart
            labels: [],

            //Here goes the data for each line
            datasets:
                [
                    {
                        label: "Prijavljene",
                        fill:false,
                        data: [0]

                    },{

                    label: "Potrjene",
                    fill:false,
                    data: [0]

                },{

                    label: "Testirane",
                    fill:false,
                    data: [0]

                },{

                    label: "Uvedene",
                    fill:false,
                    data: [0]

                },{

                    label: "Zavrnjene",
                    fill:false,
                    data: [0]

                },{

                    //Label for the line, it's colors and values
                    label: "Skupaj",
                    fill:false,

                    //edit this values to change what you want to show
                    data: [0]

                }
                ]
        },

        //Configuration of the chart
        options: {
            //Responsive true or false
            responsive: true,
            //Display tooltip true or false, if true type the text
            title: {
                display: true,
                text: 'Skupna statistika'
            },

            legend:{
                display:true,
                labels:{
                    fontSize:14
                }
            },

            //Display tooltip true or false, check different modes on chart.js
            tooltips: {
                mode: 'index',
                intersect: false,
            },
            //Display hover true or false, check modes on chart.js (works pretty similar to tooltip)
            hover: {
                mode: 'nearest',
                intersect: true
            },

            //Configurations of the scales, display true or false, scaleLabel display true or false if true type the labelString
            scales: {

                //Scale X
                xAxes: [{
                    display: true,
                    scaleLabel: {
                        display: true,
                        labelString: 'Obdobje',
                        fontSize: 14
                    },
                }],
                //Scale Y
                yAxes: [{
                    display: true,
                    scaleLabel: {
                        display: true,
                        labelString: 'Število',
                        fontSize: 14
                    },
                    ticks: {
                        beginAtZero: true,
                        userCallback: function(label, index, labels) {
                            // when the floored value is the same as the value we have a whole number
                            if (Math.floor(label) === label) {
                                return label;
                            }

                        },
                    }

                }]
            }
        }
    };

    //When the page is loaded it execute this script to load the chart properly
    window.onload = function () {

        ctx = document.getElementById('lineChart').getContext('2d');
        window.myLine = new Chart(ctx, config);
        ctxTotal = document.getElementById('lineChartTotal').getContext('2d');
        window.myLineTotal = new Chart(ctxTotal, configTotal);
        filterByDate();

    };

    //This function insert data into the chart
    function addData(chart, label, data) {

        //execute this lines if any of this options is checked on the filter
        if(CheckRegister || CheckApproved || CheckTested || CheckImplemented || CheckDiscarded || CheckTotal || FilterDept!="AutoDept") {

            document.getElementById("lineChart").hidden=false;

            chart.data.labels.push(label);
            var i = 0;

            if(FilterDept=="AutoDept") {

                chart.options.title.text = "Statistika v obdobju";
                //check every checked value in the filter to add or not add it to the chart
                if (CheckRegister) {
                    chart.data.datasets[i].label = "Prijavljene";
                    chart.data.datasets[i].data.push(data[i]);
                    chart.data.datasets[i].backgroundColor = 'rgba(0,0,0,0.5)';
                    chart.data.datasets[i].borderColor = 'rgba(0,0,0,0.5)';
                }
                i++;
                if (CheckApproved) {
                    chart.data.datasets[i].label = "Potrjene";
                    chart.data.datasets[i].data.push(data[i]);
                    chart.data.datasets[i].backgroundColor = 'rgba(255,0,0,0.5)';
                    chart.data.datasets[i].borderColor = 'rgba(255,0,0,0.5)';
                }
                i++;
                if (CheckTested) {
                    chart.data.datasets[i].label = "Testirane";
                    chart.data.datasets[i].data.push(data[i]);
                    chart.data.datasets[i].backgroundColor = 'rgba(255,255,0,0.5)';
                    chart.data.datasets[i].borderColor = 'rgba(255,255,0,0.5)';
                }
                i++;
                if (CheckImplemented) {
                    chart.data.datasets[i].label = "Uvedene";
                    chart.data.datasets[i].data.push(data[i]);
                    chart.data.datasets[i].backgroundColor = 'rgba(0,0,255,0.5)';
                    chart.data.datasets[i].borderColor = 'rgba(0,0,255,0.5)';
                }
                i++;
                if (CheckDiscarded) {
                    chart.data.datasets[i].label = "Zavrnjene";
                    chart.data.datasets[i].data.push(data[i]);
                    chart.data.datasets[i].backgroundColor = 'rgba(255,0,255,0.5)';
                    chart.data.datasets[i].borderColor = 'rgba(255,0,255,0.5)';
                }
            }else{

                if(typeof (data[i])=='string'){
                    chart.options.title.text = "Statistika v obdobju od " + data[i];
                }
                if (CheckRegister) {
                    chart.data.datasets[i].label = "Prijavljene";
                    chart.data.datasets[i].data.push(data[i+1]);
                    chart.data.datasets[i].backgroundColor = 'rgba(0,0,0,0.5)';
                    chart.data.datasets[i].borderColor = 'rgba(0,0,0,0.5)';
                }
                i++;
                if (CheckApproved) {
                    chart.data.datasets[i].label = "Potrjene";
                    chart.data.datasets[i].data.push(data[i+1]);
                    chart.data.datasets[i].backgroundColor = 'rgba(255,0,0,0.5)';
                    chart.data.datasets[i].borderColor = 'rgba(255,0,0,0.5)';
                }
                i++;
                if (CheckTested) {
                    chart.data.datasets[i].label = "Testirane";
                    chart.data.datasets[i].data.push(data[i+1]);
                    chart.data.datasets[i].backgroundColor = 'rgba(255,255,0,0.5)';
                    chart.data.datasets[i].borderColor = 'rgba(255,255,0,0.5)';
                }
                i++;
                if (CheckImplemented) {
                    chart.data.datasets[i].label = "Uvedene";
                    chart.data.datasets[i].data.push(data[i+1]);
                    chart.data.datasets[i].backgroundColor = 'rgba(0,0,255,0.5)';
                    chart.data.datasets[i].borderColor = 'rgba(0,0,255,0.5)';
                }
                i++;
                if (CheckDiscarded) {
                    chart.data.datasets[i].label = "Zavrnjene";
                    chart.data.datasets[i].data.push(data[i+1]);
                    chart.data.datasets[i].backgroundColor = 'rgba(255,0,255,0.5)';
                    chart.data.datasets[i].borderColor = 'rgba(255,0,255,0.5)';
                }
                i++;
            }

            chart.update();

        }else{

            //if there is no type of data checked and the department is on automake the chart to dissapear
            if(FilterDept=="Auto") {
                document.getElementById("lineChart").hidden = true;
            }
        }
    }

    function addDataTotal(chart, label, data) {

        //execute this lines if any of this options is checked on the filter
        if(CheckRegister || CheckApproved || CheckTested || CheckImplemented || CheckDiscarded || CheckTotal || FilterDept!="AutoDept") {

            document.getElementById("lineChartTotal").hidden=false;

            chart.data.labels.push(label);
            var i = 0;

            if(FilterDept=="AutoDept") {

                chart.options.title.text ="Skupna statistika";

                if (CheckRegister) {
                    chart.data.datasets[i].label = "Prijavljene";
                    chart.data.datasets[i].data.push(data[i]);
                    chart.data.datasets[i].backgroundColor = 'rgba(0,0,0,0.5)';
                    chart.data.datasets[i].borderColor = 'rgba(0,0,0,0.5)';
                }
                i++;
                if (CheckApproved) {
                    chart.data.datasets[i].label = "Potrjene";
                    chart.data.datasets[i].data.push(data[i]);
                    chart.data.datasets[i].backgroundColor = 'rgba(255,0,0,0.5)';
                    chart.data.datasets[i].borderColor = 'rgba(255,0,0,0.5)';
                }
                i++;
                if (CheckTested) {
                    chart.data.datasets[i].label = "Testirane";
                    chart.data.datasets[i].data.push(data[i]);
                    chart.data.datasets[i].backgroundColor = 'rgba(255,255,0,0.5)';
                    chart.data.datasets[i].borderColor = 'rgba(255,255,0,0.5)';
                }
                i++;
                if (CheckImplemented) {
                    chart.data.datasets[i].label = "Uvedene";
                    chart.data.datasets[i].data.push(data[i]);
                    chart.data.datasets[i].backgroundColor = 'rgba(0,0,255,0.5)';
                    chart.data.datasets[i].borderColor = 'rgba(0,0,255,0.5)';
                }
                i++;
                if (CheckDiscarded) {
                    chart.data.datasets[i].label = "Zavrnjene";
                    chart.data.datasets[i].data.push(data[i]);
                    chart.data.datasets[i].backgroundColor = 'rgba(255,0,255,0.5)';
                    chart.data.datasets[i].borderColor = 'rgba(255,0,255,0.5)';
                }
                i++;
                if (CheckTotal) {
                    chart.data.datasets[i].label = "Skupaj";
                    chart.data.datasets[i].data.push(data[i]);
                    chart.data.datasets[i].backgroundColor = 'rgba(0,255,255,0.5)';
                    chart.data.datasets[i].borderColor = 'rgba(0,255,255,0.5)';
                }
            }else{

                if(typeof (data[i])=='string'){
                    chart.options.title.text = "Skupna statistika od " + data[i];
                }
                if (CheckRegister) {
                    chart.data.datasets[i].label = "Prijavljene";
                    chart.data.datasets[i].data.push(data[i+1]);
                    chart.data.datasets[i].backgroundColor = 'rgba(0,0,0,0.5)';
                    chart.data.datasets[i].borderColor = 'rgba(0,0,0,0.5)';
                }
                i++;
                if (CheckApproved) {
                    chart.data.datasets[i].label = "Potrjene";
                    chart.data.datasets[i].data.push(data[i+1]);
                    chart.data.datasets[i].backgroundColor = 'rgba(255,0,0,0.5)';
                    chart.data.datasets[i].borderColor = 'rgba(255,0,0,0.5)';
                }
                i++;
                if (CheckTested) {
                    chart.data.datasets[i].label = "Testirane";
                    chart.data.datasets[i].data.push(data[i+1]);
                    chart.data.datasets[i].backgroundColor = 'rgba(255,255,0,0.5)';
                    chart.data.datasets[i].borderColor = 'rgba(255,255,0,0.5)';
                }
                i++;
                if (CheckImplemented) {
                    chart.data.datasets[i].label = "Uvedene";
                    chart.data.datasets[i].data.push(data[i+1]);
                    chart.data.datasets[i].backgroundColor = 'rgba(0,0,255,0.5)';
                    chart.data.datasets[i].borderColor = 'rgba(0,0,255,0.5)';
                }
                i++;
                if (CheckDiscarded) {
                    chart.data.datasets[i].label = "Zavrnjene";
                    chart.data.datasets[i].data.push(data[i+1]);
                    chart.data.datasets[i].backgroundColor = 'rgba(255,0,255,0.5)';
                    chart.data.datasets[i].borderColor = 'rgba(255,0,255,0.5)';
                }
                i++;
                if (CheckTotal) {
                    chart.data.datasets[i].label = "Skupaj";
                    chart.data.datasets[i].data.push(data[i+1]);
                    chart.data.datasets[i].backgroundColor = 'rgba(0,255,255,0.5)';
                    chart.data.datasets[i].borderColor = 'rgba(0,255,255,0.5)';
                }
            }

            chart.update();

        }else{

            //if there is no type of data checked and the department is on automake the chart to dissapear
            if(FilterDept=="AutoDept") {
                document.getElementById("lineChartTotal").hidden = true;
            }
        }
    }

    //This function delete ALL the points in the chart
    function removeData(chart) {

        var i=0;
        var datasetLength = chart.config.data.datasets.length;

        for(i=0;i<datasetLength;i++) {

            if(chart.config.data.datasets[i].data.length>totalDataset){

                totalDataset = chart.config.data.datasets[i].data.length;

            }
        }


        for(i=0;i<totalDataset;i++){

            chart.data.labels.pop();
            chart.data.datasets.forEach((dataset) => {
                dataset.data.pop();
                dataset.label = "";
                dataset.backgroundColor ="";
            });
            chart.update();
        }
    }

