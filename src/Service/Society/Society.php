<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Service\Society;

use AgoraLearningPhp\DTO\Input\Society\SocietyInput;
use AgoraLearningPhp\DTO\Input\Society\SocietyPatchInput;
use AgoraLearningPhp\DTO\Output\CreatedOutput;
use AgoraLearningPhp\DTO\Output\Society\SocietyOutput;
use AgoraLearningPhp\Enum\RequestMethod;
use AgoraLearningPhp\Exception\AgoraLearningClientException;
use AgoraLearningPhp\Identifier\ApiObjectIdentifier;
use AgoraLearningPhp\Identifier\ExternalId;
use AgoraLearningPhp\Service\ClientCore\ApiService;
use AgoraLearningPhp\Service\ClientCore\ApiUrls;
use AgoraLearningPhp\Service\Person\Person;
use AgoraLearningPhp\Service\Tools\JsonHandler;

/**
 * @extends ApiService<SocietyOutput>
 */
class Society extends ApiService
{
    /**
     * @return array<int, SocietyOutput>
     */
    public function getCollectionSociety(): array
    {
        $body = '';
        $response = $this->httpClient->request(RequestMethod::GET, ApiUrls::getGetCollectionSociety(), $body);
        return $this->convertResponseToDTOarray($response);
    }

    public function getSociety(ApiObjectIdentifier $identifier): SocietyOutput
    {
        $body = '';
        $response = $this->httpClient->request(RequestMethod::GET, ApiUrls::getGetSociety($identifier->getId()), $body);
        return $this->convertResponseToDTO($response);
    }

    public function postSociety(SocietyInput $societyInput): CreatedOutput
    {
        $body = JsonHandler::jsonEncode($this->buildBody($societyInput));
        $response = $this->httpClient->request(RequestMethod::POST, ApiUrls::getCreateSociety(), $body);
        return $this->convertResponsePostToDTO($response);
    }

    /**
     * Remplace la société (PUT) : un champ facultatif non renseigné est vidé
     * et un responsable non renseigné est retiré.
     */
    public function putSociety(ApiObjectIdentifier $identifier, SocietyInput $societyInput): CreatedOutput
    {
        $body = JsonHandler::jsonEncode($this->buildBody($societyInput));
        $response = $this->httpClient->request(RequestMethod::PUT, ApiUrls::getUpdateSociety($identifier->getId()), $body);
        return $this->convertResponsePostToDTO($response);
    }

    /**
     * Modifie partiellement la société (PATCH) : seuls les champs renseignés sont modifiés.
     */
    public function patchSociety(ApiObjectIdentifier $identifier, SocietyPatchInput $societyPatchInput): CreatedOutput
    {
        return $this->sendPatch(ApiUrls::getUpdateSociety($identifier->getId()), $societyPatchInput);
    }

    /**
     * Détache la société de l'application source (PATCH /societies/{id}/detach, réponse 204 sans contenu) :
     * elle reste sur Agora mais n'est plus liée à l'application qui l'a créée.
     *
     * @throws AgoraLearningClientException si la société n'existe pas (404) ou si Agora refuse le détachement
     */
    public function detachSociety(ApiObjectIdentifier $identifier): void
    {
        $this->sendDetach(ApiUrls::getDetachSociety($identifier->getId()));
    }

