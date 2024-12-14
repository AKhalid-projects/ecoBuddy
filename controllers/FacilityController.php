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
    }

    public function updateFacility($id, $data) {
        $this->facilityModel->updateFacility($id, $data);
    }

    public function deleteFacility($id) {
        $this->facilityModel->deleteFacility($id);
    }
}
?>
