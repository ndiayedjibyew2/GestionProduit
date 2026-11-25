<?php
/**
 * Bootstrap de l'application : base de données, session, fonctions utilitaires.
 *
 * - En local (XAMPP) : MySQL avec les valeurs par défaut ci-dessous.
 * - En ligne (Vercel) : variable d'environnement DATABASE_URL (Postgres Supabase).
 */

declare(strict_types=1);

define('RACINE', __DIR__);
define('EN_LIGNE', getenv('VERCEL') !== false);

error_reporting(E_ALL);
ini_set('display_errors', EN_LIGNE ? '0' : '1');
date_default_timezone_set('Africa/Dakar');

/* ------------------------------------------------------------------ */
/* Base de données                                                     */
/* ------------------------------------------------------------------ */

function connexion_bdd(): PDO
{
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];

    $url = getenv('DATABASE_URL');
    if ($url) {
        // Postgres (Supabase via le pooler) : postgresql://user:pass@host:port/base
        $p = parse_url($url);
        if ($p === false || empty($p['host'])) {
            throw new RuntimeException('DATABASE_URL invalide');
        }
        $dsn = sprintf(
            'pgsql:host=%s;port=%d;dbname=%s;sslmode=require',
            $p['host'],
            $p['port'] ?? 5432,
            ltrim($p['path'] ?? '/postgres', '/') ?: 'postgres'
        );
        // Le pooler en mode transaction ne garde pas les requêtes préparées côté serveur.
        $options[PDO::ATTR_EMULATE_PREPARES] = true;
        return new PDO($dsn, urldecode($p['user'] ?? ''), urldecode($p['pass'] ?? ''), $options);
    }

    $hote = getenv('DB_HOST') ?: 'localhost';
    $base = getenv('DB_NAME') ?: 'gestion_produits';
    $user = getenv('DB_USER') ?: 'root';
    $pass = getenv('DB_PASS') ?: '';
    $options[PDO::MYSQL_ATTR_INIT_COMMAND] = 'SET NAMES utf8mb4';
    return new PDO("mysql:host=$hote;dbname=$base;charset=utf8mb4", $user, $pass, $options);
}

try {
    $bdd = connexion_bdd();
} catch (Throwable $e) {
    http_response_code(503);
    error_log('Connexion BDD impossible : ' . $e->getMessage());
    $detail = EN_LIGNE ? '' : '<p style="color:#b91c1c">' . htmlspecialchars($e->getMessage()) . '</p>';
    exit('<!doctype html><meta charset="utf-8"><title>Service indisponible</title>'
        . '<div style="font-family:system-ui;max-width:520px;margin:15vh auto;text-align:center">'
        . '<h1>Service momentanément indisponible</h1><p>La base de données ne répond pas. Réessayez dans un instant.</p>'
        . $detail . '</div>');
}

/* ------------------------------------------------------------------ */
/* Session                                                             */
/* ------------------------------------------------------------------ */

/**
 * Sur Vercel chaque requête peut tourner sur une machine différente :
 * les sessions sont donc stockées en base plutôt que dans des fichiers.
 */
class SessionBdd implements SessionHandlerInterface
{
    public function __construct(private PDO $bdd) {}

    public function open(string $path, string $name): bool { return true; }
    public function close(): bool { return true; }

    public function read(string $id): string|false
    {
        $r = $this->bdd->prepare('SELECT data FROM sessions WHERE id = ?');
        $r->execute([$id]);
        return (string) ($r->fetchColumn() ?: '');
    }

    public function write(string $id, string $data): bool
    {
        $this->bdd->beginTransaction();
        $this->bdd->prepare('DELETE FROM sessions WHERE id = ?')->execute([$id]);
        $this->bdd->prepare('INSERT INTO sessions (id, data, derniere_activite) VALUES (?, ?, ?)')
            ->execute([$id, $data, time()]);
        return $this->bdd->commit();
    }

    public function destroy(string $id): bool
    {
        return $this->bdd->prepare('DELETE FROM sessions WHERE id = ?')->execute([$id]);
    }

