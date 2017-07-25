<!doctype html>
<html>
<head>
    <title>Line Chart</title>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

    <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.bundle.js"></script>
    <script src="assets/datepicker-tr.js"></script>
    <script src="assets/utils.js"></script>
    <style>
        .datepickers{
            display: flex;
            justify-content: flex-end;
        }
        .datepickers input{
            margin-right: 5px;
            -webkit-border-radius: 3px;
            -moz-border-radius: 3px;
            border-radius: 3px;
            border: 1px solid #d4d2d2;
            padding: 5px;
        }
        .dropbtn {
            background-color: rgb(70, 101, 150);
            color: white;
            padding: 6px;
            margin-right: 5px;
            -webkit-border-radius: 3px;
            -moz-border-radius: 3px;
            border-radius: 3px;
            font-size: 14px;
            border: none;
            cursor: pointer;
            font-family: arial;
            display: block;
        }

        .dropbtn:hover, .dropbtn:focus {
            background-color: rgb(87, 118, 168);
        }
        .btn-operator {
            background-color: rgb(226, 162, 113);
            color: white;
            padding: 6px;
            margin-right: 5px;
            -webkit-border-radius: 3px;
            -moz-border-radius: 3px;
            border-radius: 3px;
            font-size: 14px;
            border: none;
            cursor: pointer;
            font-family: arial;
            display: block;
        }

        .btn-operator:hover, .btn-operator:focus {
            background-color: rgb(239, 184, 141);
        }

        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
        }

        .dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            font-size: 14px;
            font-family: arial;
            cursor: pointer;
        }

        .dropdown-content a:hover {background-color: #f1f1f1}


        .dropdown-operator-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
        }

        .dropdown-operator-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            font-size: 14px;
            font-family: arial;
            cursor: pointer;
        }

        .dropdown-operator-content a:hover {background-color: #f1f1f1}

        .show {display:block;}

        #btn-send {
            background-color: #4CAF50;
            color: white;
            padding: 6px;
            margin-right: 5px;
            -webkit-border-radius: 3px;
            -moz-border-radius: 3px;
            border-radius: 3px;
            font-size: 14px;
            border: none;
            cursor: pointer;
            font-family: arial;
        }
        #btn-send:disabled {
            background-color: #666666;
            color: #cdcdcd;
            padding: 6px;
            margin-right: 5px;
            -webkit-border-radius: 3px;
            -moz-border-radius: 3px;
            border-radius: 3px;
            font-size: 14px;
            border: none;
            cursor: pointer;
            font-family: arial;
        }
        canvas {
            -moz-user-select: none;
            -webkit-user-select: none;
            -ms-user-select: none;
            color: #878787;
        }
        #report {
            margin-top:20px;
        }
        #report div {
            float: left;
            width: 33%;
            text-align: center;
            font-family: arial;
            font-size: 12px;
            color: #878787;
            padding-bottom: 5px;
        }
        #report div:first-child  {
            border-bottom: 2px solid rgba(119, 196, 74, 0.5);
        }
        #report div:nth-child(2) {
            border-bottom: 2px solid rgba(206, 39, 39, 0.5);
        }
        #report div:last-child {
            border-bottom: 2px solid rgba(81, 139, 232, 0.5);
        }
    </style>
</head>
<body>

