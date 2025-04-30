<?php
function randomizerPerc($linkData) {
    if (empty($linkData['destinations'])) {
        return [
            'redirectedUrl' => $linkData['default_destination_url'],
            'updated' => [
                'totalVisits' => $linkData['total_visits'] + 1,
                'defaultDestination' => [
                    'visits' => $linkData['default_destination_visits'] + 1
                ],
                'full' => $linkData['destinations']
            ]
        ];
    }

    $total = array_reduce($linkData['destinations'], function($carry, $item) {
        return $carry + $item['percentage'];
    }, 0);

    $rand = mt_rand(1, $total * 100) / 100;
    $selectedUrl = null;

    foreach ($linkData['destinations'] as &$destination) {
        $rand -= $destination['percentage'];
        if ($rand <= 0) {
            $selectedUrl = $destination['url'];
            $destination['visits'] = ($destination['visits'] ?? 0) + 1;
            break;
        }
    }

    return [
        'redirectedUrl' => $selectedUrl ?? $linkData['destinations'][0]['url'],
        'updated' => [
            'totalVisits' => $linkData['total_visits'] + 1,
            'defaultDestination' => [
                'visits' => $linkData['default_destination_visits']
            ],
            'full' => $linkData['destinations']
        ]
    ];
}

function randomizerClick($linkData) {
    // Implement click-based randomization if needed
    // Similar structure to randomizerPerc but with click logic
    return randomizerPerc($linkData); // Default to same behavior for now
}
?>