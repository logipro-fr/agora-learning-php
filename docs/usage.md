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

## Identifiants

Les méthodes de lecture unitaire (`getPerson()`, `getTrainer()`, `getLearner()`, `getSociety()`, `getSession()`, `getEnrollment()`) ainsi que les lectures d'inscriptions par session et/ou apprenant attendent un `ApiObjectIdentifier` :

```php
use AgoraLearningPhp\Identifier\AgoraId;
use AgoraLearningPhp\Identifier\ExternalId;

$client->getLearner(new AgoraId('per_01KRXPMF8GENX8HCB8QKECBGZC')); // GET /learners/per_01KRXPMF8GENX8HCB8QKECBGZC
$client->getLearner(new ExternalId('MonERP', 'L-42'));               // GET /learners/MonERP%3AL-42
```

> ⚠️ La lecture par `ExternalId` nécessite que le serveur Agora prenne en charge les identifiants `source:id` sur la route appelée.

| Classe          | Format `getId()` | Contraintes                                                                                         |
| --------------- | ---------------- | --------------------------------------------------------------------------------------------------- |
| `AgoraId`       | `per_…`, `ses_…` | non vide, ne contient jamais `:`                                                                    |
| `ExternalId`    | `source:id`      | `source` : `[A-Za-z0-9_-]{1,50}` ; `id` : non vide, `:` autorisé                                    |

Les deux classes appliquent `trim()` à leurs arguments et lèvent une `\InvalidArgumentException` si une contrainte n'est pas respectée. L'identifiant est encodé (`rawurlencode`) avant d'être placé dans l'URL.

Pour reconstruire un identifiant stocké sous sa forme `getId()` :

```php
use AgoraLearningPhp\Identifier\ApiObjectIdentifierFactory;

$identifier = ApiObjectIdentifierFactory::fromString('MonERP:S-42'); // ExternalId('MonERP', 'S-42')
$identifier = ApiObjectIdentifierFactory::fromString('ses_abc');     // AgoraId('ses_abc')
```

### Identifiant externe à la création

Chaque DTO de création (`PersonInput`, `TrainerEmployeeInput`, `TrainerFreeInput`, `LearnerInput`, `SocietyInput`, `SessionInput`, `EnrollmentInput`) prend en **premier argument obligatoire** un `ExternalId` : l'identifiant de l'objet dans votre système. Il est envoyé à l'API (`{"sourceName": ..., "id": ...}`) et permet ensuite de relire l'objet sans connaître son `AgoraId`. L'API répond `409` si cet identifiant externe existe déjà.

Les méthodes `create*()` ne renvoient que les identifiants de l'objet créé, sous la forme d'un `CreatedOutput` :

| Champ        | Type        | Description                              |
| ------------ | ----------- | ---------------------------------------- |
| `agoraId`    | AgoraId     | Identifiant Agora de l'objet créé        |
| `externalId` | ?ExternalId | Identifiant externe renvoyé par l'API    |

```php
$created = $client->createLearner($input);
$learner = $client->getLearner($created->agoraId); // relire l'objet complet si besoin
```

