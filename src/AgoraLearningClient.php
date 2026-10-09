<?php

declare(strict_types=1);

namespace AgoraLearningPhp;

use AgoraLearningPhp\DTO\Input\Enrollment\EnrollmentInput;
use AgoraLearningPhp\DTO\Input\Learner\LearnerInput;
use AgoraLearningPhp\DTO\Input\Person\PersonInput;
use AgoraLearningPhp\DTO\Input\Session\SessionInput;
use AgoraLearningPhp\DTO\Input\Society\SocietyInput;
use AgoraLearningPhp\DTO\Input\Trainer\TrainerEmployeeInput;
use AgoraLearningPhp\DTO\Input\Trainer\TrainerFreeInput;
use AgoraLearningPhp\DTO\Output\CreatedOutput;
use AgoraLearningPhp\DTO\Output\Enrollment\EnrollmentOutput;
use AgoraLearningPhp\DTO\Output\Learner\LearnerOutput;
use AgoraLearningPhp\DTO\Output\Person\PersonOutput;
use AgoraLearningPhp\DTO\Output\Session\SessionOutput;
use AgoraLearningPhp\DTO\Output\Society\SocietyOutput;
use AgoraLearningPhp\DTO\Output\Trainer\TrainerOutput;
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

    public function createTrainerFree(TrainerFreeInput $trainerFreeInput): CreatedOutput
    {
        return $this->makeTrainer()->postTrainerFree($trainerFreeInput);
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
