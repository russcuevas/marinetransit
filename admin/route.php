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

                    </tbody>
                </table>
            </div>
        </div>
    </div>



</div>




<!-- Add Modal-->
<div class="modal fade" id="addRoute" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
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

                                <label for="exampleFormControlInput1">From</label>
                                <select class="form-control form-control-solid" id="route_from" name="route_from">

                                </select>
                            </div>


                            <div class="mb-3">

                                <label for="exampleFormControlInput1">To</label>
                                <select class="form-control form-control-solid" id="route_to" name="route_to">

                                </select>
                            </div>


                        </div>
                    </div>


                    <button class="btn btn-primary" type="submit">Add Route</button>
            </div>

            </form>
        </div>
    </div>
</div>




<!-- Update Modal-->
<div class="modal fade" id="editRoute" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
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

                                <label for="exampleFormControlInput1">From</label>
                                <select class="form-control form-control-solid" id="update_route_from" name="update_route_from">

                                </select>
                            </div>


                            <div class="mb-3">

                                <label for="exampleFormControlInput1">To</label>
                                <select class="form-control form-control-solid" id="update_route_to" name="update_route_to">

                                </select>
                            </div>


                        </div>
                    </div>


                    <button class="btn btn-primary" type="submit">Update Route</button>
            </div>

            </form>
        </div>
    </div>
</div>

<script src="assets/admin/vendor/jquery/jquery.min.js"></script>

<?php include 'footer.php' ?>