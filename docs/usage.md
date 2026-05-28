# Utilisation

Ce document présente un exemple complet par méthode publique du client.

## Initialisation préalable

Tous les exemples ci-dessous supposent que le client est initialisé comme suit :

```php
<?php
require_once __DIR__ . '/vendor/autoload.php';

use AgoraLearningPhp\AgoraLearningClient;

$client = new AgoraLearningClient(
    'https://agora.example.com', // à adapter
    'votre_cle_api'              // à adapter
);
```

---

## Auth — Vérification de connexion

### `ping()`

Vérifie que l'API est joignable et que la clé est valide.

```php
$response = $client->ping();

if ($response->getStatusCode() === 200) {
    echo 'API joignable';
} else {
    echo 'Erreur HTTP : ' . $response->getStatusCode();
}
```

**Retourne :** `Symfony\Contracts\HttpClient\ResponseInterface`

---

## Person — Gestion des personnes

Une **Person** représente un individu et ses informations de contact, elle est la base des comptes apprenant et des comptes formateur.

### Énumérations disponibles

```php
use AgoraLearningPhp\Enum\Gender;

Gender::GENDER_NA;     // valeur par défaut
Gender::GENDER_FEMALE;
Gender::GENDER_MALE;
```

### `createPerson(PersonInput $input) : PersonOutput`

Crée une nouvelle personne à partir d'un objet PersonInput, retourne en cas de succès un objet PersonOutput

```php
use AgoraLearningPhp\DTO\Input\Person\PersonInput;
use AgoraLearningPhp\Enum\Gender;

$input = new PersonInput(
    'Dupont',                  // familyName (obligatoire)
    'Jean',                    // givenName (obligatoire)
    'jean.dupont@example.com', // email
    '0601020304',              // telephone
    '12 rue de la Paix',       // addressStreet
    '75001',                   // addressPostcode
    'Paris',                   // addressLocality
    'FR',                      // addressCountry
    'url/image.jpeg',          // image (URL)
    Gender::GENDER_MALE,       // gender
    new \DateTimeImmutable(),  // birthDate
    'Directeur commercial'     // jobTitle
);

$person = $client->createPerson($input);

echo $person->uuid;       // "per_01KRXPMF8FET29Q0YV9CYQ3Z8C"
echo $person->familyName; // "Dupont"
echo $person->givenName;  // "Jean"
```

**Champs de `PersonInput` :**

| Champ             | Type                | Obligatoire | Description                    |
| ----------------- | ------------------- | ----------- | ------------------------------ |
| `familyName`      | string              | Oui         | Nom de famille                 |
| `givenName`       | string              | Oui         | Prénom                         |
| `email`           | ?string             | Non         | Adresse e-mail                 |
| `telephone`       | ?string             | Non         | Numéro de téléphone            |
| `addressStreet`   | ?string             | Non         | Rue                            |
| `addressPostcode` | ?string             | Non         | Code postal                    |
| `addressLocality` | ?string             | Non         | Ville                          |
| `addressCountry`  | ?string             | Non         | Pays                           |
| `image`           | ?string             | Non         | URL d'avatar                   |
| `gender`          | string              | Non         | `Gender::GENDER_NA` par défaut |
| `birthDate`       | ?\DateTimeImmutable | Non         | Date de naissance              |
| `jobTitle`        | ?string             | Non         | Intitulé du poste              |

**Champs de `PersonOutput` :**

| Champ             | Type                | Description                    |
| ----------------- | ------------------- | ------------------------------ |
| `uuid`            | string              | Identifiant unique             |
| `familyName`      | string              | Nom de famille                 |
| `givenName`       | string              | Prénom                         |
| `email`           | ?string             | Adresse e-mail                 |
| `telephone`       | ?string             | Numéro de téléphone            |
| `addressStreet`   | ?string             | Rue                            |
| `addressPostcode` | ?string             | Code postal                    |
| `addressLocality` | ?string             | Ville                          |
| `addressCountry`  | ?string             | Pays                           |
| `image`           | ?string             | URL d'une image                |
| `gender`          | string              | `Gender::GENDER_NA` par défaut |
| `birthDate`       | ?\DateTimeImmutable | Date de naissance              |
| `jobTitle`        | ?string             | Intitulé du poste              |

### `getPerson(string $uuid) : PersonOutput`

Récupère une personne par son identifiant unique, retourne en cas de succès un objet PersonOutput

```php
$person = $client->getPerson('per_01KRXPMF8GENX8HCB8QKECBGZC');

echo $person->uuid;       // per_01KRXPMF8GENX8HCB8QKECBGZC
echo $person->familyName; // "Dupont"
echo $person->email;      // "jean.dupont@example.com"
```

