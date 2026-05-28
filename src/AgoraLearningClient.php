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
use AgoraLearningPhp\DTO\Output\Enrollment\EnrollmentOutput;
use AgoraLearningPhp\DTO\Output\Learner\LearnerOutput;
use AgoraLearningPhp\DTO\Output\Person\PersonOutput;
use AgoraLearningPhp\DTO\Output\Session\SessionOutput;
use AgoraLearningPhp\DTO\Output\Society\SocietyOutput;
use AgoraLearningPhp\DTO\Output\Trainer\TrainerOutput;
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

    public function getPerson(string $uuid): PersonOutput
    {
        return $this->makePerson()->getPerson($uuid);
    }

    public function createPerson(PersonInput $personInput): PersonOutput
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

    public function getTrainer(string $uuid): TrainerOutput
    {
        return $this->makeTrainer()->getTrainer($uuid);
    }

    public function createTrainerEmployee(TrainerEmployeeInput $trainerEmployeeInput): TrainerOutput
    {
        return $this->makeTrainer()->postTrainerEmployee($trainerEmployeeInput);
    }

    public function createTrainerFree(TrainerFreeInput $trainerFreeInput): TrainerOutput
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

    public function getLearner(string $uuid): LearnerOutput
    {
        return $this->makeLearner()->getLearner($uuid);
    }

    public function createLearner(LearnerInput $learnerInput): LearnerOutput
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

    public function getSociety(string $uuid): SocietyOutput
    {
        return $this->makeSociety()->getSociety($uuid);
    }

    public function createSociety(SocietyInput $societyInput): SocietyOutput
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

    public function getSession(string $uuid): SessionOutput
    {
        return $this->makeSession()->getSession($uuid);
    }

    public function createSession(SessionInput $sessionInput): SessionOutput
    {
        return $this->makeSession()->postSession($sessionInput);
    }

    // Enrollment
    /**
     * @return array<int, EnrollmentOutput>
     */
    public function getCollectionEnrollmentFromSession(string $sessionUuid): array
    {
        return $this->makeEnrollment()->getCollectionEnrollmentFromSession($sessionUuid);
    }

    /**
     * @return array<int, EnrollmentOutput>
     */
    public function getCollectionEnrollmentFromLearner(string $learnerUuid): array
    {
        return $this->makeEnrollment()->getCollectionEnrollmentFromLearner($learnerUuid);
    }

    public function getEnrollment(string $uuid): EnrollmentOutput
    {
        return $this->makeEnrollment()->getEnrollment($uuid);
    }

    public function getEnrollmentFromSessionAndLearner(string $sessionUuid, string $learnerUuid): EnrollmentOutput
    {
        return $this->makeEnrollment()->getEnrollmentFromSessionAndLearner($sessionUuid, $learnerUuid);
    }

    public function createEnrollment(EnrollmentInput $enrollmentInput): EnrollmentOutput
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
