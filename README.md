# GestionProduits

Application PHP (MVC sans framework) de gestion de catalogue : produits, catégories, stock, utilisateurs.

## Structure

```
index.php              routeur (index.php?page=…&action=…)
config.php             BDD (MySQL en local, Postgres en ligne), session, helpers, CSRF
controleurs/           Accueil, Auth, Produit, Categorie, Admin
vues/                  gabarit (entete/pied) + pages ; vues/public/style.css
api/index.php          point d'entrée Vercel
database/              schémas MySQL et Postgres
vercel.json            runtime PHP (vercel-php) + routage
```

## En local (XAMPP)

Rien à configurer : MySQL `localhost`, utilisateur `root`, base `gestion_produits`.
Ouvrir http://localhost/gestions_produits_mvc/

## En ligne (Vercel + Supabase)

La base Postgres est le projet Supabase **gestion-produits** (tables déjà créées, catégories et produits importés).

1. Récupérer l'URL de connexion : Supabase → projet *gestion-produits* → **Connect** → **Transaction pooler**.
   Si besoin, définir le mot de passe de la base : *Project Settings → Database → Reset database password*.
2. Dans ce dossier :

```powershell
npx vercel login
npx vercel link --yes --project gestion-produits
npx vercel env add DATABASE_URL production
npx vercel deploy --prod
```

3. Créer son compte sur le site, puis le passer administrateur dans Supabase → SQL Editor :

```sql
UPDATE utilisateur SET role = 'admin' WHERE email = 'votre@email.com';
```

## Améliorations apportées

- Sécurité : jetons CSRF sur tous les formulaires, suppressions en POST, échappement systématique à l'affichage
  (corrige le double encodage `d&#039;…`), régénération de l'ID de session à la connexion, cookies `HttpOnly`/`SameSite`,
  mots de passe de 8 caractères minimum.
- Code : routeur avec liste blanche, contrôleurs nettoyés, formulaires créer/modifier fusionnés, validation stricte,
  HTML du tableau de bord réparé, nom de fichier `AuthControleur.php` corrigé (sensible à la casse sous Linux).
- Fonctionnalités : recherche et filtre par catégorie, alertes de stock bas/rupture, tableau de bord enrichi,
  promotion/rétrogradation des utilisateurs, page 404.
- Interface : nouveau design responsive (menu mobile), sans dépendance externe (plus de jQuery/DataTables).
- Déploiement : compatible Vercel (sessions stockées en base car les fonctions serverless n'ont pas de disque partagé).
