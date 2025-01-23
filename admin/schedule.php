<?php include 'header.php' ?>

<!-- Begin Page Content -->
<div class="container-fluid">


    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">All Schedules</h6>
            <a class="btn btn-secondary" data-toggle="modal" data-target="#addSchedule"> Add New Schedule </a>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Schedule Time</th>
                            <th>Ship</th>
                            <th>Route</th>
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
<div class="modal fade" id="addSchedule" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add New Schedule</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">

                <form id="AddScheduleForm" class="user" method="POST">
                    <div class="row">
                        <div class="col-xl-12 col-md-6 mb-4">


                            <div class="form-group row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="exampleFormControlInput1">Schedule Time</label>
                                        <input required class="form-control form-control-solid" type="time" id="schedule_time" name="schedule_time">
                                    </div>

                                </div>

                            </div>


                            <div class="form-group row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="exampleFormControlInput1">Ship Name</label>
                                        <select class="form-control form-control-solid" id="ship_id" name="ship_id">

                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="exampleFormControlInput1">Route</label>
                                        <select class="form-control form-control-solid" id="route_id" name="route_id">

                                        </select>
                                    </div>
                                </div>
                            </div>


                            <div class="form-group row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <table class="table table-bordered border-hovered border-stripped" id="accom_list">
                                            <colgroup>
                                                <col width="60%">
                                                <col width="15%">
                                                <col width="15%">
                                                <col width="10%">
                                            </colgroup>
                                            <thead>
                                                <tr>
                                                    <th class="py-1 px-2 text-center">Accomodation</th>
                                                    <th class="py-1 px-2 text-center">Net Fare</th>
                                                    <th class="py-1 px-2 text-center">Max</th>
                                                </tr>
                                            </thead>
                                            <tbody>


                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>


                    <button class="btn btn-primary" type="submit">Add Schedule</button>
            </div>

            </form>
        </div>
    </div>
</div>







<!-- Add Modal-->
<div class="modal fade" id="editSchedule" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add New Schedule</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="UpdateScheduleForm" class="user" method="POST">
                    <input type="hidden" name="update_schedule_id" id="update_schedule_id">
                    <div class="row">
                        <!-- Left-->
                        <div class="col-xl-12 col-md-6 mb-4">


                            <div class="form-group row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="exampleFormControlInput1">Schedule Time</label>
                                        <input required class="form-control form-control-solid" type="time" id="update_schedule_time" name="update_schedule_time">
                                    </div>

                                </div>

                            </div>


                            <div class="form-group row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="exampleFormControlInput1">Ship Name</label>
                                        <select class="form-control form-control-solid" id="update_ship_id" name="update_ship_id">

                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="exampleFormControlInput1">Route</label>
                                        <select class="form-control form-control-solid" id="update_route_id" name="update_route_id">

                                        </select>
                                    </div>
                                </div>
                            </div>


                            <div class="form-group row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <table class="table table-bordered border-hovered border-stripped" id="update_accom_list">
                                            <colgroup>
                                                <col width="60%">
                                                <col width="15%">
                                                <col width="15%">
                                                <col width="10%">
                                            </colgroup>
                                            <thead>
                                                <tr>
                                                    <th class="py-1 px-2 text-center">Accomodation</th>
                                                    <th class="py-1 px-2 text-center">Net Fare</th>
                                                    <th class="py-1 px-2 text-center">Max</th>
                                                </tr>
                                            </thead>
                                            <tbody>


                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>


                    <button class="btn btn-warning" type="submit">Upadte Schedule</button>
                    <button class="btn btn-delete" type="button" data-dismiss="modal">Cancel</button>
            </div>

            </form>
        </div>
    </div>
</div>






<script src="assets/admin/vendor/jquery/jquery.min.js"></script>

<?php include 'footer.php' ?>