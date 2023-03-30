<?php
 include('ServeurFonction.php');
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
                    $data = getDataModerateur($_GET['Id_article'], $linkpdo);
                    if ($data) {
                        deliver_response(200, "Voici les données de la publication : ", $data);
                        break;
                    } else {
                        deliver_response(404, "La publication demandée est introuvable", null);
                        break;
                    }
                }
                if($RoleUtilisateur == "publisher"){
                    $data = getDataPublisher($_GET['Id_article'], $linkpdo);
                    if ($data) {
                        deliver_response(200, "Voici les données de la publication : ", $data); 
                        break;                       
                    } else {
                        deliver_response(404, "La publication demandée est introuvable", null);
                        break;
                    }
                }
            } else {
                deliver_response(400, "L'ID de la publication est manquant", null);
                break;
            }
        } else {
            $data = getDataAnonyme($_GET['Id_article'], $linkpdo);
            if ($data) {
                deliver_response(200, "Voici les données de la publication : ", $data);
                break;
            } else {
                deliver_response(404, "La publication demandée est introuvable", null);
                break;
            }
        }
        break;

    case "POST" :
        if(empty($RoleUtilisateur)){
                deliver_response(400, "Erreur", "Une personne anonyme ne peut pas poster/liker d'article");
                break;
        }
        $postedData = file_get_contents('php://input');
        $data = json_decode($postedData);
        
        if($RoleUtilisateur == "publisher" && empty($data->Id_Article)){
            if(empty($data->auteur) || empty($data->contenu)){
                deliver_response(400, "Erreur", "Le nom de l'auteur et le contenu ne peuvent pas être vides");
                break;
            }
            $Auteur = $data->auteur;
            $Contenu = $data->contenu;
            //Publier Article
            if(empty($Auteur) || empty($Contenu)){
                deliver_response(400, "Erreur", "Le nom de l'auteur et le contenu ne peuvent pas être vides");
                break;
            }
            $result = addData($linkpdo, $Auteur, $Contenu);
            if($result === false){
                deliver_response(500, "Erreur", "Impossible d'ajouter l'article");
                break;
            }
            deliver_response(201, "Article publié", $result);
            break;
        }

        // Liker ou Disliker
        
        elseif(isset($data->Id_Article) && !isset ($data->contenu) && $RoleUtilisateur == "publisher"){
            
            $Id_Article = $data->Id_Article;
            $AuteurName = getAuteurName($linkpdo, $Id_Article);
            if(empty($AuteurName[0]['Auteur'])){
                deliver_response(404, "Erreur", "Article introuvable");
                break;
            }
            $AuteurId = getAuteurId($linkpdo, $AuteurName[0]['Auteur']);
            if($Id_utilisateur == $AuteurId[0]['Id_Utilisateur']){
                deliver_response(400, "Erreur", "Vous ne pouvez pas aimer votre propre article");
                break;
            }
            if(!isset($data->Like_ou_Dislike) || !in_array($data->Like_ou_Dislike, [0,1])){
                deliver_response(400, "Erreur", "La valeur 'Like_ou_Dislike' est manquante ou incorrecte, elle doit être soit 1 (like) ou 0 (dislike)");
                break;
            }
            $like = $data->Like_ou_Dislike;
            $result = addLike($linkpdo, $Id_Article, $Id_utilisateur, $like);
            if($result === false){
                deliver_response(500, "Erreur", "Impossible de mettre à jour l'article");
                break;
            }
            deliver_response(201, "like/dislike poster ", $result);
            break;
        }
        else{
            if($RoleUtilisateur != "publisher"){
                deliver_response(404, "Erreur", "Seul les personnes ayant le rôle publisher peuvent publier, les modérateur et les anonymes ne peuvent pas publier d'articles",null);
                break;
            }else{
                deliver_response(400, "Erreur", "L'ID de l'article ne peut pas être vide");
                break;
            }
        }
            
            

    case "PUT" :
        if(!empty($RoleUtilisateur)){
            $postedData = file_get_contents('php://input');
            $data = json_decode($postedData);


            if($RoleUtilisateur == "publisher" && !isset($data->Like_ou_Dislike)){
                
                if(isset($data->Contenu) && isset($data->Id_Article)){
                    $Contenu = $data->Contenu;
                    $Id_Article = $data->Id_Article;
                    $AuteurName = getAuteurName($linkpdo, $Id_Article);
                    $AuteurId = getAuteurId($linkpdo, $AuteurName[0]['Auteur']);
                    if($Id_utilisateur == $AuteurId[0]['Id_Utilisateur']){
                        deliver_response(201, "Votre publication à bien été modifié", updateData($linkpdo, $Id_Article, $Contenu));
                        break;
                    }else{
                        deliver_response(404, "Erreur", "Seul l'auteur de l'article peut modifier l'article",null);
                        break;
                    }
                } else {
                    deliver_response(400, "Erreur : Contenu et/ou Id_Article manquant(s).", null);
                    break;
                }
            }
    
            if(isset($data->Like_ou_Dislike) && isset($data->Id_Article)) { 
                if(!empty($data->Id_Article)) {
                    $Id_Article = $data->Id_Article;
                    $Like_ou_Dislike = $data->Like_ou_Dislike;
                    deliver_response(201, "Le Like/Dislike à bien été modifié", updateLike($linkpdo, $Id_Article, $Id_utilisateur, $Like_ou_Dislike));
                    break;
                } else {
                    deliver_response(400, "Erreur : Id_Article manquant.", null);
                    break;
                }
            } else {
                deliver_response(400, "Erreur : Like_ou_Dislike et/ou Id_Article manquant(s).", null);
                break;
            }
    
        } else {
            deliver_response(401, "Erreur : Utilisateur non authentifié.", null);
            break;
        }
        break;

    case "DELETE" :
        if(!empty($RoleUtilisateur)){
            $postedData = file_get_contents('php://input');
            $data = json_decode($postedData);
        
            // suppression par l'auteur
            if($RoleUtilisateur == "publisher"){
                if (isset($data->Id_Article)){
                    $Id_Article = $data->Id_Article;
                    $AuteurName = getAuteurName($linkpdo, $Id_Article);
                    if(empty($AuteurName)){
                        deliver_response(400, "Erreur: Article non trouvé",null);
                        break;
                    }else{
                        $AuteurId = getAuteurId($linkpdo, $AuteurName[0]['Auteur']);
                        if(empty($AuteurId)){
                            deliver_response(400, "Erreur: Auteur non trouvé",null);
                            break;
                        }else{
                            if($Id_utilisateur != $AuteurId[0]['Id_Utilisateur']){
                                
                                if(isset($data->Like_ou_Dislike)){
                                    $resultatLikes = deleteAllLike($linkpdo, $Id_Article);
                                    deliver_response(200, "Like supprimer",null);
                                    break;
                                }
                                deliver_response(404, "Seul l'auteur de l'article peut supprimer l'article",null);
                                break;
                            }else{
                                $resultat = deleteData($linkpdo, $Id_Article);
                                if(!$resultat){
                                    deliver_response(500, "Erreur: Impossible de supprimer l'article",null);
                                    break;
                                }else{
                                    deliver_response(200, "Article supprimé avec succès", $resultat);
                                    break;
                                    $resultatLikes = deleteAllLike($linkpdo, $Id_Article);
                                    if(!$resultatLikes){
                                        deliver_response(500, "Erreur: Impossible de supprimer les likes de l'article",null);
                                        break;
                                    }else{
                                        deliver_response(200, "Likes de l'article supprimés avec succès", $resultatLikes);
                                        break;
                                    }
                                }
                            }
                        }
                    }
                }else{
                    if(isset($data->Like_ou_Dislike) && !is_null($data->Like_ou_Dislike)){
                        $Like_ou_Dislike = $data->Like_ou_Dislike;
                        $resultat = deleteLikeUser($linkpdo, $Like_ou_Dislike, $Id_utilisateur);
                        if(!$resultat){
                            deliver_response(500, "Erreur: Impossible de supprimer le like de l'utilisateur",null);
                            break;
                        }else{
                            deliver_response(200, "Like de l'utilisateur supprimé avec succès", $resultat);
                            break;
                        }
                    }else{
                        deliver_response(400, "Erreur: Données manquantes",null);
                        break;
                    }
                }
            }
            // suppression par un modérateur
            else if($RoleUtilisateur == "moderateur"){
                if (!empty($data->Id_Article)){
                    $Id_Article = $data->Id_Article;
                    $resultat = deleteData($linkpdo, $Id_Article);
                    if(!$resultat){
                        deliver_response(500, "Erreur: Impossible de supprimer l'article",null);
                        break;
                    }else{
                        deliver_response(200, "Article supprimé avec succès", $resultat);
                        $resultatLikes = deleteAllLike($linkpdo, $Id_Article);
                        if(!$resultatLikes){
                            deliver_response(500, "Erreur: Impossible de supprimer les likes de l'article",null);
                            break;
                        }else{
                            deliver_response(200, "Likes de l'article supprimés avec succès", $resultatLikes);
                            break;
                        }
                        break;
                    }
                }else{
                    deliver_response(400, "Erreur: Données manquantes",null);
                    break;
                }
            }
            else{
                deliver_response(403, "Erreur: Vous n'êtes pas autorisé à supprimer cet article",null);
                break;
            }
        }else{
            deliver_response(403, "Erreur: Les personnes non authentifier ne peuvent pas supprimmer d'article",null);
            break;
        }
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