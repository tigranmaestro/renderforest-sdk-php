<?php

namespace Renderforest;

use GuzzleHttp\Client;
use Renderforest\Font\Collection\FontCollection;
use Renderforest\Project\Collection\ProjectCollection;
use Renderforest\Project\Project;
use Renderforest\ProjectData\ProjectData;
use Renderforest\Sound\Collection\SoundCollection;
use Renderforest\Support\SupportTicket;
use Renderforest\Support\SupportTicketResponse;
use Renderforest\Template\Category\Collection\CategoryCollection;
use Renderforest\Template\Collection\TemplateCollection;
use Renderforest\Template\ColorPreset\Collection\ColorPresetCollection;
use Renderforest\Template\CustomColor\Collection\CustomColorCollection;
use Renderforest\Template\PluggableScreensGroup\Collection\PluggableScreensGroupCollection;
use Renderforest\Template\TemplateExtended;
use Renderforest\Template\TemplateTheme;
use Renderforest\Template\TemplateTransitions;
use Renderforest\User\User;

/**
 * Class ApiClient
 * @package Renderforest
 */
class ApiClient
{
    const SDK_VERSION = '0.3.13';
    const USER_AGENT = 'renderforest/sdk-php/' . self::SDK_VERSION;

    const API_ENDPOINT = 'https://api.renderforest.com';

    const PROJECTS_API_PATH_PREFIX = '/api/v1';
    const PROJECTS_API_PATH = self::PROJECTS_API_PATH_PREFIX . '/projects';

    const DUPLICATE_PROJECT_API_PATH_PREFIX = '/api/v1';
    const DUPLICATE_PROJECT_API_PATH = self::DUPLICATE_PROJECT_API_PATH_PREFIX . '/projects/%d/duplicate';

    const DELETE_PROJECT_VIDEOS_API_PATH_PREFIX = '/api/v1';
    const DELETE_PROJECT_VIDEOS_API_PATH = self::DELETE_PROJECT_VIDEOS_API_PATH_PREFIX . '/projects/%d/videos/%d';

    const APPLY_TEMPLATE_PRESET_PROJECT_API_PATH_PREFIX = '/api/v1';
    const APPLY_TEMPLATE_PRESET_PROJECT_API_PATH = self::APPLY_TEMPLATE_PRESET_PROJECT_API_PATH_PREFIX . '/projects/%d/apply-template-preset';

    const RENDER_PROJECT_API_PATH_PREFIX = '/api/v1';
    const RENDER_PROJECT_API_PATH = self::RENDER_PROJECT_API_PATH_PREFIX . '/projects/%d/render';

    const PROJECT_DATA_API_PATH_PREFIX = '/api/v5';
    const PROJECT_DATA_API_PATH = self::PROJECT_DATA_API_PATH_PREFIX . '/project-data';

    const TRIAL_PROJECT_API_PATH_PREFIX = '/api/v1';
    const TRIAL_PROJECT_API_PATH = self::TRIAL_PROJECT_API_PATH_PREFIX . '/projects/trial';

    const SUPPORT_API_PATH_PREFIX = '/api/v1';
    const SUPPORT_API_PATH = self::SUPPORT_API_PATH_PREFIX . '/supports/ticket';

    const CURRENT_USER_API_PATH_PREFIX = '/api/v1';
    const CURRENT_USER_API_PATH = self::CURRENT_USER_API_PATH_PREFIX . '/users/current';

    const TEMPLATE_RECOMMENDED_CUSTOM_COLORS_API_PATH_PREFIX = '/api/v1';
    const TEMPLATE_RECOMMENDED_CUSTOM_COLORS_API_PATH = self::TEMPLATE_RECOMMENDED_CUSTOM_COLORS_API_PATH_PREFIX . '/templates/%s/recommended-custom-colors';

    const TEMPLATES_API_PATH_PREFIX = '/api/v1';
    const TEMPLATES_API_PATH = self::TEMPLATES_API_PATH_PREFIX . '/templates';

    const TEMPLATE_CATEGORIES_API_PATH_PREFIX = '/api/v1';
    const TEMPLATE_CATEGORIES_API_PATH = self::TEMPLATE_CATEGORIES_API_PATH_PREFIX . '/templates/categories';

