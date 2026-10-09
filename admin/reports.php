
<?php
require_once __DIR__ . '/../config/db.php';

$pageTitle = 'Reports';
$basePath = '../';

$summary = $db->getRow(
    "SELECT
        (SELECT COUNT(*) FROM courses) AS total_courses,
        (SELECT COUNT(*) FROM classes) AS total_classes,
        (SELECT COUNT(*) FROM students) AS total_students,
        (SELECT COUNT(*) FROM enrollments WHERE status = 'active') AS active_enrollments,
        (SELECT COUNT(*) FROM enrollments WHERE status = 'cancelled') AS cancelled_enrollments"
);

$classReports = $db->getRows(
    "SELECT co.course_name, c.class_code, c.schedule, c.slots,
            SUM(CASE WHEN e.status = 'active' THEN 1 ELSE 0 END) AS enrolled
     FROM classes c
     JOIN courses co ON c.course_id = co.course_id
     LEFT JOIN enrollments e ON c.class_id = e.class_id
     GROUP BY c.class_id, co.course_name, c.class_code, c.schedule, c.slots
     ORDER BY co.course_name"
);

require_once __DIR__ . '/../includes/header.php';
?>

<h1>Training Reports</h1>

<div class="grid">
<?php foreach ($summary as $label => $value): ?>
<div class="card">
<h2><?= htmlspecialchars(ucwords(str_replace('_', ' ', $label))) ?></h2>
<p><?= (int) $value ?></p>
</div>
<?php endforeach; ?>
</div>

<h2>Class Enrollment Summary</h2>
<div class="table-wrap">
<table>
<tr><th>Course</th><th>Class</th><th>Schedule</th><th>Active Enrollments</th><th>Remaining Slots</th></tr>
<?php foreach ($classReports as $row): ?>
<tr>
<td><?= htmlspecialchars($row['course_name']) ?></td>
<td><?= htmlspecialchars($row['class_code']) ?></td>
<td><?= htmlspecialchars($row['schedule'] ?? '') ?></td>
<td><?= (int) $row['enrolled'] ?></td>
<td><?= (int) $row['slots'] ?></td>
</tr>
<?php endforeach; ?>
</table>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
