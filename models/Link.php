<?php
require_once __DIR__ . '/../config/db.php';

class Link
{
    private $conn;
    private $table = 'links';
    private $destinations_table = 'link_destinations';
    public $id;
    public $name;
    public $is_click;
    public $default_destination_url;
    public $default_destination_visits;
    public $total_visits;
    public $short;
    public $isArchive;
    public $created_at;
    public $updated_at;
    public $destinations = [];

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->connect();
    }

    // Create short code
    private function generateShortCode()
    {
        return substr(md5(uniqid(rand(), true)), 0, 8);
    }

    // Create a new link
    public function create()
    {
        $query = 'INSERT INTO ' . $this->table . ' 
                  SET name = :name,
                      is_click = :is_click,
                      default_destination_url = :default_destination_url,
                      short = :short';

        $stmt = $this->conn->prepare($query);
        $this->short = $this->generateShortCode();

        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':is_click', $this->is_click);
        $stmt->bindParam(':default_destination_url', $this->default_destination_url);
        $stmt->bindParam(':short', $this->short);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();

            $destinationsResult = $this->addDestinations();
            return $destinationsResult;
        }

        return false;
    }

    private function addDestinations()
    {
        if (empty($this->destinations)) {
            return true;
        }

        $success = true;

        foreach ($this->destinations as $destination) {
            $query = 'INSERT INTO ' . $this->destinations_table . ' 
                      SET link_id = :link_id,
                          url = :url,
                          percentage = :percentage,
                          clicks = :clicks';

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':link_id', $this->id);
            $stmt->bindParam(':url', $destination['url']);
            $stmt->bindParam(':percentage', $destination['percentage']);
            $stmt->bindParam(':clicks', $destination['clicks']);

            if (!$stmt->execute()) {
                $success = false;
            }
        }

        return $success;
    }

    // Get link by short code
    public function getByShortCode($short)
    {
        $query = 'SELECT * FROM ' . $this->table . ' 
              WHERE short = :short LIMIT 1';

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':short', $short);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return false;
        }
        // Get destinations using the link ID
        $destinations = $this->getDestinations($row['id']);

        return [
            'id' => $row['id'],
            'name' => $row['name'],
            'is_click' => (bool)$row['is_click'],
            'default_destination' => [
                'url' => $row['default_destination_url'],
                'visits' => $row['default_destination_visits']
            ],
            'total_visits' => $row['total_visits'],
            'short' => $row['short'],
            'isArchive' => (bool)$row['isArchive'],
            'full' => $destinations
        ];
    }
    public function toArray()
    {
        return [
            'name' => $this->name ?? null,
            'short' => $this->short ?? null,
            'total_visits' => $this->total_visits ?? 0,
            'default_url' => $this->default_destination_url ?? null,
            'default_destination_visits' => $this->default_destination_visits ?? 0,
            'created_at' => $this->created_at ?? null,
            'destinations' => $this->destinations ?? [],
            // For compatibility with different template expectations:
            'defaultDestination' => [
                'url' => $this->default_destination_url ?? null,
                'visits' => $this->default_destination_visits ?? 0
            ],
            'full' => $this->destinations ?? []
        ];
    }
    // Get destinations for a link
    private function getDestinations($linkId)
    {
        $query = 'SELECT * FROM ' . $this->destinations_table . ' 
              WHERE link_id = :link_id';

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':link_id', $linkId);  // Use the passed parameter
        $stmt->execute();

        $destinations = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Transform to match what your view expects
        $formatted = [];
        foreach ($destinations as $dest) {
            $formatted[] = [
                'url' => $dest['url'],
                'perc' => isset($dest['percentage']) ? (float)$dest['percentage'] : null,
                'clicks' => isset($dest['clicks']) ? (int)$dest['clicks'] : null
                // Add other fields if needed
            ];
        }

        return $formatted;
    }

    // Handle link visit and return destination URL
    public function handleVisit($linkId)
    {
        // Set the link ID
        $this->id = $linkId;

        // 1. Update total visits count
        $this->updateTotalVisits();

        // 2. Get destinations for this link
        $this->destinations = $this->getDestinations($linkId);

        // 3. Check if link is in click mode
        $this->is_click = $this->isClickMode($linkId);

        // 4. Handle the redirection
        if ($this->is_click) {
            return $this->handleClickMode();
        } else {
            return $this->handlePercentageMode();
        }
    }

    // Check if the link is in click mode
    private function isClickMode($linkId)
    {
        $stmt = $this->conn->prepare('SELECT is_click FROM ' . $this->table . ' WHERE id = ?');
        $stmt->execute([$linkId]);
        return (bool)$stmt->fetchColumn();
    }

    // Update total visits for the link
    private function updateTotalVisits()
    {
        $query = 'UPDATE ' . $this->table . ' 
                  SET total_visits = total_visits + 1 
                  WHERE id = ?';
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$this->id]);
    }

    private function handleClickMode()
    {

        // Get all destinations that still need clicks (or unlimited destinations)
        $query = 'SELECT * FROM ' . $this->destinations_table . ' 
              WHERE link_id = ? AND (clicks > visits OR clicks IS NULL)';
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$this->id]);
        $availableDestinations = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // If we have available destinations, select one randomly
        if (!empty($availableDestinations)) {
            // Random selection
            $selectedIndex = array_rand($availableDestinations);
            $selectedDestination = $availableDestinations[$selectedIndex];


            // Update visit count
            $this->updateDestinationVisits($selectedDestination['id']);

            return $selectedDestination['url'];
        }

        // Fall back to default destination
        $this->updateDefaultUrlVisits();
        $defaultUrl = $this->getDefaultUrl();
        return $defaultUrl;
    }
    private function updateDefaultUrlVisits()
    {
        $query = 'UPDATE ' . $this->table . ' 
              SET default_destination_visits = default_destination_visits + 1 
              WHERE id = ?';

        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$this->id]);
    }
    private function getDefaultUrl()
    {

        $query = 'SELECT default_destination_url FROM ' . $this->table . ' WHERE id = ?';
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$this->id]);
        $result = $stmt->fetchColumn();

        return $result;
    }
    // Handle redirection based on percentage mode
    private function handlePercentageMode()
    {
        // Get all destinations with their weights
        $query = 'SELECT * FROM ' . $this->destinations_table . ' 
                  WHERE link_id = ? AND percentage IS NOT NULL';
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$this->id]);
        $destinations = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($destinations)) {
            return $this->default_destination_url;
        }

        // Create weighted array
        $weighted = [];
        foreach ($destinations as $dest) {
            $weighted = array_merge($weighted, array_fill(0, $dest['percentage'] * 100, $dest));
        }
        // Select random destination based on weights
        $selected = $weighted[array_rand($weighted)];
        $this->updateDestinationVisits($selected['id']);

        return $selected['url'];
    }

    // Update visits for a specific destination
    private function updateDestinationVisits($destinationId)
    {
        $query = 'UPDATE ' . $this->destinations_table . ' 
                  SET visits = visits + 1 
                  WHERE id = ?';
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$destinationId]);
    }

    /**
     * Get all active (non-archived) links
     */
    public function getAllActive()
    {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE isArchive = 0 ORDER BY created_at DESC';
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $links = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $link = new Link();
            $link->id = $row['id'];
            $link->name = $row['name'];
            $link->is_click = $row['is_click'];
            $link->default_destination_url = $row['default_destination_url'];
            $link->default_destination_visits = $row['default_destination_visits'];
            $link->total_visits = $row['total_visits'];
            $link->short = $row['short'];
            $link->created_at = $row['created_at'];
            $link->updated_at = $row['updated_at'];

            // Get destinations
            $queryDest = 'SELECT * FROM ' . $this->destinations_table . ' WHERE link_id = :link_id';
            $stmtDest = $this->conn->prepare($queryDest);
            $stmtDest->bindParam(':link_id', $link->id);
            $stmtDest->execute();
            $link->destinations = $stmtDest->fetchAll(PDO::FETCH_ASSOC);

            $links[] = $link;
        }

        return $links;
    }

    /**
     * Update link data
     */
    public function update($shortCode, $data)
    {
        $this->conn->beginTransaction();
        try {
            // Update main link info
            $query = 'UPDATE ' . $this->table . ' 
                  SET 
                    name = :name,
                    is_click = :is_click,
                    default_destination_url = :default_url,' .
                    ($data['isClick'] ? '' : ' default_destination_visits = 0,') . '
                    updated_at = NOW()
                  WHERE short = :short';

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':name', $data['name']);
            $stmt->bindParam(':is_click', $data['isClick']);
            if (empty($data['defaultUrl'])) {
                $stmt->bindValue(':default_url', null, PDO::PARAM_NULL);
            } else {
                $stmt->bindParam(':default_url', $data['defaultUrl']);
            }
            $stmt->bindParam(':short', $shortCode);

            if (!$stmt->execute()) {
                return false;
            }
            // Get the link by short code
            $linkId = $this->getIdByShortCode($shortCode);
            if (!$linkId) {
                throw new Exception('Link not found');
            }
            // Delete existing destinations
            $this->conn->prepare('DELETE FROM ' . $this->destinations_table . ' WHERE link_id = ?')
                ->execute([$linkId]);
            // Get link ID
            $this->getByShortCode($shortCode);


            // Add new destinations
            $this->destinations = [];
            foreach ($data['full'] as $destination) {
                $query = 'INSERT INTO ' . $this->destinations_table . ' 
                     (link_id, url, percentage, clicks) 
                     VALUES (:link_id, :url, :percentage, :clicks)';

                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':link_id', $linkId);
                $stmt->bindParam(':url', $destination['url']);
                $stmt->bindParam(':percentage', $destination['perc']);
                $stmt->bindParam(':clicks', $destination['clicks']);
                $stmt->execute();
            }
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log('Update Error: ' . $e->getMessage());
            return false;
        }
    }
    /**
     * Get link ID by short code
     */
    public function getIdByShortCode($shortCode)
    {
        $stmt = $this->conn->prepare('SELECT id FROM ' . $this->table . ' WHERE short = ? LIMIT 1');
        $stmt->execute([$shortCode]);
        return $stmt->fetchColumn();
    }
    /**
     * Delete a link
     */
    public function delete($shortCode)
    {
        $this->conn->beginTransaction();

        try {
            // First get the link ID
            if (!$this->getByShortCode($shortCode)) {
                throw new Exception('Link not found');
            }

            // Delete destinations
            if (!$this->clearDestinations()) {
                throw new Exception('Failed to clear destinations');
            }

            // Delete the main link
            $query = 'DELETE FROM ' . $this->table . ' WHERE short = :short';
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':short', $shortCode);

            if (!$stmt->execute()) {
                throw new Exception('Failed to delete link');
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Link deletion failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Clear all destinations for current link
     */
    private function clearDestinations()
    {
        if (!$this->id) return false;

        $query = 'DELETE FROM ' . $this->destinations_table . ' WHERE link_id = :link_id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':link_id', $this->id);

        return $stmt->execute();
    }
    /**
     * Update link statistics
     */
    public function updateStats($shortCode, $data)
    {
        // Update main link stats
        $query = 'UPDATE ' . $this->table . ' 
              SET 
                total_visits = :total_visits,
                default_destination_visits = :default_visits
              WHERE short = :short';

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':total_visits', $data['totalVisits']);
        $stmt->bindParam(':default_visits', $data['defaultDestination']['visits']);
        $stmt->bindParam(':short', $shortCode);
        $stmt->execute();

        // Update destination stats
        foreach ($data['full'] as $destination) {
            $query = 'UPDATE ' . $this->destinations_table . ' 
                  SET visits = :visits
                  WHERE id = :id';
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':visits', $destination['visits']);
            $stmt->bindParam(':id', $destination['id']);
            $stmt->execute();
        }

        return true;
    }

    /**
     * Create link from processed data (for use with MainController)
     */
    public function createFromProcessedData($data)
    {
        if (!isset($data['isClick']) || $data['isClick'] === '') {
            $data['isClick'] = 0;
        }
        $this->name = $data['name'];
        $this->is_click = $data['isClick'];
        $this->default_destination_url = $data['defaultUrl'];
        $this->destinations = [];
        
        foreach ($data['full'] as $destination) {
            $this->destinations[] = [
                'url' => $destination['url'],
                'percentage' => $destination['perc'] ?? null,
                'clicks' => $destination['clicks'] ?? null
            ];
        }

        return $this->create();
    }
    /**
     * Get all archived links
     * 
     * This function retrieves all links that have been marked as archived.
     * 
     * @return array List of archived links
     */
    public function getAllArchived()
    {
        try {
            $query = 'SELECT * FROM links WHERE isArchive = 1';
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $links = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Load destinations for each link
            foreach ($links as &$link) {
                $link['destinations'] = $this->getLinkDestinations($link['id']);
                $link['full'] = $link['destinations']; // For template compatibility
            }

            return $links;
        } catch (Exception $e) {
            error_log('Archive Error: ' . $e->getMessage());
            return [];
        }
    }

    private function getLinkDestinations($linkId)
    {
        $stmt = $this->conn->prepare("SELECT * FROM link_destinations WHERE link_id = ?");
        $stmt->execute([$linkId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Archive a link
     * 
     * This function marks a link as archived by setting its `isArchive` field to 1.
     * 
     * @param string $id The short code of the link to archive
     * @return bool True on success, false on failure
     */
    public function archive($id)
    {
        $query = 'UPDATE links SET isArchive = 1 WHERE short = :id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    /**
     * Permanently delete an archived link
     * 
     * This function deletes a link permanently from the database
     * 
     * @param string $id The short code of the link to delete
     * @return bool True on success, false on failure
     */
    public function deletePermanently($id)
    {
        $query = 'DELETE FROM links WHERE short = :id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    /**
     * Restore an archived link
     * 
     * This function restores an archived link by setting its `isArchive` field to 0.
     * 
     * @param string $id The short code of the link to restore
     * @return bool True on success, false on failure
     */
    public function restore($id)
    {
        $query = 'UPDATE links SET isArchive = 0 WHERE short = :id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
