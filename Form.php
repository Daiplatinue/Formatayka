<?php

class Form {
    private $last_name;
    private $first_name;
    private $middle_name;
    private $date_of_birth;
    private $gender;
    private $civil_status;
    private $civil_status_other;
    private $tin;
    private $nationality;
    private $religion;
    private $age;
    
    private $pob_bldg;
    private $pob_lot;
    private $pob_street;
    private $pob_subdivision;
    private $pob_barangay;
    private $pob_city;
    private $pob_province;
    private $pob_country;
    private $pob_zip;
    
    private $home_bldg;
    private $home_lot;
    private $home_street;
    private $home_subdivision;
    private $home_barangay;
    private $home_city;
    private $home_province;
    private $home_country;
    private $home_zip;
    
    private $mobile_number;
    private $email_address;
    private $telephone_number;
    
    private $father_last_name;
    private $father_first_name;
    private $father_middle_name;
    
    private $mother_last_name;
    private $mother_first_name;
    private $mother_middle_name;
    
    
    private $form_id;
    private $db_id;
    private $submission_date;
    private $status;
    
   
    public function __construct($formData = []) {
        if (!empty($formData)) {
            $this->setFromArray($formData);
        }
    }
    
    
    public function setFromArray($data) {
        $this->setLastName($data['last_name'] ?? '');
        $this->setFirstName($data['first_name'] ?? '');
        $this->setMiddleName($data['middle_name'] ?? '');
        $this->setDateOfBirth($data['date'] ?? '');
        $this->setGender($data['gender'] ?? '');
        $this->setCivilStatus($data['civil_status'] ?? '');
        $this->setCivilStatusOther($data['others'] ?? '');
        $this->setTin($data['tin'] ?? '');
        $this->setNationality($data['nationality'] ?? '');
        $this->setReligion($data['religion'] ?? '');
        
        $this->setPobBldg($data['rm_flr_unit_no'] ?? '');
        $this->setPobLot($data['house_lot_blk_no'] ?? '');
        $this->setPobStreet($data['street_name'] ?? '');
        $this->setPobSubdivision($data['subdivision'] ?? '');
        $this->setPobBarangay($data['barangay'] ?? '');
        $this->setPobCity($data['city'] ?? '');
        $this->setPobProvince($data['province'] ?? '');
        $this->setPobCountry($data['country'] ?? '');
        $this->setPobZip($data['zip_code'] ?? '');
        
        $this->setHomeBldg($data['home_rm_flr_unit_no'] ?? '');
        $this->setHomeLot($data['home_house_lot_blk_no'] ?? '');
        $this->setHomeStreet($data['home_street_name'] ?? '');
        $this->setHomeSubdivision($data['home_subdivision'] ?? '');
        $this->setHomeBarangay($data['home_barangay'] ?? '');
        $this->setHomeCity($data['home_city'] ?? '');
        $this->setHomeProvince($data['home_province'] ?? '');
        $this->setHomeCountry($data['home_country'] ?? '');
        $this->setHomeZip($data['home_zip_code'] ?? '');
        
        $this->setMobileNumber($data['mobile_number'] ?? '');
        $this->setEmailAddress($data['email_address'] ?? '');
        $this->setTelephoneNumber($data['telephone_number'] ?? '');
        
        $this->setFatherLastName($data['father_last_name'] ?? '');
        $this->setFatherFirstName($data['father_first_name'] ?? '');
        $this->setFatherMiddleName($data['father_middle_name'] ?? '');
        
        $this->setMotherLastName($data['mother_last_name'] ?? '');
        $this->setMotherFirstName($data['mother_first_name'] ?? '');
        $this->setMotherMiddleName($data['mother_middle_name'] ?? '');
        
        $this->setFormId($data['form_id'] ?? '');
        $this->setDbId($data['f_id'] ?? $data['db_id'] ?? null);
        $this->setSubmissionDate($data['submission_date'] ?? date('Y-m-d H:i:s'));
        $this->setStatus($data['status'] ?? 'pending');
        
        if (!empty($data['date'])) {
            $this->calculateAge();
        }
    }
  
