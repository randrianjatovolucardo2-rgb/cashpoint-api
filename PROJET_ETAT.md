# État du projet — Cash Point Mobile Money

## 1. Objectif
Application de gestion centralisée de Cash Points Mobile Money à Madagascar.

Opérateurs prévus :
- MVola
- Orange Money
- Airtel Money

Architecture actuelle :
- Application mobile : Flutter / Android
- Backend API : Laravel
- Base de données : MySQL
- Authentification API : Laravel Sanctum

Le MVP fonctionne sans API officielle des opérateurs : les transactions sont saisies manuellement par l’agent, puis le serveur met automatiquement à jour les soldes.

---

## 2. Projets locaux

### Backend Laravel
Chemin local :
`D:\Projets\cashpoint_api`

Commande de lancement :
```powershell
cd D:\Projets\cashpoint_api
php artisan serve --host=0.0.0.0 --port=8000
```

API sur le réseau local :
`http://192.168.0.107:8000/api`

### Application Flutter
Chemin local :
`D:\Projets\mobile_money_cashpoint`

Commande de lancement :
```powershell
cd D:\Projets\mobile_money_cashpoint
flutter run -d 099186436B000386
```

---

## 3. Base de données

Base :
`cashpoint_db`

Tables principales :
- users
- agents
- cash_points
- transactions
- clotures
- personal_access_tokens

Relations principales :
- un User de rôle agent peut être lié à un Agent via `agent_id`
- un Agent possède un Cash Point
- une Transaction appartient à un Agent et à un Cash Point
- une Clôture appartient à un Agent et à un Cash Point

---

## 4. Règles métier

### Dépôt client
Quand un client effectue un dépôt :
- les espèces du Cash Point augmentent
- le solde électronique de l’opérateur sélectionné diminue

### Retrait client
Quand un client effectue un retrait :
- les espèces du Cash Point diminuent
- le solde électronique de l’opérateur sélectionné augmente

### Contrôles
Le serveur refuse :
- un dépôt si le solde électronique de l’opérateur est insuffisant
- un retrait si le solde espèces est insuffisant

### Commission
La commission est saisie manuellement pour le moment.

---

## 5. Authentification

Deux rôles :
- `patron`
- `agent`

Le login se fait par :
- téléphone
- mot de passe

Laravel Sanctum génère un token.

Important :
Le login ne doit pas supprimer tous les anciens tokens. Plusieurs sessions peuvent donc rester actives.

---

## 6. Routes API principales

### Publique
`POST /api/login`

### Authentifiées
`POST /api/logout`

### Espace Agent
`GET /api/mon-espace`

`POST /api/mon-espace/transactions`

`POST /api/mon-espace/clotures`

### Patron
Les routes suivantes sont protégées par `auth:sanctum` + `PatronMiddleware`.

`GET /api/agents`

`POST /api/agents`

`GET /api/cash-points`

`POST /api/cash-points`

`GET /api/transactions`

`POST /api/transactions`

`GET /api/clotures`

`POST /api/clotures`

---

## 7. Fonctionnalités déjà réalisées

### Patron
- connexion
- consultation des Cash Points
- consultation des soldes
- consultation des transactions
- consultation des clôtures
- rafraîchissement du tableau de bord
- création de transactions côté Patron
- création de clôtures côté Patron

### Agent
- connexion
- accès uniquement à son Cash Point
- consultation de ses soldes
- consultation de ses transactions
- saisie d’un dépôt ou retrait
- mise à jour automatique des soldes côté serveur
- création d’une clôture journalière

---

## 8. État de test actuel

Un Cash Point de démonstration existe :
- Cash Point Analakely
- Agent : Jean

Le backend a déjà retourné correctement :
- le Cash Point
- ses soldes
- 3 transactions

Les transactions de démonstration comprennent :
- deux dépôts MVola
- un retrait Airtel Money

Le tableau de bord Patron affiche correctement les données après authentification.

L’espace Agent affiche correctement :
- les soldes
- les transactions
- le bouton Transaction
- l’accès à la clôture journalière

---

## 9. Fichiers importants

### Laravel
- `app/Http/Controllers/Api/AuthController.php`
- `app/Http/Middleware/PatronMiddleware.php`
- `routes/api.php`
- modèles : Agent, CashPoint, Transaction, Cloture, User

### Flutter
- `lib/services/service_api.dart`
- `lib/ecrans/page_connexion.dart`
- `lib/ecrans/page_espace_agent.dart`
- `lib/ecrans/page_tableau_de_bord.dart`
- `lib/ecrans/page_transaction.dart`
- `lib/ecrans/page_cloture_journaliere.dart`
- `lib/ecrans/page_historique_transactions.dart`
- `lib/ecrans/page_historique_clotures.dart`

---

## 10. Points à continuer

Priorités proposées :
1. tester complètement la clôture journalière Agent
2. vérifier que le Patron voit immédiatement la clôture et les écarts
3. ajouter l’historique des clôtures côté Agent
4. créer automatiquement un compte User lorsqu’un nouvel Agent est créé par le Patron
5. améliorer les alertes d’écart de clôture
6. préparer un vrai hébergement distant du backend pour sortir du réseau local
7. plus tard, intégrer les API officielles opérateurs si elles sont disponibles

---

## 11. Important pour GitHub

Ne jamais publier :
- `.env`
- mots de passe réels
- clés API
- tokens Sanctum
- secrets MySQL
- clés privées

Avant chaque push :
```powershell
git status
```

Puis :
```powershell
git add .
git commit -m "Description des changements"
git push
```

---

## 12. Reprise dans un nouveau ChatGPT

Dans le nouveau compte ChatGPT, fournir les dépôts GitHub puis dire :

> Lis `PROJET_ETAT.md` et reprends le développement du projet Cash Point Mobile Money à partir de l’état actuel.

