<?php
ob_start();
include 'db.php';
$conn = new mysqli($servername, $usernameDB, $passwordDB, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$data = file_get_contents("php://input");
$testData = json_decode($data, true);

if (is_null($testData)) {
    ob_end_clean();
    die(json_encode(['success' => false, 'message' => 'Invalid JSON data']));
}

function get_q_id($conn, $q) {
    $stmt2 = $conn->prepare("SELECT question_id FROM questions WHERE question = ?");
    $stmt2->bind_param("s", $q);
    $stmt2->execute();
    $res = $stmt2->get_result();
    $data = $res->fetch_assoc();
    
    return $data['question_id'];
}

function get_user_id($conn, $username) {
    $stmt3 = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
    $stmt3->bind_param("s", $username);
    $stmt3->execute();
    $res = $stmt3->get_result();
    $data = $res->fetch_assoc();
    
    return $data['user_id'];
}

$stmt = $conn->prepare("INSERT INTO test_results (date_submitted, q_id, examinee_id, answer) VALUES (?, ?, ?, ?)");
$stmt->bind_param("siii", $date, $q_id, $examinee_id, $answer_results);

$conn->begin_transaction();

foreach ($testData as $test) {
    $question = $test['question'];
    $examinee_id = get_user_id($conn, $test['examinee']);
    $date = $test['date'];
    $q_id = get_q_id($conn, $question);
    $answer_results = $test['ans'];
   
    $stmt->execute();
}

$conn->commit();

$stmt->close();
$conn->close();


ob_end_clean();
$response = array("success" => true);
echo json_encode($response);

?>
