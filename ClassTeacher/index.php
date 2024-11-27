<?php
include '../Includes/dbcon.php';
include '../Includes/session.php';

// Get the teacher's full name
$query_teacher = "
    SELECT teacher_firstName, teacher_lastName
    FROM teacher
    WHERE teacher.Id = '$_SESSION[userId]'
";
$rs_teacher = $conn->query($query_teacher);
$teacher_data = $rs_teacher->fetch_assoc();
$teacher_name = $teacher_data['teacher_firstName'] . ' ' . $teacher_data['teacher_lastName'];

// Get the courses taught by the teacher
$query = "
    SELECT course.course_code, course.course_name
    FROM course_teacher
    INNER JOIN course ON course.Id = course_teacher.course_id
    WHERE course_teacher.teacher_id = '$_SESSION[userId]'
";

$rs = $conn->query($query);
$num = $rs->num_rows;
$assigned_courses = [];
while ($row = $rs->fetch_assoc()) {
  $assigned_courses[] = $row;  // Collect course data
}

$query_students = "
    SELECT COUNT(*) AS total_students
    FROM course_student
    INNER JOIN course_teacher ON course_teacher.course_id = course_student.course_id
    WHERE course_teacher.teacher_id = '$_SESSION[userId]'
    AND course_teacher.semester = course_student.semester
    AND course_teacher.session = course_student.session
";
$students_rs = $conn->query($query_students);
$students_data = $students_rs->fetch_assoc();
$total_students = $students_data['total_students'];
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
  <title>Teacher Dashboard</title>
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
            <h1 class="h4 mb-0 text-gray-800">Teacher Dashboard</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
            </ol>
          </div>

          <div class="row mb-3">
            <!-- Total Courses Assigned Card Example -->
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card h-100 shadow-sm">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-uppercase mb-1">Assigned Courses</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo count($assigned_courses); ?></div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-book fa-2x text-info"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Total Students Card Example -->
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card h-100 shadow-sm">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-uppercase mb-1">Total Students</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_students; ?></div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-users fa-2x text-primary"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Display Assigned Courses -->
          <div class="row mb-3">
            <div class="col-lg-12">
              <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                  <h6>Courses Assigned to <?php echo $teacher_name; ?></h6> <!-- Display teacher's name -->
                </div>
                <div class="card-body">
                  <?php if ($num > 0): ?>
                    <ul class="list-group">
                      <?php foreach ($assigned_courses as $course): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                          <span><strong><?php echo $course['course_code']; ?>:</strong> <?php echo $course['course_name']; ?></span>
                        </li>
                      <?php endforeach; ?>
                    </ul>
                  <?php else: ?>
                    <p>No courses assigned to you.</p>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>

        </div>
        <!---Container Fluid-->
      </div>
      <!-- Footer -->
      <?php include 'includes/footer.php'; ?>
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
  <script src="../vendor/chart.js/Chart.min.js"></script>
  <script src="js/demo/chart-area-demo.js"></script>
</body>

</html>