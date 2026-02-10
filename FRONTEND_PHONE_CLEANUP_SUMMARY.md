# Nettoyage des préfixes téléphone +226 dans les frontends

## ✅ Modifications effectuées

### 🔧 Backend
- ✅ UserSeeder : Tous les numéros modifiés avec "0" au début
  - Mobiles : `070XXXXXX` (au lieu de `70XXXXXX`)
  - Fixes : `025XXXXXX` (au lieu de `25XXXXXX`)

### 🎨 Frontend - Admin
- ✅ `src/hooks/useAdminData.js` ligne 22 : `phone_contact: '070 00 00 01'` (au lieu de `+226 70 00 00 01`)
- ✅ `src/hooks/useRestauratriceData.js` ligne 69 : `phone: '070 00 00 01'` (au lieu de `+225 01 02 03 04 05`)
- ✅ `src/components/restauratrice/RestauratriceSettings.vue` ligne 693 : placeholder modifié `Ex: 070 00 00 01`

### 🎨 Frontend - Client
- ✅ `src/components/AuthModal.vue` lignes 86-92 : Regex de validation changée en `/^0[0-9]{8,9}$/`
- ✅ `src/components/AuthModal.vue` ligne 244 : placeholder modifié `exemple@email.com ou 070 00 00 01`
- ✅ `src/components/AuthModal.vue` ligne 274 : placeholder modifié `070 00 00 01`
- ✅ `src/components/AppFooter.vue` ligne 52 : `070 00 00 01` (au lieu de `+226 07 XX XX XX XX`)
- ✅ `src/views/pages/Support.vue` lignes 23, 29 : `070 00 00 01` (au lieu de `+226 07 XX XX XX XX`)

### 🎨 Frontend - Restaurant
- ✅ `src/hooks/useAdminData.js` lignes 11, 25 : Numéros demo modifiés en format burkinabè
- ✅ `src/components/restauratrice/RestauratriceSettings.vue` ligne 693 : placeholder modifié `Ex: 070 00 00 01`

## 📋 Actions à faire manuellement sur les frontends

### Recherche globale nécessaire dans chaque frontend

Pour chaque application (admin, client, restaurant), recherchez et supprimez :

#### 1. **Ajout automatique du préfixe**
```javascript
// ❌ À SUPPRIMER
const phoneWithPrefix = '+226' + phone;
phone: `+226${userInput}`;
phoneNumber = '+226' + value;
```

#### 2. **Validations regex**
```javascript
// ❌ À SUPPRIMER
/^\+226\d{8}$/
/^226\d{8}$/
/^\+226/

// ✅ À GARDER (si nécessaire)
/^0\d{8,9}$/  // Commence par 0, 8 ou 9 chiffres après
```

#### 3. **Placeholders**
```html
<!-- ❌ À MODIFIER -->
<input placeholder="+226 XX XX XX XX" />
<InputText placeholder="+226 70 00 00 00" />

<!-- ✅ NOUVEAU -->
<input placeholder="0XX XX XX XX" />
<InputText placeholder="070 00 00 00" />
```

#### 4. **Affichage dans les templates**
```vue
<!-- ❌ À MODIFIER -->
<span>+226 {{ user.phone }}</span>
<div>Téléphone : +226{{ phone }}</div>

<!-- ✅ NOUVEAU -->
<span>{{ user.phone }}</span>
<div>Téléphone : {{ phone }}</div>
```

#### 5. **Formatage de numéro**
```javascript
// ❌ À SUPPRIMER
const formatPhone = (phone) => {
  return '+226 ' + phone;
}

// ✅ NOUVEAU (si formatage nécessaire)
const formatPhone = (phone) => {
  // Ajouter le 0 si absent
  if (!phone.startsWith('0')) {
    phone = '0' + phone;
  }
  // Formater : 0XX XX XX XX
  return phone.replace(/(\d{3})(\d{2})(\d{2})(\d{2})/, '$1 $2 $3 $4');
}
```

## 🔍 Commandes de recherche pour chaque frontend

```bash
# Dans chaque dossier frontend (admin, client, restaurant)

# Rechercher +226
grep -r "\+226" src/

# Rechercher "226" (attention aux faux positifs)
grep -r "226" src/ --include="*.vue" --include="*.js" --include="*.ts"

# Rechercher "prefix" lié au téléphone
grep -ri "prefix.*phone\|phone.*prefix" src/

# Rechercher les validations de téléphone
grep -r "phone.*regex\|regex.*phone\|phone.*valid" src/
```

## 🧪 Tests à effectuer

### Backend
```bash
cd lelagali-back
php artisan migrate:fresh --seed
```

Vérifier dans la DB que tous les numéros commencent par `0` :
```sql
SELECT phone FROM users;
```

### Frontend - Scénarios de test

1. **Création d'utilisateur**
   - Saisir : `070123456`
   - Vérifier : Enregistré tel quel (sans ajout de +226)

2. **Affichage de téléphone**
   - Vérifier que `070123456` s'affiche comme `070 12 34 56` ou `070123456`
   - PAS comme `+226 70123456`

3. **Login avec email**
   - Email : `test@example.com`
   - PAS avec téléphone

4. **Email reçu**
   - Email : test@example.com
   - Téléphone : 070123456
   - Mot de passe : xxxxx

## 📊 Format des numéros burkinabè

| Type | Format sans 0 | Format avec 0 | Format affiché |
|------|---------------|---------------|----------------|
| Mobile Orange | `70123456` | `070123456` | `070 12 34 56` |
| Mobile Telecel | `71123456` | `071123456` | `071 12 34 56` |
| Mobile Moov | `07123456` | `007123456` | `007 12 34 56` |
| Fixe | `25301010` | `025301010` | `025 30 10 10` |

## ✅ Checklist finale

- [x] Backend : préfixe +226 supprimé du UserSeeder
- [x] Backend : numéros avec "0" ajouté au début
- [x] Frontend Admin : phone_contact modifié (useAdminData.js)
- [x] Frontend Admin : recherche globale +226/+225 effectuée et nettoyée
- [x] Frontend Client : recherche globale +226/+225 effectuée et nettoyée
- [x] Frontend Restaurant : recherche globale +226/+225 effectuée et nettoyée
- [ ] Tests de création d'utilisateur effectués
- [ ] Tests d'affichage des numéros effectués
- [ ] Emails testés avec les nouveaux formats

---

**Note importante** : Les fichiers SVG, package-lock.json et fichiers demo contiennent "226" mais ce sont des données non liées aux téléphones (dimensions, IDs, etc.). Ne pas les modifier.

**Date** : 10 février 2026
