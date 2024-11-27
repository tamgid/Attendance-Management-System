<?php
error_reporting(0);
include '../Includes/dbcon.php';
include '../Includes/session.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $data = json_decode(file_get_contents("php://input"), true);
  $response = ['success' => false, 'message' => ''];

  if (isset($data['teacher_id'], $data['course_id'], $data['attendance_date'], $data['attendance'])) {
    $teacher_id = $conn->real_escape_string($data['teacher_id']);
    $course_id = $conn->real_escape_string($data['course_id']);
    $attendance_date = $conn->real_escape_string($data['attendance_date']);
    $attendance = $data['attendance'];

    foreach ($attendance as $record) {
      $student_id = $conn->real_escape_string($record['student_id']);
      $status = $conn->real_escape_string($record['status']);

      $query = "
                INSERT INTO attendance (student_id, teacher_id, course_id, status, attendance_date)
                VALUES ('$student_id', '$teacher_id', '$course_id', '$status', '$attendance_date')
                ON DUPLICATE KEY UPDATE status = '$status'
            ";

      $result = $conn->query($query);
      if (!$result) {
        error_log("SQL Error: " . $conn->error . " Query: " . $query);
        $response['message'] = "SQL Error: " . $conn->error;
        echo json_encode($response);
        exit;
      }
    }

    $response['success'] = true;
    $response['message'] = "Attendance saved successfully!";
  } else {
    $response['message'] = "Invalid data received.";
  }

  echo json_encode($response);
  exit;
}

// Fetch courses taught by the teacher
$query_courses = "
    SELECT course.course_code, course.course_name, course_teacher.semester, course_teacher.session, course_teacher.course_id
    FROM course_teacher
    INNER JOIN course ON course.Id = course_teacher.course_id
    WHERE course_teacher.teacher_id = '$_SESSION[userId]'
";
$rs_courses = $conn->query($query_courses);

if (isset($_GET['course_id']) && isset($_GET['semester']) && isset($_GET['session'])) {
  $course_id = $conn->real_escape_string($_GET['course_id']);
  $semester = $conn->real_escape_string($_GET['semester']);
  $session = $conn->real_escape_string($_GET['session']);

  $query_students = "
        SELECT course_student.student_id
        FROM course_student
        WHERE course_student.course_id = '$course_id'
        AND course_student.semester = '$semester'
        AND course_student.session = '$session'
    ";

  $rs_students = $conn->query($query_students);
  $students_html = "";

  if ($rs_students->num_rows > 0) {
    $sn = 1;
    while ($row = $rs_students->fetch_assoc()) {
      $student_id = $row['student_id'];

      $query_student_details = "
                SELECT student_firstName, student_lastName, student_id, student_semester, student_session
                FROM student
                WHERE student_id = '$student_id'
            ";
      $rs_student_details = $conn->query($query_student_details);
      $student_row = $rs_student_details->fetch_assoc();

      $students_html .= "
                <tr>
                    <td>$sn</td>
                    <td>" . htmlspecialchars($student_row['student_firstName']) . "</td>
                    <td>" . htmlspecialchars($student_row['student_lastName']) . "</td>
                    <td>
                        <input type='hidden' class='student-id' value='" . htmlspecialchars($student_row['student_id']) . "'>
                        " . htmlspecialchars($student_row['student_id']) . "
                    </td>
                    <td>" . htmlspecialchars($student_row['student_semester']) . "</td>
                    <td>" . htmlspecialchars($student_row['student_session']) . "</td>
                    <td><input type='checkbox' class='attendance-checkbox' style='width: 15px; height: 15px;'></td>
                </tr>";
      $sn++;
    }
  } else {
    $students_html = "<tr><td colspan='7'>No students found</td></tr>";
  }

  echo $students_html;
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link href="img/logo/attnlg.jpg" rel="icon">
  <title>Dashboard</title>
  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
  <link href="css/ruang-admin.min.css" rel="stylesheet">
</head>

<body id="page-top">
  <div id="wrapper">
    <?php include "Includes/sidebar.php"; ?>
    <div id="content-wrapper" class="d-flex flex-column">
      <div id="content">
        <?php include "Includes/topbar.php"; ?>
        <div class="container-fluid" id="container-wrapper">
          <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h4 mb-0 text-gray-800">Take Attendance</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">Attendance</li>
            </ol>
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

          <div id="students-list" style="display:none;">
            <div class="row">
              <div class="col-lg-12">
                <div class="card mb-4">
                  <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Students Enrolled</h6>
                  </div>
                  <div class="table-responsive p-3">
                    <form id="attendanceForm">
                      <table class="table align-items-center table-flush table-hover" id="dataTableHover">
                        <thead class="thead-light">
                          <tr>
                            <th>#</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Student ID</th>
                            <th>Semester</th>
                            <th>Session</th>
                            <th>Attendance</th>
                          </tr>
                        </thead>
                        <tbody id="students-table-body">
                          <!-- Students will be loaded dynamically -->
                        </tbody>
                      </table>
                      <div class="text-center mt-3">
                        <button type="button" class="btn btn-success" onclick="submitAttendance()">Submit Attendance</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <?php include "Includes/footer.php"; ?>
    </div>
  </div>

  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
  </a>

  <script src="../vendor/jquery/jquery.min.js"></script>
  <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
  <script src="js/ruang-admin.min.js"></script>

  <script>
    // Store selected course details globally
    let selectedCourseId = null;
    let selectedSemester = null;
    let selectedSession = null;

    function showStudents(course_id, semester, session) {
      // Store the selected course details
      selectedCourseId = course_id;
      selectedSemester = semester;
      selectedSession = session;

      $('#students-list').show();
      $.ajax({
        url: "",
        type: "GET",
        data: {
          course_id,
          semester,
          session
        },
        success: function(response) {
          $('#students-table-body').html(response);
        }
      });
    }

    function submitAttendance() {
      const attendanceData = [];
      $('#students-table-body tr').each(function() {
        const row = $(this);
        const studentId = row.find('.student-id').val();
        const status = row.find('.attendance-checkbox').is(':checked') ? 1 : 0;
        attendanceData.push({
          student_id: studentId,
          status
        });
      });

      // Use the globally stored courseId
      const teacherId = "<?php echo $_SESSION['userId']; ?>";
      const courseId = selectedCourseId; // This comes from the button click
      const attendanceDate = new Date().toISOString().slice(0, 10);

      $.ajax({
        url: "",
        type: "POST",
        contentType: "application/json",
        data: JSON.stringify({
          teacher_id: teacherId,
          course_id: courseId,
          attendance_date: attendanceDate,
          attendance: attendanceData,
        }),
        success: function(response) {
          const res = JSON.parse(response);
          alert(res.message);

          if (res.success) {
            // Uncheck all checkboxes
            $('.attendance-checkbox').prop('checked', false);
          }
        },
        error: function() {
          alert("Failed to submit attendance. Please try again.");
        }
      });
    }
  </script>

</body>

</html>