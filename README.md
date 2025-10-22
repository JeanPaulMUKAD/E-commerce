# 🏗️ E-Commerce - Johnson Jr Construction

## 📋 Description du logiciel

**E-Commerce Johnson Jr Construction** est une plateforme e-commerce spécialisée dans le secteur de la construction, développée en PHP avec une interface d'administration moderne. Le système permet la gestion complète d'un site de vente en ligne avec un focus sur les produits de construction et le marché congolais.

## 🎯 Fonctionnalités principales

### 📊 Tableau de bord administratif
- **Statistiques en temps réel** : Produits, réservations, clients
- **Filtrage par mois** : Analyse des performances mensuelles
- **Graphiques de fréquence** : Évolution sur 6 mois
- **Interface moderne** : Design responsive avec Tailwind CSS

### 📦 Gestion des produits
- **CRUD complet** : Ajout, modification, suppression, consultation
- **Upload d'images** : Gestion des photos de produits
- **Catégorisation** : Organisation par catégories
- **Gestion des stocks** : Suivi des quantités disponibles
- **Système de devises avancé** :
  - Prix en USD et Francs Congolais (FC)
  - Conversion automatique (1 USD = 2,750 FC)
  - Sélection de devise d'entrée (USD/FC)
  - Affichage en temps réel des conversions
  - Stockage unifié en USD

### 👥 Gestion des clients
- **Base de données clients** : Informations complètes
- **Rôles utilisateurs** : Distinction admin/client
- **Interface de consultation** : Liste détaillée des clients

### 📅 Système de réservations
- **Réservations produits** : Liaison clients-produits
- **Suivi des commandes** : Statut et historique
- **Interface de gestion** : Vue d'ensemble des réservations

### 🔐 Authentification et sécurité
- **Système de connexion** : Protection des pages admin
- **Gestion des sessions** : Sécurité des accès
- **Déconnexion sécurisée** : Logout propre

## 🛠️ Technologies utilisées

### Backend
- **PHP** : Langage principal
- **MySQL** : Base de données
- **Sessions** : Gestion de l'authentification

### Frontend
- **Tailwind CSS** : Framework CSS moderne
- **Font Awesome** : Icônes
- **JavaScript** : Interactions dynamiques
- **Design responsive** : Compatible mobile/desktop

### Base de données
- **Tables principales** :
  - `produits` : Catalogue des produits
  - `utilisateurs` : Clients et admins
  - `reservations` : Commandes et réservations
  - `clients` : Clients

## 🎨 Interface utilisateur

### Design moderne
- **Couleur principale** : Violet (#673DE6)
- **Sidebar navigation** : Menu latéral fixe
- **Cards et tables** : Présentation claire des données
- **Animations** : Transitions fluides

### Expérience utilisateur
- **Navigation intuitive** : Menu latéral avec icônes
- **Feedback visuel** : Messages de confirmation/erreur
- **Conversion de devises** : Interface interactive
- **Responsive design** : Adaptation mobile

## 🌍 Spécialisation géographique

### Marché ciblé
- **République Démocratique du Congo** : Support FC
- **Marché international** : Support USD
- **Conversion automatique** : Facilité d'utilisation

### Fonctionnalités multidevises
- **Saisie flexible** : Choix de la devise d'entrée (USD/FC)
- **Stockage unifié** : Tout en USD en base de données
- **Affichage adaptatif** : Conversion temps réel
- **Taux de change** : 1 USD = 2,750 FC

## 📊 Fonctionnalités avancées

### Analytics et reporting
- **Statistiques mensuelles** : Suivi des performances
- **Graphiques de tendance** : Évolution sur 6 mois
- **Métriques clés** : Produits, clients, réservations

### Gestion des médias
- **Upload d'images** : Photos produits
- **Stockage local** : Dossier uploads
- **Prévisualisation** : Images dans les listes

## 🚀 Points forts du système

1. **Interface moderne** : Design professionnel et intuitif
2. **Multidevises** : Support USD/FC avec conversion automatique
3. **Sécurité** : Authentification et protection des données
4. **Scalabilité** : Architecture modulaire et extensible
5. **Spécialisation** : Adapté au marché congolais
6. **Performance** : Requêtes optimisées et interface fluide

## 📁 Structure du projet

```
E-commerce/
├── admin/                    # Interface d'administration
│   ├── includes/            # Composants réutilisables
│   ├── connexion/          # Système d'authentification
│   ├── dashboard.php        # Tableau de bord
│   ├── produits.php        # Gestion des produits
│   ├── clients.php         # Gestion des clients
│   ├── reservations.php    # Gestion des réservations
│   └── uploads/           # Images des produits
├── config/
│   └── database.php        # Configuration base de données
└── assets/                 # Ressources statiques
```

## 🔧 Installation

1. **Prérequis** :
   - PHP 7.4+
   - MySQL 5.7+
   - Serveur web (Apache/Nginx)

2. **Configuration** :
   - Modifier `config/database.php` avec vos paramètres
   - Créer la base de données `e_commerce_db`
   - Importer les tables nécessaires

3. **Démarrage** :
   - Accéder à `admin/index.php`
   - Se connecter avec les identifiants admin

## 📞 Support

Pour toute question ou support technique, contactez l'équipe de développement.

---

**© 2024 Johnson Jr Construction - Tous droits réservés**