    /**
     * Supprime la société (DELETE, réponse 204 sans contenu).
     *
     * @throws AgoraLearningClientException si la société n'existe pas (404) ou en cas d'erreur de l'API
     */
    public function deleteSociety(ApiObjectIdentifier $identifier): void
    {
        $this->sendDelete(ApiUrls::getDeleteSociety($identifier->getId()));
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function convertDataToDTO(array $data): SocietyOutput
    {
        /** @var array{
         *     agoraId: string,
         *     externalIdentifier?: array<string, mixed>|null,
         *     name: string,
         *     siret?: string|null,
         *     legalStatus?: string|null,
         *     email?: string|null,
         *     telephone?: string|null,
         *     addressStreetHeadOffice?: string|null,
         *     addressPostcodeHeadOffice?: string|null,
         *     addressLocalityHeadOffice?: string|null,
         *     addressCountryHeadOffice?: string|null,
         *     addressStreetInvoicing?: string|null,
         *     addressPostcodeInvoicing?: string|null,
         *     addressLocalityInvoicing?: string|null,
         *     addressCountryInvoicing?: string|null,
         *     legalPerson?: array<string, mixed>,
         *     administrativePerson?: array<string, mixed>,
         *     rhPerson?: array<string, mixed>,
         * } $data */
        $siret = $data['siret'] ?? null;
        $telephone = $data['telephone'] ?? null;
        $email = $data['email'] ?? null;
        $legalStatus = $data['legalStatus'] ?? null;
        $addressStreetHeadOffice = $data['addressStreetHeadOffice'] ?? null;
        $addressPostcodeHeadOffice = $data['addressPostcodeHeadOffice'] ?? null;
        $addressLocalityHeadOffice = $data['addressLocalityHeadOffice'] ?? null;
        $addressCountryHeadOffice = $data['addressCountryHeadOffice'] ?? null;
        $addressStreetInvoicing = $data['addressStreetInvoicing'] ?? null;
        $addressPostcodeInvoicing = $data['addressPostcodeInvoicing'] ?? null;
        $addressLocalityInvoicing = $data['addressLocalityInvoicing'] ?? null;
        $addressCountryInvoicing = $data['addressCountryInvoicing'] ?? null;
        $externalIdentifier = !empty($data['externalIdentifier']) ? ExternalId::fromArray($data['externalIdentifier']) : null;

        $legalPerson = array_key_exists('legalPerson', $data)
            ? Person::convertDataToDTO($data['legalPerson'])
            : null;
        $administrativePerson = array_key_exists('administrativePerson', $data)
            ? Person::convertDataToDTO($data['administrativePerson'])
            : null;
        $rhPerson = array_key_exists('rhPerson', $data)
            ? Person::convertDataToDTO($data['rhPerson'])
            : null;

        return new SocietyOutput(
            $data['agoraId'],
            $data['name'],
            $siret,
            $legalStatus,
            $email,
            $telephone,
            $addressStreetHeadOffice,
            $addressPostcodeHeadOffice,
            $addressLocalityHeadOffice,
            $addressCountryHeadOffice,
            $addressStreetInvoicing,
            $addressPostcodeInvoicing,
            $addressLocalityInvoicing,
            $addressCountryInvoicing,
            $legalPerson,
            $administrativePerson,
            $rhPerson,
            $externalIdentifier,
        );
    }

    /**
     * Corps commun à la création (POST) et au remplacement (PUT).
     * Attention : la clé de l'identifiant externe est "externalId" (et non "externalIdentifier").
     *
     * @return array<string, mixed>
     */
    private function buildBody(SocietyInput $societyInput): array
    {
        return [
            "externalId" => $societyInput->externalIdentifier->toArray(),
            "name" => $societyInput->name,
            "siret" => $societyInput->siret,
            "telephone" => $societyInput->telephone,
            "email" => $societyInput->email,
            "legalStatus" => $societyInput->legalStatus,
            "addressStreetHeadOffice" => $societyInput->addressStreetHeadOffice,
            "addressPostcodeHeadOffice" => $societyInput->addressPostcodeHeadOffice,
            "addressLocalityHeadOffice" => $societyInput->addressLocalityHeadOffice,
            "addressCountryHeadOffice" => $societyInput->addressCountryHeadOffice,
            "addressStreetInvoicing" => $societyInput->addressStreetInvoicing,
            "addressPostcodeInvoicing" => $societyInput->addressPostcodeInvoicing,
            "addressLocalityInvoicing" => $societyInput->addressLocalityInvoicing,
            "addressCountryInvoicing" => $societyInput->addressCountryInvoicing,
            "legalPersonId" => $societyInput->legalPersonId,
            "administrativePersonId" => $societyInput->administrativePersonId,
            "rhPersonId" => $societyInput->rhPersonId,
            "image" => $societyInput->image
        ];
    }
}
