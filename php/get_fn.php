<?php
ob_start(); 
include 'db.php';

$conn = new mysqli($servername, $usernameDB, $passwordDB, $dbname);

function get_user_fn($conn, $username) {
    $stmt = $conn->prepare("SELECT fn FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $res = $stmt->get_result();
    $data = $res->fetch_assoc();
    
    return $data['fn'];
}

$data = file_get_contents("php://input");
$data_decoded = json_decode($data, true);
$reviewer_username = $data_decoded['username'];
$reviewer_fn = get_user_fn($conn, $reviewer_username);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
    $stmt = $conn->prepare("SELECT DISTINCT creator_fn FROM questions WHERE creator_fn != ?");
    $stmt->bind_param("s", $reviewer_fn);
    $stmt->execute();
    $result_fns = $stmt->get_result();

    if ($result_fns->num_rows > 0) {
        $facultyNumbers = [];
        while ($row = $result_fns->fetch_assoc()) {
            $facultyNumbers[] = $row['creator_fn'];
        }
        ob_end_clean();
        echo json_encode($facultyNumbers, JSON_UNESCAPED_UNICODE);
    } else {
        ob_end_clean();
        echo json_encode([]);
    }


$conn->close();
?>