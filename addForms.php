<?php
session_start();

if (!isset($_SESSION['forms'])) {
    $_SESSION['forms'] = [];
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "form_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function validate_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$editMode = false;
$editFormId = '';
$formData = [];

if (isset($_GET['edit']) && $_GET['edit'] === 'true' && isset($_SESSION['edit_form']) && isset($_SESSION['edit_form_id'])) {
    $editMode = true;
    $editFormId = $_SESSION['edit_form_id'];
    $formData = $_SESSION['edit_form'];
    unset($_SESSION['edit_form']);
    unset($_SESSION['edit_form_id']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];

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
        if (empty(trim($_POST[$field] ?? ''))) {
            $errors[$field] = "$label is required.";
        }
    }

    if (!empty($_POST['last_name']) && !preg_match("/^[a-zA-Z ]*$/", $_POST['last_name'])) {
        $errors['last_name'] = "Last Name must contain letters only.";
    }

    if (!empty($_POST['first_name']) && !preg_match("/^[a-zA-Z ]*$/", $_POST['first_name'])) {
        $errors['first_name'] = "First Name must contain letters only.";
    }

    if (!empty($_POST['middle_name']) && !preg_match("/^[a-zA-Z ]*$/", $_POST['middle_name'])) {
        $errors['middle_name'] = "Middle Name must contain letters only.";
    }

    if (!empty($_POST['date'])) {
        $dob = strtotime($_POST['date']);
        $current_year = date('Y');
        $birth_year = date('Y', $dob);
        $age = $current_year - $birth_year;

        if ($age < 18) {
            $errors['date'] = "You must be at least 18 years old.";
        }
    }

    if (!empty($_POST['tin']) && !preg_match("/^[0-9]*$/", $_POST['tin'])) {
        $errors['tin'] = "TIN must contain numbers only.";
    }

    if (!empty($_POST['mobile_number']) && !preg_match("/^[0-9]{11}$/", $_POST['mobile_number'])) {
        $errors['mobile_number'] = "Mobile Number must be 11 digits.";
    }

    if (!empty($_POST['email_address']) && !filter_var($_POST['email_address'], FILTER_VALIDATE_EMAIL)) {
        $errors['email_address'] = "Invalid email format.";
    }

    if (!empty($_POST['telephone_number']) && !preg_match("/^[0-9]*$/", $_POST['telephone_number'])) {
        $errors['telephone_number'] = "Telephone Number must contain numbers only.";
    }

    if (!empty($_POST['zip_code']) && !preg_match("/^[0-9]*$/", $_POST['zip_code'])) {
        $errors['zip_code'] = "Zip Code must contain numbers only.";
    }

    if (!empty($_POST['home_zip_code']) && !preg_match("/^[0-9]*$/", $_POST['home_zip_code'])) {
        $errors['home_zip_code'] = "Home Zip Code must contain numbers only.";
    }

    if (isset($_POST['civil_status']) && $_POST['civil_status'] == 'others' && empty(trim($_POST['others'] ?? ''))) {
        $errors['others'] = "Please specify your civil status.";
    }

    if (empty($errors)) {
        $dob = new DateTime($_POST['date']);
        $now = new DateTime();
        $age = $now->diff($dob)->y;
        
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
            
            $last_name = $_POST['last_name'];
            $first_name = $_POST['first_name'];
            $middle_name = $_POST['middle_name'];
            $date = $_POST['date'];
            $gender = $_POST['gender'];
            $civil_status = $_POST['civil_status'] == 'others' ? $_POST['others'] : $_POST['civil_status'];
            $tin = $_POST['tin'];
            $nationality = $_POST['nationality'];
            $religion = $_POST['religion'];
            
            $pob_bldg = $_POST['rm_flr_unit_no'];
            $pob_lot = $_POST['house_lot_blk_no'];
            $pob_street = $_POST['street_name'];
            $pob_subdivision = $_POST['subdivision'];
            $pob_barangay = $_POST['barangay'];
            $pob_city = $_POST['city'];
            $pob_province = $_POST['province'];
            $pob_country = $_POST['country'];
            $pob_zip = $_POST['zip_code'];
            
            $home_bldg = $_POST['home_rm_flr_unit_no'];
            $home_lot = $_POST['home_house_lot_blk_no'];
            $home_street = $_POST['home_street_name'];
            $home_subdivision = $_POST['home_subdivision'];
            $home_barangay = $_POST['home_barangay'];
            $home_city = $_POST['home_city'];
            $home_province = $_POST['home_province'];
            $home_country = $_POST['home_country'];
            $home_zip = $_POST['home_zip_code'];
            
            $mobile_number = $_POST['mobile_number'];
            $email_address = $_POST['email_address'];
            $telephone_number = $_POST['telephone_number'];
            
            $father_last_name = $_POST['father_last_name'];
            $father_first_name = $_POST['father_first_name'];
            $father_middle_name = $_POST['father_middle_name'];
            
            $mother_last_name = $_POST['mother_last_name'];
            $mother_first_name = $_POST['mother_first_name'];
            $mother_middle_name = $_POST['mother_middle_name'];
            
            $stmt->bind_param("ssssssssssssssssssssssssssssssssssssi", 
                $last_name, $first_name, $middle_name, $date, $gender, $civil_status, $tin, $nationality, $religion,
                $pob_bldg, $pob_lot, $pob_street, $pob_subdivision, $pob_barangay, $pob_city, $pob_province, $pob_country, $pob_zip,
                $home_bldg, $home_lot, $home_street, $home_subdivision, $home_barangay, $home_city, $home_province, $home_country, $home_zip,
                $mobile_number, $email_address, $telephone_number,
                $father_last_name, $father_first_name, $father_middle_name,
                $mother_last_name, $mother_first_name, $mother_middle_name,
                $age
            );
            
            $stmt->execute();
            $form_id = $conn->insert_id;
            $stmt->close();
            
            $conn->commit();
            
            $_POST['submission_date'] = date('Y-m-d H:i:s');
            if (!isset($_POST['status'])) {
                $_POST['status'] = 'pending';
            }

            if (isset($_POST['form_id']) && $_POST['form_id']) {
                $formId = $_POST['form_id'];
                if (isset($_SESSION['forms'][$formId])) {
                    $_SESSION['forms'][$formId] = $_POST;
                    $_SESSION['message'] = "Form updated successfully and saved to database!";
                }
            } else {
                $formId = uniqid();
                $_SESSION['forms'][$formId] = $_POST;
                $_SESSION['message'] = "Form added successfully and saved to database!";
            }

            header("Location: index.php");
            exit();
            
        } catch (Exception $e) {
            $conn->rollback();
            $_SESSION['message'] = "Error: " . $e->getMessage();
            $formData = $_POST;
        }
    } else {
        $formData = $_POST;
    }
}