### `getCollectionPerson() : array`

Récupère la liste de toutes les personnes.

```php
$persons = $client->getCollectionPerson();

foreach ($persons as $person) {
    // chaque élément est un PersonOutput
    echo $person->uuid . ' — ' . $person->familyName . ' ' . $person->givenName . PHP_EOL;
}
```

**Retourne :** `PersonOutput[]`

---

## Trainer — Gestion des formateurs

Un **Trainer** est un formateur rattaché à la plateforme. Il existe deux statuts :

- **Salarié (`TrainerEmployeeInput`)** : formateur employé par une société.
- **Indépendant (`TrainerFreeInput`)** : formateur auto-entrepreneur, avec des champs supplémentaires (SIRET, URSSAF, coûts horaires/journaliers, adresse de facturation).

### Énumérations disponibles

```php
use AgoraLearningPhp\Enum\TrainerStatut;

TrainerStatut::STATUT_EMPLOYE; // formateur salarié
TrainerStatut::STATUT_FREE;    // formateur indépendant
```

### `createTrainerEmployee(TrainerEmployeeInput $input) : TrainerOutput`

Crée un formateur salarié.

```php
use AgoraLearningPhp\DTO\Input\Trainer\TrainerEmployeeInput;
use AgoraLearningPhp\Enum\Gender;

$input = new TrainerEmployeeInput(
    'Bernard',                        // familyName (obligatoire)
    'Luc',                            // givenName (obligatoire)
    'luc.bernard@example.com',        // email (obligatoire)
    true,                             // visibleInformation
    true,                             // notifyUser
    Gender::GENDER_MALE,              // gender
    '0601020304',                     // telephone
    '0612131415',                     // mobileNumber
    '5 avenue des Formateurs',        // addressStreet
    '75015',                          // addressPostcode
    'Paris',                          // addressLocality
    'FR',                             // addressCountry
    'url/image.jpeg',                 // image (url)
    new \DateTimeImmutable(),         // birthDate (\DateTimeImmutable)
    'Formateur PHP',                  // jobTitle
    'soc_01KRXPMF93F039K58SC9QEZZNY', // societyId (UUID de la société employeur)
    'url/cv_file.pdf',                // cv (url)
    'url/degree_file.pdf',            // degree (url)
    'url/contract_file.pdf',          // contract (url)
    'Description du poste'            // jobDescription
);

$trainer = $client->createTrainerEmployee($input);

echo $trainer->uuid;
echo $trainer->familyName; // "Bernard"
```

**Champs de `TrainerEmployeeInput` :**

| Champ                | Type                | Obligatoire | Description                    |
| -------------------- | ------------------- | ----------- | ------------------------------ |
| `familyName`         | string              | Oui         | Nom de famille                 |
| `givenName`          | string              | Oui         | Prénom                         |
| `email`              | string              | Oui         | Adresse e-mail                 |
| `visibleInformation` | bool                | Oui         | `true` par défaut              |
| `notifyUser`         | bool                | Oui         | `true` par défaut              |
| `gender`             | string              | Oui         | `Gender::GENDER_NA` par défaut |
| `telephone`          | ?string             | Non         | Numéro de téléphone            |
| `mobileNumber`       | ?string             | Non         | Numéro de téléphone portable   |
| `addressStreet`      | ?string             | Non         | Rue                            |
| `addressPostcode`    | ?string             | Non         | Code Postale                   |
| `addressLocality`    | ?string             | Non         | Ville                          |
| `addressCountry`     | ?string             | Non         | Pays                           |
| `image`              | ?string             | Non         | URL d'avatar                   |
| `birthDate`          | ?\DateTimeImmutable | Non         | Date de naissance              |
| `jobTitle`           | ?string             | Non         | Nom du poste                   |
| `societyId`          | ?string             | Non         | UUID de la société employeur   |
| `cv`                 | ?string             | Non         | URL du CV                      |
| `degree`             | ?string             | Non         | URL des Diplômes               |
| `contract`           | ?string             | Non         | URL du contrat                 |
| `jobDescription`     | ?string             | Non         | Description du poste           |

### `createTrainerFree(TrainerFreeInput $input) : TrainerOutput`

Crée un formateur indépendant. Hérite de tous les champs de `TrainerEmployeeInput` et ajoute :

