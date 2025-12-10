<?php
session_start();
if (!isset($_SESSION['staff_id'])) {
    header("Location: stafflogin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Time Table</title>
    <link rel="stylesheet" href="dashboard.css">
    <style>
        body {
            background: #f4f5fb;
            font-family: Arial, sans-serif;
        }
        .tt-wrapper {
            max-width: 1100px;
            margin: 30px auto;
            background: #fff;
            padding: 20px 24px 26px;
            border-radius: 14px;
            box-shadow: 0 4px 18px rgba(0,0,0,0.12);
        }
        .tt-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }
        .tt-header h2 {
            margin: 0;
            color: #1f3c88;
        }
        .back-link {
            text-decoration: none;
            font-size: 14px;
            color: #1f3c88;
        }
        .tt-meta {
            font-size: 13px;
            color: #666;
            margin-bottom: 16px;
        }
        table.timetable {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            text-align: center;
        }
        .timetable th,
        .timetable td {
            border: 1px solid #d0d7e2;
            padding: 6px 4px;
        }
        .timetable th {
            background: #e4ebfb;
            color: #1f3c88;
            font-weight: 600;
        }
        .timetable th span.time {
            display: block;
            font-size: 11px;
            color: #555;
            margin-top: 2px;
        }
        .timetable td.day {
            font-weight: bold;
            background: #f5f7ff;
        }
        .timetable td.break,
        .timetable th.break {
            background: #f3f3f3;
            font-weight: 600;
        }
    </style>
</head>
<body>

<div class="tt-wrapper">
    <div class="tt-header">
        <h2>Time Table</h2>
        <a href="staffdashboard.php" class="back-link">← Back to Dashboard</a>
    </div>

    <div class="tt-meta">
        Year &amp; Sem: II B.Tech II Sem &nbsp;|&nbsp;
        Dept: CSE (Data Science) &nbsp;|&nbsp;
        Class Teacher: Mrs. A. Triveni &nbsp;|&nbsp;
        Lecture Hall: 211
    </div>

    <table class="timetable">
        <thead>
            <tr>
                <th>Day</th>
                <th>Period 1<span class="time">9:00 – 10:05</span></th>
                <th>Period 2<span class="time">10:05 – 11:10</span></th>
                <th class="break">Break<br><span class="time">11:10 – 11:25</span></th>
                <th>Period 3<span class="time">11:25 – 12:30</span></th>
                <th class="break">Lunch<br><span class="time">12:30 – 1:20</span></th>
                <th>Period 4<span class="time">1:20 – 2:20</span></th>
                <th>Period 5<span class="time">2:20 – 3:20</span></th>
                <th class="break">Break<br><span class="time">3:20 – 3:30</span></th>
                <th>Period 6<span class="time">3:30 – 4:30</span></th>
            </tr>
        </thead>
        <tbody>
            <!-- 👇 Fill subjects exactly as in your paper timetable -->
            <tr>
                <td class="day">MON</td>
                <td>OT</td>
                <td>SMDS</td>
                <td class="break">–</td>
                <td>DE</td>
                <td class="break">–</td>
                <td>DL&amp;CO</td>
                <td>DBMS</td>
                <td class="break">–</td>
                <td>DTI</td>
            </tr>
            <tr>
                <td class="day">TUE</td>
                <td>REAS</td>
                <td>SMDS</td>
                <td class="break">–</td>
                <td colspan="2">DBMS LAB</td>
                <td>DL&amp;CO</td>
                <td>DBMS</td>
                <td class="break">–</td>
                <td>OT</td>
            </tr>
            <tr>
                <td class="day">WED</td>
                <td>DTI</td>
                <td>APT</td>
                <td class="break">–</td>
                <td>DE</td>
                <td class="break">LUNCH</td>
                <td>DE</td>
                <td>COMM</td>
                <td class="break">–</td>
                <td>COMM</td>
            </tr>
            <tr>
                <td class="day">THU</td>
                <td>DE</td>
                <td>SMDS</td>
                <td class="break">–</td>
                <td>SMDS</td>
                <td class="break">–</td>
                <td colspan="3">EDA LAB</td>
                <td>–</td>
            </tr>
            <tr>
                <td class="day">FRI</td>
                <td>DBMS</td>
                <td>TECH</td>
                <td class="break">–</td>
                <td>SMDS</td>
                <td class="break">LUNCH</td>
                <td>OT</td>
                <td>DL&amp;CO</td>
                <td class="break">–</td>
                <td>DE LAB</td>
            </tr>
            <tr>
                <td class="day">SAT</td>
                <td>DL&amp;CO</td>
                <td>OT</td>
                <td class="break">–</td>
                <td>DE</td>
                <td class="break">LUNCH</td>
                <td>EDA</td>
                <td class="break">BREAK</td>
                <td class="break">–</td>
                <td>TECH LAB</td>
            </tr>
        </tbody>
    </table>
</div>

</body>
</html>
