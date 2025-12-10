<?php
session_start();

// Only staff
if (!isset($_SESSION['staff_id'])) {
    header("Location: stafflogin.php");
    exit();
}

$staff_id = $_SESSION['staff_id'];

require 'config.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle new notification form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title    = trim($_POST['title'] ?? '');
    $message  = trim($_POST['message'] ?? '');
    $audience = $_POST['audience'] ?? 'ALL';

    if ($title !== '' && $message !== '') {
        $stmt = $conn->prepare("
            INSERT INTO notifications (title, message, audience, created_by_role, created_by_id)
            VALUES (?, ?, ?, 'STAFF', ?)
        ");
        if (!$stmt) {
            die("Insert prepare failed: " . $conn->error);
        }
        $stmt->bind_param("sssi", $title, $message, $audience, $staff_id);
        $stmt->execute();
        $stmt->close();

        echo "<script>alert('Notification posted successfully'); window.location.href='staff_notifications.php';</script>";
        exit();
    } else {
        echo "<script>alert('Title and message are required');</script>";
    }
}

// Fetch notifications (you can later filter by date or created_by_id)
$notifications = [];
$res = $conn->query("
    SELECT id, title, message, audience, created_by_role, created_at
    FROM notifications
    ORDER BY created_at DESC
");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $notifications[] = $row;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Staff Notifications</title>
    <link rel="stylesheet" href="dashboard.css">
    <style>
        body {
            background:#f4f5fb;
            font-family: Arial, sans-serif;
        }
        .wrap {
            max-width: 900px;
            margin: 25px auto 40px;
            background:#fff;
            padding:18px 22px 24px;
            border-radius:14px;
            box-shadow:0 4px 16px rgba(0,0,0,0.1);
        }
        h2 {
            color:#1f3c88;
            margin-bottom:10px;
        }
        .top-row {
            display:flex;
            justify-content:space-between;
            align-items:center;
            margin-bottom:10px;
        }
        .top-row a {
            font-size:13px;
            text-decoration:none;
            color:#1f3c88;
        }
        form.new-note {
            margin-bottom:16px;
            padding:10px 12px;
            border-radius:10px;
            background:#f5f7ff;
        }
        form.new-note input[type="text"],
        form.new-note textarea,
        form.new-note select {
            width:100%;
            padding:8px;
            margin:5px 0;
            border-radius:8px;
            border:1px solid #ccd4e0;
            font-size:13px;
        }
        form.new-note textarea {
            min-height:70px;
            resize:vertical;
        }
        form.new-note button {
            margin-top:6px;
            padding:8px 14px;
            border-radius:8px;
            border:none;
            background:#1f3c88;
            color:#fff;
            font-size:13px;
            font-weight:600;
            cursor:pointer;
        }
        form.new-note button:hover {
            background:#163473;
        }
        .note-list {
            margin-top:10px;
        }
        .note-card {
            border-bottom:1px solid #e0e4f0;
            padding:8px 4px 10px;
        }
        .note-title {
            font-weight:bold;
            font-size:14px;
            color:#1f3c88;
        }
        .note-meta {
            font-size:11px;
            color:#777;
            margin:2px 0 4px;
        }
        .note-message {
            font-size:13px;
            white-space:pre-wrap;
        }
        .badge {
            display:inline-block;
            font-size:11px;
            padding:2px 6px;
            border-radius:10px;
            margin-left:6px;
        }
        .badge-all { background:#e8f5e9; color:#2e7d32; }
        .badge-stu { background:#e3f2fd; color:#1565c0; }
        .badge-staff { background:#fff3e0; color:#ef6c00; }
    </style>
</head>
<body>

<div class="wrap">
    <div class="top-row">
        <h2>Notifications / Announcements</h2>
        <a href="staffdashboard.php">← Back to Dashboard</a>
    </div>

    <!-- New notification form -->
    <form class="new-note" method="POST" action="staff_notifications.php">
        <input type="text" name="title" placeholder="Title (e.g. DBMS Test on Friday)" required>
        <textarea name="message" placeholder="Write the announcement details..." required></textarea>

        <select name="audience" required>
            <option value="ALL">Audience: All (Staff & Students)</option>
            <option value="STUDENTS">Only Students</option>
            <option value="STAFF">Only Staff</option>
        </select>

        <button type="submit">Post Notification</button>
    </form>

    <!-- List notifications -->
    <div class="note-list">
        <?php if (count($notifications) === 0): ?>
            <p style="font-size:13px; color:#555;">No notifications posted yet.</p>
        <?php else: ?>
            <?php foreach ($notifications as $n): ?>
                <div class="note-card">
                    <div class="note-title">
                        <?php echo htmlspecialchars($n['title']); ?>
                        <?php
                        $aud = $n['audience'];
                        if ($aud === 'ALL') {
                            echo '<span class="badge badge-all">All</span>';
                        } elseif ($aud === 'STUDENTS') {
                            echo '<span class="badge badge-stu">Students</span>';
                        } else {
                            echo '<span class="badge badge-staff">Staff</span>';
                        }
                        ?>
                    </div>
                    <div class="note-meta">
                        Posted by <?php echo htmlspecialchars($n['created_by_role']); ?> 
                        · <?php echo htmlspecialchars($n['created_at']); ?>
                    </div>
                    <div class="note-message">
                        <?php echo nl2br(htmlspecialchars($n['message'])); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</div>

</body>
</html>
