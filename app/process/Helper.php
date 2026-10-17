<?php

// ------------------ Global Input Filter ------------------
function filterInput($value)
{
    if (is_array($value)) {
        return array_map('filterInput', $value);
    }

    return htmlspecialchars(strip_tags(trim($value)), ENT_QUOTES, 'UTF-8');
}

// ------------------ Standard API Response ------------------
function api_response($status, $httpCode, $code, $message, $data = null)
{
    http_response_code($httpCode);

    return [
        "status"       => $status,
        "http_code"    => $httpCode,
        "status_code"  => $code,
        "message"      => $message,
        "data"         => $data
    ];
}
