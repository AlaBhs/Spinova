<?php
class MainController
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }
    /**
     * Home - Redirect to dashboard if authenticated
     */
    public function home($vars)
    {
        if (is_authenticated()) {
            redirect('/dashboard');
        } else {
            redirect('/login');
        }
    }
    /**
     * Dashboard - Show all links
     */
    public function index($vars)
    {
        if (!is_authenticated()) {
            redirect('/login');
        }

        try {
            $link = new Link($this->db);
            $linkObjects = $link->getAllActive();

            // Convert Link objects to arrays
            $links = array_map(function ($linkObj) {
                return $linkObj->toArray();
            }, $linkObjects);

            $vars['links'] = !empty($links) ? tbodyTemplate($links, 'dashboard') : [];
            $vars['title'] = "Dashboard - Spinova URL Rotator";
            render_view('links/index', $vars);
        } catch (Exception $e) {
            error_log('Dashboard Error: ' . $e->getMessage());
            flash('error', 'Failed to load links. Please try again.');
            redirect('/');
        }
    }

    /**
     * Show create form
     */
    public function create($vars)
    {
        if (!is_authenticated()) {
            redirect('/login');
        }

        // Handle POST request (form submission)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Validation
                if (empty($_POST['name'])) {
                    throw new Exception('Link name is required');
                }

                // Prepare data
                $data = [
                    'name' => trim($_POST['name']),
                    'defaultUrl' => isset($_POST['defaultUrl']) && !empty(trim($_POST['defaultUrl'])) ? trim($_POST['defaultUrl']) : null,
                    'isClick' => isset($_POST['isClickCheckbox']) ?? 0,
                    'isEqualDistribution' => isset($_POST['is_equal_distribution']) ?? 0,
                    'osFilterEnabled' => isset($_POST['os_filter_enabled']) ? 1 : 0,
                    'full' => []
                ];

                // Process destinations based on mode
                if ($data['osFilterEnabled']) {
                    // Handle OS-specific URLs
                    if (!empty($_POST['os']) && !empty($_POST['os_url'])) {
                        foreach ($_POST['os'] as $index => $os) {
                            if (!empty(trim($_POST['os_url'][$index]))) {
                                $data['full'][] = [
                                    'os' => $os,
                                    'url' => trim($_POST['os_url'][$index]),
                                    'perc' => null,
                                    'clicks' => null
                                ];
                            }
                        }
                    }
                } else {
                    // Handle regular URLs (percentage or click mode)
                    if (!empty($_POST['url'])) {
                        foreach ($_POST['url'] as $index => $url) {
                            if (!empty(trim($url))) {
                                $data['full'][] = [
                                    'url' => trim($url),
                                    'perc' => $data['isClick'] ? null : ($_POST['perc'][$index] ?? null),
                                    'clicks' => $data['isClick'] ? ($_POST['clicks'][$index] ?? 0) : null,
                                    'os' => null
                                ];
                            }
                        }
                    }
                }

                error_log("Processed data: " . print_r($data, true));
                if (empty($data['full'])) {
                    throw new Exception('At least one destination URL is required');
                }

                $link = new Link($this->db);

                if ($link->createFromProcessedData($data)) {
                    flash('success', 'Link created successfully!');
                    redirect('/dashboard');
                }

                throw new Exception('Failed to create link');
            } catch (Exception $e) {
                error_log("Link creation error: " . $e->getMessage());
                error_log("Stack trace: " . $e->getTraceAsString());
                flash('error', $e->getMessage());
                $_SESSION['old_input'] = $_POST;
                // Stay on the create page to show errors
            }
        }

        // Handle GET request (show form)
        try {
            $vars['title'] = "Create Link - Spinova URL Rotator";

            // Pre-fill form with old input if available
            $vars['old_input'] = $_SESSION['old_input'] ?? null;
            unset($_SESSION['old_input']);

            render_view('links/create', $vars);
        } catch (Exception $e) {
            error_log("Create form display error: " . $e->getMessage());
            flash('error', 'Something went wrong, please try again!');
            redirect('/dashboard');
        }
    }

    /**
     * Handle link editing (GET) and updating (POST/PUT)
     */
    public function edit($vars)
    {
        if (!is_authenticated()) {
            redirect('/login');
        }

        $id = $vars['id'] ?? '';
        $link = new Link($this->db);

        try {
            // Handle POST/PUT request (update)
            if ($_SERVER['REQUEST_METHOD'] === 'POST' || ($_SERVER['REQUEST_METHOD'] === 'PUT' && isset($_POST['_method']))) {
                $data = [
                    'name' => $_POST['name'] ?? '',
                    'defaultUrl' => $_POST['defaultUrl'] ?? '',
                    'isClick' => isset($_POST['isClickCheckbox']),
                    'full' => []
                ];

                foreach ($_POST['url'] as $index => $url) {
                    $entry = [
                        'url' => $url
                    ];

                    if ($data['isClick']) {
                        $entry['perc'] = null;
                        $entry['clicks'] = $_POST['clicks'][$index] ?? 0;
                    } else {
                        $entry['perc'] = isset($_POST['perc'][$index]) ? $_POST['perc'][$index] : null;
                        $entry['clicks'] = null;
                    }

                    $data['full'][] = $entry;
                }

                if ($link->update($id, $data)) {
                    flash('success', 'Link updated successfully!');
                    redirect('/dashboard');
                } else {
                    throw new Exception('Failed to update link');
                }
            }

            // Handle GET request (show edit form)
            $linkData = $link->getByShortCode($id);

            if (!$linkData) {
                throw new Exception('Link not found');
            }

            $vars['title'] = "Edit Link - Spinova URL Rotator";
            $vars['link'] = $linkData;
            render_view('links/edit', $vars);
        } catch (Exception $e) {
            error_log("Link edit/update error: " . $e->getMessage());

            if ($_SERVER['REQUEST_METHOD'] === 'POST' || ($_SERVER['REQUEST_METHOD'] === 'PUT' && isset($_POST['_method']))) {
                flash('error', 'Failed to update link: ' . $e->getMessage());
                redirect("/edit/{$id}");
            } else {
                flash('error', 'Something went wrong, please try again!');
                redirect('/dashboard');
            }
        }
    }

    /**
     * Delete link
     */
    public function destroy($vars)
    {
        if (!is_authenticated()) {
            redirect('/login');
        }

        try {
            $id = $vars['link'] ?? '';
            $link = new Link($this->db);

            if ($link->deletePermanently($id)) {
                flash('success', 'Link deleted permanently!');
            } else {
                throw new Exception('Link not found');
            }
        } catch (Exception $e) {
            flash('error', 'Something went wrong, please try again!');
        }

        redirect('/dashboard');
    }

    /**
     * Handle link redirection
     */
    public function redirect($vars)
    {
        try {
            $startTime = microtime(true);

            $shortCode = $vars['short'] ?? '';

            $link = new Link($this->db);

            // 1. First check if link exists and get its status
            $linkData = $link->getByShortCode($shortCode);

            // Check if link exists
            if (!$linkData) {
                throw new Exception("Link not found");
            }


            // 2. Check if link is archived
            if ($linkData['isArchive']) {
                throw new Exception("This link has been archived and is no longer active");
            }

            $url = $link->handleVisit($linkData['id']);

            if (empty($url)) {
                throw new Exception("No valid URL to redirect to");
            }
            // Calculate duration
            $duration = round((microtime(true) - $startTime) * 1000, 2); // in milliseconds

            // Log the timing
            error_log(sprintf(
                "[Redirect Timing] Short: %s | URL: %s | Time: %.2fms",
                $shortCode,
                $url,
                $duration
            ));
            header("Location: " . $url);
            exit;
        } catch (Exception $e) {
            error_log("Redirect error: " . $e->getMessage());
            http_response_code(404);
            render_view('template/error', [
                'title' => "404 - Link Not Found",
                'error' => $e->getMessage()
            ]);
        }
    }
}
