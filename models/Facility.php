<?php
class Facility {
    private $pdo;

    public function __construct($pdo) {
        // Dependency injection of PDO object for database interactions
        $this->pdo = $pdo;
    }

    // Fetch all categories for the dropdown menu in the search functionality
    public function getAllCategories() {
        $stmt = $this->pdo->query("SELECT id, name FROM ecoCategories ORDER BY name ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Fetch all facilities with their categories and contributors
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
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Fetch a specific facility by its ID, including its category and contributor details
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

    // Create a new facility in the database
    public function createFacility($data) {
        $stmt = $this->pdo->prepare("
            INSERT INTO ecoFacilities (title, category, description, houseNumber, streetName, county, town, postcode, lng, lat, contributor)
            VALUES (:title, :category, :description, :houseNumber, :streetName, :county, :town, :postcode, :lng, :lat, :contributor)
        ");
        $stmt->execute($data);
    }

    // Update an existing facility in the database
    public function updateFacility($id, $data) {
        $stmt = $this->pdo->prepare("
            UPDATE ecoFacilities
            SET title = :title, category = :category, description = :description, 
                houseNumber = :houseNumber, streetName = :streetName, county = :county, town = :town, 
                postcode = :postcode, lng = :lng, lat = :lat, contributor = :contributor
            WHERE id = :id
        ");
        $data[':id'] = $id;
        $stmt->execute($data);
    }

    // Delete a facility by ID from the database
    public function deleteFacility($id) {
        $stmt = $this->pdo->prepare("DELETE FROM ecoFacilities WHERE id = :id");
        $stmt->execute([':id' => $id]);
    }

    // Fetch the status history of a specific facility
    public function getFacilityStatus($facilityId) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM ecoFacilityStatus
            WHERE facilityId = :facilityId
            ORDER BY ROWID DESC
        ");
        $stmt->execute([':facilityId' => $facilityId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Add a new status comment for a facility
    public function addFacilityStatus($facilityId, $statusComment) {
        $stmt = $this->pdo->prepare("
            INSERT INTO ecoFacilityStatus (facilityId, statusComment)
            VALUES (:facilityId, :statusComment)
        ");
        $stmt->execute([
            ':facilityId' => $facilityId,
            ':statusComment' => $statusComment
        ]);
    }

    // Search for facilities based on title, description, category, location, and status
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

        // Add category filter
        if ($category) {
            $query .= " AND ecoFacilities.category = :category";
        }

        // Add location filter
        if ($location) {
            $query .= " AND (ecoFacilities.town LIKE :location 
                        OR ecoFacilities.county LIKE :location 
                        OR ecoFacilities.postcode LIKE :location 
                        OR ecoFacilities.streetName LIKE :location
                        OR ecoFacilities.houseNumber LIKE :location)";
        }

        // Add status filter
        if ($status) {
            $query .= " AND (SELECT statusComment 
                         FROM ecoFacilityStatus 
                         WHERE facilityId = ecoFacilities.id 
                         ORDER BY ROWID DESC LIMIT 1) = :status";
        }

        $query .= " ORDER BY ecoFacilities.title ASC";

        $stmt = $this->pdo->prepare($query);
        
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

        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Fetch paginated facilities for a large dataset
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

        $stmt = $this->pdo->prepare($query);

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

        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Fetch the total number of facilities for pagination
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

        $stmt = $this->pdo->prepare($query);
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

        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

}
