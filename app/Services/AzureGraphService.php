<?php

namespace App\Services;

use App\Helpers\PkceHelper;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Session;
use Microsoft\Graph\GraphServiceClient;
use Microsoft\Graph\Generated\Models\ODataErrors\ODataError;
use Microsoft\Kiota\Authentication\Oauth\AuthorizationCodeContext;
use Microsoft\Kiota\Authentication\PhpLeagueAccessTokenProvider;
use League\OAuth2\Client\Provider\GenericProvider;
use League\OAuth2\Client\Token\AccessToken;

class AzureGraphService
{
    private $clientId;
    private $clientSecret;
    private $redirectUri;
    private $tenantId;

    private $provider;
    private $scopes = [
        'Mail.Read',
        'Mail.ReadBasic',
        'Mail.ReadWrite',
        'openid',
        'profile',
        'User.Read',
        'email',
        'offline_access'
    ];

    public function __construct()
    {
        $this->clientId = config('services.azure.client_id');
        $this->clientSecret = config('services.azure.client_secret');
        $this->redirectUri = config('services.azure.redirect_uri');
        $this->tenantId = config('services.azure.tenant_id', 'common');

        $this->provider = new GenericProvider([
            'clientId' => $this->clientId,
            'clientSecret' => $this->clientSecret,
            'redirectUri' =>  $this->redirectUri,
            'urlAuthorize' => "https://login.microsoftonline.com/{$this->tenantId}/oauth2/v2.0/authorize",
            'urlAccessToken' => "https://login.microsoftonline.com/{$this->tenantId}/oauth2/v2.0/token",
            'urlResourceOwnerDetails' => 'https://graph.microsoft.com/v1.0/me',
            'scopes' => implode(' ', $this->scopes)
        ]);
    }

    /**
     * Get authorization URL for OAuth2 flow
     */
    public function getAuthorizationUrl($state = null)
    {
        if (!$state) {
            $state = bin2hex(random_bytes(16));
        }

        $authUrl = $this->provider->getAuthorizationUrl([
            'scope' => implode(' ', $this->scopes)
        ]);

        // $codeVerifier = PkceHelper::generateCodeVerifier();
        // $codeChallenge = PkceHelper::generateCodeChallenge($codeVerifier);

        // // Store code verifier with expiration (10 minutes)
        // Redis::setex("azure_code_verifier_{$state}", 600, $codeVerifier);

        // $params = [
        //     'client_id' => $this->clientId,
        //     'response_type' => 'code',
        //     'redirect_uri' => $this->redirectUri,
        //     'scope' => implode(' ', $this->scopes),
        //     'response_mode' => 'query',
        //     'state' => $state,
        //     'code_challenge' => $codeChallenge,
        //     'code_challenge_method' => 'S256',
        //     'prompt' => 'select_account', // Force account selection
        // ];

        // $authUrl = "https://login.microsoftonline.com/{$this->tenantId}/oauth2/v2.0/authorize?"
        //     . http_build_query($params);

        return ['url' => $authUrl, 'state' => $state];
    }

