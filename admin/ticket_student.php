<?php
include '../connection/database.php';
session_start();

$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
    header('location:admin_login.php');
}
?>
<?php include 'header.php' ?>

<!-- Begin Page Content -->
<div class="container-fluid">


    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Student Tickets</h6>
            <a class="btn btn-secondary" data-toggle="modal" data-target="#addTicket"> Add New Ticket </a>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <!-- <th>Ticket No.</th> -->
                            <th>Schedule Date/Time</th>
                            <th>Name</th>
                            <th>Ship</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Total Fare</th>
                            <th>Status</th>
                            <th>Action</th>

                        </tr>
                    </thead>
                    <tbody>


                    </tbody>
                </table>
            </div>
        </div>
    </div>



</div>
<!-- /.container-fluid -->



<!-- Add Modal-->
<div class="modal fade" id="addTicket" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add New Ticket</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">

                <form id="AddTicketForm" class="user" method="POST">
                    <input type="hidden" name="ticket_type" value="Regular">
                    <input type="hidden" name="passenger_type" value="Student">
                    <div class="row">
                        <!-- Left-->
                        <div class="col-xl-12 col-md-6 mb-4">


                            <div class="form-group row">


                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="exampleFormControlInput1">From</label>
                                        <select class="form-control form-control-solid" id="route_from" name="route_from">
                                            <?php foreach ($ports as $key => $value) : ?>
                                                <option value="<?= $value->port_id; ?>"><?= $value->port_name; ?></option>
                                            <?php endforeach; ?>
                                        </select>

                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="exampleFormControlInput1">To</label>
                                        <select class="form-control form-control-solid" id="route_to" name="route_to" required>
                                        </select>

                                    </div>
                                </div>

                            </div>


                            <div class="form-group row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="exampleFormControlInput1">Ticket Date</label>
                                        <input required class="form-control form-control-solid" type="date" id="ticket_date" name="ticket_date" onfocus="this.setAttribute('min', new Date().toISOString().split('T')[0])">
                                    </div>
                                </div>


                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="exampleFormControlInput1">Schedule</label>
                                        <select class="form-control form-control-solid" id="schedule_id" name="schedule_id">

                                        </select>
                                    </div>
                                </div>

                            </div>

                            <h4>Passenger/s Details</h4>
                            <hr class="border-light">
                            <div class="mb-2 border-bottom p-item">

                                <div class="form-group row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="" class="control-laberl">First Name</label>
                                            <input type="text" class="form-control form-control-sm rounded-0" name="passenger_fname[]" required>
                                        </div>
                                    </div>


                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="" class="control-laberl">Middle Name</label>
                                            <input type="text" class="form-control form-control-sm rounded-0" name="passenger_mname[]">
                                        </div>
                                    </div>


                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="" class="control-laberl">Last Name</label>
                                            <input type="text" class="form-control form-control-sm rounded-0" name="passenger_lname[]" required>
                                        </div>
                                    </div>

                                </div>

                                <div class="form-group row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="" class="control-laberl">Birthdate</label>
                                            <input type="date" class="form-control form-control-sm rounded-0" name="passenger_bdate[]" required>
                                        </div>
                                    </div>


                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="" class="control-laberl">Contact</label>
                                            <input type="text" class="form-control form-control-sm rounded-0" name="passenger_contact[]">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="" class="control-laberl">Gender</label>
                                            <select class="form-control form-control-solid" id="passenger_gender" name="passenger_gender[]">
                                                <option value="Male">Male</option>
                                                <option value="Female">Female</option>
                                            </select>
                                        </div>
                                    </div>

                                </div>

                                <div class="form-group row">
                                    <div class="col-md-8">
                                        <div class="form-group">

                                            <label for="" class="control-laberl">Address</label>
                                            <textarea rows="3" class="form-control form-control-sm rounded-0" name="passenger_address[]" required></textarea>
                                        </div>
                                    </div>


                                    <div class="col-md-4 mt-5">
                                        <div class="form-group">
                                            <button class="btn btn-danger btn-sm btn-flat rem_item" type="button" onclick="rem_item($(this))"><i class="fa fa-trash"></i> Remove</button>
                                        </div>
                                    </div>

                                </div>

                            </div>
                            <div class="w-100 d-flex justify-content-center py-1">
                                <button class="btn btn-primary btn-sm btn-flat" type="button" id="add_passenger">Add Passenger</button>
                            </div>


                        </div>
                    </div>


                    <button class="btn btn-primary" type="submit">Add Ticket</button>
            </div>

            </form>
        </div>
    </div>
</div>


<!-- View Passenger Modal-->
<div class="modal fade" id="viewPassenger" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">List of Passenger</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">

                <form id="AddPassengerForm" class="user" method="POST">
                    <div class="row">
                        <!-- Left-->
                        <div class="col-xl-12 col-md-6">


                            <div class="form-group row">
                                <div class="col-md-12">

                                    <div class="d-flex justify-content-between">
                                        <h6 class="m-0 font-weight-bold text-primary"></h6>
                                        <a class="btn btn-primary" id="print"><i class="fas fa-print"></i> </a>
                                    </div>
                                </div>

                            </div>

                            <div class="form-group row">

                                <div class="col-md-12">
                                    <div class="mb-3" id="printTicket">





                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>
            </div>

            </form>
        </div>
    </div>
</div>
<script src="assets/admin/vendor/jquery/jquery.min.js"></script>

<?php include 'footer.php' ?>