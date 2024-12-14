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
        if (!$stmt) {
            die("Error in query preparation: " . implode(":", $this->pdo->errorInfo()));
        }

        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            die("Facility with ID $id not found.");
        }

        return $result;
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
}
?>
