<?php
include '../connection/database.php';
session_start();

$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
    header('location:admin_login.php');
}

// Fetch cashiers (user_type = 'cashier')
$get_cashier = "SELECT * FROM `users` WHERE user_type = 'cashier'";
$stmt_get_cashier = $conn->query($get_cashier);
$cashiers = $stmt_get_cashier->fetchAll(PDO::FETCH_ASSOC);

// Adding cashier
if (isset($_POST['add-cashier'])) {
    $user_name = $_POST['user_name'];
    $user_password = $_POST['user_password'];
    $user_fname = $_POST['user_fname'];
    $user_mname = $_POST['user_mname'];
    $user_lname = $_POST['user_lname'];
    $user_email = $_POST['user_email'];
    $user_contact = $_POST['user_contact'];

    $query = "INSERT INTO users (user_name, user_password, user_type, user_fname, user_mname, user_lname, user_email, user_contact) 
              VALUES (?, ?, 'cashier', ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);

    if ($stmt) {
        $stmt->execute([$user_name, $user_password, $user_fname, $user_mname, $user_lname, $user_email, $user_contact]);
        if ($stmt->rowCount() > 0) {
            $_SESSION['success'] = 'Cashier added successfully!';
            header('location: cashier.php');
            exit;
        } else {
            $_SESSION['error'] = 'Error adding cashier.';
        }
    }
}

// Update cashier
if (isset($_POST['update-cashier'])) {
    $user_id = $_POST['update_user_id'];
    $user_name = $_POST['update_user_name'];
    $user_password = $_POST['update_user_password'];
    $user_fname = $_POST['update_user_fname'];
    $user_mname = $_POST['update_user_mname'];
    $user_lname = $_POST['update_user_lname'];
    $user_email = $_POST['update_user_email'];
    $user_contact = $_POST['update_user_contact'];

    $update_query = "UPDATE users SET user_name = ?, user_password = ?, user_fname = ?, user_mname = ?, user_lname = ?, user_email = ?, user_contact = ? 
                     WHERE user_id = ?";
    $stmt_update = $conn->prepare($update_query);
    $stmt_update->execute([$user_name, $user_password, $user_fname, $user_mname, $user_lname, $user_email, $user_contact, $user_id]);

    if ($stmt_update->rowCount() > 0) {
        $_SESSION['success'] = 'Cashier updated successfully!';
        header('location: cashier.php');
        exit;
    } else {
        $_SESSION['error'] = 'Error updating cashier.';
    }
}

// Delete cashier
if (isset($_POST['delete-cashier'])) {
    $user_id = $_POST['user_id_to_delete'];
    $delete_query = "DELETE FROM users WHERE user_id = ?";
    $stmt_delete = $conn->prepare($delete_query);
    $stmt_delete->execute([$user_id]);

    if ($stmt_delete->rowCount() > 0) {
        $_SESSION['success'] = 'Cashier deleted successfully!';
        header('location: cashier.php');
        exit;
    } else {
        $_SESSION['error'] = 'Error deleting cashier.';
    }
}

?>

<?php include 'header.php' ?>

<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Cashiers</h6>

            <a class="btn btn-secondary" data-toggle="modal" data-target="#addCashier"> Add New Cashier </a>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Username</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Contact</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cashiers as $cashier): ?>
                            <tr>
                                <td><?php echo $cashier['user_id']; ?></td>
                                <td><?php echo $cashier['user_name']; ?></td>
                                <td><?php echo $cashier['user_fname'] . ' ' . $cashier['user_lname']; ?></td>
                                <td><?php echo $cashier['user_email']; ?></td>
                                <td><?php echo $cashier['user_contact']; ?></td>
                                <td class="text-center">
                                    <a href="#" class="btn btn-primary edit" data-toggle="modal" data-target="#editCashier"
                                        data-id="<?php echo $cashier['user_id']; ?>"
                                        data-name="<?php echo $cashier['user_name']; ?>"
                                        data-fname="<?php echo $cashier['user_fname']; ?>"
                                        data-mname="<?php echo $cashier['user_mname']; ?>"
                                        data-lname="<?php echo $cashier['user_lname']; ?>"
                                        data-email="<?php echo $cashier['user_email']; ?>"
                                        data-contact="<?php echo $cashier['user_contact']; ?>">
                                        <i class="fas fa-edit"> Edit</i>
                                    </a>

                                    <a href="#" class="btn btn-danger delete" data-toggle="modal" data-target="#deleteCashier"
                                        data-id="<?php echo $cashier['user_id']; ?>">
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

