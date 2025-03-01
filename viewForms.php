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
    $action = $_GET['action'];
    $formId = $_GET['id'] ?? null;
    
    if ($action === 'delete' && $formId && isset($_SESSION['forms'][$formId])) {
        try {
            $conn->begin_transaction();
            $form = $_SESSION['forms'][$formId];
            
            if (isset($form['f_id'])) {
                $form_id = $form['f_id'];
                $delete_stmt = $conn->prepare("DELETE FROM form_tb WHERE f_id = ?");
                $delete_stmt->bind_param("i", $form_id);
                $delete_stmt->execute();
                $delete_stmt->close();
                
                unset($_SESSION['forms'][$formId]);
                $_SESSION['message'] = "Form deleted successfully from database!";
                $conn->commit();
            } else {
                $stmt = $conn->prepare("SELECT f_id FROM form_tb WHERE f_ln = ? AND f_fn = ? AND f_mi = ? AND f_dob = ?");
                $stmt->bind_param("ssss", $form['last_name'], $form['first_name'], $form['middle_name'], $form['date']);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($row = $result->fetch_assoc()) {
                    $form_id = $row['f_id'];
                    $delete_stmt = $conn->prepare("DELETE FROM form_tb WHERE f_id = ?");
                    $delete_stmt->bind_param("i", $form_id);
                    $delete_stmt->execute();
                    $delete_stmt->close();
                }
                
                unset($_SESSION['forms'][$formId]);
                $_SESSION['message'] = "Form deleted successfully!";
                $stmt->close();
                $conn->commit();
            }
        } catch (Exception $e) {
            $conn->rollback();
            $_SESSION['message'] = "Error deleting form: " . $e->getMessage();
        }
        header("Location: viewForms.php");
        exit();
    }
    
    if ($action === 'view' && $formId && isset($_SESSION['forms'][$formId])) {
        $_SESSION['view_form'] = $_SESSION['forms'][$formId];
        $_SESSION['view_form_id'] = $formId;
        header("Location: viewForms.php?mode=view&id=$formId");
        exit();
    }
    
    if ($action === 'edit' && $formId && isset($_SESSION['forms'][$formId])) {
        $_SESSION['edit_form'] = $_SESSION['forms'][$formId];
        $_SESSION['edit_form_id'] = $formId;
        header("Location: addForms.php?edit=true");
        exit();
    }
}

if (isset($_POST['update_form']) && isset($_POST['form_id']) && isset($_SESSION['forms'][$_POST['form_id']])) {
    try {
        $conn->begin_transaction();
        $formId = $_POST['form_id'];
        $form = $_SESSION['forms'][$formId];
        
        if (isset($form['f_id'])) {
            $db_id = $form['f_id'];
            $_SESSION['edit_form'] = $form;
            $_SESSION['edit_form_id'] = $formId;
            
            $conn->commit();
            header("Location: addForms.php?edit=true");
            exit();
        } else {
            $_SESSION['message'] = "Error: Could not find the database record to update.";
        }
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['message'] = "Error preparing form for update: " . $e->getMessage();
    }
    
    header("Location: viewForms.php");
    exit();
}

foreach ($_SESSION['forms'] as $id => &$form) {
    if (!isset($form['f_id'])) {
        $stmt = $conn->prepare("SELECT f_id FROM form_tb WHERE f_ln = ? AND f_fn = ? AND f_mi = ? AND f_dob = ?");
        $stmt->bind_param("ssss", $form['last_name'], $form['first_name'], $form['middle_name'], $form['date']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            $form['f_id'] = $row['f_id'];
        }
        $stmt->close();
    }
}