    const TEMPLATE_PRESETS_API_PATH_PREFIX = '/api/v1';
    const TEMPLATE_PRESETS_API_PATH = self::TEMPLATE_PRESETS_API_PATH_PREFIX . '/templates/%d/template-presets';

    const TEMPLATE_PLUGGABLE_SCREENS_API_PATH_PREFIX = '/api/v1';
    const TEMPLATE_PLUGGABLE_SCREENS_API_PATH = self::TEMPLATE_PLUGGABLE_SCREENS_API_PATH_PREFIX . '/templates/%d/pluggable-screens';

    const TEMPLATE_THEME_API_PATH_PREFIX = '/api/v1';
    const TEMPLATE_THEME_API_PATH = self::TEMPLATE_THEME_API_PATH_PREFIX . '/templates/%s/theme';

    const TEMPLATE_TRANSITIONS_API_PATH_PREFIX = '/api/v1';
    const TEMPLATE_TRANSITIONS_API_PATH = self::TEMPLATE_TRANSITIONS_API_PATH_PREFIX . '/templates/%s/transitions';

    const TEMPLATE_COLOR_PRESETS_API_PATH_PREFIX = '/api/v1';
    const TEMPLATE_COLOR_PRESETS_API_PATH = self::TEMPLATE_COLOR_PRESETS_API_PATH_PREFIX . '/templates/%s/color-presets';

    const FONTS_API_PATH_PREFIX = '/api/v1';
    const FONTS_API_PATH = self::FONTS_API_PATH_PREFIX . '/fonts';


    /** @var string */
    protected $apiKey;

    /** @var string */
    protected $clientId;

    /** @var Client */
    protected $httpClient;

    /**
     * ApiClient constructor.
     * @param string $apiKey
     * @param string $clientId
     */
    public function __construct(string $apiKey, string $clientId)
    {
        $this->apiKey = $apiKey;
        $this->clientId = $clientId;

        $this->httpClient = new Client();
    }

    public function updateProjectData(
        int $projectId,
        ProjectData $projectData
    ) {
        $endpoint = self::PROJECT_DATA_API_PATH;
        $uri = self::API_ENDPOINT . self::PROJECT_DATA_API_PATH . '/' . $projectId;

        $options = [
            'method' => 'PATCH',
            'headers' => [
                'Accept' => 'application/json',
                'User-Agent' => self::USER_AGENT,
            ],
            'endpoint' => $endpoint,
            'uri' => $uri,
            'json' => [
                'data' => $projectData->getArrayCopy(),
            ],
        ];

        $options = $this->setAuthorization($options);

        $response = $this->httpClient->request(
            $options['method'],
            $options['uri'],
            $options
        );

        $jsonResponse = $response->getBody()->getContents();
        $arrayResponse = \GuzzleHttp\json_decode($jsonResponse, true);

        $data = $arrayResponse['data'];
        $projectId = intval($data['projectId']);

        return $projectId;
    }

