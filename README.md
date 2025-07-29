# 📚 Projet de Fin de Formation : **Plumédia** – Plateforme de lecture & écriture d’histoires

## 📝 Description du Projet

**Plumédia** est un projet réalisé dans le cadre de mon **projet de fin de formation développeuse web**.  
Il s’agit d’une **plateforme en ligne** qui permet aux utilisateurs de **lire, écrire et partager des histoires** dans un espace communautaire, intuitif et créatif.
Ce projet a été conçu avec Symfony et des outils modernes du web. Il a pour objectif de proposer une expérience immersive pour les passionnés de lecture et d’écriture.

## 🚀 Fonctionnalités
  
  ### ✍️ Côté utilisateurs
  * Création de **comptes utilisateurs**, modification des données, suppression de compte
  * Publication et édition de ses propres **histoires** et de leur chapitres
  * Lecture des histoires postées par la communauté
  * Classement des histoires populaires par follow puis par like
  * Possibilité de **commenter** et de **liker** les histoires
  * Calendrier avec gestion d'affichage des chapitres

  ### 🛠️ Côté administrateur
  * Possibilité de changer les roles des utilisateurs
  * Panneau admin pour afficher les données

  ### 📊 Autres fonctionnalités clés
  * **Interface responsive** (mobile/tablette/desktop)
  * **Protection des formulaires** avec reCAPTCHA
  * **Gestion des cookies** avec tarteAuCitron.js
  * Connexion Via Google
  * Un utilisateur peut mettre une biographie et partager ces réseaux sociaux.

## 🗃️ Modèles de données
  ### 📌 MCD (Modèle Conceptuel de Données)
  <img width="899" height="606" alt="Image2" src="https://github.com/user-attachments/assets/61f53e3a-db8d-4937-a02c-a6ec837bc424" />
  ### 📌 MLD (Modèle Logique de Données)
  <img width="872" height="604" alt="Image1" src="https://github.com/user-attachments/assets/d543023c-9b65-49f3-a9f0-420aaa232d12" />

## 🔧 Technologies Utilisées

* **Symfony 7**
  *  Framework PHP robuste et extensible
* **Twig**
  *  Moteur de template utilisé pour l’interface utilisateur
* **Doctrine ORM**
  *  Pour la base de données relationnelle
* **Bootstrap 5**
  *  Pour une mise en page moderne et responsive
* **Tarteaucitron.js**
  *  Pour la gestion des cookies (RGPD)
* **Google reCAPTCHA**
  *  Pour la sécurité des formulaires
* **MySQL**
  *  Base de données relationnelle
* **Font Awesome**
  *  Pour les icônes interactives
* **FullCalendar**
  * Pour les événements et dates de publication (si utilisé)

## 📌 Points Importants

* Projet **full-stack** conçu de A à Z (architecture, base de données, logique métier, design)
* **Sécurité** : validation des données, rôles utilisateurs, gestion des permissions
* Respect des normes **RGPD** (cookies, mentions légales, politique de confidentialité)
* Interface **user-friendly** pensée pour les écrivains et lecteurs
* Code structuré, réutilisable

## 🎯 Objectifs Pédagogiques

* Mener un projet complet, de la conception à la mise en production
* Développer des fonctionnalités avancées en Symfony
* Maîtriser l'intégration front-end avec Twig
* Gérer une base de données relationnelle complexe avec Doctrine
* Appliquer les bonnes pratiques de sécurité et d’accessibilité

## 🔮 Améliorations Possibles

* Export d’histoires en format PDF ou EPUB
* Intégration d’un **chat communautaire** ou d’un forum
* Tableau de bord pour les auteurs (statistiques avancées)
* Intégration de **notifications par e-mail**

## 📥 Installation

    git clone https://github.com/Oarlina/Plumedia  
    cd Plumedia  
    composer install    
    symfony server:start  
