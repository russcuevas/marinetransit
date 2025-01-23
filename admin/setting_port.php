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

                <form id="AddPortForm" class="user" method="POST">
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


                    <button class="btn btn-primary" type="submit">Add Port</button>
            </div>

            </form>
        </div>
    </div>
</div>



<!-- Update Modal-->
<div class="modal fade" id="editPort" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
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
                            <input id="port_id" name="port_id" type="hidden">

                            <div class="mb-3">
                                <label for="exampleFormControlInput1">Port Name</label>
                                <input class="form-control" id="update_port_name" name="update_port_name" type="text">
                            </div>

                            <div class="mb-3">
                                <label for="exampleFormControlInput1">Port Address</label>
                                <input class="form-control" id="update_port_location" name="update_port_location" type="text">
                            </div>

                        </div>
                    </div>


                    <button class="btn btn-primary" type="submit">Update Port</button>
                    <button class="btn btn-danger" type="button" data-dismiss="modal">Cancel</button>
            </div>

            </form>
        </div>
    </div>
</div>



<script src="assets/admin/vendor/jquery/jquery.min.js"></script>

<?php include 'footer.php' ?>