<?php
include "connection.php";
?>
<div style="width:100%;">
    <div class="datepickers">
        <div class="dropdown">
            <a onclick="dropDownOperator()" class="btn-operator">Operatör</a>
            <div id="dropDownOperator" class="dropdown-operator-content">
                <a class="dd-operator">Avea</a>
                <a class="dd-operator">Vodafone</a>
                <a class="dd-operator">Turkcell</a>
                <a class="dd-operator">Yerli</a>
                <a class="dd-operator">Uluslararası</a>
            </div>
        </div>
        <div class="dropdown">
            <a onclick="dropDownPeriod()" class="dropbtn">Zaman Dilimi</a>
            <div id="dropDownPeriod" class="dropdown-content">
                <a class="dd-period">Günlük</a>
                <a class="dd-period">Aylık</a>
                <a class="dd-period">Uzun Süreli</a>
            </div>
        </div>
        <input type="text" id="datepicker-start" name="datepicker-start" placeholder="Başlangıç Tarihi Seçiniz">
        <input type="text" id="datepicker-end" name="datepicker-start" placeholder="Bitiş Tarihi Seçiniz">
        <input type="button" id="btn-send" value="Göster" disabled>
    </div>
    <div id="report"></div>
    <div class="canvas-container">
        <canvas id="canvas"></canvas>
    </div>
</div>

