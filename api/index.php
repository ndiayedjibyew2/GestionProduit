<?php
// Point d'entrée Vercel : toutes les requêtes sont redirigées ici (voir vercel.json).
chdir(dirname(__DIR__));
require dirname(__DIR__) . '/index.php';
