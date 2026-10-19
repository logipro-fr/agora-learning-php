<?php

declare(strict_types=1);

namespace AgoraLearningPhp;

use AgoraLearningPhp\DTO\Input\Enrollment\EnrollmentInput;
use AgoraLearningPhp\DTO\Input\Learner\LearnerInput;
use AgoraLearningPhp\DTO\Input\Learner\LearnerPatchInput;
use AgoraLearningPhp\DTO\Input\Person\PersonInput;
use AgoraLearningPhp\DTO\Input\Person\PersonPatchInput;
use AgoraLearningPhp\DTO\Input\Session\AbstractSessionPatchInput;
use AgoraLearningPhp\DTO\Input\Session\SessionInput;
use AgoraLearningPhp\DTO\Input\Society\SocietyInput;
use AgoraLearningPhp\DTO\Input\Society\SocietyPatchInput;
use AgoraLearningPhp\DTO\Input\Trainer\TrainerEmployeeInput;
use AgoraLearningPhp\DTO\Input\Trainer\TrainerEmployeePatchInput;
use AgoraLearningPhp\DTO\Input\Trainer\TrainerFreeInput;
use AgoraLearningPhp\DTO\Input\Trainer\TrainerFreePatchInput;
use AgoraLearningPhp\DTO\Output\CreatedOutput;
use AgoraLearningPhp\DTO\Output\Enrollment\EnrollmentOutput;
use AgoraLearningPhp\DTO\Output\Learner\LearnerOutput;
use AgoraLearningPhp\DTO\Output\Person\PersonOutput;
use AgoraLearningPhp\DTO\Output\Session\SessionOutput;
use AgoraLearningPhp\DTO\Output\Society\SocietyOutput;
use AgoraLearningPhp\DTO\Output\Trainer\TrainerOutput;
use AgoraLearningPhp\Exception\AgoraLearningClientException;
use AgoraLearningPhp\Identifier\ApiObjectIdentifier;
use AgoraLearningPhp\Service\Auth\Ping;
use AgoraLearningPhp\Service\ClientCore\ApiUrls;
use AgoraLearningPhp\Service\ClientCore\TokenHandler;
use AgoraLearningPhp\Service\Enrollment\Enrollment;
use AgoraLearningPhp\Service\Learner\Learner;
use AgoraLearningPhp\Service\Person\Person;
use AgoraLearningPhp\Service\Session\Session;
use AgoraLearningPhp\Service\Society\Society;
use AgoraLearningPhp\Service\Trainer\Trainer;
use Symfony\Contracts\HttpClient\ResponseInterface;

class AgoraLearningClient
{
    public function __construct(
        string $url,
        string $apiKey
    ) {
        ApiUrls::initBaseUrl($url);
        TokenHandler::initApiKey($apiKey);
    }

    // Auth
    public function ping(): ResponseInterface
    {
        return $this->makePing()->ping();
    }

    // Person
    /**
     * @return array<int, PersonOutput>
     */
    public function getCollectionPerson(): array
    {
        return $this->makePerson()->getCollectionPerson();
    }

    public function getPerson(ApiObjectIdentifier $identifier): PersonOutput
    {
        return $this->makePerson()->getPerson($identifier);
    }

    public function createPerson(PersonInput $personInput): CreatedOutput
    {
        return $this->makePerson()->postPerson($personInput);
    }

    /**
     * Remplace le contact (PUT) : un champ facultatif non renseigné est vidé.
     */
    public function replacePerson(ApiObjectIdentifier $identifier, PersonInput $personInput): CreatedOutput
    {
        return $this->makePerson()->putPerson($identifier, $personInput);
    }

    /**
     * Modifie partiellement le contact (PATCH) : seuls les champs renseignés sont modifiés.
     */
    public function patchPerson(ApiObjectIdentifier $identifier, PersonPatchInput $personPatchInput): CreatedOutput
    {
        return $this->makePerson()->patchPerson($identifier, $personPatchInput);
    }

    /**
     * Supprime le contact.
     *
     * @throws AgoraLearningClientException si le contact n'existe pas (404) ou en cas d'erreur de l'API
     */
    public function deletePerson(ApiObjectIdentifier $identifier): void
    {
        $this->makePerson()->deletePerson($identifier);
    }

    // Trainer
    /**
     * @return array<int, TrainerOutput>
     */
    public function getCollectionTrainer(): array
    {
        return $this->makeTrainer()->getCollectionTrainer();
    }

    public function getTrainer(ApiObjectIdentifier $identifier): TrainerOutput
    {
        return $this->makeTrainer()->getTrainer($identifier);
    }

    public function createTrainerEmployee(TrainerEmployeeInput $trainerEmployeeInput): CreatedOutput
    {
        return $this->makeTrainer()->postTrainerEmployee($trainerEmployeeInput);
    }

