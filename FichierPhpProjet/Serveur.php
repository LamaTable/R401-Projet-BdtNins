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
        if(!empty($RoleUtilisateur)){
            $postedData = file_get_contents('php://input');
            $data = json_decode($postedData);
            if($RoleUtilisateur == "publisher"){
                if(!empty($data->auteur) && !empty($data->contenu)){
                    $Auteur = $data->auteur;
                    $Contenu = $data->contenu;
                    //Publier Article
                    if(!empty($Auteur) && !empty($Contenu)){
                        deliver_response(201, "Votre message", addData($linkpdo, $Auteur, $Contenu));
                    }
                }

                // Liker ou Disliker
                if(!empty($data->Id_Article)){
                    $Id_Article = $data->Id_Article;
                    $AuteurName = getAuteurName($linkpdo, $Id_Article);
                    $AuteurId = getAuteurId($linkpdo, $AuteurName[0]['Auteur']);
                    if($Id_utilisateur !=  $AuteurId[0]['Id_Utilisateur']){
                        if(($data->Like_ou_Dislike) == 0 && !empty($data->Id_Article) || ($data->Like_ou_Dislike) == 1 && !empty($data->Id_Article)){                            
                            $like = $data->Like_ou_Dislike;
                            $Id_Article = $data->Id_Article;   
                            if($like == 1 || $like == 0){
                                deliver_response(201, "Votre message", addLike($linkpdo, $Id_Article, $Id_utilisateur, $like));
                            }else{
                                echo "la valeur inserer est incorrecte, veuillez verifier que la valeur est soit 1 (like) ou 0 (dislike)";
                            }
                        }
                    }
                }                
            }
        }
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