<?php
include '../Includes/dbcon.php';
include '../Includes/session.php';

// Assuming student information is stored in session variables
$student_name = $_SESSION['firstName'] . ' ' . $_SESSION['lastName'];
$student_id = $_SESSION['userId']; // Assuming this holds student's ID

// Fetching course_id from URL parameter
if (isset($_GET['course_id'])) {
    $course_id = $_GET['course_id'];
} else {
    // Redirect or handle error if course_id is not provided
    header("Location: index.php");
    exit();
}

// Function to calculate attendance details for a student in a specific course
function calculateAttendanceDetails($student_id, $course_id, $conn) {
    $query = "SELECT COUNT(*) AS total_attendance, SUM(status) AS attended, COUNT(CASE WHEN status = 1 THEN 1 END) AS present_days FROM tblattendance WHERE SymbolNo = $student_id AND CourseId = $course_id";
    $result = mysqli_query($conn, $query);
    
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        if ($row) {
            $total_attendance = $row['total_attendance'];
            $attended = $row['attended'];
            $present_days = $row['present_days'];
            $absent_days = $total_attendance - $present_days;

            return [
                'attendance_percentage' => ($total_attendance > 0) ? round(($attended / $total_attendance) * 100, 2) : 0,
                'total_class_days' => $total_attendance,
                'present_days' => $present_days,
                'absent_days' => $absent_days,
            ];
        }
    }
    
    // Return default values or handle no data scenario
    return [
        'attendance_percentage' => 0,
        'total_class_days' => 0,
        'present_days' => 0,
        'absent_days' => 0,
    ];
}

// Calculate attendance details for the selected course
$attendance_data = calculateAttendanceDetails($student_id, $course_id, $conn);

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
    <title>Student Attendance</title>
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="css/ruang-admin.min.css" rel="stylesheet">
    <style>
        .chart-container {
            position: relative;
            margin: auto;
            height: 300px;
            width: 80%;
        }
    </style>
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
                        <h1 class="h3 mb-0 text-gray-800">Welcome, <?php echo $student_name; ?></h1>
                    </div>

                    <!-- Course Attendance Details -->
                    <div class="row mb-3">
                        <div class="col-lg-6">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Course Attendance</h6>
                                </div>
                                <div class="card-body">
                                    <div class="text-center">
                                        <h2 class="text-primary"><?php echo $attendance_data['attendance_percentage']; ?>%</h2>
                                        <p class="text-muted">Overall Attendance</p>
                                    </div>
                                    <div class="chart-container">
                                        <canvas id="courseAttendanceChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <!-- Attendance Alert Box -->
                            <?php if ($attendance_data['attendance_percentage'] < 70): ?>
                            <div class="alert alert-danger" role="alert">
                                Your attendance is below the required threshold of 70%. Please ensure to attend more classes to improve your attendance percentage.
                            </div>
                            <?php else: ?>
                            <div class="alert alert-success" role="alert">
                                Your attendance is above the required threshold. Keep up the good work!
                            </div>
                            <?php endif; ?>

                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Attendance Details</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p>Total Classes: <strong><?php echo $attendance_data['total_class_days']; ?></strong></p>
                                            <p>Present : <strong><?php echo $attendance_data['present_days']; ?></strong></p>
                                            <p>Absent : <strong><?php echo $attendance_data['absent_days']; ?></strong></p>
                                        </div>
                                        <div class="col-md-6">
                                            <!-- Add any additional attendance details here -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!---Container Fluid-->
            </div>
            <!-- Footer -->
            <?php include 'Includes/footer.php'; ?>
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Function to initialize and render the pie chart
        function renderPieChart(attendancePercentage) {
            var courseAttendanceChart = document.getElementById('courseAttendanceChart').getContext('2d');
            var myCourseAttendanceChart = new Chart(courseAttendanceChart, {
                type: 'pie',
                data: {
                    datasets: [{
                        data: [attendancePercentage, 100 - attendancePercentage],
                        backgroundColor: ['#4e73df', '#e74a3b'],
                        hoverBackgroundColor: ['#2e59d9', '#e74a3b'],
                        borderWidth: 2,
                        hoverBorderColor: 'rgba(234, 236, 244, 1)',
                    }],
                    labels: ['Attended', 'Absent'],
                },
                options: {
                    maintainAspectRatio: false,
                    tooltips: {
                        backgroundColor: 'rgb(255,255,255)',
                        bodyFontColor: '#858796',
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        xPadding: 15,
                        yPadding: 15,
                        displayColors: false,
                        caretPadding: 10,
                    },
                    legend: {
                        display: false,
                    },
                    cutoutPercentage: 80,
                },
            });
        }

        // Call the function with attendance percentage data
        renderPieChart(<?php echo $attendance_data['attendance_percentage']; ?>);
    </script>
</body>

</html>