En lecture, chaque DTO de sortie expose `uuid` (l'`agoraId`) et `externalIdentifier` (`?ExternalId`).

### Modification : `replace*()` (PUT) et `patch*()` (PATCH)

Chaque ressource modifiable (Person, Learner, Trainer, Society, Session) propose deux méthodes de modification. Elles prennent en premier argument l'identifiant de l'objet à modifier (`AgoraId` ou `ExternalId`) et renvoient, comme les créations, un `CreatedOutput` (identifiants de l'objet modifié). Les inscriptions (Enrollment) ne sont pas modifiables.

| Méthode      | HTTP    | Argument                                     | Effet                                                                 |
| ------------ | ------- | -------------------------------------------- | --------------------------------------------------------------------- |
| `replace*()` | `PUT`   | le DTO de création (`PersonInput`, …)        | **Remplace** l'objet : un champ facultatif non renseigné est **vidé** |
| `patch*()`   | `PATCH` | un DTO de patch (`PersonPatchInput`, …)      | **Modifie** uniquement les champs renseignés                          |

Les DTO de patch se construisent vides puis se remplissent par des setters chaînables : un champ jamais renseigné n'est pas envoyé (donc inchangé), un champ renseigné à `null` est vidé. Les setters des champs obligatoires n'acceptent pas `null`. La requête est envoyée en `application/merge-patch+json`.

```php
use AgoraLearningPhp\DTO\Input\Person\PersonPatchInput;
use AgoraLearningPhp\Identifier\ExternalId;

$client->patchPerson(
    new ExternalId('MonERP', 'P-41'),
    (new PersonPatchInput())
        ->setTelephone('0102030405') // modifié
        ->setJobTitle(null)          // vidé
);                                   // tous les autres champs sont inchangés
```

À savoir :

- **Relations** (responsables d'une société, référent d'une session, société d'un formateur) : désignées par leur identifiant (`AgoraId::getId()` ou `ExternalId::getId()`). En `replace*()`, une relation non renseignée est retirée ; en `patch*()`, `null` la retire.
- **Fichiers** (image, CV, diplôme…) : URL publique téléchargée par Agora. Une valeur vide ou `null` **ne supprime pas** le fichier existant.
- **Sessions et formateurs** : la route dépend du type (`/sessions/fixed/{id}`, `/sessions/opened/{id}`, `/trainers/employee/{id}`, `/trainers/free/{id}`). Le type d'une session et le statut d'un formateur ne peuvent pas être changés : utiliser le DTO correspondant au type existant, sinon l'API répond `404`.

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

### `createPerson(PersonInput $input) : CreatedOutput`

Crée une nouvelle personne à partir d'un objet PersonInput, retourne en cas de succès un objet CreatedOutput contenant les identifiants de la personne créée (`agoraId` et `externalId`). Utilisez `getPerson()` pour récupérer la personne complète.

```php
use AgoraLearningPhp\DTO\Input\Person\PersonInput;
use AgoraLearningPhp\Enum\Gender;
use AgoraLearningPhp\Identifier\ExternalId;

$input = new PersonInput(
    new ExternalId('MonERP', 'P-41'),  // externalIdentifier (obligatoire)
    'Dupont',                  // familyName (obligatoire)
    'Jean',                    // givenName (obligatoire)
    'jean.dupont@example.com', // email (obligatoire)
    Gender::GENDER_MALE,       // gender
    '0601020304',              // telephone
    '12 rue de la Paix',       // addressStreet
    '75001',                   // addressPostcode
    'Paris',                   // addressLocality
    'FR',                      // addressCountry
    'url/image.jpeg',          // image (URL)
    new \DateTimeImmutable(),  // birthDate
    'Directeur commercial'     // jobTitle
);

$created = $client->createPerson($input);

echo $created->agoraId->getId();    // "per_01KRXPMF8FET29Q0YV9CYQ3Z8C"
echo $created->externalId->getId(); // "MonERP:P-41"

$person = $client->getPerson($created->agoraId);
echo $person->familyName; // "Dupont"
```

**Champs de `PersonInput` :**

| Champ             | Type                | Obligatoire | Description                    |
| ----------------- | ------------------- | ----------- | ------------------------------ |
| `externalIdentifier` | ExternalId | Oui         | Identifiant dans le système externe |
| `familyName`      | string              | Oui         | Nom de famille                 |
| `givenName`       | string              | Oui         | Prénom                         |
| `email`           | string              | Oui         | Adresse e-mail                 |
| `gender`          | string              | Non         | `Gender::GENDER_NA` par défaut |
| `telephone`       | ?string             | Non         | Numéro de téléphone            |
| `addressStreet`   | ?string             | Non         | Rue                            |
| `addressPostcode` | ?string             | Non         | Code postal                    |
| `addressLocality` | ?string             | Non         | Ville                          |
| `addressCountry`  | ?string             | Non         | Pays                           |
| `image`           | ?string             | Non         | URL d'avatar                   |
| `birthDate`       | ?\DateTimeImmutable | Non         | Date de naissance              |
| `jobTitle`        | ?string             | Non         | Intitulé du poste              |
| `mobileNumber`    | ?string             | Non         | Téléphone portable               |
| `societyId`       | ?string             | Non         | Société du contact : identifiant Agora ou `source:id` (dernier argument). En `replacePerson()`, `null` retire la relation |

**Champs de `PersonOutput` :**

| Champ             | Type                | Description                    |
| ----------------- | ------------------- | ------------------------------ |
| `uuid`            | string              | Identifiant unique             |
| `externalIdentifier` | ?ExternalId         | Identifiant dans le système externe |
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

### `replacePerson(ApiObjectIdentifier $identifier, PersonInput $input) : CreatedOutput`

Remplace la personne (`PUT /persons/{id}`). Le corps est le même que pour `createPerson()` : un champ facultatif non renseigné est vidé.

```php
$updated = $client->replacePerson(
    new ExternalId('MonERP', 'P-41'),
    new PersonInput(new ExternalId('MonERP', 'P-41'), 'Dupont', 'Jeanne', 'jeanne.dupont@example.com', Gender::GENDER_FEMALE)
);
echo $updated->agoraId->getId(); // "per_01KRXPMF8FET29Q0YV9CYQ3Z8C"
```

### `patchPerson(ApiObjectIdentifier $identifier, PersonPatchInput $patch) : CreatedOutput`

Modifie uniquement les champs renseignés (`PATCH /persons/{id}`).

```php
use AgoraLearningPhp\DTO\Input\Person\PersonPatchInput;

$client->patchPerson(
    new AgoraId('per_01KRXPMF8FET29Q0YV9CYQ3Z8C'),
    (new PersonPatchInput())->setTelephone('0102030405')->setJobTitle(null)
);
```

**Setters de `PersonPatchInput` :** `setExternalIdentifier(ExternalId)`, `setFamilyName(string)`, `setGivenName(string)`, `setEmail(string)`, `setGender(string)`, `setTelephone(?string)`, `setMobileNumber(?string)`, `setAddressStreet(?string)`, `setAddressPostcode(?string)`, `setAddressLocality(?string)`, `setAddressCountry(?string)`, `setImage(?string)`, `setBirthDate(?\DateTimeInterface)`, `setJobTitle(?string)`, `setSocietyId(?string)` (`null` retire la relation), `clearExternalIdentifier()` (détache la personne, voir `detachPerson()`).

### `getPerson(ApiObjectIdentifier $identifier) : PersonOutput`

Récupère une personne par son identifiant unique, retourne en cas de succès un objet PersonOutput

`$person->societyId` contient l'UUID Agora de la société du contact, ou `null`.

L'identifiant est un `ApiObjectIdentifier` : `AgoraId` ou `ExternalId` (voir [Identifiants](#identifiants)).

```php
use AgoraLearningPhp\Identifier\AgoraId;
use AgoraLearningPhp\Identifier\ExternalId;

$person = $client->getPerson(new AgoraId('per_01KRXPMF8GENX8HCB8QKECBGZC'));
$person = $client->getPerson(new ExternalId('MonERP', 'P-42'));

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

### `detachPerson(ApiObjectIdentifier $identifier) : void`

Détache la personne de l'application source en retirant son identifiant externe (`PATCH /persons/{id}`
avec `{"externalIdentifier": null}`, équivalent à `patchPerson()` avec `PersonPatchInput::clearExternalIdentifier()`) :
elle reste sur Agora mais n'est plus liée à l'application qui l'a créée. À appeler avant `deletePerson()`
lorsque l'application source supprime son objet. L'identifiant externe étant retiré, la personne n'est
ensuite plus désignable que par son UUID Agora (`AgoraId`).

```php
$client->detachPerson(new ExternalId('MonERP', 'P-42'));
```

**Lève :** `AgoraLearningClientException` de code 404 si la personne n'existe pas.

### `deletePerson(ApiObjectIdentifier $identifier) : void`

Supprime le contact (`DELETE /persons/{id}`, réponse 204 sans contenu).

```php
$client->deletePerson(new ExternalId('MonERP', 'P-42'));
```

**Lève :** `AgoraLearningClientException` de code 404 si le contact n'existe pas.

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

### `createTrainerEmployee(TrainerEmployeeInput $input) : CreatedOutput`

Crée un formateur salarié, retourne en cas de succès un objet CreatedOutput contenant les identifiants du formateur créé (`agoraId` et `externalId`). Utilisez `getTrainer()` pour récupérer le formateur complet.

```php
use AgoraLearningPhp\DTO\Input\Trainer\TrainerEmployeeInput;
use AgoraLearningPhp\Enum\Gender;
use AgoraLearningPhp\Identifier\ExternalId;

$input = new TrainerEmployeeInput(
    new ExternalId('MonERP', 'T-41'),  // externalIdentifier (obligatoire)
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
    'url/job_description.pdf'         // jobDescription (url)
);

$created = $client->createTrainerEmployee($input);

echo $created->agoraId->getId();    // "per_01KRXPMF8GENX8HCB8QKECBGZC"
echo $created->externalId->getId(); // "MonERP:T-41"

$trainer = $client->getTrainer($created->agoraId);
echo $trainer->familyName; // "Bernard"
```

**Champs de `TrainerEmployeeInput` :**

| Champ                | Type                | Obligatoire | Description                    |
| -------------------- | ------------------- | ----------- | ------------------------------ |
| `externalIdentifier` | ExternalId | Oui         | Identifiant dans le système externe |
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
| `jobDescription`     | ?string             | Non         | URL de la fiche de poste       |

### `createTrainerFree(TrainerFreeInput $input) : CreatedOutput`

Crée un formateur indépendant, retourne en cas de succès un objet CreatedOutput contenant les identifiants du formateur créé (`agoraId` et `externalId`). Utilisez `getTrainer()` pour récupérer le formateur complet. Hérite de tous les champs de `TrainerEmployeeInput` et ajoute :

```php
use AgoraLearningPhp\DTO\Input\Trainer\TrainerFreeInput;
use AgoraLearningPhp\Identifier\ExternalId;

$input = new TrainerFreeInput(
    new ExternalId('MonERP', 'T-42'), // externalIdentifier (obligatoire)
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

$created = $client->createTrainerFree($input);

echo $created->agoraId->getId();    // "per_01KRXPMF8GENX8HCB8QKECBGZD"
echo $created->externalId->getId(); // "MonERP:T-42"

$trainer = $client->getTrainer($created->agoraId);
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
| `externalIdentifier`         | ?ExternalId         | Identifiant dans le système externe                 |
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
| `jobDescription`             | ?string             | URL de la fiche de poste                            |

**Champs de `TrainerFreeOutput` :**

| Champ                        | Type                | Description                                         |
| ---------------------------- | ------------------- | --------------------------------------------------- |
| `uuid`                       | string              | Identifiant unique                                  |
| `externalIdentifier`         | ?ExternalId         | Identifiant dans le système externe                 |
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
| `jobDescription`             | ?string             | URL de la fiche de poste                            |
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

### `replaceTrainerEmployee(ApiObjectIdentifier $identifier, TrainerEmployeeInput $input) : CreatedOutput`
### `replaceTrainerFree(ApiObjectIdentifier $identifier, TrainerFreeInput $input) : CreatedOutput`

Remplace le formateur (`PUT /trainers/employee/{id}` ou `PUT /trainers/free/{id}`). Le corps est le même qu'à la création ; `notifyUser` est ignoré (pris en compte uniquement à la création). L'e-mail reste unique (`409` sinon).

```php
$client->replaceTrainerEmployee(
    new ExternalId('MonERP', 'T-41'),
    new TrainerEmployeeInput(new ExternalId('MonERP', 'T-41'), 'Martin', 'Sophie', 'sophie.martin@example.com', false)
);
```

### `patchTrainerEmployee(ApiObjectIdentifier $identifier, TrainerEmployeePatchInput $patch) : CreatedOutput`
### `patchTrainerFree(ApiObjectIdentifier $identifier, TrainerFreePatchInput $patch) : CreatedOutput`

Modifie uniquement les champs renseignés (`PATCH /trainers/employee/{id}` ou `PATCH /trainers/free/{id}`).

```php
use AgoraLearningPhp\DTO\Input\Trainer\TrainerFreePatchInput;

$client->patchTrainerFree(
    new ExternalId('MonERP', 'T-42'),
    (new TrainerFreePatchInput())->setHourlyCost(65.0)->setSocietyId(null)
);
```

**Setters de `TrainerEmployeePatchInput` :** `setExternalIdentifier(ExternalId)`, `setFamilyName(string)`, `setGivenName(string)`, `setEmail(string)`, `setGender(string)`, `setTelephone(?string)`, `setMobileNumber(?string)`, `setAddressStreet(?string)`, `setAddressPostcode(?string)`, `setAddressLocality(?string)`, `setAddressCountry(?string)`, `setImage(?string)`, `setBirthDate(?\DateTimeInterface)`, `setJobTitle(?string)`, `setVisibleInformation(bool)`, `setSocietyId(?string)`, `setCv(?string)`, `setDegree(?string)`, `setContract(?string)`, `setJobDescription(?string)`, `clearExternalIdentifier()` (détache le formateur, voir `detachTrainer()`).

**Setters supplémentaires de `TrainerFreePatchInput` :** `setHourlyCost(?float)`, `setDaylyCost(?float)`, `setSiret(?string)`, `setTvaNumber(?string)`, `setUrssafNumber(?string)`, `setUrssafCertificate(?string)`, `setBillingAddressStreet(?string)`, `setBillingAddressPostcode(?string)`, `setBillingAddressLocality(?string)`, `setBillingAddressCountry(?string)`.

### `getTrainer(ApiObjectIdentifier $identifier) : TrainerOutput`

```php
$trainer = $client->getTrainer(new AgoraId('per_01KRXPQB15F1RSTJHET0FAWGPT'));

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

### `detachTrainer(ApiObjectIdentifier $identifier) : void`

Détache le formateur de l'application source en retirant son identifiant externe : il reste sur Agora mais
n'est plus lié à l'application qui l'a créé. La route de modification dépendant du statut, le formateur est
d'abord lu (`GET /trainers/{id}`), puis modifié avec `{"externalIdentifier": null}` sur
`PATCH /trainers/employee/{id}` ou `PATCH /trainers/free/{id}` (équivalent à `patchTrainerEmployee()` /
`patchTrainerFree()` avec `clearExternalIdentifier()`). À appeler avant `deleteTrainer()` lorsque
l'application source supprime son objet. L'identifiant externe étant retiré, le formateur n'est ensuite plus
désignable que par son UUID Agora (`AgoraId`).

```php
$client->detachTrainer(new ExternalId('MonERP', 'T-42'));
```

**Lève :** `AgoraLearningClientException` de code 404 si le formateur n'existe pas.

### `deleteTrainer(ApiObjectIdentifier $identifier) : void`

Supprime le formateur (`DELETE /trainers/{id}`, réponse 204 sans contenu). Une seule méthode pour les deux statuts (employé et indépendant).

```php
$client->deleteTrainer(new ExternalId('MonERP', 'T-42'));
```

**Lève :** `AgoraLearningClientException` de code 404 si le formateur n'existe pas.

---

## Learner — Gestion des apprenants

Un **Learner** est un compte apprenant pouvant se connecter à l'espace apprenant.

### `createLearner(LearnerInput $input) : CreatedOutput`

Crée un nouvel apprenant, retourne en cas de succès un objet CreatedOutput contenant les identifiants de l'apprenant créé (`agoraId` et `externalId`). Utilisez `getLearner()` pour récupérer l'apprenant complet.

```php
use AgoraLearningPhp\DTO\Input\Learner\LearnerInput;
use AgoraLearningPhp\Enum\Gender;
use AgoraLearningPhp\Identifier\ExternalId;

$input = new LearnerInput(
    new ExternalId('MonERP', 'L-41'), // externalIdentifier (obligatoire)
    'Martin',                    // familyName (obligatoire)
    'Sophie',                    // givenName (obligatoire)
    'sophie.martin@example.com', // recoverEmail (obligatoire) — e-mail de récupération
    'sophie@pro.example.com',    // email (obligatoire) — e-mail de contact
    Gender::GENDER_FEMALE,       // gender
    '0612131415',                // telephone
    '5 avenue des Fleurs',       // addressStreet
    '69001',                     // addressPostcode
    'Lyon',                      // addressLocality
    'FR',                        // addressCountry
    'url/image.jpeg',            // image
    new \DateTimeImmutable,      // birthDate
    'Responsable RH'             // jobTitle
);

$created = $client->createLearner($input);

echo $created->agoraId->getId();    // "per_01KRXPMF8GENX8HCB8QKECBGZC"
echo $created->externalId->getId(); // "MonERP:L-41"

$learner = $client->getLearner($created->agoraId);
```

**Champs de `LearnerInput` :**

| Champ             | Type                | Obligatoire | Description                      |
| ----------------- | ------------------- | ----------- | -------------------------------- |
| `externalIdentifier` | ExternalId | Oui         | Identifiant dans le système externe |
| `familyName`      | string              | Oui         | Nom de famille                   |
| `givenName`       | string              | Oui         | Prénom                           |
| `recoverEmail`    | string              | Oui         | E-mail de récupération du compte |
| `email`           | string              | Oui         | E-mail de contact                |
| `gender`          | string              | Non         | `Gender::GENDER_NA` par défaut   |
| `telephone`       | ?string             | Non         | Numéro de téléphone              |
| `addressStreet`   | ?string             | Non         | Rue                              |
| `addressPostcode` | ?string             | Non         | Code postal                      |
| `addressLocality` | ?string             | Non         | Ville                            |
| `addressCountry`  | ?string             | Non         | Pays                             |
| `image`           | ?string             | Non         | URL d'une image                  |
| `birthDate`       | ?\DateTimeImmutable | Non         | Date de naissance                |
| `jobTitle`        | ?string             | Non         | Intitulé du poste                |
| `mobileNumber`    | ?string             | Non         | Téléphone portable               |
| `societyId`       | ?string             | Non         | Société de l'apprenant : identifiant Agora ou `source:id` (dernier argument). En `replaceLearner()`, `null` retire la relation |

### `replaceLearner(ApiObjectIdentifier $identifier, LearnerInput $input) : CreatedOutput`

Remplace l'apprenant (`PUT /learners/{id}`). L'identifiant de connexion et le statut du compte sont conservés.

```php
$client->replaceLearner(
    new ExternalId('MonERP', 'L-41'),
    new LearnerInput(new ExternalId('MonERP', 'L-41'), 'Lennon', 'John', 'john.recovery@example.com', 'john.lennon@example.com')
);
```

### `patchLearner(ApiObjectIdentifier $identifier, LearnerPatchInput $patch) : CreatedOutput`

Modifie uniquement les champs renseignés (`PATCH /learners/{id}`).

```php
use AgoraLearningPhp\DTO\Input\Learner\LearnerPatchInput;

$client->patchLearner(new ExternalId('MonERP', 'L-41'), (new LearnerPatchInput())->setAddressLocality('Liverpool'));
```

**Setters de `LearnerPatchInput` :** ceux de `PersonPatchInput`, plus `setRecoverEmail(string)` et `setSocietyId(?string)` (`null` retire la relation).

### `getLearner(ApiObjectIdentifier $identifier) : LearnerOutput`

```php
$learner = $client->getLearner(new AgoraId('per_01KRXPMF8GENX8HCB8QKECBGZC'));

echo $learner->uuid;
```

`$learner->society` contient la société de l'apprenant (`SocietyOutput`), ou `null`.

### `getCollectionLearner() : array`

```php
$learners = $client->getCollectionLearner();

foreach ($learners as $learner) {
    // chaque élément est un LearnerOutput
    echo $learner->uuid . PHP_EOL;
}
```

**Retourne :** `LearnerOutput[]`

### `deleteLearner(ApiObjectIdentifier $identifier) : void`

Supprime l'apprenant (`DELETE /learners/{id}`, réponse 204 sans contenu).

```php
$client->deleteLearner(new ExternalId('MonERP', 'L-42'));
```

**Lève :** `AgoraLearningClientException` de code 404 si l'apprenant n'existe pas.

---

## Society — Gestion des sociétés

Une **Society** représente une entreprise cliente ou partenaire. Elle peut référencer jusqu'à trois contacts **existants** (référent légal, référent administratif, référent RH), désignés par leur identifiant (`AgoraId` ou `ExternalId`, sous forme de chaîne `getId()`). Créer d'abord les contacts avec `createPerson()`.

### `createSociety(SocietyInput $input) : CreatedOutput`

Crée une nouvelle société, retourne en cas de succès un objet CreatedOutput contenant les identifiants de la société créée (`agoraId` et `externalId`). Utilisez `getSociety()` pour récupérer la société complète.

```php
use AgoraLearningPhp\DTO\Input\Society\SocietyInput;
use AgoraLearningPhp\DTO\Input\Person\PersonInput;
use AgoraLearningPhp\Enum\Gender;
use AgoraLearningPhp\Identifier\ExternalId;

// Contacts optionnels : des personnes existantes, désignées par leur identifiant
$legalPersonId = new ExternalId('MonERP', 'P-42');
$client->createPerson(new PersonInput($legalPersonId, 'Legrand', 'Pierre', 'pierre.legrand@acme.example.com', Gender::GENDER_MALE));

$input = new SocietyInput(
    new ExternalId('MonERP', 'SOC-41'), // externalIdentifier (obligatoire)
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
    $legalPersonId->getId(),        // legalPersonId ("MonERP:P-42")
    'per_01KRXPMF8GENX8HCB8QKECBGZC', // administrativePersonId (AgoraId d'un contact existant)
    null,                           // rhPersonId
    'url/image.jpeg',               // image
);

$created = $client->createSociety($input);

echo $created->agoraId->getId();    // "soc_01KRXPMF93F039K58SC9QEZZNY"
echo $created->externalId->getId(); // "MonERP:SOC-41"

$society = $client->getSociety($created->agoraId);
echo $society->name; // "ACME Corp"
```

**Champs de `SocietyInput` :**

| Champ                       | Type         | Obligatoire | Description                      |
| --------------------------- | ------------ | ----------- | -------------------------------- |
| `externalIdentifier`        | ExternalId | Oui         | Identifiant dans le système externe (envoyé sous la clé `externalId`) |
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
| `legalPersonId`             | ?string      | Non         | Identifiant du référent légal    |
| `administrativePersonId`    | ?string      | Non         | Identifiant du référent administratif |
| `rhPersonId`                | ?string      | Non         | Identifiant du référent RH       |
| `image`                     | ?string      | Non         | URL du logo de la société        |

**Champs de `SocietyOutput` :**

| Champ                       | Type          | Description                      |
| --------------------------- | ------------- | -------------------------------- |
| `uuid`                      | string        | Identifiant unique               |
| `externalIdentifier`        | ?ExternalId   | Identifiant dans le système externe |
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

### `replaceSociety(ApiObjectIdentifier $identifier, SocietyInput $input) : CreatedOutput`

Remplace la société (`PUT /societies/{id}`) : un champ facultatif non renseigné est vidé et un référent non renseigné est retiré.

```php
$client->replaceSociety(
    new ExternalId('MonERP', 'SOC-41'),
    new SocietyInput(new ExternalId('MonERP', 'SOC-41'), 'ACME SA', '12345678901234', 'SA')
);
```

### `patchSociety(ApiObjectIdentifier $identifier, SocietyPatchInput $patch) : CreatedOutput`

Modifie uniquement les champs renseignés (`PATCH /societies/{id}`).

```php
use AgoraLearningPhp\DTO\Input\Society\SocietyPatchInput;

// Retirer le référent RH et changer le téléphone
$client->patchSociety(
    new AgoraId('soc_01KRXPMF93F039K58SC9QEZZNY'),
    (new SocietyPatchInput())->setRhPersonId(null)->setTelephone('0140506071')
);
```

**Setters de `SocietyPatchInput` :** `setExternalIdentifier(ExternalId)`, `setName(string)`, `setSiret(?string)`, `setLegalStatus(?string)`, `setEmail(?string)`, `setTelephone(?string)`, `setAddressStreetHeadOffice(?string)`, `setAddressPostcodeHeadOffice(?string)`, `setAddressLocalityHeadOffice(?string)`, `setAddressCountryHeadOffice(?string)`, `setAddressStreetInvoicing(?string)`, `setAddressPostcodeInvoicing(?string)`, `setAddressLocalityInvoicing(?string)`, `setAddressCountryInvoicing(?string)`, `setLegalPersonId(?string)`, `setAdministrativePersonId(?string)`, `setRhPersonId(?string)`, `setImage(?string)`.

### `getSociety(ApiObjectIdentifier $identifier) : SocietyOutput`

```php
$society = $client->getSociety(new AgoraId('soc_01KRXPMF93F039K58SC9QEZZNY'));

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

### `detachSociety(ApiObjectIdentifier $identifier) : void`

Détache la société de l'application source en retirant son identifiant externe (`PATCH /societies/{id}`
avec `{"externalId": null}`, équivalent à `patchSociety()` avec `SocietyPatchInput::clearExternalIdentifier()`) :
elle reste sur Agora mais n'est plus liée à l'application qui l'a créée. À appeler avant `deleteSociety()`
lorsque l'application source supprime son objet. L'identifiant externe étant retiré, la société n'est
ensuite plus désignable que par son UUID Agora (`AgoraId`).

```php
$client->detachSociety(new ExternalId('MonERP', 'SOC-42'));
```

**Lève :** `AgoraLearningClientException` de code 404 si la société n'existe pas.

### `deleteSociety(ApiObjectIdentifier $identifier) : void`

Supprime la société (`DELETE /societies/{id}`, réponse 204 sans contenu).

```php
$client->deleteSociety(new ExternalId('MonERP', 'SOC-42'));
```

**Lève :** `AgoraLearningClientException` de code 404 si la société n'existe pas.

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

### `createSession(SessionInput $input) : CreatedOutput` — Session fixe

Crée une session à partir d'un objet SessionInput, retourne en cas de succès un objet CreatedOutput contenant les identifiants de la session créée (`agoraId` et `externalId`). Utilisez `getSession()` pour récupérer la session complète.

```php
use AgoraLearningPhp\DTO\Input\Session\SessionInput;
use AgoraLearningPhp\DTO\Input\Session\FixedSessionInput;
use AgoraLearningPhp\Enum\SessionType;
use AgoraLearningPhp\Enum\SessionMode;
use AgoraLearningPhp\Identifier\ExternalId;

$sessionData = new FixedSessionInput(
    new \DateTimeImmutable('2026-06-01'),  // availabilityStartDate
    new \DateTimeImmutable('2026-06-30'),  // availabilityEndDate
    SessionType::SESSION_TYPE_INTER,       // type
    SessionMode::SESSION_MODE_FACE_TO_FACE // mode
);

$input = new SessionInput(
    new ExternalId('MonERP', 'S-41'),          // externalIdentifier (obligatoire)
    'Formation PHP avancé',                    // title (obligatoire)
    $sessionData,                              // sessionData (obligatoire)
    false,                                     // manualDuration
    true,                                      // hasForum
    true,                                      // notifyMailForum
    490.0,                                     // price (en euros)
    21600,                                     // durationInSeconds (6 heures)
    'Formation intensive PHP',                 // description
    20,                                        // maxPlaces
    null,                                      // pedagogicalTrainer (UUID du référent pédagogique)
    null,                                      // administrativePerson (UUID du référent administratif)
    'mon-site.com/questionnaire_preliminaire', // urlPreTrainingSurvey
    'mon-site.com/questionnaire_a_chaud',      // urlOnTheSpotSurvey
    'mon-site.com/questionnaire_a_froid',      // urlDelayedSurvey
    'url/image.jpeg'                           // image
);

$created = $client->createSession($input);
echo $created->agoraId->getId();    // "ses_01KRXPMF96F9DVGQKHHPD386D7"
echo $created->externalId->getId(); // "MonERP:S-41"

// Récupérer la session complète
$session = $client->getSession($created->agoraId);
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

### `createSession(SessionInput $input) : CreatedOutput` — Session ouverte

```php
use AgoraLearningPhp\DTO\Input\Session\SessionInput;
use AgoraLearningPhp\DTO\Input\Session\OpenedSessionInput;
use AgoraLearningPhp\Identifier\ExternalId;

$sessionData = new OpenedSessionInput(
    new \DateTimeImmutable('2026-01-01'), // subscribeStartDate (Y-m-d)
    new \DateTimeImmutable('2026-12-31'), // subscribeEndDate (Y-m-d)
    90                                    // accessDurationDays — durée d'accès après invitation
);

$input = new SessionInput(
    new ExternalId('MonERP', 'S-42'), // externalIdentifier (obligatoire)
    'Parcours e-learning PHP',
    $sessionData
    // tous les autres paramètres sont optionnels
);

$created = $client->createSession($input);
echo $created->agoraId->getId(); // "ses_01KRXPMF97F8BRPH7N7972QRYY"

// Récupérer la session complète
$session = $client->getSession($created->agoraId);
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
| `externalIdentifier`   | ExternalId | Oui         | Identifiant dans le système externe |
| `title`                | string                                  | Oui         | Titre de la session             |
| `sessionData`          | `FixedSessionInput\|OpenedSessionInput` | Oui         | Données spécifiques au type     |
| `manualDuration`       | bool                                    | Non         | `false` par défaut              |
| `hasForum`             | bool                                    | Non         | `true` par défaut               |
| `notifyMailForum`      | bool                                    | Non         | `true` par défaut               |
| `price`                | float                                   | Non         | `0` par défaut                  |
| `durationInSeconds`    | int                                     | Non         | `0` par défaut                  |
| `description`          | ?string                                 | Non         | Description de la session       |
| `maxPlaces`            | ?int                                    | Non         | Nombre de places maximum        |
| `pedagogicalTrainer`   | ?string                                 | Non         | UUID du référent pédagogique    |
| `administrativePerson` | ?string                                 | Non         | UUID du référent administratif  |
| `urlPreTrainingSurvey` | ?string                                 | Non         | URL questionnaire pré-formation |
| `urlOnTheSpotSurvey`   | ?string                                 | Non         | URL questionnaire à chaud       |
| `urlDelayedSurvey`     | ?string                                 | Non         | URL questionnaire à froid       |
| `image`                | ?string                                 | Non         | URL de l'image d'illustration   |

**Champs de `SessionOutput` :**

| Champ                  | Type                                      | Description                                           |
| ---------------------- | ----------------------------------------- | ----------------------------------------------------- |
| `uuid`                 | string                                    | Identifiant unique                                    |
| `externalIdentifier`   | ?ExternalId                               | Identifiant dans le système externe                   |
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

### `replaceSession(ApiObjectIdentifier $identifier, SessionInput $input) : CreatedOutput`

Remplace la session (`PUT /sessions/fixed/{id}` ou `PUT /sessions/opened/{id}`, selon le type de `$input->sessionData`). Le type doit être celui de la session existante (sinon `404`) ; un champ facultatif non renseigné est vidé.

```php
$client->replaceSession(
    new ExternalId('MonERP', 'S-41'),
    new SessionInput(
        new ExternalId('MonERP', 'S-41'),
        'Formation PHP avancée',
        new FixedSessionInput(
            new \DateTimeImmutable('2026-03-02T09:00:00+01:00'),
            new \DateTimeImmutable('2026-03-04T17:00:00+01:00'),
            SessionType::SESSION_TYPE_INTER,
            SessionMode::SESSION_MODE_BLENDED
        )
    )
);
```

### `patchSession(ApiObjectIdentifier $identifier, AbstractSessionPatchInput $patch) : CreatedOutput`

Modifie uniquement les champs renseignés. La route dépend de la classe du patch : `FixedSessionPatchInput` → `PATCH /sessions/fixed/{id}`, `OpenedSessionPatchInput` → `PATCH /sessions/opened/{id}`. La cohérence des dates est vérifiée avec les valeurs déjà enregistrées (`422` si la fin précède le début).

```php
use AgoraLearningPhp\DTO\Input\Session\FixedSessionPatchInput;
use AgoraLearningPhp\DTO\Input\Session\OpenedSessionPatchInput;

// Décaler les dates d'une session à dates fixes
$client->patchSession(
    new ExternalId('MonERP', 'S-41'),
    (new FixedSessionPatchInput())
        ->setAvailabilityStartDate(new \DateTimeImmutable('2026-03-02T09:00:00+01:00'))
        ->setAvailabilityEndDate(new \DateTimeImmutable('2026-03-04T17:00:00+01:00'))
);

// Allonger la durée d'accès d'une session ouverte
$client->patchSession(new AgoraId('ses_01KRXPMF9AE5HBH7JY2N8Q2PQB'), (new OpenedSessionPatchInput())->setAccessDurationDays(60));
```

**Setters communs :** `setExternalIdentifier(ExternalId)`, `setTitle(string)`, `setDescription(?string)`, `setDurationInSeconds(int)`, `setManualDuration(bool)`, `setPrice(float)`, `setMaxPlaces(?int)` (`null` = illimité), `setHasForum(bool)`, `setNotifyMailForum(bool)`, `setPedagogicalTrainer(?string)`, `setAdministrativePerson(?string)`, `setUrlPreTrainingSurvey(?string)`, `setUrlOnTheSpotSurvey(?string)`, `setUrlDelayedSurvey(?string)`, `setImage(?string)`, `clearExternalIdentifier()` (détache la session, voir `detachSession()`).

**`FixedSessionPatchInput` :** `setType(string)`, `setMode(string)`, `setAvailabilityStartDate(\DateTimeInterface)`, `setAvailabilityEndDate(\DateTimeInterface)`.

**`OpenedSessionPatchInput` :** `setSubscribeStartDate(\DateTimeInterface)`, `setSubscribeEndDate(\DateTimeInterface)`, `setAccessDurationDays(int)`.

### `getSession(ApiObjectIdentifier $identifier) : SessionOutput`

Une session peut être lue par son identifiant Agora ou par l'identifiant qu'elle porte dans un système externe (voir [Identifiants](#identifiants)).

```php
use AgoraLearningPhp\Identifier\AgoraId;
use AgoraLearningPhp\Identifier\ExternalId;

// Identifiant Agora
$session = $client->getSession(new AgoraId('ses_01KRXPMF97F8BRPH7N7972QRYY'));

// Identifiant externe : appelle GET /sessions/MonERP%3AS-42
$session = $client->getSession(new ExternalId('MonERP', 'S-42'));

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

### `detachSession(ApiObjectIdentifier $identifier) : void`

Détache la session de l'application source en retirant son identifiant externe : elle reste sur Agora mais
n'est plus liée à l'application qui l'a créée. La route de modification dépendant du type, la session est
d'abord lue (`GET /sessions/{id}`), puis modifiée avec `{"externalIdentifier": null}` sur
`PATCH /sessions/fixed/{id}` ou `PATCH /sessions/opened/{id}` (équivalent à `patchSession()` avec
`clearExternalIdentifier()`). L'identifiant externe étant retiré, la session n'est ensuite plus désignable que
par son UUID Agora (`AgoraId`).

```php
$client->detachSession(new ExternalId('MonERP', 'S-42'));
```

**Lève :** `AgoraLearningClientException` de code 404 si la session n'existe pas.

### `deleteSession(ApiObjectIdentifier $identifier) : void`

Supprime la session (`DELETE /sessions/{id}`, réponse 204 sans contenu). Une seule méthode pour les deux types (fixe et ouverte).

```php
$client->deleteSession(new ExternalId('MonERP', 'S-42'));
```

**Lève :** `AgoraLearningClientException` de code 404 si la session n'existe pas.

---

## Enrollment — Gestion des inscriptions

Une **Enrollment** représente l'inscription d'un apprenant à une session. Elle lie un `Learner` à une `Session`.

### `createEnrollment(EnrollmentInput $input) : CreatedOutput`

Inscrit un apprenant à une session, retourne en cas de succès un objet CreatedOutput contenant les identifiants de l'inscription créée (`agoraId` et `externalId`). Utilisez `getEnrollment()` pour récupérer l'inscription complète.

```php
use AgoraLearningPhp\DTO\Input\Enrollment\EnrollmentInput;
use AgoraLearningPhp\Identifier\ExternalId;

$input = new EnrollmentInput(
    new ExternalId('MonERP', 'E-41'),  // externalIdentifier (obligatoire)
    'ses_01KRXPMF97F8BRPH7N7972QRYY', // sessionUuid (obligatoire)
    'per_01KRXPMF8GENX8HCB8QKECBGZC', // learnerUuid (obligatoire)
    true                              // sendingInvite — envoie un e-mail d'invitation (dans le cadre d'une session open, l'invitation déclenche le début de la session)
);

$created = $client->createEnrollment($input);

echo $created->agoraId->getId();    // "enr_01KRXPMF9AF2Y5ZT0B3R1N6QXW"
echo $created->externalId->getId(); // "MonERP:E-41"

$enrollment = $client->getEnrollment($created->agoraId);
echo $enrollment->session->title;
echo $enrollment->learner->familyName;
```

**Champs de `EnrollmentInput` :**

| Champ           | Type   | Obligatoire | Description                   |
| --------------- | ------ | ----------- | ----------------------------- |
| `externalIdentifier` | ExternalId | Oui    | Identifiant dans le système externe |
| `sessionUuid`   | string | Oui         | UUID de la session            |
| `learnerUuid`   | string | Oui         | UUID de l'apprenant           |
| `sendingInvite` | bool   | Oui         | Envoie un e-mail d'invitation |

**Champs de `EnrollmentOutput` :**

| Champ                   | Type                | Description                                     |
| ----------------------- | ------------------- | ----------------------------------------------- |
| `uuid`                  | string              | Identifiant unique de l'inscription             |
| `externalIdentifier`    | ?ExternalId         | Identifiant dans le système externe             |
| `learner`               | PersonOutput        | Apprenant inscrit                               |
| `session`               | SessionOutput       | Session concernée                               |
| `availabilityStartDate` | ?\DateTimeImmutable | Date de début de disponibilité pour l'apprenant |
| `availabilityEndDate`   | ?\DateTimeImmutable | Date de fin de disponibilité pour l'apprenant   |

### `getEnrollment(ApiObjectIdentifier $identifier) : EnrollmentOutput`

Récupère une inscription par son identifiant.

```php
$enrollment = $client->getEnrollment(new AgoraId('enr_01KRXPMF9DEXHVPG9B6WAGFX4K'));

echo $enrollment->session->title;
echo $enrollment->learner->familyName;
```

### `getEnrollmentFromSessionAndLearner(ApiObjectIdentifier $sessionIdentifier, ApiObjectIdentifier $learnerIdentifier) : EnrollmentOutput`

Récupère l'inscription d'un apprenant dans une session donnée.

```php
$enrollment = $client->getEnrollmentFromSessionAndLearner(
    new AgoraId('ses_01KRXPMF97F8BRPH7N7972QRYY'), // session
    new ExternalId('MonERP', 'L-42')               // apprenant
);

echo $enrollment->uuid;
```

### `getCollectionEnrollmentFromSession(ApiObjectIdentifier $sessionIdentifier) : array`

Récupère toutes les inscriptions d'une session.

```php
$enrollments = $client->getCollectionEnrollmentFromSession(new AgoraId('ses_01KRXPMF97F8BRPH7N7972QRYY'));

foreach ($enrollments as $enrollment) {
    // chaque élément est un EnrollmentOutput
    echo $enrollment->learner->familyName . ' ' . $enrollment->learner->givenName . PHP_EOL;
}
```

**Retourne :** `EnrollmentOutput[]`

### `getCollectionEnrollmentFromLearner(ApiObjectIdentifier $learnerIdentifier) : array`

Récupère toutes les inscriptions d'un apprenant.

```php
$enrollments = $client->getCollectionEnrollmentFromLearner(new AgoraId('per_01KRXPMF8GENX8HCB8QKECBGZC'));

foreach ($enrollments as $enrollment) {
    // chaque élément est un EnrollmentOutput
    echo $enrollment->session->title . PHP_EOL;
}
```

**Retourne :** `EnrollmentOutput[]`

### `deleteEnrollment(ApiObjectIdentifier $identifier) : void`

Désinscrit l'apprenant de la session (`DELETE /enrollments/{id}`, réponse 204 sans contenu). Agora applique
les mêmes traitements qu'une désinscription depuis son back-office.

```php
$client->deleteEnrollment(new ExternalId('MonERP', 'E-42'));
```

**Lève :** `AgoraLearningClientException` de code 404 si l'inscription n'existe pas (déjà supprimée par exemple).

### `deleteEnrollmentFromSessionAndLearner(ApiObjectIdentifier $sessionIdentifier, ApiObjectIdentifier $learnerIdentifier) : void`

Désinscrit l'apprenant de la session à partir de leurs identifiants (`DELETE /sessions/{s}/learners/{l}/enrollments`, réponse 204).

```php
$client->deleteEnrollmentFromSessionAndLearner(
    new ExternalId('MonERP', 'S-42'),
    new ExternalId('MonERP', 'L-42')
);
```

**Lève :** `AgoraLearningClientException` de code 404 si l'inscription n'existe pas.

### `deleteCollectionEnrollmentFromSession(ApiObjectIdentifier $sessionIdentifier) : void`

Supprime toutes les inscriptions de la session (`DELETE /sessions/{s}/enrollments`, réponse 204).

```php
$client->deleteCollectionEnrollmentFromSession(new ExternalId('MonERP', 'S-42'));
```

**Lève :** `AgoraLearningClientException` de code 404 si la session n'existe pas.

### `deleteCollectionEnrollmentFromLearner(ApiObjectIdentifier $learnerIdentifier) : void`

Supprime toutes les inscriptions de l'apprenant (`DELETE /learners/{l}/enrollments`, réponse 204).

```php
$client->deleteCollectionEnrollmentFromLearner(new ExternalId('MonERP', 'L-42'));
```

**Lève :** `AgoraLearningClientException` de code 404 si l'apprenant n'existe pas.

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

Si l'API retourne un statut hors 2xx, le client lève une `AgoraLearningClientException`. Son message reprend le champ `detail` de la réponse, et `getCode()` renvoie le statut HTTP :

```php
use AgoraLearningPhp\Exception\AgoraLearningClientException;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

try {
    $person = $client->getPerson(new AgoraId('uuid-inexistant'));
} catch (AgoraLearningClientException $e) {
    if ($e->getCode() === 404) {
        echo 'Ressource non trouvée : ' . $e->getMessage();
    } else {
        echo 'Erreur API (HTTP ' . $e->getCode() . ') : ' . $e->getMessage();
    }
} catch (TransportExceptionInterface $e) {
    // Erreur réseau (timeout, DNS, etc.)
    echo 'Erreur réseau : ' . $e->getMessage();
} catch (\RuntimeException $e) {
    // Échec d'authentification
    echo 'Authentification échouée : ' . $e->getMessage();
}
```
