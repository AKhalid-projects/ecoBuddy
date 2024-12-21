<?php
class FacilityController {
    private $facilityModel;

    public function __construct($facilityModel) {
        $this->facilityModel = $facilityModel;
    }

    public function browseFacilities($search = '', $category = null) {
        return $this->facilityModel->searchFacilities($search, $category);
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

    public function getPaginatedFacilities($page, $limit, $search = '', $category = null) {
        $offset = ($page - 1) * $limit;
        return $this->facilityModel->getPaginatedFacilities($offset, $limit, $search, $category);
    }

    public function getTotalFacilities($search = '', $category = null) {
        return $this->facilityModel->getTotalFacilities($search, $category);
    }

}
?>
