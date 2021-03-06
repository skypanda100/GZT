<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/26
 * Time: 19:59
 */

require_once "../db/pgsql.php";

$start_date = null;
if(isset($_POST["start_date"]))
{
    $start_date = $_POST["start_date"];
}

$end_date = null;
if(isset($_POST["end_date"]))
{
    $end_date = $_POST["end_date"];
}

$db = new pgsql("127.0.0.1", "5432", "postgres", "postgres", "123456");
//$db = new pgsql("192.168.233.138", "15432", "postgres", "postgres", "123456");

$db->connect();
$sql = "";
if(!is_null($start_date) && !is_null($end_date))
{
    $sql = "select * from status_sleep where date >= '$start_date'and date <= '$end_date'order by date";

}
else if(!is_null($start_date) && is_null($end_date))
{
    $sql = "select * from status_sleep where date >= '$start_date' order by date";
}
else if(is_null($start_date) && !is_null($end_date))
{
    $sql = "select * from status_sleep where date <= '$end_date'order by date";
}
else
{
    $sql = "select * from status_sleep order by date";
}


$s_date = null;
$e_date = null;
$gg_date_r = array();
$gg_sleep_start_r = array();
$gg_sleep_end_r = array();
$gg_deep_r = array();
$gg_shallow_r = array();
$zdt_date_r = array();
$zdt_sleep_start_r = array();
$zdt_sleep_end_r = array();
$zdt_deep_r = array();
$zdt_shallow_r = array();

$result = $db->query($sql);
while(($row = $db->fetchRow()) != NULL)
{
    $person = $row[0];
    $date = $row[1];
    $start = $row[2];
    $end = $row[3];
    $deepStart01 = $row[4];
    $deepEnd01 = $row[5];
    $deepStart02 = $row[6];
    $deepEnd02 = $row[7];
    $deepStart03 = $row[8];
    $deepEnd03 = $row[9];
    $deepStart04 = $row[10];
    $deepEnd04 = $row[11];
    $deepStart05 = $row[12];
    $deepEnd05 = $row[13];
    $deepStart06 = $row[14];
    $deepEnd06 = $row[15];
    $deepStart07 = $row[16];
    $deepEnd07 = $row[17];
    $deepStart08 = $row[18];
    $deepEnd08 = $row[19];
    $deepStart09 = $row[20];
    $deepEnd09 = $row[21];
    $deepStart10 = $row[22];
    $deepEnd10 = $row[23];
    $awakeStart01 = $row[24];
    $awakeEnd01 = $row[25];
    $awakeStart02 = $row[26];
    $awakeEnd02 = $row[27];
    $awakeStart03 = $row[28];
    $awakeEnd03 = $row[29];
    $awakeStart04 = $row[30];
    $awakeEnd04 = $row[31];

    $sum = strtotime($end) - strtotime($start);

    $deep = strtotime($deepEnd01) - strtotime($deepStart01);
    $deep += strtotime($deepEnd02) - strtotime($deepStart02);
    $deep += strtotime($deepEnd03) - strtotime($deepStart03);
    $deep += strtotime($deepEnd04) - strtotime($deepStart04);
    $deep += strtotime($deepEnd05) - strtotime($deepStart05);
    $deep += strtotime($deepEnd06) - strtotime($deepStart06);
    $deep += strtotime($deepEnd07) - strtotime($deepStart07);
    $deep += strtotime($deepEnd08) - strtotime($deepStart08);
    $deep += strtotime($deepEnd09) - strtotime($deepStart09);
    $deep += strtotime($deepEnd10) - strtotime($deepStart10);

    $awake = strtotime($awakeEnd01) - strtotime($awakeStart01);
    $awake += strtotime($awakeEnd02) - strtotime($awakeStart02);
    $awake += strtotime($awakeEnd03) - strtotime($awakeStart03);
    $awake += strtotime($awakeEnd04) - strtotime($awakeStart04);

    $shallow = $sum - $deep - $awake;

    $date_s = substr($date, 0, 10);

    if($person == 0)
    {
        array_push($gg_date_r, $date_s);
        array_push($gg_deep_r, $deep / 60 / 60.0);
        array_push($gg_shallow_r, $shallow / 60 / 60.0);
        array_push($gg_sleep_start_r, strtotime($start));
        array_push($gg_sleep_end_r, strtotime($end));
    }
    else
    {
        array_push($zdt_date_r, $date_s);
        array_push($zdt_deep_r, $deep / 60 / 60.0);
        array_push($zdt_shallow_r, $shallow / 60 / 60.0);
        array_push($zdt_sleep_start_r, strtotime($start));
        array_push($zdt_sleep_end_r, strtotime($end));
    }

    if(is_null($s_date))
    {
        $s_date = strtotime($date);
    }
    else
    {
        $e_date = strtotime($date);
    }
}

