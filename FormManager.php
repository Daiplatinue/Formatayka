<?php
require_once 'Form.php';
require_once 'FormValidator.php';
require_once 'DBConnector.php';

class FormManager {
    private $db;
    private $validator;
    
    /**
     * @param DatabaseConnection $db 
     */
    public function __construct(DatabaseConnection $db) {
        $this->db = $db;
        $this->validator = new FormValidator();
    }
    
    /**
     * @param Form $form 
     * @return bool|int 
     */
    public function addForm(Form $form) {
        $conn = $this->db->getConnection();
        $conn->begin_transaction();
        
        try {
            $stmt = $conn->prepare("INSERT INTO form_tb (
                f_ln, f_fn, f_mi, f_dob, f_sex, f_civil, f_tin, f_nationality, f_religion,
                f_pob_bldg, f_pob_lot, f_pob_street, f_pob_subdivision, f_pob_barangay, f_pob_city, f_pob_province, f_pob_country, f_pob_zip,
                f_home_bldg, f_home_lot, f_home_street, f_home_subdivision, f_home_barangay, f_home_city, f_home_province, f_home_country, f_home_zip,
                f_home_mobile, f_home_email, f_home_telephone,
                f_father_ln, f_father_fn, f_father_mi,
                f_mother_ln, f_mother_fn, f_mother_mi,
                f_age
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?,
                ?, ?, ?, ?, ?, ?, ?, ?, ?,
                ?, ?, ?, ?, ?, ?, ?, ?, ?,
                ?, ?, ?,
                ?, ?, ?,
                ?, ?, ?,
                ?
            )");
            
            $lastName = $form->getLastName();
            $firstName = $form->getFirstName();
            $middleName = $form->getMiddleName();
            $dateOfBirth = $form->getDateOfBirth();
            $gender = $form->getGender();
            $civilStatus = $form->getCivilStatus() == 'others' ? $form->getCivilStatusOther() : $form->getCivilStatus();
            $tin = $form->getTin();
            $nationality = $form->getNationality();
            $religion = $form->getReligion();
            
            $pobBldg = $form->getPobBldg();
            $pobLot = $form->getPobLot();
            $pobStreet = $form->getPobStreet();
            $pobSubdivision = $form->getPobSubdivision();
            $pobBarangay = $form->getPobBarangay();
            $pobCity = $form->getPobCity();
            $pobProvince = $form->getPobProvince();
            $pobCountry = $form->getPobCountry();
            $pobZip = $form->getPobZip();
            
            $homeBldg = $form->getHomeBldg();
            $homeLot = $form->getHomeLot();
            $homeStreet = $form->getHomeStreet();
            $homeSubdivision = $form->getHomeSubdivision();
            $homeBarangay = $form->getHomeBarangay();
            $homeCity = $form->getHomeCity();
            $homeProvince = $form->getHomeProvince();
            $homeCountry = $form->getHomeCountry();
            $homeZip = $form->getHomeZip();
            
            $mobileNumber = $form->getMobileNumber();
            $emailAddress = $form->getEmailAddress();
            $telephoneNumber = $form->getTelephoneNumber();
            
            $fatherLastName = $form->getFatherLastName();
            $fatherFirstName = $form->getFatherFirstName();
            $fatherMiddleName = $form->getFatherMiddleName();
            
            $motherLastName = $form->getMotherLastName();
            $motherFirstName = $form->getMotherFirstName();
            $motherMiddleName = $form->getMotherMiddleName();
            
            $age = $form->getAge();
            
            $stmt->bind_param("ssssssssssssssssssssssssssssssssssssi", 
                $lastName, 
                $firstName, 
                $middleName, 
                $dateOfBirth, 
                $gender, 
                $civilStatus, 
                $tin, 
                $nationality, 
                $religion,
                $pobBldg, 
                $pobLot, 
                $pobStreet, 
                $pobSubdivision, 
                $pobBarangay, 
                $pobCity, 
                $pobProvince, 
                $pobCountry, 
                $pobZip,
                $homeBldg, 
                $homeLot, 
                $homeStreet, 
                $homeSubdivision, 
                $homeBarangay, 
                $homeCity, 
                $homeProvince, 
                $homeCountry, 
                $homeZip,
                $mobileNumber, 
                $emailAddress, 
                $telephoneNumber,
                $fatherLastName, 
                $fatherFirstName, 
                $fatherMiddleName,
                $motherLastName, 
                $motherFirstName, 
                $motherMiddleName,
                $age
            );
            
            $stmt->execute();
            $form_id = $conn->insert_id;
            $stmt->close();
            
            $conn->commit();
            
            return $form_id;
        } catch (Exception $e) {
            $conn->rollback();
            throw $e;
        }
    }
    
