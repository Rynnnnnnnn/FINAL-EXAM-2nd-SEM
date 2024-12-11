<?php
session_start();
require 'core/dbConfig.php';
require 'core/models.php';

if ($_SESSION['role'] !== 'HR') {
    header("Location: login.php");
    exit();
}

$job_id = $_GET['job_id'] ?? null;

if (!$job_id) {
    echo "Job ID is missing.";
    exit();
}

$job = getJobPostByID($pdo, $job_id);

if (!$job) {
    echo "Job not found.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];

    $stmt = $pdo->prepare("UPDATE job_posts SET title = ?, description = ? WHERE job_id = ?");
    $updated = $stmt->execute([$title, $description, $job_id]);

    if ($updated) {
        echo "<div style='text-align: center; font-family: Arial, sans-serif; padding: 20px;'>
                <h2>Job post updated successfully.</h2>
                <a href='hr_dashboard.php' style='text-decoration: none; color: #6d4c41; font-size: 18px;'>Go back to HR Dashboard</a>
              </div>";
        exit();
    } else {
        echo "<div style='text-align: center; font-family: Arial, sans-serif; padding: 20px;'>
                <h2>Error updating job post.</h2>
                <a href='edit_job_application.php?job_id=$job_id' style='text-decoration: none; color: #6d4c41; font-size: 18px;'>Try Again</a>
              </div>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Job Post</title>
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

        input, textarea {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border-radius: 8px;
            border: 1px solid #ddd;
            font-size: 16px;
        }

        textarea {
            resize: none;
            height: 100px;
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

        a {
            text-decoration: none;
            color: #6d4c41;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Edit Job Post</h1>
        <form action="" method="POST">
            <input type="hidden" name="job_id" value="<?php echo htmlspecialchars($job['job_id']); ?>">
            <input type="text" name="title" value="<?php echo htmlspecialchars($job['title']); ?>" required placeholder="Job Title">
            <textarea name="description" required placeholder="Job Description"><?php echo htmlspecialchars($job['description']); ?></textarea>
            <button type="submit">Update Job</button>
        </form>
        <p><a href="hr_dashboard.php">Cancel and Go Back</a></p>
    </div>
</body>
</html>