    /**
     * @param int $templateId
     * @return int
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function addProject(int $templateId): int
    {
        $endpoint = self::PROJECTS_API_PATH;
        $uri = self::API_ENDPOINT . self::PROJECTS_API_PATH;

        $options = [
            'method' => 'POST',
            'headers' => [
                'Accept' => 'application/json',
                'User-Agent' => self::USER_AGENT,
            ],
            'endpoint' => $endpoint,
            'uri' => $uri,
            'json' => [
                'templateId' => $templateId,
            ],
        ];

        $options = $this->setAuthorization($options);

        $response = $this->httpClient->request(
            $options['method'],
            $options['uri'],
            $options
        );

        $jsonResponse = $response->getBody()->getContents();
        $arrayResponse = \GuzzleHttp\json_decode($jsonResponse, true);

        $data = $arrayResponse['data'];
        $projectId = intval($data['projectId']);

        return $projectId;
    }

    /**
     * @param int $projectId
     * @param string|null $customTitle
     * @return int
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function updateProject(
        int $projectId,
        string $customTitle = null
    ): int {
        $endpoint = self::PROJECTS_API_PATH;
        $uri = self::API_ENDPOINT . self::PROJECTS_API_PATH . '/' . $projectId;

        $updateParams = [];

        if (false === is_null($customTitle)) {
            $updateParams['customTitle'] = $customTitle;
        }

        $options = [
            'method' => 'PATCH',
            'headers' => [
                'Accept' => 'application/json',
                'User-Agent' => self::USER_AGENT,
            ],
            'endpoint' => $endpoint,
            'uri' => $uri,
            'json' => $updateParams,
        ];

        $options = $this->setAuthorization($options);

        $response = $this->httpClient->request(
            $options['method'],
            $options['uri'],
            $options
        );

        $jsonResponse = $response->getBody()->getContents();
        $arrayResponse = \GuzzleHttp\json_decode($jsonResponse, true);

        $data = $arrayResponse['data'];
        $projectId = intval($data['projectId']);

        return $projectId;
    }

    /**
     * @param int $projectId
     * @return int
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function deleteSpecificProject(int $projectId): int
    {
        $endpoint = self::PROJECTS_API_PATH;
        $uri = self::API_ENDPOINT . self::PROJECTS_API_PATH . '/' . $projectId;

        $options = [
            'method' => 'DELETE',
            'headers' => [
                'Accept' => 'application/json',
                'User-Agent' => self::USER_AGENT,
            ],
            'endpoint' => $endpoint,
            'uri' => $uri,
        ];

        $options = $this->setAuthorization($options);

        $response = $this->httpClient->request(
            $options['method'],
            $options['uri'],
            $options
        );

        $jsonResponse = $response->getBody()->getContents();
        $arrayResponse = \GuzzleHttp\json_decode($jsonResponse, true);

        $data = $arrayResponse['data'];
        $affectedRows = intval($data['affectedRows']);

        return $affectedRows;
    }

    /**
     * @param int $projectId
     * @param int|null $quality
     * @return int
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function deleteSpecificProjectVideos(int $projectId, int $quality = null): int
    {
        $endpoint = self::DELETE_PROJECT_VIDEOS_API_PATH;

        if (is_null($quality)) {
            $quality = '';
        }

        $deleteProjectVideosApiPath = sprintf(
            self::DELETE_PROJECT_VIDEOS_API_PATH,
            $projectId,
            $quality
        );
        $uri = self::API_ENDPOINT . $deleteProjectVideosApiPath;

        $options = [
            'method' => 'DELETE',
            'headers' => [
                'Accept' => 'application/json',
                'User-Agent' => self::USER_AGENT,
            ],
            'endpoint' => $endpoint,
            'uri' => $uri,
        ];

        $options = $this->setAuthorization($options);

        $response = $this->httpClient->request(
            $options['method'],
            $options['uri'],
            $options
        );

        $jsonResponse = $response->getBody()->getContents();
        $arrayResponse = \GuzzleHttp\json_decode($jsonResponse, true);

        $data = $arrayResponse['data'];
        $affectedRows = intval($data['affectedRows']);

        return $affectedRows;
    }

    /**
     * @param int $projectId
     * @param int $templatePresetId
     * @return int
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function applyTemplatePresetOnProject(int $projectId, int $templatePresetId): int
    {
        $endpoint = self::APPLY_TEMPLATE_PRESET_PROJECT_API_PATH;

        $applyTemplatePresetOnProjectApiPath = sprintf(
            self::APPLY_TEMPLATE_PRESET_PROJECT_API_PATH,
            $projectId
        );
        $uri = self::API_ENDPOINT . $applyTemplatePresetOnProjectApiPath;

        $postData = [
            'presetId' => $templatePresetId,
        ];

        $options = [
            'method' => 'POST',
            'headers' => [
                'Accept' => 'application/json',
                'User-Agent' => self::USER_AGENT,
            ],
            'endpoint' => $endpoint,
            'uri' => $uri,
            'json' => $postData,
        ];

        $options = $this->setAuthorization($options);

        $response = $this->httpClient->request(
            $options['method'],
            $options['uri'],
            $options
        );

        $jsonResponse = $response->getBody()->getContents();
        $arrayResponse = \GuzzleHttp\json_decode($jsonResponse, true);

        $data = $arrayResponse['data'];
        $projectId = intval($data['projectId']);

        return $projectId;
    }

    /**
     * @param int $projectId
     * @return int
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function duplicateProject(int $projectId): int
    {
        $endpoint = self::DUPLICATE_PROJECT_API_PATH;

        $duplicateProjectApiPath = sprintf(
            self::DUPLICATE_PROJECT_API_PATH,
            $projectId
        );
        $uri = self::API_ENDPOINT . $duplicateProjectApiPath;

        $options = [
            'method' => 'POST',
            'headers' => [
                'Accept' => 'application/json',
                'User-Agent' => self::USER_AGENT,
            ],
            'endpoint' => $endpoint,
            'uri' => $uri,
        ];

        $options = $this->setAuthorization($options);

        $response = $this->httpClient->request(
            $options['method'],
            $options['uri'],
            $options
        );

        $jsonResponse = $response->getBody()->getContents();
        $arrayResponse = \GuzzleHttp\json_decode($jsonResponse, true);

        $data = $arrayResponse['data'];
        $newProjectId = intval($data['projectId']);

        return $newProjectId;
    }

    /**
     * @param int $projectId
     * @param int $quality
     * @param string|null $watermarkImageUrl
     * @return int
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function renderProject(
        int $projectId,
        int $quality,
        string $watermarkImageUrl = null
    ): int {
        $endpoint = self::RENDER_PROJECT_API_PATH;

        $renderProjectApiPath = sprintf(
            self::RENDER_PROJECT_API_PATH,
            $projectId
        );
        $uri = self::API_ENDPOINT . $renderProjectApiPath;

        $postData = [
            'quality' => $quality,
        ];

        if (false === is_null($watermarkImageUrl)) {
            $postData['watermark'] = $watermarkImageUrl;
        }

        $options = [
            'method' => 'POST',
            'headers' => [
                'Accept' => 'application/json',
                'User-Agent' => self::USER_AGENT,
            ],
            'endpoint' => $endpoint,
            'uri' => $uri,
            'json' => $postData,
        ];

        $options = $this->setAuthorization($options);

        $response = $this->httpClient->request(
            $options['method'],
            $options['uri'],
            $options
        );

        $jsonResponse = $response->getBody()->getContents();
        $arrayResponse = \GuzzleHttp\json_decode($jsonResponse, true);

        $data = $arrayResponse['data'];
        $queueId = intval($data['queueId']);

        return $queueId;
    }

    /**
     * @todo validate language code, supported codes - ar, de, en, es, fr, pt, ru, tr
     *
     * @param string|null $languageIsoCode
     * @return CategoryCollection
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function getTemplatesCategories(string $languageIsoCode = null): CategoryCollection
    {
        $endpoint = self::TEMPLATE_CATEGORIES_API_PATH;
        $uri = self::API_ENDPOINT . self::TEMPLATE_CATEGORIES_API_PATH;

        if (false === is_null($languageIsoCode)) {
            $queryParams = [
                'language' => $languageIsoCode,
            ];

            $queryString = http_build_query($queryParams);
            $uri .= '?' . $queryString;
        }

        $options = [
            'method' => 'GET',
            'headers' => [
                'Accept' => 'application/json',
                'User-Agent' => self::USER_AGENT,
            ],
            'endpoint' => $endpoint,
            'uri' => $uri,
        ];

        $httpClient = new Client();

        $response = $httpClient->request(
            $options['method'],
            $options['uri'],
            $options
        );

        $json = $response->getBody()->getContents();

        $categoryCollection = new CategoryCollection();
        $categoryCollection->exchangeJson($json);

        return $categoryCollection;
    }

    /**
     * @param int $templateId
     * @return CustomColorCollection
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function getTemplateRecommendedCustomColors(int $templateId): CustomColorCollection
    {
        $endpoint = self::TEMPLATE_RECOMMENDED_CUSTOM_COLORS_API_PATH;

        $templateRecommendedCustomColorsApiPath = sprintf(
            self::TEMPLATE_RECOMMENDED_CUSTOM_COLORS_API_PATH,
            $templateId
        );
        $uri = self::API_ENDPOINT . $templateRecommendedCustomColorsApiPath;

        $options = [
            'method' => 'GET',
            'headers' => [
                'Accept' => 'application/json',
                'User-Agent' => self::USER_AGENT,
            ],
            'endpoint' => $endpoint,
            'uri' => $uri,
        ];

        $httpClient = new Client();

        $response = $httpClient->request(
            $options['method'],
            $options['uri'],
            $options
        );

        $json = $response->getBody()->getContents();

        $customColorCollection = new CustomColorCollection();
        $customColorCollection->exchangeJson($json);

        return $customColorCollection;
    }

    /**
     * @param int $templateId
     * @return TemplatePresetCollection
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function getTemplatePresets(int $templateId): TemplatePresetCollection
    {
        $endpoint = self::TEMPLATE_PRESETS_API_PATH;

        $templatePresetsApiPath = sprintf(
            self::TEMPLATE_PRESETS_API_PATH,
            $templateId
        );
        $uri = self::API_ENDPOINT . $templatePresetsApiPath;

        $options = [
            'method' => 'GET',
            'headers' => [
                'Accept' => 'application/json',
                'User-Agent' => self::USER_AGENT,
            ],
            'endpoint' => $endpoint,
            'uri' => $uri,
        ];

        $httpClient = new Client();

        $response = $httpClient->request(
            $options['method'],
            $options['uri'],
            $options
        );

        $json = $response->getBody()->getContents();

        $templatePresetCollection = new TemplatePresetCollection();
        $templatePresetCollection->exchangeJson($json);

        return $templatePresetCollection;
    }

    /**
     * @param int $templateId
     * @return TemplateTheme
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function getTemplateTheme(int $templateId): TemplateTheme
    {
        $endpoint = self::TEMPLATE_THEME_API_PATH;

        $templateThemeApiPath = sprintf(
            self::TEMPLATE_THEME_API_PATH,
            $templateId
        );
        $uri = self::API_ENDPOINT . $templateThemeApiPath;

        $options = [
            'method' => 'GET',
            'headers' => [
                'Accept' => 'application/json',
                'User-Agent' => self::USER_AGENT,
            ],
            'endpoint' => $endpoint,
            'uri' => $uri,
        ];

        $httpClient = new Client();

        $response = $httpClient->request(
            $options['method'],
            $options['uri'],
            $options
        );

        $json = $response->getBody()->getContents();

        $templateTheme = new TemplateTheme();
        $templateTheme->exchangeJson($json);

        return $templateTheme;
    }

    /**
     * @param int $templateId
     * @return PluggableScreensGroupCollection
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function getTemplatePluggableScreens(int $templateId): PluggableScreensGroupCollection
    {
        $endpoint = self::TEMPLATE_PLUGGABLE_SCREENS_API_PATH;

        $templatePluggableScreensApiPath = sprintf(
            self::TEMPLATE_PLUGGABLE_SCREENS_API_PATH,
            $templateId
        );
        $uri = self::API_ENDPOINT . $templatePluggableScreensApiPath;

        $options = [
            'method' => 'GET',
            'headers' => [
                'Accept' => 'application/json',
                'User-Agent' => self::USER_AGENT,
            ],
            'endpoint' => $endpoint,
            'uri' => $uri,
        ];

        $httpClient = new Client();

        $response = $httpClient->request(
            $options['method'],
            $options['uri'],
            $options
        );

        $json = $response->getBody()->getContents();

        $pluggableScreensGroupCollection = new PluggableScreensGroupCollection();
        $pluggableScreensGroupCollection->exchangeJson($json);

        return $pluggableScreensGroupCollection;
    }

    /**
     * @param int $templateId
     * @return TemplateTransitions
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function getTemplateTransitions(int $templateId): TemplateTransitions
    {
        $endpoint = self::TEMPLATE_TRANSITIONS_API_PATH;

        $templateTransitionsApiPath = sprintf(
            self::TEMPLATE_TRANSITIONS_API_PATH,
            $templateId
        );
        $uri = self::API_ENDPOINT . $templateTransitionsApiPath;

        $options = [
            'method' => 'GET',
            'headers' => [
                'Accept' => 'application/json',
                'User-Agent' => self::USER_AGENT,
            ],
            'endpoint' => $endpoint,
            'uri' => $uri,
        ];

        $httpClient = new Client();

        $response = $httpClient->request(
            $options['method'],
            $options['uri'],
            $options
        );

        $json = $response->getBody()->getContents();

        $templateTransitions = new TemplateTransitions();
        $templateTransitions->exchangeJson($json);

        return $templateTransitions;
    }

    /**
     * @param int $templateId
     * @return ColorPresetCollection
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function getTemplateColorPresets(int $templateId): ColorPresetCollection
    {
        $endpoint = self::TEMPLATE_COLOR_PRESETS_API_PATH;

        $templateColorPresetsApiPath = sprintf(
            self::TEMPLATE_COLOR_PRESETS_API_PATH,
            $templateId
        );
        $uri = self::API_ENDPOINT . $templateColorPresetsApiPath;

        $options = [
            'method' => 'GET',
            'headers' => [
                'Accept' => 'application/json',
                'User-Agent' => self::USER_AGENT,
            ],
            'endpoint' => $endpoint,
            'uri' => $uri,
        ];

        $httpClient = new Client();

        $response = $httpClient->request(
            $options['method'],
            $options['uri'],
            $options
        );

        $json = $response->getBody()->getContents();

        $templateColorPresetCollection = new ColorPresetCollection();
        $templateColorPresetCollection->exchangeJson($json);

        return $templateColorPresetCollection;
    }

    /**
     * @return TemplateCollection
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function getAllTemplates(): TemplateCollection
    {
        // @todo query params
        // ?categoryId=3&equalizer=false&limit=4&offset=10

        $endpoint = self::TEMPLATES_API_PATH_PREFIX;
        $uri = self::API_ENDPOINT . self::TEMPLATES_API_PATH;

        $options = [
            'method' => 'GET',
            'headers' => [
                'Accept' => 'application/json',
                'User-Agent' => self::USER_AGENT,
            ],
            'endpoint' => $endpoint,
            'uri' => $uri,
        ];

        $httpClient = new Client();

        $response = $httpClient->request(
            $options['method'],
            $options['uri'],
            $options
        );

        $json = $response->getBody()->getContents();

        $templateCollection = new TemplateCollection();
        $templateCollection->exchangeJson($json);

        return $templateCollection;
    }

    /**
     * @param int $templateId
     * @param string|null $languageIsoCode
     * @return TemplateExtended
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function getTemplate(
        int $templateId,
        string $languageIsoCode = null
    ): TemplateExtended
    {
        $endpoint = self::TEMPLATES_API_PATH_PREFIX;
        $uri = self::API_ENDPOINT . self::TEMPLATES_API_PATH . '/' . $templateId;

        if (false === is_null($languageIsoCode)) {
            $queryParams = [
                'language' => $languageIsoCode,
            ];

            $queryString = http_build_query($queryParams);
            $uri .= '?' . $queryString;
        }

        $options = [
            'method' => 'GET',
            'headers' => [
                'Accept' => 'application/json',
                'User-Agent' => self::USER_AGENT,
            ],
            'endpoint' => $endpoint,
            'uri' => $uri,
        ];

        $httpClient = new Client();

        $response = $httpClient->request(
            $options['method'],
            $options['uri'],
            $options
        );

        $json = $response->getBody()->getContents();

        $template = new TemplateExtended();
        $template->exchangeJson($json);

        return $template;
    }

    /**
     * @return User
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getCurrentUser(): User
    {
        $endpoint = self::CURRENT_USER_API_PATH;
        $uri = self::API_ENDPOINT . self::CURRENT_USER_API_PATH;

        $options = [
            'method' => 'GET',
            'headers' => [
                'Accept' => 'application/json',
                'User-Agent' => self::USER_AGENT,
            ],
            'endpoint' => $endpoint,
            'uri' => $uri,
        ];

        $options = $this->setAuthorization($options);

        $response = $this->httpClient->request(
            $options['method'],
            $options['uri'],
            $options
        );

        $json = $response->getBody()->getContents();

        $user = new User();
        $user->exchangeJson($json);

        return $user;
    }

    /**
     * @param int $templateId
     * @return FontCollection
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getTemplateAvailableFonts(int $templateId): FontCollection
    {
        $endpoint = self::FONTS_API_PATH;
        $uri = self::API_ENDPOINT . self::FONTS_API_PATH;

        $queryParams = [
            'templateId' => $templateId,
        ];

        $queryString = http_build_query($queryParams);
        $uri .= '?' . $queryString;

        $options = [
            'method' => 'GET',
            'headers' => [
                'Accept' => 'application/json',
                'User-Agent' => self::USER_AGENT,
            ],
            'endpoint' => $endpoint,
            'uri' => $uri,
        ];

        $options = $this->setAuthorization($options);

        $response = $this->httpClient->request(
            $options['method'],
            $options['uri'],
            $options
        );

        $json = $response->getBody()->getContents();

        $fontCollection = new FontCollection();
        $fontCollection->exchangeJson($json);

        return $fontCollection;
    }

    /**
     * @param SupportTicket $supportTicket
     * @return SupportTicketResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function createSupportTicket(SupportTicket $supportTicket): SupportTicketResponse
    {
        $endpoint = self::SUPPORT_API_PATH;
        $uri = self::API_ENDPOINT . self::SUPPORT_API_PATH;

        $options = [
            'method' => 'POST',
            'headers' => [
                'Accept' => 'application/json',
                'User-Agent' => self::USER_AGENT,
            ],
            'endpoint' => $endpoint,
            'uri' => $uri,
            'json' => $supportTicket->getArrayCopy(),
        ];

        $options = $this->setAuthorization($options);

        $response = $this->httpClient->request(
            $options['method'],
            $options['uri'],
            $options
        );

        $json = $response->getBody()->getContents();

        $supportTicketResponse = new SupportTicketResponse();
        $supportTicketResponse->exchangeJson($json);

        return $supportTicketResponse;
    }

    /**
     * @param int $projectId
     * @return ProjectData
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getProjectData(int $projectId): ProjectData
    {
        $endpoint = self::PROJECT_DATA_API_PATH . '/' . $projectId;
        $uri = self::API_ENDPOINT . self::PROJECT_DATA_API_PATH . '/' . $projectId;

        $options = [
            'method' => 'GET',
            'headers' => [
                'Accept' => 'application/json',
                'User-Agent' => self::USER_AGENT,
            ],
            'endpoint' => $endpoint,
            'uri' => $uri,
        ];

        $options = $this->setAuthorization($options);

        $response = $this->httpClient->request(
            $options['method'],
            $options['uri'],
            $options
        );

        $json = $response->getBody()->getContents();

        $projectData = new ProjectData();
        $projectData->exchangeJson($json);

        return $projectData;
    }

    /**
     * @param int $templateId
     * @return ProjectData
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function getTrialProject(int $templateId): ProjectData
    {
        $endpoint = self::TRIAL_PROJECT_API_PATH;
        $uri = self::API_ENDPOINT . self::TRIAL_PROJECT_API_PATH;

        $queryParams = [
            'templateId' => $templateId,
        ];

        $queryString = http_build_query($queryParams);
        $uri .= '?' . $queryString;

        $options = [
            'method' => 'GET',
            'headers' => [
                'Accept' => 'application/json',
                'User-Agent' => self::USER_AGENT,
            ],
            'endpoint' => $endpoint,
            'uri' => $uri,
        ];

        $httpClient = new Client();

        $response = $httpClient->request(
            $options['method'],
            $options['uri'],
            $options
        );

        $json = $response->getBody()->getContents();

        $trialProject = new ProjectData();
        $trialProject->exchangeJson($json);

        return $trialProject;
    }

    /**
     * @param int $limit
     * @param int $offset
     * @param bool $includeApiProjects
     * @param string $order
     * @param string $orderBy
     * @param string|null $search
     * @return ProjectCollection
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getAllProjects(
        int $limit = null,
        int $offset = null,
        bool $includeApiProjects = null,
        string $order = null,
        string $orderBy = null,
        string $search = null
    ): ProjectCollection {
        $endpoint = self::PROJECTS_API_PATH;
        $uri = self::API_ENDPOINT . self::PROJECTS_API_PATH;

        $queryParams = [];

        if (false === is_null($limit)) {
            $queryParams['limit'] = $limit;
        }
        if (false === is_null($offset)) {
            $queryParams['offset'] = $offset;
        }
        if (false === is_null($includeApiProjects)) {
            $queryParams['includeApiProjects'] = $includeApiProjects ? 'true' : 'false';
        }
        if (false === is_null($order)) {
            $queryParams['order'] = $order;
        }
        if (false === is_null($orderBy)) {
            $queryParams['orderBy'] = $orderBy;
        }
        if (false === is_null($search)) {
            $queryParams['search'] = $search;
        }

        $queryString = http_build_query($queryParams);
        $uri .= '?' . $queryString;

        $options = [
            'method' => 'GET',
            'headers' => [
                'Accept' => 'application/json',
                'User-Agent' => self::USER_AGENT,
            ],
            'endpoint' => $endpoint,
            'uri' => $uri,
        ];

        $options = $this->setAuthorization($options);

        $response = $this->httpClient->request(
            $options['method'],
            $options['uri'],
            $options
        );

        $json = $response->getBody()->getContents();

        $projectCollection = new ProjectCollection();
        $projectCollection->exchangeJson($json);

        return $projectCollection;
    }

    /**
     * @param int $projectId
     * @return Project
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getProject(int $projectId): Project
    {
        $endpoint = self::PROJECTS_API_PATH . '/' . $projectId;
        $uri = self::API_ENDPOINT . self::PROJECTS_API_PATH . '/' . $projectId;

        $options = [
            'method' => 'GET',
            'headers' => [
                'Accept' => 'application/json',
                'User-Agent' => self::USER_AGENT,
            ],
            'endpoint' => $endpoint,
            'uri' => $uri,
        ];

        $options = $this->setAuthorization($options);

        $response = $this->httpClient->request(
            $options['method'],
            $options['uri'],
            $options
        );

        $json = $response->getBody()->getContents();

        $project = new Project();
        $project->exchangeJson($json);

        return $project;
    }

    /**
     * @param $options
     * @return array
     */
    private function setAuthorization($options): array
    {
        $signKey = $this->apiKey;
        $clientId = $this->clientId;

        $opts = $options ? $options : [];
        $headers = isset($opts['headers']) ? $opts['headers'] : [];
        $headers['nonce'] = $this->generateNonce();
        $headers['clientid'] = $clientId;
        $headers['timestamp'] = $this->dateNow();
        $parsedUrl = parse_url(isset($opts['uri']) ? $opts['uri'] : '');
        $query = isset($parsedUrl['query']) ? $parsedUrl['query'] : '';
        $path = $query ? $parsedUrl['path'] . '?' . $query : $parsedUrl['path'];

        $headers['authorization'] = $this->generateHash([
            'clientId' => $clientId,
            'path' => $path ? $path : '',
            'qs' => $query ? $query : '',
            'body' => isset($opts['json']) ? json_encode($opts['json'], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) : '{}',
            'nonce' => $headers['nonce'],
            'timestamp' => $headers['timestamp']
        ], $signKey);
        $opts['headers'] = $headers;

        return $opts;
    }

