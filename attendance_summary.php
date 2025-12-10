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

// Get selected subject filter (for requirement 3)
$selectedSubject = isset($_GET['subject']) ? $_GET['subject'] : 'all';

// ------------ Fetch students -------------
$students = [];
$sqlStudents = "
    SELECT s.id AS student_id, sd.roll_number, s.full_name
    FROM students s
    INNER JOIN student_details sd ON sd.student_id = s.id
    ORDER BY sd.roll_number
";
$resStu = $conn->query($sqlStudents);
if ($resStu) {
    while ($row = $resStu->fetch_assoc()) {
        $students[] = $row;
    }
}

// ------------ Fetch subjects -------------
$subjects = [];
$resSub = $conn->query("SELECT subject_name FROM subjects ORDER BY subject_name");
if ($resSub) {
    while ($row = $resSub->fetch_assoc()) {
        $subjects[] = $row['subject_name'];
    }
}

// ------------ Fetch per-student per-subject attendance (all-time) -------------
$subjectData = []; // $subjectData[student_id][subject] = ['p' => x, 't' => y]

$sql = "
    SELECT student_id, subject,
           SUM(status='P') AS presents,
           COUNT(*) AS total_classes
    FROM attendance
    GROUP BY student_id, subject
";
$resAtt = $conn->query($sql);
if ($resAtt) {
    while ($row = $resAtt->fetch_assoc()) {
        $sid = $row['student_id'];
        $sub = $row['subject'];
        $subjectData[$sid][$sub] = [
            'p' => (int)$row['presents'],
            't' => (int)$row['total_classes']
        ];
    }
}

// ------------ Fetch overall per-student attendance (all subjects) -------------
$overallData = []; // $overallData[student_id] = ['p' => x, 't' => y]

$sqlOverall = "
    SELECT student_id,
           SUM(status='P') AS presents,
           COUNT(*) AS total_classes
    FROM attendance
    GROUP BY student_id
";
$resOv = $conn->query($sqlOverall);
if ($resOv) {
    while ($row = $resOv->fetch_assoc()) {
        $overallData[$row['student_id']] = [
            'p' => (int)$row['presents'],
            't' => (int)$row['total_classes']
        ];
    }
}

// ------------ If specific subject selected: build subject-wise list -------------
$subjectWise = []; // $subjectWise[student_id] = ['p' => x, 't' => y]

