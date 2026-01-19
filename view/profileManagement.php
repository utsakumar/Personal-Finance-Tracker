<?php
    session_start();
    if(isset($_SESSION['status'])){
        $errors = [];
        $success = false;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (empty($_POST['firstName'])) {
                $errors[] = "First name is required";
            } elseif (strlen($_POST['firstName']) > 50) {
                $errors[] = "First name must be less than 50 characters";
            }

            if (empty($_POST['lastName'])) {
                $errors[] = "Last name is required";
            } elseif (strlen($_POST['lastName']) > 50) {
                $errors[] = "Last name must be less than 50 characters";
            }

            if (empty($_POST['email'])) {
                $errors[] = "Email is required";
            } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Invalid email format";
            }

            if (!empty($_POST['phone'])) {
                if (!preg_match('/^[0-9]{10,15}$/', $_POST['phone'])) {
                    $errors[] = "Phone number must be between 10 and 15 digits";
                }
            }

            if (!empty($_POST['newPassword'])) {
                if (empty($_POST['currentPassword'])) {
                    $errors[] = "Current password is required to set a new password";
                }
            }

            if (!empty($_POST['newPassword'])) {
                if (strlen($_POST['newPassword']) < 8) {
                    $errors[] = "New password must be at least 8 characters long";
                } elseif (!preg_match('/[A-Z]/', $_POST['newPassword'])) {
                    $errors[] = "New password must contain at least one uppercase letter";
                } elseif (!preg_match('/[a-z]/', $_POST['newPassword'])) {
                    $errors[] = "New password must contain at least one lowercase letter";
                } elseif (!preg_match('/[0-9]/', $_POST['newPassword'])) {
                    $errors[] = "New password must contain at least one number";
                }
            }

            if (empty($_POST['currency'])) {
                $errors[] = "Preferred currency is required";
            } elseif (!in_array($_POST['currency'], ['USD', 'EUR', 'GBP', 'JPY', 'CAD', 'AUD'])) {
                $errors[] = "Invalid currency selected";
            }

            if (empty($_POST['language'])) {
                $errors[] = "Preferred language is required";
            } elseif (!in_array($_POST['language'], ['English', 'Spanish', 'French', 'German', 'Chinese', 'Japanese'])) {
                $errors[] = "Invalid language selected";
            }

            if (empty($_POST['timezone'])) {
                $errors[] = "Timezone is required";
            } elseif (!in_array($_POST['timezone'], ['UTC', 'EST', 'CST', 'PST', 'GMT', 'IST'])) {
                $errors[] = "Invalid timezone selected";
            }

           
            if (empty($errors)) {
                $success = true;

            }
        }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Profile Management</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <link rel="stylesheet" href="../assets/css/feature.css" />
    <link rel="stylesheet" href="../assets/css/profileManagement.css" />
</head>

