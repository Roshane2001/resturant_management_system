<?php
session_start();
include('../include/dbconnection.php');

if (empty($_SESSION['uid'])) {
    header('location:../auth/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>RMS - User Activity Log</title>
    <?php include_once('../include/header.php'); ?>
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <?php include_once('../include/sidebar.php'); ?>

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <?php include_once('../include/top-nav.php'); ?>

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <h1 class="h3 mb-4 text-gray-800">User Activity Log</h1>

                    <!-- Recent Activity Card -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">System Activity History</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="activityTable" width="100%" cellspacing="0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Staff Member</th>
                                            <th>Action Taken</th>
                                            <th>Date & Time</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Fetch latest activities from the log (showing all instead of LIMIT 5)
                                        $activity_sql = "SELECT a.*, s.StaffName FROM tbluser_activity a 
                                                         LEFT JOIN tblstaff s ON a.UserID = s.ID 
                                                         ORDER BY a.ActivityTime DESC";
                                        $activity_result = mysqli_query($con, $activity_sql);
                                        if(mysqli_num_rows($activity_result) > 0) {
                                            while($act = mysqli_fetch_assoc($activity_result)) {
                                                $activity = $act['Activity'];
                                                $badge_class = 'badge-secondary';
                                                if (strpos($activity, 'Stock') !== false) $badge_class = 'badge-info';
                                                if (strpos($activity, 'branding') !== false) $badge_class = 'badge-primary';
                                                if (strpos($activity, 'product') !== false) $badge_class = 'badge-success';
                                                if (strpos($activity, 'category') !== false) $badge_class = 'badge-warning';
                                                
                                                echo "<tr>
                                                        <td class='font-weight-bold'>".htmlspecialchars($act['StaffName'] ?? 'Unknown User')."</td>
                                                        <td>
                                                            <span class='badge $badge_class mr-2'>&nbsp;</span>
                                                            ".htmlspecialchars($activity)."
                                                        </td>
                                                        <td>".date('Y-m-d h:i A', strtotime($act['ActivityTime']))."</td>
                                                      </tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='3' class='text-center text-muted'>No activity found in logs.</td></tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <?php include_once('../include/footer.php'); ?>

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <?php include_once('../include/script.php'); ?>
    <!-- Page level plugins -->
    <script src="/resturant-management-system/vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="/resturant-management-system/vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <script>
    $(document).ready(function() {
        $('#activityTable').DataTable({
            "order": [[ 2, "desc" ]] // Sort by Date & Time descending by default
        });
    });
    </script>

</body>

</html>