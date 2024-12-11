<?php
session_start();
require 'core/dbConfig.php';
require 'core/models.php';

if ($_SESSION['role'] !== 'HR') {
    header("Location: login.php");
    exit();
}

$hr_id = $_SESSION['user_id'];

$stmtJobPosts = $pdo->prepare("SELECT * FROM job_posts WHERE created_by = ?");
$stmtJobPosts->execute([$hr_id]);
$job_posts = $stmtJobPosts->fetchAll();

$stmtApplications = $pdo->prepare("
    SELECT a.*, j.title AS job_title, u.username AS applicant_name, a.status AS application_status
    FROM applications a
    JOIN job_posts j ON a.job_id = j.job_id
    JOIN users u ON a.applicant_id = u.user_id
    WHERE j.created_by = ?
");
$stmtApplications->execute([$hr_id]);
$applications = $stmtApplications->fetchAll();

$stmtMessages = $pdo->prepare("
    SELECT m.*, u.username AS sender_name
    FROM messages m
    JOIN users u ON m.sender_id = u.user_id
    WHERE m.receiver_id = ?
    ORDER BY m.sent_at DESC
");
$stmtMessages->execute([$hr_id]);
$messages = $stmtMessages->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = trim($_POST['message']);
    $receiver_id = $_POST['receiver_id'];  
    $sender_id = $_SESSION['user_id']; 

    if (!empty($message) && !empty($receiver_id)) {
        $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, message, message_type) VALUES (?, ?, ?, 'reply')");
        if ($stmt->execute([$sender_id, $receiver_id, $message])) {
            $_SESSION['message'] = "Reply sent successfully!";
            header("Location: hr_dashboard.php");  
            exit();
        } else {
            $_SESSION['message'] = "Error sending reply.";
        }
    } else {
        $_SESSION['message'] = "Please fill in all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body, html {
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
            background-color: #f4f1f0;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            max-width: 1000px;
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        h1, h2, h3 {
            font-family: 'Poppins', sans-serif;
            color: #3e2723;
        }

        h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
            text-align: center;
        }

        h2 {
            font-size: 2em;
            margin-top: 30px;
            text-align: center;
        }

        ul {
            list-style-type: none;
            padding: 0;
            width: 100%;
        }

        li {
            background-color: #fff;
            margin-bottom: 15px;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        p {
            font-size: 1em;
            color: #5d4037;
        }

        a {
            text-decoration: none;
            color: #6d4c41;
        }

        a:hover {
            text-decoration: underline;
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

        .section-box {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 40px;
            margin-bottom: 30px;
            width: 100%;
            max-width: 800px;
        }

        .logout-link {
            margin-top: 20px;
            text-align: center;
        }

        .logout-link a {
            color: #6d4c41;
            font-size: 16px;
        }

        .logout-link a:hover {
            text-decoration: underline;
        }

        textarea {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            margin-bottom: 20px;
            border-radius: 8px;
            border: 1px solid #ddd;
            font-size: 16px;
        }

        form {
            margin-top: 15px;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>HR Dashboard</h1>

        <div class="section-box">
            <p><a href="create_job_application.php"><button>Add New Job Post</button></a></p>
        </div>

        <div class="section-box">
            <h2>Manage Job Posts</h2>
            <?php if ($job_posts): ?>
                <ul>
                    <?php foreach ($job_posts as $job): ?>
                        <li>
                            <strong><?php echo htmlspecialchars($job['title']); ?></strong><br>
                            <p><?php echo htmlspecialchars($job['description']); ?></p>
                            <a href="edit_job_application.php?job_id=<?php echo $job['job_id']; ?>">Edit</a> |
                            <a href="delete_job_application.php?job_id=<?php echo $job['job_id']; ?>">Delete</a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No job posts available.</p>
            <?php endif; ?>
        </div>

        <div class="section-box">
            <h2>Applications</h2>
            <?php if ($applications): ?>
                <ul>
                    <?php foreach ($applications as $application): ?>
                        <li>
                            <strong><?php echo htmlspecialchars($application['job_title']); ?></strong><br>
                            <p><strong>Applicant:</strong> <?php echo htmlspecialchars($application['applicant_name']); ?></p>
                            <p><strong>Status:</strong> <?php echo htmlspecialchars($application['application_status']); ?></p>
                            <a href="update.php?application_id=<?php echo $application['application_id']; ?>">Update Status</a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No applications yet.</p>
            <?php endif; ?>
        </div>

        <div class="section-box">
            <h2>Messages from Applicants</h2>
            <?php if ($messages): ?>
                <ul>
                    <?php foreach ($messages as $message): ?>
                        <li>
                            <strong>From: <?php echo htmlspecialchars($message['sender_name']); ?></strong><br>
                            <p><?php echo htmlspecialchars($message['message']); ?></p>
                            <p><small>Sent on: <?php echo $message['sent_at']; ?></small></p>

                            <form action="hr_dashboard.php" method="POST">
                                <input type="hidden" name="receiver_id" value="<?php echo $message['sender_id']; ?>">
                                <textarea name="message" rows="3" required></textarea><br>
                                <button type="submit">Reply</button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No messages from applicants yet.</p>
            <?php endif; ?>
        </div>

        <div class="logout-link">
            <p><a href="logout.php">Logout</a></p>
        </div>
    </div>

</body>
</html>