    public function gc(int $max_lifetime): int|false
    {
        $r = $this->bdd->prepare('DELETE FROM sessions WHERE derniere_activite < ?');
        $r->execute([time() - $max_lifetime]);
        return $r->rowCount();
    }
}

if (EN_LIGNE || getenv('SESSION_BDD')) {
    session_set_save_handler(new SessionBdd($bdd), true);
}

$https = ($_SERVER['HTTPS'] ?? '') === 'on' || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https';
session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'secure'   => $https,
    'httponly' => true,
    'samesite' => 'Lax',
]);
ini_set('session.gc_maxlifetime', '7200');
ini_set('session.use_strict_mode', '1');
session_start();

/* ------------------------------------------------------------------ */
/* Fonctions utilitaires                                               */
/* ------------------------------------------------------------------ */

/** Échappe une valeur pour l'affichage HTML (à utiliser sur TOUTE donnée affichée). */
function e(mixed $valeur): string
{
    return htmlspecialchars((string) ($valeur ?? ''), ENT_QUOTES, 'UTF-8');
}

/** Nettoie une saisie texte (on stocke la valeur brute, l'échappement se fait à l'affichage). */
function nettoyer(?string $valeur): string
{
    return trim((string) $valeur);
}

function est_connecte(): bool
{
    return isset($_SESSION['utilisateur']);
}

function est_admin(): bool
{
    return ($_SESSION['utilisateur']['role'] ?? null) === 'admin';
}

function utilisateur_courant(): ?array
{
    return $_SESSION['utilisateur'] ?? null;
}

function rediriger(string $url): void
{
    header('Location: ' . $url);
    exit;
}

function flash(string $type, string $texte): void
{
    $_SESSION['message'] = ['type' => $type, 'texte' => $texte];
}

function lire_flash(): ?array
{
    $m = $_SESSION['message'] ?? null;
    unset($_SESSION['message']);
    return $m;
}

function formater_prix(mixed $prix): string
{
    return number_format((float) $prix, 0, ',', ' ') . ' FCFA';
}

function formater_date(?string $date): string
{
    return $date ? date('d/m/Y', strtotime($date)) : '';
}

function extrait(?string $texte, int $longueur = 80): string
{
    $texte = (string) $texte;
    return mb_strlen($texte) > $longueur ? mb_substr($texte, 0, $longueur) . '…' : $texte;
}

/* ---------- Protection CSRF ---------- */

function jeton_csrf(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function champ_csrf(): string
{
    return '<input type="hidden" name="csrf" value="' . e(jeton_csrf()) . '">';
}

function verifier_csrf(): void
{
    $jeton = $_POST['csrf'] ?? '';
    if (!is_string($jeton) || !hash_equals(jeton_csrf(), $jeton)) {
        http_response_code(400);
        flash('danger', 'Votre session a expiré, merci de réessayer.');
        rediriger($_SERVER['HTTP_REFERER'] ?? 'index.php');
    }
}

function est_post(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}

/* ---------- Rendu des vues ---------- */

/**
 * Affiche une vue dans le gabarit commun.
 * @param array<string,mixed> $donnees variables accessibles dans la vue
 */
function afficher(string $vue, array $donnees = [], string $titre = ''): void
{
    extract($donnees, EXTR_SKIP);
    $titre_page = $titre;
    $page_active = $_GET['page'] ?? 'accueil';
    require RACINE . '/vues/entete.php';
    require RACINE . '/vues/' . $vue . '.php';
    require RACINE . '/vues/pied.php';
}

function exiger_connexion(): void
{
    if (!est_connecte()) {
        flash('info', 'Connectez-vous pour accéder à cette page.');
        rediriger('index.php?page=connexion');
    }
}

function exiger_admin(): void
{
    exiger_connexion();
    if (!est_admin()) {
        flash('danger', 'Accès réservé aux administrateurs.');
        rediriger('index.php');
    }
}
