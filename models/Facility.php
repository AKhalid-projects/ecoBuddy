<?php
class Facility {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAllFacilities() {
        $stmt = $this->pdo->query("SELECT * FROM eco_facilities");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getFacilityById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM eco_facilities WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createFacility($data) {
        $stmt = $this->pdo->prepare("
            INSERT INTO eco_facilities (title, category, description, location, latitude, longitude, status, image_path)
            VALUES (:title, :category, :description, :location, :latitude, :longitude, :status, :image_path)
        ");
        $stmt->execute($data);
    }
}
?>
