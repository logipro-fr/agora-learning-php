<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Tests\unit\Service\Program;

use AgoraLearningPhp\DTO\Input\Program\ProgramBiLevelComplexList;
use AgoraLearningPhp\DTO\Input\Program\ProgramComplexList;
use AgoraLearningPhp\DTO\Input\Program\ProgramInput;
use AgoraLearningPhp\DTO\Input\Program\ProgramSimpleList;
use AgoraLearningPhp\DTO\Input\Program\ProgramText;
use AgoraLearningPhp\Enum\RequestMethod;
use AgoraLearningPhp\Exception\AgoraLearningClientException;
use AgoraLearningPhp\Identifier\AgoraId;
use AgoraLearningPhp\Identifier\ExternalId;
use AgoraLearningPhp\Service\ClientCore\HttpClient;
use AgoraLearningPhp\Service\Program\Program;
use AgoraLearningPhp\Tests\unit\Service\ServiceTestCase;

class ProgramTest extends ServiceTestCase
{
    private const UPDATED_RESPONSE = '{"agoraId":"ses_1","sourceName":"Src","externalId":"S-1"}';

    private function makeProgram(HttpClient $httpClient): Program
    {
        return new class ($httpClient) extends Program {
            public function __construct(HttpClient $client)
            {
                $this->httpClient = $client;
            }
        };
    }

    public function testGetProgramCallsSessionProgramUrl(): void
    {
        // Arrange
        $method = '';
        $url = '';
        $program = $this->makeProgram(
            $this->makeHttpClientInspectingRequest(200, '{"sessionAgoraId":"ses_1"}', $method, $url)
        );

        // Act
        $program->getProgram(new ExternalId('Src', 'S-1'));

        // Assert
        $this->assertSame(RequestMethod::GET, $method);
        $this->assertSame('https://api.test.local/api/external/v1/programs/sessions/Src%3AS-1', $url);
    }

    public function testGetProgramConvertsEachProgramFormat(): void
    {
        // Arrange
        $body = (string) json_encode([
            'sessionAgoraId' => 'ses_1',
            'preRequisite' => ['description' => 'Avant', 'programSimpleList' => ['programSimpleList' => ['A', 'B']]],
            'pedagogicalContent' => [
                'description' => null,
                'programComplexList' => [
                    ['description' => 'Module 1', 'programSimpleList' => ['programSimpleList' => ['Saisie']]],
                ],
            ],
            'level' => ['programText' => 'Débutant'],
            'metricsEquivalences' => ['programSimpleList' => ['Équivalence A']],
            'skills' => null,
        ]);
        $program = $this->makeProgram($this->makeHttpClientWithResponse(200, $body));

        // Act
        $output = $program->getProgram(new AgoraId('ses_1'));

        // Assert
        $this->assertSame('ses_1', $output->sessionAgoraId);
        $this->assertEquals(
            new ProgramComplexList('Avant', new ProgramSimpleList(['A', 'B'])),
            $output->program->preRequisite
        );
        $this->assertEquals(
            new ProgramBiLevelComplexList(null, [
                new ProgramComplexList('Module 1', new ProgramSimpleList(['Saisie'])),
            ]),
            $output->program->pedagogicalContent
        );
        $this->assertEquals(new ProgramText('Débutant'), $output->program->level);
        $this->assertEquals(new ProgramSimpleList(['Équivalence A']), $output->program->metricsEquivalences);
        $this->assertNull($output->program->skills);
        $this->assertNull($output->program->accessDelay);
    }

    public function testGetProgramThrowsWithAgoraReasonWhenSessionHasNoLearningPath(): void
    {
        // Arrange
        $program = $this->makeProgram(
            $this->makeHttpClientWithResponse(422, '{"detail":"La session ne comporte aucun parcours"}')
        );

        // Assert
        $this->expectException(AgoraLearningClientException::class);
        $this->expectExceptionCode(422);
        $this->expectExceptionMessage('La session ne comporte aucun parcours');

        // Act
        $program->getProgram(new AgoraId('ses_1'));
    }

    public function testPutProgramCallsSessionProgramUrlAndReturnsIdentifiers(): void
    {
        // Arrange
        $method = '';
        $url = '';
        $program = $this->makeProgram(
            $this->makeHttpClientInspectingRequest(200, self::UPDATED_RESPONSE, $method, $url)
        );

        // Act
        $updated = $program->putProgram(new AgoraId('ses_1'), new ProgramInput());

        // Assert
        $this->assertSame(RequestMethod::PUT, $method);
        $this->assertSame('https://api.test.local/api/external/v1/programs/sessions/ses_1', $url);
        $this->assertSame('ses_1', $updated->agoraId->getId());
    }