if (empty($_SESSION['forms'])) {
    $stmt = $conn->prepare("SELECT * FROM form_tb");
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $formId = uniqid();
        $_SESSION['forms'][$formId] = [
            'last_name' => $row['f_ln'],
            'first_name' => $row['f_fn'],
            'middle_name' => $row['f_mi'],
            'date' => $row['f_dob'],
            'gender' => $row['f_sex'],
            'civil_status' => $row['f_civil'],
            'tin' => $row['f_tin'],
            'nationality' => $row['f_nationality'],
            'religion' => $row['f_religion'],
            'rm_flr_unit_no' => $row['f_pob_bldg'],
            'house_lot_blk_no' => $row['f_pob_lot'],
            'street_name' => $row['f_pob_street'],
            'subdivision' => $row['f_pob_subdivision'],
            'barangay' => $row['f_pob_barangay'],
            'city' => $row['f_pob_city'],
            'province' => $row['f_pob_province'],
            'country' => $row['f_pob_country'],
            'zip_code' => $row['f_pob_zip'],
            'home_rm_flr_unit_no' => $row['f_home_bldg'],
            'home_house_lot_blk_no' => $row['f_home_lot'],
            'home_street_name' => $row['f_home_street'],
            'home_subdivision' => $row['f_home_subdivision'],
            'home_barangay' => $row['f_home_barangay'],
            'home_city' => $row['f_home_city'],
            'home_province' => $row['f_home_province'],
            'home_country' => $row['f_home_country'],
            'home_zip_code' => $row['f_home_zip'],
            'mobile_number' => $row['f_home_mobile'],
            'email_address' => $row['f_home_email'],
            'telephone_number' => $row['f_home_telephone'],
            'father_last_name' => $row['f_father_ln'],
            'father_first_name' => $row['f_father_fn'],
            'father_middle_name' => $row['f_father_mi'],
            'mother_last_name' => $row['f_mother_ln'],
            'mother_first_name' => $row['f_mother_fn'],
            'mother_middle_name' => $row['f_mother_mi'],
            'f_id' => $row['f_id'],
            'submission_date' => date('Y-m-d H:i:s'),
            'status' => 'pending'
        ];
    }
    $stmt->close();
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
    $currentDate = new DateTime();
    return $currentDate->diff($birthDate)->y;
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
                            <?php if (isset($viewForm['f_id'])): ?>
                                <span class="form-id">(ID: <?php echo htmlspecialchars($viewForm['f_id']); ?>)</span>
                            <?php endif; ?>
                        </h2>
                        <div class="view-actions">
                            <a href="viewForms.php" class="btn secondary">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                            <form method="post" action="viewForms.php" style="display: inline;">
                                <input type="hidden" name="form_id" value="<?php echo $viewFormId; ?>">
                                <button type="submit" name="update_form" class="btn primary">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                            </form>
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
                                            <h3>
                                                <?php echo htmlspecialchars($form['last_name'] . ', ' . $form['first_name'] . ' ' . $form['middle_name']); ?>
                                                <?php if (isset($form['f_id'])): ?>
                                                    <span class="form-id">(ID: <?php echo htmlspecialchars($form['f_id']); ?>)</span>
                                                <?php endif; ?>
                                            </h3>
                                        </div>
                                    </div>

                                    <div class="form-details">
                                        <div class="detail-row">
                                            <div class="detail-icon">
                                                <i class="fas fa-envelope"></i>
                                            </div>
                                            <div class="detail-value"><?php echo htmlspecialchars($form['email_address'] ?? ''); ?></div>
                                        </div>

                                        <div class="detail-row">
                                            <div class="detail-icon">
                                                <i class="fas fa-phone"></i>
                                            </div>
                                            <div class="detail-value"><?php echo htmlspecialchars($form['mobile_number'] ?? ''); ?></div>
                                        </div>

                                        <div class="detail-row">
                                            <div class="detail-icon">
                                                <i class="fas fa-birthday-cake"></i>
                                            </div>
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
                                            <div class="detail-value"><?php echo htmlspecialchars($form['home_city'] ?? $form['city'] ?? ''); ?></div>
                                        </div>
                                    </div>

                                    <div class="form-actions">
                                        <a href="viewForms.php?action=view&id=<?php echo $id; ?>" class="view-btn">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        <form method="post" action="viewForms.php" style="display: inline;">
                                            <input type="hidden" name="form_id" value="<?php echo $id; ?>">
                                            <button type="submit" name="update_form" class="edit-btn">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                        </form>
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
</body>
</html>