<?php
// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "job_portal";

// Establish a connection to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // File upload configurations
    $targetDir = "uploads/";
    $fileName = basename($_FILES["profile_picture"]["name"]);
    $targetFilePath = $targetDir . $fileName;
    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

    $username = htmlspecialchars($_POST['username']);

    // Allow only certain file formats
    $allowTypes = array("jpg", "jpeg", "png", "gif");
    if (in_array($fileType, $allowTypes)) {
        // Upload file to server
        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $targetFilePath)) {
            // Update profile picture in the database
            $stmt = $conn->prepare("UPDATE profiles SET profile_picture = ? WHERE username = ?");
            $stmt->bind_param("ss", $fileName, $username);

            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    echo "<p>Profile updated successfully!</p>";
                } else {
                    echo "<p>No matching username found.</p>";
                }
            } else {
                echo "<p>Error updating profile: " . $stmt->error . "</p>";
            }

            $stmt->close();
        } else {
            echo "<p>Sorry, there was an error uploading your file.</p>";
        }
    } else {
        echo "<p>Sorry, only JPG, JPEG, PNG, & GIF files are allowed.</p>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile</title>
    <style>
        form {
            margin: 0 auto;
            width: 50%;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        input {
            width: 100%;
            padding: 5px;
        }

        button {
            padding: 5px 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <h1>Update Profile</h1>
    <form action="" method="POST" enctype="multipart/form-data">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br><br>

        <label for="profile_picture">New Profile Picture:</label>
        <input type="file" id="profile_picture" name="profile_picture" required><br><br>

        <button type="submit">Update Profile</button>
    </form>
</body>
</html>
