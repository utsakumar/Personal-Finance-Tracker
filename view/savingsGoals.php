<?php
    session_start();
    if(isset($_SESSION['status'])){
        require_once('../controller/savingsGoalsCheck.php');
        
        $errors = [];
        $success = false;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $validationResult = validateSavingsGoal($_POST);
            $errors = $validationResult['errors'];
            $success = $validationResult['success'];
        }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Savings Goals</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <link rel="stylesheet" href="../assets/css/feature.css" />
    <link rel="stylesheet" href="../assets/css/savingsGoals.css" />
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
                <li class="active" data-page="savings-goals">
                    <a href="savingsGoals.php"><i data-feather="dollar-sign"></i> Savings Goals</a>
                </li>
                <li data-page="reports">
                    <a href="reports-graphs.php"><i data-feather="bar-chart-2"></i> Reports</a>
                </li>
                <li data-page="account-linking">
                    <a href="account-linking.php"><i data-feather="link"></i> Account Linking</a>
                </li>
                <li data-page="profile">
                    <a href="profileManagement.php"><i data-feather="user"></i> Profile</a>
                </li>
                <li data-page="logout">
                    <a href="../controller/logout.php"><i data-feather="log-out"></i> Log Out</a>
                </li>
            </ul>
        </nav>

        <main class="main-content">
        <header class="page-header">
            <h1>Savings Goals</h1>
            <div class="header-stats">
                <div class="stat-card">
                    <i data-feather="target"></i>
                    <div class="stat-info">
                        <span class="stat-value" id="totalGoals">0</span>
                        <span class="stat-label">Active Goals</span>
                    </div>
                </div>
                <div class="stat-card">
                    <i data-feather="dollar-sign"></i>
                    <div class="stat-info">
                        <span class="stat-value" id="totalSaved">$0</span>
                        <span class="stat-label">Total Saved</span>
                    </div>
                </div>
                <div class="stat-card">
                    <i data-feather="award"></i>
                    <div class="stat-info">
                        <span class="stat-value" id="completedGoals">0</span>
                        <span class="stat-label">Completed Goals</span>
                    </div>
                </div>
            </div>
        </header>

        <!-- Goal Visualizer Section -->
        <div class="goal-container">
            <div class="header">
                <div class="section-title">
                    <i data-feather="target"></i>
                    <h2>Goal Visualizer</h2>
                </div>
                <button class="add-goal-btn" onclick="showAddGoalModal()">
                    <i data-feather="plus"></i> Add New Goal
                </button>
            </div>
            <div class="goals-grid" id="goalsGrid">
                <!-- Goals will be populated by JavaScript -->
            </div>
        </div>

        <!-- Milestone Tracker Section -->
        <div class="goal-container">
            <div class="header">
                <div class="section-title">
                    <i data-feather="award"></i>
                    <h2>Milestone Tracker</h2>
                </div>
            </div>
            <div class="milestones-container" id="milestonesContainer">
                <!-- Milestones will be populated by JavaScript -->
            </div>
        </div>
    </main>

    <!-- Add Goal Modal -->
    <div class="modal" id="addGoalModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Add New Savings Goal</h2>
                <button class="close-btn" onclick="hideAddGoalModal()">
                    <i data-feather="x"></i>
                </button>
            </div>
            <form id="addGoalForm">
                <div class="form-group">
                    <label for="goalName">Goal Name</label>
                    <input type="text" id="goalName" name="goalName" required>
                </div>
                <div class="form-group">
                    <label for="targetAmount">Target Amount</label>
                    <input type="number" id="targetAmount" name="targetAmount" min= "0.01" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="currentAmount">Current Amount</label>
                    <input type="number" id="currentAmount" name="currentAmount" min= "0" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="targetDate">Target Date</label>
                    <input type="date" id="targetDate" name="targetDate" required>
                </div>
                <div class="form-group">
                    <label for="category">Category</label>
                    <select id="category" name="category" required>
                        <option value="">Select Category</option>
                        <option value="Emergency Fund">Emergency Fund</option>
                        <option value="Vacation">Vacation</option>
                        <option value="Home">Home</option>
                        <option value="Car">Car</option>
                        <option value="Education">Education</option>
                        <option value="Retirement">Retirement</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="monthlyContribution">Monthly Contribution</label>
                    <input type="number" id="monthlyContribution" name="monthlyContribution" min="0" step="0.01" required>
                </div>
                <button type="submit" class="submit-btn">Create Goal</button>
            </form>
        </div>
    </div>

    <!-- Celebration Modal -->
    <div class="modal" id="celebrationModal">
        <div class="modal-content celebration">
            <div class="celebration-content">
                <i data-feather="award" class="celebration-icon"></i>
                <h2>Goal Achieved!</h2>
                <p id="celebrationMessage"></p>
                <button class="celebration-btn" onclick="hideCelebrationModal()">Continue</button>
            </div>
        </div>
    </div>
        </main>
    </div>

    <script src="../assets/js/features.js"></script>
    <script src="../assets/js/savingsGoals.js"></script>
</body>
</html>
<?php
    }else{
        header('location: login.html');
    }
?>
