<?php

include('ServeurFonction.php');
include('jwt_utils.php');
$linkpdo = connectToDatabase();
header("Content-Type:application/json");
$http_method = $_SERVER['REQUEST_METHOD'];


switch ($http_method){
    case "POST" :
        $data = (array) json_decode(file_get_contents('php://input'), TRUE);
        $username = $data['Username'];
        $pwd = $data['pwd'];
        $check = checkUser($linkpdo, $username, $pwd);
        if($check){
            $id_utilisateur = getIdUtilisateur($linkpdo, $username, $pwd);
            $Role = getUtilisateurRole($linkpdo, $username, $pwd);
            $header = array('alg'=>'HS256', 'typ'=>'JWT');
            $payload = array('Username'=>$id_utilisateur, 'Role'=>$Role, 'exp'=>(time() + 6000));
            $jwt = generate_jwt($header, $payload);
            echo $jwt;
        }
}
?>