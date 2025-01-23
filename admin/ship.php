<?php
include '../connection/database.php';
session_start();

$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
    header('location:admin_login.php');
    exit;
}

// crud

// get ships
$get_ships = "SELECT * FROM `ships`";
$stmt_get_ships = $conn->query($get_ships);
$ships = $stmt_get_ships->fetchAll(PDO::FETCH_ASSOC);

// add ship
if (isset($_POST['add-ship'])) {
    $ship_code = $_POST['ship_code'];
    $ship_name = $_POST['ship_name'];
    $ship_status = $_POST['ship_status'];

    $query = "INSERT INTO `ships` (ship_code, ship_name, ship_status) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    if ($stmt->execute([$ship_code, $ship_name, $ship_status])) {
        $_SESSION['success'] = 'Ship added successfully!';
        header('location: ship.php');
        exit;
    } else {
        $_SESSION['error'] = 'Error adding ship.';
    }
}

// update ship
if (isset($_POST['update-ship'])) {
    $ship_id = $_POST['update_ship_id'];
    $ship_code = $_POST['update_ship_code'];
    $ship_name = $_POST['update_ship_name'];
    $ship_status = $_POST['update_ship_status'];

    $update_query = "UPDATE `ships` SET ship_code = ?, ship_name = ?, ship_status = ? WHERE ship_id = ?";
    $stmt_update = $conn->prepare($update_query);
    if ($stmt_update->execute([$ship_code, $ship_name, $ship_status, $ship_id])) {
        $_SESSION['success'] = 'Ship updated successfully!';
        header('location: ship.php');
        exit;
    } else {
        $_SESSION['error'] = 'Error updating ship.';
    }
}
?>

<?php include 'header.php' ?>

<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">All Ships</h6>
            <a class="btn btn-secondary" data-toggle="modal" data-target="#addShip"> Add New Ship </a>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Ship Code</th>
                            <th>Ship Name</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ships as $ship): ?>
                            <tr>
                                <td><?php echo $ship['ship_id']; ?></td>
                                <td><?php echo $ship['ship_code']; ?></td>
                                <td><?php echo $ship['ship_name']; ?></td>
                                <td><?php echo $ship['ship_status']; ?></td>
                                <td class="text-center">
                                    <a href="#" class="btn btn-primary edit" data-toggle="modal" data-target="#editShip"
                                        data-id="<?php echo $ship['ship_id']; ?>"
                                        data-code="<?php echo $ship['ship_code']; ?>"
                                        data-name="<?php echo $ship['ship_name']; ?>"
                                        data-status="<?php echo $ship['ship_status']; ?>">
                                        <i class="fas fa-edit"> Edit</i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
<!-- /.container-fluid -->


<!-- Add Modal-->
<div class="modal fade" id="addShip" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add New Ship</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">

                <form id="AddShipForm" class="user" method="POST">
                    <div class="row">
                        <div class="col-xl-12 col-md-6 mb-4">
                            <div class="mb-3">
                                <label for="ship_code">Ship Code</label>
                                <input class="form-control form-control-solid" type="text" id="ship_code" name="ship_code" required>
                            </div>

                            <div class="mb-3">
                                <label for="ship_name">Ship Name</label>
                                <input class="form-control form-control-solid" type="text" id="ship_name" name="ship_name" required>
                            </div>

                            <div class="mb-3">
                                <label for="ship_status">Status</label>
                                <select class="form-control form-control-solid" id="ship_status" name="ship_status">
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-primary" type="submit" name="add-ship">Add Ship</button>
                </form>
            </div>
        </div>
    </div>
</div>


<!-- Update Modal-->
<div class="modal fade" id="editShip" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Update Ship</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">

                <form id="UpdateShipForm" class="user" method="POST">
                    <input type="hidden" id="update_ship_id" name="update_ship_id">
                    <div class="row">
                        <div class="col-xl-12 col-md-6 mb-4">
                            <div class="mb-3">
                                <label for="update_ship_code">Ship Code</label>
                                <input class="form-control form-control-solid" type="text" id="update_ship_code" name="update_ship_code" required>
                            </div>

                            <div class="mb-3">
                                <label for="update_ship_name">Ship Name</label>
                                <input class="form-control form-control-solid" type="text" id="update_ship_name" name="update_ship_name" required>
                            </div>

                            <div class="mb-3">
                                <label for="update_ship_status">Status</label>
                                <select class="form-control form-control-solid" id="update_ship_status" name="update_ship_status">
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-success" type="submit" name="update-ship">Update Ship</button>
                </form>
            </div>
        </div>
    </div>
</div>


<script src="assets/admin/vendor/jquery/jquery.min.js"></script>
<?php include 'footer.php' ?>

<script>
    $('#editShip').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var shipId = button.data('id');
        var shipCode = button.data('code');
        var shipName = button.data('name');
        var shipStatus = button.data('status');

        var modal = $(this);
        modal.find('#update_ship_id').val(shipId);
        modal.find('#update_ship_code').val(shipCode);
        modal.find('#update_ship_name').val(shipName);
        modal.find('#update_ship_status').val(shipStatus);
    });
</script>