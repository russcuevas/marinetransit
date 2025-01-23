<?php
include '../connection/database.php';
session_start();

$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
    header('location:admin_login.php');
}

// CRUD

// get accomodations
$get_accomodations = "SELECT * FROM `accomodations`";
$stmt_get_accomodations = $conn->query($get_accomodations);
$accomodations = $stmt_get_accomodations->fetchAll(PDO::FETCH_ASSOC);


// adding accomodations
if (isset($_POST['add-accomodation'])) {
    $accomodation_name = $_POST['accomodation_name'];
    $accomodation_type = $_POST['accomodation_type'];

    // Insert the new accomodation into the database
    $query = "INSERT INTO accomodations (accomodation_name, accomodation_type) VALUES (?, ?)";
    $stmt = $conn->prepare($query);

    if ($stmt) {
        $stmt->execute([$accomodation_name, $accomodation_type]);
        if ($stmt->rowCount() > 0) {
            $_SESSION['success'] = 'Accommodation added successfully!';
            header('location: setting_accom.php');
            exit;
        } else {
            $_SESSION['error'] = 'Error adding accommodation.';
        }
    }
}

// update accomodation
if (isset($_POST['update-accomodation'])) {
    $accomodation_id = $_POST['accomodation_id'];
    $accomodation_name = $_POST['accomodation_name'];
    $accomodation_type = $_POST['accomodation_type'];

    $update_query = "UPDATE accomodations SET accomodation_name = ?, accomodation_type = ? WHERE accomodation_id = ?";
    $stmt_update = $conn->prepare($update_query);
    $stmt_update->execute([$accomodation_name, $accomodation_type, $accomodation_id]);

    if ($stmt_update->rowCount() > 0) {
        $_SESSION['success'] = 'Accommodation updated successfully!';
        header('location: setting_accom.php');
        exit;
    } else {
        $_SESSION['error'] = 'Error updating accommodation.';
    }
}

// delete accomodation
if (isset($_POST['delete-accomodation'])) {
    $accomodation_id = $_POST['accomodation_id_to_delete'];
    $delete_query = "DELETE FROM accomodations WHERE accomodation_id = ?";
    $stmt_delete = $conn->prepare($delete_query);
    $stmt_delete->execute([$accomodation_id]);

    if ($stmt_delete->rowCount() > 0) {
        $_SESSION['success'] = 'Accommodation deleted successfully!';
        header('location: setting_accom.php');
        exit;
    } else {
        $_SESSION['error'] = 'Error deleting accommodation.';
    }
}

?>

<?php include 'header.php' ?>


<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">All Accomodations</h6>

            <a class="btn btn-secondary" data-toggle="modal" data-target="#addAccomodation"> Add New Accomodation </a>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Action</th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($accomodations as $accomodation): ?>
                            <tr>
                                <td><?php echo $accomodation['accomodation_id']; ?></td>
                                <td><?php echo $accomodation['accomodation_name']; ?></td>
                                <td><?php echo $accomodation['accomodation_type']; ?></td>
                                <td class="text-center">
                                    <a href="#" class="btn btn-primary edit" data-toggle="modal" data-target="#edit-accomodation"
                                        data-id="<?php echo $accomodation['accomodation_id']; ?>"
                                        data-name="<?php echo $accomodation['accomodation_name']; ?>"
                                        data-type="<?php echo $accomodation['accomodation_type']; ?>">
                                        <i class="fas fa-edit"> Edit</i>
                                    </a>
                                    <a href="#" class="btn btn-danger delete" data-toggle="modal" data-target="#delete-accomodation"
                                        data-id="<?php echo $accomodation['accomodation_id']; ?>">
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

    <div class="modal fade" id="delete-accomodation" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Delete Accommodation</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this accommodation?</p>
                    <form method="POST">
                        <input type="hidden" name="accomodation_id_to_delete" id="accomodation_id_to_delete">
                        <button class="btn btn-danger" type="submit" name="delete-accomodation">Yes, Delete</button>
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->




    <!-- Add Modal-->
    <div class="modal fade" id="addAccomodation" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add New Accomodation</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">

                    <form id="AddAccomodationForm" class="user" method="POST">
                        <div class="row">
                            <!-- Left-->
                            <div class="col-xl-12 col-md-6">

                                <div class="mb-3">
                                    <label for="exampleFormControlInput1">Accomodation Name</label>
                                    <input class="form-control" id="accomodation_name" name="accomodation_name" type="text">
                                </div>

                            </div>

                            <div class="col-xl-12 col-md-6 mb-5">

                                <div class="mb-3">
                                    <label for="exampleFormControlInput1">Accomodation Type</label>
                                    <select class="form-control" name="accomodation_type" id="accomodation_type">
                                        <option value="passenger"> Passenger </option>
                                        <option value="cargo"> Cargo </option>
                                    </select>
                                </div>

                            </div>
                        </div>


                        <button class="btn btn-primary" type="submit" name="add-accomodation">Add Accommodation</button>
                </div>

                </form>
            </div>
        </div>
    </div>



    <!-- Update Modal-->
    <div class="modal fade" id="edit-accomodation" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add New Accomodation</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">

                    <form method="POST" id="UpdateAccomodationForm" class="user">
                        <input type="hidden" name="accomodation_id" id="update_accomodation_id">
                        <div class="row">
                            <div class="col-xl-12 col-md-6">
                                <div class="mb-3">
                                    <label for="update_accomodation_name">Accommodation Name</label>
                                    <input class="form-control" id="update_accomodation_name" name="accomodation_name" type="text" required>
                                </div>
                            </div>

                            <div class="col-xl-12 col-md-6 mb-5">
                                <div class="mb-3">
                                    <label for="update_accomodation_type">Accommodation Type</label>
                                    <select class="form-control" name="accomodation_type" id="update_accomodation_type" required>
                                        <option value="passenger">Passenger</option>
                                        <option value="cargo">Cargo</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-warning" type="submit" name="update-accomodation">Update Accommodation</button>
                    </form>
                </div>
            </div>
        </div>


    </div>

</div>




<script src="assets/admin/vendor/jquery/jquery.min.js"></script>

<?php include 'footer.php' ?>

<script>
    $('#edit-accomodation').on('show.bs.modal', function(e) {
        var button = $(e.relatedTarget);
        var accomodation_id = button.data('id');
        var accomodation_name = button.data('name');
        var accomodation_type = button.data('type');

        $(this).find('#update_accomodation_id').val(accomodation_id);
        $(this).find('#update_accomodation_name').val(accomodation_name);
        $(this).find('#update_accomodation_type').val(accomodation_type);
    });

    $('#delete-accomodation').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var accomodationId = button.data('id');

        var modal = $(this);
        modal.find('#accomodation_id_to_delete').val(accomodationId);

        console.log("Accomodation ID to delete: " + accomodationId); // Debugging line
    });
</script>