```php
use AgoraLearningPhp\DTO\Input\Trainer\TrainerFreeInput;

$input = new TrainerFreeInput(
    'Martin',                      // familyName (obligatoire)
    'Claire',                      // givenName (obligatoire)
    'claire.martin@freelance.com', // email (obligatoire)
    true,                          // visibleInformation
    false,                         // notifyUser
    // ... mêmes champs que TrainerEmployeeInput ...
    null, null, null, null, null, null, null, null, null, null, null, null, null,
    // ... Champs spécifiques à TrainerFreeInput ...
    45.0,                          // hourlyCost (coût horaire en €)
    320.0,                         // daylyCost (coût journalier en €)
    '12345678900012',              // siret
    'FR12345678900',               // tvaNumber
    '123456789',                   // urssafNumber
    'url/urssaf.pdf',              // urssafCertificate (URL)
    '10 rue du Commerce',          // billingAddressStreet
    '69002',                       // billingAddressPostcode
    'Lyon',                        // billingAddressLocality
    'FR'                           // billingAddressCountry
);

$trainer = $client->createTrainerFree($input);

echo $trainer->uuid;
```

**Champs supplémentaires de `TrainerFreeInput` :**

| Champ                    | Type    | Description                          |
| ------------------------ | ------- | ------------------------------------ |
| `hourlyCost`             | ?float  | Coût horaire en euros                |
| `daylyCost`              | ?float  | Coût journalier en euros             |
| `siret`                  | ?string | Numéro SIRET                         |
| `tvaNumber`              | ?string | Numéro de TVA intracommunautaire     |
| `urssafNumber`           | ?string | Numéro URSSAF                        |
| `urssafCertificate`      | ?string | URL du certificat URSSAF             |
| `billingAddressStreet`   | ?string | Rue - Adresse de facturation         |
| `billingAddressPostcode` | ?string | Code postal - Adresse de facturation |
| `billingAddressLocality` | ?string | Ville - Adresse de facturation       |
| `billingAddressCountry`  | ?string | Pays - Adresse de facturation        |

**Champs de `TrainerEmployeeOutput` :**

| Champ                        | Type                | Description                                         |
| ---------------------------- | ------------------- | --------------------------------------------------- |
| `uuid`                       | string              | Identifiant unique                                  |
| `familyName`                 | string              | Nom de famille                                      |
| `givenName`                  | string              | Prénom                                              |
| `email`                      | string              | Adresse e-mail                                      |
| `visibleInformation`         | bool                | Visibilité des informations sur l'espace apprenant  |
| `gender`                     | string              | Genre                                               |
| `educationalManagerSessions` | array               | Sessions dont le formateur est référent pédagogique |
| `telephone`                  | ?string             | Numéro de téléphone                                 |
| `mobileNumber`               | ?string             | Numéro de téléphone portable                        |
| `addressStreet`              | ?string             | Rue                                                 |
| `addressPostcode`            | ?string             | Code postale                                        |
| `addressLocality`            | ?string             | Ville                                               |
| `addressCountry`             | ?string             | Pays                                                |
| `image`                      | ?string             | URL de l'avatar                                     |
| `birthDate`                  | ?\DateTimeImmutable | Date de naissance                                   |
| `jobTitle`                   | ?string             | Nom du poste                                        |
| `society`                    | ?SocietyOutput      | Société employeur                                   |
| `cv`                         | ?string             | URL du CV                                           |
| `degree`                     | ?string             | URL des Diplômes                                    |
| `contract`                   | ?string             | URL du contrat                                      |
| `jobDescription`             | ?string             | Description du poste                                |

**Champs de `TrainerFreeOutput` :**

| Champ                        | Type                | Description                                         |
| ---------------------------- | ------------------- | --------------------------------------------------- |
| `uuid`                       | string              | Identifiant unique                                  |
| `familyName`                 | string              | Nom de famille                                      |
| `givenName`                  | string              | Prénom                                              |
| `email`                      | string              | Adresse e-mail                                      |
| `visibleInformation`         | bool                | Visibilité des informations sur l'espace apprenant  |
| `gender`                     | string              | Genre                                               |
| `educationalManagerSessions` | array               | Sessions dont le formateur est référent pédagogique |
| `telephone`                  | ?string             | Numéro de téléphone                                 |
| `mobileNumber`               | ?string             | Numéro de téléphone portable                        |
| `addressStreet`              | ?string             | Rue                                                 |
| `addressPostcode`            | ?string             | Code postale                                        |
| `addressLocality`            | ?string             | Ville                                               |
| `addressCountry`             | ?string             | Pays                                                |
| `image`                      | ?string             | URL de l'avatar                                     |
| `birthDate`                  | ?\DateTimeImmutable | Date de naissance                                   |
| `jobTitle`                   | ?string             | Nom du poste                                        |
| `society`                    | ?SocietyOutput      | Société employeur                                   |
| `cv`                         | ?string             | URL du CV                                           |
| `degree`                     | ?string             | URL des Diplômes                                    |
| `contract`                   | ?string             | URL du contrat                                      |
| `jobDescription`             | ?string             | Description du poste                                |
| `hourlyCost`                 | ?float              | Coût horaire en euros                               |
| `daylyCost`                  | ?float              | Coût journalier en euros                            |
| `siret`                      | ?string             | Numéro SIRET                                        |
| `tvaNumber`                  | ?string             | Numéro de TVA intracommunautaire                    |
| `urssafNumber`               | ?string             | Numéro URSSAF                                       |
| `urssafCertificate`          | ?string             | URL du certificat URSSAF                            |
| `billingAddressStreet`       | ?string             | Rue - Adresse de facturation                        |
| `billingAddressPostcode`     | ?string             | Code postal - Adresse de facturation                |
| `billingAddressLocality`     | ?string             | Ville - Adresse de facturation                      |
| `billingAddressCountry`      | ?string             | Pays - Adresse de facturation                       |

