<?php
session_start();

if (!isset($_SESSION['forms'])) {
    $_SESSION['forms'] = [];
}

if (isset($_GET['action'])) {
    if ($_GET['action'] === 'delete' && isset($_GET['id'])) {
        $formId = $_GET['id'];
        if (isset($_SESSION['forms'][$formId])) {
            unset($_SESSION['forms'][$formId]);
            $_SESSION['message'] = "Form deleted successfully!";
        }
        header("Location: index.php");
        exit();
    }

    if ($_GET['action'] === 'view' && isset($_GET['id'])) {
        $formId = $_GET['id'];
        if (isset($_SESSION['forms'][$formId])) {
            $_SESSION['view_form'] = $_SESSION['forms'][$formId];
            $_SESSION['view_form_id'] = $formId;
            header("Location: viewForms.php");
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

$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

$totalForms = count($_SESSION['forms']);

$inspirationalQuotes = [
    "The only way to do great work is to love what you do. - Steve Jobs",
    "Success is not final, failure is not fatal: It is the courage to continue that counts. - Winston Churchill",
    "Believe you can and you're halfway there. - Theodore Roosevelt",
    "The future belongs to those who believe in the beauty of their dreams. - Eleanor Roosevelt",
    "Don't watch the clock; do what it does. Keep going. - Sam Levenson"
];

$newsQuotes = [
    "Stay informed, stay ahead. Knowledge is power in today's fast-paced world.",
    "The best preparation for tomorrow is doing your best today.",
    "Information is the currency of success in the digital age.",
    "Great achievements require time, dedication, and consistent effort.",
    "Progress is impossible without change, and those who cannot change their minds cannot change anything."
];

$randomInspirationalQuote = $inspirationalQuotes[array_rand($inspirationalQuotes)];
$randomNewsQuote = $newsQuotes[array_rand($newsQuotes)];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Form Management Dashboard</title>
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
                <a href="index.php" class="menu-item active">
                    <i class="fas fa-th-large"></i>
                    <span>Dashboard</span>
                </a>
                <a href="viewForms.php" class="menu-item">
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
            <!-- Header -->
            <header class="dashboard-header">
                <div class="header-left">
                    <h1>Dashboard</h1>
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
            
            <!-- Stats Cards -->
            <div class="stats-container">
                <div class="stat-card blue">
                    <div class="stat-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="stat-details">
                        <h3>Total Forms</h3>
                        <div class="stat-number"><?php echo $totalForms; ?></div>
                        <p>All forms in the system</p>
                    </div>
                </div>
                
                <div class="stat-card green">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div class="stat-details">
                        <h3>Today's Date</h3>
                        <div class="stat-number"><?php echo date('d M Y'); ?></div>
                        <p><?php echo htmlspecialchars($randomInspirationalQuote); ?></p>
                    </div>
                </div>
                
                <div class="stat-card orange">
                    <div class="stat-icon">
                        <i class="fas fa-newspaper"></i>
                    </div>
                    <div class="stat-details">
                        <h3>Daily Insight</h3>
                        <div class="stat-number"><?php echo date('l'); ?></div>
                        <p><?php echo htmlspecialchars($randomNewsQuote); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="recent-forms">
                <div class="section-header">
                    <h2>Recent Forms</h2>
                    <a href="viewForms.php" class="view-all">View All</a>
                </div>
                
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Date Submitted</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $recentForms = array_slice($_SESSION['forms'], -5, 5, true);
                            if (empty($recentForms)): 
                            ?>
                                <tr>
                                    <td colspan="5" class="no-data">No forms submitted yet. <a href="addForms.php">Add a new form</a></td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recentForms as $id => $form): ?>
                                    <tr>
                                        <td>
                                            <div class="user-info">
                                                <div class="user-avatar">
                                                    <i class="fas fa-user"></i>
                                                </div>
                                                <div>
                                                    <?php echo htmlspecialchars($form['last_name'] . ', ' . $form['first_name']); ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($form['email_address'] ?? ''); ?></td>
                                        <td><?php echo isset($form['submission_date']) ? htmlspecialchars($form['submission_date']) : date('M d, Y'); ?></td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="index.php?action=view&id=<?php echo $id; ?>" class="btn-icon view" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="index.php?action=edit&id=<?php echo $id; ?>" class="btn-icon edit" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="index.php?action=delete&id=<?php echo $id; ?>" class="btn-icon delete" title="Delete" 
                                                   onclick="return confirm('Are you sure you want to delete this form?');">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="quick-actions">
                <div class="section-header">
                    <h2>Quick Actions</h2>
                </div>
                <div class="action-cards">
                    <a href="addForms.php" class="action-card">
                        <div class="action-icon">
                            <i class="fas fa-plus-circle"></i>
                        </div>
                        <h3>Add New Form</h3>
                        <p>Create a new personal information form</p>
                    </a>
                    
                    <a href="viewForms.php" class="action-card">
                        <div class="action-icon">
                            <i class="fas fa-list"></i>
                        </div>
                        <h3>View All Forms</h3>
                        <p>Browse and manage all submitted forms</p>
                    </a>
                    
                    <a href="#" class="action-card">
                        <div class="action-icon">
                            <i class="fas fa-sign-out-alt"></i>
                        </div>
                        <h3>Logout</h3>
                        <p>Securely sign out from your account</p>
                    </a>
                </div>
            </div>
        </main>
    </div>

    <script>
        function toggleDropdown(id) {
            var dropdown = document.getElementById('dropdown-' + id);
            
            var allDropdowns = document.querySelectorAll('.dropdown-menu');
            allDropdowns.forEach(function(menu) {
                if (menu.id !== 'dropdown-' + id) {
                    menu.style.display = 'none';
                }
            });
            
            if (dropdown.style.display === 'block') {
                dropdown.style.display = 'none';
            } else {
                dropdown.style.display = 'block';
            }
            
            document.addEventListener('click', function closeDropdown(e) {
                if (!e.target.matches('.dropdown-toggle') && !e.target.closest('.dropdown-menu')) {
                    dropdown.style.display = 'none';
                    document.removeEventListener('click', closeDropdown);
                }
            });
        }
    </script>
</body>
</html>