<body>
    <div class="app-container">
        <nav class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h1>FinanceFlow</h1>
                <button id="sidebar-toggle" class="sidebar-toggle mobile-only">
                    <i data-feather="menu"></i>
                </button>
            </div>
            <ul class="sidebar-menu">
                <li data-page="dashboard">
                    <a href="dashboard.php"><i data-feather="home"></i> Dashboard</a>
                </li>
                <li data-page="features">
                    <a href="features.php"><i data-feather="grid"></i> Features</a>
                </li>
                <li data-page="income">
                    <a href="Income.php"><i data-feather="trending-up"></i> Income</a>
                </li>
                <li data-page="expense">
                    <a href="Expense.php"><i data-feather="trending-down"></i> Expense</a>
                </li>
                <li data-page="debts">
                    <a href="Debts.php"><i data-feather="credit-card"></i> Debt Tracking</a>
                </li>
                <li data-page="budget-goals">
                    <a href="budget-goals.php"><i data-feather="target"></i> Budget Goals</a>
                </li>
                <li data-page="bill-reminders">
                    <a href="billReminders.php"><i data-feather="bell"></i> Bill Reminders</a>
                </li>
                <li data-page="savings-goals">
                    <a href="savingsGoals.php"><i data-feather="dollar-sign"></i> Savings Goals</a>
                </li>
                <li data-page="reports">
                    <a href="reports-graphs.php"><i data-feather="bar-chart-2"></i> Reports</a>
                </li>
                <li data-page="account-linking">
                    <a href="account-linking.php"><i data-feather="link"></i> Account Linking</a>
                </li>
                <li class="active" data-page="profile">
                    <a href="profileManagement.php"><i data-feather="user"></i> Profile</a>
                </li>
                <li data-page="logout">
                    <a href="../controller/logout.php"><i data-feather="log-out"></i> Log Out</a>
                </li>
            </ul>
        </nav>

        <main class="main-content">
        <header class="page-header">
            <h1>Profile Management</h1>
            <div class="date-display" id="current-date"></div>
        </header>

        <div class="profile-container">
            <div class="profile-header">
                <div class="profile-avatar">
                    <img src="../assets/images/default-avatar.png" alt="Profile Picture" id="profilePicture">
                    <button class="change-avatar-btn" onclick="document.getElementById('avatarInput').click()">
                        <i data-feather="camera"></i>
                        Change Photo
                    </button>
                    <input type="file" id="avatarInput" accept="image/*" style="display: none;" onchange="handleAvatarChange(event)">
                </div>
                <div class="profile-name">
                    <h2 id="displayName">Loading...</h2>
                    <p id="displayEmail">Loading...</p>
                </div>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="error-messages" style="background-color: var(--error-color); color: white; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                    <?php foreach ($errors as $error): ?>
                        <p><?php echo htmlspecialchars($error); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="success-message" style="background-color: var(--success-color); color: white; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                    <p>Profile updated successfully!</p>
                </div>
            <?php endif; ?>
            </div>

            <form class="profile-form" id="profileForm">
                <div class="form-section">
                    <h3>Personal Information</h3>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="firstName">First Name</label>
                            <input type="text" id="firstName" name="firstName" required>
                        </div>
                        <div class="form-group">
                            <label for="lastName">Last Name</label>
                            <input type="text" id="lastName" name="lastName" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone">
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h3>Security</h3>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="currentPassword">Current Password</label>
                            <input type="password" id="currentPassword" name="currentPassword">
                        </div>
                        <div class="form-group">
                            <label for="newPassword">New Password</label>
                            <input type="password" id="newPassword" name="newPassword">
                        </div>
                        <div class="form-group">
                            <label for="confirmPassword">Confirm New Password</label>
                            <input type="password" id="confirmPassword" name="confirmPassword">
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h3>Preferences</h3>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="currency">Preferred Currency</label>
                            <select id="currency" name="currency">
                                <option value="USD">USD - US Dollar</option>
                                <option value="EUR">EUR - Euro</option>
                                <option value="GBP">GBP - British Pound</option>
                                <option value="JPY">JPY - Japanese Yen</option>
                                <option value="CAD">CAD - Canadian Dollar</option>
                                <option value="AUD">AUD - Australian Dollar</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="language">Language</label>
                            <select id="language" name="language">
                                <option value="English">English</option>
                                <option value="Spanish">Spanish</option>
                                <option value="French">French</option>
                                <option value="German">German</option>
                                <option value="Chinese">Chinese</option>
                                <option value="Japanese">Japanese</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="timezone">Timezone</label>
                            <select id="timezone" name="timezone">
                                <option value="UTC">UTC - Coordinated Universal Time</option>
                                <option value="EST">EST - Eastern Standard Time</option>
                                <option value="CST">CST - Central Standard Time</option>
                                <option value="PST">PST - Pacific Standard Time</option>
                                <option value="GMT">GMT - Greenwich Mean Time</option>
                                <option value="IST">IST - Indian Standard Time</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="save-btn">
                        <i data-feather="save"></i>
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
        </main>
    </div>

    <script src="../assets/js/features.js"></script>
    <script src="../assets/js/profileManagement.js"></script>
</body>
    </html>
<?php
    } else {
        header('location: login.html');
    }
?>

