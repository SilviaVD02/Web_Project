<?php
header('Content-Type: application/json');



if ($_FILES["csvFile"]["error"] !== UPLOAD_ERR_OK) {
    $response = ['success' => false, 'message' => 'File upload failed'];
    echo json_encode($response);
    exit();
}

if (!isset($_POST['username'])) {
    $response = ['success' => false, 'message' => 'Username not provided'];
    echo json_encode($response);
    exit();
}

$username = $_POST['username'];

function get_user_id($conn, $username) {
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $res = $stmt->get_result();
    $data = $res->fetch_assoc();
    return $data ? $data['user_id'] : null;
}

$targetDir = "../files/";
$targetFile = $targetDir . basename($_FILES["csvFile"]["name"]);
$response = [];

if (move_uploaded_file($_FILES["csvFile"]["tmp_name"], $targetFile)) {
    $handle = fopen($targetFile, "r");

    if ($handle !== FALSE) {
       ob_start(); 
        include 'db.php';
        $conn = new mysqli($servername, $usernameDB, $passwordDB, $dbname);

       
        $userId = get_user_id($conn, $username);
        if (!$userId) {
            $response = ['success' => false, 'message' => 'User not found'];
            ob_end_clean();
            echo json_encode($response);
            fclose($handle);
            $conn->close();
            exit();
        }

        $stmt = $conn->prepare("INSERT INTO questions (importer_id ,creator_fn, goal, question, opt1, opt2, opt3, opt4, answer, difficulty, react_when_wrong, react_when_right, date_submitted, note) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        if (!$stmt) {
            $response = ['success' => false, 'message' => 'Prepare statement failed: ' . $db->error];
            ob_end_clean();
            echo json_encode($response);
            fclose($handle);
            $conn->close();
            exit();
        }

        $stmt->bind_param("issssssssissss", $userId, $fn, $goal, $question, $opt1, $opt2, $opt3, $opt4, $ans, $diff, $right, $wrong, $date, $note);

        fgetcsv($handle);

        while (($data = fgetcsv($handle, 3000, ",")) !== FALSE) {
            $date = $data[0];
            $fn = $data[1];
            $goal = $data[3];
            $question = $data[5];
            $opt1 = $data[6];
            $opt2 = $data[7];
            $opt3 = $data[8];
            $opt4 = $data[9];
            $ansN = $data[10];
            $ans = $data[5 + $ansN];
            $diff = $data[11];
            $right = $data[12];
            $wrong = $data[13];
            $note = $data[14];

            if (!$stmt->execute()) {
                $response = ['success' => false, 'message' => 'Database insertion failed: ' . $stmt->error];
                ob_end_clean();
                echo json_encode($response);
                fclose($handle);
                $stmt->close();
                $conn->close();
                exit();
            }
        }

        $stmt->close();
        $conn->close();
        ob_end_clean();
        $response = ['success' => true, 'message' => 'File uploaded and data inserted successfully'];
    } else {
        ob_end_clean();
        $response = ['success' => false, 'message' => 'Failed to open uploaded file'];
    }

    fclose($handle);
} else {
    ob_end_clean();
    $response = ['success' => false, 'message' => 'File move failed'];
}

echo json_encode($response);
?>



