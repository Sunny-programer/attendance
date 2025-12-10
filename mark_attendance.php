<?php
session_start();

// Must be logged in as staff
if (!isset($_SESSION['staff_id'])) {
    header("Location: stafflogin.php");
    exit();
}

$staff_id = $_SESSION['staff_id'];

// DB connection
require 'config.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

/*
--------------------------------------------------
PART 1: SAVE ATTENDANCE (when second form is submitted)
--------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_attendance'])) {

    $branch  = $_POST['branch'];
    $year    = $_POST['year'];
    $section = $_POST['section'];
    $date    = $_POST['date'];
    $period  = $_POST['period'];
    $subject = $_POST['subject'];

    $student_ids = isset($_POST['student_ids']) ? $_POST['student_ids'] : [];
    $present_ids = isset($_POST['present_ids']) ? $_POST['present_ids'] : [];

    if (empty($student_ids)) {
        echo "<script>alert('No students found to mark attendance.'); window.location.href='mark_attendance_select.php';</script>";
        exit();
    }

    // Optional: delete previous attendance for same class + date + period for this staff (to allow re-marking)
    $del = $conn->prepare("
        DELETE FROM attendance 
        WHERE staff_id = ? AND date = ? AND period = ? 
          AND branch = ? AND year = ? AND section = ?
    ");
    $del->bind_param("isssss", $staff_id, $date, $period, $branch, $year, $section);
    $del->execute();
    $del->close();

    // Insert new attendance records
    $stmt = $conn->prepare("
        INSERT INTO attendance 
        (student_id, staff_id, date, period, branch, year, section, subject, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    foreach ($student_ids as $sid) {
        $status = in_array($sid, $present_ids) ? 'P' : 'A';
        $stmt->bind_param("iisssssss", $sid, $staff_id, $date, $period, $branch, $year, $section, $subject, $status);
        $stmt->execute();
    }

    $stmt->close();
    $conn->close();

    // Redirect to dashboard with success message
    echo "<script>alert('Attendance saved successfully!'); window.location.href='staffdashboard.php';</script>";
    exit();
}

/*
--------------------------------------------------
PART 2: FIRST LOAD – SHOW STUDENT LIST WITH CHECKBOXES
Called from mark_attendance_select.php
--------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: mark_attendance_select.php");
    exit();
}

// Get class info from previous form
$branch  = $_POST['branch'];
$year    = $_POST['year'];
$section = $_POST['section'];
$date    = $_POST['date'];
$period  = $_POST['period'];
$subject = $_POST['subject'];

// Fetch students of that class
// Fetch all students (single section system)
$students = [];

$sql = "
    SELECT s.id AS student_id,
           sd.roll_number,
           s.full_name
    FROM students s
    INNER JOIN student_details sd ON sd.student_id = s.id
    ORDER BY sd.roll_number
";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Student query prepare failed: " . $conn->error);
}

$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}

$stmt->close();

// If no students found
if (count($students) === 0) {
    $conn->close();
    echo "<script>alert('No students found for this class!'); window.location.href='mark_attendance_select.php';</script>";
    exit();
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Mark Attendance</title>
    <link rel="stylesheet" href="dashboard.css">
    <style>
        body {
            background: #f4f5fb;
            font-family: Arial, sans-serif;
        }
        .att-container {
            max-width: 900px;
            margin: 25px auto;
            background: #fff;
            padding: 20px 24px 26px;
            border-radius: 14px;
            box-shadow: 0 4px 18px rgba(0,0,0,0.12);
        }
        .att-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }
        .att-header h2 {
            margin: 0;
            color: #1f3c88;
        }
        .att-meta {
            font-size: 13px;
            color: #555;
            margin-bottom: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }
        th, td {
            border: 1px solid #d0d7e2;
            padding: 6px 8px;
            text-align: left;
        }
        th {
            background: #e4ebfb;
            color: #1f3c88;
        }
        td.center {
            text-align: center;
        }
        .btn-row {
            margin-top: 12px;
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }
        button, .btn-small {
            padding: 8px 14px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }
        .btn-main {
            background: #1f3c88;
            color: #fff;
            font-weight: 600;
        }
        .btn-main:hover {
            background: #163473;
        }
        .btn-small {
            background: #f0f2ff;
            color: #1f3c88;
            border: 1px solid #c5cbe8;
        }
        .btn-small:hover {
            background: #e0e5ff;
        }
        .back-link {
            text-decoration: none;
            font-size: 13px;
            color: #1f3c88;
        }
    </style>
</head>
<body>

<div class="att-container">

    <div class="att-header">
        <h2>Mark Attendance</h2>
        <a href="mark_attendance_select.php" class="back-link">← Change Class</a>
    </div>

    <div class="att-meta">
        <strong>Branch:</strong> <?php echo htmlspecialchars($branch); ?> &nbsp;|
        <strong>Year:</strong> <?php echo htmlspecialchars($year); ?> &nbsp;|
        <strong>Section:</strong> <?php echo htmlspecialchars($section); ?> &nbsp;|
        <strong>Date:</strong> <?php echo htmlspecialchars($date); ?> &nbsp;|
        <strong>Period:</strong> <?php echo htmlspecialchars($period); ?> &nbsp;|
        <strong>Subject:</strong> <?php echo htmlspecialchars($subject ?: 'Not specified'); ?>
    </div>

    <form method="POST" action="mark_attendance.php">
        <!-- keep class info in hidden fields -->
        <input type="hidden" name="branch" value="<?php echo htmlspecialchars($branch); ?>">
        <input type="hidden" name="year" value="<?php echo htmlspecialchars($year); ?>">
        <input type="hidden" name="section" value="<?php echo htmlspecialchars($section); ?>">
        <input type="hidden" name="date" value="<?php echo htmlspecialchars($date); ?>">
        <input type="hidden" name="period" value="<?php echo htmlspecialchars($period); ?>">
        <input type="hidden" name="subject" value="<?php echo htmlspecialchars($subject); ?>">
        <input type="hidden" name="save_attendance" value="1">

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Roll Number</th>
                    <th>Student Name</th>
                    <th class="center">Present</th>
                </tr>
            </thead>
            <tbody>
                <?php
$i = 1;
foreach ($students as $stu):
?>
<tr>
    <td><?php echo $i++; ?></td>
    <td><?php echo htmlspecialchars($stu['roll_number']); ?></td>
    <td><?php echo htmlspecialchars($stu['full_name']); ?></td>

    <!-- student_id from JOIN -->
    <input type="hidden" name="student_ids[]" value="<?php echo $stu['student_id']; ?>">

    <td class="center">
        <input type="checkbox" name="present_ids[]" value="<?php echo $stu['student_id']; ?>" checked>
    </td>
</tr>
<?php endforeach; ?>

            </tbody>
        </table>

        <div class="btn-row">
            <div>
                <button type="button" class="btn-small" onclick="setAll(true)">Mark All Present</button>
                <button type="button" class="btn-small" onclick="setAll(false)">Mark All Absent</button>
            </div>
            <button type="submit" class="btn-main">Save Attendance</button>
        </div>
    </form>
</div>

<script>
function setAll(isPresent) {
    const boxes = document.querySelectorAll('input[name="present_ids[]"]');
    boxes.forEach(cb => cb.checked = isPresent);
}
</script>

</body>
</html>
