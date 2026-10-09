
<?php
$pageTitle = 'Home | Training Enrollment System';
$basePath = '';
require_once __DIR__ . '/includes/header.php';
?>

<h1>Training Enrollment System</h1>

<p>
    Welcome to the Training Enrollment System. Manage training courses,
    class schedules, student records, enrollments, and reports in one place.
</p>

<div class="grid">
    <div class="card">
        <h2>Courses</h2>
        <p>Create, update, and manage training courses.</p>
        <a class="button" href="admin/courses.php">Manage Courses</a>
    </div>

    <div class="card">
        <h2>Classes</h2>
        <p>View schedules, instructors, and remaining slots.</p>
        <a class="button" href="admin/classes.php">Manage Classes</a>
    </div>

    <div class="card">
        <h2>Students</h2>
        <p>Record a student and enroll them in one transaction.</p>
        <a class="button" href="admin/students.php">Record Student</a>
    </div>

    <div class="card">
        <h2>Enrollments</h2>
        <p>View, cancel, and track training enrollments.</p>
        <a class="button" href="admin/enrollments.php">View Enrollments</a>
    </div>

    <div class="card">
        <h2>Reports</h2>
        <p>Review enrollment totals and class availability.</p>
        <a class="button" href="admin/reports.php">View Reports</a>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