### `getTrainer(string $uuid) : TrainerOutput`

```php
$trainer = $client->getTrainer('per_01KRXPQB15F1RSTJHET0FAWGPT');

echo $trainer->familyName;
echo $trainer->email;
```

### `getCollectionTrainer() : array`

```php
$trainers = $client->getCollectionTrainer();

foreach ($trainers as $trainer) {
    // chaque élément est un TrainerOutput
    echo $trainer->uuid . ' — ' . $trainer->familyName . ' ' . $trainer->givenName . PHP_EOL;
}
```

**Retourne :** `TrainerOutput[]`

---

## Learner — Gestion des apprenants

Un **Learner** est un compte apprenant pouvant se connecter à l'espace apprenant.

### `createLearner(LearnerInput $input) : LearnerOutput`

Crée un nouvel apprenant.

```php
use AgoraLearningPhp\DTO\Input\Learner\LearnerInput;
use AgoraLearningPhp\Enum\Gender;

$input = new LearnerInput(
    'Martin',                    // familyName (obligatoire)
    'Sophie',                    // givenName (obligatoire)
    'sophie.martin@example.com', // recoverEmail (obligatoire) — e-mail de récupération
    Gender::GENDER_FEMALE,       // gender
    'sophie@pro.example.com',    // email (e-mail de contact)
    '0612131415',                // telephone
    '5 avenue des Fleurs',       // addressStreet
    '69001',                     // addressPostcode
    'Lyon',                      // addressLocality
    'FR',                        // addressCountry
    'url/image.jpeg',            // image
    new \DateTimeImmutable,      // birthDate
    'Responsable RH'             // jobTitle
);

$learner = $client->createLearner($input);

echo $learner->uuid;
```

**Champs de `LearnerInput` :**

| Champ             | Type                | Obligatoire | Description                      |
| ----------------- | ------------------- | ----------- | -------------------------------- |
| `familyName`      | string              | Oui         | Nom de famille                   |
| `givenName`       | string              | Oui         | Prénom                           |
| `recoverEmail`    | string              | Oui         | E-mail de récupération du compte |
| `gender`          | string              | Non         | `Gender::GENDER_NA` par défaut   |
| `email`           | ?string             | Non         | E-mail de contact                |
| `telephone`       | ?string             | Non         | Numéro de téléphone              |
| `addressStreet`   | ?string             | Non         | Rue                              |
| `addressPostcode` | ?string             | Non         | Code postal                      |
| `addressLocality` | ?string             | Non         | Ville                            |
| `addressCountry`  | ?string             | Non         | Pays                             |
| `image`           | ?string             | Non         | URL d'une image                  |
| `birthDate`       | ?\DateTimeImmutable | Non         | Date de naissance                |
| `jobTitle`        | ?string             | Non         | Intitulé du poste                |

### `getLearner(string $uuid) : LearnerOutput`

```php
$learner = $client->getLearner('per_01KRXPMF8GENX8HCB8QKECBGZC');

echo $learner->uuid;
```

### `getCollectionLearner() : array`

```php
$learners = $client->getCollectionLearner();

foreach ($learners as $learner) {
    // chaque élément est un LearnerOutput
    echo $learner->uuid . PHP_EOL;
}
```

**Retourne :** `LearnerOutput[]`

---

## Society — Gestion des sociétés

Une **Society** représente une entreprise cliente ou partenaire. Elle peut embarquer jusqu'à trois contacts (référent légal, référent administratif, référent RH), chacun modélisé par un `PersonInput`.

