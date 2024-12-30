<?php
ob_start(); 
include 'db.php';

function writetoDB($username, $password, $role, $fn, $conn) {
    
    if($role == 1){
        $sql_2 = "INSERT INTO users (username, password, role, fn) VALUES (?, ?, ?, ?)";



        $stmt2 = $conn->prepare($sql_2);
        $stmt2->bind_param("ssis", $username, $password, $role, $fn); 

            if ($stmt2->execute()) {
                return true;
            } else {
                return false;
            }

        $stmt2->close();

    }else if($role == 2){

         $sql_2 = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";


        $stmt2 = $conn->prepare($sql_2);
        $stmt2->bind_param("ssi", $username, $password, $role); 

            if ($stmt2->execute()) {
                return true;
            } else {
                return false;
            }

        $stmt2->close();

    }else{
        return false;
    }
}

$conn = new mysqli($servername, $usernameDB, $passwordDB, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

header('Content-Type: application/json');


$input = json_decode(file_get_contents('php://input'), true);

$username = $input['username'];
$password = $input['password'];
$role = $input['role'];
$fn = isset($input['fn']) ? $input['fn'] : "";

$sql = "SELECT * FROM users WHERE username = ? ";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
 
    $response = array(
        "success" => false,
        "message" => "user exists",
    );
    ob_end_clean();
    echo json_encode($response);

} else {
   
    if(writetoDB($username, $password, $role, $fn, $conn)){
        
        $response = array(
        'success' => true,
        'message' => 'Operation completed successfully'
        );
        ob_end_clean();
        echo json_encode($response);

    } else{
         $response = array(
        'success' => false,
        'message' => 'Operation did NOT complete successfully'
        );
        ob_end_clean();
       echo json_encode($response);
    }    
}

$stmt->close();
$conn->close();
?>