    /**
     * Remplace le formateur employé (PUT) : un champ facultatif non renseigné est vidé.
     */
    public function replaceTrainerEmployee(ApiObjectIdentifier $identifier, TrainerEmployeeInput $trainerEmployeeInput): CreatedOutput
    {
        return $this->makeTrainer()->putTrainerEmployee($identifier, $trainerEmployeeInput);
    }

    /**
     * Modifie partiellement le formateur employé (PATCH) : seuls les champs renseignés sont modifiés.
     */
    public function patchTrainerEmployee(ApiObjectIdentifier $identifier, TrainerEmployeePatchInput $trainerEmployeePatchInput): CreatedOutput
    {
        return $this->makeTrainer()->patchTrainerEmployee($identifier, $trainerEmployeePatchInput);
    }

    public function createTrainerFree(TrainerFreeInput $trainerFreeInput): CreatedOutput
    {
        return $this->makeTrainer()->postTrainerFree($trainerFreeInput);
    }

    /**
     * Remplace le formateur indépendant (PUT) : un champ facultatif non renseigné est vidé.
     */
    public function replaceTrainerFree(ApiObjectIdentifier $identifier, TrainerFreeInput $trainerFreeInput): CreatedOutput
    {
        return $this->makeTrainer()->putTrainerFree($identifier, $trainerFreeInput);
    }

    /**
     * Modifie partiellement le formateur indépendant (PATCH) : seuls les champs renseignés sont modifiés.
     */
    public function patchTrainerFree(ApiObjectIdentifier $identifier, TrainerFreePatchInput $trainerFreePatchInput): CreatedOutput
    {
        return $this->makeTrainer()->patchTrainerFree($identifier, $trainerFreePatchInput);
    }

    /**
     * Supprime le formateur, employé ou indépendant.
     *
     * @throws AgoraLearningClientException si le formateur n'existe pas (404) ou en cas d'erreur de l'API
     */
    public function deleteTrainer(ApiObjectIdentifier $identifier): void
    {
        $this->makeTrainer()->deleteTrainer($identifier);
    }

    // Learner
    /**
     * @return array<int, LearnerOutput>
     */
    public function getCollectionLearner(): array
    {
        return $this->makeLearner()->getCollectionLearner();
    }

    public function getLearner(ApiObjectIdentifier $identifier): LearnerOutput
    {
        return $this->makeLearner()->getLearner($identifier);
    }

    public function createLearner(LearnerInput $learnerInput): CreatedOutput
    {
        return $this->makeLearner()->postLearner($learnerInput);
    }

    /**
     * Remplace l'apprenant (PUT) : un champ facultatif non renseigné est vidé.
     */
    public function replaceLearner(ApiObjectIdentifier $identifier, LearnerInput $learnerInput): CreatedOutput
    {
        return $this->makeLearner()->putLearner($identifier, $learnerInput);
    }

    /**
     * Modifie partiellement l'apprenant (PATCH) : seuls les champs renseignés sont modifiés.
     */
    public function patchLearner(ApiObjectIdentifier $identifier, LearnerPatchInput $learnerPatchInput): CreatedOutput
    {
        return $this->makeLearner()->patchLearner($identifier, $learnerPatchInput);
    }

    /**
     * Supprime l'apprenant.
     *
     * @throws AgoraLearningClientException si l'apprenant n'existe pas (404) ou en cas d'erreur de l'API
     */
    public function deleteLearner(ApiObjectIdentifier $identifier): void
    {
        $this->makeLearner()->deleteLearner($identifier);
    }

    // Society
    /**
     * @return array<int, SocietyOutput>
     */
    public function getCollectionSociety(): array
    {
        return $this->makeSociety()->getCollectionSociety();
    }

    public function getSociety(ApiObjectIdentifier $identifier): SocietyOutput
    {
        return $this->makeSociety()->getSociety($identifier);
    }

    public function createSociety(SocietyInput $societyInput): CreatedOutput
    {
        return $this->makeSociety()->postSociety($societyInput);
    }

    /**
     * Remplace la société (PUT) : un champ facultatif non renseigné est vidé.
     */
    public function replaceSociety(ApiObjectIdentifier $identifier, SocietyInput $societyInput): CreatedOutput
    {
        return $this->makeSociety()->putSociety($identifier, $societyInput);
    }

    /**
     * Modifie partiellement la société (PATCH) : seuls les champs renseignés sont modifiés.
     */
    public function patchSociety(ApiObjectIdentifier $identifier, SocietyPatchInput $societyPatchInput): CreatedOutput
    {
        return $this->makeSociety()->patchSociety($identifier, $societyPatchInput);
    }

    /**
     * Supprime la société.
     *
     * @throws AgoraLearningClientException si la société n'existe pas (404) ou en cas d'erreur de l'API
     */
    public function deleteSociety(ApiObjectIdentifier $identifier): void
    {
        $this->makeSociety()->deleteSociety($identifier);
    }

    // Session
    /**
     * @return array<int, SessionOutput>
     */
    public function getCollectionSession(): array
    {
        return $this->makeSession()->getCollectionSession();
    }

