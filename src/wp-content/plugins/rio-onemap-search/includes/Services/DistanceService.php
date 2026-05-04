<?php
namespace Rio_OneMap_Search\Services;

if (!defined('ABSPATH')) exit;

class DistanceService
{
    private string $route_endpoint = 'https://www.onemap.gov.sg/api/public/routingsvc/route';

    public function distance_km(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earth_radius = 6371;
        $d_lat = deg2rad($lat2 - $lat1);
        $d_lng = deg2rad($lng2 - $lng1);

        $a = sin($d_lat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($d_lng / 2) ** 2;
        return $earth_radius * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

    public function driving_route(float $lat1, float $lng1, float $lat2, float $lng2): ?array
    {
        $token_service = new OneMapTokenService();
        $token = $token_service->token();

        if ($token === '') {
            return null;
        }

        $url = add_query_arg([
            'start' => $lat1 . ',' . $lng1,
            'end' => $lat2 . ',' . $lng2,
            'routeType' => 'drive',
        ], $this->route_endpoint);

        $response = $this->request_route($url, $token);

        if (is_wp_error($response)) {
            return null;
        }

        $code = wp_remote_retrieve_response_code($response);
        if ($code === 401 || $code === 403) {
            $token = $token_service->token(true);
            if ($token !== '') {
                $response = $this->request_route($url, $token);
                if (is_wp_error($response)) {
                    return null;
                }
                $code = wp_remote_retrieve_response_code($response);
            }
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);

        if ($code < 200 || $code >= 300 || !is_array($body)) {
            return null;
        }

        $summary = $body['route_summary'] ?? null;
        if (!is_array($summary) || !isset($summary['total_distance']) || !is_numeric($summary['total_distance'])) {
            return null;
        }

        $duration_seconds = isset($summary['total_time']) && is_numeric($summary['total_time'])
            ? (int) $summary['total_time']
            : null;

        return [
            'distance_km' => round(((float) $summary['total_distance']) / 1000, 2),
            'duration_seconds' => $duration_seconds,
            'duration_text' => $duration_seconds !== null ? $this->format_duration($duration_seconds) : '',
        ];
    }

    public function nearest(float $lat, float $lng, array $stores): ?array
    {
        $nearest = null;
        $shortest_route = INF;
        $fallback_nearest = null;
        $fallback_shortest = INF;

        foreach ($stores as $store) {
            $straight_distance = $this->distance_km($lat, $lng, (float) $store['lat'], (float) $store['lng']);
            if ($straight_distance < $fallback_shortest) {
                $fallback_shortest = $straight_distance;
                $fallback_nearest = $store;
                $fallback_nearest['distance_km'] = round($straight_distance, 2);
                $fallback_nearest['distance_type'] = 'straight_line';
                $fallback_nearest['duration_seconds'] = null;
                $fallback_nearest['duration_text'] = '';
            }

            $route = $this->driving_route($lat, $lng, (float) $store['lat'], (float) $store['lng']);
            if ($route === null || $route['distance_km'] >= $shortest_route) {
                continue;
            }

            $shortest_route = $route['distance_km'];
            $nearest = $store;
            $nearest['distance_km'] = $route['distance_km'];
            $nearest['distance_type'] = 'driving';
            $nearest['duration_seconds'] = $route['duration_seconds'];
            $nearest['duration_text'] = $route['duration_text'];
        }

        return $nearest ?: $fallback_nearest;
    }

    private function format_duration(int $seconds): string
    {
        if ($seconds < 60) {
            return $seconds . ' sec';
        }

        $minutes = (int) round($seconds / 60);
        if ($minutes < 60) {
            return $minutes . ' min';
        }

        $hours = intdiv($minutes, 60);
        $remaining_minutes = $minutes % 60;

        return $remaining_minutes > 0
            ? $hours . ' hr ' . $remaining_minutes . ' min'
            : $hours . ' hr';
    }

    private function request_route(string $url, string $token)
    {
        return wp_remote_get($url, [
            'timeout' => 10,
            'headers' => [
                'Authorization' => $token,
                'Accept' => 'application/json',
            ],
        ]);
    }
}