$db->free();

//day
$date_day_r = array();
$gg_deep_day_r = array();
$gg_shallow_day_r = array();
$zdt_deep_day_r = array();
$zdt_shallow_day_r = array();

//week
$date_week_r = array();
$gg_deep_week = 0;
$gg_deep_week_r = array();
$gg_shallow_week_r = array();
$zdt_shallow_week = 0;
$zdt_deep_week_r = array();
$zdt_shallow_week_r = array();
$is_week_done = false;

//month
$date_month_r = array();
$gg_deep_month = 0;
$gg_deep_month_r = array();
$gg_shallow_month_r = array();
$zdt_shallow_month = 0;
$zdt_deep_month_r = array();
$zdt_shallow_month_r = array();
$is_month_done = false;

//sleep time
$gg_sleep_start_r_r = array();
$gg_sleep_end_r_r = array();
$zdt_sleep_start_r_r = array();
$zdt_sleep_end_r_r = array();

foreach ($gg_sleep_start_r as $date)
{
    $temp = array();
    array_push($temp, strtotime(strftime("%Y-%m-%d", $date)));
    $hour = intval(strftime("%H", $date), 10);
    $minute = intval(strftime("%M", $date), 10);
    array_push($temp, $hour * 60 + $minute);
    array_push($gg_sleep_start_r_r, $temp);
}

foreach ($gg_sleep_end_r as $date)
{
    $temp = array();
    array_push($temp, strtotime(strftime("%Y-%m-%d", $date)));
    $hour = intval(strftime("%H", $date), 10);
    $minute = intval(strftime("%M", $date), 10);
    array_push($temp, $hour * 60 + $minute);
    array_push($gg_sleep_end_r_r, $temp);
}

foreach ($zdt_sleep_start_r as $date)
{
    $temp = array();
    array_push($temp, strtotime(strftime("%Y-%m-%d", $date)));
    $hour = intval(strftime("%H", $date), 10);
    $minute = intval(strftime("%M", $date), 10);
    array_push($temp, $hour * 60 + $minute);
    array_push($zdt_sleep_start_r_r, $temp);
}

foreach ($zdt_sleep_end_r as $date)
{
    $temp = array();
    array_push($temp, strtotime(strftime("%Y-%m-%d", $date)));
    $hour = intval(strftime("%H", $date), 10);
    $minute = intval(strftime("%M", $date), 10);
    array_push($temp, $hour * 60 + $minute);
    array_push($zdt_sleep_end_r_r, $temp);
}

