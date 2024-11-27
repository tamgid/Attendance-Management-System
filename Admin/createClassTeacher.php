<?php
error_reporting(0);
include '../Includes/dbcon.php';
include '../Includes/session.php';

//------------------------SAVE--------------------------------------------------

if (isset($_POST['save'])) {
  $firstName = $_POST['firstName'];
  $lastName = $_POST['lastName'];
  $designation = $_POST['designation'];
  $emailAddress = $_POST['emailAddress'];
  $password = $_POST['password'];  // Take password from user directly (non-encrypted)
  $phoneNo = $_POST['phoneNo'];

  // Check if teacher email already exists
  $query = mysqli_query($conn, "SELECT * FROM teacher WHERE teacher_email ='$emailAddress'");
  $ret = mysqli_fetch_array($query);

  if ($ret > 0) {
    $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>This Email Address Already Exists!</div>";
  } else {
    // Insert teacher details into the database
    $query = mysqli_query($conn, "INSERT INTO teacher (teacher_firstName, teacher_lastName, teacher_designation, teacher_email, teacher_password, teacher_phoneNo) 
        VALUES ('$firstName', '$lastName', '$designation', '$emailAddress', '$password', '$phoneNo')");

    if ($query) {
      $statusMsg = "<div class='alert alert-success' style='margin-right:700px;'>Created Successfully!</div>";
    } else {
      $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>An error Occurred!</div>";
    }
  }
}

//---------------------------------------EDIT------------------------------------------------------------

if (isset($_GET['Id']) && isset($_GET['action']) && $_GET['action'] == "edit") {
  $Id = $_GET['Id'];

  // Fetch teacher details for editing
  $query = mysqli_query($conn, "SELECT * FROM teacher WHERE Id ='$Id'");
  $row = mysqli_fetch_array($query);

  //------------UPDATE-----------------------------
  if (isset($_POST['update'])) {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $designation = $_POST['designation'];
    $emailAddress = $_POST['emailAddress'];
    $password = $_POST['password'];  // Take password from user directly (non-encrypted)
    $phoneNo = $_POST['phoneNo'];

    // Update teacher details
    $query = mysqli_query($conn, "UPDATE teacher SET teacher_firstName='$firstName', teacher_lastName='$lastName',
        teacher_designation='$designation', teacher_email='$emailAddress', teacher_password='$password', teacher_phoneNo='$phoneNo'
        WHERE Id='$Id'");

    if ($query) {
      echo "<script type='text/javascript'>
            window.location = ('createClassTeacher.php')
            </script>";
    } else {
      $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>An error Occurred!</div>";
    }
  }
}

//--------------------------------DELETE------------------------------------------------------------------

if (isset($_GET['Id']) && isset($_GET['action']) && $_GET['action'] == "delete") {
  $Id = $_GET['Id'];

  // Delete teacher from the database
  $query = mysqli_query($conn, "DELETE FROM teacher WHERE Id='$Id'");

  if ($query == TRUE) {
    echo "<script type='text/javascript'>
        window.location = ('createClassTeacher.php')
        </script>";
  } else {
    $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>An error Occurred!</div>";
  }
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
            <h1 class="h4 mb-0 text-gray-800">Manage Teachers</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">Create Teachers</li>
            </ol>
          </div>

          <div class="row">
            <div class="col-lg-12">
              <!-- Form Basic -->
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">Create Teacher</h6>
                  <?php echo $statusMsg; ?>
                </div>
                <div class="card-body">
                  <form method="post">
                    <div class="form-group row mb-3">
                      <div class="col-xl-6">
                        <label class="form-control-label">Firstname<span class="text-danger ml-2">*</span></label>
                        <input type="text" class="form-control" required name="firstName" value="<?php echo $row['teacher_firstName']; ?>" id="exampleInputFirstName">
                      </div>
                      <div class="col-xl-6">
                        <label class="form-control-label">Lastname<span class="text-danger ml-2">*</span></label>
                        <input type="text" class="form-control" required name="lastName" value="<?php echo $row['teacher_lastName']; ?>" id="exampleInputFirstName">
                      </div>
                    </div>
                    <div class="form-group row mb-3">
                      <div class="col-xl-6">
                        <label class="form-control-label">Designation<span class="text-danger ml-2">*</span></label>
                        <input type="text" class="form-control" required name="designation" value="<?php echo $row['teacher_designation']; ?>" id="exampleInputFirstName">
                      </div>
                      <div class="col-xl-6">
                        <label class="form-control-label">Email Address<span class="text-danger ml-2">*</span></label>
                        <input type="email" class="form-control" required name="emailAddress" value="<?php echo $row['teacher_email']; ?>" id="exampleInputFirstName">
                      </div>
                    </div>
                    <div class="form-group row mb-3">
                      <div class="col-xl-6">
                        <label class="form-control-label">Phone No<span class="text-danger ml-2">*</span></label>
                        <input type="text" class="form-control" name="phoneNo" value="<?php echo $row['teacher_phoneNo']; ?>" id="exampleInputFirstName">
                      </div>
                      <div class="col-xl-6">
                        <label class="form-control-label">Password<span class="text-danger ml-2">*</span></label>
                        <input type="text" class="form-control" name="password" value="<?php echo $row['teacher_password']; ?>" id="exampleInputPassword">
                      </div>
                    </div>
                    <?php
                    if (isset($Id)) {
                    ?>
                      <button type="submit" name="update" class="btn btn-warning">Update</button>
                      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <?php
                    } else {
                    ?>
                      <button type="submit" name="save" class="btn btn-primary">Save</button>
                    <?php
                    }
                    ?>
                  </form>
                </div>
              </div>

              <!-- Input Group -->
              <div class="row">
                <div class="col-lg-12">
                  <div class="card mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                      <h6 class="m-0 font-weight-bold text-primary">All Teachers</h6>
                    </div>
                    <div class="table-responsive p-3">
                      <table class="table align-items-center table-flush table-hover" id="dataTableHover">
                        <thead class="thead-light">
                          <tr>
                            <th>#</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Designation</th>
                            <th>Email Address</th>
                            <th>Phone No</th>
                            <th>Action</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                          $query = mysqli_query($conn, "SELECT * FROM teacher");
                          $i = 1;
                          while ($row = mysqli_fetch_array($query)) {
                          ?>
                            <tr>
                              <td><?php echo $i++; ?></td>
                              <td><?php echo $row['teacher_firstName']; ?></td>
                              <td><?php echo $row['teacher_lastName']; ?></td>
                              <td><?php echo $row['teacher_designation']; ?></td>
                              <td><?php echo $row['teacher_email']; ?></td>
                              <td><?php echo $row['teacher_phoneNo']; ?></td>
                              <td>
                                <a href="createClassTeacher.php?action=edit&Id=<?php echo $row['Id']; ?>" class="btn btn-info">Edit</a>
                                <a href="createClassTeacher.php?action=delete&Id=<?php echo $row['Id']; ?>" class="btn btn-danger">Delete</a>
                              </td>
                            </tr>
                          <?php } ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <?php include 'Includes/footer.php'; ?>
    </div>
  </div>

  <!-- Scroll to Top Button-->
  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
  </a>

  <!-- Logout Modal-->
  <?php include 'Includes/logoutmodal.php'; ?>

  <script src="../vendor/jquery/jquery.min.js"></script>
  <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
  <script src="js/ruang-admin.min.js"></script>

</body>

</html>