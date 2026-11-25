<?php
/**
 * Point d'entrée unique (front controller).
 * URL : index.php?page=<page>&action=<action>
 */

require_once __DIR__ . '/config.php';

$page   = (string) ($_GET['page'] ?? 'accueil');
$action = (string) ($_GET['action'] ?? 'liste');

// page => [classe du contrôleur, actions autorisées]
$routes = [
    'accueil'      => ['AccueilControleur',   ['liste']],
    'connexion'    => ['AuthControleur',      ['connexion']],
    'inscription'  => ['AuthControleur',      ['inscription']],
    'deconnexion'  => ['AuthControleur',      ['deconnexion']],
    'produits'     => ['ProduitControleur',   ['liste', 'creer', 'modifier', 'supprimer']],
    'categories'   => ['CategorieControleur', ['liste', 'creer', 'modifier', 'supprimer']],
    'admin'        => ['AdminControleur',     ['dashboard']],
    'utilisateurs' => ['AdminControleur',     ['utilisateurs', 'role']],
];

if (!isset($routes[$page])) {
    http_response_code(404);
    afficher('erreur', ['message' => "La page demandée n'existe pas."], 'Page introuvable');
    exit;
}

[$classe, $actions] = $routes[$page];

// Pour les pages « simples », l'action est le nom de la page.
if (in_array($page, ['connexion', 'inscription', 'deconnexion'], true)) {
    $action = $page;
} elseif ($page === 'admin') {
    $action = 'dashboard';
} elseif ($page === 'utilisateurs' && $action === 'liste') {
    $action = 'utilisateurs';
}

if (!in_array($action, $actions, true)) {
    $action = $actions[0];
}

require_once __DIR__ . '/controleurs/' . $classe . '.php';
(new $classe($bdd))->$action();
