<?php
// Start session
session_start();

// Include database connection
require_once '../config/koneksi.php';

echo "<h1>🔍 Debug Information</h1>";

echo "<h2>Session Information:</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h2>User Information:</h2>";
$isLoggedIn = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
$userName = $_SESSION['user_name'] ?? '';

echo "isLoggedIn: " . ($isLoggedIn ? 'true' : 'false') . "<br>";
echo "userName: " . $userName . "<br>";

echo "<h2>Database Connection:</h2>";
if ($db) {
    echo "Database connected successfully<br>";
    
    // Check if tugas table exists
    $result = mysqli_query($db, "SHOW TABLES LIKE 'tugas'");
    if (mysqli_num_rows($result) > 0) {
        echo "✅ Table 'tugas' exists<br>";
        
        // Check table structure
        $result = mysqli_query($db, "DESCRIBE tugas");
        echo "<h3>Table Structure:</h3>";
        echo "<table border='1'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . $row['Field'] . "</td>";
            echo "<td>" . $row['Type'] . "</td>";
            echo "<td>" . $row['Null'] . "</td>";
            echo "<td>" . $row['Key'] . "</td>";
            echo "<td>" . $row['Default'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Check if user has tasks
        if (!empty($userName)) {
            $query = "SELECT * FROM tugas WHERE nama = ?";
            $stmt = mysqli_prepare($db, $query);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "s", $userName);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                
                echo "<h3>Tasks for user '$userName':</h3>";
                echo "Number of tasks: " . mysqli_num_rows($result) . "<br>";
                
                if (mysqli_num_rows($result) > 0) {
                    echo "<table border='1'>";
                    echo "<tr><th>ID</th><th>Nama</th><th>Judul Tugas</th><th>Status</th><th>Created</th></tr>";
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . $row['id'] . "</td>";
                        echo "<td>" . $row['nama'] . "</td>";
                        echo "<td>" . $row['judul_tugas'] . "</td>";
                        echo "<td>" . $row['status'] . "</td>";
                        echo "<td>" . $row['created_at'] . "</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                } else {
                    echo "❌ No tasks found for this user<br>";
                }
                mysqli_stmt_close($stmt);
            } else {
                echo "❌ Error preparing query: " . mysqli_error($db) . "<br>";
            }
        } else {
            echo "❌ No user name found in session<br>";
        }
        
        // Show all tasks in database
        echo "<h3>All tasks in database:</h3>";
        $result = mysqli_query($db, "SELECT * FROM tugas ORDER BY created_at DESC LIMIT 10");
        if (mysqli_num_rows($result) > 0) {
            echo "<table border='1'>";
            echo "<tr><th>ID</th><th>Nama</th><th>Judul Tugas</th><th>Status</th><th>Created</th></tr>";
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . $row['nama'] . "</td>";
                echo "<td>" . $row['judul_tugas'] . "</td>";
                echo "<td>" . $row['status'] . "</td>";
                echo "<td>" . $row['created_at'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "❌ No tasks found in database<br>";
        }
        
    } else {
        echo "❌ Table 'tugas' does not exist<br>";
    }
} else {
    echo "❌ Database connection failed<br>";
}

echo "<h2>Actions:</h2>";
echo "<a href='tugas.php'>Go to Tasks Page</a><br>";
echo "<a href='index.php'>Go to Home Page</a><br>";
echo "<a href='?logout=1'>Logout</a><br>";
?> 