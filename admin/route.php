<?php
include '../connection/database.php';
session_start();

$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
    header('location:admin_login.php');
    exit;
}

// crud

//get ports
$query = "SELECT * FROM ports";
$stmt = $conn->query($query);
$ports = $stmt->fetchAll(PDO::FETCH_ASSOC);

// existing routes
$route_query = "SELECT r.route_id, r.route_from, r.route_to, 
                       p_from.port_name AS from_port, p_to.port_name AS to_port
                FROM routes r
                JOIN ports p_from ON r.route_from = p_from.port_id
                JOIN ports p_to ON r.route_to = p_to.port_id";
$route_stmt = $conn->query($route_query);
$routes = $route_stmt->fetchAll(PDO::FETCH_ASSOC);

// add route
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add-route'])) {
    $from_port = $_POST['route_from'];
    $to_port = $_POST['route_to'];

    $insert_query = "INSERT INTO routes (route_from, route_to) VALUES (?, ?)";
    $stmt = $conn->prepare($insert_query);
    if ($stmt->execute([$from_port, $to_port])) {
        $_SESSION['success'] = 'Route added successfully!';
        header('Location: route.php');
        exit;
    } else {
        $_SESSION['error'] = 'Error adding route.';
    }
}

// update route
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update-route'])) {
    $route_id = $_POST['update_route_id'];
    $from_port = $_POST['update_route_from'];
    $to_port = $_POST['update_route_to'];

    $update_query = "UPDATE routes SET route_from = ?, route_to = ? WHERE route_id = ?";
    $stmt = $conn->prepare($update_query);
    if ($stmt->execute([$from_port, $to_port, $route_id])) {
        $_SESSION['success'] = 'Route updated successfully!';
        header('Location: route.php');
        exit;
    } else {
        $_SESSION['error'] = 'Error updating route.';
    }
}
?>

<?php include 'header.php' ?>

<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">All Routes</h6>
            <a class="btn btn-secondary" data-toggle="modal" data-target="#addRoute"> Add New Route </a>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($routes as $route): ?>
                            <tr>
                                <td><?php echo $route['route_id']; ?></td>
                                <td><?php echo $route['from_port']; ?></td>
                                <td><?php echo $route['to_port']; ?></td>
                                <td class="text-center">
                                    <a href="#" class="btn btn-primary edit" data-toggle="modal" data-target="#editRoute"
                                        data-id="<?php echo $route['route_id']; ?>"
                                        data-from="<?php echo $route['route_from']; ?>"
                                        data-to="<?php echo $route['route_to']; ?>">
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

<!-- Add Route Modal -->
<div class="modal fade" id="addRoute" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add New Route</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="AddRouteForm" class="user" method="POST">
                    <div class="row">
                        <div class="col-xl-12 col-md-6 mb-4">
                            <div class="mb-3">
                                <label for="route_from">From</label>
                                <select class="form-control form-control-solid" id="route_from" name="route_from">
                                    <?php foreach ($ports as $port): ?>
                                        <option value="<?php echo $port['port_id']; ?>"><?php echo $port['port_name']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="route_to">To</label>
                                <select class="form-control form-control-solid" id="route_to" name="route_to">
                                    <?php foreach ($ports as $port): ?>
                                        <option value="<?php echo $port['port_id']; ?>"><?php echo $port['port_name']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <button class="btn btn-primary" type="submit" name="add-route">Add Route</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Update Route Modal -->
<div class="modal fade" id="editRoute" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Update Route</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="UpdateRouteForm" class="user" method="POST">
                    <input type="hidden" id="update_route_id" name="update_route_id">
                    <div class="row">
                        <div class="col-xl-12 col-md-6 mb-4">
                            <div class="mb-3">
                                <label for="update_route_from">From</label>
                                <select class="form-control form-control-solid" id="update_route_from" name="update_route_from">
                                    <?php foreach ($ports as $port): ?>
                                        <option value="<?php echo $port['port_id']; ?>"><?php echo $port['port_name']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="update_route_to">To</label>
                                <select class="form-control form-control-solid" id="update_route_to" name="update_route_to">
                                    <?php foreach ($ports as $port): ?>
                                        <option value="<?php echo $port['port_id']; ?>"><?php echo $port['port_name']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <button class="btn btn-primary" type="submit" name="update-route">Update Route</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="assets/admin/vendor/jquery/jquery.min.js"></script>

<?php include 'footer.php' ?>

<script>
    $('#editRoute').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var routeId = button.data('id');
        var fromPortId = button.data('from'); // Port ID for "From"
        var toPortId = button.data('to'); // Port ID for "To"

        console.log('Route ID:', routeId);
        console.log('From Port ID:', fromPortId);
        console.log('To Port ID:', toPortId);

        var modal = $(this);
        modal.find('#update_route_id').val(routeId);

        // Set the correct "From" port based on the port_id
        modal.find('#update_route_from').val(fromPortId);

        // Set the correct "To" port based on the port_id
        modal.find('#update_route_to').val(toPortId);
    });
</script>