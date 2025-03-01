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

if (isset($_GET['action'])) {
    if ($_GET['action'] === 'delete' && isset($_GET['id'])) {
        $formId = $_GET['id'];
        
        try {
            $conn->begin_transaction();
            
            if (isset($_SESSION['forms'][$formId])) {
                $form = $_SESSION['forms'][$formId];
                
                $stmt = $conn->prepare("SELECT f_id FROM form_tb WHERE f_ln = ? AND f_fn = ? AND f_mi = ? AND f_dob = ?");
                
                $last_name = $form['last_name'];
                $first_name = $form['first_name'];
                $middle_name = $form['middle_name'];
                $date = $form['date'];
                
                $stmt->bind_param("ssss", $last_name, $first_name, $middle_name, $date);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($row = $result->fetch_assoc()) {
                    $form_id = $row['f_id']; 
                    
                    $delete_stmt = $conn->prepare("DELETE FROM form_tb WHERE f_id = ?");
                    $delete_stmt->bind_param("i", $form_id);
                    $delete_stmt->execute();
                    $delete_stmt->close();
                    
                    unset($_SESSION['forms'][$formId]);
                    $_SESSION['message'] = "Form deleted successfully from database!";
                    
                    $conn->commit();
                } else {
                    unset($_SESSION['forms'][$formId]);
                    $_SESSION['message'] = "Form deleted successfully!";
                }
                
                $stmt->close();
            }
        } catch (Exception $e) {
            $conn->rollback();
            $_SESSION['message'] = "Error deleting form: " . $e->getMessage();
        }
        
        header("Location: viewForms.php");
        exit();
    }

    if ($_GET['action'] === 'view' && isset($_GET['id'])) {
        $formId = $_GET['id'];
        if (isset($_SESSION['forms'][$formId])) {
            $_SESSION['view_form'] = $_SESSION['forms'][$formId];
            $_SESSION['view_form_id'] = $formId;
            header("Location: viewForms.php?mode=view&id=$formId");
            exit();
        }
    }

    if ($_GET['action'] === 'edit' && isset($_GET['id'])) {
        $formId = $_GET['id'];
        if (isset($_SESSION['forms'][$formId])) {
            $_SESSION['edit_form'] = $_SESSION['forms'][$formId];
            $_SESSION['edit_form_id'] = $formId;
            header("Location: addForms.php?edit=true");
            exit();
        }
    }
}

