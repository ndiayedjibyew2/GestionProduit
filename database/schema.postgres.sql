-- Schéma Postgres (Supabase) — déjà appliqué au projet Supabase "gestion-produits".
CREATE TABLE public.categories (
  id SERIAL PRIMARY KEY,
  nom VARCHAR(100) NOT NULL UNIQUE,
  description TEXT,
  date_creation TIMESTAMPTZ NOT NULL DEFAULT now()
);
CREATE TABLE public.produits (
  id SERIAL PRIMARY KEY,
  nom VARCHAR(150) NOT NULL,
  description TEXT,
  prix NUMERIC(10,2) NOT NULL CHECK (prix > 0),
  quantite INTEGER NOT NULL DEFAULT 0 CHECK (quantite >= 0),
  categorie_id INTEGER REFERENCES public.categories(id) ON DELETE SET NULL,
  date_creation TIMESTAMPTZ NOT NULL DEFAULT now(),
  date_modification TIMESTAMPTZ NOT NULL DEFAULT now()
);
CREATE INDEX produits_categorie_id_idx ON public.produits(categorie_id);
CREATE TABLE public.utilisateur (
  id SERIAL PRIMARY KEY,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  nom VARCHAR(100) NOT NULL,
  role VARCHAR(20) NOT NULL DEFAULT 'utilisateur' CHECK (role IN ('admin','utilisateur')),
  date_creation TIMESTAMPTZ NOT NULL DEFAULT now()
);
CREATE TABLE public.sessions (
  id VARCHAR(128) PRIMARY KEY,
  data TEXT NOT NULL,
  derniere_activite INTEGER NOT NULL
);
-- L'API REST publique de Supabase n'a aucun accès : seule l'application (rôle propriétaire) lit/écrit.
ALTER TABLE public.categories ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.produits ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.utilisateur ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.sessions ENABLE ROW LEVEL SECURITY;
REVOKE ALL ON public.categories, public.produits, public.utilisateur, public.sessions FROM anon, authenticated;
