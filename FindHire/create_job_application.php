<?php
session_start();
require 'core/dbConfig.php';

if ($_SESSION['role'] !== 'HR') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $created_by = $_SESSION['user_id'];

    $stmt = $pdo->prepare("INSERT INTO job_posts (title, description, created_by) VALUES (?, ?, ?)");
    $stmt->execute([$title, $description, $created_by]);

    echo "<div style='text-align: center; font-family: Arial, sans-serif; padding: 20px;'>
            <h2>Job post created successfully.</h2>
            <a href='hr_dashboard.php' style='text-decoration: none; color: #6d4c41; font-size: 18px;'>Return to Menu</a>
          </div>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Job Post</title>
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

        .return-home {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Add a Job Post</h1>
        <form action="" method="POST">
            <input type="text" name="title" required placeholder="Job Title">
            <textarea name="description" required placeholder="Job Description"></textarea>
            <button type="submit">Create Job Post</button>
        </form>
        <div class="return-home">
            <a href="hr_dashboard.php"><button type="button">Return to Home</button></a>
        </div>
    </div>
</body>
</html>
