<ul class="navbar-nav sidebar sidebar-light accordion" id="accordionSidebar">
  <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php" style="background-color: #6777EF;">
    <div class="sidebar-brand-icon">
      <img src="img/logo/admin_logo.png">
    </div>
    <div class="sidebar-brand-text mx-3" style="font-size: larger;">AMS</div>
  </a>
  <hr class="sidebar-divider my-0">

  <li class="nav-item active">
    <a class="nav-link text-dark" href="index.php" style="font-size: larger;">
      <i class="fas fa-fw fa-tachometer-alt"></i>
      <span>Dashboard</span>
    </a>
  </li>

  <hr class="sidebar-divider">
  <div class="sidebar-heading text-dark" style="font-size: medium;">
    Courses
  </div>
  <li class="nav-item">
    <a class="nav-link collapsed text-dark" href="#" data-toggle="collapse" data-target="#collapseManageCourses" aria-expanded="true" aria-controls="collapseManageCourses" style="font-size: larger;">
      <i class="fas fa-chalkboard"></i>
      <span>Manage Courses</span>
    </a>
    <div id="collapseManageCourses" class="collapse" aria-labelledby="headingManageCourses" data-parent="#accordionSidebar">
      <div class="bg-white py-2 collapse-inner rounded">
        <a class="collapse-item text-dark" href="createClass.php" style="font-size: medium;">Create Course</a>
      </div>
    </div>
  </li>

  <hr class="sidebar-divider">
  <div class="sidebar-heading text-dark" style="font-size: medium;">
    Teachers
  </div>
  <li class="nav-item">
    <a class="nav-link collapsed text-dark" href="#" data-toggle="collapse" data-target="#collapseManageTeachers" aria-expanded="true" aria-controls="collapseManageTeachers" style="font-size: larger;">
      <i class="fas fa-chalkboard-teacher"></i>
      <span>Manage Teachers</span>
    </a>
    <div id="collapseManageTeachers" class="collapse" aria-labelledby="headingManageTeachers" data-parent="#accordionSidebar">
      <div class="bg-white py-2 collapse-inner rounded">
        <a class="collapse-item text-dark" href="createClassTeacher.php" style="font-size: medium;">Create Teachers</a>
      </div>
    </div>
  </li>

  <hr class="sidebar-divider">
  <div class="sidebar-heading text-dark" style="font-size: medium;">
    Students
  </div>
  <li class="nav-item">
    <a class="nav-link collapsed text-dark" href="#" data-toggle="collapse" data-target="#collapseManageStudents" aria-expanded="true" aria-controls="collapseManageStudents" style="font-size: larger;">
      <i class="fas fa-user-graduate"></i>
      <span>Manage Students</span>
    </a>
    <div id="collapseManageStudents" class="collapse" aria-labelledby="headingManageStudents" data-parent="#accordionSidebar">
      <div class="bg-white py-2 collapse-inner rounded">
        <a class="collapse-item text-dark" href="createStudents.php" style="font-size: medium;">Create Students</a>
      </div>
    </div>
  </li>

  <hr class="sidebar-divider">
  <div class="sidebar-heading text-dark" style="font-size: medium;">
    Teacher Assign
  </div>
  <li class="nav-item">
    <a class="nav-link collapsed text-dark" href="#" data-toggle="collapse" data-target="#collapseAssignTeacher" aria-expanded="true" aria-controls="collapseAssignTeacher" style="font-size: larger;">
      <i class="fas fa-chalkboard-teacher"></i>
      <span>Assign Teacher</span>
    </a>
    <div id="collapseAssignTeacher" class="collapse" aria-labelledby="headingAssignTeacher" data-parent="#accordionSidebar">
      <div class="bg-white py-2 collapse-inner rounded">
        <a class="collapse-item text-dark" href="assignCourseTeacher.php" style="font-size: medium;">Assign Teacher</a>
      </div>
    </div>
  </li>

  <hr class="sidebar-divider">
  <div class="sidebar-heading text-dark" style="font-size: medium;">
    Student Assign
  </div>
  <li class="nav-item">
    <a class="nav-link collapsed text-dark" href="#" data-toggle="collapse" data-target="#collapseAssignStudent" aria-expanded="true" aria-controls="collapseAssignStudent" style="font-size: larger;">
      <i class="fas fa-user-graduate"></i>
      <span>Assign Student</span>
    </a>
    <div id="collapseAssignStudent" class="collapse" aria-labelledby="headingAssignStudent" data-parent="#accordionSidebar">
      <div class="bg-white py-2 collapse-inner rounded">
        <a class="collapse-item text-dark" href="assignCourseStudent.php" style="font-size: medium;">Assign Student</a>
      </div>
    </div>
  </li>
</ul>