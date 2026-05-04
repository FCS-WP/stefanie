<?php
namespace Rio_OneMap_Search\Services;

if (!defined('ABSPATH')) exit;

class OneMapClient
{
    private string $endpoint = 'https://www.onemap.gov.sg/api/common/elastic/search';

    public function search(string $search_val): array
    {
        $token_service = new OneMapTokenService();
        $token = $token_service->token();

        if ($token === '') {
            return ['success' => false, 'message' => 'OneMap API token is missing. Please add a token or OneMap email/password in settings.'];
        }

        $url = add_query_arg([
            'searchVal' => $search_val,
            'returnGeom' => 'Y',
            'getAddrDetails' => 'Y',
            'pageNum' => 1,
        ], $this->endpoint);

        $response = $this->request($url, $token);

        if (is_wp_error($response)) {
            return ['success' => false, 'message' => $response->get_error_message()];
        }

        $code = wp_remote_retrieve_response_code($response);
        if ($code === 401 || $code === 403) {
            $token = $token_service->token(true);
            if ($token !== '') {
                $response = $this->request($url, $token);
                if (is_wp_error($response)) {
                    return ['success' => false, 'message' => $response->get_error_message()];
                }
                $code = wp_remote_retrieve_response_code($response);
            }
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);

        if ($code < 200 || $code >= 300) {
            return ['success' => false, 'message' => 'OneMap API error: HTTP ' . $code];
        }

        if (empty($body['results']) || !is_array($body['results'])) {
            return ['success' => false, 'message' => 'No address found.'];
        }

        $results = array_values(array_filter(array_map(function ($item) {
            if (empty($item['LATITUDE']) || empty($item['LONGITUDE'])) {
                return null;
            }

            return [
                'search_value' => sanitize_text_field($item['SEARCHVAL'] ?? ''),
                'address' => sanitize_text_field($item['ADDRESS'] ?? ''),
                'postal' => sanitize_text_field($item['POSTAL'] ?? ''),
                'lat' => (float) $item['LATITUDE'],
                'lng' => (float) $item['LONGITUDE'],
            ];
        }, $body['results'])));

        return ['success' => true, 'results' => $results];
    }

    private function request(string $url, string $token)
    {
        return wp_remote_get($url, [
            'timeout' => 15,
            'headers' => [
                'Authorization' => $token,
                'Accept' => 'application/json',
            ],
        ]);
    }
}
