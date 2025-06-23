<?php
include '../Includes/dbcon.php';
include '../Includes/session.php';

$teacherId = $_SESSION['userId'];
$message = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $courseId = $_POST['courseId'];
    $classDate = $_POST['classDate'];
    $jitsiRoom = "ClassPlus-" . uniqid(); // Room name only

    $stmt = $conn->prepare("INSERT INTO tblvirtualclass (courseId, teacherId, jitsiLink, classDate, isActive) VALUES (?, ?, ?, ?, 1)");
    $stmt->bind_param("iiss", $courseId, $teacherId, $jitsiRoom, $classDate);
    if ($stmt->execute()) {
        $message = "<div class='alert alert-success'>Virtual class created successfully. <a href='startClass.php?room=$jitsiRoom' target='_blank'>Join Now</a></div>";
    } else {
        $message = "<div class='alert alert-danger'>Error creating virtual class.</div>";
    }
    $stmt->close();
}

// Handle end class
if (isset($_GET['end']) && is_numeric($_GET['end'])) {
    $endId = intval($_GET['end']);
    $stmt = $conn->prepare("UPDATE tblvirtualclass SET isActive = 0 WHERE Id = ? AND teacherId = ?");
    $stmt->bind_param("ii", $endId, $teacherId);
    if ($stmt->execute()) {
        $message = "<div class='alert alert-warning'>Class ended successfully.</div>";
    }
    $stmt->close();
}

// Fetch teacher's courses
$courses = [];
$query = "SELECT ca.Id, ca.CourseName, c.Program, c.`Year(Batch)`, c.section
          FROM tblclassteacher t
          INNER JOIN tblclassarms ca ON t.Id = ca.AssignedTo
          INNER JOIN tblclass c ON ca.ClassId = c.Id
          WHERE t.Id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $teacherId);
$stmt->execute();
$result = $stmt->get_result();
$courses = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Fetch upcoming classes
$classes = [];
$query = "SELECT vc.Id, vc.jitsiLink, vc.classDate, ca.CourseName 
          FROM tblvirtualclass vc 
          INNER JOIN tblclassarms ca ON vc.courseId = ca.Id 
          WHERE vc.teacherId = ? AND vc.isActive = 1 
          ORDER BY vc.classDate ASC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $teacherId);
$stmt->execute();
$res = $stmt->get_result();
$classes = $res->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link href="img/logo/attnlg.jpg" rel="icon">
  <title>Create Virtual Class</title>
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
          <h1 class="h3 mb-0 text-gray-800">Create Virtual Class</h1>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="./">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Create Virtual Class</li>
          </ol>
        </div>

        <div class="row">
          <div class="col-lg-12">
            <div class="card mb-4">
              <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">New Virtual Class Session</h6>
              </div>
              <div class="card-body">
                <?= $message ?>
                <form method="POST">
                  <div class="form-group">
                    <label for="courseId">Select Course</label>
                    <select class="form-control" name="courseId" required>
                      <option value="">-- Select Course --</option>
                      <?php foreach ($courses as $course): ?>
                        <option value="<?= $course['Id'] ?>">
                          <?= htmlspecialchars($course['CourseName'] . ' - ' . $course['Program'] . ' - ' . $course['Year(Batch)'] . ' - ' . $course['section']) ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="classDate">Class Date and Time</label>
                    <input type="datetime-local" name="classDate" class="form-control" required>
                  </div>
                  <button type="submit" class="btn btn-primary">Create Class</button>
                </form>

                <?php if (!empty($classes)): ?>
                  <hr>
                  <h5>Upcoming Classes</h5>
                  <div class="table-responsive">
                    <table class="table table-striped">
                      <thead class="thead-light">
                        <tr>
                          <th>Course</th>
                          <th>Date & Time</th>
                          <th>Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($classes as $vc): ?>
                          <tr>
                            <td><?= htmlspecialchars($vc['CourseName']) ?></td>
                            <td><?= date("d M Y, h:i A", strtotime($vc['classDate'])) ?></td>
                            <td>
                              <a href="startClass.php?room=<?= urlencode($vc['jitsiLink']) ?>" target="_blank" class="btn btn-success btn-sm">
                                Join as Moderator
                              </a>
                              <a href="?end=<?= $vc['Id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to end this class?');">
                                End Class
                              </a>
                            </td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                  </div>
                <?php endif; ?>

              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
    <?php include "Includes/footer.php"; ?>
  </div>
</div>
<a class="scroll-to-top rounded" href="#page-top"><i class="fas fa-angle-up"></i></a>
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="js/ruang-admin.min.js"></script>
</body>
</html>
