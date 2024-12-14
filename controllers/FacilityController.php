<?php
class FacilityController {
    private $facilityModel;

    public function __construct($facilityModel) {
        $this->facilityModel = $facilityModel;
    }

    public function browseFacilities() {
        return $this->facilityModel->getAllFacilities();
    }

    public function createFacility($data) {
        $this->facilityModel->createFacility($data);
        header("Location: /ecoBuddy/views/facilities/browse.php");
    }
}
?>
