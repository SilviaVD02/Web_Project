<?php

ob_start(); 

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

$username = $input['username'];
$password = $input['password'];

checkCredentials($username, $password);

function checkCredentials($username, $password) {
   include 'db.php';

    $conn = new mysqli($servername, $usernameDB, $passwordDB, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $sql = "SELECT * FROM users WHERE username = ? AND password = ? ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    $response;

    if ($result->num_rows) {
 
        $role = getRole($username, $conn);        

        $response = array(
            "success" => true,
            "role" => $role
        );
        
    } else {

        $response = array(
        "success" => false,
        "message" => "Нерегистриран потребител!"
        );
    }
    
    $stmt->close();
    $conn->close();

    ob_end_clean();
    echo json_encode($response);
    
}

function getRole($username, $conn){
    
    $sql = "SELECT role FROM users WHERE username = ?";

    $stmt2 = $conn->prepare($sql);
    $stmt2->bind_param("s", $username);
    $stmt2->execute();
    $result = $stmt2->get_result();
    
    $row = $result->fetch_assoc();
            
    $stmt2->close();

    return $row['role'];
}

?>