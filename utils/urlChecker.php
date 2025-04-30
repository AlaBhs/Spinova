<?php
function urlChecker($shortCode, $data) {
    // Validate URLs (simple example)
    foreach ($data['full'] as &$destination) {
        if (!filter_var($destination['url'], FILTER_VALIDATE_URL)) {
            throw new Exception('Invalid URL: ' . $destination['url']);
        }
    }
    
    if (!empty($data['defaultUrl']) && !filter_var($data['defaultUrl'], FILTER_VALIDATE_URL)) {
        throw new Exception('Invalid default URL');
    }
    
    // Return validated data
    return $data;
}
?>