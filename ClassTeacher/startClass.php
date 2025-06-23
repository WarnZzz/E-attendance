<?php
include '../Includes/dbcon.php';
include '../Includes/session.php';

$roomName = $_GET['room'] ?? '';
if (!$roomName) {
    die("<div class='alert alert-danger m-4'>❌ Room not specified.</div>");
}

// Teacher user info
$displayName = $_SESSION['firstName'] . ' ' . $_SESSION['lastName'];
$email = $_SESSION['email'] ?? '';

// Fetch virtualclassId from tblvirtualclass using the room name (jitsiLink contains roomName)
$virtualclassId = 0;
$stmt = $conn->prepare("SELECT Id FROM tblvirtualclass WHERE jitsiLink LIKE CONCAT('%', ?, '%') LIMIT 1");
$stmt->bind_param("s", $roomName);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $virtualclassId = (int)$row['Id'];
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Virtual Class - <?= htmlspecialchars($roomName) ?></title>
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
                        <h1 class="h4 mb-0 text-white">Live Virtual Class</h1>
                        <a href="createVirtualClass.php" class="btn btn-light btn-sm">⬅ Back to Classes</a>
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
    const virtualclassId = <?= json_encode($virtualclassId) ?>;
    window.onload = () => {
        const domain = "8x8.vc";
        const options = {
            roomName: "vpaas-magic-cookie-e6852fc2496b4be898cbb50968193ee4/<?= htmlspecialchars($roomName) ?>",
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
                DEFAULT_REMOTE_DISPLAY_NAME: 'Student'
            }
            // jwt: "YOUR_JWT_HERE" // Uncomment if needed
        };

        const api = new JitsiMeetExternalAPI(domain, options);

        api.addEventListener('readyToClose', () => {
            if (!virtualclassId) {
                console.error('virtualclassId not set. Cannot mark attendance.');
                return;
            }

            fetch(`markAttendance.php?virtualclassId=${virtualclassId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log('Attendance marked successfully');
                    } else {
                        console.warn('Attendance marking failed:', data.error || data);
                    }
                })
                .catch(err => {
                    console.error('Error calling markAttendance.php:', err);
                });
        });
    };
    </script>
</body>
</html>
