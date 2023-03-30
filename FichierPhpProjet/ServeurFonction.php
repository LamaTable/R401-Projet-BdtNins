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
        if (!empty($bearer_token = get_bearer_token())){
            $bearer_token = get_bearer_token();
            if(is_jwt_valid($bearer_token)){
                $tokenParts = explode('.', $bearer_token);
                $header = base64_decode($tokenParts[1]);
                $objet = json_decode($header);
                $RoleUtilisateur = $objet->Role[0]->Role_Utilisateur;
                return  $RoleUtilisateur;
            }else{
                die("Le Token n'est pas valide");
            }
           
        }

}

function getTokenIdUtilisateur(){
        if (!empty($bearer_token = get_bearer_token())){
            $bearer_token = get_bearer_token();
            if (!empty($bearer_token = get_bearer_token())){
                $tokenParts = explode('.', $bearer_token);
                $header = base64_decode($tokenParts[1]);
                $objet = json_decode($header);
                $id_utilisateur = $objet->Username[0]->Id_Utilisateur;
                return  $id_utilisateur;
            }
            else{
                die("Le Token n'est pas valide");
            }  
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
            die("l'article que vous essayer de récuperer n'existe pas");
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
            die("l'article que vous essayer de récuperer n'existe pas");
        }
    } catch (PDOException $e) {
        // En cas d'erreur, on affiche un message et on arrête le script
        die("Erreur lors de la récupération des données : " . $e->getMessage());
    }
}

function getDataAnonyme($Id_Article, $pdo){
    try {
        if (!is_null($Id_Article)){
            $statement = $pdo->prepare("SELECT Auteur, Date_Publication, Contenu FROM articles WHERE Id_Article = :Id_Article");
            $statement->bindParam(":Id_Article", $Id_Article, PDO::PARAM_INT);
            $statement->execute();
            $data = $statement->fetchAll(PDO::FETCH_ASSOC);
            return $data;
        } else {
            die("l'article que vous essayer de récuperer n'existe pas");
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
    $stmt->bindParam(":Id_Article", $Id_Article, PDO::PARAM_INT);
    $stmt->execute();
    $nbrlike = $stmt->fetchColumn();
    return $nbrlike;
}
//retourne le nbr de dislikes
function getNumberDislike($pdo, $Id_Article){
    $query = "SELECT COUNT(*) FROM likes WHERE Id_Article = :Id_Article AND Like_or_Dislike = 0";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":Id_Article", $Id_Article, PDO::PARAM_INT);
    $stmt->execute();
    $nbrdislike = $stmt->fetchColumn();
    return $nbrdislike;
}
//retourne la liste des utilisateurs qui ont liker l'article
function getListUserLike($pdo, $Id_Article){
    $sql = "SELECT Id_Utilisateur FROM likes WHERE Id_Article = :Id_Article AND Like_or_Dislike = 1";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":Id_Article", $Id_Article, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $userslike = array();
    foreach($result as $row) {
        $sql = "SELECT Username FROM utilisateurs WHERE Id_Utilisateur = :Id_Utilisateur";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":Id_Utilisateur", $row['Id_Utilisateur'], PDO::PARAM_INT);
        $stmt->execute();
        $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $userslike[] = $resultat;
    }
    return  $userslike;
}
//retourn la liste des utilisateurs qui ont disliker l'article
function getListUserDislike($pdo, $Id_Article){
    $sql = "SELECT Id_Utilisateur FROM likes WHERE Id_Article = :Id_Article AND like_or_dislike = 0";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":Id_Article", $Id_Article, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $usersdislike = array();
    foreach($result as $row) {
        $sql = "SELECT Username FROM utilisateurs WHERE Id_Utilisateur = :Id_Utilisateur";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":Id_Utilisateur", $row['Id_Utilisateur'], PDO::PARAM_INT);
        $stmt->execute();
        $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $usersdislike[] = $resultat;
    }
    return $usersdislike;
}

//retourne tous les élement associer présent dans la table article associer à Id_Article fournis
function getAllDataArticle($pdo, $Id_Article){
    $statement = $pdo->prepare("SELECT * FROM articles WHERE Id_Article = :Id_Article");
    $statement->bindParam(":Id_Article", $Id_Article, PDO::PARAM_INT);
    $statement->execute();
    $dataArticle = $statement->fetchAll(PDO::FETCH_ASSOC);
    return  $dataArticle;
}


//-----------------------------------POST---------------------------------
// Ajout d'une donnée
function addData($pdo, $Auteur, $Contenu){
    try {
        if (!empty($Contenu)&&!empty($Auteur)){
            $statement = $pdo->prepare("INSERT INTO articles (Date_Publication, Auteur, Contenu) VALUES (NOW(), :Auteur, :Contenu)");
            $statement->bindParam(':Auteur', $Auteur, PDO::PARAM_STR);
            $statement->bindParam(':Contenu', $Contenu, PDO::PARAM_STR);
            $statement->execute();
            return true;
        } else {
            return false;
        }
    } catch (PDOException $e) {
        // En cas d'erreur, on affiche un message et on arrête le script
        die("Erreur lors de l'ajout de données : " . $e->getMessage());
    }
}

