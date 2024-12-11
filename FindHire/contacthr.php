<?php
session_start();
require 'core/dbConfig.php';

if ($_SESSION['role'] !== 'Applicant') {
    header("Location: login.php");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE role = 'HR'");
$stmt->execute();
$hr_users = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = trim($_POST['message']);
    $receiver_id = $_POST['receiver_id'];  
    $sender_id = $_SESSION['user_id'];  

    if (!empty($message) && !empty($receiver_id)) {
        $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
        if ($stmt->execute([$sender_id, $receiver_id, $message])) {
            $_SESSION['message'] = "Message sent successfully!";
            header("Location: contacthr.php");
            exit();
        } else {
            $_SESSION['message'] = "Error sending message.";
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
    <title>Send Message to HR</title>
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
            margin-bottom: 20px;
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

        .form-container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 40px;
            width: 100%;
            max-width: 600px;
            text-align: left;
        }

        .form-container h2 {
            font-size: 1.8em;
            margin-bottom: 20px;
        }

        .form-container select, .form-container textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 1em;
        }

        .form-container textarea {
            resize: vertical;
        }

        .message {
            color: red;
            font-size: 1em;
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

    <div class="form-container">
        <h1>Send Message to HR</h1>

        <?php if (isset($_SESSION['message'])): ?>
            <p class="message"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></p>
        <?php endif; ?>

        <form action="contacthr.php" method="POST">
            <p>
                <label for="receiver_id">Select HR:</label>
                <select name="receiver_id" required>
                    <?php foreach ($hr_users as $hr): ?>
                        <option value="<?php echo $hr['user_id']; ?>"><?php echo htmlspecialchars($hr['username']); ?></option>
                    <?php endforeach; ?>
                </select>
            </p>
            <p>
                <label for="message">Message:</label>
                <textarea name="message" rows="5" required></textarea>
            </p>
            <button type="submit">Send Message</button>
        </form>

        <p><a href="applicant_dashboard.php">Go back to Dashboard</a></p>
    </div>

</body>
</html>