    public function toArray() {
        return [
            'last_name' => $this->getLastName(),
            'first_name' => $this->getFirstName(),
            'middle_name' => $this->getMiddleName(),
            'date' => $this->getDateOfBirth(),
            'gender' => $this->getGender(),
            'civil_status' => $this->getCivilStatus(),
            'others' => $this->getCivilStatusOther(),
            'tin' => $this->getTin(),
            'nationality' => $this->getNationality(),
            'religion' => $this->getReligion(),
            
            'rm_flr_unit_no' => $this->getPobBldg(),
            'house_lot_blk_no' => $this->getPobLot(),
            'street_name' => $this->getPobStreet(),
            'subdivision' => $this->getPobSubdivision(),
            'barangay' => $this->getPobBarangay(),
            'city' => $this->getPobCity(),
            'province' => $this->getPobProvince(),
            'country' => $this->getPobCountry(),
            'zip_code' => $this->getPobZip(),
            
            'home_rm_flr_unit_no' => $this->getHomeBldg(),
            'home_house_lot_blk_no' => $this->getHomeLot(),
            'home_street_name' => $this->getHomeStreet(),
            'home_subdivision' => $this->getHomeSubdivision(),
            'home_barangay' => $this->getHomeBarangay(),
            'home_city' => $this->getHomeCity(),
            'home_province' => $this->getHomeProvince(),
            'home_country' => $this->getHomeCountry(),
            'home_zip_code' => $this->getHomeZip(),
            
            'mobile_number' => $this->getMobileNumber(),
            'email_address' => $this->getEmailAddress(),
            'telephone_number' => $this->getTelephoneNumber(),
            
            'father_last_name' => $this->getFatherLastName(),
            'father_first_name' => $this->getFatherFirstName(),
            'father_middle_name' => $this->getFatherMiddleName(),
            
            'mother_last_name' => $this->getMotherLastName(),
            'mother_first_name' => $this->getMotherFirstName(),
            'mother_middle_name' => $this->getMotherMiddleName(),
            
            'form_id' => $this->getFormId(),
            'f_id' => $this->getDbId(),
            'submission_date' => $this->getSubmissionDate(),
            'status' => $this->getStatus()
        ];
    }
    
  
    private function calculateAge() {
        if (empty($this->date_of_birth)) return;
        
        $dob = new DateTime($this->date_of_birth);
        $now = new DateTime();
        $this->age = $now->diff($dob)->y;
    }
    
    
    public function getLastName() {
        return $this->last_name;
    }
    
    public function setLastName($last_name) {
        $this->last_name = $last_name;
        return $this;
    }
    
    public function getFirstName() {
        return $this->first_name;
    }
    
    public function setFirstName($first_name) {
        $this->first_name = $first_name;
        return $this;
    }
    
    public function getMiddleName() {
        return $this->middle_name;
    }
    
    public function setMiddleName($middle_name) {
        $this->middle_name = $middle_name;
        return $this;
    }
    
    public function getFullName() {
        $name = $this->last_name . ', ' . $this->first_name;
        if (!empty($this->middle_name)) {
            $name .= ' ' . $this->middle_name;
        }
        return $name;
    }
    
    public function getDateOfBirth() {
        return $this->date_of_birth;
    }
    
    public function setDateOfBirth($date_of_birth) {
        $this->date_of_birth = $date_of_birth;
        $this->calculateAge();
        return $this;
    }
    
    public function getGender() {
        return $this->gender;
    }
    
    public function setGender($gender) {
        $this->gender = $gender;
        return $this;
    }
    
    public function getCivilStatus() {
        return $this->civil_status;
    }
    
    public function setCivilStatus($civil_status) {
        $this->civil_status = $civil_status;
        return $this;
    }
    
    public function getCivilStatusOther() {
        return $this->civil_status_other;
    }
    
    public function setCivilStatusOther($civil_status_other) {
        $this->civil_status_other = $civil_status_other;
        return $this;
    }
    
    public function getDisplayCivilStatus() {
        if ($this->civil_status == 'others' && !empty($this->civil_status_other)) {
            return $this->civil_status_other;
        }
        return ucfirst($this->civil_status);
    }
    
    public function getTin() {
        return $this->tin;
    }
    
    public function setTin($tin) {
        $this->tin = $tin;
        return $this;
    }
    
    public function getNationality() {
        return $this->nationality;
    }
    
    public function setNationality($nationality) {
        $this->nationality = $nationality;
        return $this;
    }
    
    public function getReligion() {
        return $this->religion;
    }
    
    public function setReligion($religion) {
        $this->religion = $religion;
        return $this;
    }
    
    public function getAge() {
        return $this->age;
    }
    
    
    public function getPobBldg() {
        return $this->pob_bldg;
    }
    
    public function setPobBldg($pob_bldg) {
        $this->pob_bldg = $pob_bldg;
        return $this;
    }
    
    public function getPobLot() {
        return $this->pob_lot;
    }
    
    public function setPobLot($pob_lot) {
        $this->pob_lot = $pob_lot;
        return $this;
    }
    
    public function getPobStreet() {
        return $this->pob_street;
    }
    
    public function setPobStreet($pob_street) {
        $this->pob_street = $pob_street;
        return $this;
    }
    
    public function getPobSubdivision() {
        return $this->pob_subdivision;
    }
    
    public function setPobSubdivision($pob_subdivision) {
        $this->pob_subdivision = $pob_subdivision;
        return $this;
    }
    
    public function getPobBarangay() {
        return $this->pob_barangay;
    }
    
    public function setPobBarangay($pob_barangay) {
        $this->pob_barangay = $pob_barangay;
        return $this;
    }
    
    public function getPobCity() {
        return $this->pob_city;
    }
    
    public function setPobCity($pob_city) {
        $this->pob_city = $pob_city;
        return $this;
    }
    
    public function getPobProvince() {
        return $this->pob_province;
    }
    
    public function setPobProvince($pob_province) {
        $this->pob_province = $pob_province;
        return $this;
    }
    
