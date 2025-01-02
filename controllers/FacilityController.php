<?php
class FacilityController {
    private $facilityModel;

    /**
     * Constructor to initialize the Facility model.
     *
     * @param $facilityModel
     * The model that handles database operations for facilities.
     */
    public function __construct($facilityModel) {
        $this->facilityModel = $facilityModel;
    }

    /**
     * Browse facilities with optional filters: search, category, location, and status.
     *
     * @param string $search Search keyword for title or description.
     * @param int|null $category Category ID for filtering facilities.
     * @param string|null $location Location keyword for filtering (town, county, or postcode).
     * @param string|null $status Status keyword for filtering (e.g., "Active", "Under Maintenance").
     * @return array The list of facilities matching the criteria.
     */
    public function browseFacilities($search = '', $category = null, $location = null, $status = null) {
        return $this->facilityModel->searchFacilities($search, $category, $location, $status);
    }

    /**
     * Create a new facility by passing data to the model.
     *
     * @param array $data Associative array of facility data (e.g., title, category, location).
     */
    public function createFacility($data) {
        $this->facilityModel->createFacility($data);
    }

    /**
     * Update an existing facility by ID.
     *
     * @param int $id Facility ID to update.
     * @param array $data Associative array of updated facility data.
     */
    public function updateFacility($id, $data) {
        $this->facilityModel->updateFacility($id, $data);
    }

    /**
     * Delete a facility by ID.
     *
     * @param int $id Facility ID to delete.
     */
    public function deleteFacility($id) {
        $this->facilityModel->deleteFacility($id);
    }

    /**
     * Fetch paginated facilities for large datasets.
     *
     * @param int $page The current page number.
     * @param int $limit The number of facilities per page.
     * @param string $search Search keyword for title or description.
     * @param int|null $category Category ID for filtering facilities.
     * @return array The paginated list of facilities.
     */
    public function getPaginatedFacilities($page, $limit, $search = '', $category = null, $location = null, $status = null) {
        $offset = ($page - 1) * $limit;
        return $this->facilityModel->getPaginatedFacilities($offset, $limit, $search, $category, $location, $status);
    }



    /**
     * Get the total number of facilities for pagination purposes.
     *
     * @param string $search Search keyword for title or description.
     * @param int|null $category Category ID for filtering facilities.
     * @return int The total number of facilities matching the criteria.
     */
    public function getTotalFacilities($search = '', $category = null) {
        return $this->facilityModel->getTotalFacilities($search, $category);
    }

}
?>