if ($selectedSubject !== 'all') {
    $stmt = $conn->prepare("
        SELECT student_id,
               SUM(status='P') AS presents,
               COUNT(*) AS total_classes
        FROM attendance
        WHERE subject = ?
        GROUP BY student_id
    ");
    if ($stmt) {
        $stmt->bind_param("s", $selectedSubject);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            $subjectWise[$row['student_id']] = [
                'p' => (int)$row['presents'],
                't' => (int)$row['total_classes']
            ];
        }
        $stmt->close();
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Attendance Summary</title>
    <link rel="stylesheet" href="dashboard.css">
    <style>
        body {
            background:#f4f5fb;
            font-family: Arial, sans-serif;
        }
        .wrap {
            max-width: 1100px;
            margin: 20px auto 40px;
            background:#fff;
            padding: 18px 22px 24px;
            border-radius: 14px;
            box-shadow:0 4px 16px rgba(0,0,0,0.1);
        }
        h2 {
            color:#1f3c88;
            margin-bottom:8px;
        }
        .filter-row {
            display:flex;
            justify-content:space-between;
            align-items:center;
            margin-bottom:12px;
            gap:10px;
            flex-wrap:wrap;
        }
        .filter-row form {
            display:flex;
            gap:8px;
            align-items:center;
            flex-wrap:wrap;
        }
        select, button {
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

        table {
            width:100%;
            border-collapse:collapse;
            font-size:13px;
            margin-top:10px;
        }
        th, td {
            border:1px solid #d0d7e2;
            padding:5px 6px;
            text-align:center;
        }
        th {
            background:#e4ebfb;
            color:#1f3c88;
            position:sticky;
            top:0;
        }
        td.name-cell, th.name-cell { text-align:left; }
        .low { color:#c0392b; font-weight:bold; }
        .good { color:#27ae60; font-weight:bold; }
        .med { color:#e67e22; font-weight:bold; }
        .sub-section-title {
            margin-top:24px;
            font-size:15px;
            color:#1f3c88;
        }
    </style>
</head>
<body>

<div class="wrap">
    <div class="filter-row">
        <h2>Attendance Summary (All Subjects)</h2>
        <form method="GET" action="attendance_summary.php">
            <span style="font-size:13px;">Subject-wise view:</span>
            <select name="subject">
                <option value="all">All Subjects</option>
                <?php foreach ($subjects as $sub): ?>
                    <option value="<?php echo htmlspecialchars($sub); ?>"
                        <?php if ($selectedSubject === $sub) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($sub); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Apply</button>
        </form>
    </div>

    <!-- 1. ALL SUBJECTS ATTENDANCE MATRIX -->
    <?php if (count($students) === 0): ?>
        <p>No students found.</p>
    <?php else: ?>
        <div style="overflow:auto; max-height:400px;">
            <table>
                <thead>
                    <tr>
                        <th>Roll No</th>
                        <th class="name-cell">Name</th>
                        <?php foreach ($subjects as $sub): ?>
                            <th><?php echo htmlspecialchars($sub); ?> (%)</th>
                        <?php endforeach; ?>
                        <th>Total (%)</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($students as $stu): 
                    $sid = $stu['student_id'];
                    $overallP = $overallData[$sid]['p'] ?? 0;
                    $overallT = $overallData[$sid]['t'] ?? 0;
                    $overallPerc = $overallT > 0 ? round($overallP * 100 / $overallT) : 0;
                    $overallClass = $overallPerc < 75 ? 'low' : ($overallPerc >= 90 ? 'good' : 'med');
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($stu['roll_number']); ?></td>
                        <td class="name-cell"><?php echo htmlspecialchars($stu['full_name']); ?></td>
                        <?php foreach ($subjects as $sub): 
                            $p = $subjectData[$sid][$sub]['p'] ?? 0;
                            $t = $subjectData[$sid][$sub]['t'] ?? 0;
                            $perc = $t > 0 ? round($p * 100 / $t) : 0;
                        ?>
                            <td><?php echo $t > 0 ? $perc.'%' : '-'; ?></td>
                        <?php endforeach; ?>
                        <td class="<?php echo $overallClass; ?>"><?php echo $overallPerc; ?>%</td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <!-- 3. SPECIFIC SUBJECT SUMMARY (each student's % in that subject) -->
    <?php if ($selectedSubject !== 'all'): ?>
        <h3 class="sub-section-title">
            Subject-wise Summary: <?php echo htmlspecialchars($selectedSubject); ?>
        </h3>
        <table>
            <thead>
                <tr>
                    <th>Roll No</th>
                    <th class="name-cell">Name</th>
                    <th>Present</th>
                    <th>Total Classes</th>
                    <th>Percentage</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($students as $stu):
                $sid = $stu['student_id'];
                $p = $subjectWise[$sid]['p'] ?? 0;
                $t = $subjectWise[$sid]['t'] ?? 0;
                $perc = $t > 0 ? round($p * 100 / $t) : 0;
                $cls = $perc < 75 ? 'low' : ($perc >= 90 ? 'good' : 'med');
            ?>
                <tr>
                    <td><?php echo htmlspecialchars($stu['roll_number']); ?></td>
                    <td class="name-cell"><?php echo htmlspecialchars($stu['full_name']); ?></td>
                    <td><?php echo $p; ?></td>
                    <td><?php echo $t; ?></td>
                    <td class="<?php echo $cls; ?>"><?php echo $t > 0 ? $perc.'%' : '-'; ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

</div>
</body>
</html>
