<?php
ob_start(); 
include 'db.php';

$conn = new mysqli($servername, $usernameDB, $passwordDB, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$input = json_decode(file_get_contents('php://input'), true);

$studentNumber = $input['studentFN'];

    $sql = "SELECT question, opt1, opt2, opt3, opt4, answer, difficulty, note FROM questions WHERE creator_fn = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $studentNumber);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $questions = [];
        while ($row = $result->fetch_assoc()) {
            $questions[] = [
                'question' => $row['question'],
                'opt1' => $row['opt1'],
                'opt2' => $row['opt2'],
                'opt3' => $row['opt3'],
                'opt4' => $row['opt4'],
                'answer' => $row['answer'],
                'difficulty' => $row['difficulty'],
                'note' => $row['note']
            ];
        }
        ob_end_clean();
        echo json_encode(['questions' => $questions], JSON_UNESCAPED_UNICODE);
    } else {
        ob_end_clean();
        echo json_encode(['questions' => []]);
    }

$conn->close();
?>