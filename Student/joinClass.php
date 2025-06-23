<?php
include '../Includes/session.php';
include '../Includes/dbcon.php';
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', realpath(__DIR__ . '/../../')); // adjust as needed
}



$room = $_GET['room'] ?? '';
if (!$room) {
    die("<div class='alert alert-danger m-4'>❌ Room not specified.</div>");
}

// Student info
$displayName = $_SESSION['firstName'] . ' ' . $_SESSION['lastName'];
$email = $_SESSION['email'] ?? '';
$studentId = $_SESSION['userId'] ?? null;  // Assuming userId is student identifier

$virtualclassId = 0;
$sessionId = null;

if ($studentId && $room) {
    $stmt = $conn->prepare("SELECT Id FROM tblvirtualclass WHERE jitsiLink LIKE CONCAT('%', ?, '%') LIMIT 1");
    $stmt->bind_param("s", $room);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $virtualclassId = $row['Id'];

        $insert = $conn->prepare("INSERT INTO tblvirtualsessions (virtualclassId, studentId, joinTime) VALUES (?, ?, NOW())");
        $insert->bind_param("is", $virtualclassId, $studentId);
        $insert->execute();

        $sessionId = $insert->insert_id;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Join Virtual Class</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="css/ruang-admin.min.css" rel="stylesheet">
    <style>
        html, body, #jaas-container {
            height: 100%;
            margin: 0;
            background: #000;
        }
    </style>
    <script src="https://8x8.vc/vpaas-magic-cookie-e6852fc2496b4be898cbb50968193ee4/external_api.js" async></script>
</head>
<body id="page-top">
<div id="wrapper">
    <?php include "Includes/sidebar.php"; ?>

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <?php include "Includes/topbar.php"; ?>

            <div class="container-fluid" id="container-wrapper">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h1 class="h4 text-white">Live Virtual Class</h1>
                    <a href="joinVirtualClass.php" class="btn btn-light btn-sm">⬅ Back to Class List</a>
                </div>

                <div class="card shadow border-0">
                    <div class="card-body p-0" style="height: 80vh;">
                        <div id="jaas-container"></div>
                    </div>
                </div>
            </div>
        </div>

        <?php include "Includes/footer.php"; ?>
    </div>
</div>

<script>
const sessionId = <?= json_encode($sessionId) ?>;

window.onload = () => {
    const domain = "8x8.vc";
    const options = {
        roomName: "vpaas-magic-cookie-e6852fc2496b4be898cbb50968193ee4/<?= htmlspecialchars($room) ?>",
        parentNode: document.querySelector('#jaas-container'),
        userInfo: {
            displayName: "<?= addslashes($displayName) ?>",
            email: "<?= addslashes($email) ?>"
        },
        configOverwrite: {
            startWithAudioMuted: true,
            startWithVideoMuted: true
        },
        interfaceConfigOverwrite: {
            SHOW_JITSI_WATERMARK: false,
            DEFAULT_REMOTE_DISPLAY_NAME: 'Participant'
        }
        // Optional: jwt: "your-jwt-token-if-needed"
    };

    const api = new JitsiMeetExternalAPI(domain, options);

    api.addEventListener('readyToClose', () => {
        if (sessionId) {
            fetch(`markLeft.php?session_id=${sessionId}`)
                .then(response => {
                    if (!response.ok) {
                        console.error('Failed to mark leave time');
                    }
                })
                .catch(error => console.error('Error marking leave time:', error));
        }
    });
};
</script>
</body>
</html>