    /**
     * Generate timestamp with milliseconds.
     *
     * @return float
     */
    private function dateNow(): float
    {
        return round(microtime(true) * 1000);
    }

    /**
     * Creates keyed-hash message authentication code (HMAC) based on given `$text` and `$key`.
     * @param string $text
     * @param string $key
     * @return string
     */
    private function createHMAC($text, $key)
    {
        return hash_hmac("SHA512", $text, $key);
    }

    /**
     * Generates `HMAC` based on given `$options` and `$key`.
     * Source is defined as combination of clientId, path, qs, body, nonce and timestamp respectively.
     * @param array $options
     * @param string $key
     * @return string
     */
    private function generateHash($options, $key)
    {
        $clientId = $options['clientId'];
        $qs = $options['qs'];
        $path = $options['path'];
        $body = $options['body'];
        $nonce = $options['nonce'];
        $timestamp = $options['timestamp'];
        $hashSource = $clientId . $path . $qs . $body . $nonce . $timestamp;

        return $this->createHMAC($hashSource, $key);
    }

    /**
     * Generates nonce.
     *  Creates timestamp
     *  Gets the last 6 chars of the timestamp
     *  Generates random number between 10-99
     *  Combined the last two ones.
     * @returns string
     */
    private function generateNonce()
    {
        $timestamp = $this->dateNow();
        $str = substr($timestamp, strlen($timestamp) - 6);
        $suffix = rand(10, 99);

        return $str . $suffix;
    }
}
