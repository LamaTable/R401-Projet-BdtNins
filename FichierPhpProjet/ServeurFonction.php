<?php
// Connexion à la base de données
include('jwt_utils.php');
function connectToDatabase(){
    try {
        $host = 'localhost';
        $user = 'root';
        $password = '';
        $dbname = 'apirest_project';
        $dsn = "mysql:host=$host;dbname=$dbname;charset=UTF8";
        $pdo = new PDO($dsn, $user, $password);
        return $pdo;
    } catch (PDOException $e) {
        // En cas d'erreur, on affiche un message et on arrête le script
        die("Erreur de connexion à la base de données : " . $e->getMessage());
    }
}

//-----------------------Serveur Authentifiacation------------------------------------
function getIdUtilisateur($pdo, $Username, $pwd){
    
    try {
        if (!empty($Username) && !empty($pwd)){
            $statement = $pdo->prepare("SELECT Id_Utilisateur FROM utilisateurs WHERE Username = :Username AND pwd = :pwd");
            $statement->bindParam(':Username', $Username, PDO::PARAM_STR);
            $statement->bindParam(':pwd', $pwd, PDO::PARAM_STR);
            $statement->execute();
            $data = $statement->fetchAll(PDO::FETCH_ASSOC);
            return $data;
        }
    } catch (PDOException $e) {
        // En cas d'erreur, on affiche un message et on arrête le script
        die("Erreur lors de la récupération des données : " . $e->getMessage());
    }
}

function getUtilisateurRole($pdo, $Username, $pwd){
    try {
        if (!empty($Username) && !empty($pwd)){
            $statement = $pdo->prepare("SELECT Role_Utilisateur FROM utilisateurs WHERE Username = :Username AND pwd = :pwd");
            $statement->bindParam(':Username', $Username, PDO::PARAM_STR);
            $statement->bindParam(':pwd', $pwd, PDO::PARAM_STR);
            $statement->execute();
            $data = $statement->fetchAll(PDO::FETCH_ASSOC);
            return $data;
        }
    } catch (PDOException $e) {
        // En cas d'erreur, on affiche un message et on arrête le script
        die("Erreur lors de la récupération des données : " . $e->getMessage());
    }
}

function checkUser($pdo, $Username, $pwd){
    
    try {
        if (!empty($Username) && !empty($pwd)){
            $statement = $pdo->prepare("SELECT * FROM utilisateurs WHERE Username = :Username AND Pwd = :pwd");
            $statement->bindParam(':Username', $Username, PDO::PARAM_STR);
            $statement->bindParam(':pwd', $pwd, PDO::PARAM_STR);
            $statement->execute();
            $data = $statement->fetchAll(PDO::FETCH_ASSOC);
            if(count($data) == 0){
                throw new Exception("Nom d'utilisateur ou mot de passe incorrect");
            }
            if(count($data) == 1){
                return true;
            }else{
                return false;
            }
        }else{
            throw new Exception("Le nom d'utilisateur et le mot de passe sont requis");
        }
    } catch (PDOException $e) {
        // En cas d'erreur, on affiche un message et on arrête le script
        die("Erreur lors de la récupération des données : " . $e->getMessage());
    } catch (Exception $e) {
        // En cas d'erreur, on lève une exception avec le message approprié
        throw new Exception($e->getMessage());
    }
}


//-------------------------------------ALL----------------------------------------------------
function getTokenUtilisateurRole(){
    try {

        if (!empty($bearer_token = get_bearer_token())){
            $bearer_token = get_bearer_token();
            if(is_jwt_valid($bearer_token)){
                $tokenParts = explode('.', $bearer_token);
                $header = base64_decode($tokenParts[1]);
                $objet = json_decode($header);
                $RoleUtilisateur = $objet->Role[0]->Role_Utilisateur;
                return  $RoleUtilisateur;
            }
           
        }
    } catch (PDOException $e) {
        // En cas d'erreur, on affiche un message et on arrête le script
        die("Erreur lors de la récupération des données : " . $e->getMessage());
    }
}

function getTokenIdUtilisateur(){
    try {
        if (!empty($bearer_token = get_bearer_token())){
            $bearer_token = get_bearer_token();
            if (!empty($bearer_token = get_bearer_token())){
                $tokenParts = explode('.', $bearer_token);
                $header = base64_decode($tokenParts[1]);
                $objet = json_decode($header);
                $id_utilisateur = $objet->Username[0]->Id_Utilisateur;
                return  $id_utilisateur;
            }
        }
    } catch (PDOException $e) {
        // En cas d'erreur, on affiche un message et on arrête le script
        die("Erreur lors de la récupération des données : " . $e->getMessage());
    }
}

//-------------------------------------GET----------------------------------------------------