    /**
     * @param Form $form 
     * @return bool 
     */
    public function updateForm(Form $form) {
        $conn = $this->db->getConnection();
        $conn->begin_transaction();
        
        try {
            $stmt = $conn->prepare("UPDATE form_tb SET 
                f_ln = ?, f_fn = ?, f_mi = ?, f_dob = ?, f_sex = ?, f_civil = ?, 
                f_tin = ?, f_nationality = ?, f_religion = ?,
                f_pob_bldg = ?, f_pob_lot = ?, f_pob_street = ?, f_pob_subdivision = ?, 
                f_pob_barangay = ?, f_pob_city = ?, f_pob_province = ?, f_pob_country = ?, f_pob_zip = ?,
                f_home_bldg = ?, f_home_lot = ?, f_home_street = ?, f_home_subdivision = ?, 
                f_home_barangay = ?, f_home_city = ?, f_home_province = ?, f_home_country = ?, f_home_zip = ?,
                f_home_mobile = ?, f_home_email = ?, f_home_telephone = ?,
                f_father_ln = ?, f_father_fn = ?, f_father_mi = ?,
                f_mother_ln = ?, f_mother_fn = ?, f_mother_mi = ?,
                f_age = ?
                WHERE f_id = ?");
            
            $lastName = $form->getLastName();
            $firstName = $form->getFirstName();
            $middleName = $form->getMiddleName();
            $dateOfBirth = $form->getDateOfBirth();
            $gender = $form->getGender();
            $civilStatus = $form->getCivilStatus() == 'others' ? $form->getCivilStatusOther() : $form->getCivilStatus();
            $tin = $form->getTin();
            $nationality = $form->getNationality();
            $religion = $form->getReligion();
            
            $pobBldg = $form->getPobBldg();
            $pobLot = $form->getPobLot();
            $pobStreet = $form->getPobStreet();
            $pobSubdivision = $form->getPobSubdivision();
            $pobBarangay = $form->getPobBarangay();
            $pobCity = $form->getPobCity();
            $pobProvince = $form->getPobProvince();
            $pobCountry = $form->getPobCountry();
            $pobZip = $form->getPobZip();
            
            $homeBldg = $form->getHomeBldg();
            $homeLot = $form->getHomeLot();
            $homeStreet = $form->getHomeStreet();
            $homeSubdivision = $form->getHomeSubdivision();
            $homeBarangay = $form->getHomeBarangay();
            $homeCity = $form->getHomeCity();
            $homeProvince = $form->getHomeProvince();
            $homeCountry = $form->getHomeCountry();
            $homeZip = $form->getHomeZip();
            
            $mobileNumber = $form->getMobileNumber();
            $emailAddress = $form->getEmailAddress();
            $telephoneNumber = $form->getTelephoneNumber();
            
            $fatherLastName = $form->getFatherLastName();
            $fatherFirstName = $form->getFatherFirstName();
            $fatherMiddleName = $form->getFatherMiddleName();
            
            $motherLastName = $form->getMotherLastName();
            $motherFirstName = $form->getMotherFirstName();
            $motherMiddleName = $form->getMotherMiddleName();
            
            $age = $form->getAge();
            $dbId = $form->getDbId();
            
            $stmt->bind_param("sssssssssssssssssssssssssssssssssssssi", 
                $lastName, 
                $firstName, 
                $middleName, 
                $dateOfBirth, 
                $gender, 
                $civilStatus, 
                $tin, 
                $nationality, 
                $religion,
                $pobBldg, 
                $pobLot, 
                $pobStreet, 
                $pobSubdivision, 
                $pobBarangay, 
                $pobCity, 
                $pobProvince, 
                $pobCountry, 
                $pobZip,
                $homeBldg, 
                $homeLot, 
                $homeStreet, 
                $homeSubdivision, 
                $homeBarangay, 
                $homeCity, 
                $homeProvince, 
                $homeCountry, 
                $homeZip,
                $mobileNumber, 
                $emailAddress, 
                $telephoneNumber,
                $fatherLastName, 
                $fatherFirstName, 
                $fatherMiddleName,
                $motherLastName, 
                $motherFirstName, 
                $motherMiddleName,
                $age,
                $dbId
            );
            
            $stmt->execute();
            $stmt->close();
            
            $conn->commit();
            
            return true;
        } catch (Exception $e) {
            $conn->rollback();
            throw $e;
        }
    }
    
    /**
     * @param int $form_id 
     * @return bool 
     */
    public function deleteForm($form_id) {
        $conn = $this->db->getConnection();
        $conn->begin_transaction();
        
        try {
            $stmt = $conn->prepare("DELETE FROM form_tb WHERE f_id = ?");
            $stmt->bind_param("i", $form_id);
            $stmt->execute();
            $stmt->close();
            
            $conn->commit();
            
            return true;
        } catch (Exception $e) {
            $conn->rollback();
            throw $e;
        }
    }
    
    /**
     * @param int $form_id 
     * @return Form|null 
     */
    public function getFormById($form_id) {
        $conn = $this->db->getConnection();
        
        $stmt = $conn->prepare("SELECT * FROM form_tb WHERE f_id = ?");
        $stmt->bind_param("i", $form_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            $form = new Form();
            $form->setLastName($row['f_ln'])
                 ->setFirstName($row['f_fn'])
                 ->setMiddleName($row['f_mi'])
                 ->setDateOfBirth($row['f_dob'])
                 ->setGender($row['f_sex'])
                 ->setCivilStatus($row['f_civil'])
                 ->setTin($row['f_tin'])
                 ->setNationality($row['f_nationality'])
                 ->setReligion($row['f_religion'])
                 ->setPobBldg($row['f_pob_bldg'])
                 ->setPobLot($row['f_pob_lot'])
                 ->setPobStreet($row['f_pob_street'])
                 ->setPobSubdivision($row['f_pob_subdivision'])
                 ->setPobBarangay($row['f_pob_barangay'])
                 ->setPobCity($row['f_pob_city'])
                 ->setPobProvince($row['f_pob_province'])
                 ->setPobCountry($row['f_pob_country'])
                 ->setPobZip($row['f_pob_zip'])
                 ->setHomeBldg($row['f_home_bldg'])
                 ->setHomeLot($row['f_home_lot'])
                 ->setHomeStreet($row['f_home_street'])
                 ->setHomeSubdivision($row['f_home_subdivision'])
                 ->setHomeBarangay($row['f_home_barangay'])
                 ->setHomeCity($row['f_home_city'])
                 ->setHomeProvince($row['f_home_province'])
                 ->setHomeCountry($row['f_home_country'])
                 ->setHomeZip($row['f_home_zip'])
                 ->setMobileNumber($row['f_home_mobile'])
                 ->setEmailAddress($row['f_home_email'])
                 ->setTelephoneNumber($row['f_home_telephone'])
                 ->setFatherLastName($row['f_father_ln'])
                 ->setFatherFirstName($row['f_father_fn'])
                 ->setFatherMiddleName($row['f_father_mi'])
                 ->setMotherLastName($row['f_mother_ln'])
                 ->setMotherFirstName($row['f_mother_fn'])
                 ->setMotherMiddleName($row['f_mother_mi'])
                 ->setDbId($row['f_id']);
            
            $stmt->close();
            
            return $form;
        }
        
        $stmt->close();
        
        return null;
    }
    
    /**
     * @return array 
     */
    public function getAllForms() {
        $conn = $this->db->getConnection();
        
        $stmt = $conn->prepare("SELECT * FROM form_tb");
        $stmt->execute();
        $result = $stmt->get_result();
        
        $forms = [];
        
        while ($row = $result->fetch_assoc()) {
            $form = new Form();
            $form->setLastName($row['f_ln'])
                 ->setFirstName($row['f_fn'])
                 ->setMiddleName($row['f_mi'])
                 ->setDateOfBirth($row['f_dob'])
                 ->setGender($row['f_sex'])
                 ->setCivilStatus($row['f_civil'])
                 ->setTin($row['f_tin'])
                 ->setNationality($row['f_nationality'])
                 ->setReligion($row['f_religion'])
                 ->setPobBldg($row['f_pob_bldg'])
                 ->setPobLot($row['f_pob_lot'])
                 ->setPobStreet($row['f_pob_street'])
                 ->setPobSubdivision($row['f_pob_subdivision'])
                 ->setPobBarangay($row['f_pob_barangay'])
                 ->setPobCity($row['f_pob_city'])
                 ->setPobProvince($row['f_pob_province'])
                 ->setPobCountry($row['f_pob_country'])
                 ->setPobZip($row['f_pob_zip'])
                 ->setHomeBldg($row['f_home_bldg'])
                 ->setHomeLot($row['f_home_lot'])
                 ->setHomeStreet($row['f_home_street'])
                 ->setHomeSubdivision($row['f_home_subdivision'])
                 ->setHomeBarangay($row['f_home_barangay'])
                 ->setHomeCity($row['f_home_city'])
                 ->setHomeProvince($row['f_home_province'])
                 ->setHomeCountry($row['f_home_country'])
                 ->setHomeZip($row['f_home_zip'])
                 ->setMobileNumber($row['f_home_mobile'])
                 ->setEmailAddress($row['f_home_email'])
                 ->setTelephoneNumber($row['f_home_telephone'])
                 ->setFatherLastName($row['f_father_ln'])
                 ->setFatherFirstName($row['f_father_fn'])
                 ->setFatherMiddleName($row['f_father_mi'])
                 ->setMotherLastName($row['f_mother_ln'])
                 ->setMotherFirstName($row['f_mother_fn'])
                 ->setMotherMiddleName($row['f_mother_mi'])
                 ->setDbId($row['f_id']);
            
            $forms[] = $form;
        }
        
        $stmt->close();
        
        return $forms;
    }
    
    /**
     * @param array $data 
     * @return bool 
     */
    public function validateForm($data) {
        $this->validator->setData($data);
        return $this->validator->validate();
    }
    
    /**
     * @return array 
     */
    public function getValidationErrors() {
        return $this->validator->getErrors();
    }
}