<?php
ob_start(); 
include 'db.php';

$conn = new mysqli($servername, $usernameDB, $passwordDB, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function get_user_id($conn, $username) {
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $res = $stmt->get_result();
    $data = $res->fetch_assoc();
    
    return $data['user_id'];
}

function get_fn($conn, $user_id) {
    $stmt = $conn->prepare("SELECT fn FROM users WHERE user_id = ?");
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $data = $res->fetch_assoc();
    
    return $data['fn'];
}

function get_role($conn, $username) {
    $stmt = $conn->prepare("SELECT role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $res = $stmt->get_result();
    $data = $res->fetch_assoc();
    
    return $data['role'];
}


$input = json_decode(file_get_contents('php://input'), true);
$username = $input['username'];

$role = get_role($conn, $username);   

    if($role == 1){
        
        $user_id = get_user_id($conn, $username);
        $fn = get_fn($conn, $user_id);

        
        $sql = "SELECT result_id, test_results.date_submitted, test_results.examinee_id, test_results.answer AS t_answer, questions.creator_fn, questions.question, questions.opt1, questions.opt2, questions.opt3, questions.opt4, questions.answer AS q_ans, questions.difficulty, questions.note
                FROM test_results
                LEFT JOIN questions ON test_results.q_id = questions.question_id
                WHERE (questions.creator_fn = ? AND test_results.examinee_id != ?) OR test_results.examinee_id = ? ";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sss', $fn, $user_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();


        if ($result->num_rows > 0) {
            $test_results = [];

            $iter = 0;

            while ($row = $result->fetch_assoc()) {

                if($row['creator_fn'] != get_fn($conn, $row['examinee_id'])){

                     $test_results[$iter] = [
                        't_id' => $row['result_id'],
                        't_answer' => $row['t_answer'],
                        'date' => $row['date_submitted'],
                        'creator_fn' => $row['creator_fn'],
                        'question' => $row['question'],
                        'opt1' => $row['opt1'],
                        'opt2' => $row['opt2'],
                        'opt3' => $row['opt3'],
                        'opt4' => $row['opt4'],
                        'answer' => $row['q_ans'],
                        'difficulty' => $row['difficulty'],
                        'note' => $row['note'],
                        'examinee_fn' => get_fn($conn, $row['examinee_id']),
                        'empty' => false
                     ];

                     $iter++;
                }
                           
            }



            if($iter != 0)  {
                ob_end_clean();
                echo json_encode($test_results, JSON_UNESCAPED_UNICODE);
            } else{
                    ob_end_clean();
                 echo json_encode(['empty' => true]);
            }

        } else {
            ob_end_clean();
            echo json_encode(['empty' => true]);
        }


    }   else if ($role == 2) {

        $user_id = get_user_id($conn, $username);

        
        $sql = "SELECT result_id, test_results.date_submitted, test_results.examinee_id, test_results.answer AS t_answer, questions.creator_fn, questions.question, questions.opt1, questions.opt2, questions.opt3, questions.opt4, questions.answer AS q_ans, questions.difficulty, questions.note
                FROM test_results
                LEFT JOIN questions ON test_results.q_id = questions.question_id
                WHERE questions.importer_id = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $user_id);
        $stmt->execute();
        $result = $stmt->get_result();


        if ($result->num_rows > 0) {
            $test_results = [];

            $iter = 0;

            while ($row = $result->fetch_assoc()) {
               $test_results[$iter] = [
                    't_id' => $row['result_id'],
                    't_answer' => $row['t_answer'],
                    'date' => $row['date_submitted'],
                    'creator_fn' => $row['creator_fn'],
                    'question' => $row['question'],
                    'opt1' => $row['opt1'],
                    'opt2' => $row['opt2'],
                    'opt3' => $row['opt3'],
                    'opt4' => $row['opt4'],
                    'answer' => $row['q_ans'],
                    'difficulty' => $row['difficulty'],
                    'note' => $row['note'],
                    'examinee_fn' => get_fn($conn, $row['examinee_id']),
                    'empty' => false
                ];
                $iter++;
            }
            ob_end_clean();
            echo json_encode($test_results, JSON_UNESCAPED_UNICODE);
        } else {
            ob_end_clean();
            echo json_encode(['empty' => true]);
        }

    }
$conn->close();
?>