### `createSociety(SocietyInput $input) : SocietyOutput`

Crée une nouvelle société.

```php
use AgoraLearningPhp\DTO\Input\Society\SocietyInput;
use AgoraLearningPhp\DTO\Input\Person\PersonInput;
use AgoraLearningPhp\Enum\Gender;

// Contacts optionnels
$legalPerson = new PersonInput(
    'Legrand',
    'Pierre',
    'pierre.legrand@acme.example.com'
);

$legalPerson = new PersonInput(
    'Legrand',
    'Paul',
    'paul.legrand@acme.example.com'
);

$legalPerson = new PersonInput(
    'Legrand',
    'Jacques',
    'jacques.legrand@acme.example.com'
);

$input = new SocietyInput(
    'ACME Corp',                    // name (obligatoire)
    '12345678901234',               // siret
    'SAS',                          // legalStatus
    'contact@acme.example.com',     // email
    '0140506070',                   // telephone
    '1 place de la Bourse',         // addressStreetHeadOffice
    '75002',                        // addressPostcodeHeadOffice
    'Paris',                        // addressLocalityHeadOffice
    'FR',                           // addressCountryHeadOffice
    '1 place de la Bourse',         // addressStreetInvoicing
    '75002',                        // addressPostcodeInvoicing
    'Paris',                        // addressLocalityInvoicing
    'FR',                           // addressCountryInvoicing
    $legalPerson,                   // legalPerson
    $administrativePerson,          // administrativePerson
    $rhPerson,                      // rhPerson
    'url/image.jpeg',               // image
);

$society = $client->createSociety($input);

echo $society->uuid; // "soc_01KRXPMF93F039K58SC9QEZZNY"
echo $society->name; // "ACME Corp"
```

**Champs de `SocietyInput` :**

| Champ                       | Type         | Obligatoire | Description                      |
| --------------------------- | ------------ | ----------- | -------------------------------- |
| `name`                      | string       | Oui         | Raison sociale                   |
| `siret`                     | ?string      | Non         | Numéro SIRET                     |
| `legalStatus`               | ?string      | Non         | Forme juridique (ex : SAS, SARL) |
| `email`                     | ?string      | Non         | E-mail général                   |
| `telephone`                 | ?string      | Non         | Numéro de téléphone              |
| `addressStreetHeadOffice`   | ?string      | Non         | Adresse siège social             |
| `addressPostcodeHeadOffice` | ?string      | Non         | Code postale siège social        |
| `addressLocalityHeadOffice` | ?string      | Non         | Ville siège social               |
| `addressCountryHeadOffice`  | ?string      | Non         | Pays siège social                |
| `addressStreetInvoicing`    | ?string      | Non         | Adresse facturation              |
| `addressPostcodeInvoicing`  | ?string      | Non         | Code postale facturation         |
| `addressLocalityInvoicing`  | ?string      | Non         | Ville facturation                |
| `addressCountryInvoicing`   | ?string      | Non         | Pays facturation                 |
| `legalPerson`               | ?PersonInput | Non         | Référent légal                   |
| `administrativePerson`      | ?PersonInput | Non         | Référent administratif           |
| `rhPerson`                  | ?PersonInput | Non         | Référent RH                      |
| `image`                     | ?string      | Non         | URL du logo de la société        |

**Champs de `SocietyOutput` :**

| Champ                       | Type          | Description                      |
| --------------------------- | ------------- | -------------------------------- |
| `uuid`                      | string        | Identifiant unique               |
| `name`                      | string        | Raison sociale                   |
| `siret`                     | ?string       | Numéro SIRET                     |
| `legalStatus`               | ?string       | Forme juridique (ex : SAS, SARL) |
| `email`                     | ?string       | E-mail général                   |
| `telephone`                 | ?string       | Numéro de téléphone              |
| `addressStreetHeadOffice`   | ?string       | Adresse siège social             |
| `addressPostcodeHeadOffice` | ?string       | Code postale siège social        |
| `addressLocalityHeadOffice` | ?string       | Ville siège social               |
| `addressCountryHeadOffice`  | ?string       | Pays siège social                |
| `addressStreetInvoicing`    | ?string       | Adresse facturation              |
| `addressPostcodeInvoicing`  | ?string       | Code postale facturation         |
| `addressLocalityInvoicing`  | ?string       | Ville facturation                |
| `addressCountryInvoicing`   | ?string       | Pays facturation                 |
| `legalPerson`               | ?PersonOutput | Référent légal                   |
| `administrativePerson`      | ?PersonOutput | Référent administratif           |
| `rhPerson`                  | ?PersonOutput | Référent RH                      |
| `image`                     | ?string       | URL du logo de la société        |

