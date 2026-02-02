<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

class OutlookLoginController extends Controller
{
    private array $config;
    private string $authEndpoint = 'https://login.microsoftonline.com';
    private array $requiredScopes = [
        'openid',
        'profile',
        'email',
        'User.Read',
    ];

    public function __construct()
    {
        $this->config = [
            'client_id' => config('services.azure.client_id'),
            'client_secret' => config('services.azure.client_secret'),
            'tenant_id' => config('services.azure.tenant_id', 'common'),
            'redirect_uri' => config('services.azure.login_redirect_uri', config('app.url') . '/auth/outlook/login-callback'),
        ];
    }

    public function redirect()
    {
        try {
            $state = Str::random(40);
            $codeVerifier = $this->generateCodeVerifier();
            $codeChallenge = $this->generateCodeChallenge($codeVerifier);

            Redis::setex("azure_login_state_{$state}", 600, json_encode([
                'code_verifier' => $codeVerifier,
                'created_at' => now()->toISOString()
            ]));

            $authParams = [
                'client_id' => $this->config['client_id'],
                'response_type' => 'code',
                'redirect_uri' => $this->config['redirect_uri'],
                'scope' => implode(' ', $this->requiredScopes),
                'state' => $state,
                'code_challenge' => $codeChallenge,
                'code_challenge_method' => 'S256',
                'response_mode' => 'query',
                'prompt' => 'select_account',
            ];

            $authUrl = "{$this->authEndpoint}/{$this->config['tenant_id']}/oauth2/v2.0/authorize?" . http_build_query($authParams);

            return redirect()->away($authUrl);
        } catch (Exception $e) {
            return redirect()->route('login')->withErrors([
                'outlook' => 'Unable to connect to Microsoft. Please try again later.'
            ]);
        }
    }

    public function callback(Request $request)
    {
        try {
            if ($request->has('error')) {
                $error = $request->get('error');
                $errorDescription = $request->get('error_description', 'Authentication was denied.');

                return redirect()->route('login')->withErrors([
                    'outlook' => $this->getErrorMessage($error)
                ]);
            }

            if (!$request->has('code')) {
                return redirect()->route('login')->withErrors([
                    'outlook' => 'No authorization code received from Microsoft.'
                ]);
            }

            $code = $request->get('code');
            $state = $request->get('state');

            $pkceData = Redis::get("azure_login_state_{$state}");
            if (!$pkceData) {
                return redirect()->route('login')->withErrors([
                    'outlook' => 'Session expired. Please try again.'
                ]);
            }

            $pkceInfo = json_decode($pkceData, true);
            $codeVerifier = $pkceInfo['code_verifier'];

            Redis::del("azure_login_state_{$state}");

            $tokenData = $this->exchangeCodeForToken($code, $codeVerifier);
            if (!$tokenData) {
                return redirect()->route('login')->withErrors([
                    'outlook' => 'Failed to authenticate with Microsoft. Please try again.'
                ]);
            }

            $profile = $this->getMicrosoftProfile($tokenData['access_token']);
            if (!$profile) {
                return redirect()->route('login')->withErrors([
                    'outlook' => 'Failed to retrieve your profile from Microsoft.'
                ]);
            }

            $user = $this->findOrBindUser($profile);
            if (!$user) {
                return redirect()->route('login')->withErrors([
                    'outlook' => 'No account found with this email address. Please contact your administrator.'
                ]);
            }

            Auth::login($user, true);

            $user->update(['last_login' => now()]);

            return redirect()->intended(route('dashboard.index'));
        } catch (Exception $e) {
            return redirect()->route('login')->withErrors([
                'outlook' => 'An error occurred during sign in. Please try again.'
            ]);
        }
    }

    private function exchangeCodeForToken(string $code, string $codeVerifier): ?array
    {
        try {
            $tokenUrl = "{$this->authEndpoint}/{$this->config['tenant_id']}/oauth2/v2.0/token";

            $response = Http::asForm()->timeout(30)->post($tokenUrl, [
                'client_id' => $this->config['client_id'],
                'client_secret' => $this->config['client_secret'],
                'code' => $code,
                'redirect_uri' => $this->config['redirect_uri'],
                'grant_type' => 'authorization_code',
                'code_verifier' => $codeVerifier
            ]);

            if (!$response->successful()) {
                Log::error('Token exchange failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return null;
            }

            return $response->json();
        } catch (Exception $e) {
            Log::error('Token exchange exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    private function getMicrosoftProfile(string $accessToken): ?array
    {
        try {
            $response = Http::withToken($accessToken)
                ->timeout(30)
                ->get('https://graph.microsoft.com/v1.0/me');

            if (!$response->successful()) {
                return null;
            }

            return $response->json();
        } catch (Exception $e) {
            return null;
        }
    }

    private function findOrBindUser(array $profile): ?User
    {
        $azureId = $profile['id'] ?? null;
        $email = $profile['mail'] ?? $profile['userPrincipalName'] ?? null;

        if (!$email) {
            return null;
        }

        if ($azureId) {
            $user = User::where('azure_id', $azureId)->first();
            if ($user) {
                return $user;
            }
        }

        $user = User::where('email', $email)->first();

        if ($user && $azureId) {
            $user->update(['azure_id' => $azureId]);
        }

        return $user;
    }

    private function generateCodeVerifier(): string
    {
        return Str::random(128);
    }

    private function generateCodeChallenge(string $verifier): string
    {
        $hash = hash('sha256', $verifier, true);
        return rtrim(strtr(base64_encode($hash), '+/', '-_'), '=');
    }

    private function getErrorMessage(string $error): string
    {
        return match ($error) {
            'access_denied' => 'You denied access to the application.',
            'invalid_request' => 'Invalid authentication request.',
            'unauthorized_client' => 'Application is not authorized.',
            'server_error' => 'Microsoft server error. Please try again later.',
            'temporarily_unavailable' => 'Microsoft is temporarily unavailable. Please try again later.',
            default => 'Authentication failed. Please try again.'
        };
    }
}
