<?php
// Flash message helper
function flash($type, $message)
{
    $_SESSION['flash'][$type] = $message;
}

// Method override for PUT/DELETE
function get_method()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['_method'])) {
        return strtoupper($_POST['_method']);
    }
    return $_SERVER['REQUEST_METHOD'];
}

// CSRF protection
function csrf_token()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token($token)
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Authentication check
function is_authenticated()
{
    return isset($_SESSION['user']);
}

// Redirect helper
function redirect($url)
{
    header("Location: $url");
    exit;
}
function processData($postData)
{
    $processed = [
        'name' => trim($postData['name']),
        'isClick' => $postData['isClick'] ?? false,
        'defaultUrl' => trim($postData['defaultUrl'] ?? '')
    ];

    // Process destinations
    $processed['full'] = [];
    $urls = $postData['url'] ?? [];
    $percs = $postData['perc'] ?? [];
    $clicks = $postData['clicks'] ?? [];

    foreach ($urls as $index => $url) {
        if (!empty(trim($url))) {
            $processed['full'][] = [
                'url' => trim($url),
                'perc' => isset($percs[$index]) ? (float)$percs[$index] : null,
                'clicks' => isset($clicks[$index]) ? (int)$clicks[$index] : null
            ];
        }
    }

    return $processed;
}

/**
 * Generates HTML table rows for links with nested structure
 * @param array $links Array of link data
 * @param string $mode Display mode ('dashboard' or 'archive')
 * @return string HTML table content
 */
function tbodyTemplate($links, $mode = 'dashboard')
{
    $tbodyContent = '';

    foreach ($links as $item) {
        // Extract data with null checks
        $rotatorName = htmlspecialchars($item['name'] ?? '');
        $rotatorUrl = htmlspecialchars($item['short'] ?? '');
        $rotatorTotalVisits = $item['total_visits'] ?? $item['totalVisits'] ?? 0;
        $defaultUrl = htmlspecialchars($item['default_url'] ?? $item['defaultDestination']['url'] ?? '');
        $defaultVisits = $item['default_destination_visits'] ?? $item['defaultDestination']['visits'] ?? 0;
        $timestamp = isset($item['created_at']) ?
            (new DateTime($item['created_at']))->format('M j, Y g:i A') : (new DateTime($item['createdAt']))->format('M j, Y g:i A');

        // Default URL display
        $defaultUrlTD = !empty($defaultUrl) ?
            'Default <i class="form-tooltip mr-2 ml-1" title="Traffic will be redirected here after all click counts are filled">?</i>: 
            <a href="' . $defaultUrl . '" target="_blank">' . $defaultUrl . '</a> <hr>' :
            'N/A <hr>';

        // Action buttons based on mode
        $titleBtnsHtml = '';
        if ($mode === 'dashboard') {
            $titleBtnsHtml = '
                <div class="float-right">
                    <a class="link-slug-copy" role="button" title="Copy">
                        <ion-icon name="copy-outline" class="mr-1"></ion-icon>
                    </a>
                    <a href="/edit/' . $rotatorUrl . '" class="link-edit-btn" role="button" title="Edit">
                        <ion-icon name="create-outline" class="mr-1"></ion-icon>
                    </a>
                    <button type="button"
                        data-toggle="modal" 
                        data-target="#exampleModalCenter" 
                        data-id="' . htmlspecialchars($rotatorUrl) . '"
                        title="Delete">
                        <ion-icon name="trash-outline" class="mr-1"></ion-icon>
                    </button>
                </div>';
        } elseif ($mode === 'archive') {
            $titleBtnsHtml = '
                <div class="float-right d-flex">
                    <form action="/archive/restore/' . $rotatorUrl . '" method="POST">
                        <button type="submit" title="Restore">
                            <ion-icon name="refresh" class="mr-1"></ion-icon>
                        </button>
                        <button type="button"
                        data-toggle="modal" 
                        data-target="#exampleModalCenter" 
                        data-id="' . htmlspecialchars($rotatorUrl) . '"
                        title="Delete">
                        <ion-icon name="trash-outline" class="mr-1"></ion-icon>
                    </button>
                    </form>
                    
                </div>';
        }

        // Build destination rows
        $consecutiveTR = '';
        $destinations = $item['destinations'] ?? $item['full'] ?? [];

        foreach ($destinations as $el) {
            $url = htmlspecialchars($el['url'] ?? '');
            $visits = $el['visits'] ?? 0;
            $percentage = $el['percentage'] ?? $el['perc'] ?? 0;
            $perc = is_numeric($percentage) && floatval($percentage) != 0 ?
                (floatval($percentage) == intval($percentage) ?
                    intval($percentage) . '%' :
                    number_format(floatval($percentage), 2) . '%') :
                '-';
            $clicks = $el['clicks'] ?? $el['click_count'] ?? '-';

            $consecutiveTR .= '
                <tr class="destination-row">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td><a href="' . $url . '" target="_blank">' . $url . '</a></td>
                    <td>' . $visits . '</td>
                    <td>' . $perc . '</td>
                    <td>' . $clicks . '</td>
                </tr>';
        }

        // Main row
        $tbodyContent .= '
            <tr data-slug="' . $rotatorUrl . '" class="main-row">
                <td id="link-btn">
                    <div class="rotator-title overflow-hidden mb-2 ml-1">
                        <div class="float-left">
                            <h5>' . $rotatorName . '</h5>
                        </div>
                        ' . $titleBtnsHtml . '      
                    </div>
                    <div class="d-table mb-2">
                        <ion-icon name="link" class="mr-1"></ion-icon>
                        <a href="/' . $rotatorUrl . '" class="dash-link d-table-cell align-middle" target="_blank">
                            <span class="host-name"></span>/' . $rotatorUrl . '
                        </a>
                    </div>
                    <div class="timestamp ml-1">
                        <p>' . $timestamp . '</p> 
                    </div>
                </td>
                <td>' . $rotatorTotalVisits . '</td>
                <td>' . $defaultUrlTD . '</td>
                <td>' . $defaultVisits . '</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            ' . $consecutiveTR;
    }

    return $tbodyContent;
}
