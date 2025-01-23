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


                    </tbody>
                </table>
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


                    <button class="btn btn-primary" type="submit">Add Accomodation</button>
            </div>

            </form>
        </div>
    </div>
</div>



<!-- Update Modal-->
<div class="modal fade" id="editAccomodation" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
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

                <form id="UpdateAccomodationForm" class="user" method="POST">
                    <input type="hidden" name="accomodation_id" id="update_accomodation_id">
                    <div class="row">
                        <!-- Left-->
                        <div class="col-xl-12 col-md-6">

                            <div class="mb-3">
                                <label for="exampleFormControlInput1">Accomodation Name</label>
                                <input class="form-control" id="update_accomodation_name" name="update_accomodation_name" type="text">
                            </div>

                        </div>

                        <div class="col-xl-12 col-md-6 mb-5">

                            <div class="mb-3">
                                <label for="exampleFormControlInput1">Accomodation Type</label>
                                <select class="form-control" name="update_accomodation_type" id="update_accomodation_type">
                                    <option value="passenger"> Passenger </option>
                                    <option value="cargo"> Cargo </option>
                                </select>
                            </div>

                        </div>
                    </div>


                    <button class="btn btn-warning" type="submit">Update Accomodation</button>
            </div>

            </form>
        </div>
    </div>
</div>

<script src="assets/admin/vendor/jquery/jquery.min.js"></script>

<?php include 'footer.php' ?>