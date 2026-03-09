# Plan de restructuration des chatbots

## Problèmes identifiés

### Performance
- **1 requête DB par étape** (UPDATE à chaque persist) = 15-30 round-trips par soumission
- Bundle fait UPDATE + REFRESH = **2 requêtes par étape**
- `calculateStep()` itère TOUTES les étapes à chaque persist
- Requêtes VehicleBrand/VehicleModel rechargées sans cache
- Aucun `wire:loading` = zéro feedback visuel pendant les requêtes réseau
- Session driver = `database` (ajoute overhead à chaque requête)

### Sécurité
- `goToStep()` dans Auto/Home accepte N'IMPORTE QUEL string sans validation
- Aucun rate limiting sur les actions Livewire
- Pas de validation sur certains inputs (year, km, license_number dans Auto)

### Duplication de code
- `mount()` = ~95% identique dans les 3 composants
- `persist()` = identique dans Auto/Home
- `calculateStep()` = identique dans Auto/Home
- `finalize()` = ~95% identique dans les 3
- `fillPropertiesFromData()` = quasi-identique
- `goToStep()` = identique dans Auto/Home
- Total : **~60% du code est dupliqué**

### Bug
- `chat.years_old` manquant dans les 4 fichiers lang/*/chat.php

---

## Options de restructuration

### Option A : Corrections rapides seulement (1-2h)

**Quoi :**
- Ajouter `chat.years_old` dans les 4 traductions
- Ajouter `wire:loading` sur tous les boutons/formulaires
- Valider `goToStep()` (whitelist de steps valides)
- Ajouter validation manquante (year, km, license)

**Fichiers touchés :** ~12 fichiers (blades + PHP + traductions)
**Risque :** Très faible
**Gain performance :** Faible (juste UX améliorée avec loading states)
**Gain sécurité :** Moyen

---

### Option B : Trait partagé + Corrections (4-6h) ⭐ RECOMMANDÉ

**Quoi :**
- Tout de l'Option A
- Créer un trait `HasChatSteps` qui centralise :
  - `mountChat()` : init advisor, session, hydratation
  - `persistStep()` : sauvegarde avec batch optionnel
  - `calculateCurrentStep()` : logique de progression
  - `finalizeSubmission()` : envoi email + cleanup
  - `goToStep()` avec validation whitelist
- Supprimer le `->refresh()` dans Bundle (inutile)
- Cacher les VehicleBrand/Model (Cache::remember)
- Ajouter `wire:loading` partout
- Ajouter `wire:model.blur` au lieu de `.live` pour les inputs texte

**Fichiers touchés :** ~20 fichiers
- Nouveau : `app/Livewire/Concerns/HasChatSteps.php`
- Modifiés : 3 composants PHP + 6 blades + 4 traductions
**Risque :** Moyen (refactor interne, même API publique)
**Gain performance :** Bon (-30% requêtes DB, cache queries, meilleur UX)
**Gain sécurité :** Bon (validation centralisée, whitelist steps)

---

### Option C : Refactor complet vers le pattern Bundle (8-12h)

**Quoi :**
- Tout de l'Option B
- Étendre StepEngine/StepRegistry/StepValidation pour couvrir Auto et Home aussi
- Créer des DTOs : `QuoteAutoData`, `QuoteHomeData`
- 1 seul `QuoteChatEngine` composant abstrait
- Les 3 composants deviennent très légers (~50 lignes chacun, juste config)
- Blade templates refactorisés avec des partials partagés

**Fichiers touchés :** ~30+ fichiers
**Risque :** Élevé (rewrite majeur, testing extensif requis)
**Gain performance :** Très bon (architecture optimisée)
**Gain sécurité :** Très bon (tout centralisé)

---

### Option D : Redesign moderne complet (20-30h)

**Quoi :**
- Tout de l'Option C
- Alpine.js pour transitions/animations instantanées côté client
- Optimistic UI (affichage immédiat avant confirmation serveur)
- Batch persist (sauvegarder plusieurs champs en 1 seule requête DB)
- Session driver Redis au lieu de database
- Typing animation côté client (pas de round-trip serveur)

**Risque :** Très élevé (rewrite complet)
**Gain performance :** Maximum
**Gain sécurité :** Maximum

---

## Recommandation

**Option B** est le meilleur ratio effort/bénéfice :
- Corrige tous les bugs et problèmes de sécurité
- Réduit la duplication de ~60% à ~15%
- Améliore la performance de façon tangible
- Risque gérable car on garde la même structure de composants
- Facile à tester sur staging