if (isset($_POST['form_id']) && isset($_SESSION['forms'][$_POST['form_id']])) {
    try {
        $conn->begin_transaction();
        
        $formId = $_POST['form_id'];
        $form = $_SESSION['forms'][$formId];
        
        $check_stmt = $conn->prepare("SELECT f_id FROM form_tb WHERE f_ln = ? AND f_fn = ? AND f_mi = ? AND f_dob = ?");
        
        $last_name = $form['last_name'];
        $first_name = $form['first_name'];
        $middle_name = $form['middle_name'];
        $date = $form['date'];
        
        $check_stmt->bind_param("ssss", $last_name, $first_name, $middle_name, $date);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_row = $check_result->fetch_assoc()) {
            $form_id = $check_row['f_id'];
            
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
            
            $dob = new DateTime($_POST['date']);
            $now = new DateTime();
            $age = $now->diff($dob)->y;
            
            $last_name = $_POST['last_name'];
            $first_name = $_POST['first_name'];
            $middle_name = $_POST['middle_name'];
            $date = $_POST['date'];
            $gender = $_POST['gender'];
            $civil_status = $_POST['civil_status'] == 'others' ? $_POST['others'] : $_POST['civil_status'];
            $tin = $_POST['tin'];
            $nationality = $_POST['nationality'];
            $religion = $_POST['religion'];
            
            // Fix for Place of Birth fields
            $pob_bldg = $_POST['rm_flr_unit_no'];
            $pob_lot = $_POST['house_lot_blk_no'];
            $pob_street = $_POST['street_name'];
            $pob_subdivision = $_POST['subdivision'];
            $pob_barangay = $_POST['barangay'];
            $pob_city = $_POST['city'];
            $pob_province = $_POST['province'];
            $pob_country = $_POST['country'];
            $pob_zip = $_POST['zip_code'];
            
            // Fix for Home Address fields
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
            
            $stmt->bind_param("ssssssssssssssssssssssssssssssssssssssi", 
                $last_name, $first_name, $middle_name, $date, $gender, $civil_status, 
                $tin, $nationality, $religion,
                $pob_bldg, $pob_lot, $pob_street, $pob_subdivision, 
                $pob_barangay, $pob_city, $pob_province, $pob_country, $pob_zip,
                $home_bldg, $home_lot, $home_street, $home_subdivision, 
                $home_barangay, $home_city, $home_province, $home_country, $home_zip,
                $mobile_number, $email_address, $telephone_number,
                $father_last_name, $father_first_name, $father_middle_name,
                $mother_last_name, $mother_first_name, $mother_middle_name,
                $age,
                $form_id
            );
            $stmt->execute();
            
            $_SESSION['forms'][$formId] = [
                'last_name' => $last_name,
                'first_name' => $first_name,
                'middle_name' => $middle_name,
                'date' => $date,
                'gender' => $gender,
                'civil_status' => $_POST['civil_status'],
                'others' => $_POST['civil_status'] == 'others' ? $_POST['others'] : '',
                'tin' => $tin,
                'nationality' => $nationality,
                'religion' => $religion,
                'rm_flr_unit_no' => $pob_bldg,
                'house_lot_blk_no' => $pob_lot,
                'street_name' => $pob_street,
                'subdivision' => $pob_subdivision,
                'barangay' => $pob_barangay,
                'city' => $pob_city,
                'province' => $pob_province,
                'country' => $pob_country,
                'zip_code' => $pob_zip,
                'home_rm_flr_unit_no' => $home_bldg,
                'home_house_lot_blk_no' => $home_lot,
                'home_street_name' => $home_street,
                'home_subdivision' => $home_subdivision,
                'home_barangay' => $home_barangay,
                'home_city' => $home_city,
                'home_province' => $home_province,
                'home_country' => $home_country,
                'home_zip_code' => $home_zip,
                'mobile_number' => $mobile_number,
                'email_address' => $email_address,
                'telephone_number' => $telephone_number,
                'father_last_name' => $father_last_name,
                'father_first_name' => $father_first_name,
                'father_middle_name' => $father_middle_name,
                'mother_last_name' => $mother_last_name,
                'mother_first_name' => $mother_first_name,
                'mother_middle_name' => $mother_middle_name
            ];
            
            $conn->commit();
            $_SESSION['message'] = "Form updated successfully in database!";
        } else {
            $_SESSION['message'] = "Error: Could not find the record to update.";
        }
        
        $check_stmt->close();
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['message'] = "Error updating form: " . $e->getMessage();
    }
    
    header("Location: viewForms.php");
    exit();
}

$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

$viewMode = false;
$viewForm = null;
$viewFormId = null;

if (isset($_GET['mode']) && $_GET['mode'] === 'view' && isset($_GET['id'])) {
    $formId = $_GET['id'];
    if (isset($_SESSION['forms'][$formId])) {
        $viewMode = true;
        $viewForm = $_SESSION['forms'][$formId];
        $viewFormId = $formId;
    }
}

$sortBy = $_GET['sort'] ?? 'last_name';
$sortOrder = $_GET['order'] ?? 'asc';

$forms = $_SESSION['forms'];

if (!empty($forms)) {
    uasort($forms, function ($a, $b) use ($sortBy, $sortOrder) {
        $valueA = $a[$sortBy] ?? '';
        $valueB = $b[$sortBy] ?? '';

        if ($sortOrder === 'asc') {
            return strcasecmp($valueA, $valueB);
        } else {
            return strcasecmp($valueB, $valueA);
        }
    });
}

$formsPerPage = 12;
$totalForms = count($forms);
$totalPages = ceil($totalForms / $formsPerPage);
$currentPage = isset($_GET['page']) ? max(1, min($totalPages, intval($_GET['page']))) : 1;
$offset = ($currentPage - 1) * $formsPerPage;

