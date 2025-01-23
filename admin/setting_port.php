<?php
include '../connection/database.php';
session_start();

$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
    header('location:admin_login.php');
}

// CRUD

// get port
$get_port = "SELECT * FROM `ports`";
$stmt_get_port = $conn->query($get_port);
$ports = $stmt_get_port->fetchAll(PDO::FETCH_ASSOC);


// adding port
if (isset($_POST['add-port'])) {
    $port_name = $_POST['port_name'];
    $port_location = $_POST['port_location'];
    $query = "INSERT INTO ports (port_name, port_location) VALUES (?, ?)";
    $stmt = $conn->prepare($query);

    if ($stmt) {
        $stmt->execute([$port_name, $port_location]);
        if ($stmt->rowCount() > 0) {
            $_SESSION['success'] = 'Port added successfully!';
            header('location: setting_port.php');
            exit;
        } else {
            $_SESSION['error'] = 'Error adding port.';
        }
    }
}

// update port
if (isset($_POST['update-port'])) {
    $port_id = $_POST['update_port_id'];
    $port_name = $_POST['update_port_name'];
    $port_location = $_POST['update_port_location'];

    $update_query = "UPDATE ports SET port_name = ?, port_location = ? WHERE port_id = ?";
    $stmt_update = $conn->prepare($update_query);
    $stmt_update->execute([$port_name, $port_location, $port_id]);

    if ($stmt_update->rowCount() > 0) {
        $_SESSION['success'] = 'Port updated successfully!';
        header('location: setting_port.php');
        exit;
    } else {
        $_SESSION['error'] = 'Error updating port.';
    }
}

// delete port
if (isset($_POST['delete-port'])) {
    $port_id = $_POST['port_id_to_delete'];
    $delete_query = "DELETE FROM ports WHERE port_id = ?";
    $stmt_delete = $conn->prepare($delete_query);
    $stmt_delete->execute([$port_id]);

    if ($stmt_delete->rowCount() > 0) {
        $_SESSION['success'] = 'Port deleted successfully!';
        header('location: setting_port.php');
        exit;
    } else {
        $_SESSION['error'] = 'Error deleting port.';
    }
}

?>

<?php include 'header.php' ?>


<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">All Ports</h6>

            <a class="btn btn-secondary" data-toggle="modal" data-target="#addPort"> Add New Port </a>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Location</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ports as $port): ?>
                            <tr>
                                <td><?php echo $port['port_id']; ?></td>
                                <td><?php echo $port['port_name']; ?></td>
                                <td><?php echo $port['port_location']; ?></td>
                                <td class="text-center">
                                    <a href="#" class="btn btn-primary edit" data-toggle="modal" data-target="#editPort"
                                        data-id="<?php echo $port['port_id']; ?>"
                                        data-name="<?php echo $port['port_name']; ?>"
                                        data-location="<?php echo $port['port_location']; ?>">
                                        <i class="fas fa-edit"> Edit</i>
                                    </a>

                                    <a href="#" class="btn btn-danger delete" data-toggle="modal" data-target="#deletePort"
                                        data-id="<?php echo $port['port_id']; ?>">
                                        <i class="fas fa-trash"> Delete</i>
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
<div class="modal fade" id="addPort" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add New Port</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">

                <form id="AddPortForm" class="user" action="" method="POST">
                    <div class="row">
                        <!-- Left-->
                        <div class="col-xl-12 col-md-6 mb-4">

                            <div class="mb-3">
                                <label for="exampleFormControlInput1">Port Name</label>
                                <input class="form-control" id="port_name" name="port_name" type="text">
                            </div>

                            <div class="mb-3">
                                <label for="exampleFormControlInput1">Port Address</label>
                                <input class="form-control" id="port_location" name="port_location" type="text">
                            </div>

                        </div>
                    </div>
                    <button class="btn btn-primary" type="submit" name="add-port">Add Port</button>
            </div>
            </form>
        </div>
    </div>
</div>



<!-- Update Modal-->
<div class="modal fade" id="editPort" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Update Port</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="UpdatePortForm" class="user" method="POST">
                    <input type="hidden" name="update_port_id" id="update_port_id">
                    <div class="row">
                        <div class="col-xl-12 col-md-6 mb-4">
                            <div class="mb-3">
                                <label for="update_port_name">Port Name</label>
                                <input class="form-control" id="update_port_name" name="update_port_name" type="text" required>
                            </div>
                            <div class="mb-3">
                                <label for="update_port_location">Port Address</label>
                                <input class="form-control" id="update_port_location" name="update_port_location" type="text" required>
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-primary" type="submit" name="update-port">Update Port</button>
                    <button class="btn btn-danger" type="button" data-dismiss="modal">Cancel</button>
                </form>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="deletePort" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Delete Port</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this port?</p>
                <form method="POST">
                    <input type="hidden" name="port_id_to_delete" id="port_id_to_delete">
                    <button class="btn btn-danger" type="submit" name="delete-port">Yes, Delete</button>
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                </form>
            </div>
        </div>
    </div>
</div>



<script src="assets/admin/vendor/jquery/jquery.min.js"></script>
<?php include 'footer.php' ?>

<script>
    $('#editPort').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var portId = button.data('id');
        var portName = button.data('name');
        var portLocation = button.data('location');

        var modal = $(this);
        modal.find('#update_port_id').val(portId);
        modal.find('#update_port_name').val(portName);
        modal.find('#update_port_location').val(portLocation);
    });

    $('#deletePort').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var portId = button.data('id');

        var modal = $(this);
        modal.find('#port_id_to_delete').val(portId);
    });
</script>