<script>
    var barGraph = null;
    function createGraph(time , answer, end, total, answerTotal, sumTotal, endTotal) {
        var chartdata = {
            labels: time,
            datasets: [
                {
                    label: 'Açılan Aramalar',
                    backgroundColor: 'rgba(119, 196, 74, 0.5)',
                    borderColor: 'rgba(119, 196, 74, 0.75)',
                    hoverBackgroundColor: 'rgba(119, 196, 74, 1)',
                    hoverBorderColor: 'rgba(119, 196, 74, 1)',
                    data: answer
                },
                {
                    label: 'Kapanan Aramalar',
                    backgroundColor: 'rgba(206, 39, 39, 0.5)',
                    borderColor: 'rgba(206, 39, 39, 0.5)',
                    hoverBackgroundColor: 'rgba(206, 39, 39, 1)',
                    hoverBorderColor: 'rgba(206, 39, 39, 1)',
                    data: end
                },
                {
                    label: 'Toplam Arama Süresi',
                    backgroundColor: 'rgba(81, 139, 232, 0.5)',
                    borderColor: 'rgba(81, 139, 232, 1)',
                    hoverBackgroundColor: 'rgba(81, 139, 232, 1)',
                    hoverBorderColor: 'rgba(81, 139, 232, 1)',
                    data: total
                }
            ]
        };

        var ctx = $("#canvas");
        if(barGraph != null){
            barGraph.destroy();
        }
        barGraph = new Chart(ctx, {
            type: 'line',
            data: chartdata
        });
        $("#report").html("<div><b>Açılan Aramalar: </b> " + answerTotal + "</div><div> <b>Kapanan Aramlar: </b> " + endTotal * (-1) + " </div><div> <b>Toplam Arama Süresi: </b>  " + sumTotal.toFixed(2) + " (dk) </div>");
        $("#date").html("<div>" + answerTotal + "</div><div> " + sumTotal + " </div>");

    }
    function dateReverse(text){
        var result = "";
        var i = 0;
        text = text.replace(".", "");
        var day = text.slice(0,2);
        var month = text.slice(2,4);
        var year = text.slice(5,10);
        result = year + "-" + month + "-" + day;
        console.log(day);
        console.log(month);
        console.log(year);
        console.log(result);
        return result;
    }

    $(document).ready(function(){
        var res = $.ajax({
                type: "GET",
                url: "data.php",
                async: true,
                success: function (datas){
                    var data = datas;
                    console.log(data);
                    var myData = JSON.parse(data);
                    console.log("retret"+ myData);
                    var end = [],
                        answer = [],
                        time = [],
                        total = [],
                        calldate = [],
                        month = [],
                        endTotal = 0,
                        sumTotal = 0,
                        answerTotal = 0;
                    console.log('myData start');
                    console.log(myData);
                    console.log('myData end');

                    for (var i in myData) {
                        answer.push(myData[i].answer);
                        time.push(myData[i].time);
                        end.push(myData[i].end);
                        total.push(myData[i].minute);
                        calldate.push(myData[i].calldate);
                        month.push(myData[i].month);
                        answerTotal += parseInt(myData[i].answer);
                        endTotal += parseInt(myData[i].end);
                        sumTotal += parseFloat(myData[i].minute);
                    }
                    createGraph(time, answer, end, total, answerTotal, sumTotal, endTotal);
                },
                error: function(request, status, error){
                    console.log("Hata" + error + " " + request + " " + status);
                }
            });
    });


    function dropDownPeriod() {
        document.getElementById("dropDownPeriod").classList.toggle("show");
    }

    function dropDownOperator() {
        document.getElementById("dropDownOperator").classList.toggle("show");
    }

   $( function() {
        $( "#datepicker-start" ).datepicker();
        $( "#datepicker-end" ).datepicker();
        $.datepicker.setDefaults( $.datepicker.regional[ "tr" ] );
    } );

    window.onclick = function(event) {
        if (!event.target.matches('.dropbtn')) {

            var dropdowns = document.getElementsByClassName("dropdown-content");
            var i;
            for (i = 0; i < dropdowns.length; i++) {
                var openDropdown = dropdowns[i];
                if (openDropdown.classList.contains('show')) {
                    openDropdown.classList.remove('show');
                }
            }
        }

        if (!event.target.matches('.btn-operator')) {

            var dropdowns = document.getElementsByClassName("dropdown-operator-content");
            var i;
            for (i = 0; i < dropdowns.length; i++) {
                var openDropdown = dropdowns[i];
                if (openDropdown.classList.contains('show')) {
                    openDropdown.classList.remove('show');
                }
            }
        }
    }

    var operator, period;
    $(".dropdown-operator-content a").click(function() {
        operator = this.text;
        $(".btn-operator").html("Operator: " + operator);

        $(".dropdown-content a").click(function() {
            period = this.text;
            $("#btn-send").prop("disabled", "");
            $(".dropbtn").html("Zaman Dilimi: " + period);

            if (period == "Günlük") {
                $("#datepicker-end").prop("disabled", "true");
            } else {
                $("#datepicker-end").prop("disabled", "");
            }
            console.log(period);
        });
        console.log(operator);
    });


    $("#btn-send").click(function(callback) {
        var datepickerstart = $('#datepicker-start').val();
        var datepickerend = $('#datepicker-end').val();
        var periodtime = period;
        var operatortype = operator;

        datepickerstart = dateReverse(datepickerstart);
        datepickerend = dateReverse(datepickerend);
        console.log(operator + " " + period + " " + datepickerstart + " " + datepickerend);
        function getGraph(callback) {
            var box = {
                'poststart': datepickerstart,
                'postend': datepickerend,
                'postperiod': periodtime,
                'postoperator': operatortype
            };

            console.log(box);
            var res = $.ajax({
                type: "POST",
                url: "data.php",
                data: box,
                async: true,
                success: function (datas){

                    var data = datas;
                    console.log(data);
                    var myData = JSON.parse(data);
                    console.log("retret"+ myData);
                    var end = [],
                        answer = [],
                        time = [],
                        total = [],
                        calldate = [],
                        month = [],
                        endTotal = 0,
                        sumTotal = 0,
                        answerTotal = 0;
                    console.log('myData start');
                    console.log(myData);
                    console.log('myData end');

                    for (var i in myData) {
                        answer.push(myData[i].answer);
                        time.push(myData[i].time);
                        end.push(myData[i].end);
                        total.push(myData[i].minute);
                        calldate.push(myData[i].calldate);
                        month.push(myData[i].month);
                        answerTotal += parseInt(myData[i].answer);
                        endTotal += parseInt(myData[i].end);
                        sumTotal += parseFloat(myData[i].minute);
                    }
                    createGraph(time, answer, end, total, answerTotal, sumTotal, endTotal);
                },
                error: function(request, status, error){
                    console.log("Hata" + error + " " + request + " " + status);
                }
            });
            console.log(callback);
        }
            getGraph(callback);



    });




</script>
</body>

</html>
