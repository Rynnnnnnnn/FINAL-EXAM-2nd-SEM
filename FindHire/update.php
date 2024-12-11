<?php
session_start();
require 'core/dbConfig.php';
require 'core/models.php';

if ($_SESSION['role'] !== 'HR') {
    header("Location: login.php");
    exit();
}

if (isset($_GET['application_id'])) {
    $application_id = $_GET['application_id'];

    $stmt = $pdo->prepare("SELECT * FROM applications WHERE application_id = ?");
    $stmt->execute([$application_id]);
    $application = $stmt->fetch();

    if (!$application) {
        $_SESSION['message'] = "Application not found.";
        header("Location: hr_dashboard.php");
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $status = $_POST['status'];

        $stmt = $pdo->prepare("UPDATE applications SET status = ? WHERE application_id = ?");
        if ($stmt->execute([$status, $application_id])) {
            $_SESSION['message'] = "Application status updated to $status.";
        } else {
            $_SESSION['message'] = "Error updating status.";
        }

        header("Location: hr_dashboard.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Application Status</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body, html {
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
            background-color: #f4f1f0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }

        h1 {
            font-family: 'Poppins', sans-serif;
            color: #3e2723;
            font-size: 2.5em;
            margin-bottom: 20px;
        }

        .form-container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 40px;
            width: 100%;
            max-width: 500px;
            text-align: center;
        }

        .radio-group {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin: 20px 0;
        }

        .radio-group label {
            font-size: 16px;
            font-family: 'Inter', sans-serif;
            color: #333;
        }

        button {
            padding: 12px 20px;
            background-color: #6d4c41;
            color: #fff;
            font-size: 16px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #4e342e;
        }

        .return-home {
            margin-top: 20px;
        }

        a {
            text-decoration: none;
            color: #6d4c41;
            font-size: 16px;
        }

        a:hover {
            text-decoration: underline;
        }

        p.error-message {
            color: red;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Update Application Status</h1>

        <?php if (isset($_SESSION['message'])): ?>
            <p class="error-message"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></p>
        <?php endif; ?>

        <form action="update.php?application_id=<?php echo $application['application_id']; ?>" method="POST">
            <div class="radio-group">
                <label>
                    <input type="radio" name="status" value="Accepted" <?php if ($application['status'] == 'Accepted') echo 'checked'; ?>>
                    Accept
                </label>
                <label>
                    <input type="radio" name="status" value="Rejected" <?php if ($application['status'] == 'Rejected') echo 'checked'; ?>>
                    Reject
                </label>
            </div>
            <button type="submit">Update Status</button>
        </form>

        <div class="return-home">
            <a href="hr_dashboard.php"><button type="button">Back to HR Dashboard</button></a>
        </div>
    </div>
</body>
</html>