<!-- Add Cashier Modal-->
<div class="modal fade" id="addCashier" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add New Cashier</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">

                <form id="AddCashierForm" class="user" action="" method="POST">
                    <div class="row">
                        <div class="col-xl-12 col-md-6 mb-4">
                            <div class="mb-3">
                                <label for="user_name">Username</label>
                                <input class="form-control" id="user_name" name="user_name" type="text" required>
                            </div>
                            <div class="mb-3">
                                <label for="user_password">Password</label>
                                <input class="form-control" id="user_password" name="user_password" type="text" required>
                            </div>
                            <div class="mb-3">
                                <label for="user_fname">First Name</label>
                                <input class="form-control" id="user_fname" name="user_fname" type="text" required>
                            </div>
                            <div class="mb-3">
                                <label for="user_mname">Middle Name</label>
                                <input class="form-control" id="user_mname" name="user_mname" type="text" required>
                            </div>
                            <div class="mb-3">
                                <label for="user_lname">Last Name</label>
                                <input class="form-control" id="user_lname" name="user_lname" type="text" required>
                            </div>
                            <div class="mb-3">
                                <label for="user_email">Email</label>
                                <input class="form-control" id="user_email" name="user_email" type="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="user_contact">Contact Number</label>
                                <input class="form-control" id="user_contact" name="user_contact" type="text" required>
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-primary" type="submit" name="add-cashier">Add Cashier</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Cashier Modal-->
<div class="modal fade" id="editCashier" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Update Cashier</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" method="POST">
                    <input type="hidden" id="update_user_id" name="update_user_id">
                    <div class="mb-3">
                        <label for="update_user_name">Username</label>
                        <input class="form-control" id="update_user_name" name="update_user_name" type="text" required>
                    </div>
                    <div class="mb-3">
                        <label for="update_user_password">Password</label>
                        <input class="form-control" id="update_user_password" name="update_user_password" type="text" required>
                    </div>
                    <div class="mb-3">
                        <label for="update_user_fname">First Name</label>
                        <input class="form-control" id="update_user_fname" name="update_user_fname" type="text" required>
                    </div>
                    <div class="mb-3">
                        <label for="update_user_mname">Middle Name</label>
                        <input class="form-control" id="update_user_mname" name="update_user_mname" type="text" required>
                    </div>
                    <div class="mb-3">
                        <label for="update_user_lname">Last Name</label>
                        <input class="form-control" id="update_user_lname" name="update_user_lname" type="text" required>
                    </div>
                    <div class="mb-3">
                        <label for="update_user_email">Email</label>
                        <input class="form-control" id="update_user_email" name="update_user_email" type="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="update_user_contact">Contact Number</label>
                        <input class="form-control" id="update_user_contact" name="update_user_contact" type="text" required>
                    </div>
                    <button class="btn btn-primary" type="submit" name="update-cashier">Update Cashier</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Cashier Modal-->
<div class="modal fade" id="deleteCashier" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Delete Cashier</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" method="POST">
                    <input type="hidden" id="user_id_to_delete" name="user_id_to_delete">
                    <p>Are you sure you want to delete this cashier?</p>
                    <button class="btn btn-danger" type="submit" name="delete-cashier">Yes, Delete</button>
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php' ?>

<script>
    // Populate edit modal with cashier data
    $('#editCashier').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget); // Button that triggered the modal
        var user_id = button.data('id');
        var user_name = button.data('name');
        var user_fname = button.data('fname');
        var user_mname = button.data('mname');
        var user_lname = button.data('lname');
        var user_email = button.data('email');
        var user_contact = button.data('contact');

        // Fill in the modal fields with the data
        var modal = $(this);
        modal.find('#update_user_id').val(user_id);
        modal.find('#update_user_name').val(user_name);
        modal.find('#update_user_fname').val(user_fname);
        modal.find('#update_user_mname').val(user_mname);
        modal.find('#update_user_lname').val(user_lname);
        modal.find('#update_user_email').val(user_email);
        modal.find('#update_user_contact').val(user_contact);
    });

    // Populate delete modal with cashier ID
    $('#deleteCashier').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget); // Button that triggered the modal
        var user_id = button.data('id'); // Get the user ID

        // Set the hidden input field with the user ID
        var modal = $(this);
        modal.find('#user_id_to_delete').val(user_id);
    });
</script>