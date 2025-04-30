<?php
class ArchiveController
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Show archived links
     */
    public function index($vars) {
        if (!is_authenticated()) {
            redirect('/login');
        }
    
        try {
            $link = new Link($this->db);
            // This already returns an array of arrays
            $links = $link->getAllArchived();
            
            // No need for toArray() conversion since we already have arrays
            $vars['links'] = !empty($links) ? tbodyTemplate($links, 'archive') : [];
            $vars['title'] = "Archive - Spinova URL Rotator";
            render_view('archive/index', $vars);
    
        } catch (Exception $e) {
            error_log('Archive Error: ' . $e->getMessage());
            flash('error', 'Archive feature not available. Please contact admin.');
            redirect('/');
        }
    }

    /**
     * Archive a link
     */
    public function archive($vars)
    {
        if (!is_authenticated()) {
            redirect('/login');
        }

        try {
            $id = $vars['link'] ?? '';
            $link = new Link($this->db);

            if ($link->archive($id)) {
                flash('success', 'Link moved to archive!');
            } else {
                throw new Exception('Link not found');
            }
        } catch (Exception $e) {
            flash('error', 'Something went wrong, please try again!');
        }

        redirect('/dashboard');
    }

    /**
     * Delete permanently
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

        redirect('/archive');
    }

    /**
     * Restore from archive
     */
    public function restore($vars)
    {
        if (!is_authenticated()) {
            redirect('/login');
        }

        try {
            $id = $vars['link'] ?? '';
            $link = new Link($this->db);

            if ($link->restore($id)) { // Implement this method
                flash('success', 'Link restored!');
            } else {
                throw new Exception('Link not found');
            }
        } catch (Exception $e) {
            flash('error', 'Something went wrong, please try again!');
        }

        redirect('/archive');
    }
}
