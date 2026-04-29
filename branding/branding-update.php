<?php
session_start();
include('../include/dbconnection.php');

if (empty($_SESSION['uid'])) {
    header('location:../auth/login.php');
    exit;
}

// Initialize variables
$company_name = $_POST['company_name'] ?? '';
$website_name = $_POST['website_name'] ?? '';
$phone_no = $_POST['phone_no'] ?? '';
$address = $_POST['address'] ?? '';
$service_charge = $_POST['service_charge'] ?? 0;
$pax = $_POST['no_of_pax'] ?? 0;

$uploadDir = 'uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Get current file paths from DB to avoid overwriting them with empty values if no new file is uploaded
$sql_select = "SELECT * FROM tblbranding LIMIT 1";
$result = mysqli_query($con, $sql_select);
$current_data = mysqli_fetch_assoc($result) ?: [];
$logo_filename_db = $current_data['logo'] ?? '';
$favicon_filename_db = $current_data['favicon'] ?? '';
$sound_filename_db = $current_data['notification_sound'] ?? '';

// Track changes for a more detailed activity log
$changes = [];
if (($current_data['company_name'] ?? '') != $company_name) $changes[] = "Company Name";
if (($current_data['website_name'] ?? '') != $website_name) $changes[] = "Website Name";
if (($current_data['phone_no'] ?? '') != $phone_no) $changes[] = "Phone No";
if (($current_data['address'] ?? '') != $address) $changes[] = "Address";
if (floatval($current_data['service_charge'] ?? 0) != floatval($service_charge)) $changes[] = "Service Charge";
if (intval($current_data['pax'] ?? 0) != intval($pax)) $changes[] = "Pax";

// Handle logo upload
if (isset($_FILES['logo']) && $_FILES['logo']['error'] == UPLOAD_ERR_OK) {
    $new_logo_filename = time() . '_' . basename($_FILES['logo']['name']);
    $logo_target_file = $uploadDir . $new_logo_filename;
    if (move_uploaded_file($_FILES['logo']['tmp_name'], $logo_target_file)) {
        // Delete old file if it exists
        if (!empty($logo_filename_db) && file_exists($uploadDir . $logo_filename_db)) {
            unlink($uploadDir . $logo_filename_db);
        }
        $logo_filename_db = $new_logo_filename; // Update the variable for the DB with the new filename
        $changes[] = "Logo";
    } else {
        echo "Error uploading logo.";
        exit;
    }
}

// Handle favicon upload
if (isset($_FILES['favicon']) && $_FILES['favicon']['error'] == UPLOAD_ERR_OK) {
    $new_favicon_filename = time() . '_' . basename($_FILES['favicon']['name']);
    $favicon_target_file = $uploadDir . $new_favicon_filename;
    if (move_uploaded_file($_FILES['favicon']['tmp_name'], $favicon_target_file)) {
        // Delete old file if it exists
        if (!empty($favicon_filename_db) && file_exists($uploadDir . $favicon_filename_db)) {
            unlink($uploadDir . $favicon_filename_db);
        }
        $favicon_filename_db = $new_favicon_filename; // Update the variable for the DB with the new filename
        $changes[] = "Favicon";
    } else {
        echo "Error uploading favicon.";
        exit;
    }
}

// Handle notification sound upload
if (isset($_FILES['notification_sound']) && $_FILES['notification_sound']['error'] == UPLOAD_ERR_OK) {
    $new_sound_filename = time() . '_' . basename($_FILES['notification_sound']['name']);
    $sound_target_file = $uploadDir . $new_sound_filename;
    if (move_uploaded_file($_FILES['notification_sound']['tmp_name'], $sound_target_file)) {
        // Delete old file if it exists
        if (!empty($sound_filename_db) && file_exists($uploadDir . $sound_filename_db)) {
            unlink($uploadDir . $sound_filename_db);
        }
        $sound_filename_db = $new_sound_filename; // Update the variable for the DB with the new filename
        $changes[] = "Notification Sound";
    } else {
        echo "Error uploading notification sound.";
        exit;
    }
}

// Check if a row exists. If not, insert. If yes, update.
$sql_check = "SELECT ID FROM tblbranding LIMIT 1";
$check_result = mysqli_query($con, $sql_check);

if (mysqli_num_rows($check_result) > 0) {
    // Row exists, so UPDATE
    $sql = "UPDATE tblbranding SET company_name=?, website_name=?, phone_no=?, address=?, logo=?, favicon=?, service_charge=?, pax=?, notification_sound=? LIMIT 1";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ssssssdis", $company_name, $website_name, $phone_no, $address, $logo_filename_db, $favicon_filename_db, $service_charge, $pax, $sound_filename_db);
} else {
    // No row exists, so INSERT
    $sql = "INSERT INTO tblbranding (ID, company_name, website_name, phone_no, address, logo, favicon, service_charge, pax, notification_sound) VALUES (1, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ssssssdis", $company_name, $website_name, $phone_no, $address, $logo_filename_db, $favicon_filename_db, $service_charge, $pax, $sound_filename_db);
}

if ($stmt) {
    if (mysqli_stmt_execute($stmt)) {
        // Log user activity for the branding update
        $user_id = $_SESSION['uid'];
        
        $change_list = !empty($changes) ? " (" . implode(', ', $changes) . ")" : "";
        $activity_desc = (empty($current_data)) ? "Initial branding setup" : "Updated branding details" . $change_list;

        $log_sql = "INSERT INTO tbluser_activity (UserID, Activity, ActivityTime) VALUES (?, ?, NOW())";
        $log_stmt = mysqli_prepare($con, $log_sql);
        if ($log_stmt) {
            mysqli_stmt_bind_param($log_stmt, "is", $user_id, $activity_desc);
            mysqli_stmt_execute($log_stmt);
            mysqli_stmt_close($log_stmt);
        }
        echo "yes";
    } else {
        echo "no: " . mysqli_stmt_error($stmt);
    }
    mysqli_stmt_close($stmt);
} else {
    echo "no: " . mysqli_error($con);
}

mysqli_close($con);
?>