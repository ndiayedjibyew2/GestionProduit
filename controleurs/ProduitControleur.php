<?php

class ProduitControleur
{
    public const SEUIL_STOCK_BAS = 5;

    public function __construct(private PDO $bdd)
    {
        exiger_connexion();
    }

    public function liste(): void
    {
        $recherche = nettoyer($_GET['q'] ?? '');
        $categorie = (int) ($_GET['categorie'] ?? 0);

        $sql = "SELECT p.*, c.nom AS categorie_nom
                FROM produits p
                LEFT JOIN categories c ON p.categorie_id = c.id
                WHERE 1 = 1";
        $params = [];
        if ($recherche !== '') {
            $sql .= ' AND (LOWER(p.nom) LIKE ? OR LOWER(p.description) LIKE ?)';
            $motif = '%' . mb_strtolower($recherche) . '%';
            array_push($params, $motif, $motif);
        }
        if ($categorie > 0) {
            $sql .= ' AND p.categorie_id = ?';
            $params[] = $categorie;
        }
        $sql .= ' ORDER BY p.date_creation DESC';

        $r = $this->bdd->prepare($sql);
        $r->execute($params);
        $produits = $r->fetchAll();
        $categories = $this->categories();
        $seuil = self::SEUIL_STOCK_BAS;

        afficher('produits/liste', compact('produits', 'categories', 'recherche', 'categorie', 'seuil'), 'Produits');
    }

    public function creer(): void
    {
        exiger_admin();
        $produit = ['nom' => '', 'description' => '', 'prix' => '', 'quantite' => '', 'categorie_id' => ''];
        $erreurs = [];

        if (est_post()) {
            verifier_csrf();
            [$produit, $erreurs] = $this->lireFormulaire();
            if (!$erreurs) {
                $this->bdd->prepare('
                    INSERT INTO produits (nom, description, prix, quantite, categorie_id)
                    VALUES (?, ?, ?, ?, ?)
                ')->execute([$produit['nom'], $produit['description'], $produit['prix'], $produit['quantite'], $produit['categorie_id']]);
                flash('success', 'Produit « ' . $produit['nom'] . ' » ajouté.');
                rediriger('index.php?page=produits');
            }
        }

        $categories = $this->categories();
        afficher('produits/formulaire', compact('produit', 'erreurs', 'categories'), 'Nouveau produit');
    }

    public function modifier(): void
    {
        exiger_admin();
        $id = (int) ($_GET['id'] ?? 0);
        $produit = $this->trouver($id);
        $erreurs = [];

        if (est_post()) {
            verifier_csrf();
            [$saisie, $erreurs] = $this->lireFormulaire();
            $produit = array_merge($produit, $saisie);
            if (!$erreurs) {
                $this->bdd->prepare('
                    UPDATE produits
                    SET nom = ?, description = ?, prix = ?, quantite = ?, categorie_id = ?,
                        date_modification = CURRENT_TIMESTAMP
                    WHERE id = ?
                ')->execute([$saisie['nom'], $saisie['description'], $saisie['prix'], $saisie['quantite'], $saisie['categorie_id'], $id]);
                flash('success', 'Produit « ' . $saisie['nom'] . ' » mis à jour.');
                rediriger('index.php?page=produits');
            }
        }

        $categories = $this->categories();
        afficher('produits/formulaire', compact('produit', 'erreurs', 'categories'), 'Modifier le produit');
    }

    public function supprimer(): void
    {
        exiger_admin();
        if (!est_post()) {
            rediriger('index.php?page=produits');
        }
        verifier_csrf();
        $produit = $this->trouver((int) ($_POST['id'] ?? 0));
        $this->bdd->prepare('DELETE FROM produits WHERE id = ?')->execute([$produit['id']]);
        flash('success', 'Produit « ' . $produit['nom'] . ' » supprimé.');
        rediriger('index.php?page=produits');
    }

    /** @return array{0: array, 1: string[]} */
    private function lireFormulaire(): array
    {
        $prixBrut = str_replace([' ', ','], ['', '.'], (string) ($_POST['prix'] ?? ''));
        $d = [
            'nom'          => nettoyer($_POST['nom'] ?? ''),
            'description'  => nettoyer($_POST['description'] ?? ''),
            'prix'         => $prixBrut,
            'quantite'     => nettoyer($_POST['quantite'] ?? ''),
            'categorie_id' => ($_POST['categorie_id'] ?? '') !== '' ? (int) $_POST['categorie_id'] : null,
        ];

        $erreurs = [];
        if ($d['nom'] === '' || mb_strlen($d['nom']) > 150) $erreurs[] = 'Le nom est obligatoire (150 caractères max).';
        if (!is_numeric($d['prix']) || (float) $d['prix'] <= 0) $erreurs[] = 'Le prix doit être un nombre positif.';
        if (filter_var($d['quantite'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]) === false) {
            $erreurs[] = 'La quantité doit être un entier positif ou nul.';
        }
        if ($d['categorie_id'] !== null) {
            $r = $this->bdd->prepare('SELECT 1 FROM categories WHERE id = ?');
            $r->execute([$d['categorie_id']]);
            if (!$r->fetch()) $erreurs[] = "La catégorie choisie n'existe pas.";
        }

        if (!$erreurs) {
            $d['prix'] = round((float) $d['prix'], 2);
            $d['quantite'] = (int) $d['quantite'];
        }
        return [$d, $erreurs];
    }

    private function trouver(int $id): array
    {
        $r = $this->bdd->prepare('SELECT * FROM produits WHERE id = ?');
        $r->execute([$id]);
        $produit = $r->fetch();
        if (!$produit) {
            flash('danger', 'Produit introuvable.');
            rediriger('index.php?page=produits');
        }
        return $produit;
    }

    private function categories(): array
    {
        return $this->bdd->query('SELECT id, nom FROM categories ORDER BY nom')->fetchAll();
    }
}
