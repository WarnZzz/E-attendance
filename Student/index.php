<?php
include '../Includes/dbcon.php';
include '../Includes/session.php';

// Check and assign session values
$student_name = isset($_SESSION['firstName'], $_SESSION['lastName']) 
    ? $_SESSION['firstName'] . ' ' . $_SESSION['lastName'] 
    : 'Student';

$student_id = isset($_SESSION['userId']) ? intval($_SESSION['userId']) : 0;

// Function to calculate overall attendance percentage
function calculateOverallAttendance($student_id, $conn) {
    $query = "SELECT COUNT(*) AS total_attendance, 
                     SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) AS present_days 
              FROM tblattendance 
              WHERE SymbolNo = $student_id";

    $result = mysqli_query($conn, $query);
    if (!$result) {
        die("Attendance query failed: " . mysqli_error($conn));
    }

    $row = mysqli_fetch_assoc($result);

    $total_attendance = $row['total_attendance'];
    $present_days = $row['present_days'];
    $absent_days = $total_attendance - $present_days;

    return [
        'attendance_percentage' => ($total_attendance > 0) ? round(($present_days / $total_attendance) * 100, 2) : 0,
        'total_class_days' => $total_attendance,
        'present_days' => $present_days,
        'absent_days' => $absent_days,
    ];
}

// Fetch courses
$query_courses = "SELECT tblclassarms.Id, tblclassarms.CourseName 
                  FROM tblclassarms
                  INNER JOIN tblclass ON tblclassarms.ClassId = tblclass.Id
                  INNER JOIN tblstudents ON tblstudents.ClassId = tblclass.Id 
                  WHERE tblstudents.SymbolNo = $student_id";

$rs = mysqli_query($conn, $query_courses);
if (!$rs) {
    die("Course query failed: " . mysqli_error($conn));
}

$courses = [];
while ($row = mysqli_fetch_assoc($rs)) {
    $courses[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Student Dashboard</title>
    <link href="img/logo/attnlg.jpg" rel="icon">
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/ruang-admin.min.css" rel="stylesheet">
    <style>
        .chart-container {
            margin: auto;
            height: 300px;
            width: 80%;
        }
    </style>
</head>

<body id="page-top">
    <div id="wrapper">
        <?php include "Includes/sidebar.php"; ?>

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include "Includes/topbar.php"; ?>

                <div class="container-fluid" id="container-wrapper">
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Welcome, <?php echo htmlspecialchars($student_name); ?></h1>
                    </div>

                    <!-- Overall Attendance Section -->
                    <div class="row mb-3">
                        <?php $attendance_data = calculateOverallAttendance($student_id, $conn); ?>

                        <div class="col-lg-6">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Overall Attendance</h6>
                                </div>
                                <div class="card-body">
                                    <div class="text-center">
                                        <h2 class="text-primary">
                                            <?php echo $attendance_data['attendance_percentage']; ?>%
                                        </h2>
                                        <p class="text-muted">Overall Attendance</p>
                                    </div>
                                    <div class="chart-container">
                                        <canvas id="overallAttendanceChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Attendance Breakdown -->
                        <div class="col-lg-6">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Attendance Details</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p>Total Classes: <strong><?php echo $attendance_data['total_class_days']; ?></strong></p>
                                            <p>Present: <strong><?php echo $attendance_data['present_days']; ?></strong></p>
                                            <p>Absent: <strong><?php echo $attendance_data['absent_days']; ?></strong></p>
                                        </div>
                                        <div class="col-md-6">
                                            <!-- Optional extra info -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <?php include 'Includes/footer.php'; ?>
        </div>
    </div>

    <!-- Scroll to Top -->
    <a class="scroll-to-top rounded" href="#page-top"><i class="fas fa-angle-up"></i></a>

    <!-- Scripts -->
    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/ruang-admin.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Attendance Pie Chart -->
    <script>
        function renderPieChart(attendancePercentage) {
            var ctx = document.getElementById('overallAttendanceChart').getContext('2d');
            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['Present', 'Absent'],
                    datasets: [{
                        data: [attendancePercentage, 100 - attendancePercentage],
                        backgroundColor: ['#4e73df', '#e74a3b'],
                        hoverBackgroundColor: ['#2e59d9', '#f6c23e'],
                        borderWidth: 2
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
                    legend: {
                        display: true,
                        position: 'bottom'
                    },
                    tooltips: {
                        enabled: true
                    }
                }
            });
        }

        renderPieChart(<?php echo $attendance_data['attendance_percentage']; ?>);
    </script>
</body>

</html>
