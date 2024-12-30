<?php
ob_start(); 
include 'db.php';

$conn = new mysqli($servername, $usernameDB, $passwordDB, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$data = file_get_contents("php://input");
$reviewData = json_decode($data, true);

if (is_null($reviewData)) {
    die(json_encode(['success' => false, 'message' => 'Invalid JSON data']));
}

function get_creator_fn_and_q_id($conn, $q) {
    $stmt2 = $conn->prepare("SELECT DISTINCT creator_fn, question_id FROM questions WHERE question = ?");
    $stmt2->bind_param("s", $q);
    $stmt2->execute();
    $res = $stmt2->get_result();
    $data = $res->fetch_assoc();
    
    return [
        'fn' => $data['creator_fn'],
        'q_id' => $data['question_id']
    ];
}

function get_user_id($conn, $username) {
    $stmt3 = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
    $stmt3->bind_param("s", $username);
    $stmt3->execute();
    $res = $stmt3->get_result();
    $data = $res->fetch_assoc();
    
    return $data['user_id'];
}

$stmt = $conn->prepare("INSERT INTO reviews (date_submitted, creator_fn, q_id, user_id, review, rating) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssiisi", $date, $creator_fn, $q_id, $reviewer_id, $review_note, $rating);

$conn->begin_transaction();

foreach ($reviewData as $review) {
    $question = $review['question'];
    $rating = $review['rating'];
    $review_note = $review['review_note'];
    $reviewer_id = get_user_id($conn, $review['reviewer']);
    $date = $review['date'];
    $add_info = get_creator_fn_and_q_id($conn, $question);
    
    $creator_fn = $add_info['fn'];
    $q_id = $add_info['q_id'];

    $stmt->execute();
}

$conn->commit();

$stmt->close();
$conn->close();

ob_end_clean();
$response = array("success" => true);
echo json_encode($response);

?>
