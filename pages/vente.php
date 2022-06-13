<?php 
    include 'bd.php';
    session_start();
    if(!isset($_SESSION['cle_id'])){
        echo "connexion impossible";
    } else {
        $id = $_SESSION['cle_id'];
    }
    
    $titre = mysqli_query($conn,"SELECT * FROM TypeItem ORDER BY id ASC ");
    $pays = mysqli_query($conn,"SELECT DISTINCT country FROM Business ORDER BY country ASC ");
?>
<!DOCTYPE HTML>
<html lang="fr">
<head>
    <meta charset='utf-8'>
    <title>IT+ - Vente</title>
    <link rel='stylesheet' type='text/css' media='screen' href='../assets/css/header.css'>
    <link rel='stylesheet' type='text/css' media='screen' href='../assets/css/main.css'>
    <link rel='stylesheet' type='text/css' media='screen' href='../assets/css/pages/vente.css'>
</head>
<body>
    <section class="site">
        <header>
            <div class="bouton-burger">
               <div class="barre"></div>
               <div class="barre"></div>
               <div class="barre"></div>
            </div>
            <div class="nav">
                <ul class="header_barre_nav">
                    <li class="items"><a href="../index.php" class="accueil">Accueil</a></li>
                    <li class="items"><a href="./achat.php" class="achat">Achat</a></li>
                    <div class="page-actuelle"><li class="items">Vente</li></div>
                    <li class="items"><a href="./profil.php" class="profil">Mon profil</a></li>
                    <li class="items"><a href="./connexion.php" class="connexion">Connexion</a></li>
                </ul>
            </div>
        </header>
    </section>
    <div class="rectangle">
        <h3>Quel est votre produit ?</h3>
        <form class="formulaire" action="" method="POST">
            <div class="custom-select" style="width:300px;">
                <select name="produit">
                    <option value="0">Type de produit :</option>
                    <?php
                        if($titre){
                                    while(($titreprod = mysqli_fetch_array($titre))!=null)
                                    {
                                        echo"<option value='{$titreprod['id']}'>{$titreprod['name']}</option>";
                                    }
                            }
                    ?>
                </select>
            </div><br><br>
            <!-- <input type="number" placeholder="Quantité" id="quantite" name="quantite" value=""><br><br>
            <input type="number" placeholder="Votre prix" id="prix" name="prix" value=""><br><br><br> -->
            <button type="submit" id="bouton" name="boutonVendre">TROUVER UN VENDEUR</button>
                <?php

                    if(!empty($_POST)){

                    extract($_POST);
                    $valid = true;

                    if (isset($_POST['boutonVendre'])) {
                       
                        $quantite = $_POST['quantite'];
                        $titreprod = $_POST['produit'];
                        $prixUnit = $_POST['prix'];
                        $prixTot = $prixUnit * $quantite ;

                    if(empty($quantite)){

                    $valid = false;

                    $er_nom = ("La quantite ne peut pas être vide");
                    }

                    if(empty($titreprod)){

                    $valid = false;

                    $er_nom = ("Le type de produit ne peut pas être vide");
                    }
                    if(empty($prixUnit)){

                    $valid = false;


                    $er_nom = ("Le prix de l'offre ne peut pas être vide");
                    }
                        
                            if ($valid) {
                                
                        
                            // on mets à jour la cagnotte de l'utilisateur    
                            $stmt = mysqli_prepare($conn,"UPDATE Customer SET stash = stash + ? WHERE id=? ");
                            mysqli_stmt_bind_param($stmt,'ii',$prixTot,$id);
                            mysqli_stmt_execute($stmt);


                            
                            // on cherche l'id du produit de la table TypeItem
                            $stmt = mysqli_prepare($conn,"SELECT id FROM TypeItem WHERE id=?");
                            mysqli_stmt_bind_param($stmt,'i',$titreprod);
                            mysqli_stmt_execute($stmt);
                            // on recupère l'id du produit de la table TypeItem 
                            $table = mysqli_stmt_get_result($stmt);
                            $tuple = mysqli_fetch_assoc($table);
                            $titreprodId = $tuple['id'];

                            $request = mysqli_query($conn,"SELECT element,quantity FROM ExtractionFromTypeItem WHERE TypeItem=$titreprodId");

                            

                            if ($request) {
                                $stmt = mysqli_prepare($conn,"UPDATE CustomerExtraction SET quantity = quantity + ? WHERE Customer=? AND element=? ");
                                
                                foreach ($request as $requestas) {
                                    mysqli_stmt_bind_param($stmt,'iii',$requestas['quantity'],$id,$requestas['element']);
                                    mysqli_stmt_execute($stmt);
                        }
                    }


            
                    echo"votre profil à été mis à jour";

                        }else{
                            $valid = false;
                        }
                    }
                }
                ?>
        </form>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
    <script src="../assets/javascript/transitionBurger.js"></script>
    <script src="../assets/javascript/menuSelectionVente.js"></script>

</body>
</html>