// Récupération des données
function getDataModerateur($Id_Article=null, $pdo){
    try {
        if (!is_null($Id_Article)){
            //retourne table article
            $dataArticle = getAllDataArticle($pdo, $Id_Article);
            
            //retourne count(like)
            $nbrlike = getNumberLike($pdo, $Id_Article);

            //retourne nbr de dislike
            $nbrdislike = getNumberDislike($pdo, $Id_Article);
 

            // Liste des utilisateur qui ont liker l'article
            $userslike = getListUserLike($pdo, $Id_Article);

            // Liste des utilisateur qui ont Disliker l'article
            $usersdislike = getListUserDislike($pdo, $Id_Article);

            //retourne tous les résultat dans un tableau
            return array('dataArticle'=>$dataArticle, 'nbrLike'=>$nbrlike, 'nbrDislike'=>$nbrdislike, 'Liste_des_Utilisateur_Like'=>$userslike, 'Liste_Des_Utilisateur_Dislike'=>$usersdislike) ;

        } else {
            echo("l'article que vous essayer de récuperer n'existe pas");
        }
    } catch (PDOException $e) {
        // En cas d'erreur, on affiche un message et on arrête le script
        die("Erreur lors de la récupération des données : " . $e->getMessage());
    }
}

function getDataPublisher($Id_Article, $pdo){
    try {
        if (!is_null($Id_Article)){
            //retourne table article
            $dataArticle = getAllDataArticle($pdo, $Id_Article);
            
            //retourne count(like)
            $nbrlike = getNumberLike($pdo, $Id_Article);

            //retourne nbr de dislike
            $nbrdislike = getNumberDislike($pdo, $Id_Article);

            return array('dataArticle'=>$dataArticle, 'nbrLike'=>$nbrlike, 'nbrDislike'=>$nbrdislike) ;

        } else {
            echo("l'article que vous essayer de récuperer n'existe pas");
        }
    } catch (PDOException $e) {
        // En cas d'erreur, on affiche un message et on arrête le script
        die("Erreur lors de la récupération des données : " . $e->getMessage());
    }
}

function getDataAnonyme($id, $pdo){
    try {
        if (!is_null($id)){
            $statement = $pdo->prepare("SELECT Auteur, Date_Publication, Contenu FROM articles WHERE Id_Article = ?");
            $statement->execute([$id]);
            $data = $statement->fetchAll(PDO::FETCH_ASSOC);
            return $data;
        } else {
            echo("l'article que vous essayer de récuperer n'existe pas");
        }
    } catch (PDOException $e) {
        // En cas d'erreur, on affiche un message et on arrête le script
        die("Erreur lors de la récupération des données : " . $e->getMessage());
    }
}

//retourne le nbr de likes
function getNumberLike($pdo, $Id_Article){
    $query = "SELECT COUNT(*) FROM likes WHERE Id_Article = :Id_Article AND Like_or_Dislike = 1";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":Id_Article", $Id_Article);
    $stmt->execute();
    $nbrlike = $stmt->fetchColumn();
    return $nbrlike;
}
//retourne le nbr de dislikes
function getNumberDislike($pdo, $Id_Article){
    $query = "SELECT COUNT(*) FROM likes WHERE Id_Article = :Id_Article AND Like_or_Dislike = 0";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":Id_Article", $Id_Article);
    $stmt->execute();
    $nbrdislike = $stmt->fetchColumn();
    return $nbrdislike;
}
//retourne la liste des utilisateurs qui ont liker l'article
function getListUserLike($pdo, $Id_Article){
    $sql = "SELECT Id_Utilisateur FROM likes WHERE Id_Article = :Id_Article AND Like_or_Dislike = 1";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":Id_Article", $Id_Article);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $userslike = array();
    foreach($result as $row) {
        $userslike[] = $row['Id_Utilisateur'];
    }
    return  $userslike;
}
//retourn la liste des utilisateurs qui ont disliker l'article
function getListUserDislike($pdo, $Id_Article){
    $sql = "SELECT Id_Utilisateur FROM likes WHERE Id_Article = :Id_Article AND like_or_dislike = 0";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":Id_Article", $Id_Article);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $usersdislike = array();
    foreach($result as $row) {
        $usersdislike[] = $row['Id_Utilisateur'];
    }
    return $usersdislike;
}

//retourne tous les élement associer présent dans la table article associer à Id_Article fournis
function getAllDataArticle($pdo, $Id_Article){
    $statement = $pdo->prepare("SELECT * FROM articles WHERE Id_Article = ?");
    $statement->execute([$Id_Article]);
    $dataArticle = $statement->fetchAll(PDO::FETCH_ASSOC);
    return  $dataArticle;
}


