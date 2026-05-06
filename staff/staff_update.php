<?php
session_start();
include('../include/dbconnection.php');

if (empty($_SESSION['uid'])) {
    header('location:../auth/login.php');
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $staff_id = $_POST['staff_id'];
    $staff_name = $_POST['staff_name'];
    $staff_nic = $_POST['staff_nic'];
    $staff_telephone = $_POST['staff_telephone'];
    $staff_role = $_POST['staff_role'];
    $staff_username = $_POST['staff_username'];
    $staff_password = $_POST['staff_password'];

    // Basic validation
    if (empty($staff_id) || empty($staff_name) || empty($staff_nic) || empty($staff_telephone) || empty($staff_role) || empty($staff_username)) {
        echo "All fields except password are required.";
        exit;
    }

    // Check for duplicate username, excluding the current staff member
    $check_stmt = $con->prepare("SELECT ID FROM tblstaff WHERE UserName = ? AND ID != ?");
    $check_stmt->bind_param("si", $staff_username, $staff_id);
    $check_stmt->execute();
    $check_stmt->store_result();
    if ($check_stmt->num_rows > 0) {
        echo "Username already exists. Please choose a different one.";
        $check_stmt->close();
        $con->close();
        exit;
    }
    $check_stmt->close();

    // Fetch current details for activity logging
    $stmt_old = $con->prepare("SELECT * FROM tblstaff WHERE ID = ?");
    $stmt_old->bind_param("i", $staff_id);
    $stmt_old->execute();
    $res_old = $stmt_old->get_result();
    $old_data = $res_old->fetch_assoc();
    $old_name = $old_data['StaffName'] ?? 'Unknown';
    $stmt_old->close();

    // Track specific changes for logging
    $changes = [];
    if (($old_data['StaffName'] ?? '') != $staff_name) $changes[] = "Name";
    if (($old_data['StaffNIC'] ?? '') != $staff_nic) $changes[] = "NIC";
    if (($old_data['StaffTel'] ?? '') != $staff_telephone) $changes[] = "Telephone";
    if (($old_data['StaffRole'] ?? '') != $staff_role) $changes[] = "Role";
    if (($old_data['UserName'] ?? '') != $staff_username) $changes[] = "Username";
    if (!empty($staff_password)) $changes[] = "Password";

    // Prepare the update query
    if (!empty($staff_password)) {
        // If password is being updated
        $hashed_password = password_hash($staff_password, PASSWORD_DEFAULT);
        $stmt = $con->prepare("UPDATE tblstaff SET StaffName=?, StaffNIC=?, StaffTel=?, StaffRole=?, UserName=?, Password=? WHERE ID=?");
        $stmt->bind_param("ssssssi", $staff_name, $staff_nic, $staff_telephone, $staff_role, $staff_username, $hashed_password, $staff_id);
    } else {
        // If password is not being updated
        $stmt = $con->prepare("UPDATE tblstaff SET StaffName=?, StaffNIC=?, StaffTel=?, StaffRole=?, UserName=? WHERE ID=?");
        $stmt->bind_param("sssssi", $staff_name, $staff_nic, $staff_telephone, $staff_role, $staff_username, $staff_id);
    }

    if ($stmt->execute()) {
        // Log user activity
        $user_id = $_SESSION['uid'];
        $change_list = !empty($changes) ? " (" . implode(', ', $changes) . ")" : "";
        $activity_desc = "Updated staff member details for: " . $old_name . $change_list;
        $log_sql = "INSERT INTO tbluser_activity (UserID, Activity, ActivityTime) VALUES (?, ?, NOW())";
        if ($log_stmt = $con->prepare($log_sql)) {
            $log_stmt->bind_param("is", $user_id, $activity_desc);
            $log_stmt->execute();
            $log_stmt->close();
        }

        echo 'yes';
    } else {
        echo 'Something went wrong. Please try again. Error: ' . htmlspecialchars($stmt->error);
    }
    $stmt->close();
    $con->close();
} else {
    echo "Invalid request method.";
}
?>