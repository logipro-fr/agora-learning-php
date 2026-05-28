<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Service\ClientCore;

class ApiUrls
{
    protected static string $baseUrl = '';

    public const DEFAULT_BASE_URL = 'localhost/phoenix';

    public const PREFIX_API = '/api/external';

    public const API_VERSION = '/v1';

    // AUTH
    public const PING = self::PREFIX_API . self::API_VERSION . '/ping';
    public const GET_TOKEN = self::PREFIX_API .  self::API_VERSION . '/auth/token';

    // PERSON
    public const CREATE_PERSON = self::PREFIX_API . self::API_VERSION . '/persons';
    public const GET_PERSON = self::PREFIX_API . self::API_VERSION . '/persons/';
    public const GET_COLLECTION_PERSON = self::PREFIX_API . self::API_VERSION . '/persons';

    // TRAINER
    public const CREATE_TRAINER_EMPLOYEE = self::PREFIX_API . self::API_VERSION . '/trainers/employee';
    public const CREATE_TRAINER_FREE = self::PREFIX_API . self::API_VERSION . '/trainers/free';
    public const GET_TRAINER = self::PREFIX_API . self::API_VERSION . '/trainers/';
    public const GET_COLLECTION_TRAINER = self::PREFIX_API . self::API_VERSION . '/trainers';

    // LEARNER
    public const CREATE_LEARNER = self::PREFIX_API . self::API_VERSION . '/learners';
    public const GET_LEARNER = self::PREFIX_API . self::API_VERSION . '/learners/';
    public const GET_COLLECTION_LEARNER = self::PREFIX_API . self::API_VERSION . '/learners';

    // SESSION
    public const CREATE_FIXED_SESSION = self::PREFIX_API . self::API_VERSION . '/sessions/fixed';
    public const CREATE_OPEN_SESSION = self::PREFIX_API . self::API_VERSION . '/sessions/opened';
    public const GET_SESSION = self::PREFIX_API . self::API_VERSION . '/sessions/';
    public const GET_COLLECTION_SESSION = self::PREFIX_API . self::API_VERSION . '/sessions';

    // SOCIETY
    public const CREATE_SOCIETY = self::PREFIX_API . self::API_VERSION . '/societies';
    public const GET_SOCIETY = self::PREFIX_API . self::API_VERSION . '/societies/';
    public const GET_COLLECTION_SOCIETY = self::PREFIX_API . self::API_VERSION . '/societies';

    // ENROLLMENT
    public const CREATE_ENROLLMENT = self::PREFIX_API . self::API_VERSION . '/enrollments';
    public const GET_ENROLLMENT = self::PREFIX_API . self::API_VERSION . '/enrollments/';
    public const GET_ENROLLMENT_FROM_SESSION_AND_LEARNER = self::PREFIX_API . self::API_VERSION . '/sessions/%s/learners/%s/enrollments';
    public const GET_COLLECTION_ENROLLMENT_FROM_SESSION = self::PREFIX_API . self::API_VERSION . '/sessions/%s/enrollments';
    public const GET_COLLECTION_ENROLLMENT_FROM_LEARNER = self::PREFIX_API . self::API_VERSION . '/learners/%s/enrollments';

    public static function initBaseUrl(string $baseUrl): void
    {
        if (self::$baseUrl === '') {
            self::$baseUrl = $baseUrl;
        }
    }

    private static function getBaseUrl(): string
    {
        $baseUrl = self::DEFAULT_BASE_URL;

        if (!empty(self::$baseUrl)) {
            return self::$baseUrl;
        }

        return $baseUrl;
    }

    // AUTH
    public static function getPing(): string
    {
        return self::getBaseUrl() . self::PING;
    }

    public static function getGetToken(): string
    {
        return self::getBaseUrl() . self::GET_TOKEN;
    }

    // PERSON
    public static function getCreatePerson(): string
    {
        return self::getBaseUrl() . self::CREATE_PERSON;
    }

    public static function getGetPerson(string $personUuid): string
    {
        return self::getBaseUrl() . self::GET_PERSON . $personUuid;
    }

    public static function getGetCollectionPerson(): string
    {
        return self::getBaseUrl() . self::GET_COLLECTION_PERSON;
    }

    // TRAINER
    public static function getCreateTrainerEmployee(): string
    {
        return self::getBaseUrl() . self::CREATE_TRAINER_EMPLOYEE;
    }

    public static function getCreateTrainerFree(): string
    {
        return self::getBaseUrl() . self::CREATE_TRAINER_FREE;
    }

    public static function getGetTrainer(string $trainerUuid): string
    {
        return self::getBaseUrl() . self::GET_TRAINER . $trainerUuid;
    }

    public static function getGetCollectionTrainer(): string
    {
        return self::getBaseUrl() . self::GET_COLLECTION_TRAINER;
    }

    // LEARNER
    public static function getCreateLearner(): string
    {
        return self::getBaseUrl() . self::CREATE_LEARNER;
    }

    public static function getGetLearner(string $learnerUuid): string
    {
        return self::getBaseUrl() . self::GET_LEARNER . $learnerUuid;
    }

    public static function getGetCollectionLearner(): string
    {
        return self::getBaseUrl() . self::GET_COLLECTION_LEARNER;
    }

    // SESSION
    public static function getCreateFixedSession(): string
    {
        return self::getBaseUrl() . self::CREATE_FIXED_SESSION;
    }

    public static function getCreateOpenedSession(): string
    {
        return self::getBaseUrl() . self::CREATE_OPEN_SESSION;
    }

    public static function getGetSession(string $sessionUuid): string
    {
        return self::getBaseUrl() . self::GET_SESSION . $sessionUuid;
    }

    public static function getGetCollectionSession(): string
    {
        return self::getBaseUrl() . self::GET_COLLECTION_SESSION;
    }

    // SOCIETY
    public static function getCreateSociety(): string
    {
        return self::getBaseUrl() . self::CREATE_SOCIETY;
    }

    public static function getGetSociety(string $societyUuid): string
    {
        return self::getBaseUrl() . self::GET_SOCIETY . $societyUuid;
    }

    public static function getGetCollectionSociety(): string
    {
        return self::getBaseUrl() . self::GET_COLLECTION_SOCIETY;
    }

    // ENROLLMENT
    public static function getCreateEnrollment(): string
    {
        return self::getBaseUrl() . self::CREATE_ENROLLMENT;
    }

    public static function getGetEnrollment(string $enrollmentUuid): string
    {
        return self::getBaseUrl() . self::GET_ENROLLMENT . $enrollmentUuid;
    }

    public static function getGetEnrollmentFromSessionAndLearner(string $sessionUuid, string $learnerUuid): string
    {
        return self::getBaseUrl() . sprintf(self::GET_ENROLLMENT_FROM_SESSION_AND_LEARNER, $sessionUuid, $learnerUuid);
    }

    public static function getGetCollectionEnrollmentFromSession(string $sessionUuid): string
    {
        return self::getBaseUrl() . sprintf(self::GET_COLLECTION_ENROLLMENT_FROM_SESSION, $sessionUuid);
    }

    public static function getGetCollectionEnrollmentFromLearner(string $learnerUuid): string
    {
        return self::getBaseUrl() . sprintf(self::GET_COLLECTION_ENROLLMENT_FROM_LEARNER, $learnerUuid);
    }
}