    public function getSession(ApiObjectIdentifier $identifier): SessionOutput
    {
        return $this->makeSession()->getSession($identifier);
    }

    public function createSession(SessionInput $sessionInput): CreatedOutput
    {
        return $this->makeSession()->postSession($sessionInput);
    }

    /**
     * Remplace la session (PUT) : un champ facultatif non renseigné est vidé.
     * Le type de $sessionInput->sessionData doit être celui de la session existante.
     */
    public function replaceSession(ApiObjectIdentifier $identifier, SessionInput $sessionInput): CreatedOutput
    {
        return $this->makeSession()->putSession($identifier, $sessionInput);
    }

    /**
     * Modifie partiellement la session (PATCH) : seuls les champs renseignés sont modifiés.
     *
     * @param AbstractSessionPatchInput $sessionPatchInput FixedSessionPatchInput ou OpenedSessionPatchInput
     */
    public function patchSession(ApiObjectIdentifier $identifier, AbstractSessionPatchInput $sessionPatchInput): CreatedOutput
    {
        return $this->makeSession()->patchSession($identifier, $sessionPatchInput);
    }

    /**
     * Supprime la session, fixe ou ouverte.
     *
     * @throws AgoraLearningClientException si la session n'existe pas (404) ou en cas d'erreur de l'API
     */
    public function deleteSession(ApiObjectIdentifier $identifier): void
    {
        $this->makeSession()->deleteSession($identifier);
    }

    // Enrollment
    /**
     * @return array<int, EnrollmentOutput>
     */
    public function getCollectionEnrollmentFromSession(ApiObjectIdentifier $sessionIdentifier): array
    {
        return $this->makeEnrollment()->getCollectionEnrollmentFromSession($sessionIdentifier);
    }

    /**
     * @return array<int, EnrollmentOutput>
     */
    public function getCollectionEnrollmentFromLearner(ApiObjectIdentifier $learnerIdentifier): array
    {
        return $this->makeEnrollment()->getCollectionEnrollmentFromLearner($learnerIdentifier);
    }

    public function getEnrollment(ApiObjectIdentifier $identifier): EnrollmentOutput
    {
        return $this->makeEnrollment()->getEnrollment($identifier);
    }

    public function getEnrollmentFromSessionAndLearner(
        ApiObjectIdentifier $sessionIdentifier,
        ApiObjectIdentifier $learnerIdentifier
    ): EnrollmentOutput {
        return $this->makeEnrollment()->getEnrollmentFromSessionAndLearner($sessionIdentifier, $learnerIdentifier);
    }

    public function createEnrollment(EnrollmentInput $enrollmentInput): CreatedOutput
    {
        return $this->makeEnrollment()->postEnrollment($enrollmentInput);
    }

    /**
     * Désinscrit l'apprenant de la session.
     *
     * @throws AgoraLearningClientException si l'inscription n'existe pas (404) ou en cas d'erreur de l'API
     */
    public function deleteEnrollment(ApiObjectIdentifier $identifier): void
    {
        $this->makeEnrollment()->deleteEnrollment($identifier);
    }

    /**
     * Désinscrit l'apprenant de la session.
     *
     * @throws AgoraLearningClientException si l'inscription n'existe pas (404) ou en cas d'erreur de l'API
     */
    public function deleteEnrollmentFromSessionAndLearner(
        ApiObjectIdentifier $sessionIdentifier,
        ApiObjectIdentifier $learnerIdentifier
    ): void {
        $this->makeEnrollment()->deleteEnrollmentFromSessionAndLearner($sessionIdentifier, $learnerIdentifier);
    }

    /**
     * Supprime toutes les inscriptions de la session.
     *
     * @throws AgoraLearningClientException si la session n'existe pas (404) ou en cas d'erreur de l'API
     */
    public function deleteCollectionEnrollmentFromSession(ApiObjectIdentifier $sessionIdentifier): void
    {
        $this->makeEnrollment()->deleteCollectionEnrollmentFromSession($sessionIdentifier);
    }

    /**
     * Supprime toutes les inscriptions de l'apprenant.
     *
     * @throws AgoraLearningClientException si l'apprenant n'existe pas (404) ou en cas d'erreur de l'API
     */
    public function deleteCollectionEnrollmentFromLearner(ApiObjectIdentifier $learnerIdentifier): void
    {
        $this->makeEnrollment()->deleteCollectionEnrollmentFromLearner($learnerIdentifier);
    }


    protected function makePing(): Ping
    {
        return new Ping();
    }

    protected function makePerson(): Person
    {
        return new Person();
    }

    protected function makeTrainer(): Trainer
    {
        return new Trainer();
    }

    protected function makeLearner(): Learner
    {
        return new Learner();
    }

    protected function makeSociety(): Society
    {
        return new Society();
    }

    protected function makeSession(): Session
    {
        return new Session();
    }

    protected function makeEnrollment(): Enrollment
    {
        return new Enrollment();
    }
}
