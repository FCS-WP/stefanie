<?php
namespace Rio_OneMap_Search\Services;

if (!defined('ABSPATH')) exit;

class OneMapTokenService
{
    private string $endpoint = 'https://www.onemap.gov.sg/api/auth/post/getToken';

    public function token(bool $force_refresh = false): string
    {
        $token = trim((string) get_option('rio_onemap_api_token', ''));
        $expires_at = (int) get_option('rio_onemap_token_expires_at', 0);

        if (!$force_refresh && $token !== '' && ($expires_at === 0 || $expires_at > time() + 300)) {
            return $token;
        }

        $refresh = $this->refresh();
        if ($refresh['success']) {
            return $refresh['token'];
        }

        return $force_refresh ? '' : $token;
    }

    public function refresh(): array
    {
        $email = trim((string) get_option('rio_onemap_api_email', ''));
        $password = (string) get_option('rio_onemap_api_password', '');

        if ($email === '' || $password === '') {
            return [
                'success' => false,
                'token' => '',
                'message' => 'OneMap API email and password are missing.',
            ];
        }

        $response = wp_remote_post($this->endpoint, [
            'timeout' => 15,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
            'body' => wp_json_encode([
                'email' => $email,
                'password' => $password,
            ]),
        ]);

        if (is_wp_error($response)) {
            return [
                'success' => false,
                'token' => '',
                'message' => $response->get_error_message(),
            ];
        }

        $code = wp_remote_retrieve_response_code($response);
        $body = json_decode(wp_remote_retrieve_body($response), true);

        if ($code < 200 || $code >= 300 || !is_array($body) || empty($body['access_token'])) {
            return [
                'success' => false,
                'token' => '',
                'message' => is_array($body) && !empty($body['error'])
                    ? sanitize_text_field($body['error'])
                    : 'OneMap token refresh failed: HTTP ' . $code,
            ];
        }

        $token = sanitize_text_field($body['access_token']);
        update_option('rio_onemap_api_token', $token);
        update_option('rio_onemap_token_expires_at', $this->expiry_timestamp($body));

        return [
            'success' => true,
            'token' => $token,
            'message' => 'OneMap API token refreshed.',
        ];
    }

    private function expiry_timestamp(array $body): int
    {
        if (!empty($body['expiry_timestamp']) && is_numeric($body['expiry_timestamp'])) {
            return (int) $body['expiry_timestamp'];
        }

        if (!empty($body['expires_in']) && is_numeric($body['expires_in'])) {
            return time() + (int) $body['expires_in'];
        }

        return 0;
    }
}