### `getSociety(string $uuid) : SocietyOutput`

```php
$society = $client->getSociety('soc_01KRXPMF93F039K58SC9QEZZNY');

echo $society->name;
if ($society->legalPerson !== null) {
    echo $society->legalPerson->familyName;
}
```

### `getCollectionSociety() : array`

```php
$societies = $client->getCollectionSociety();

foreach ($societies as $society) {
    // chaque élément est un SocietyOutput
    echo $society->uuid . ' — ' . $society->name . PHP_EOL;
}
```

**Retourne :** `SocietyOutput[]`

---

## Session — Gestion des sessions de formation

Une **Session** représente une formation planifiée. Le client supporte deux types de sessions :

- **Session fixe (`FixedSessionInput`)** : dates de disponibilité précises, type et modalité configurables.
- **Session ouverte (`OpenedSessionInput`)** : fenêtre d'inscription avec durée d'accès en jours ; type et modalité sont fixés automatiquement à `INTER` / `E_LEARNING`.

### Énumérations disponibles

```php
use AgoraLearningPhp\Enum\SessionType;
use AgoraLearningPhp\Enum\SessionMode;

// Types de session (uniquement pour FixedSessionInput)
SessionType::SESSION_TYPE_INTER;  // inter-entreprise
SessionType::SESSION_TYPE_INTRA;  // intra-entreprise

// Modalités (uniquement pour FixedSessionInput)
SessionMode::SESSION_MODE_FACE_TO_FACE; // présentiel
SessionMode::SESSION_MODE_E_LEARNING;   // e-learning
SessionMode::SESSION_MODE_BLENDED;      // mixte
SessionMode::SESSION_MODE_DISTANCE;     // à distance
```

### `createSession(SessionInput $input) : SessionOutput` — Session fixe

```php
use AgoraLearningPhp\DTO\Input\Session\SessionInput;
use AgoraLearningPhp\DTO\Input\Session\FixedSessionInput;
use AgoraLearningPhp\Enum\SessionType;
use AgoraLearningPhp\Enum\SessionMode;

$sessionData = new FixedSessionInput(
    new \DateTimeImmutable('2026-06-01'),  // availabilityStartDate
    new \DateTimeImmutable('2026-06-30'),  // availabilityEndDate
    SessionType::SESSION_TYPE_INTER,       // type
    SessionMode::SESSION_MODE_FACE_TO_FACE // mode
);

$input = new SessionInput(
    'Formation PHP avancé',                    // title (obligatoire)
    $sessionData,                              // sessionData (obligatoire)
    false,                                     // manualDuration
    true,                                      // hasForum
    true,                                      // notifyMailForum
    490.0,                                     // price (en euros)
    21600,                                     // durationInSeconds (6 heures)
    'Formation intensive PHP',                 // description
    20,                                        // maxPlaces
    'mon-site.com/questionnaire_preliminaire', // urlPreTrainingSurvey
    'mon-site.com/questionnaire_a_chaud',      // urlOnTheSpotSurvey
    'mon-site.com/questionnaire_a_froid',      // urlDelayedSurvey
    'url/image.jpeg'                           // image
);

$session = $client->createSession($input);

echo $session->uuid;  // "ses_01KRXPMF96F9DVGQKHHPD386D7"
echo $session->title; // "Formation PHP avancé"
echo $session->type;  // "INTER"
echo $session->mode;  // "FACE_TO_FACE"

// Données spécifiques à la session fixe
use AgoraLearningPhp\DTO\Output\Session\FixedSessionOutput;
if ($session->sessionData instanceof FixedSessionOutput) {
    echo $session->sessionData->availabilityStartDate->format('Y-m-d');
    echo $session->sessionData->availabilityEndDate->format('Y-m-d');
}
```

### `createSession(SessionInput $input) : SessionOutput` — Session ouverte

```php
use AgoraLearningPhp\DTO\Input\Session\SessionInput;
use AgoraLearningPhp\DTO\Input\Session\OpenedSessionInput;

$sessionData = new OpenedSessionInput(
    new \DateTimeImmutable('2026-01-01'), // subscribeStartDate (Y-m-d)
    new \DateTimeImmutable('2026-12-31'), // subscribeEndDate (Y-m-d)
    90                                    // accessDurationDays — durée d'accès après invitation
);

$input = new SessionInput(
    'Parcours e-learning PHP',
    $sessionData
    // tous les autres paramètres sont optionnels
);

$session = $client->createSession($input);

echo $session->uuid; // "ses_01KRXPMF97F8BRPH7N7972QRYY"
echo $session->type; // "INTER" (fixé automatiquement)
echo $session->mode; // "E_LEARNING" (fixé automatiquement)

// Données spécifiques à la session ouverte
use AgoraLearningPhp\DTO\Output\Session\OpenedSessionOutput;
if ($session->sessionData instanceof OpenedSessionOutput) {
    echo $session->sessionData->subscribeStartDate->format('Y-m-d');
    echo $session->sessionData->subscribeEndDate->format('Y-m-d');
    echo $session->sessionData->accessDurationDays; // 90
}
```

