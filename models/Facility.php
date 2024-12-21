<?php
class Facility {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Get all categories
    public function getAllCategories() {
        $stmt = $this->pdo->query("SELECT id, name FROM ecoCategories ORDER BY name ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get all facilities with category names and contributor details
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

    // Get a specific facility by ID
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

    // Create a new facility
    public function createFacility($data) {
        $stmt = $this->pdo->prepare("
            INSERT INTO ecoFacilities (title, category, description, houseNumber, streetName, county, town, postcode, lng, lat, contributor)
            VALUES (:title, :category, :description, :houseNumber, :streetName, :county, :town, :postcode, :lng, :lat, :contributor)
        ");
        $stmt->execute($data);
    }

    // Update an existing facility
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

    // Delete a facility by ID
    public function deleteFacility($id) {
        $stmt = $this->pdo->prepare("DELETE FROM ecoFacilities WHERE id = :id");
        $stmt->execute([':id' => $id]);
    }

    // Get the status history for a specific facility
    public function getFacilityStatus($facilityId) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM ecoFacilityStatus
            WHERE facilityId = :facilityId
            ORDER BY ROWID DESC
        ");
        $stmt->execute([':facilityId' => $facilityId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Add a new status for a facility
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

    public function searchFacilities($search = '', $category = null) {
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

        $query .= " ORDER BY ecoFacilities.title ASC";

        $stmt = $this->pdo->prepare($query);
        $params = [
            ':search' => '%' . $search . '%',
        ];
        if ($category) {
            $params[':category'] = $category;
        }

        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getPaginatedFacilities($offset, $limit, $search = '', $category = null) {
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

        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalFacilities($search = '', $category = null) {
        $query = "
        SELECT COUNT(*) as total 
        FROM ecoFacilities
        WHERE (title LIKE :search OR description LIKE :search)
    ";
        if ($category) {
            $query .= " AND category = :category";
        }

        $stmt = $this->pdo->prepare($query);
        $params = [
            ':search' => '%' . $search . '%',
        ];
        if ($category) {
            $params[':category'] = $category;
        }

        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
}
