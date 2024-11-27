<?php
error_reporting(0);
include '../Includes/dbcon.php';
include '../Includes/session.php';

// Define the number of records per page
$records_per_page = 10;

// Get the current page from the URL or set to 1 if not present
$current_page = isset($_GET['page']) ? intval($_GET['page']) : 1;
if ($current_page <= 0) $current_page = 1;

// Calculate the offset for the SQL query
$offset = ($current_page - 1) * $records_per_page;

// Count total records for pagination
$total_query = "SELECT COUNT(*) as total_records FROM student";
$total_result = mysqli_query($conn, $total_query);
$total_row = mysqli_fetch_assoc($total_result);
$total_records = $total_row['total_records'];
$total_pages = ceil($total_records / $records_per_page);

//------------------------SAVE--------------------------------------------------

if (isset($_POST['save'])) {
  $student_firstName = $_POST['student_firstName'];
  $student_lastName = $_POST['student_lastName'];
  $student_id = $_POST['student_id'];
  $student_email = $_POST['student_email'];
  $student_phoneNo = $_POST['student_phoneNo'];
  $student_semester = $_POST['student_semester'];
  $student_session = $_POST['student_session'];

  $query = mysqli_query($conn, "SELECT * FROM student WHERE student_id ='$student_id'");
  $ret = mysqli_fetch_array($query);

  if ($ret > 0) {
    $statusMsg = "<div class='alert alert-danger'>This Student ID Already Exists!</div>";
  } else {
    $query = mysqli_query(
      $conn,
      "INSERT INTO student(student_firstName, student_lastName, student_id, student_email, student_phoneNo, student_semester, student_session) 
            VALUES('$student_firstName', '$student_lastName', '$student_id', '$student_email', '$student_phoneNo', '$student_semester', '$student_session')"
    );

    if ($query) {
      $statusMsg = "<div class='alert alert-success'>Created Successfully!</div>";
    } else {
      $statusMsg = "<div class='alert alert-danger'>An error occurred!</div>";
    }
  }
}

//------------------------EDIT--------------------------------------------------

if (isset($_GET['Id']) && isset($_GET['action']) && $_GET['action'] == "edit") {
  $Id = $_GET['Id'];

  $query = mysqli_query($conn, "SELECT * FROM student WHERE Id ='$Id'");
  $row = mysqli_fetch_array($query);

  if (isset($_POST['update'])) {
    $student_firstName = $_POST['student_firstName'];
    $student_lastName = $_POST['student_lastName'];
    $student_id = $_POST['student_id'];
    $student_email = $_POST['student_email'];
    $student_phoneNo = $_POST['student_phoneNo'];
    $student_semester = $_POST['student_semester'];
    $student_session = $_POST['student_session'];

    $query = mysqli_query(
      $conn,
      "UPDATE student SET student_firstName='$student_firstName', student_lastName='$student_lastName', 
            student_id='$student_id', student_email='$student_email', student_phoneNo='$student_phoneNo', 
            student_semester='$student_semester', student_session='$student_session' WHERE Id='$Id'"
    );

    if ($query) {
      echo "<script>window.location = 'createStudents.php';</script>";
    } else {
      $statusMsg = "<div class='alert alert-danger'>An error occurred!</div>";
    }
  }
}

//------------------------DELETE------------------------------------------------