$tmp_date = $s_date;
$day_seconds = 24 * 60 * 60;
do{
    $date_week = date("w", $tmp_date);
    $date_month = date("t", $tmp_date);
    $date_day = date("d", $tmp_date);
    $date = strftime("%Y-%m-%d", $tmp_date);
    $tmp_gg_deep = 0;
    $tmp_gg_shallow = 0;
    $tmp_zdt_deep = 0;
    $tmp_zdt_shallow = 0;

    //day
    if(in_array($date, $gg_date_r, true))
    {
        $index = array_search($date, $gg_date_r, true);
        $tmp_gg_deep = $gg_deep_r[$index];
        $tmp_gg_shallow = $gg_shallow_r[$index];
    }

    if(in_array($date, $zdt_date_r, true))
    {
        $index = array_search($date, $zdt_date_r, true);
        $tmp_zdt_deep = $zdt_deep_r[$index];
        $tmp_zdt_shallow = $zdt_shallow_r[$index];
    }

    array_push($date_day_r, $date);
    array_push($gg_deep_day_r, $tmp_gg_deep);
    array_push($gg_shallow_day_r, $tmp_gg_shallow);
    array_push($zdt_deep_day_r, $tmp_zdt_deep);
    array_push($zdt_shallow_day_r, $tmp_zdt_shallow);

    //week
    $gg_deep_week += $tmp_gg_deep;
    $gg_shallow_week += $tmp_gg_shallow;
    $zdt_deep_week += $tmp_zdt_deep;
    $zdt_shallow_week += $tmp_zdt_shallow;
    $is_week_done = false;

    if($date_week == 0)
    {
        array_push($date_week_r, $date);
        array_push($gg_deep_week_r, $gg_deep_week);
        array_push($gg_shallow_week_r, $gg_shallow_week);
        array_push($zdt_deep_week_r, $zdt_deep_week);
        array_push($zdt_shallow_week_r, $zdt_shallow_week);

        $gg_deep_week = 0;
        $gg_shallow_week = 0;
        $zdt_deep_week = 0;
        $zdt_shallow_week = 0;
        $is_week_done = true;
    }

    //month
    $gg_deep_month += $tmp_gg_deep;
    $gg_shallow_month += $tmp_gg_shallow;
    $zdt_deep_month += $tmp_zdt_deep;
    $zdt_shallow_month += $tmp_zdt_shallow;
    $is_month_done = false;

    if($date_day == $date_month)
    {
        array_push($date_month_r, $date);
        array_push($gg_deep_month_r, $gg_deep_month);
        array_push($gg_shallow_month_r, $gg_shallow_month);
        array_push($zdt_deep_month_r, $zdt_deep_month);
        array_push($zdt_shallow_month_r, $zdt_shallow_month);

        $gg_deep_month = 0;
        $gg_shallow_month = 0;
        $zdt_deep_month = 0;
        $zdt_shallow_month = 0;
        $is_month_done = true;
    }

    $tmp_date = $tmp_date + $day_seconds;
}
while(($tmp_date - $e_date) <= 0);

if(!$is_week_done)
{
    array_push($date_week_r, $date);
    array_push($gg_deep_week_r, $gg_deep_week);
    array_push($gg_shallow_week_r, $gg_shallow_week);
    array_push($zdt_deep_week_r, $zdt_deep_week);
    array_push($zdt_shallow_week_r, $zdt_shallow_week);
}

if(!$is_month_done)
{
    array_push($date_month_r, $date);
    array_push($gg_deep_month_r, $gg_deep_month);
    array_push($gg_shallow_month_r, $gg_shallow_month);
    array_push($zdt_deep_month_r, $zdt_deep_month);
    array_push($zdt_shallow_month_r, $zdt_shallow_month);
}

$data_r = array("date_day" => $date_day_r, "gg_deep_day" => $gg_deep_day_r, "gg_shallow_day" => $gg_shallow_day_r, "zdt_deep_day" => $zdt_deep_day_r, "zdt_shallow_day" => $zdt_shallow_day_r
                ,"date_week" => $date_week_r, "gg_deep_week" => $gg_deep_week_r, "gg_shallow_week" => $gg_shallow_week_r, "zdt_deep_week" => $zdt_deep_week_r, "zdt_shallow_week" => $zdt_shallow_week_r
                ,"date_month" => $date_month_r, "gg_deep_month" => $gg_deep_month_r, "gg_shallow_month" => $gg_shallow_month_r, "zdt_deep_month" => $zdt_deep_month_r, "zdt_shallow_month" => $zdt_shallow_month_r
                ,"gg_sleep_start" => $gg_sleep_start_r_r,"gg_sleep_end" => $gg_sleep_end_r_r
                ,"zdt_sleep_start" => $zdt_sleep_start_r_r,"zdt_sleep_end" => $zdt_sleep_end_r_r);

echo json_encode($data_r);
?>