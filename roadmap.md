# Roadmap Complète - Application Gestion de Crèche

## 📋 Stack Technique
- **Backend:** Laravel
- **Frontend:** Inertia.js + Vue.js + Tailwind CSS + shadcn-vue
- **Icons:** Lucide Icons
- **Permissions:** Laravel Permission (spatie/laravel-permission)
- **Testing:** Pest (Tests d'intégration)
- **Méthodologie:** TDD (Test-Driven Development)

---

## 👥 Types d'Utilisateurs

1. **Admin** - Gestion globale de l'application
2. **Director/Manager** - Création et gestion complète de SA crèche
3. **Staff/Personnel** - Gestion des transmissions et communication
4. **Parent** - Consultation et communication

---

## 🎯 Phase 0 : Fondations (Prérequis)

### 0.1 Architecture & Setup Initial
- [ ] Configuration des rôles et permissions (Admin, Director, Staff, Parent)
- [ ] Structure des dossiers pour les tests
- [ ] Configuration Pest pour les tests d'intégration
- [ ] Setup des helpers de test

**Durée estimée:** 1-2 jours

---

## 📋 Phase 1 : Gestion des Utilisateurs & Authentification

### Feature 1.1 : Système de Rôles
**Besoins :**
- 4 rôles distincts avec permissions spécifiques
- Gestion des permissions via Laravel Permission
- Super admin pour la gestion globale

**Livrables :**
- [ ] Migration pour les rôles de base
- [ ] Seeder pour les rôles et permissions initiales
- [ ] Tests d'intégration pour la création/attribution des rôles
- [ ] Middleware pour les vérifications de rôles

**Durée estimée:** 2-3 jours

---

## 🏢 Phase 2 : Gestion des Crèches (Daycare)

### Feature 2.1 : CRUD Crèche (Director uniquement)
**Besoins :**
- Un directeur peut créer/gérer plusieurs crèches
- **Une crèche appartient à UN SEUL directeur** (relation One-to-Many)
- Informations : nom, adresse, téléphone, email, horaires, capacité, director_id

**Livrables :**
- [ ] Migration `daycares` table avec `director_id`
- [ ] Factory & Seeder
- [ ] Tests d'intégration :
  - [ ] `DaycareIndexTest.php`
  - [ ] `DaycareCreateTest.php`
  - [ ] `DaycareStoreTest.php`
  - [ ] `DaycareShowTest.php`
  - [ ] `DaycareUpdateTest.php`
  - [ ] `DaycareDestroyTest.php`
- [ ] Policy `DaycarePolicy` (create, view, update, delete)
- [ ] Form Requests :
  - [ ] `StoreDaycareRequest`
  - [ ] `UpdateDaycareRequest`
- [ ] Controller + Routes
- [ ] Views (Inertia/Vue)

### Feature 2.2 : Vérification Propriété Crèche
**Besoins :**
- Un directeur ne peut gérer que SES crèches
- Middleware pour vérifier la propriété avant toute action

**Livrables :**
- [ ] Middleware `EnsureDaycareOwnership`
- [ ] Policy `DaycarePolicy`
- [ ] Tests pour les tentatives d'accès non autorisées

**Durée estimée:** 4-5 jours

---

## 👥 Phase 3 : Gestion du Personnel (Staff)

### Feature 3.1 : CRUD Personnel
**Besoins :**
- Le directeur peut ajouter du personnel à sa crèche
- Informations : nom, prénom, email, téléphone, poste, date d'embauche
- Relation many-to-many (un staff peut travailler dans plusieurs crèches)

**Livrables :**
- [ ] Migration `staff_daycare` (pivot table)
- [ ] Migration pour profil staff (job_title, hire_date, etc.)
- [ ] Factory & Seeder
- [ ] Tests d'intégration :
  - [ ] `StaffIndexTest.php`
  - [ ] `StaffStoreTest.php`
  - [ ] `StaffUpdateTest.php`
  - [ ] `StaffDestroyTest.php`
- [ ] Controller + Routes
- [ ] Views pour gestion du personnel

### Feature 3.2 : Permissions du Personnel
**Besoins :**
- Le personnel ne peut accéder qu'aux crèches où il est assigné
- Permissions limitées (pas d'administration de la crèche)

**Livrables :**
- [ ] Middleware `EnsureStaffBelongsToDaycare`
- [ ] Tests pour la vérification des accès
- [ ] Policy `StaffPolicy`

**Durée estimée:** 3-4 jours

---

## 👶 Phase 4 : Gestion des Enfants (Children)

### Feature 4.1 : CRUD Enfants
**Besoins :**
- Directeur et personnel peuvent gérer les enfants
- Informations : nom, prénom, date de naissance, allergies, notes médicales, photo
- Un enfant appartient à une crèche

**Livrables :**
- [ ] Migration `children` table
- [ ] Relation avec `daycares` (belongsTo)
- [ ] Factory & Seeder
- [ ] Tests d'intégration :
  - [ ] `ChildIndexTest.php`
  - [ ] `ChildStoreTest.php`
  - [ ] `ChildShowTest.php`
  - [ ] `ChildUpdateTest.php`
  - [ ] `ChildDestroyTest.php`
- [ ] Controller + Routes
- [ ] Views (liste, fiche enfant, formulaires)
- [ ] Upload de photo

### Feature 4.2 : Association Enfant-Parent
**Besoins :**
- Un enfant peut avoir plusieurs parents (tuteurs)
- Un parent peut avoir plusieurs enfants
- Relation many-to-many avec informations supplémentaires (lien de parenté)

**Livrables :**
- [ ] Migration pivot `child_parent` (avec relationship_type)
- [ ] Tests pour les associations
- [ ] Vue pour gérer les liens parent-enfant
- [ ] Validation des associations

**Durée estimée:** 4-5 jours

---

## 👪 Phase 5 : Gestion des Parents

### Feature 5.1 : CRUD Parents
**Besoins :**
- Directeur et personnel peuvent ajouter des parents
- Informations : nom, prénom, email, téléphone, adresse
- Association automatique avec la crèche via les enfants

**Livrables :**
- [ ] Migration pour informations profil parent
- [ ] Factory & Seeder
- [ ] Tests d'intégration :
  - [ ] `ParentIndexTest.php`
  - [ ] `ParentStoreTest.php`
  - [ ] `ParentUpdateTest.php`
  - [ ] `ParentDestroyTest.php`
- [ ] Controller + Routes
- [ ] Views pour gestion des parents

### Feature 5.2 : Accès Parents
**Besoins :**
- Les parents ne voient que les infos de leurs enfants
- Accès restreint à la crèche de leurs enfants
- Dashboard parent personnalisé

**Livrables :**
- [ ] Middleware `EnsureParentAccess`
- [ ] Policy `ParentPolicy`
- [ ] Tests pour les restrictions d'accès
- [ ] Dashboard parent
- [ ] Navigation spécifique parents

**Durée estimée:** 3-4 jours

---

## 📝 Phase 6 : Transmissions (Core Feature) ⭐

### Feature 6.1 : Types de Transmissions
**Besoins :**
- Plusieurs types : repas, sieste, activité, change, incident, médicament, humeur
- Chaque type a des champs spécifiques (JSON)
- Types configurables par crèche

**Livrables :**
- [ ] Migration `transmission_types` table
- [ ] Migration `transmissions` table avec champs JSON
- [ ] Seeder pour les types de base
- [ ] Factory pour transmissions

### Feature 6.2 : CRUD Transmissions
**Besoins :**
- Personnel peut créer des transmissions pour les enfants
- Transmissions visibles par les parents (temps réel si possible)
- Photos/fichiers attachés optionnels

**Livrables :**
- [ ] Tests d'intégration :
  - [ ] `TransmissionIndexTest.php`
  - [ ] `TransmissionStoreTest.php`
  - [ ] `TransmissionShowTest.php`
  - [ ] `TransmissionUpdateTest.php`
  - [ ] `TransmissionDestroyTest.php`
- [ ] Controller + Routes
- [ ] Views (formulaires par type de transmission)
- [ ] Upload de fichiers/photos
- [ ] Validation par type

### Feature 6.3 : Timeline des Transmissions
**Besoins :**
- Vue chronologique par enfant par jour
- Filtres (date, type, enfant)
- Recherche
- Export PDF journalier

**Livrables :**
- [ ] Tests pour les filtres
- [ ] Component Vue timeline
- [ ] API endpoint optimisé
- [ ] Pagination/infinite scroll
- [ ] Export PDF

**Durée estimée:** 5-7 jours

---

## 💬 Phase 7 : Communication

### Feature 7.1 : Messages Crèche-Parents
**Besoins :**
- Personnel et directeur peuvent envoyer des messages aux parents
- Parents peuvent répondre
- Conversation thread (historique)
- Pièces jointes

**Livrables :**
- [ ] Migration `messages` table
- [ ] Migration `conversations` table
- [ ] Tests d'intégration :
  - [ ] `MessageIndexTest.php`
  - [ ] `MessageStoreTest.php`
  - [ ] `MessageShowTest.php`
- [ ] Controller + Routes
- [ ] Views messagerie
- [ ] Component Vue chat

### Feature 7.2 : Annonces Générales
**Besoins :**
- Directeur peut publier des annonces pour tous les parents
- Catégories : info, urgence, événement
- Marquage lu/non lu

**Livrables :**
- [ ] Migration `announcements` table
- [ ] Tests d'intégration
- [ ] Controller + Routes
- [ ] Component Vue pour affichage annonces
- [ ] Système de lecture tracking

**Durée estimée:** 4-5 jours

---

## 🔔 Phase 8 : Notifications

### Feature 8.1 : Système de Notifications
**Besoins :**
- Notifications email pour nouvelles transmissions
- Notifications in-app pour messages
- Préférences de notification par utilisateur
- Badge de notifications non lues

**Livrables :**
- [ ] Migration `notification_preferences` table
- [ ] Laravel Notifications setup
- [ ] Tests pour l'envoi de notifications
- [ ] Queue jobs pour les emails
- [ ] Component Vue notifications dropdown
- [ ] Page préférences utilisateur

**Durée estimée:** 2-3 jours

---

## 📊 Phase 9 : Tableaux de Bord

### Feature 9.1 : Dashboard Admin
**Besoins :**
- Vue d'ensemble de toutes les crèches
- Statistiques globales (nombre crèches, utilisateurs, enfants)
- Activité récente

**Livrables :**
- [ ] Tests dashboard admin
- [ ] Controller + Routes
- [ ] Vue dashboard avec graphiques
- [ ] Components Vue statistiques

### Feature 9.2 : Dashboard Directeur
**Besoins :**
- Vue par crèche (sélecteur si plusieurs)
- Statistiques : nombre d'enfants, personnel, présences du jour
- Activité récente de la crèche

**Livrables :**
- [ ] Tests dashboard directeur
- [ ] Controller + Routes
- [ ] Vue dashboard directeur
- [ ] Widgets statistiques

### Feature 9.3 : Dashboard Personnel
**Besoins :**
- Liste des enfants présents aujourd'hui
- Raccourcis pour transmissions rapides
- Messages non lus
- Planning du jour

**Livrables :**
- [ ] Tests dashboard personnel
- [ ] Controller + Routes
- [ ] Vue dashboard personnel
- [ ] Quick actions components

### Feature 9.4 : Dashboard Parent
**Besoins :**
- Infos de leurs enfants (présence, dernière transmission)
- Dernières transmissions (timeline)
- Messages non lus
- Prochains événements

**Livrables :**
- [ ] Tests dashboard parent
- [ ] Controller + Routes
- [ ] Vue dashboard parent
- [ ] Components enfants cards

**Durée estimée:** 5-6 jours

---

## 🔐 Phase 10 : Sécurité & Performance

### Feature 10.1 : Audit & Logs
**Besoins :**
- Logs des actions sensibles (suppression, modification données enfants)
- Trail d'audit pour conformité

**Livrables :**
- [ ] Migration `activity_logs` table
- [ ] Event listeners
- [ ] Tests audit trail
- [ ] Vue historique actions

### Feature 10.2 : Optimisation
**Besoins :**
- Cache pour les requêtes fréquentes
- Eager loading des relations
- Optimisation des requêtes N+1

**Livrables :**
- [ ] Cache strategy
- [ ] Tests de performance
- [ ] Optimisation queries
- [ ] Indexation DB

**Durée estimée:** 3-5 jours

---

## 📱 Phase 11 : Mobile & UX

### Feature 11.1 : Responsive Design
**Besoins :**
- Optimisation mobile (crucial pour les parents)
- Navigation adaptative
- PWA capabilities (optionnel)

**Livrables :**
- [ ] Responsive design tous écrans
- [ ] Touch-friendly interfaces
- [ ] PWA manifest (optionnel)
- [ ] Tests responsive

**Durée estimée:** 3-5 jours

---

## 📐 Structure des Tests

```
tests/
├── Feature/
│   ├── Auth/
│   │   └── RolePermissionTest.php
│   ├── Daycare/
│   │   ├── DaycareIndexTest.php
│   │   ├── DaycareCreateTest.php
│   │   ├── DaycareStoreTest.php
│   │   ├── DaycareShowTest.php
│   │   ├── DaycareUpdateTest.php
│   │   └── DaycareDestroyTest.php
│   ├── Staff/
│   │   ├── StaffIndexTest.php
│   │   ├── StaffStoreTest.php
│   │   ├── StaffUpdateTest.php
│   │   └── StaffDestroyTest.php
│   ├── Child/
│   │   ├── ChildIndexTest.php
│   │   ├── ChildStoreTest.php
│   │   ├── ChildShowTest.php
│   │   ├── ChildUpdateTest.php
│   │   └── ChildDestroyTest.php
│   ├── Parent/
│   │   ├── ParentIndexTest.php
│   │   ├── ParentStoreTest.php
│   │   ├── ParentUpdateTest.php
│   │   └── ParentDestroyTest.php
│   ├── Transmission/
│   │   ├── TransmissionIndexTest.php
│   │   ├── TransmissionStoreTest.php
│   │   ├── TransmissionShowTest.php
│   │   ├── TransmissionUpdateTest.php
│   │   └── TransmissionDestroyTest.php
│   ├── Message/
│   │   ├── MessageIndexTest.php
│   │   ├── MessageStoreTest.php
│   │   └── MessageShowTest.php
│   ├── Announcement/
│   │   ├── AnnouncementIndexTest.php
│   │   ├── AnnouncementStoreTest.php
│   │   └── AnnouncementDestroyTest.php
│   └── Dashboard/
│       ├── AdminDashboardTest.php
│       ├── DirectorDashboardTest.php
│       ├── StaffDashboardTest.php
│       └── ParentDashboardTest.php
└── Pest.php
```

---

## 🗂️ Relations Base de Données

```
User (Director)
  └─ hasMany → Daycare (director_id)

User (Staff)
  └─ belongsToMany → Daycare (via staff_daycare pivot)

User (Parent)
  └─ belongsToMany → Child (via child_parent pivot)

Daycare
  ├─ belongsTo → User (Director)
  ├─ belongsToMany → User (Staff)
  ├─ hasMany → Children
  ├─ hasMany → Transmissions
  └─ hasMany → Announcements

Child
  ├─ belongsTo → Daycare
  ├─ belongsToMany → User (Parents)
  └─ hasMany → Transmissions

Transmission
  ├─ belongsTo → Child
  ├─ belongsTo → User (author - staff)
  └─ belongsTo → TransmissionType

Message
  ├─ belongsTo → User (sender)
  ├─ belongsTo → User (receiver)
  └─ belongsTo → Conversation

Announcement
  ├─ belongsTo → Daycare
  └─ belongsTo → User (author)
```

---

## 🚀 Ordre de Développement Recommandé

1. **Phase 0** : Setup (1-2 jours)
2. **Phase 1** : Rôles (2-3 jours)
3. **Phase 2** : Crèches (4-5 jours)
4. **Phase 3** : Personnel (3-4 jours)
5. **Phase 4** : Enfants (4-5 jours)
6. **Phase 5** : Parents (3-4 jours)
7. **Phase 6** : Transmissions ⭐ (5-7 jours) - Core feature
8. **Phase 7** : Communication (4-5 jours)
9. **Phase 8** : Notifications (2-3 jours)
10. **Phase 9** : Dashboards (5-6 jours)
11. **Phase 10** : Sécurité & Performance (3-5 jours)
12. **Phase 11** : Mobile & UX (3-5 jours)

**Estimation totale : 39-54 jours de développement**

---

## 📝 Méthodologie TDD

Pour chaque feature :

1. **Réflexion** : Analyse des besoins et cas d'usage
2. **Migrations** : Structure de la base de données
3. **Factory & Seeders** : Données de test
4. **Tests** : Écriture des tests d'intégration (RED)
5. **Policies** : Règles d'autorisation (qui peut faire quoi)
6. **Form Requests** : Validation des données entrantes
7. **Controller** : Logique métier et réponses
8. **Routes** : Enregistrement des routes
9. **Views** : Interface utilisateur (Inertia/Vue)
10. **Refactoring** : Amélioration du code (REFACTOR)

**Ordre d'implémentation :**
Migration → Factory → Seeder → Tests → Policies → FormRequest → Controller → Routes → Views

---

## 📚 Exemple de Workflow Complet (Feature Daycare)

### 1. Migration
```php
Schema::create('daycares', function (Blueprint $table) {
    $table->id();
    $table->foreignId('director_id')->constrained('users')->onDelete('cascade');
    $table->string('name');
    $table->string('address');
    // ...
});
```

### 2. Factory
```php
Daycare::factory()->create(['director_id' => $director->id]);
```

### 3. Seeder
```php
Daycare::factory(10)->create();
```

### 4. Tests
```php
// DaycareStoreTest.php
it('allows director to create daycare', function () {
    actingAsDirector();
    $response = $this->post(route('daycares.store'), [...]);
    $response->assertRedirect();
});
```

### 5. Policy
```php
// DaycarePolicy.php
public function create(User $user): bool
{
    return $user->hasPermissionTo('daycare.manage');
}

public function update(User $user, Daycare $daycare): bool
{
    return $user->hasPermissionTo('daycare.manage') 
        && $user->id === $daycare->director_id;
}
```

### 6. Form Request
```php
// StoreDaycareRequest.php
public function rules(): array
{
    return [
        'name' => 'required|string|max:255',
        'address' => 'required|string',
        // ...
    ];
}

public function authorize(): bool
{
    return $this->user()->can('create', Daycare::class);
}
```

### 7. Controller
```php
public function store(StoreDaycareRequest $request)
{
    $daycare = Daycare::create([
        ...$request->validated(),
        'director_id' => $request->user()->id,
    ]);
    
    return redirect()->route('daycares.show', $daycare);
}
```

### 8. Routes
```php
Route::middleware(['auth', 'role:director'])->group(function () {
    Route::resource('daycares', DaycareController::class);
});
```

### 9. Views
```vue
<!-- DaycareForm.vue -->
<template>
  <form @submit.prevent="submit">
    <Input v-model="form.name" label="Nom" />
    <!-- ... -->
  </form>
</template>
```