**Champs de `SessionInput` :**

| Champ                  | Type                                    | Obligatoire | Description                     |
| ---------------------- | --------------------------------------- | ----------- | ------------------------------- |
| `title`                | string                                  | Oui         | Titre de la session             |
| `sessionData`          | `FixedSessionInput\|OpenedSessionInput` | Oui         | Données spécifiques au type     |
| `manualDuration`       | bool                                    | Non         | `false` par défaut              |
| `hasForum`             | bool                                    | Non         | `true` par défaut               |
| `notifyMailForum`      | bool                                    | Non         | `true` par défaut               |
| `price`                | float                                   | Non         | `0` par défaut                  |
| `durationInSeconds`    | int                                     | Non         | `0` par défaut                  |
| `description`          | ?string                                 | Non         | Description de la session       |
| `maxPlaces`            | ?int                                    | Non         | Nombre de places maximum        |
| `urlPreTrainingSurvey` | ?string                                 | Non         | URL questionnaire pré-formation |
| `urlOnTheSpotSurvey`   | ?string                                 | Non         | URL questionnaire à chaud       |
| `urlDelayedSurvey`     | ?string                                 | Non         | URL questionnaire à froid       |
| `image`                | ?string                                 | Non         | URL de l'image d'illustration   |

**Champs de `SessionOutput` :**

| Champ                  | Type                                      | Description                                           |
| ---------------------- | ----------------------------------------- | ----------------------------------------------------- |
| `uuid`                 | string                                    | Identifiant unique                                    |
| `title`                | string                                    | Titre de la session                                   |
| `type`                 | string                                    | `INTER` ou `INTRA`                                    |
| `mode`                 | string                                    | `FACE_TO_FACE`, `E_LEARNING`, `BLENDED` ou `DISTANCE` |
| `sessionData`          | `FixedSessionOutput\|OpenedSessionOutput` | Données spécifiques relatives aux dates et aux durées |
| `manualDuration`       | bool                                      | La durée affichée est elle celle saisie manuellement  |
| `hasForum`             | bool                                      | L'espace de discussion est il disponible              |
| `notifyMailForum`      | bool                                      | Recevoir une notification lors d'un nouveau message   |
| `price`                | float                                     | Prix (€)                                              |
| `duration`             | ?string                                   | Durée (jour)                                          |
| `description`          | ?string                                   | Description de la session                             |
| `maxPlaces`            | ?int                                      | Nombre de places maximum                              |
| `urlPreTrainingSurvey` | ?string                                   | URL questionnaire pré-formation                       |
| `urlOnTheSpotSurvey`   | ?string                                   | URL questionnaire à chaud                             |
| `urlDelayedSurvey`     | ?string                                   | URL questionnaire à froid                             |
| `image`                | ?string                                   | URL de l'image d'illustration                         |

### `getSession(string $uuid) : SessionOutput`

```php
$session = $client->getSession('ses_01KRXPMF97F8BRPH7N7972QRYY');

echo $session->title;
echo $session->price;
```

### `getCollectionSession() : array`

```php
$sessions = $client->getCollectionSession();

foreach ($sessions as $session) {
    // chaque élément est un SessionOutput
    echo $session->uuid . ' — ' . $session->title . PHP_EOL;
}
```

**Retourne :** `SessionOutput[]`

---

## Enrollment — Gestion des inscriptions

Une **Enrollment** représente l'inscription d'un apprenant à une session. Elle lie un `Learner` à une `Session`.

### `createEnrollment(EnrollmentInput $input) : EnrollmentOutput`

Inscrit un apprenant à une session.

```php
use AgoraLearningPhp\DTO\Input\Enrollment\EnrollmentInput;

$input = new EnrollmentInput(
    'ses_01KRXPMF97F8BRPH7N7972QRYY', // sessionUuid (obligatoire)
    'per_01KRXPMF8GENX8HCB8QKECBGZC', // learnerUuid (obligatoire)
    true                              // sendingInvite — envoie un e-mail d'invitation (dans le cadre d'une session open, l'invitation déclenche le début de la session)
);

$enrollment = $client->createEnrollment($input);

echo $enrollment->uuid;
echo $enrollment->session->title;
echo $enrollment->learner->familyName;
```

