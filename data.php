<?php
    include "connection.php";
    //error_reporting(E_ALL ^ E_NOTICE);
        if (isset($_POST["poststart"]) || isset($_POST["postend"]))
        {
            $date_start = $_POST["poststart"];
            $date_end = $_POST["postend"];
            $period = $_POST["postperiod"];
            $operator = $_POST["postoperator"];
        }
        else
        {
            $date_end = date("Y-m-d H:i:s");
            $date_start = date("Y-m-d H:i:s", strtotime ( '-2 day' , strtotime ( $date_end ))) ;
            $period = "";
            $operator = "Avea";
        }

        if ($operator == "Avea") {
            $operator = "avea_reports";
        } elseif ($operator == "Turkcell") {
            $operator = "turkcell_reports";
        } elseif ($operator == "Vodafone") {
            $operator = "vodafone_reports";
        } elseif ($operator == "Uluslararası") {
            $operator = "international_reports";
        } elseif ($operator == "Yerli") {
            $operator = "domestic_reports";
        }

        if ($period == "Günlük")
        {
            $date_start = $_POST["poststart"];
            $date_end = date('Y-m-d H:i:s',strtotime($date_start . "+2 days"));
        }

        if ($period == "Aylık")
        {
            $time = "DATE_FORMAT(calldate, '%M')";
        } else {
            $time = "DATE_FORMAT(calldate, '%d %M %Y')";
        }


/*$sql="SELECT  DATE_FORMAT(calldate, '%H:%i') as time, COUNT(answerdate) as answer, COUNT(enddate) as end, calldate, answerdate, enddate,  ROUND(SUM(billsec) / 60, 2) as minute
              FROM avea_reports
              GROUP BY uniqid HAVING calldate BETWEEN '2017-06-01' AND '2017-06-02' ";*/

    $sql="SELECT  DATE_FORMAT(calldate, '%d %M %Y') as time, COUNT(answerdate) as answer, COUNT(enddate) * (-1) as end, 
                  DATE_FORMAT(calldate, '%M') as month,
                  calldate, answerdate, enddate,  ROUND((SUM(billsec) / 60), 2) as minute
                  FROM $operator
                  GROUP BY $time HAVING calldate BETWEEN '".$date_start."' AND '".$date_end."' ORDER BY calldate";

    /*$sql="SELECT  DATE_FORMAT(calldate, '%H:%i') as time, COUNT(answerdate) as answer, COUNT(enddate) * (-1) as end, calldate, answerdate, enddate,  ROUND(SUM(billsec) / 60, 2) as minute
                  FROM domestic_reports
                  GROUP BY uniqid HAVING calldate BETWEEN '2016-10-31' AND '2016-11-01' ";*/

    $data = array();
    if ($result=mysqli_query($con,$sql))
    {
        while($r = mysqli_fetch_assoc($result))
        {
            $data[] = $r;
        }
        print json_encode($data, JSON_FORCE_OBJECT);

        mysqli_free_result($result);
    } else {
        print mysqli_error ($con);
    }

    mysqli_close($con);
?>


