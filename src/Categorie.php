l<?php
namespace Eshop;

class Categorie {

    public function getIdCategorie() {
        return $this->idCategorie;
    }

    public function getLibelle() {
        return $this->libelle;
    }

    public function getImage() {
        return $this->image;
    }

    public function setLibelle($libelle) {
        $this->libelle = $libelle;
    }

    public function setImage($image) {
        $this->image = $image;
    }

    private $idCategorie;
    private $libelle;
    private $image;

    private static function arrayToCategorie(Array $array) {
        $c = new Categorie();
        $c->idCategorie = $array["idProduit"];
        $c->libelle = $array["libelle"];
        $c->image = $array["image"];
        $c->collectionProduit =
                Produit::fetchAllByCategorie($c);
        return $c;
         
    }

    private static $select = "select * from categorie ";
    private static $selectById = "select * from categorie where idCategorie = :idCategorie";
    private static $insert = "insert into categorie (libelle,image) values (:libelle,:image)";
    private static $update = "update categorie set libelle =:libelle,image=:image where idCategorie=:idCategorie";

    public static function fetchAll() {
        $collectionCategorie = null;
        $pdo = (new DBA())->getPDO();
        $pdoStatement = $pdo->query(Categorie::$select);
        $recordSet = $pdoStatement->fetchAll(PDO::FETCH_ASSOC);
        foreach ($recordSet as $record) {
            $collectionCategorie[] = Produit::arrayToCategorie($record);
        }
        return $collectionCategorie;
    }

    public static function fetch($idCategorie) {
        $pdo = (new DBA())->getPDO;
        $pdoStatement = $pdo->prepare(Categorie::$selectById);
        $pdoStatement->bindParam(":idCategorie", $idCategorie);
        $pdoStatement->execute();
        $record = $pdoStatement->fetch(PDO::FETCH_ASSOC);
        $categorie = Categorie::arrayToCategorie($record);
        return $categorie;
    }

    public function save() {
     if ($this->idCategorie == null){
         $this->insert();
     }
     else {
         $this->update();
     }
     $this->saveProduits;
    }

    public function insert() {
        $pdo = (new DBA())->getPDO();
        $pdoStatement = $pdo->prepare(Categorie::$insert);
        $pdoStatement->bindParam(":libelle", $this->libelle);
        $pdoStatement->bindParam(":image", $this->image);
        $pdoStatement->execute();
        var_dump($pdoStatement->errorInfo());
        $this->idCategorie = $pdo->lastInsertId();
    }

    public function update() {
        $pdo = (new DBA())->getPDO();
        $pdoStatement = $pdo->prepare(Categorie::$update);
        $pdoStatement->bindParam("idCategorie", $this->idCategorie);
        $pdoStatement->bindParam(":libelle", $this->libelle);
        $pdoStatement->bindParam(":image", $this->image);
        $pdoStatement->execute();
    }

    public function delete() {
        $pdo = (new DBA())->getPDO();
        $pdoStatement = $pdo->prepare(Categorie::$delete);
        $pdoStatement->bindParam(":idCategorie", $this->idCategorie);
        $resultat = $pdoStatement->execute();
        $nblignesAffectees = $pdoStatement->rowCount();
        if ($nblignesAffectees == 1) {
            $this->idCategorie = null;
        }
        return $resultat;
    }
 private $collectionProduit;
 
 public function compareTo(Categorie $categorie){
     return $this->idCategorie == $categorie -> idCategorie;
 }
 private function existProduit(Produit $produit){
     $existe = false;
     foreach($this->collectionProduit as $produitCourant){
         if($produit->compareTo($produitCourant)){
             $existe = true;
             break;
          
         }
     }
     return $existe;
 }
 
 private function saveProduits() {
     foreach ($this->collectionProduit as $produit){
         $produit->save();
     }
 }
public function addProduit(Produit $produit){
    if (!$this->existProduit($produit)){
        $this->collectionProduit[] = $produit;
        if(!$produit->getCategorie()->compareTo($this)){
            $produit-> setCategorie($this);
        }
    }
}
public function removeProduit(Produit $produit){
    $new = array();
    foreach($this->collectionProduit as $produitCourant){
        if(!$produitCourant->compareTo($produit)){
            $new[]=$produitCourant;
            break;
        }
    }
    $this->collectionProduit = $new;
    $produit->setCategorie(null);
}
}


/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

