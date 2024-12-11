<?php
session_start();
require 'core/dbConfig.php';
require 'core/models.php';

if ($_SESSION['role'] !== 'Applicant') {
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
    $applicant_id = $_SESSION['user_id'];
    $resume = $_FILES['resume'];

    if (!empty($resume['name'])) {
        // Check if the file is a PDF
        $fileType = pathinfo($resume['name'], PATHINFO_EXTENSION);
        if (strtolower($fileType) !== 'pdf') {
            echo "<div style='text-align: center; font-family: Arial, sans-serif; padding: 20px;'>
                    <h2>Only PDF files are allowed.</h2>
                    <a href='apply_job.php?job_id=$job_id' style='text-decoration: none; color: #6d4c41; font-size: 18px;'>Go back</a>
                  </div>";
            exit();
        }

        $resumePath = 'uploads/' . basename($resume['name']);
        
        if (move_uploaded_file($resume['tmp_name'], $resumePath)) {
            $stmt = $pdo->prepare("INSERT INTO applications (job_id, applicant_id, resume) VALUES (?, ?, ?)");
            $stmt->execute([$job_id, $applicant_id, $resumePath]);

            echo "<div style='text-align: center; font-family: Arial, sans-serif; padding: 20px;'>
                    <h2>Application submitted successfully.</h2>
                    <a href='applicant_dashboard.php' style='text-decoration: none; color: #6d4c41; font-size: 18px;'>Return to Dashboard</a>
                  </div>";
        } else {
            echo "Error uploading resume.";
        }
    } else {
        echo "Please upload a resume.";
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply for Job</title>
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

        input[type="file"] {
            display: block;
            margin: 10px auto;
            padding: 12px;
            font-size: 16px;
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

        .return-home {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Apply for Job: <?php echo htmlspecialchars($job['title']); ?></h1>
        <form action="apply_job.php?job_id=<?php echo $job_id; ?>" method="POST" enctype="multipart/form-data">
            <p><label for="resume">Submit Your Resume (PDF only):</label></p>
            <input type="file" name="resume" accept=".pdf" required>
            <button type="submit">Apply</button>
        </form>
        <div class="return-home">
            <a href="applicant_dashboard.php"><button type="button">Return to Dashboard</button></a>
        </div>
    </div>
</body>
</html>