**Champs de `EnrollmentInput` :**

| Champ           | Type   | Obligatoire | Description                   |
| --------------- | ------ | ----------- | ----------------------------- |
| `sessionUuid`   | string | Oui         | UUID de la session            |
| `learnerUuid`   | string | Oui         | UUID de l'apprenant           |
| `sendingInvite` | bool   | Oui         | Envoie un e-mail d'invitation |

**Champs de `EnrollmentOutput` :**

| Champ                   | Type                | Description                                     |
| ----------------------- | ------------------- | ----------------------------------------------- |
| `uuid`                  | string              | Identifiant unique de l'inscription             |
| `learner`               | PersonOutput        | Apprenant inscrit                               |
| `session`               | SessionOutput       | Session concernée                               |
| `availabilityStartDate` | ?\DateTimeImmutable | Date de début de disponibilité pour l'apprenant |
| `availabilityEndDate`   | ?\DateTimeImmutable | Date de fin de disponibilité pour l'apprenant   |

### `getEnrollment(string $uuid) : EnrollmentOutput`

Récupère une inscription par son UUID.

```php
$enrollment = $client->getEnrollment('enr_01KRXPMF9DEXHVPG9B6WAGFX4K');

echo $enrollment->session->title;
echo $enrollment->learner->familyName;
```

### `getEnrollmentFromSessionAndLearner(string $sessionUuid, string $learnerUuid) : EnrollmentOutput`

Récupère l'inscription d'un apprenant dans une session donnée.

```php
$enrollment = $client->getEnrollmentFromSessionAndLearner(
    'ses_01KRXPMF97F8BRPH7N7972QRYY', // sessionUuid
    'per_01KRXPMF8GENX8HCB8QKECBGZC'  // learnerUuid
);

echo $enrollment->uuid;
```

### `getCollectionEnrollmentFromSession(string $sessionUuid) : array`

Récupère toutes les inscriptions d'une session.

```php
$enrollments = $client->getCollectionEnrollmentFromSession('ses_01KRXPMF97F8BRPH7N7972QRYY');

foreach ($enrollments as $enrollment) {
    // chaque élément est un EnrollmentOutput
    echo $enrollment->learner->familyName . ' ' . $enrollment->learner->givenName . PHP_EOL;
}
```

**Retourne :** `EnrollmentOutput[]`

### `getCollectionEnrollmentFromLearner(string $learnerUuid) : array`

Récupère toutes les inscriptions d'un apprenant.

```php
$enrollments = $client->getCollectionEnrollmentFromLearner('per_01KRXPMF8GENX8HCB8QKECBGZC');

foreach ($enrollments as $enrollment) {
    // chaque élément est un EnrollmentOutput
    echo $enrollment->session->title . PHP_EOL;
}
```

**Retourne :** `EnrollmentOutput[]`

---

## Gestion des erreurs

### Authentification échouée

Si la clé API est invalide ou si le serveur est inaccessible lors de l'échange du jeton :

```php
use AgoraLearningPhp\AgoraLearningClient;

try {
    $client = new AgoraLearningClient(
        'https://agora.example.com',
        'cle_invalide'
    );
    $client->getCollectionPerson(); // déclenche la demande de jeton
} catch (\RuntimeException $e) {
    // "Authentication failed (HTTP 401): unable to retrieve token."
    echo 'Erreur d\'authentification : ' . $e->getMessage();
}
```

### Erreur HTTP sur un appel métier

Les méthodes retournent un objet hydraté. Si l'API retourne une erreur (4xx, 5xx), le client Symfony HTTP lève une exception lors de l'appel à `getContent()` en interne. Il est recommandé d'encapsuler les appels dans un bloc `try/catch` :

```php
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

try {
    $person = $client->getPerson('uuid-inexistant');
} catch (ClientExceptionInterface $e) {
    // Erreur 4xx (ex : 404 Not Found, 422 Validation)
    echo 'Ressource non trouvée ou données invalides : ' . $e->getMessage();
} catch (ServerExceptionInterface $e) {
    // Erreur 5xx
    echo 'Erreur serveur : ' . $e->getMessage();
} catch (TransportExceptionInterface $e) {
    // Erreur réseau (timeout, DNS, etc.)
    echo 'Erreur réseau : ' . $e->getMessage();
} catch (\RuntimeException $e) {
    // Échec d'authentification
    echo 'Authentification échouée : ' . $e->getMessage();
}
```
