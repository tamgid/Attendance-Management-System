<?php
error_reporting(0);
session_start(); // Start the session for status messages.
include '../Includes/dbcon.php';
include '../Includes/session.php';

// Initialize status message
$statusMsg = '';

//------------------------SAVE--------------------------------------------------
if (isset($_POST['save'])) {
    $course_id = mysqli_real_escape_string($conn, $_POST['course_id']);
    $teacher_id = mysqli_real_escape_string($conn, $_POST['teacher_id']);
    $semester = mysqli_real_escape_string($conn, $_POST['semester']);
    $session = mysqli_real_escape_string($conn, $_POST['session']);

    // Check if the course-teacher assignment already exists
    $query = mysqli_query($conn, "SELECT * FROM course_teacher WHERE course_id = '$course_id' AND teacher_id = '$teacher_id'");
    $ret = mysqli_fetch_array($query);

    if ($ret) {
        $statusMsg = "<div class='alert alert-danger'>This Course is Already Assigned to This Teacher!</div>";
    } else {
        // Save new course-teacher assignment
        $query = mysqli_query($conn, "INSERT INTO course_teacher (course_id, teacher_id, semester, session) VALUES ('$course_id', '$teacher_id', '$semester', '$session')");
        if ($query) {
            $statusMsg = "<div class='alert alert-success'>Assigned Successfully!</div>";
        } else {
            $statusMsg = "<div class='alert alert-danger'>An error Occurred!</div>";
        }
    }
}

//--------------------------------DELETE---------------------------------------
if (isset($_GET['Id']) && isset($_GET['action']) && $_GET['action'] == "delete") {
    $Id = mysqli_real_escape_string($conn, $_GET['Id']);

    $query = mysqli_query($conn, "DELETE FROM course_teacher WHERE Id='$Id'");

    if ($query) {
        $_SESSION['statusMsg'] = "<div class='alert alert-success'>Deleted Successfully!</div>";
        echo "<script>window.location = 'assignCourseTeacher.php';</script>";
    } else {
        $_SESSION['statusMsg'] = "<div class='alert alert-danger'>An error Occurred!</div>";
        echo "<script>window.location = 'assignCourseTeacher.php';</script>";
    }
}

// Display any status messages stored in the session
if (isset($_SESSION['statusMsg'])) {
    $statusMsg = $_SESSION['statusMsg'];
    unset($_SESSION['statusMsg']);
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
    <?php include 'includes/title.php'; ?>
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
                        <h1 class="h4 mb-0 text-gray-800">Assign Teacher to Course</h1>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="./">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Assign Teacher</li>
                        </ol>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <!-- Form Basic -->
                            <div class="card mb-4">
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Assign Teacher to Course</h6>
                                    <?php echo $statusMsg; ?>
                                </div>
                                <div class="card-body">
                                    <form method="post">
                                        <div class="form-group row mb-3">
                                            <div class="col-xl-6">
                                                <label class="form-control-label">Course<span class="text-danger ml-2">*</span></label>
                                                <select class="form-control" name="course_id" required>
                                                    <option value="">Select Course</option>
                                                    <?php
                                                    $query = "SELECT * FROM course";
                                                    $result = mysqli_query($conn, $query);
                                                    while ($course = mysqli_fetch_assoc($result)) {
                                                        echo "<option value='{$course['Id']}'>{$course['course_name']} ({$course['course_code']})</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-xl-6">
                                                <label class="form-control-label">Teacher<span class="text-danger ml-2">*</span></label>
                                                <select class="form-control" name="teacher_id" required>
                                                    <option value="">Select Teacher</option>
                                                    <?php
                                                    $query = "SELECT * FROM teacher";
                                                    $result = mysqli_query($conn, $query);
                                                    while ($teacher = mysqli_fetch_assoc($result)) {
                                                        echo "<option value='{$teacher['Id']}'>{$teacher['teacher_firstName']} {$teacher['teacher_lastName']}</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row mb-3">
                                            <div class="col-xl-6">
                                                <label class="form-control-label">Semester<span class="text-danger ml-2">*</span></label>
                                                <input type="number" class="form-control" name="semester" placeholder="Semester" required>
                                            </div>
                                            <div class="col-xl-6">
                                                <label class="form-control-label">Session<span class="text-danger ml-2">*</span></label>
                                                <input type="text" class="form-control" name="session" value="2020-2021" readonly>
                                            </div>
                                        </div>
                                        <button type="submit" name="save" class="btn btn-primary">Save</button>
                                    </form>
                                </div>
                            </div>

                            <!-- Course Teacher Assignments -->
                            <div class="card mb-4">
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Assigned Teacher</h6>
                                </div>
                                <div class="table-responsive p-3">
                                    <table class="table align-items-center table-flush table-hover" id="dataTableHover">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Course</th>
                                                <th>Teacher</th>
                                                <th>Semester</th>
                                                <th>Session</th>
                                                <th>Delete</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $query = "SELECT course_teacher.Id, course.course_name, course.course_code, 
                                                      teacher.teacher_firstName, teacher.teacher_lastName, 
                                                      course_teacher.semester, course_teacher.session 
                                                      FROM course_teacher 
                                                      INNER JOIN course ON course_teacher.course_id = course.Id
                                                      INNER JOIN teacher ON course_teacher.teacher_id = teacher.Id";
                                            $result = $conn->query($query);
                                            $sn = 0;
                                            while ($row = $result->fetch_assoc()) {
                                                $sn++;
                                                echo "<tr>
                                                    <td>$sn</td>
                                                    <td>{$row['course_name']} ({$row['course_code']})</td>
                                                    <td>{$row['teacher_firstName']} {$row['teacher_lastName']}</td>
                                                    <td>{$row['semester']}</td>
                                                    <td>{$row['session']}</td>
                                                    <td><a href='?action=delete&Id={$row['Id']}' class='text-danger' onclick='return confirm(\"Are you sure?\");'><i class='fas fa-fw fa-trash'></i>Delete</a></td>
                                                </tr>";
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Row-->
                </div>
                <!---Container Fluid-->
            </div>
            <!-- Footer -->
            <?php include "Includes/footer.php"; ?>
            <!-- Footer -->
        </div>
    </div>

    <!-- Scroll to top -->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/ruang-admin.min.js"></script>
    <script src="../vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="../vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#dataTableHover').DataTable();
        });
    </script>
</body>

</html>