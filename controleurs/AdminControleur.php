<?php

require_once __DIR__ . '/ProduitControleur.php';

class AdminControleur
{
    public function __construct(private PDO $bdd)
    {
        exiger_admin();
    }

    public function dashboard(): void
    {
        $stats = $this->bdd->query('
            SELECT
                (SELECT COUNT(*) FROM produits)                           AS total_produits,
                (SELECT COUNT(*) FROM utilisateur)                        AS total_utilisateurs,
                (SELECT COUNT(*) FROM categories)                         AS total_categories,
                (SELECT COALESCE(SUM(quantite), 0) FROM produits)         AS total_stock,
                (SELECT COALESCE(SUM(prix * quantite), 0) FROM produits)  AS valeur_stock
        ')->fetch();

        $derniers_produits = $this->bdd->query('
            SELECT p.id, p.nom, p.prix, p.quantite, p.date_creation, c.nom AS categorie_nom
            FROM produits p LEFT JOIN categories c ON p.categorie_id = c.id
            ORDER BY p.date_creation DESC LIMIT 5
        ')->fetchAll();

        $categories_populaires = $this->bdd->query('
            SELECT c.id, c.nom, COUNT(p.id) AS nb_produits
            FROM categories c LEFT JOIN produits p ON c.id = p.categorie_id
            GROUP BY c.id, c.nom
            ORDER BY nb_produits DESC, c.nom
            LIMIT 5
        ')->fetchAll();

        $r = $this->bdd->prepare('SELECT id, nom, quantite FROM produits WHERE quantite <= ? ORDER BY quantite, nom LIMIT 5');
        $r->execute([ProduitControleur::SEUIL_STOCK_BAS]);
        $stock_bas = $r->fetchAll();

        afficher('admin/dashboard', compact('stats', 'derniers_produits', 'categories_populaires', 'stock_bas'), 'Tableau de bord');
    }

    public function utilisateurs(): void
    {
        $utilisateurs = $this->bdd->query('SELECT id, nom, email, role, date_creation FROM utilisateur ORDER BY nom')->fetchAll();
        afficher('utilisateurs/liste', compact('utilisateurs'), 'Utilisateurs');
    }

    /** Promouvoir / rétrograder un utilisateur. */
    public function role(): void
    {
        if (!est_post()) {
            rediriger('index.php?page=utilisateurs');
        }
        verifier_csrf();
        $id = (int) ($_POST['id'] ?? 0);
        $role = ($_POST['role'] ?? '') === 'admin' ? 'admin' : 'utilisateur';

        if ($id === (int) utilisateur_courant()['id']) {
            flash('danger', 'Vous ne pouvez pas modifier votre propre rôle.');
            rediriger('index.php?page=utilisateurs');
        }

        $this->bdd->prepare('UPDATE utilisateur SET role = ? WHERE id = ?')->execute([$role, $id]);
        flash('success', 'Rôle mis à jour.');
        rediriger('index.php?page=utilisateurs');
    }
}
