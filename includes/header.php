
<?php
$pageTitle = $pageTitle ?? 'Training Enrollment System';
$basePath = $basePath ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="stylesheet" href="<?= $basePath ?>css/style.css">
</head>
<body>
<nav class="navbar">
    <a href="<?= $basePath ?>index.php">Home</a>
    <a href="<?= $basePath ?>admin/courses.php">Courses</a>
    <a href="<?= $basePath ?>admin/classes.php">Classes</a>
    <a href="<?= $basePath ?>admin/students.php">Record Student</a>
    <a href="<?= $basePath ?>admin/enroll.php">Enroll</a>
    <a href="<?= $basePath ?>admin/enrollments.php">Enrollments</a>
    <a href="<?= $basePath ?>admin/reports.php">Reports</a>
</nav>
<main class="container">
