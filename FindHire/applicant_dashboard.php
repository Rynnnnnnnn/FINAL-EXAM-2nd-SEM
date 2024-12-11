<?php
session_start();
require 'core/dbConfig.php';
require 'core/models.php';

if ($_SESSION['role'] !== 'Applicant') {
    header("Location: login.php");
    exit();
}

$applicant_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT a.*, j.title AS job_title, j.description AS job_description, a.status AS application_status
    FROM applications a
    JOIN job_posts j ON a.job_id = j.job_id
    WHERE a.applicant_id = ?
");
$stmt->execute([$applicant_id]);
$applications = $stmt->fetchAll();

$stmtJobPosts = $pdo->query("SELECT * FROM job_posts");
$job_posts = $stmtJobPosts->fetchAll();

$stmtMessages = $pdo->prepare("
    SELECT m.*, u.username AS sender_name
    FROM messages m
    JOIN users u ON m.sender_id = u.user_id
    WHERE m.receiver_id = ?
    ORDER BY m.sent_at DESC
");
$stmtMessages->execute([$applicant_id]);
$messages = $stmtMessages->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Applicant Dashboard</title>
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

        h1, h2, h3 {
            font-family: 'Poppins', sans-serif;
            color: #3e2723;
        }

        h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
        }

        h2 {
            font-size: 2em;
            margin-top: 30px;
        }

        ul {
            list-style-type: none;
            padding: 0;
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

        .dashboard-container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 40px;
            width: 100%;
            max-width: 800px;
            text-align: left;
        }

        .section-box {
            background-color: #fff;
            border: 2px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
        }

        .section-box h2 {
            font-size: 1.8em;
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

    </style>
</head>
<body>

    <div class="dashboard-container">
        <h1>Welcome to the Applicant Dashboard</h1>

        <div class="section-box">
            <h2>Available Job Posts</h2>
            <?php if ($job_posts): ?>
                <ul>
                    <?php foreach ($job_posts as $job): ?>
                        <li>
                            <h3><?php echo htmlspecialchars($job['title']); ?></h3>
                            <p><strong>Description:</strong> <?php echo htmlspecialchars($job['description']); ?></p>
                            <a href="apply_job.php?job_id=<?php echo $job['job_id']; ?>"><button>Apply</button></a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No job posts available at the moment.</p>
            <?php endif; ?>
        </div>

        <div class="section-box">
            <h2>Your Applications</h2>
            <?php if ($applications): ?>
                <ul>
                    <?php foreach ($applications as $application): ?>
                        <li>
                            <strong><?php echo htmlspecialchars($application['job_title']); ?></strong><br>
                            <p><strong>Status:</strong> <?php echo htmlspecialchars($application['application_status']); ?></p>
                            <p><strong>Description:</strong> <?php echo htmlspecialchars($application['job_description']); ?></p>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>You haven't applied to any jobs yet.</p>
            <?php endif; ?>
        </div>

        <div class="section-box">
            <h2>Your Messages</h2>
            <?php if ($messages): ?>
                <ul>
                    <?php foreach ($messages as $message): ?>
                        <li>
                            <strong>From: <?php echo htmlspecialchars($message['sender_name']); ?></strong><br>
                            <p><?php echo htmlspecialchars($message['message']); ?></p>
                            <p><small>Sent on: <?php echo $message['sent_at']; ?></small></p>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No messages from HR yet.</p>
            <?php endif; ?>
        </div>

        <p><a href="contact_hr.php"><button>Send Message to HR</button></a></p>

        <div class="logout-link">
            <p><a href="logout.php">Logout</a></p>
        </div>
    </div>

</body>
</html>
