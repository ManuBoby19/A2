<?php

$host = "localhost";
$username = "root";
$password = "rootPass@2024";
$dbname = "news_db";

$conn = mysqli_connect($host, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$sql = "SELECT title, content, date_published FROM news ORDER BY date_published DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dynamic News Page</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { width: 60%; margin: auto; padding: 20px; }
        .news-item { margin-bottom: 20px; padding: 10px; border-bottom: 1px solid #ddd; }
        .news-title { font-size: 24px; color: #333; }
        .news-date { font-size: 14px; color: #666; }
        .news-content { font-size: 18px; color: #555; }
        .form-container { margin-top: 30px; padding: 10px; border: 1px solid #ddd; }
        .form-container input, .form-container textarea { width: 100%; margin: 5px 0; padding: 10px; }
        .form-container button { padding: 10px 20px; background-color: #28a745; color: #fff; border: none; cursor: pointer; }
        .form-container button:hover { background-color: #218838; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Latest News</h1>
        
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<div class='news-item'>";
                echo "<div class='news-title'>" . htmlspecialchars($row["title"]) . "</div>";
                echo "<div class='news-date'>Published on: " . htmlspecialchars($row["date_published"]) . "</div>";
                echo "<div class='news-content'>" . htmlspecialchars($row["content"]) . "</div>";
                echo "</div>";
            }
        } else {
            echo "<p>No news available.</p>";
        }
        ?>
    
        <div class="form-container">
            <h2>Add New News Article</h2>
            <form method="POST" action="">
                <input type="text" name="title" placeholder="News Title" required>
                <textarea name="content" rows="4" placeholder="News Content" required></textarea>
                <button type="submit">Submit</button>
            </form>
        </div>

        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (!empty($_POST["title"]) && !empty($_POST["content"])) {
                $title = $_POST["title"];
                $content = $_POST["content"];
                $date_published = date("Y-m-d");

                // Use prepared statement to prevent SQL injection
                $stmt = $conn->prepare("INSERT INTO news (title, content, date_published) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $title, $content, $date_published);

                if ($stmt->execute()) {
                    echo "<p style='color: green;'>New article added successfully!</p>";
                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit;
                } else {
                    echo "<p style='color: red;'>Error: " . $stmt->error . "</p>";
                }
                
                $stmt->close();
            } else {
                echo "<p style='color: red;'>Please fill out all required fields.</p>";
            }
        }

        $conn->close();
        ?>
    </div>
</body>
</html>