    /**
     * Exchange authorization code for access token
     */
    public function getAccessToken($code, $state)
    {
        $tokenUrl = "https://login.microsoftonline.com/{$this->tenantId}/oauth2/v2.0/token";
        $codeVerifier = Redis::get("azure_code_verifier_{$state}");

        if (!$codeVerifier) {
            throw new \Exception('Code verifier not found or expired for state: ' . $state);
        }

        $params = [
            'client_id' => $this->clientId,
            'code' => $code,
            'code_verifier' => $codeVerifier,
            'redirect_uri' => $this->redirectUri,
            'grant_type' => 'authorization_code',
        ];

        logger()->debug('Token request params', array_merge($params, ['client_secret' => 'HIDDEN']));

        try {
            $response = Http::asForm()->post($tokenUrl, $params);

            if (!$response->successful()) {
                logger()->error('Token request failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                throw new \Exception('Failed to get access token: ' . $response->body());
            }

            $tokenData = $response->json();

            // Clean up code verifier
            Redis::del("azure_code_verifier_{$state}");

            logger()->debug('Token obtained successfully', [
                'expires_in' => $tokenData['expires_in'] ?? 'unknown',
                'scope' => $tokenData['scope'] ?? 'unknown'
            ]);

            return $tokenData;
        } catch (\Exception $e) {
            logger()->error('Token request exception', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Create Graph Service Client with access token
     */
    public function createGraphClient($accessToken)
    {
        try {
            // Create OAuth2 provider
            $oauthProvider = new GenericProvider([
                'clientId' => $this->clientId,
                'clientSecret' => $this->clientSecret,
                'redirectUri' => $this->redirectUri,
                'urlAuthorize' => "https://login.microsoftonline.com/{$this->tenantId}/oauth2/v2.0/authorize",
                'urlAccessToken' => "https://login.microsoftonline.com/{$this->tenantId}/oauth2/v2.0/token",
                'urlResourceOwnerDetails' => '',
                'scopes' => ['User.Read', 'Mail.Read']
            ]);

            // Create access token object
            $accessTokenObj = new AccessToken([
                'access_token' => $accessToken,
                'expires' => time() + 3600 // Default 1 hour expiry
            ]);

            // Create token provider
            // $tokenProvider = new PhpLeagueAccessTokenProvider($oauthProvider, $accessTokenObj);

            // Create Graph service client
            // $graphServiceClient = new GraphServiceClient($tokenProvider);

            logger()->debug('Graph client created successfully');

            // return $graphServiceClient;
            return null;
        } catch (\Exception $e) {
            logger()->error('Failed to create Graph client', ['error' => $e->getMessage()]);
            throw new \Exception('Failed to create Graph client: ' . $e->getMessage());
        }
    }

    /**
     * Get user's email messages
     */
    public function getMessages($accessToken, $limit = 10)
    {
        // try {
        //     $graphServiceClient = $this->createGraphClient($accessToken);

        //    logger()->debug('Fetching messages', ['limit' => $limit]);

        //     // $requestConfiguration = new \Microsoft\Graph\Generated\Me\Messages\MessagesRequestBuilderGetRequestConfiguration();
        //     $requestConfiguration->queryParameters = new \Microsoft\Graph\Generated\Me\Messages\MessagesRequestBuilderGetQueryParameters();
        //     $requestConfiguration->queryParameters->top = $limit;
        //     $requestConfiguration->queryParameters->select = ['subject', 'from', 'receivedDateTime', 'bodyPreview', 'isRead'];
        //     $requestConfiguration->queryParameters->orderby = ['receivedDateTime desc'];

        //     $messages = $graphServiceClient->me()->messages()->get($requestConfiguration)->wait();

        //     $messageArray = [];
        //     if ($messages && $messages->getValue()) {
        //         foreach ($messages->getValue() as $message) {
        //             $messageArray[] = [
        //                 'id' => $message->getId(),
        //                 'subject' => $message->getSubject(),
        //                 'from' => $message->getFrom() ? $message->getFrom()->getEmailAddress()->getAddress() : null,
        //                 'fromName' => $message->getFrom() ? $message->getFrom()->getEmailAddress()->getName() : null,
        //                 'receivedDateTime' => $message->getReceivedDateTime() ? $message->getReceivedDateTime()->format('Y-m-d H:i:s') : null,
        //                 'bodyPreview' => $message->getBodyPreview(),
        //                 'isRead' => $message->getIsRead()
        //             ];
        //         }
        //     }

        //    logger()->debug('Messages fetched successfully', ['count' => count($messageArray)]);

        //     return $messageArray;
        // } catch (ODataError $e) {
        //    logger()->error('Graph API OData Error', [
        //         'error' => $e->getMessage(),
        //         'code' => $e->getError() ? $e->getError()->getCode() : 'unknown'
        //     ]);
        //     throw new \Exception('Graph API Error: ' . $e->getMessage());
        // } catch (\Exception $e) {
        //    logger()->error('Messages fetch error', ['error' => $e->getMessage()]);
        //     throw new \Exception('Failed to fetch messages: ' . $e->getMessage());
        // }
        return null;
    }

    /**
     * Get user profile
     */
    public function getUserProfile($accessToken)
    {
        $token = '';
        // try {
        //     $graphServiceClient = $this->createGraphClient($accessToken);

        //    logger()->debug('Fetching user profile');

        //     $requestConfiguration = new \Microsoft\Graph\Generated\Me\MeRequestBuilderGetRequestConfiguration();
        //     $requestConfiguration->queryParameters = new \Microsoft\Graph\Generated\Me\MeRequestBuilderGetQueryParameters();
        //     $requestConfiguration->queryParameters->select = ['id', 'displayName', 'mail', 'userPrincipalName', 'jobTitle', 'department'];

        //     $user = $graphServiceClient->me()->get($requestConfiguration)->wait();

        //     $userProfile = [
        //         'id' => $user->getId(),
        //         'displayName' => $user->getDisplayName(),
        //         'email' => $user->getMail() ?: $user->getUserPrincipalName(),
        //         'userPrincipalName' => $user->getUserPrincipalName(),
        //         'jobTitle' => $user->getJobTitle(),
        //         'department' => $user->getDepartment()
        //     ];

        //    logger()->debug('User profile fetched successfully', ['user_id' => $userProfile['id']]);

        //     return $userProfile;
        // } catch (ODataError $e) {
        //    logger()->error('Graph API OData Error', [
        //         'error' => $e->getMessage(),
        //         'code' => $e->getError() ? $e->getError()->getCode() : 'unknown'
        //     ]);
        //     throw new \Exception('Graph API Error: ' . $e->getMessage());
        // } catch (\Exception $e) {
        //    logger()->error('User profile fetch error', ['error' => $e->getMessage()]);
        //     throw new \Exception('Failed to fetch user profile: ' . $e->getMessage());
        // }
        return null;
    }

    /**
     * Refresh access token using refresh token
     */
    public function refreshAccessToken($refreshToken)
    {
        $tokenUrl = "https://login.microsoftonline.com/{$this->tenantId}/oauth2/v2.0/token";

        $params = [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'refresh_token' => $refreshToken,
            'grant_type' => 'refresh_token',
        ];

        try {
            $response = Http::asForm()->post($tokenUrl, $params);

            if (!$response->successful()) {
                logger()->error('Token refresh failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                throw new \Exception('Failed to refresh access token: ' . $response->body());
            }

            $tokenData = $response->json();
            logger()->debug('Token refreshed successfully');

            return $tokenData;
        } catch (\Exception $e) {
            logger()->error('Token refresh exception', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Validate access token
     */
    public function validateToken($accessToken)
    {
        // try {
        //     $graphServiceClient = $this->createGraphClient($accessToken);
        //     $user = $graphServiceClient->me()->get()->wait();
        //     return $user ? true : false;
        // } catch (\Exception $e) {
        //    logger()->debug('Token validation failed', ['error' => $e->getMessage()]);
        //     return false;
        // }
        return null;
    }
}
