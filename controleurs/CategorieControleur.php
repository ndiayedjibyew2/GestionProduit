<?php

class CategorieControleur
{
    public function __construct(private PDO $bdd)
    {
        exiger_connexion();
    }

    public function liste(): void
    {
        $categories = $this->bdd->query('
            SELECT c.id, c.nom, c.description, COUNT(p.id) AS nb_produits
            FROM categories c
            LEFT JOIN produits p ON c.id = p.categorie_id
            GROUP BY c.id, c.nom, c.description
            ORDER BY c.nom
        ')->fetchAll();

        afficher('categories/liste', compact('categories'), 'Catégories');
    }

    public function creer(): void
    {
        exiger_admin();
        $categorie = ['nom' => '', 'description' => ''];
        $erreurs = [];

        if (est_post()) {
            verifier_csrf();
            [$categorie, $erreurs] = $this->lireFormulaire(0);
            if (!$erreurs) {
                $this->bdd->prepare('INSERT INTO categories (nom, description) VALUES (?, ?)')
                    ->execute([$categorie['nom'], $categorie['description']]);
                flash('success', 'Catégorie « ' . $categorie['nom'] . ' » ajoutée.');
                rediriger('index.php?page=categories');
            }
        }

        afficher('categories/formulaire', compact('categorie', 'erreurs'), 'Nouvelle catégorie');
    }

    public function modifier(): void
    {
        exiger_admin();
        $id = (int) ($_GET['id'] ?? 0);
        $categorie = $this->trouver($id);
        $erreurs = [];

        if (est_post()) {
            verifier_csrf();
            [$saisie, $erreurs] = $this->lireFormulaire($id);
            $categorie = array_merge($categorie, $saisie);
            if (!$erreurs) {
                $this->bdd->prepare('UPDATE categories SET nom = ?, description = ? WHERE id = ?')
                    ->execute([$saisie['nom'], $saisie['description'], $id]);
                flash('success', 'Catégorie « ' . $saisie['nom'] . ' » mise à jour.');
                rediriger('index.php?page=categories');
            }
        }

        afficher('categories/formulaire', compact('categorie', 'erreurs'), 'Modifier la catégorie');
    }

    public function supprimer(): void
    {
        exiger_admin();
        if (!est_post()) {
            rediriger('index.php?page=categories');
        }
        verifier_csrf();
        $categorie = $this->trouver((int) ($_POST['id'] ?? 0));

        $r = $this->bdd->prepare('SELECT COUNT(*) FROM produits WHERE categorie_id = ?');
        $r->execute([$categorie['id']]);
        if ((int) $r->fetchColumn() > 0) {
            flash('danger', 'Impossible de supprimer « ' . $categorie['nom'] . ' » : elle contient encore des produits.');
            rediriger('index.php?page=categories');
        }

        $this->bdd->prepare('DELETE FROM categories WHERE id = ?')->execute([$categorie['id']]);
        flash('success', 'Catégorie « ' . $categorie['nom'] . ' » supprimée.');
        rediriger('index.php?page=categories');
    }

    /** @return array{0: array, 1: string[]} */
    private function lireFormulaire(int $id): array
    {
        $d = [
            'nom'         => nettoyer($_POST['nom'] ?? ''),
            'description' => nettoyer($_POST['description'] ?? ''),
        ];
        $erreurs = [];
        if ($d['nom'] === '' || mb_strlen($d['nom']) > 100) {
            $erreurs[] = 'Le nom est obligatoire (100 caractères max).';
        } else {
            $r = $this->bdd->prepare('SELECT id FROM categories WHERE LOWER(nom) = ? AND id <> ?');
            $r->execute([mb_strtolower($d['nom']), $id]);
            if ($r->fetch()) $erreurs[] = 'Cette catégorie existe déjà.';
        }
        return [$d, $erreurs];
    }

    private function trouver(int $id): array
    {
        $r = $this->bdd->prepare('SELECT * FROM categories WHERE id = ?');
        $r->execute([$id]);
        $categorie = $r->fetch();
        if (!$categorie) {
            flash('danger', 'Catégorie introuvable.');
            rediriger('index.php?page=categories');
        }
        return $categorie;
    }
}