$paginatedForms = array_slice($forms, $offset, $formsPerPage, true);

function calculateAge($dob)
{
    if (empty($dob)) return '';

    $birthDate = new DateTime($dob);
    $currentYear = 2025;
    $birthYear = $birthDate->format('Y');

    return $currentYear - $birthYear;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>View Forms - Form Management</title>
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
                <a href="viewForms.php" class="menu-item active">
                    <i class="fas fa-file-alt"></i>
                    <span>View Forms</span>
                </a>
                <a href="addForms.php" class="menu-item">
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
            <header class="dashboard-header">
                <div class="header-left">
                    <h1>View Forms</h1>
                    <p class="date"><?php echo date('jS M Y'); ?></p>
                </div>
                <div class="header-right">
                    <div class="search-bar">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Search forms..." id="searchInput" onkeyup="searchForms()">
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

            <?php if ($viewMode): ?>
                <div class="form-view">
                    <div class="view-header">
                        <h2>
                            <i class="fas fa-user"></i>
                            <?php echo htmlspecialchars($viewForm['last_name'] . ', ' . $viewForm['first_name'] . ' ' . ($viewForm['middle_name'] ?? '')); ?>
                        </h2>
                        <div class="view-actions">
                            <a href="viewForms.php" class="btn secondary">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                            <a href="viewForms.php?action=edit&id=<?php echo $viewFormId; ?>" class="btn primary">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        </div>
                    </div>

                    <div class="form-sections">
                        <div class="form-section">
                            <h3><i class="fas fa-user-circle"></i> Personal Data</h3>
                            <div class="form-data">
                                <div class="data-row">
                                    <div class="data-label">Last Name:</div>
                                    <div class="data-value"><?php echo htmlspecialchars($viewForm['last_name'] ?? ''); ?></div>
                                </div>
                                <div class="data-row">
                                    <div class="data-label">First Name:</div>
                                    <div class="data-value"><?php echo htmlspecialchars($viewForm['first_name'] ?? ''); ?></div>
                                </div>
                                <div class="data-row">
                                    <div class="data-label">Middle Name:</div>
                                    <div class="data-value"><?php echo htmlspecialchars($viewForm['middle_name'] ?? ''); ?></div>
                                </div>
                                <div class="data-row">
                                    <div class="data-label">Date of Birth:</div>
                                    <div class="data-value">
                                        <?php
                                        echo htmlspecialchars($viewForm['date'] ?? '');
                                        $age = calculateAge($viewForm['date'] ?? '');
                                        if ($age) {
                                            echo ' (Age: ' . $age . ' years)';
                                        }
                                        ?>
                                    </div>
                                </div>
                                <div class="data-row">
                                    <div class="data-label">Sex:</div>
                                    <div class="data-value"><?php echo htmlspecialchars(ucfirst($viewForm['gender'] ?? '')); ?></div>
                                </div>
                                <div class="data-row">
                                    <div class="data-label">Civil Status:</div>
                                    <div class="data-value">
                                        <?php
                                        if (isset($viewForm['civil_status'])) {
                                            echo htmlspecialchars(ucfirst($viewForm['civil_status']));
                                            if ($viewForm['civil_status'] === 'others' && isset($viewForm['others'])) {
                                                echo ' - ' . htmlspecialchars($viewForm['others']);
                                            }
                                        }
                                        ?>
                                    </div>
                                </div>
                                <div class="data-row">
                                    <div class="data-label">TIN:</div>
                                    <div class="data-value"><?php echo htmlspecialchars($viewForm['tin'] ?? ''); ?></div>
                                </div>
                                <div class="data-row">
                                    <div class="data-label">Nationality:</div>
                                    <div class="data-value"><?php echo htmlspecialchars($viewForm['nationality'] ?? ''); ?></div>
                                </div>
                                <div class="data-row">
                                    <div class="data-label">Religion:</div>
                                    <div class="data-value"><?php echo htmlspecialchars($viewForm['religion'] ?? ''); ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <h3><i class="fas fa-map-marker-alt"></i> Place of Birth</h3>
                            <div class="form-data">
                                <div class="data-row">
                                    <div class="data-label">RM/FLR/Unit No. & Bldg. Name:</div>
                                    <div class="data-value"><?php echo htmlspecialchars($viewForm['rm_flr_unit_no'] ?? ''); ?></div>
                                </div>
                                <div class="data-row">
                                    <div class="data-label">House/Lot & Blk. No:</div>
                                    <div class="data-value"><?php echo htmlspecialchars($viewForm['house_lot_blk_no'] ?? ''); ?></div>
                                </div>
                                <div class="data-row">
                                    <div class="data-label">Street Name:</div>
                                    <div class="data-value"><?php echo htmlspecialchars($viewForm['street_name'] ?? ''); ?></div>
                                </div>
                                <div class="data-row">
                                    <div class="data-label">Subdivision:</div>
                                    <div class="data-value"><?php echo htmlspecialchars($viewForm['subdivision'] ?? ''); ?></div>
                                </div>
                                <div class="data-row">
                                    <div class="data-label">Barangay/District/Locality:</div>
                                    <div class="data-value"><?php echo htmlspecialchars($viewForm['barangay'] ?? ''); ?></div>
                                </div>
                                <div class="data-row">
                                    <div class="data-label">City/Municipality:</div>
                                    <div class="data-value"><?php echo htmlspecialchars($viewForm['city'] ?? ''); ?></div>
                                </div>
                                <div class="data-row">
                                    <div class="data-label">Province:</div>
                                    <div class="data-value"><?php echo htmlspecialchars($viewForm['province'] ?? ''); ?></div>
                                </div>
                                <div class="data-row">
                                    <div class="data-label">Country:</div>
                                    <div class="data-value"><?php echo htmlspecialchars($viewForm['country'] ?? ''); ?></div>
                                </div>
                                <div class="data-row">
                                    <div class="data-label">Zip Code:</div>
                                    <div class="data-value"><?php echo htmlspecialchars($viewForm['zip_code'] ?? ''); ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <h3><i class="fas fa-home"></i> Home Address</h3>
                            <div class="form-data">
                                <div class="data-row">
                                    <div class="data-label">RM/FLR/Unit No. & Bldg. Name:</div>
                                    <div class="data-value"><?php echo htmlspecialchars($viewForm['home_rm_flr_unit_no'] ?? ''); ?></div>
                                </div>
                                <div class="data-row">
                                    <div class="data-label">House/Lot & Blk. No:</div>
                                    <div class="data-value"><?php echo htmlspecialchars($viewForm['home_house_lot_blk_no'] ?? ''); ?></div>
                                </div>
                                <div class="data-row">
                                    <div class="data-label">Street Name:</div>
                                    <div class="data-value"><?php echo htmlspecialchars($viewForm['home_street_name'] ?? ''); ?></div>
                                </div>
                                <div class="data-row">
                                    <div class="data-label">Subdivision:</div>
                                    <div class="data-value"><?php echo htmlspecialchars($viewForm['home_subdivision'] ?? ''); ?></div>
                                </div>
                                <div class="data-row">
                                    <div class="data-label">Barangay/District/Locality:</div>
                                    <div class="data-value"><?php echo htmlspecialchars($viewForm['home_barangay'] ?? ''); ?></div>
                                </div>
                                <div class="data-row">
                                    <div class="data-label">City/Municipality:</div>
                                    <div class="data-value"><?php echo htmlspecialchars($viewForm['home_city'] ?? ''); ?></div>
                                </div>
                                <div class="data-row">
                                    <div class="data-label">Province:</div>
                                    <div class="data-value"><?php echo htmlspecialchars($viewForm['home_province'] ?? ''); ?></div>
                                </div>
                                <div class="data-row">
                                    <div class="data-label">Country:</div>
                                    <div class="data-value"><?php echo htmlspecialchars($viewForm['home_country'] ?? ''); ?></div>
                                </div>
                                <div class="data-row">
                                    <div class="data-label">Zip Code:</div>
                                    <div class="data-value"><?php echo htmlspecialchars($viewForm['home_zip_code'] ?? ''); ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <h3><i class="fas fa-phone-alt"></i> Contact Information</h3>
                            <div class="form-data">
                                <div class="data-row">
                                    <div class="data-label">Mobile / Cellphone Number:</div>
                                    <div class="data-value"><?php echo htmlspecialchars($viewForm['mobile_number'] ?? ''); ?></div>
                                </div>
                                <div class="data-row">
                                    <div class="data-label">E-mail Address:</div>
                                    <div class="data-value"><?php echo htmlspecialchars($viewForm['email_address'] ?? ''); ?></div>
                                </div>
                                <div class="data-row">
                                    <div class="data-label">Telephone Number:</div>
                                    <div class="data-value"><?php echo htmlspecialchars($viewForm['telephone_number'] ?? ''); ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <h3><i class="fas fa-male"></i> Father's Name</h3>
                            <div class="form-data">
                                <div class="data-row">
                                    <div class="data-label">Last Name:</div>
                                    <div class="data-value"><?php echo htmlspecialchars($viewForm['father_last_name'] ?? ''); ?></div>
                                </div>
                                <div class="data-row">
                                    <div class="data-label">First Name:</div>
                                    <div class="data-value"><?php echo htmlspecialchars($viewForm['father_first_name'] ?? ''); ?></div>
                                </div>
                                <div class="data-row">
                                    <div class="data-label">Middle Name:</div>
                                    <div class="data-value"><?php echo htmlspecialchars($viewForm['father_middle_name'] ?? ''); ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <h3><i class="fas fa-female"></i> Mother's Maiden Name</h3>
                            <div class="form-data">
                                <div class="data-row">
                                    <div class="data-label">Last Name:</div>
                                    <div class="data-value"><?php echo htmlspecialchars($viewForm['mother_last_name'] ?? ''); ?></div>
                                </div>
                                <div class="data-row">
                                    <div class="data-label">First Name:</div>
                                    <div class="data-value"><?php echo htmlspecialchars($viewForm['mother_first_name'] ?? ''); ?></div>
                                </div>
                                <div class="data-row">
                                    <div class="data-label">Middle Name:</div>
                                    <div class="data-value"><?php echo htmlspecialchars($viewForm['mother_middle_name'] ?? ''); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="forms-list">
                    <div class="list-header">
                        <div class="list-actions">
                            <a href="addForms.php" class="btn primary">
                                <i class="fas fa-plus"></i> Add New Form
                            </a>
                        </div>

                        <div class="list-info">
                            <p>Showing <?php echo min($offset + 1, $totalForms) . ' - ' . min($offset + $formsPerPage, $totalForms); ?> of <?php echo $totalForms; ?> forms</p>
                        </div>
                    </div>

                    <div class="forms-grid" id="formsGrid">
                        <?php if (empty($paginatedForms)): ?>
                            <div class="no-forms">
                                <i class="fas fa-file-alt" style="font-size: 48px; color: var(--gray-light); margin-bottom: 20px;"></i>
                                <h3 style="margin-bottom: 10px;">No Forms Found</h3>
                                <p>No forms found. <a href="addForms.php">Add a new form</a> to get started.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($paginatedForms as $id => $form): ?>
                                <div class="form-card">
                                    <div class="form-card-header">
                                        <div class="form-avatar">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div class="form-name">
                                            <h3><?php echo htmlspecialchars($form['last_name'] . ', ' . $form['first_name'] . ' ' . ($form['middle_name'] ?? '')); ?></h3>
                                        </div>
                                    </div>

                                    <div class="form-details">
                                        <div class="detail-row">
                                            <div class="detail-icon">
                                                <i class="fas fa-envelope"></i>
                                            </div>
                                            <div class="detail-label">Email:</div>
                                            <div class="detail-value"><?php echo htmlspecialchars($form['email_address'] ?? ''); ?></div>
                                        </div>

                                        <div class="detail-row">
                                            <div class="detail-icon">
                                                <i class="fas fa-phone"></i>
                                            </div>
                                            <div class="detail-label">Mobile:</div>
                                            <div class="detail-value"><?php echo htmlspecialchars($form['mobile_number'] ?? ''); ?></div>
                                        </div>

                                        <div class="detail-row">
                                            <div class="detail-icon">
                                                <i class="fas fa-birthday-cake"></i>
                                            </div>
                                            <div class="detail-label">DOB:</div>
                                            <div class="detail-value">
                                                <?php
                                                echo htmlspecialchars($form['date'] ?? '');
                                                $age = calculateAge($form['date'] ?? '');
                                                if ($age) {
                                                    echo ' (Age: ' . $age . ')';
                                                }
                                                ?>
                                            </div>
                                        </div>

                                        <div class="detail-row">
                                            <div class="detail-icon">
                                                <i class="fas fa-map-marker-alt"></i>
                                            </div>
                                            <div class="detail-label">City:</div>
                                            <div class="detail-value"><?php echo htmlspecialchars($form['home_city'] ?? $form['city'] ?? ''); ?></div>
                                        </div>
                                    </div>

                                    <div class="form-actions">
                                        <a href="viewForms.php?action=view&id=<?php echo $id; ?>" class="view-btn">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        <a href="viewForms.php?action=edit&id=<?php echo $id; ?>" class="edit-btn">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="viewForms.php?action=delete&id=<?php echo $id; ?>" class="delete-btn"
                                            onclick="return confirm('Are you sure you want to delete this form? This will also delete the data from the database.');">
                                            <i class="fas fa-trash-alt"></i> Delete
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <?php if ($totalPages > 1): ?>
                        <div class="pagination">
                            <?php if ($currentPage > 1): ?>
                                <a href="viewForms.php?page=<?php echo $currentPage - 1; ?>&sort=<?php echo $sortBy; ?>&order=<?php echo $sortOrder; ?>" class="page-link">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <a href="viewForms.php?page=<?php echo $i; ?>&sort=<?php echo $sortBy; ?>&order=<?php echo $sortOrder; ?>"
                                    class="page-link <?php echo $i === $currentPage ? 'active' : ''; ?>">
                                    <?php echo $i; ?>
                                </a>
                            <?php endfor; ?>

                            <?php if ($currentPage < $totalPages): ?>
                                <a href="viewForms.php?page=<?php echo $currentPage + 1; ?>&sort=<?php echo $sortBy; ?>&order=<?php echo $sortOrder; ?>" class="page-link">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </main>
    </div>

    <script>
        function searchForms() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toUpperCase();
            const grid = document.getElementById('formsGrid');
            const cards = grid.getElementsByClassName('form-card');

            let noResults = true;

            for (let i = 0; i < cards.length; i++) {
                const nameElement = cards[i].querySelector('.form-name h3');
                const emailElement = cards[i].querySelector('.detail-row:nth-child(1) .detail-value');
                const mobileElement = cards[i].querySelector('.detail-row:nth-child(2) .detail-value');

                if (nameElement || emailElement || mobileElement) {
                    const nameText = nameElement ? nameElement.textContent || nameElement.innerText : '';
                    const emailText = emailElement ? emailElement.textContent || emailElement.innerText : '';
                    const mobileText = mobileElement ? mobileElement.textContent || mobileElement.innerText : '';

                    if (nameText.toUpperCase().indexOf(filter) > -1 ||
                        emailText.toUpperCase().indexOf(filter) > -1 ||
                        mobileText.toUpperCase().indexOf(filter) > -1) {
                        cards[i].style.display = '';
                        noResults = false;
                    } else {
                        cards[i].style.display = 'none';
                    }
                }
            }

            let noFormsElement = grid.querySelector('.no-forms');

            if (filter && noResults && !noFormsElement) {
                noFormsElement = document.createElement('div');
                noFormsElement.className = 'no-forms';
                noFormsElement.innerHTML = `
                    <i class="fas fa-search" style="font-size: 48px; color: var(--gray-light); margin-bottom: 20px;"></i>
                    <h3 style="margin-bottom: 10px;">No Matching Forms</h3>
                    <p>No forms match your search criteria.</p>
                `;

                while (grid.firstChild) {
                    grid.removeChild(grid.firstChild);
                }
                grid.appendChild(noFormsElement);
            } else if (!noResults && noFormsElement) {
                noFormsElement.remove();
            }
        }
    </script>
</body>

</html>