    public function testPutProgramSendsNestedListsAsExpectedByAgora(): void
    {
        // Arrange
        $input = new ProgramInput();
        $input->preRequisite = new ProgramComplexList(null, new ProgramSimpleList(['Connaître Windows']));
        $input->pedagogicalObjective = new ProgramBiLevelComplexList('Objectifs', [
            new ProgramComplexList('Maîtriser Excel', new ProgramSimpleList(['Saisie', 'Formules'])),
        ]);
        $input->technicalMeans = new ProgramText('Ordinateur');
        $input->metricsEquivalences = new ProgramSimpleList(['Équivalence A']);
        $body = '';
        $program = $this->makeProgram($this->makeHttpClientInspectingBody(200, self::UPDATED_RESPONSE, $body));

        // Act
        $program->putProgram(new AgoraId('ses_1'), $input);

        // Assert
        $this->assertSame(
            [
                'preRequisite' => [
                    'description' => null,
                    'programSimpleList' => ['programSimpleList' => ['Connaître Windows']],
                ],
                'pedagogicalObjective' => [
                    'description' => 'Objectifs',
                    'programComplexList' => [
                        [
                            'description' => 'Maîtriser Excel',
                            'programSimpleList' => ['programSimpleList' => ['Saisie', 'Formules']],
                        ],
                    ],
                ],
                'technicalMeans' => ['programText' => 'Ordinateur'],
                'metricsEquivalences' => ['programSimpleList' => ['Équivalence A']],
            ],
            json_decode($body, true)
        );
    }

    public function testPutProgramSendsEveryProvidedType(): void
    {
        // Arrange
        $input = new ProgramInput();
        foreach (ProgramInput::TEXT_TYPES as $type) {
            $input->$type = new ProgramText('texte');
        }
        foreach (ProgramInput::SIMPLE_LIST_TYPES as $type) {
            $input->$type = new ProgramSimpleList();
        }
        foreach (ProgramInput::COMPLEX_LIST_TYPES as $type) {
            $input->$type = new ProgramComplexList();
        }
        foreach (ProgramInput::BI_LEVEL_COMPLEX_LIST_TYPES as $type) {
            $input->$type = new ProgramBiLevelComplexList();
        }
        $body = '';
        $program = $this->makeProgram($this->makeHttpClientInspectingBody(200, self::UPDATED_RESPONSE, $body));

        // Act
        $program->putProgram(new AgoraId('ses_1'), $input);

        // Assert
        /** @var array<string, mixed> $sent */
        $sent = json_decode($body, true);
        $this->assertCount(24, $sent);
        $this->assertSame(['programText' => 'texte'], $sent['accessDelay']);
        $this->assertSame(['programSimpleList' => []], $sent['metricsCareerSuites']);
        $this->assertSame(['description' => null, 'programSimpleList' => ['programSimpleList' => []]], $sent['skills']);
        $this->assertSame(['description' => null, 'programComplexList' => []], $sent['pedagogicalContent']);
    }

    public function testPutProgramEncodesEmptyProgramAsJsonObject(): void
    {
        // Arrange
        $body = '';
        $program = $this->makeProgram($this->makeHttpClientInspectingBody(200, self::UPDATED_RESPONSE, $body));

        // Act
        $program->putProgram(new AgoraId('ses_1'), new ProgramInput());

        // Assert
        $this->assertSame('{}', $body);
    }

    public function testProgramReadCanBeSentBackUnchanged(): void
    {
        // Arrange
        $read = [
            'preRequisite' => ['description' => 'Avant', 'programSimpleList' => ['programSimpleList' => ['A']]],
            'pedagogicalContent' => [
                'description' => null,
                'programComplexList' => [
                    ['description' => 'Module 1', 'programSimpleList' => ['programSimpleList' => ['Saisie']]],
                ],
            ],
            'level' => ['programText' => 'Débutant'],
            'metricsEquivalences' => ['programSimpleList' => ['Équivalence A']],
        ];
        $input = Program::convertDataToDTO(array_merge(['sessionAgoraId' => 'ses_1'], $read))->program;
        $body = '';
        $program = $this->makeProgram($this->makeHttpClientInspectingBody(200, self::UPDATED_RESPONSE, $body));

        // Act
        $program->putProgram(new AgoraId('ses_1'), $input);

        // Assert
        $this->assertEquals($read, json_decode($body, true));
    }

    public function testPutProgramThrowsWithAgoraReasonOnError(): void
    {
        // Arrange
        $program = $this->makeProgram(
            $this->makeHttpClientWithResponse(422, '{"detail":"targetAudience: valeur invalide"}')
        );

        // Assert
        $this->expectException(AgoraLearningClientException::class);
        $this->expectExceptionCode(422);
        $this->expectExceptionMessage('targetAudience: valeur invalide');

        // Act
        $program->putProgram(new AgoraId('ses_1'), new ProgramInput());
    }
}
