<?php
session_start();
if (!isset($_SESSION['staff_id'])) {
    header("Location: stafflogin.php");
    exit();
}

require 'config.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$roll = isset($_GET['roll']) ? trim($_GET['roll']) : '';
$monthInput = isset($_GET['month']) ? $_GET['month'] : ''; // format YYYY-MM

$student = null;
$overall = null;
$subjectWise = [];
$details = [];

if ($roll !== '' && $monthInput !== '') {

    // Parse month and year
    list($year, $month) = explode('-', $monthInput);
    $year = (int)$year;
    $month = (int)$month;

    // 1. Get student info by roll number
    $stmt = $conn->prepare("
        SELECT s.id AS student_id, s.full_name, sd.roll_number
        FROM students s
        INNER JOIN student_details sd ON sd.student_id = s.id
        WHERE sd.roll_number = ?
        LIMIT 1
    ");
    if (!$stmt) {
        die("Student lookup prepare failed: " . $conn->error);
    }
    $stmt->bind_param("s", $roll);
    $stmt->execute();
    $res = $stmt->get_result();
    $student = $res->fetch_assoc();
    $stmt->close();

    if ($student) {
        $sid = $student['student_id'];

        // 2. Overall monthly attendance
        $stmt = $conn->prepare("
            SELECT 
                SUM(status='P') AS presents,
                COUNT(*) AS total_classes
            FROM attendance
            WHERE student_id = ?
              AND MONTH(date) = ?
              AND YEAR(date) = ?
        ");
        if (!$stmt) {
            die("Overall prepare failed: " . $conn->error);
        }
        $stmt->bind_param("iii", $sid, $month, $year);
        $stmt->execute();
        $res = $stmt->get_result();
        $overall = $res->fetch_assoc() ?: ['presents'=>0,'total_classes'=>0];
        $stmt->close();

        // 3. Per-subject monthly summary
        $stmt = $conn->prepare("
            SELECT subject,
                   SUM(status='P') AS presents,
                   COUNT(*) AS total_classes
            FROM attendance
            WHERE student_id = ?
              AND MONTH(date) = ?
              AND YEAR(date) = ?
            GROUP BY subject
        ");
        if (!$stmt) {
            die("Subject summary prepare failed: " . $conn->error);
        }
        $stmt->bind_param("iii", $sid, $month, $year);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            $subjectWise[] = $row;
        }
        $stmt->close();

        // 4. Detailed day-wise records
        $stmt = $conn->prepare("
            SELECT date, period, subject, status
            FROM attendance
            WHERE student_id = ?
              AND MONTH(date) = ?
              AND YEAR(date) = ?
            ORDER BY date, period
        ");
        if (!$stmt) {
            die("Details prepare failed: " . $conn->error);
        }
        $stmt->bind_param("iii", $sid, $month, $year);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            $details[] = $row;
        }
        $stmt->close();
    }
}

$conn->close();

function fmtPerc($p, $t) {
    if ($t <= 0) return "0%";
    return round($p * 100 / $t) . "%";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Attendance Analysis</title>
    <link rel="stylesheet" href="dashboard.css">
    <style>
        body {
            background:#f4f5fb;
            font-family: Arial, sans-serif;
        }
        .wrap {
            max-width: 900px;
            margin: 20px auto 40px;
            background:#fff;
            padding:18px 22px 24px;
            border-radius:14px;
            box-shadow:0 4px 16px rgba(0,0,0,0.1);
        }
        h2 {
            color:#1f3c88;
            margin-bottom:8px;
        }
        form {
            display:flex;
            gap:10px;
            align-items:center;
            flex-wrap:wrap;
            margin-bottom:14px;
        }
        input, button {
            padding:6px 10px;
            border-radius:6px;
            border:1px solid #c9d0e3;
            font-size:13px;
        }
        button {
            background:#1f3c88;
            color:#fff;
            border:none;
            font-weight:600;
            cursor:pointer;
        }
        button:hover { background:#163473; }
        .info {
            font-size:13px;
            margin-bottom:10px;
        }
        .info b { color:#1f3c88; }
        table {
            width:100%;
            border-collapse:collapse;
            font-size:13px;
            margin-top:8px;
        }
        th, td {
            border:1px solid #d0d7e2;
            padding:5px 6px;
            text-align:center;
        }
        th {
            background:#e4ebfb;
            color:#1f3c88;
        }
        .name-cell { text-align:left; }
        .sec-title {
            margin-top:18px;
            font-size:14px;
            color:#1f3c88;
            font-weight:bold;
        }
        .status-p { color:#27ae60; font-weight:bold; }
        .status-a { color:#c0392b; font-weight:bold; }
    </style>
</head>
<body>

<div class="wrap">
    <h2>Student Attendance Analysis</h2>

    <form method="GET" action="student_attendance_analysis.php">
        <input type="text" name="roll" placeholder="Roll Number" value="<?php echo htmlspecialchars($roll); ?>" required>
        <input type="month" name="month" value="<?php echo htmlspecialchars($monthInput ?: date('Y-m')); ?>" required>
        <button type="submit">View</button>
    </form>

    <?php if ($roll !== '' && $monthInput !== ''): ?>
        <?php if (!$student): ?>
            <p>No student found with roll number <b><?php echo htmlspecialchars($roll); ?></b>.</p>
        <?php else: 
            $p = (int)($overall['presents'] ?? 0);
            $t = (int)($overall['total_classes'] ?? 0);
            $overallPerc = $t > 0 ? round($p * 100 / $t) : 0;
        ?>
            <div class="info">
                <b>Name:</b> <?php echo htmlspecialchars($student['full_name']); ?> &nbsp;|&nbsp;
                <b>Roll:</b> <?php echo htmlspecialchars($student['roll_number']); ?> &nbsp;|&nbsp;
                <b>Month:</b> <?php echo htmlspecialchars($monthInput); ?> &nbsp;|&nbsp;
                <b>Overall Attendance:</b> <?php echo $overallPerc; ?>%
            </div>

            <!-- Per-subject summary -->
            <div class="sec-title">Subject-wise Attendance (This Month)</div>
            <?php if (count($subjectWise) === 0): ?>
                <p>No attendance records for this month.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Subject</th>
                            <th>Present</th>
                            <th>Total Classes</th>
                            <th>Percentage</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($subjectWise as $row): 
                        $pp = (int)$row['presents'];
                        $tt = (int)$row['total_classes'];
                        $perc = $tt > 0 ? round($pp * 100 / $tt) : 0;
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['subject']); ?></td>
                            <td><?php echo $pp; ?></td>
                            <td><?php echo $tt; ?></td>
                            <td><?php echo $perc; ?>%</td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

            <!-- Detailed records -->
            <div class="sec-title">Day-wise Attendance (This Month)</div>
            <?php if (count($details) === 0): ?>
                <p>No period-wise records for this month.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Period</th>
                            <th>Subject</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($details as $row): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['date']); ?></td>
                            <td><?php echo htmlspecialchars($row['period']); ?></td>
                            <td><?php echo htmlspecialchars($row['subject']); ?></td>
                            <td class="<?php echo $row['status'] === 'P' ? 'status-p' : 'status-a'; ?>">
                                <?php echo $row['status']; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

        <?php endif; ?>
    <?php endif; ?>

</div>
</body>
</html>
