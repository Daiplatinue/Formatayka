<?php

class FormValidator {
    private $errors = [];
    private $data = [];
    
    /**
     * @param array $data 
     */
    public function __construct($data = []) {
        $this->data = $data;
    }
    
    /**
     * @param array $data 
     */
    public function setData($data) {
        $this->data = $data;
        $this->errors = [];
    }
    
    /**
     * @return array 
     */
    public function getErrors() {
        return $this->errors;
    }
    
    /**
     * @return bool 
     */
    public function hasErrors() {
        return !empty($this->errors);
    }
    
    /**
     * @param string $field 
     * @return string 
     */
    public function getErrorMessage($field) {
        return isset($this->errors[$field]) ? $this->errors[$field] : '';
    }
    
    /**
     * @param string $field 
     * @return bool 
     */
    public function hasError($field) {
        return isset($this->errors[$field]);
    }
    
    /**
     * @return bool 
     */
    public function validate() {
        $this->errors = [];
        
        foreach ($this->data as $key => $value) {
            if (is_string($value)) {
                $this->data[$key] = $this->normalizeSpaces($value);
            }
        }
        
        $this->validateRequiredFields();
        
        $this->validateNameFields();
        
        $this->validateDateOfBirth();
        
        $this->validateNumericFields();
        
        $this->validateContactInformation();
        
        $this->validateCivilStatus();
        
        return !$this->hasErrors();
    }
    
 
    private function validateRequiredFields() {
        $required_fields = [
            'last_name' => 'Last Name',
            'first_name' => 'First Name',
            'date' => 'Date of Birth',
            'gender' => 'Gender',
            'civil_status' => 'Civil Status',
            'mobile_number' => 'Mobile Number',
            'email_address' => 'Email Address'
        ];
        
        foreach ($required_fields as $field => $label) {
            if (empty(trim($this->data[$field] ?? ''))) {
                $this->errors[$field] = "$label is required.";
            }
        }
    }
    
  
    private function validateNameFields() {
        $name_fields = [
            'last_name' => 'Last Name',
            'first_name' => 'First Name',
            'middle_name' => 'Middle Name'
        ];
        
        foreach ($name_fields as $field => $label) {
            if (!empty($this->data[$field]) && !preg_match("/^[a-zA-Z ]*$/", $this->data[$field])) {
                $this->errors[$field] = "$label must contain letters only.";
            }
        }
    }
    
   
    private function validateDateOfBirth() {
        if (!empty($this->data['date'])) {
            $dob = strtotime($this->data['date']);
            $current_year = date('Y');
            $birth_year = date('Y', $dob);
            $age = $current_year - $birth_year;
            
            if ($age < 18) {
                $this->errors['date'] = "You must be at least 18 years old.";
            }
        }
    }
    
  
    private function validateNumericFields() {
        $numeric_fields = [
            'tin' => 'TIN',
            'zip_code' => 'Zip Code',
            'home_zip_code' => 'Home Zip Code',
            'telephone_number' => 'Telephone Number'
        ];
        
        foreach ($numeric_fields as $field => $label) {
            if (!empty($this->data[$field]) && !preg_match("/^[0-9]*$/", $this->data[$field])) {
                $this->errors[$field] = "$label must contain numbers only.";
            }
        }
    }
    
 
    private function validateContactInformation() {
        if (!empty($this->data['mobile_number']) && !preg_match("/^[0-9]{11}$/", $this->data['mobile_number'])) {
            $this->errors['mobile_number'] = "Mobile Number must be 11 digits.";
        }
        
        if (!empty($this->data['email_address']) && !filter_var($this->data['email_address'], FILTER_VALIDATE_EMAIL)) {
            $this->errors['email_address'] = "Invalid email format.";
        }
    }
    
  
    private function validateCivilStatus() {
        if (isset($this->data['civil_status']) && $this->data['civil_status'] == 'others' && empty(trim($this->data['others'] ?? ''))) {
            $this->errors['others'] = "Please specify your civil status.";
        }
    }
    
    /**
     * @param string $data 
     * @return string 
     */
    public function sanitizeInput($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
    
    /**
     * @param string $data 
     * @return string 
     */
    public function normalizeSpaces($data) {
        if (is_string($data)) {
            return preg_replace('/\s+/', ' ', trim($data));
        }
        return $data;
    }
}

