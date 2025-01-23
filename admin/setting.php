<?php
include '../connection/database.php';
session_start();

$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
    header('location:admin_login.php');
    exit;
}

// get system information
$get_system_info_query = "SELECT * FROM `systeminfo` WHERE systeminfo_id = 1";
$stmt = $conn->query($get_system_info_query);
$system_info = $stmt->fetch(PDO::FETCH_ASSOC);

// update system information
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $systeminfo_name = $_POST['systeminfo_name'];
    $systeminfo_shortname = $_POST['systeminfo_shortname'];

    $systeminfo_icon = $_FILES['systeminfo_icon']['name'];
    $upload_dir = 'assets/system_image/';
    $upload_file = $upload_dir . basename($systeminfo_icon);

    if ($systeminfo_icon) {
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        move_uploaded_file($_FILES['systeminfo_icon']['tmp_name'], $upload_file);
    } else {
        $systeminfo_icon = $system_info['systeminfo_icon'];
    }

    $update_query = "UPDATE `systeminfo` SET systeminfo_name = ?, systeminfo_shortname = ?, systeminfo_icon = ? WHERE systeminfo_id = 1";
    $stmt_update = $conn->prepare($update_query);
    $stmt_update->execute([$systeminfo_name, $systeminfo_shortname, $systeminfo_icon]);

    if ($stmt_update->rowCount() > 0) {
        $_SESSION['success'] = 'System information updated successfully!';
        header('location: setting.php');
        exit;
    } else {
        $_SESSION['error'] = 'Error updating system information.';
    }
}
?>

<?php include 'header.php' ?>

<!-- Begin Page Content -->
<div class="container-fluid">

    <div class="card card-outline card-primary">
        <div class="card-header">
            <h5 class="card-title">System Information</h5>
        </div>
        <div class="card-body">
            <form id="systeminfo" name="systeminfo" enctype="multipart/form-data" method="POST">
                <div id="msg" class="form-group"></div>

                <div class="form-group">
                    <label for="systeminfo_name" class="control-label">System Name</label>
                    <input type="text" class="form-control" name="systeminfo_name" id="systeminfo_name" value="<?php echo $system_info['systeminfo_name']; ?>" required>
                </div>

                <div class="form-group">
                    <label for="systeminfo_shortname" class="control-label">System Short Name</label>
                    <input type="text" class="form-control" name="systeminfo_shortname" id="systeminfo_shortname" value="<?php echo $system_info['systeminfo_shortname']; ?>" required>
                </div>

                <div class="form-group">
                    <label for="systeminfo_icon" class="control-label">System Logo</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input rounded-circle" id="systeminfo_icon" name="systeminfo_icon">
                        <label class="custom-file-label" for="systeminfo_icon">Choose file</label>
                    </div>
                </div>

                <div class="form-group d-flex justify-content-center">
                    <img src="assets/system_image/<?php echo $system_info['systeminfo_icon']; ?>" alt="System Logo" id="cimg2" class="img-fluid img-thumbnail">
                </div>

        </div>
        <div class="card-footer">
            <div class="col-md-12">
                <div class="row">
                    <button class="btn btn-sm btn-primary" type="submit">Update</button>
                </div>
            </div>
        </div>

        </form>
    </div>

</div>
<!-- /.container-fluid -->

<script src="assets/admin/vendor/jquery/jquery.min.js"></script>

<?php include 'footer.php' ?>