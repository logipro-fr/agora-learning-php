<?php

declare(strict_types=1);

namespace AgoraLearningPhp\DTO\Input\Program;

/**
 * Programme de formation complet d'une session, envoyé par replaceProgram() (PUT).
 *
 * Le PUT remplace tout le programme : un type non renseigné (null) n'est pas envoyé, donc vidé sur Agora.
 * Pour ne changer que certains types, partir du programme lu avec getProgram().
 */
final class ProgramInput
{
    /** Types au format ProgramText. */
    public const TEXT_TYPES = [
        'accessDelay',
        'pedagogicalMethods',
        'pedagogicalMeans',
        'technicalMeans',
        'level',
        'accessModality',
        'assessmentAchievements',
        'accessTerms',
        'successRate',
        'opportunities',
        'metricsDropoutRate',
        'metricsInterruptRate',
        'metricsCertificationRate',
        'metricsGateways',
        'metricsHandicapReferent',
    ];

    /** Types au format ProgramSimpleList. */
    public const SIMPLE_LIST_TYPES = [
        'metricsValidableSkills',
        'metricsEquivalences',
        'metricsCareerSuites',
    ];

    /** Types au format ProgramComplexList. */
    public const COMPLEX_LIST_TYPES = [
        'preRequisite',
        'aptitude',
        'skills',
        'targetAudience',
    ];

    /** Types au format ProgramBiLevelComplexList. */
    public const BI_LEVEL_COMPLEX_LIST_TYPES = [
        'pedagogicalObjective',
        'pedagogicalContent',
    ];

    /** Prérequis */
    public ?ProgramComplexList $preRequisite = null;
    /** Objectifs pédagogiques */
    public ?ProgramBiLevelComplexList $pedagogicalObjective = null;
    /** Contenus pédagogiques */
    public ?ProgramBiLevelComplexList $pedagogicalContent = null;
    /** Aptitudes */
    public ?ProgramComplexList $aptitude = null;
    /** Compétences */
    public ?ProgramComplexList $skills = null;
    /** Délai d'accès */
    public ?ProgramText $accessDelay = null;
    /** Moyens d'encadrement */
    public ?ProgramText $pedagogicalMethods = null;
    /** Méthodes mobilisées */
    public ?ProgramText $pedagogicalMeans = null;
    /** Moyens techniques */
    public ?ProgramText $technicalMeans = null;
    /** Niveau */
    public ?ProgramText $level = null;
    /** Modalités d'accès */
    public ?ProgramText $accessModality = null;
    /** Modalités d'évaluation */
    public ?ProgramText $assessmentAchievements = null;
    /** Accessibilité aux personnes handicapées */
    public ?ProgramText $accessTerms = null;
    /** Taux de réussite (%) */
    public ?ProgramText $successRate = null;
    /** Débouchés */
    public ?ProgramText $opportunities = null;
    /** Public cible */
    public ?ProgramComplexList $targetAudience = null;
    public ?ProgramText $metricsDropoutRate = null;
    public ?ProgramText $metricsInterruptRate = null;
    public ?ProgramText $metricsCertificationRate = null;
    public ?ProgramSimpleList $metricsValidableSkills = null;
    public ?ProgramSimpleList $metricsEquivalences = null;
    public ?ProgramText $metricsGateways = null;
    public ?ProgramSimpleList $metricsCareerSuites = null;
    public ?ProgramText $metricsHandicapReferent = null;
}