if (isset($_GET['Id']) && isset($_GET['action']) && $_GET['action'] == "delete") {
  $Id = $_GET['Id'];

  $query = mysqli_query($conn, "DELETE FROM student WHERE Id='$Id'");

  if ($query) {
    echo "<script>window.location = 'createStudents.php';</script>";
  } else {
    $statusMsg = "<div class='alert alert-danger'>An error occurred!</div>";
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>Manage Students</title>
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="css/ruang-admin.min.css" rel="stylesheet">
</head>

<body id="page-top">
  <div id="wrapper">
    <?php include "Includes/sidebar.php"; ?>
    <div id="content-wrapper" class="d-flex flex-column">
      <div id="content">
        <?php include "Includes/topbar.php"; ?>
        <div class="container-fluid">
          <h1 class="h4 mb-4 text-gray-800">Manage Students</h1>

          <?php if (isset($statusMsg)) echo $statusMsg; ?>

          <!-- Add / Update Form -->
          <div class="card mb-4">
            <div class="card-header">
              <h6 class="m-0 font-weight-bold text-primary">Create/Update Student</h6>
            </div>
            <div class="card-body">
              <form method="post">
                <div class="form-group">
                  <label>First Name</label>
                  <input type="text" class="form-control" name="student_firstName" value="<?php echo $row['student_firstName'] ?? ''; ?>" required>
                </div>
                <div class="form-group">
                  <label>Last Name</label>
                  <input type="text" class="form-control" name="student_lastName" value="<?php echo $row['student_lastName'] ?? ''; ?>" required>
                </div>
                <div class="form-group">
                  <label>Student ID</label>
                  <input type="text" class="form-control" name="student_id" value="<?php echo $row['student_id'] ?? ''; ?>" required>
                </div>
                <div class="form-group">
                  <label>Email</label>
                  <input type="email" class="form-control" name="student_email" value="<?php echo $row['student_email'] ?? ''; ?>" required>
                </div>
                <div class="form-group">
                  <label>Phone Number</label>
                  <input type="text" class="form-control" name="student_phoneNo" value="<?php echo $row['student_phoneNo'] ?? ''; ?>" required>
                </div>
                <div class="form-group">
                  <label>Semester</label>
                  <input type="text" class="form-control" name="student_semester" value="<?php echo $row['student_semester'] ?? ''; ?>" required>
                </div>
                <div class="form-group">
                  <label>Session</label>
                  <input type="text" class="form-control" name="student_session" value="<?php echo $row['student_session'] ?? ''; ?>" required>
                </div>
                <?php if (isset($Id)) { ?>
                  <button type="submit" name="update" class="btn btn-warning">Update</button>
                <?php } else { ?>
                  <button type="submit" name="save" class="btn btn-primary">Save</button>
                <?php } ?>
              </form>
            </div>
          </div>

          <!-- Students Table -->
          <div class="card mb-4">
            <div class="card-header">
              <h6 class="m-0 font-weight-bold text-primary">Students List</h6>
            </div>
            <div class="table-responsive p-3">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Student ID</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Semester</th>
                    <th>Session</th>
                    <th>Edit</th>
                    <th>Delete</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $query = "SELECT * FROM student LIMIT $records_per_page OFFSET $offset";
                  $result = mysqli_query($conn, $query);
                  $sn = $offset + 1;

                  while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>
                                            <td>{$sn}</td>
                                            <td>{$row['student_firstName']}</td>
                                            <td>{$row['student_lastName']}</td>
                                            <td>{$row['student_id']}</td>
                                            <td>{$row['student_email']}</td>
                                            <td>{$row['student_phoneNo']}</td>
                                            <td>{$row['student_semester']}</td>
                                            <td>{$row['student_session']}</td>
                                            <td><a href='?action=edit&Id={$row['Id']}' class='btn btn-warning btn-sm'>Edit</a></td>
                                            <td><a href='?action=delete&Id={$row['Id']}' class='btn btn-danger btn-sm'>Delete</a></td>
                                        </tr>";
                    $sn++;
                  }
                  ?>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Pagination -->
          <nav>
            <ul class="pagination">
              <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                <li class="page-item <?php if ($i == $current_page) echo 'active'; ?>">
                  <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
              <?php } ?>
            </ul>
          </nav>
        </div>
      </div>
      <?php include "Includes/footer.php"; ?>
    </div>
  </div>
  <script src="../vendor/jquery/jquery.min.js"></script>
  <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="js/ruang-admin.min.js"></script>
</body>

</html>