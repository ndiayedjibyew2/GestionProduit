<?php

class AuthControleur
{
    public function __construct(private PDO $bdd) {}

    public function connexion(): void
    {
        if (est_connecte()) {
            rediriger('index.php');
        }

        $erreur = '';
        $email = '';

        if (est_post()) {
            verifier_csrf();
            $email = mb_strtolower(nettoyer($_POST['email'] ?? ''));
            $password = (string) ($_POST['password'] ?? '');

            $r = $this->bdd->prepare('SELECT * FROM utilisateur WHERE LOWER(email) = ?');
            $r->execute([$email]);
            $utilisateur = $r->fetch();

            if ($utilisateur && password_verify($password, $utilisateur['password'])) {
                $this->ouvrirSession($utilisateur);
                flash('success', 'Bienvenue ' . $utilisateur['nom'] . ' !');
                rediriger(est_admin() ? 'index.php?page=admin' : 'index.php');
            }
            $erreur = 'Email ou mot de passe incorrect.';
        }

        afficher('auth/connexion', compact('erreur', 'email'), 'Connexion');
    }

    public function inscription(): void
    {
        if (est_connecte()) {
            rediriger('index.php');
        }

        $erreurs = [];
        $nom = '';
        $email = '';

        if (est_post()) {
            verifier_csrf();
            $nom = nettoyer($_POST['nom'] ?? '');
            $email = mb_strtolower(nettoyer($_POST['email'] ?? ''));
            $password = (string) ($_POST['password'] ?? '');
            $confirmation = (string) ($_POST['confirmation'] ?? '');

            if ($nom === '' || mb_strlen($nom) > 100) $erreurs[] = 'Le nom est obligatoire (100 caractères max).';
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $erreurs[] = "L'adresse email n'est pas valide.";
            if (strlen($password) < 8) $erreurs[] = 'Le mot de passe doit contenir au moins 8 caractères.';
            if ($password !== $confirmation) $erreurs[] = 'Les mots de passe ne correspondent pas.';

            if (!$erreurs) {
                $r = $this->bdd->prepare('SELECT id FROM utilisateur WHERE LOWER(email) = ?');
                $r->execute([$email]);
                if ($r->fetch()) {
                    $erreurs[] = 'Cet email est déjà utilisé.';
                }
            }

            if (!$erreurs) {
                $this->bdd->prepare('INSERT INTO utilisateur (nom, email, password) VALUES (?, ?, ?)')
                    ->execute([$nom, $email, password_hash($password, PASSWORD_DEFAULT)]);

                $r = $this->bdd->prepare('SELECT * FROM utilisateur WHERE email = ?');
                $r->execute([$email]);
                $this->ouvrirSession($r->fetch());
                flash('success', 'Compte créé. Bienvenue ' . $nom . ' !');
                rediriger('index.php');
            }
        }

        afficher('auth/inscription', compact('erreurs', 'nom', 'email'), 'Inscription');
    }

    public function deconnexion(): void
    {
        $_SESSION = [];
        session_regenerate_id(true);
        flash('success', 'Vous êtes déconnecté.');
        rediriger('index.php');
    }

    private function ouvrirSession(array $u): void
    {
        session_regenerate_id(true); // évite la fixation de session
        $_SESSION['utilisateur'] = [
            'id'    => (int) $u['id'],
            'email' => $u['email'],
            'nom'   => $u['nom'],
            'role'  => $u['role'],
        ];
    }
}
