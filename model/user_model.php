<?php
require_once 'db.php';

function login($user) {
    $con = getConnection();
    $username = $user['username'];
    $password = $user['password'];

    $sql = "SELECT * FROM login WHERE uname = '$username' AND pass = '$password'";
    $result = mysqli_query($con, $sql);
    $count = mysqli_num_rows($result);

    mysqli_close($con);

    return $count === 1;
}

function register($user) {
    $con = getConnection();

    $fname = $user['firstname'];
    $lname = $user['lastname'];
    $uname = $user['username'];
    $email = $user['email'];
    $pass  = $user['password'];

    // Check if username or email already exists
    $checkSql = "SELECT * FROM singup WHERE u_username = '$uname' OR u_email = '$email'";
    $checkResult = mysqli_query($con, $checkSql);

    if (mysqli_num_rows($checkResult) > 0) {
        mysqli_close($con);
        return "exists"; // Username/email already exists
    }

    $sql1 = "INSERT INTO singup (u_fname, u_lname, u_username, u_email, u_password)
             VALUES ('$fname', '$lname', '$uname', '$email', '$pass')";
    
    $sql2 = "INSERT INTO login (uname, pass)
             VALUES ('$uname', '$pass')";

    $success1 = mysqli_query($con, $sql1);
    $success2 = mysqli_query($con, $sql2);

    mysqli_close($con);

    return ($success1 && $success2) ? "success" : "fail";
}

// Function to get user by username
function getUserByUsername($username) {
    $con = getConnection();
    $user = null;

    $username = mysqli_real_escape_string($con, $username);
    // Try to get phone if column exists, otherwise ignore
    $sql = "SELECT u_id, u_fname, u_lname, u_username, u_email";
    
    // Check if phone column exists (optional)
    $checkColumns = "SHOW COLUMNS FROM singup LIKE 'u_phone'";
    $columnCheck = mysqli_query($con, $checkColumns);
    if ($columnCheck && mysqli_num_rows($columnCheck) > 0) {
        $sql .= ", u_phone";
    }
    
    $sql .= " FROM singup WHERE u_username = '$username'";
    $result = mysqli_query($con, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        mysqli_free_result($result);
    }
    mysqli_close($con);
    return $user;
}

// Function to add a new user from admin panel (expects associative array from $_POST)
function addUser($userData) {
    $con = getConnection();

    $fname   = $userData['firstname'];
    $lname   = $userData['lastname'];
    $uname   = $userData['username'];
    $email   = $userData['email'];
    $pass    = $userData['password']; // Remember to hash passwords in a real application!

    // Check if username or email already exists
    $checkSql = "SELECT * FROM singup WHERE u_username = '$uname' OR u_email = '$email'";
    $checkResult = mysqli_query($con, $checkSql);

    if (mysqli_num_rows($checkResult) > 0) {
        mysqli_close($con);
        return "exists"; // Username/email already exists
    }

    $sql = "INSERT INTO singup (u_fname, u_lname, u_username, u_email, u_password)
            VALUES ('$fname', '$lname', '$uname', '$email', '$pass')";
    
    $sql_login = "INSERT INTO login (uname, pass) VALUES ('$uname', '$pass')";

    $success = mysqli_query($con, $sql);
    $success_login = mysqli_query($con, $sql_login);

    mysqli_close($con);

    return ($success && $success_login) ? "success" : "fail";
}


function getAllUsers($searchTerm = '') {
    $con = getConnection();
    $users = [];

    $sql = "SELECT u_id, u_fname, u_lname, u_username, u_email FROM singup";

    if (!empty($searchTerm)) {
        $searchTerm = mysqli_real_escape_string($con, $searchTerm); 
        $sql .= " WHERE u_fname LIKE '%$searchTerm%' OR u_lname LIKE '%$searchTerm%' OR u_username LIKE '%$searchTerm%' OR u_email LIKE '%$searchTerm%'";
    }

    $result = mysqli_query($con, $sql);

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $users[] = $row;
        }
        mysqli_free_result($result);
    } else {
        error_log("Error fetching users: " . mysqli_error($con));
    }
    mysqli_close($con);
    return $users;
}

