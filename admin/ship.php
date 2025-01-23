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

                    </tbody>
                </table>
            </div>
        </div>
    </div>



</div>
<!-- /.container-fluid -->



<!-- Add Modal-->
<div class="modal fade" id="addShip" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
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
                                <label for="exampleFormControlInput1">Ship Code</label>
                                <input class="form-control form-control-solid" type="text" id="ship_code" name="ship_code">
                            </div>

                            <div class="mb-3">
                                <label for="exampleFormControlInput1">Ship Name</label>
                                <input class="form-control form-control-solid" type="text" id="ship_name" name="ship_name">
                            </div>

                            <div class="mb-3">

                                <label for="exampleFormControlInput1">Status</label>
                                <select class="form-control form-control-solid" id="ship_status" name="ship_status">
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>

                                </select>
                            </div>


                        </div>
                    </div>
                    <button class="btn btn-primary" type="submit">Add Ship</button>
            </div>

            </form>
        </div>
    </div>
</div>





<!-- Update Modal-->
<div class="modal fade" id="editShip" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add New Ship</h5>
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
                                <label for="exampleFormControlInput1">Ship Code</label>
                                <input class="form-control form-control-solid" type="text" id="update_ship_code" name="update_ship_code">
                            </div>


                            <div class="mb-3">
                                <label for="exampleFormControlInput1">Ship Name</label>
                                <input class="form-control form-control-solid" type="text" id="update_ship_name" name="update_ship_name">
                            </div>


                            <div class="mb-3">

                                <label for="exampleFormControlInput1">Status</label>
                                <select class="form-control form-control-solid" id="update_ship_status" name="update_ship_status">
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>

                                </select>
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-success" type="submit">Update Ship</button>
            </div>
            </form>
        </div>
    </div>
</div>











<script src="assets/admin/vendor/jquery/jquery.min.js"></script>

<?php include 'footer.php' ?>