<?php
error_reporting(0);
include '../Includes/dbcon.php';
include '../Includes/session.php';

// Pagination setup
$limit = 10;  // Number of students per page
$page = isset($_GET['page']) ? $_GET['page'] : 1;  // Current page (default is 1)
$offset = ($page - 1) * $limit;  // Calculate offset for the query

// Fetch courses taught by the teacher
$query_courses = "
    SELECT course.course_code, course.course_name, course_teacher.semester, course_teacher.session, course_teacher.course_id
    FROM course_teacher
    INNER JOIN course ON course.Id = course_teacher.course_id
    WHERE course_teacher.teacher_id = '$_SESSION[userId]'
";
$rs_courses = $conn->query($query_courses);
$course_count = $rs_courses->num_rows;

if (isset($_GET['course_id']) && isset($_GET['semester']) && isset($_GET['session'])) {
  // Fetch course details
  $course_id = $_GET['course_id'];
  $semester = $_GET['semester'];
  $session = $_GET['session'];
  $attendance_date = isset($_GET['attendance_date']) ? $_GET['attendance_date'] : '';

  $query_students = "
    SELECT course_student.student_id
    FROM course_student
    WHERE course_student.course_id = '$course_id'
    AND course_student.semester = '$semester'
    AND course_student.session = '$session'
    LIMIT $limit OFFSET $offset
";

  $rs_students = $conn->query($query_students);
  $students_html = "";

  if ($rs_students->num_rows > 0) {
    $sn = $offset + 1;
    while ($row = $rs_students->fetch_assoc()) {
      $student_id = $row['student_id'];

      // Fetch student details
      $query_student_details = "
            SELECT student_firstName, student_lastName, student_id, student_semester, student_session
            FROM student
            WHERE student_id = '$student_id'
        ";
      $rs_student_details = $conn->query($query_student_details);
      $student_row = $rs_student_details->fetch_assoc();

      $query_attendance = "
SELECT 
    status
FROM attendance
WHERE course_id = '$course_id'
AND student_id = '$student_id'
AND attendance_date = '$attendance_date'  -- Filter by selected date
";
      $rs_attendance = $conn->query($query_attendance);

      // Initialize status
      $attendance_status = "No"; // Default to "No"

      // Check if the student has an attendance record for that date
      if ($rs_attendance->num_rows > 0) {
        $attendance_row = $rs_attendance->fetch_assoc();

        // If the student attended (status = 1), set status to "Yes"
        if ($attendance_row['status'] == 1) {
          $attendance_status = "Yes";
        }
      }


      // Generate HTML for the table
      $students_html .= "
            <tr>
                <td>$sn</td>
                <td>" . $student_row['student_firstName'] . "</td>
                <td>" . $student_row['student_lastName'] . "</td>
                <td>" . $student_row['student_id'] . "</td>
                <td>" . $student_row['student_semester'] . "</td>
                <td>" . $student_row['student_session'] . "</td>
                <td>$attendance_status</td>
            </tr>
        ";
      $sn++;
    }
  } else {
    $students_html = "<tr><td colspan='7'>No students found</td></tr>";
  }

  // Count total students for pagination
  $query_count = "
        SELECT COUNT(course_student.student_id) AS total_students
        FROM course_student
        WHERE course_student.course_id = '$course_id'
        AND course_student.semester = '$semester'
        AND course_student.session = '$session'
    ";
  $rs_count = $conn->query($query_count);
  $total_students = $rs_count->fetch_assoc()['total_students'];
  $total_pages = ceil($total_students / $limit);  // Calculate total pages

  // Return students HTML and pagination
  echo json_encode([
    'students_html' => $students_html,
    'total_pages' => $total_pages,
    'current_page' => $page
  ]);
  exit; // End the script execution after returning the student list
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
            <h1 class="h4 mb-0 text-gray-800">Attendance In a Particular Date</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">All Students</li>
            </ol>
          </div>

          <!-- Courses and Date Picker Section -->
          <div class="container mt-4">
            <!-- Date Picker for selecting attendance date -->
            <div class="row justify-content-center mb-4">
              <div class="col-md-8">
                <div class="card border-primary shadow-sm">
                  <div class="card-body">
                    <h5 class="card-title text-center text-primary">Select Attendance Date</h5>
                    <div class="form-group">
                      <input
                        type="date"
                        class="form-control form-control-lg rounded-pill text-center"
                        id="attendance_date"
                        onchange="updateDateAndShowStudents()"
                        placeholder="Pick a date">
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Courses Taught by the Teacher -->
            <div class="row justify-content-center">
              <?php while ($course = $rs_courses->fetch_assoc()): ?>
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
                      <button
                        class="btn btn-primary btn-sm rounded-pill mt-auto"
                        onclick="showStudents(<?php echo $course['course_id']; ?>, '<?php echo $course['semester']; ?>', '<?php echo $course['session']; ?>')">
                        View Students
                      </button>
                    </div>
                  </div>
                </div>
              <?php endwhile; ?>
            </div>
          </div>



          <!-- Students List -->
          <div id="students-list" style="display:none;">
            <div class="row">
              <div class="col-lg-12">
                <div class="card mb-4">
                  <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Students Enrolled</h6>
                  </div>
                  <div class="table-responsive p-3">
                    <table class="table align-items-center table-flush table-hover" id="dataTableHover">
                      <thead class="thead-light">
                        <tr>
                          <th>#</th>
                          <th>First Name</th>
                          <th>Last Name</th>
                          <th>Student ID</th>
                          <th>Semester</th>
                          <th>Session</th>
                          <th>Is Present</th>
                        </tr>
                      </thead>
                      <tbody id="students-table-body">
                        <!-- Students will be loaded here dynamically -->
                      </tbody>
                    </table>
                  </div>
                  <div id="pagination" class="d-flex justify-content-center mt-3">
                    <!-- Pagination will be loaded here -->
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!---Container Fluid-->
        </div>
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
  <!-- Page level plugins -->
  <script src="../vendor/datatables/jquery.dataTables.min.js"></script>
  <script src="../vendor/datatables/dataTables.bootstrap4.min.js"></script>

  <script>
    // Store selected date globally
    let selectedDate = '';

    // Update the selected date and show the students for that course
    function updateDateAndShowStudents() {
      selectedDate = document.getElementById('attendance_date').value;
    }

    // Modify the showStudents function to include the selected date
    function showStudents(course_id, semester, session, page = 1) {
      if (!selectedDate) {
        alert('Please select a date first!');
        return;
      }

      // Show the students list container
      $('#students-list').show();

      // Fetch student data using Ajax
      $.ajax({
        url: "", // This is the same file, no need to change the URL
        type: "GET",
        data: {
          course_id: course_id,
          semester: semester,
          session: session,
          page: page,
          attendance_date: selectedDate // Pass the selected date
        },
        success: function(response) {
          const data = JSON.parse(response);

          // Fill the student list table with the response
          $('#students-table-body').html(data.students_html);

          // Display pagination controls
          let paginationHtml = '';
          for (let i = 1; i <= data.total_pages; i++) {
            paginationHtml += `
            <li class="page-item ${data.current_page === i ? 'active' : ''}">
              <a class="page-link" href="javascript:void(0);" onclick="showStudents(${course_id}, '${semester}', '${session}', ${i})">${i}</a>
            </li>
          `;
          }
          $('#pagination').html(`
          <nav aria-label="Page navigation example">
            <ul class="pagination">
              ${paginationHtml}
            </ul>
          </nav>
        `);
        }
      });
    }

    $(document).ready(function() {
      // Disable DataTable features like search and entries
      $('#dataTableHover').DataTable({
        "searching": false, // Disable search box
        "lengthChange": false, // Disable entries dropdown
        "paging": true, // Enable pagination
        "info": false // Disable info text
      });
    });
  </script>
</body>

</html>