$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

$countries = ["Afghanistan", "Albania", "Algeria", "Andorra", "Angola", "Antigua and Barbuda", "Argentina", "Armenia", "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bhutan", "Bolivia", "Bosnia and Herzegovina", "Botswana", "Brazil", "Brunei", "Bulgaria", "Burkina Faso", "Burundi", "Cabo Verde", "Cambodia", "Cameroon", "Canada", "Central African Republic", "Chad", "Chile", "China", "Colombia", "Comoros", "Congo, Democratic Republic of the", "Congo, Republic of the", "Costa Rica", "Croatia", "Cuba", "Cyprus", "Czech Republic", "Denmark", "Djibouti", "Dominica", "Dominican Republic", "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Eswatini", "Ethiopia", "Fiji", "Finland", "France", "Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Greece", "Grenada", "Guatemala", "Guinea", "Guinea-Bissau", "Guyana", "Haiti", "Honduras", "Hungary", "Iceland", "India", "Indonesia", "Iran", "Iraq", "Ireland", "Israel", "Italy", "Jamaica", "Japan", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Korea, North", "Korea, South", "Kosovo", "Kuwait", "Kyrgyzstan", "Laos", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libya", "Liechtenstein", "Lithuania", "Luxembourg", "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Mauritania", "Mauritius", "Mexico", "Micronesia", "Moldova", "Monaco", "Mongolia", "Montenegro", "Morocco", "Mozambique", "Myanmar", "Namibia", "Nauru", "Nepal", "Netherlands", "New Zealand", "Nicaragua", "Niger", "Nigeria", "North Macedonia", "Norway", "Oman", "Pakistan", "Palau", "Palestine", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Poland", "Portugal", "Qatar", "Romania", "Russia", "Rwanda", "Saint Kitts and Nevis", "Saint Lucia", "Saint Vincent and the Grenadines", "Samoa", "San Marino", "Sao Tome and Principe", "Saudi Arabia", "Senegal", "Serbia", "Seychelles", "Sierra Leone", "Singapore", "Slovakia", "Slovenia", "Solomon Islands", "Somalia", "South Africa", "South Sudan", "Spain", "Sri Lanka", "Sudan", "Suriname", "Sweden", "Switzerland", "Syria", "Taiwan", "Tajikistan", "Tanzania", "Thailand", "Timor-Leste", "Togo", "Tonga", "Trinidad and Tobago", "Tunisia", "Turkey", "Turkmenistan", "Tuvalu", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States", "Uruguay", "Uzbekistan", "Vanuatu", "Vatican City", "Venezuela", "Vietnam", "Yemen", "Zambia", "Zimbabwe"];


