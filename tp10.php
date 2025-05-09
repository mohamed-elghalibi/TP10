<?php
$host="localhost";
$user="root";
$pass="";
$dbname="produitsdb";
$conn=new mysqli($host,$user,$pass,$dbname);
if($conn->connect_error){
    die("erreur de connexion".$conn->connect_error);
}
if($_SERVER['REQUEST_METHOD']=="POST"){
    if (isset($_POST['ajouter'])) {
    $nom=$_POST['nom'];
    $quantite=$_POST['quantite'];
    $prix=$_POST['prix'];
    $sql="INSERT INTO produits (nom,quantité,prix,date_ajout) VALUES (?,?,?,NOW())";
    $stmt=$conn->prepare($sql);
    $stmt->bind_param("sis",$nom,$quantite,$prix);
    $stmt->execute();
    }


    if (isset($_POST['supprimer'])) {
        
        $nomm = $_POST['nomm']; 
        $sql = "DELETE FROM produits WHERE nom=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $nomm);
        $stmt->execute();
        echo "Produit supprimé avec succès!";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion de Stock</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f8f8;
            margin: 0;
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }

        input[type="text"],
        input[type="number"],
        input[type="submit"] {
            padding: 10px;
            margin: 5px 0;
            width: 100%;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: #28a745;
            color: white;
            border: none;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #218838;
        }

        .btn-modifier {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
        }

        .btn-modifier:hover {
            background-color: #0056b3;
        }

        .btn-supprimer {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
        }

        .btn-supprimer:hover {
            background-color: #bd2130;
        }

        form {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

    <h1>Bienvenue à la gestion de stock</h1>

    <form method="POST">
        Nom du produit : <input type="text" name="nom" required>
        Quantité : <input type="number" name="quantite" required>
        Prix : <input type="text" name="prix" required>
        <input type="submit" value="Ajouter Produit" name="ajouter">
    </form>

    <form method="GET">
        Rechercher un produit : <input type="text" name="search">
        <input type="submit" value="🔍 Rechercher">
    </form>
    <?php
                    if (isset($_GET['search']) && $_GET['search'] != "") {
                        $search = $_GET['search'];
                        $stmt = $conn->prepare("SELECT * FROM produits WHERE nom LIKE ?");
                        $searchTerm = "%" . $search . "%";
                        $stmt->bind_param("s", $searchTerm);
                        $stmt->execute();
                        $result = $stmt->get_result();
                    
                        if ($result->num_rows > 0) {
                            echo "<h3> Résultat de la recherche :</h3><ul>";
                            while ($row = $result->fetch_assoc()) {
                                echo "<li>{$row['nom']} - Quantité: {$row['quantité']} - Prix: {$row['prix']} dh -  Date : {$row['date_ajout']}</li>";
                            }
                            echo "</ul>";
                        } else {
                            echo "<p class='error'> Aucun produit trouvé avec le nom '<strong>$search</strong>'.</p>";
                        }
                    }
                    
    ?>

    <h2>Liste des Produits</h2>
        <table>
            <tr>
                <th>Nom</th>
                <th>Quantité</th>
                <th>Prix</th>
                <th>Date d'ajout</th>
                <th>Actions</th>
            </tr>
            <tr>
            <?php

            $sql = "SELECT * FROM produits";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                 while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['nom'] . "</td>";
                    echo "<td>" . $row['quantité'] . "</td>";
                    echo "<td>" . $row['prix'] . " €</td>";
                    echo "<td>" . date("d-m-Y", strtotime($row['date_ajout'])) . "</td>";
                    echo "<td>
                            <form style='display:inline;' method='POST'>
                            <input type='hidden' name='nomm' value='" . $row['nom'] . "'> 
                            <input class='btn-supprimer' type='submit' name='supprimer' value='Supprimer'>
                            </form>
                            </td>";
                    echo "</tr>";
                    }
                }
            ?>

            </tr>
        </table>

</body>
</html>
