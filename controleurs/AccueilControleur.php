<?php

class AccueilControleur
{
    public function __construct(private PDO $bdd) {}

    public function liste(): void
    {
        $produits = $this->bdd->query("
            SELECT p.*, c.nom AS categorie_nom
            FROM produits p
            LEFT JOIN categories c ON p.categorie_id = c.id
            ORDER BY p.date_creation DESC
            LIMIT 12
        ")->fetchAll();

        $stats = $this->bdd->query("
            SELECT
                (SELECT COUNT(*) FROM produits)   AS produits,
                (SELECT COUNT(*) FROM categories) AS categories
        ")->fetch();

        afficher('accueil', compact('produits', 'stats'));
    }
}
