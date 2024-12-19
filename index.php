<?php
session_start(); // Start the session

// Database connection
$servername = "localhost";
$username = "root"; // Replace with your database username
$password = ""; // Replace with your database password
$dbname = "aqua_data";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize error message
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if both username and password fields are filled
    $admin_worker_username = isset($_POST['worker_username']) ? trim($_POST['worker_username']) : '';  // Updated field name
    $admin_worker_password = isset($_POST['worker_password']) ? trim($_POST['worker_password']) : ''; // Updated to worker_password

    if (!empty($admin_worker_username) && !empty($admin_worker_password)) {
        // SQL query to find the user
        $sql = "SELECT worker_username, role FROM lps_admin WHERE worker_username = ? AND worker_password = ?";  // Updated column name
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $admin_worker_username, $admin_worker_password); // Updated parameter
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Fetch the user details
            $user = $result->fetch_assoc();

            // Store session variables
            $_SESSION['worker_username'] = $user['worker_username'];  // Updated session variable
            $_SESSION['user_role'] = $user['role'];

            // Redirect to the dashboard page
            header("Location: Dashboard/dashboard.php");
            exit();
        } else {
            // Login failed
            $error = "Invalid username or password.";
        }

        $stmt->close();
    } else {
        $error = "Please enter both username and password.";
    }
}

$conn->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="image/ScanFish_Logo.png" type="image/png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ScanFish Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        

        body {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            background-image: url('image/background.jpg'); /* Replace with the actual image file name */
            background-size: cover;
            background-position: center;
            font-family: Arial, sans-serif;
            position: relative;
        }

        /* Add the black overlay */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.3); /* Black filter with 50% opacity */
            z-index: -1; /* Ensures the overlay is behind the content */
        }

        .login-container {
            max-width: 500px;
            max-height: 600px;
            width: 100%;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        .login-form {

            padding: 40px;
            padding-top: 0px;
            flex: 1;
            font-weight: bold;
            background-color: white; /* Medium blue background for the form */
        }

        .login-form h2 {
            font-weight: bold;
            margin-bottom: 20px;
            text-align: center;
            color: black; /* White text for heading */
        }

        .login-form .form-control {
            border-radius: 50px;
            padding: 10px 20px;
            font-size: 16px;
            background-color: #ffffff;
            border: 1px solid #328fba; /* Light blue border */
        }

        .login-form .btn-login {
            border-radius: 50px;
            background-color: #21607c; /* Darker blue button */
            color: #ffffff;
            font-weight: bold;
            padding: 10px 20px;
            width: 100%;
            transition: background-color 0.3s;
        }

        .login-form .btn-login:hover {
            background-color: #153c4e; /* Darker blue on hover */
        }

        .login-welcome {
            flex: 1;
            color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px;
            padding-top: 30px;
            padding-bottom: 0px;
            border-top-right-radius: 5px;
            border-bottom-right-radius: 5px;
        }

        .login-welcome h3 {
            font-weight: bold;
            color: black; /* Light text for welcome message */
            text-align: center;
        }

        .login-welcome .logos {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 30px;
        }

        .login-welcome .logos img {
            width: 90px; /* Increased size */
            height: 90px; /* Increased size */
            object-fit: contain;
        }
        /* Position the icon inside the input field */
        .position-relative {
            position: relative;
        }

        .toggle-icon {
            position: absolute;
            top: 50%;
            right: 20px;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
        }

        .toggle-icon:hover {
            color: #333;
        }

    </style>
</head>
<body>

    <div class="login-container">
        <div>
            <div class="login-form">
                <div class="text-center mb-4">
                    <div class="login-welcome">
                        <div class="logos">
                            <img src="image/negeri_perak_logo.png" alt="Logo Negeri Perak" class="logo-negeri-perak">
                            <img src="image/PTD_Kerian_logo.png" alt="Logo Pejabat Tanah Dan Daerah Kerian" class="logo-second">
                            <img src="image/ScanFish_Logo.png" alt="Logo ScanFish" class="logo-second">
                        </div>
                        <h3>Selamat Datang <br> ScanFish Aquaculture QR System</h3>
                    </div>
                </div>
                <?php if (!empty($error)) { echo "<div class='alert alert-danger'>$error</div>"; } ?>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="worker_username">Username</label>
                        <input type="text" name="worker_username" id="worker_username" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="worker_password">Password</label>
                        <div class="position-relative">
                            <input type="password" name="worker_password" id="worker_password" class="form-control pr-4" required>
                            <i class="fas fa-eye toggle-icon" id="togglePasswordIcon" onmousedown="showPassword()" onmouseup="hidePassword()" onmouseout="hidePassword()"></i>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-login">LOGIN</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function showPassword() {
            const passwordInput = document.getElementById("worker_password");
            const toggleIcon = document.getElementById("togglePasswordIcon");
            passwordInput.type = "text";
            toggleIcon.classList.remove("fa-eye");
            toggleIcon.classList.add("fa-eye-slash");
        }
        function hidePassword() {
            const passwordInput = document.getElementById("worker_password");
            const toggleIcon = document.getElementById("togglePasswordIcon");
            passwordInput.type = "password";
            toggleIcon.classList.remove("fa-eye-slash");
            toggleIcon.classList.add("fa-eye");
        }
    </script>
</body>
</html>