    public function getPobCountry() {
        return $this->pob_country;
    }
    
    public function setPobCountry($pob_country) {
        $this->pob_country = $pob_country;
        return $this;
    }
    
    public function getPobZip() {
        return $this->pob_zip;
    }
    
    public function setPobZip($pob_zip) {
        $this->pob_zip = $pob_zip;
        return $this;
    }
    
    
    public function getHomeBldg() {
        return $this->home_bldg;
    }
    
    public function setHomeBldg($home_bldg) {
        $this->home_bldg = $home_bldg;
        return $this;
    }
    
    public function getHomeLot() {
        return $this->home_lot;
    }
    
    public function setHomeLot($home_lot) {
        $this->home_lot = $home_lot;
        return $this;
    }
    
    public function getHomeStreet() {
        return $this->home_street;
    }
    
    public function setHomeStreet($home_street) {
        $this->home_street = $home_street;
        return $this;
    }
    
    public function getHomeSubdivision() {
        return $this->home_subdivision;
    }
    
    public function setHomeSubdivision($home_subdivision) {
        $this->home_subdivision = $home_subdivision;
        return $this;
    }
    
    public function getHomeBarangay() {
        return $this->home_barangay;
    }
    
    public function setHomeBarangay($home_barangay) {
        $this->home_barangay = $home_barangay;
        return $this;
    }
    
    public function getHomeCity() {
        return $this->home_city;
    }
    
    public function setHomeCity($home_city) {
        $this->home_city = $home_city;
        return $this;
    }
    
    public function getHomeProvince() {
        return $this->home_province;
    }
    
    public function setHomeProvince($home_province) {
        $this->home_province = $home_province;
        return $this;
    }
    
    public function getHomeCountry() {
        return $this->home_country;
    }
    
    public function setHomeCountry($home_country) {
        $this->home_country = $home_country;
        return $this;
    }
    
    public function getHomeZip() {
        return $this->home_zip;
    }
    
    public function setHomeZip($home_zip) {
        $this->home_zip = $home_zip;
        return $this;
    }
    
    
    public function getMobileNumber() {
        return $this->mobile_number;
    }
    
    public function setMobileNumber($mobile_number) {
        $this->mobile_number = $mobile_number;
        return $this;
    }
    
    public function getEmailAddress() {
        return $this->email_address;
    }
    
    public function setEmailAddress($email_address) {
        $this->email_address = $email_address;
        return $this;
    }
    
    public function getTelephoneNumber() {
        return $this->telephone_number;
    }
    
    public function setTelephoneNumber($telephone_number) {
        $this->telephone_number = $telephone_number;
        return $this;
    }
    
    
    public function getFatherLastName() {
        return $this->father_last_name;
    }
    
    public function setFatherLastName($father_last_name) {
        $this->father_last_name = $father_last_name;
        return $this;
    }
    
    public function getFatherFirstName() {
        return $this->father_first_name;
    }
    
    public function setFatherFirstName($father_first_name) {
        $this->father_first_name = $father_first_name;
        return $this;
    }
    
    public function getFatherMiddleName() {
        return $this->father_middle_name;
    }
    
    public function setFatherMiddleName($father_middle_name) {
        $this->father_middle_name = $father_middle_name;
        return $this;
    }
    
    public function getFatherFullName() {
        $name = $this->father_last_name . ', ' . $this->father_first_name;
        if (!empty($this->father_middle_name)) {
            $name .= ' ' . $this->father_middle_name;
        }
        return $name;
    }
    
    
    public function getMotherLastName() {
        return $this->mother_last_name;
    }
    
    public function setMotherLastName($mother_last_name) {
        $this->mother_last_name = $mother_last_name;
        return $this;
    }
    
    public function getMotherFirstName() {
        return $this->mother_first_name;
    }
    
    public function setMotherFirstName($mother_first_name) {
        $this->mother_first_name = $mother_first_name;
        return $this;
    }
    
    public function getMotherMiddleName() {
        return $this->mother_middle_name;
    }
    
    public function setMotherMiddleName($mother_middle_name) {
        $this->mother_middle_name = $mother_middle_name;
        return $this;
    }
    
    public function getMotherFullName() {
        $name = $this->mother_last_name . ', ' . $this->mother_first_name;
        if (!empty($this->mother_middle_name)) {
            $name .= ' ' . $this->mother_middle_name;
        }
        return $name;
    }
    
    
    public function getFormId() {
        return $this->form_id;
    }
    
    public function setFormId($form_id) {
        $this->form_id = $form_id;
        return $this;
    }
    
    public function getDbId() {
        return $this->db_id;
    }
    
    public function setDbId($db_id) {
        $this->db_id = $db_id;
        return $this;
    }
    
    public function getSubmissionDate() {
        return $this->submission_date;
    }
    
    public function setSubmissionDate($submission_date) {
        $this->submission_date = $submission_date;
        return $this;
    }
    
    public function getStatus() {
        return $this->status;
    }
    
    public function setStatus($status) {
        $this->status = $status;
        return $this;
    }
}

