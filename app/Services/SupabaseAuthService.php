<?php

namespace App\Services;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class SupabaseAuthService
{
    private Client $client;
    private string $supabaseUrl;
    private string $supabaseKey;

    public function __construct()
    {
        $this->client = new Client();
        $this->supabaseUrl = config('services.supabase.url');
        $this->supabaseKey = config('services.supabase.anon_key');
    }

    /**
     * Sign in with email and password
     *
     * @param string $email
     * @param string $password
     * @return array
     * @throws Exception
     */
    public function signIn(string $email, string $password): array
    {
        try {
            $response = $this->client->post($this->supabaseUrl . '/auth/v1/token?grant_type=password', [
                'headers' => [
                    'apikey' => $this->supabaseKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'email' => $email,
                    'password' => $password,
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            if ($response->getStatusCode() !== 200) {
                throw new Exception('Authentication failed');
            }

            return $data;
        } catch (GuzzleException $e) {
            Log::error('Supabase authentication error: ' . $e->getMessage());
            throw new Exception('Authentication service unavailable');
        }
    }

    /**
     * Verify JWT token
     *
     * @param string $token
     * @return array|null
     */
    public function verifyToken(string $token): ?array
    {
        try {
            $response = $this->client->get($this->supabaseUrl . '/auth/v1/user', [
                'headers' => [
                    'apikey' => $this->supabaseKey,
                    'Authorization' => 'Bearer ' . $token,
                ],
            ]);

            if ($response->getStatusCode() === 200) {
                return json_decode($response->getBody()->getContents(), true);
            }

            return null;
        } catch (GuzzleException $e) {
            Log::error('Token verification error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Sign up new user
     *
     * @param string $email
     * @param string $password
     * @param array $metadata
     * @return array
     * @throws Exception
     */
    public function signUp(string $email, string $password, array $metadata = []): array
    {
        try {
            $response = $this->client->post($this->supabaseUrl . '/auth/v1/signup', [
                'headers' => [
                    'apikey' => $this->supabaseKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'email' => $email,
                    'password' => $password,
                    'data' => $metadata,
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            if ($response->getStatusCode() !== 200) {
                throw new Exception('User registration failed');
            }

            return $data;
        } catch (GuzzleException $e) {
            Log::error('Supabase signup error: ' . $e->getMessage());
            throw new Exception('Registration service unavailable');
        }
    }

    /**
     * Sign out user
     *
     * @param string $token
     * @return bool
     */
    public function signOut(string $token): bool
    {
        try {
            $response = $this->client->post($this->supabaseUrl . '/auth/v1/logout', [
                'headers' => [
                    'apikey' => $this->supabaseKey,
                    'Authorization' => 'Bearer ' . $token,
                ],
            ]);

            return $response->getStatusCode() === 204;
        } catch (GuzzleException $e) {
            Log::error('Supabase logout error: ' . $e->getMessage());
            return false;
        }
    }
}