// Function to get a single user by ID
function getUserById($id) {
    $con = getConnection();
    $user = null;

    // Sanitize input to prevent SQL injection
    $id = mysqli_real_escape_string($con, $id);

    $sql = "SELECT u_id, u_fname, u_lname, u_username, u_email FROM singup WHERE u_id = '$id'"; //
    $result = mysqli_query($con, $sql); //

    if ($result && mysqli_num_rows($result) > 0) { //
        $user = mysqli_fetch_assoc($result); //
        mysqli_free_result($result);
    } else {
        error_log("Error fetching single user: " . mysqli_error($con));
    }
    mysqli_close($con);
    return $user;
}

// Function to update user details
function updateUser($userData) { //
    $con = getConnection(); //

    $id      = mysqli_real_escape_string($con, $userData['user_id']); //
    $fname   = mysqli_real_escape_string($con, $userData['firstname']); //
    $lname   = mysqli_real_escape_string($con, $userData['lastname']); //
    $uname   = mysqli_real_escape_string($con, $userData['username']); //
    $email   = mysqli_real_escape_string($con, $userData['email']); //

    $sql = "UPDATE singup SET
                u_fname = '$fname',
                u_lname = '$lname',
                u_username = '$uname',
                u_email = '$email'"; //

    // Only update password if a new one is provided
    if (isset($userData['password']) && !empty($userData['password'])) { //
        $pass = mysqli_real_escape_string($con, $userData['password']); // Remember to hash passwords!
        $sql .= ", u_password = '$pass'"; //
        $sql_login = "UPDATE login SET pass = '$pass' WHERE uname = '$uname'"; //
        mysqli_query($con, $sql_login); //
    }

    $sql .= " WHERE u_id = '$id'"; //

    $success = mysqli_query($con, $sql); //
    mysqli_close($con); //

    return $success ? "success" : "fail"; //
}

// Function to update current user profile
function updateCurrentUserProfile($username, $userData) {
    $con = getConnection();
    
    $username = mysqli_real_escape_string($con, $username);
    $fname = mysqli_real_escape_string($con, $userData['firstName']);
    $lname = mysqli_real_escape_string($con, $userData['lastName']);
    $email = mysqli_real_escape_string($con, $userData['email']);
    $phone = isset($userData['phone']) ? mysqli_real_escape_string($con, $userData['phone']) : '';
    
    $sql = "UPDATE singup SET 
            u_fname = '$fname',
            u_lname = '$lname',
            u_email = '$email'";
    
    // Add phone if table has phone column (you may need to add this column to your database)
    // $sql .= ", u_phone = '$phone'";
    
    $sql .= " WHERE u_username = '$username'";
    
    $success = mysqli_query($con, $sql);
    
    // Update password if provided
    if (isset($userData['newPassword']) && !empty($userData['newPassword'])) {
        // Verify current password first
        $currentPass = mysqli_real_escape_string($con, $userData['currentPassword']);
        $checkSql = "SELECT * FROM login WHERE uname = '$username' AND pass = '$currentPass'";
        $checkResult = mysqli_query($con, $checkSql);
        
        if (mysqli_num_rows($checkResult) > 0) {
            $newPass = mysqli_real_escape_string($con, $userData['newPassword']);
            $updatePassSql = "UPDATE login SET pass = '$newPass' WHERE uname = '$username'";
            mysqli_query($con, $updatePassSql);
        } else {
            mysqli_close($con);
            return "invalid_password";
        }
    }
    
    mysqli_close($con);
    return $success ? "success" : "fail";
}

function deleteUser($id) { //
    $con = getConnection(); //

    // Get username before deleting from singup, if needed for login table deletion
    $uname_sql = "SELECT u_username FROM singup WHERE u_id = '$id'"; //
    $uname_result = mysqli_query($con, $uname_sql); //
    $username_to_delete = null; //
    if ($uname_result && mysqli_num_rows($uname_result) > 0) { //
        $row = mysqli_fetch_assoc($uname_result); //
        $username_to_delete = $row['u_username']; //
    }

    $sql_singup = "DELETE FROM singup WHERE u_id = '$id'"; //
    $success_singup = mysqli_query($con, $sql_singup); //

    $success_login = true; //
    if ($username_to_delete) { //
        $sql_login = "DELETE FROM login WHERE uname = '$username_to_delete'"; //
        $success_login = mysqli_query($con, $sql_login); //
    }

    mysqli_close($con); //
    return ($success_singup && $success_login) ? "success" : "fail"; //
}
?>
