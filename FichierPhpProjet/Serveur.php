<?php
 include('ServeurFonction.php');
 include('jwt_utils.php');
 $linkpdo = connectToDatabase();
 header("Content-Type:application/json");
 $http_method = $_SERVER['REQUEST_METHOD'];
 $Id_utilisateur = getTokenIdUtilisateur();
 $RoleUtilisateur = getTokenUtilisateurRole();

 // faire fonction  pour récuperer role et id utilisateur


 switch ($http_method){
    case "GET" :
        if(!empty($RoleUtilisateur)){
                if (!empty($_GET['Id_article'])){
                    if($RoleUtilisateur == "moderateur"){   
                        deliver_response(200, "Votre message", getDataModerateur($_GET['Id_article'], $linkpdo));
                    }
                    if($RoleUtilisateur == "publisher"){
                        deliver_response(200, "Votre message", getDataPublisher($_GET['Id_article'], $linkpdo));
                    }
                }
        }else{
            deliver_response(200, "Votre message", getDataAnonyme($_GET['Id_article'], $linkpdo));
        }
        break;

    case "POST" :
        break;
    case "PUT" :
        break;

    case "DELETE" :
        break;
}
function deliver_response($status, $status_message, $data){
   header("HTTP/1.1 $status $status_message");
   $response['status'] = $status;
   $response['status_message'] = $status_message;
   $response['data'] = $data;
   $json_response = json_encode($response);
   echo $json_response;
   }

?>