function hasError($field)
{
    global $errors;
    return isset($errors[$field]) ? 'error' : '';
}

function getErrorMessage($field)
{
    global $errors;
    return isset($errors[$field]) ? $errors[$field] : '';
}

function displayError($field)
{
    global $errors;
    if (isset($errors[$field])) {
        return '<span class="error-message">' . $errors[$field] . '</span>';
    }
    return '';
}

function showOthersField()
{
    global $formData;
    return (isset($formData['civil_status']) && $formData['civil_status'] == 'others') ? 'block' : 'none';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title><?php echo $editMode ? 'Edit Form' : 'Add New Form'; ?> - Form Management</title>
</head>

<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="profile">
                <div class="avatar">
                    <img src="https://s.yimg.com/ny/api/res/1.2/Fy1l68nZNOmuqCJJYJMXyg--/YXBwaWQ9aGlnaGxhbmRlcjt3PTEyNDI7aD02OTk-/https://media.zenfs.com/en/comingsoon_net_477/7725134fd139ac39088f280df2d2dad9" alt="Profile">
                </div>
                <div class="profile-info">
                    <h3>Mañacap, Vince E.</h3>
                    <p>BSIT 3C</p>
                </div>
            </div>

            <nav class="sidebar-menu">
                <a href="index.php" class="menu-item">
                    <i class="fas fa-th-large"></i>
                    <span>Dashboard</span>
                </a>
                <a href="viewForms.php" class="menu-item">
                    <i class="fas fa-file-alt"></i>
                    <span>View Forms</span>
                </a>
                <a href="addForms.php" class="menu-item active">
                    <i class="fas fa-plus-circle"></i>
                    <span>Add Forms</span>
                </a>
            </nav>

            <div class="logout">
                <a href="#" class="menu-item">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Log Out</span>
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <header class="dashboard-header">
                <div class="header-left">
                    <h1><?php echo $editMode ? 'Edit Form' : 'Add New Form'; ?></h1>
                    <p class="date"><?php echo date('jS M Y'); ?></p>
                </div>
                <div class="header-right">
                    <div class="search-bar">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Search">
                    </div>
                    <div class="header-icons">
                        <a href="#" class="icon-link"><i class="fas fa-bell"></i></a>
                        <a href="#" class="icon-link"><i class="fas fa-moon"></i></a>
                        <a href="#" class="icon-link"><i class="fas fa-cog"></i></a>
                    </div>
                    <div class="user-profile">
                        <img src="https://s.yimg.com/ny/api/res/1.2/Fy1l68nZNOmuqCJJYJMXyg--/YXBwaWQ9aGlnaGxhbmRlcjt3PTEyNDI7aD02OTk-/https://media.zenfs.com/en/comingsoon_net_477/7725134fd139ac39088f280df2d2dad9" alt="User">
                        <span>Mañacap, Vince Edward C.</span>
                    </div>
                </div>
            </header>

            <?php if (!empty($message)): ?>
                <div class="message">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <div class="form-container">
                <div class="form-header">
                    <h2><i class="fas fa-user-edit"></i> <?php echo $editMode ? 'Edit Personal Information' : 'Add Personal Information'; ?></h2>
                    <p>Please fill in the form below. Fields marked with <span class="required">*</span> are required.</p>
                </div>

                <form method="post" action="addForms.php" class="personal-form">
                    <?php if ($editMode): ?>
                        <input type="hidden" name="form_id" value="<?php echo $editFormId; ?>">
                        <input type="hidden" name="status" value="<?php echo htmlspecialchars($formData['status'] ?? 'pending'); ?>">
                        <input type="hidden" name="submission_date" value="<?php echo htmlspecialchars($formData['submission_date'] ?? date('Y-m-d H:i:s')); ?>">
                    <?php endif; ?>

                    <div class="form-section">
                        <h3><i class="fas fa-user-circle"></i> Personal Data</h3>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="last_name">Last Name <span class="required">*</span></label>
                                <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($formData['last_name'] ?? ''); ?>" class="<?php echo hasError('last_name'); ?>" required>
                                <?php echo displayError('last_name'); ?>
                            </div>

                            <div class="form-group">
                                <label for="first_name">First Name <span class="required">*</span></label>
                                <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($formData['first_name'] ?? ''); ?>" class="<?php echo hasError('first_name'); ?>" required>
                                <?php echo displayError('first_name'); ?>
                            </div>

                            <div class="form-group">
                                <label for="middle_name">Middle Name</label>
                                <input type="text" id="middle_name" name="middle_name" value="<?php echo htmlspecialchars($formData['middle_name'] ?? ''); ?>" class="<?php echo hasError('middle_name'); ?>">
                                <?php echo displayError('middle_name'); ?>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="date">Date of Birth <span class="required">*</span></label>
                                <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($formData['date'] ?? ''); ?>" class="<?php echo hasError('date'); ?>" required>
                                <?php echo displayError('date'); ?>
                            </div>

                            <div class="form-group">
                                <label>Sex <span class="required">*</span></label>
                                <div class="radio-group <?php echo hasError('gender'); ?>">
                                    <div class="radio-option">
                                        <input type="radio" id="male" name="gender" value="male" <?php if (isset($formData['gender']) && $formData['gender'] == 'male') echo 'checked'; ?> required>
                                        <label for="male">Male</label>
                                    </div>
                                    <div class="radio-option">
                                        <input type="radio" id="female" name="gender" value="female" <?php if (isset($formData['gender']) && $formData['gender'] == 'female') echo 'checked'; ?>>
                                        <label for="female">Female</label>
                                    </div>
                                </div>
                                <?php echo displayError('gender'); ?>
                            </div>

                            <div class="form-group">
                                <label for="civil_status">Civil Status <span class="required">*</span></label>
                                <select name="civil_status" id="civil_status" class="<?php echo hasError('civil_status'); ?>" required onchange="toggleOthersField()">
                                    <option value="">Select Civil Status</option>
                                    <option value="single" <?php if (isset($formData['civil_status']) && $formData['civil_status'] == 'single') echo 'selected'; ?>>Single</option>
                                    <option value="married" <?php if (isset($formData['civil_status']) && $formData['civil_status'] == 'married') echo 'selected'; ?>>Married</option>
                                    <option value="widowed" <?php if (isset($formData['civil_status']) && $formData['civil_status'] == 'widowed') echo 'selected'; ?>>Widowed</option>
                                    <option value="separated" <?php if (isset($formData['civil_status']) && $formData['civil_status'] == 'separated') echo 'selected'; ?>>Legally Separated</option>
                                    <option value="others" <?php if (isset($formData['civil_status']) && $formData['civil_status'] == 'others') echo 'selected'; ?>>Others</option>
                                </select>
                                <?php echo displayError('civil_status'); ?>
                            </div>
                        </div>

                        <div id="othersField" style="display: <?php echo showOthersField(); ?>;">
                            <div class="form-group">
                                <label for="others">Please Specify <span class="required">*</span></label>
                                <input type="text" id="others" name="others" value="<?php echo htmlspecialchars($formData['others'] ?? ''); ?>" class="<?php echo hasError('others'); ?>">
                                <?php echo displayError('others'); ?>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="tin">Tax Identification Number (TIN)</label>
                                <input type="text" id="tin" name="tin" value="<?php echo htmlspecialchars($formData['tin'] ?? ''); ?>" class="<?php echo hasError('tin'); ?>">
                                <?php echo displayError('tin'); ?>
                            </div>

                            <div class="form-group">
                                <label for="nationality">Nationality</label>
                                <input type="text" id="nationality" name="nationality" value="<?php echo htmlspecialchars($formData['nationality'] ?? ''); ?>" class="<?php echo hasError('nationality'); ?>">
                                <?php echo displayError('nationality'); ?>
                            </div>

                            <div class="form-group">
                                <label for="religion">Religion</label>
                                <input type="text" id="religion" name="religion" value="<?php echo htmlspecialchars($formData['religion'] ?? ''); ?>" class="<?php echo hasError('religion'); ?>">
                                <?php echo displayError('religion'); ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3><i class="fas fa-map-marker-alt"></i> Place of Birth</h3>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="rm_flr_unit_no">RM/FLR/Unit No. & Bldg. Name</label>
                                <input type="text" id="rm_flr_unit_no" name="rm_flr_unit_no" value="<?php echo htmlspecialchars($formData['rm_flr_unit_no'] ?? ''); ?>" class="<?php echo hasError('rm_flr_unit_no'); ?>">
                                <?php echo displayError('rm_flr_unit_no'); ?>
                            </div>

                            <div class="form-group">
                                <label for="house_lot_blk_no">House/Lot & Blk. No</label>
                                <input type="text" id="house_lot_blk_no" name="house_lot_blk_no" value="<?php echo htmlspecialchars($formData['house_lot_blk_no'] ?? ''); ?>" class="<?php echo hasError('house_lot_blk_no'); ?>">
                                <?php echo displayError('house_lot_blk_no'); ?>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="street_name">Street Name</label>
                                <input type="text" id="street_name" name="street_name" value="<?php echo htmlspecialchars($formData['street_name'] ?? ''); ?>" class="<?php echo hasError('street_name'); ?>">
                                <?php echo displayError('street_name'); ?>
                            </div>

                            <div class="form-group">
                                <label for="subdivision">Subdivision</label>
                                <input type="text" id="subdivision" name="subdivision" value="<?php echo htmlspecialchars($formData['subdivision'] ?? ''); ?>" class="<?php echo hasError('subdivision'); ?>">
                                <?php echo displayError('subdivision'); ?>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="barangay">Barangay/District/Locality</label>
                                <input type="text" id="barangay" name="barangay" value="<?php echo htmlspecialchars($formData['barangay'] ?? ''); ?>" class="<?php echo hasError('barangay'); ?>">
                                <?php echo displayError('barangay'); ?>
                            </div>

                            <div class="form-group">
                                <label for="city">City/Municipality</label>
                                <input type="text" id="city" name="city" value="<?php echo htmlspecialchars($formData['city'] ?? ''); ?>" class="<?php echo hasError('city'); ?>">
                                <?php echo displayError('city'); ?>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="province">Province</label>
                                <input type="text" id="province" name="province" value="<?php echo htmlspecialchars($formData['province'] ?? ''); ?>" class="<?php echo hasError('province'); ?>">
                                <?php echo displayError('province'); ?>
                            </div>

                            <div class="form-group">
                                <label for="country">Country</label>
                                <select id="country" name="country" class="<?php echo hasError('country'); ?>">
                                    <option value="">Select Country</option>
                                    <?php
                                    foreach ($countries as $country) {
                                        $selected = (isset($formData['country']) && $formData['country'] == $country) ? 'selected' : '';
                                        echo "<option value=\"$country\" $selected>$country</option>";
                                    }
                                    ?>
                                </select>
                                <?php echo displayError('country'); ?>
                            </div>

                            <div class="form-group">
                                <label for="zip_code">Zip Code</label>
                                <input type="text" id="zip_code" name="zip_code" value="<?php echo htmlspecialchars($formData['zip_code'] ?? ''); ?>" class="<?php echo hasError('zip_code'); ?>">
                                <?php echo displayError('zip_code'); ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3><i class="fas fa-home"></i> Home Address</h3>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="home_rm_flr_unit_no">RM/FLR/Unit No. & Bldg. Name</label>
                                <input type="text" id="home_rm_flr_unit_no" name="home_rm_flr_unit_no" value="<?php echo htmlspecialchars($formData['home_rm_flr_unit_no'] ?? ''); ?>" class="<?php echo hasError('home_rm_flr_unit_no'); ?>">
                                <?php echo displayError('home_rm_flr_unit_no'); ?>
                            </div>

                            <div class="form-group">
                                <label for="home_house_lot_blk_no">House/Lot & Blk. No</label>
                                <input type="text" id="home_house_lot_blk_no" name="home_house_lot_blk_no" value="<?php echo htmlspecialchars($formData['home_house_lot_blk_no'] ?? ''); ?>" class="<?php echo hasError('home_house_lot_blk_no'); ?>">
                                <?php echo displayError('home_house_lot_blk_no'); ?>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="home_street_name">Street Name</label>
                                <input type="text" id="home_street_name" name="home_street_name" value="<?php echo htmlspecialchars($formData['home_street_name'] ?? ''); ?>" class="<?php echo hasError('home_street_name'); ?>">
                                <?php echo displayError('home_street_name'); ?>
                            </div>

                            <div class="form-group">
                                <label for="home_subdivision">Subdivision</label>
                                <input type="text" id="home_subdivision" name="home_subdivision" value="<?php echo htmlspecialchars($formData['home_subdivision'] ?? ''); ?>" class="<?php echo hasError('home_subdivision'); ?>">
                                <?php echo displayError('home_subdivision'); ?>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="home_barangay">Barangay/District/Locality</label>
                                <input type="text" id="home_barangay" name="home_barangay" value="<?php echo htmlspecialchars($formData['home_barangay'] ?? ''); ?>" class="<?php echo hasError('home_barangay'); ?>">
                                <?php echo displayError('home_barangay'); ?>
                            </div>

                            <div class="form-group">
                                <label for="home_city">City/Municipality</label>
                                <input type="text" id="home_city" name="home_city" value="<?php echo htmlspecialchars($formData['home_city'] ?? ''); ?>" class="<?php echo hasError('home_city'); ?>">
                                <?php echo displayError('home_city'); ?>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="home_province">Province</label>
                                <input type="text" id="home_province" name="home_province" value="<?php echo htmlspecialchars($formData['home_province'] ?? ''); ?>" class="<?php echo hasError('home_province'); ?>">
                                <?php echo displayError('home_province'); ?>
                            </div>

                            <div class="form-group">
                                <label for="home_country">Country</label>
                                <select id="home_country" name="home_country" class="<?php echo hasError('home_country'); ?>">
                                    <option value="">Select Country</option>
                                    <?php
                                    foreach ($countries as $country) {
                                        $selected = (isset($formData['home_country']) && $formData['home_country'] == $country) ? 'selected' : '';
                                        echo "<option value=\"$country\" $selected>$country</option>";
                                    }
                                    ?>
                                </select>
                                <?php echo displayError('home_country'); ?>
                            </div>

                            <div class="form-group">
                                <label for="home_zip_code">Zip Code</label>
                                <input type="text" id="home_zip_code" name="home_zip_code" value="<?php echo htmlspecialchars($formData['home_zip_code'] ?? ''); ?>" class="<?php echo hasError('home_zip_code'); ?>">
                                <?php echo displayError('home_zip_code'); ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3><i class="fas fa-phone-alt"></i> Contact Information</h3>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="mobile_number">Mobile / Cellphone Number <span class="required">*</span></label>
                                <input type="text" id="mobile_number" name="mobile_number" value="<?php echo htmlspecialchars($formData['mobile_number'] ?? ''); ?>" class="<?php echo hasError('mobile_number'); ?>" required>
                                <?php echo displayError('mobile_number'); ?>
                            </div>

                            <div class="form-group">
                                <label for="email_address">E-mail Address <span class="required">*</span></label>
                                <input type="email" id="email_address" name="email_address" value="<?php echo htmlspecialchars($formData['email_address'] ?? ''); ?>" class="<?php echo hasError('email_address'); ?>" required>
                                <?php echo displayError('email_address'); ?>
                            </div>

                            <div class="form-group">
                                <label for="telephone_number">Telephone Number</label>
                                <input type="text" id="telephone_number" name="telephone_number" value="<?php echo htmlspecialchars($formData['telephone_number'] ?? ''); ?>" class="<?php echo hasError('telephone_number'); ?>">
                                <?php echo displayError('telephone_number'); ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3><i class="fas fa-male"></i> Father's Name</h3>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="father_last_name">Last Name</label>
                                <input type="text" id="father_last_name" name="father_last_name" value="<?php echo htmlspecialchars($formData['father_last_name'] ?? ''); ?>" class="<?php echo hasError('father_last_name'); ?>">
                                <?php echo displayError('father_last_name'); ?>
                            </div>

                            <div class="form-group">
                                <label for="father_first_name">First Name</label>
                                <input type="text" id="father_first_name" name="father_first_name" value="<?php echo htmlspecialchars($formData['father_first_name'] ?? ''); ?>" class="<?php echo hasError('father_first_name'); ?>">
                                <?php echo displayError('father_first_name'); ?>
                            </div>

                            <div class="form-group">
                                <label for="father_middle_name">Middle Name</label>
                                <input type="text" id="father_middle_name" name="father_middle_name" value="<?php echo htmlspecialchars($formData['father_middle_name'] ?? ''); ?>" class="<?php echo hasError('father_middle_name'); ?>">
                                <?php echo displayError('father_middle_name'); ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3><i class="fas fa-female"></i> Mother's Maiden Name</h3>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="mother_last_name">Last Name</label>
                                <input type="text" id="mother_last_name" name="mother_last_name" value="<?php echo htmlspecialchars($formData['mother_last_name'] ?? ''); ?>" class="<?php echo hasError('mother_last_name'); ?>">
                                <?php echo displayError('mother_last_name'); ?>
                            </div>

                            <div class="form-group">
                                <label for="mother_first_name">First Name</label>
                                <input type="text" id="mother_first_name" name="mother_first_name" value="<?php echo htmlspecialchars($formData['mother_first_name'] ?? ''); ?>" class="<?php echo hasError('mother_first_name'); ?>">
                                <?php echo displayError('mother_first_name'); ?>
                            </div>

                            <div class="form-group">
                                <label for="mother_middle_name">Middle Name</label>
                                <input type="text" id="mother_middle_name" name="mother_middle_name" value="<?php echo htmlspecialchars($formData['mother_middle_name'] ?? ''); ?>" class="<?php echo hasError('mother_middle_name'); ?>">
                                <?php echo displayError('mother_middle_name'); ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="index.php" class="btn secondary">
                            <i class="fas fa-arrow-left"></i> Back to Dashboard
                        </a>
                        <button type="reset" class="btn warning">
                            <i class="fas fa-redo"></i> Reset Form
                        </button>
                        <button type="submit" class="btn primary">
                            <i class="fas fa-save"></i> <?php echo $editMode ? 'Update Form' : 'Submit Form'; ?>
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script>
        function toggleOthersField() {
            var civilStatus = document.getElementById('civil_status').value;
            var othersField = document.getElementById('othersField');

            if (civilStatus === 'others') {
                othersField.style.display = 'block';
            } else {
                othersField.style.display = 'none';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            toggleOthersField();
        });
    </script>
</body>

</html>