// Mise à jour d'une donnée
function updateData($pdo, $Id_Article, $Contenu){
    try {
        if (!empty($Id_Article) && !empty($Contenu)){
            $statement = $pdo->prepare("UPDATE articles SET Contenu = :Contenu WHERE Id_Article = :Id_Article");
            $statement->bindParam(':Contenu', $Contenu, PDO::PARAM_STR);
            $statement->bindParam(':Id_Article', $Id_Article,PDO::PARAM_INT);
            $statement->execute();
            return true;
        } else {
            return false;
        }
    } catch (PDOException $e) {
        // En cas d'erreur, on affiche un message et on arrête le script
        die("Erreur lors de la mise à jour de données : " . $e->getMessage());
    }
}

// Suppression d'une donnée
function deleteData($pdo, $Id_Article){
    try {
        if(!empty($Id_Article)){
            $statement = $pdo->prepare("DELETE FROM articles WHERE Id_Article = :Id_Article");
            $statement->bindParam(':Id_Article', $Id_Article,PDO::PARAM_INT);
            $statement->execute();
            return true;
        } else {
            return false;
        }
    } catch (PDOException $e) {
        // En cas d'erreur, on affiche un message et on arrête le script
        die("Erreur lors de la suppression de données : " . $e->getMessage());
    }
}

function getAuteurId($pdo, $Auteur){
    $statement = $pdo->prepare("SELECT Id_Utilisateur FROM utilisateurs WHERE Username = :Username");
    $statement->bindParam(':Username', $Auteur,PDO::PARAM_STR);
    $statement->execute();
    $dataArticle = $statement->fetchAll(PDO::FETCH_ASSOC);
    return  $dataArticle;
}

function getAuteurName($pdo, $Id_Article){
    $statement = $pdo->prepare("SELECT Auteur FROM articles WHERE Id_Article = :Id_Article");
    $statement->bindParam(':Id_Article', $Id_Article,PDO::PARAM_INT);
    $statement->execute();
    $dataArticle = $statement->fetchAll(PDO::FETCH_ASSOC);
    return  $dataArticle;
}




// Ajout d'un Like/dislike pour un article et un utilisateur donné
function addLike($pdo, $Id_Article, $Id_Utilisateur, $Like_or_Dislike) {
    try {
        $statement = $pdo->prepare("INSERT INTO likes (Id_Article, Id_Utilisateur, Like_or_Dislike) VALUES (:Id_Article, :Id_Utilisateur, :Like_or_Dislike)");
        $statement->bindParam(':Id_Article', $Id_Article,PDO::PARAM_INT);
        $statement->bindParam(':Id_Utilisateur', $Id_Utilisateur,PDO::PARAM_INT);
        $statement->bindParam(':Like_or_Dislike', $Like_or_Dislike ,PDO::PARAM_BOOL);
        $statement->execute();
        return true;
    } catch (PDOException $e) {
        // En cas d'erreur, on affiche un message et on arrête le script
        die("Erreur lors de l'ajout d'un like : " . $e->getMessage());
    }
}

//---------------------------PATCH----------------------------------
// Mise à jour d'un like pour un article et un utilisateur donné
function updateLike($pdo, $Id_Article, $Id_Utilisateur, $Like_or_Dislike) {
    try {
        $statement = $pdo->prepare("UPDATE likes SET Like_or_Dislike = :Like_or_Dislike WHERE Id_Article = :Id_Article AND Id_Utilisateur = :Id_Utilisateur");
        $statement->bindParam(':Id_Article', $Id_Article,PDO::PARAM_INT);
        $statement->bindParam(':Id_Utilisateur', $Id_Utilisateur,PDO::PARAM_INT);
        $statement->bindParam(':Like_or_Dislike', $Like_or_Dislike,PDO::PARAM_BOOL);
        $statement->execute();
        return true;
    } catch (PDOException $e) {
        // En cas d'erreur, on affiche un message et on arrête le script
        die("Erreur lors de la mise à jour d'un like : " . $e->getMessage());
    }
}
//------------------------DELETE--------------------------------------

//delete les like ou dislike
function deleteLikeUser($pdo, $Id_Article, $Id_Utilisateur) {
    try {
        $statement = $pdo->prepare("DELETE FROM likes WHERE Id_Article = :Id_Article AND Id_Utilisateur = :Id_Utilisateur");
        $statement->bindParam(':Id_Article', $Id_Article, PDO::PARAM_INT);
        $statement->bindParam(':Id_Utilisateur', $Id_Utilisateur, PDO::PARAM_INT);
        $statement->execute();
        return true;
    } catch (PDOException $e) {
        die("Erreur lors de la suppression du like : " . $e->getMessage());
    }
}

function deleteAllLike($pdo, $Id_Article) {
    try {
        $statement = $pdo->prepare("DELETE FROM likes WHERE Id_Article = :Id_Article");
        $statement->bindParam(':Id_Article', $Id_Article, PDO::PARAM_INT);
        $statement->execute();
        return true;
    } catch (PDOException $e) {
        die("Erreur lors de la suppression du like : " . $e->getMessage());
    }
}


?>