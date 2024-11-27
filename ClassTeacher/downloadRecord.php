<?php
error_reporting(0);
include '../Includes/dbcon.php';
include '../Includes/session.php';

// Check if the request is for generating an Excel report
if (isset($_GET['course_id']) && isset($_GET['semester']) && isset($_GET['session']) && isset($_GET['download'])) {
    $course_id = $_GET['course_id'];
    $semester = $_GET['semester'];
    $session = $_GET['session'];

    // Generate and download the Excel report
    $filename = "Attendance_Report_Course_$course_id_Semester_$semester_Session_$session.xls";

    // Set headers for file download
    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=$filename");
    header("Pragma: no-cache");
    header("Expires: 0");

    // Start outputting the Excel file content
    echo "<table border='1'>
    <tr>
        <th>#</th>
        <th>First Name</th>
        <th>Last Name</th>
        <th>Student ID</th>
        <th>Semester</th>
        <th>Session</th>
        <th>Attendance Percentage</th>
    </tr>";

    // Fetch students enrolled in the course
    $query_students = "
        SELECT course_student.student_id
        FROM course_student
        WHERE course_student.course_id = '$course_id'
        AND course_student.semester = '$semester'
        AND course_student.session = '$session'
    ";
    $rs_students = $conn->query($query_students);

    if ($rs_students && $rs_students->num_rows > 0) {
        $sn = 1;
        while ($row = $rs_students->fetch_assoc()) {
            $student_id = $row['student_id'];

            // Fetch student details
            $query_student_details = "
                SELECT student_firstName, student_lastName, student_id
                FROM student
                WHERE student_id = '$student_id'
            ";
            $rs_student_details = $conn->query($query_student_details);
            $student = $rs_student_details->fetch_assoc();

            // Calculate attendance percentage
            $query_attendance = "
                SELECT 
                    COUNT(*) AS total_sessions,
                    SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) AS attended_sessions
                FROM attendance
                WHERE course_id = '$course_id'
                AND student_id = '$student_id'
            ";
            $rs_attendance = $conn->query($query_attendance);
            $attendance = $rs_attendance->fetch_assoc();

            $attendance_percentage = 0;
            if ($attendance['total_sessions'] > 0) {
                $attendance_percentage = round(($attendance['attended_sessions'] / $attendance['total_sessions']) * 100, 2);
            }

            // Output student row
            echo "
            <tr>
                <td>$sn</td>
                <td>{$student['student_firstName']}</td>
                <td>{$student['student_lastName']}</td>
                <td>{$student['student_id']}</td>
                <td>$semester</td>
                <td>$session</td>
                <td>$attendance_percentage%</td>
            </tr>";
            $sn++;
        }
    } else {
        echo "<tr><td colspan='7'>No students found</td></tr>";
    }

    echo "</table>";
    exit; // End the script execution after generating the report
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link href="img/logo/attnlg.jpg" rel="icon">
    <title>Dashboard</title>
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="css/ruang-admin.min.css" rel="stylesheet">
</head>

<body id="page-top">
    <div id="wrapper">
        <!-- Sidebar -->
        <?php include "Includes/sidebar.php"; ?>
        <!-- Sidebar -->

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <!-- TopBar -->
                <?php include "Includes/topbar.php"; ?>
                <!-- Topbar -->

                <!-- Container Fluid-->
                <div class="container-fluid" id="container-wrapper">
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h4 mb-0 text-gray-800">Generate Attendance Report</h1>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="./">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Attendance Report</li>
                        </ol>
                    </div>

                    <!-- Courses Taught by the Teacher -->
                    <div class="row justify-content-center">
                        <?php
                        $teacher_id = $_SESSION['userId'];
                        $query_courses = "
                SELECT course.course_code, course.course_name, course_teacher.semester, course_teacher.session, course_teacher.course_id
                FROM course_teacher
                INNER JOIN course ON course.Id = course_teacher.course_id
                WHERE course_teacher.teacher_id = '$teacher_id'
            ";
                        $rs_courses = $conn->query($query_courses);
                        while ($course = $rs_courses->fetch_assoc()):
                        ?>
                            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                                <div class="card text-center border-primary shadow-sm h-100">
                                    <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                        <h6 class="card-title text-primary">
                                            <i class="fas fa-book mr-2"></i>
                                            <?php echo $course['course_code']; ?>
                                        </h6>
                                        <p class="text-muted">
                                            <?php echo $course['semester']; ?>th Semester <br> <?php echo $course['session']; ?>
                                        </p>
                                        <a
                                            href="?course_id=<?php echo $course['course_id']; ?>&semester=<?php echo $course['semester']; ?>&session=<?php echo $course['session']; ?>&download=1"
                                            class="btn btn-primary btn-sm rounded-pill mt-auto">
                                            Generate Report
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <?php include "Includes/footer.php"; ?>
            <!-- Footer -->
        </div>
    </div>

    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/ruang-admin.min.js"></script>
</body>

</html>