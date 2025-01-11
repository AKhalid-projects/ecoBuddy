<?php
// The Facility class handles database interactions for eco-friendly facilities.
class Facility {
    private $pdo; // PDO instance for database interaction.
    // Constructor to initialize the PDO object through dependency injection.
    public function __construct($pdo) {
        // Dependency injection of PDO object for database interactions
        $this->pdo = $pdo;
    }
    /**
     * Get all categories for the dropdown menu in the search form.
     *
     * @return array Returns an associative array of category IDs and names.
     */
    public function getAllCategories() {
        $stmt = $this->pdo->query("SELECT id, name FROM ecoCategories ORDER BY name ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    /**
     * Fetch all facilities with category names and contributor details.
     *
     * @return array Returns an array of all facilities and their metadata.
     */
    public function getAllFacilities() {
        $stmt = $this->pdo->query("
            SELECT ecoFacilities.*, 
                   ecoCategories.name AS category_name, 
                   ecoUser.username AS contributor_name,
                   (SELECT statusComment 
                    FROM ecoFacilityStatus 
                    WHERE facilityId = ecoFacilities.id 
                    ORDER BY ROWID DESC LIMIT 1) AS statusComment
            FROM ecoFacilities
            LEFT JOIN ecoCategories ON ecoFacilities.category = ecoCategories.id
            LEFT JOIN ecoUser ON ecoFacilities.contributor = ecoUser.id
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Returns an array of facility records.
    }
    /**
     * Get a specific facility by its ID, including category and contributor details.
     *
     * @param int $id Facility ID.
     * @return array|null Returns the facility record or null if not found.
     */
    public function getFacilityById($id) {
        $stmt = $this->pdo->prepare("
        SELECT ecoFacilities.*, 
               ecoCategories.name AS category_name, 
               ecoUser.username AS contributor_name 
        FROM ecoFacilities
        LEFT JOIN ecoCategories ON ecoFacilities.category = ecoCategories.id
        LEFT JOIN ecoUser ON ecoFacilities.contributor = ecoUser.id
        WHERE ecoFacilities.id = :id
    ");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    /**
     * Create a new facility in the database.
     *
     * @param array $data Associative array containing facility data (title, category, location, etc.).
     */
    public function createFacility($data) {
        $stmt = $this->pdo->prepare("
            INSERT INTO ecoFacilities (title, category, description, houseNumber, streetName, county, town, postcode, lng, lat, contributor)
            VALUES (:title, :category, :description, :houseNumber, :streetName, :county, :town, :postcode, :lng, :lat, :contributor)
        ");
        $stmt->execute($data); // Insert the facility data into the database.
    }
    /**
     * Update an existing facility in the database.
     *
     * @param int $id Facility ID to update.
     * @param array $data Associative array of updated facility data.
     */
    public function updateFacility($id, $data) {
        $stmt = $this->pdo->prepare("
            UPDATE ecoFacilities
            SET title = :title, category = :category, description = :description, 
                houseNumber = :houseNumber, streetName = :streetName, county = :county, town = :town, 
                postcode = :postcode, lng = :lng, lat = :lat, contributor = :contributor
            WHERE id = :id
        ");
        $data[':id'] = $id; // Add the facility ID to the data array.
        $stmt->execute($data); // Execute the update query.
    }
    /**
     * Delete a facility by ID.
     *
     * @param int $id Facility ID to delete.
     */
    public function deleteFacility($id) {
        $stmt = $this->pdo->prepare("DELETE FROM ecoFacilities WHERE id = :id");
        $stmt->execute([':id' => $id]); // Bind the ID and execute the delete query.
    }
    /**
     * Get the status history for a specific facility.
     *
     * @param int $facilityId Facility ID.
     * @return array Returns an array of status history records.
     */
    public function getFacilityStatus($facilityId) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM ecoFacilityStatus
            WHERE facilityId = :facilityId
            ORDER BY ROWID DESC
        ");
        $stmt->execute([':facilityId' => $facilityId]); // Bind the facility ID.
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch the status history.
    }
    /**
     * Add a new status comment for a facility.
     *
     * @param int $facilityId Facility ID.
     * @param string $statusComment The status comment to be added.
     */
    public function addFacilityStatus($facilityId, $statusComment) {
        $stmt = $this->pdo->prepare("
            INSERT INTO ecoFacilityStatus (facilityId, statusComment)
            VALUES (:facilityId, :statusComment)
        ");
        $stmt->execute([
            ':facilityId' => $facilityId,
            ':statusComment' => $statusComment
        ]); // Insert a new status comment.
    }
    /**
     * Search for facilities based on title, description, category, location, and status.
     *
     * @param string $search Search keyword for title or description.
     * @param int|null $category Category ID for filtering facilities.
     * @param string|null $location Location keyword for filtering (town, county, street name, etc.).
     * @param string|null $status Status keyword for filtering.
     * @return array Returns an array of facilities matching the search criteria.
     */
    public function searchFacilities($search = '', $category = null, $location = null, $status = null) {
        $query = "
        SELECT ecoFacilities.*, 
               ecoCategories.name AS category_name, 
               ecoUser.username AS contributor_name,
               (SELECT statusComment 
                FROM ecoFacilityStatus 
                WHERE facilityId = ecoFacilities.id 
                ORDER BY ROWID DESC LIMIT 1) AS statusComment
        FROM ecoFacilities
        LEFT JOIN ecoCategories ON ecoFacilities.category = ecoCategories.id
        LEFT JOIN ecoUser ON ecoFacilities.contributor = ecoUser.id
        WHERE (ecoFacilities.title LIKE :search OR ecoFacilities.description LIKE :search)
    ";
        // Add filters dynamically

        // Add category filter if provided.
        if ($category) {
            $query .= " AND ecoFacilities.category = :category";
        }

        // Add location filter if provided.
        if ($location) {
            $query .= " AND (ecoFacilities.town LIKE :location 
                        OR ecoFacilities.county LIKE :location 
                        OR ecoFacilities.postcode LIKE :location 
                        OR ecoFacilities.streetName LIKE :location
                        OR ecoFacilities.houseNumber LIKE :location)";
        }

        // Add status filter if provided.
        if ($status) {
            $query .= " AND (SELECT statusComment 
                         FROM ecoFacilityStatus 
                         WHERE facilityId = ecoFacilities.id 
                         ORDER BY ROWID DESC LIMIT 1) = :status";
        }

        $query .= " ORDER BY ecoFacilities.title ASC";

        $stmt = $this->pdo->prepare($query); // Prepare the query.
        
        // Bind parameters
        $params = [
            ':search' => '%' . $search . '%',
        ];
        if ($category) {
            $params[':category'] = $category;
        }
        if ($location) {
            $params[':location'] = '%' . $location . '%';
        }
        if ($status) {
            $params[':status'] = $status;
        }

        $stmt->execute($params);  // Execute the search query.

        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Return the search results.
    }
    /**
     * Fetch paginated facilities for a large dataset.
     *
     * @param int $offset The starting point for the results.
     * @param int $limit The number of results per page.
     * @param string $search Search keyword for title or description.
     * @param int|null $category Category ID for filtering facilities.
     * @param string|null $location Location keyword for filtering.
     * @param string|null $status Status keyword for filtering.
     * @return array Returns an array of facilities for the requested page.
     */
    public function getPaginatedFacilities($offset, $limit, $search = '', $category = null, $location = null, $status = null) {
        $query = "
        SELECT ecoFacilities.*, 
               ecoCategories.name AS category_name, 
               ecoUser.username AS contributor_name,
               (SELECT statusComment 
                FROM ecoFacilityStatus 
                WHERE facilityId = ecoFacilities.id 
                ORDER BY ROWID DESC LIMIT 1) AS statusComment
        FROM ecoFacilities
        LEFT JOIN ecoCategories ON ecoFacilities.category = ecoCategories.id
        LEFT JOIN ecoUser ON ecoFacilities.contributor = ecoUser.id
        WHERE (ecoFacilities.title LIKE :search OR ecoFacilities.description LIKE :search)
    ";
        if ($category) {
            $query .= " AND ecoFacilities.category = :category";
        }
        if ($location) {
            $query .= " AND (ecoFacilities.town LIKE :location 
                      OR ecoFacilities.county LIKE :location 
                      OR ecoFacilities.postcode LIKE :location 
                      OR ecoFacilities.streetName LIKE :location)";
        }
        if ($status) {
            $query .= " AND (SELECT statusComment 
                      FROM ecoFacilityStatus 
                      WHERE facilityId = ecoFacilities.id 
                      ORDER BY ROWID DESC LIMIT 1) = :status";
        }
        $query .= " ORDER BY ecoFacilities.title ASC LIMIT :offset, :limit";

        $stmt = $this->pdo->prepare($query); // Prepare the paginated query.
        // Bind parameters.
        $params = [
            ':search' => '%' . $search . '%',
            ':offset' => $offset,
            ':limit' => $limit,
        ];
        if ($category) {
            $params[':category'] = $category;
        }
        if ($location) {
            $params[':location'] = '%' . $location . '%';
        }
        if ($status) {
            $params[':status'] = $status;
        }

        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT); // Bind offset as integer.
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT); // Bind limit as integer.
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Return paginated results.
    }
    /**
     * Fetch the total number of facilities for pagination purposes.
     *
     * @param string $search Search keyword.
     * @param int|null $category Category ID for filtering facilities.
     * @param string|null $location Location keyword for filtering.
     * @param string|null $status Status keyword for filtering.
     * @return int Total number of facilities matching the criteria.
     */
    public function getTotalFacilities($search = '', $category = null, $location = null, $status = null) {
        $query = "
    SELECT COUNT(*) as total 
    FROM ecoFacilities
    WHERE (title LIKE :search OR description LIKE :search)
";
        if ($category) {
            $query .= " AND category = :category";
        }
        if ($location) {
            $query .= " AND (town LIKE :location 
                      OR county LIKE :location 
                      OR postcode LIKE :location 
                      OR streetName LIKE :location)";
        }
        if ($status) {
            $query .= " AND (SELECT statusComment 
                      FROM ecoFacilityStatus 
                      WHERE facilityId = ecoFacilities.id 
                      ORDER BY ROWID DESC LIMIT 1) = :status";
        }

        $stmt = $this->pdo->prepare($query); // Prepare the count query.
        $params = [
            ':search' => '%' . $search . '%',
        ];
        if ($category) {
            $params[':category'] = $category;
        }
        if ($location) {
            $params[':location'] = '%' . $location . '%';
        }
        if ($status) {
            $params[':status'] = $status;
        }

        $stmt->execute($params); // Execute the query.
        return $stmt->fetch(PDO::FETCH_ASSOC)['total']; // Return the total count of facilities.
    }
}
