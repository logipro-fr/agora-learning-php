<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Service\Society;

use AgoraLearningPhp\DTO\Input\Person\PersonInput;
use AgoraLearningPhp\DTO\Input\Society\SocietyInput;
use AgoraLearningPhp\DTO\Output\Society\SocietyOutput;
use AgoraLearningPhp\Enum\RequestMethod;
use AgoraLearningPhp\Service\ClientCore\ApiService;
use AgoraLearningPhp\Service\ClientCore\ApiUrls;
use AgoraLearningPhp\Service\Person\Person;
use AgoraLearningPhp\Service\Tools\JsonHandler;
use Symfony\Contracts\HttpClient\ResponseInterface;

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

    public function getSociety(string $uuid): SocietyOutput
    {
        $body = '';
        $response = $this->httpClient->request(RequestMethod::GET, ApiUrls::getGetSociety($uuid), $body);
        return $this->convertResponseToDTO($response);
    }

    public function postSociety(SocietyInput $societyInput): SocietyOutput
    {
        $body = JsonHandler::jsonEncode(
            [
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
                'legalPerson' => $this->serializePersonInput($societyInput->legalPerson),
                'administrativePerson' => $this->serializePersonInput($societyInput->administrativePerson),
                'rhPerson' => $this->serializePersonInput($societyInput->rhPerson),
                "image" => $societyInput->image
            ]
        );
        $response = $this->httpClient->request(RequestMethod::POST, ApiUrls::getCreateSociety(), $body);
        return $this->convertResponseToDTO($response);
    }

    protected function convertResponseToDTO(ResponseInterface $response): SocietyOutput
    {
        $responseData = JsonHandler::jsonDecode($response->getContent(false), true);
        if (200 === $response->getStatusCode()) {
            return self::convertDataToDTO($responseData);
        }

        $message = array_key_exists('detail', $responseData) ? $responseData['detail'] : 'An unexpected error occured';
        throw new \AgoraLearningPhp\Exception\AgoraLearningClientException($message);
    }

    /**
     * @return array<int, SocietyOutput>
     */
    protected function convertResponseToDTOarray(ResponseInterface $response): array
    {
        $result = [];
        $responseData = JsonHandler::jsonDecode($response->getContent(false), true);
        if (200 === $response->getStatusCode()) {
            foreach ($responseData as $data) {
                $result[] = self::convertDataToDTO($data);
            }
            return $result;
        }

        $message = array_key_exists('detail', $responseData) ? $responseData['detail'] : 'An unexpected error occured';
        throw new \AgoraLearningPhp\Exception\AgoraLearningClientException($message);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function convertDataToDTO(array $data): SocietyOutput
    {
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
        $image = $data['image'] ?? null;

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
            $data['id'],
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
            $image
        );
    }

    /**
     * @return array<string, string>
     */
    private function serializePersonInput(?PersonInput $person): ?array
    {
        if ($person === null) {
            return null;
        }
        return [
            'familyName'      => $person->familyName,
            'givenName'       => $person->givenName,
            'gender'          => $person->gender,
            'birthDate'       => $person->birthDate ? $person->birthDate->format(self::DATETIME_FORMAT) : null,
            'jobTitle'        => $person->jobTitle,
            'addressStreet'   => $person->addressStreet,
            'addressPostcode' => $person->addressPostcode,
            'addressLocality' => $person->addressLocality,
            'addressCountry'  => $person->addressCountry,
            'telephone'       => $person->telephone,
            'email'           => $person->email,
            'image'           => $person->image,
        ];
    }
}
