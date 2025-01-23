<?php include 'header.php' ?>

<!-- Begin Page Content -->
<div class="container-fluid">


    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class=" p-3  d-flex justify-content-between">

            <div class=" d-flex justify-content-start">


                <div class="mr-3">
                    <label for="reportByLevel">From </label>

                    <input value="" class="form-control mr-3" type="date" name="dateFrom" id="dateFrom">
                </div>

                <div class="mr-3">
                    <label for="reportByLevel">To</label>

                    <input value="" class="form-control" type="date" name="dateTo" id="dateTo">
                </div>
                <div class=" mr-3">
                    <button class="btn btn-primary filterby" style="margin-top: 32px">Filter</button>

                </div>
            </div>



            <i class="fa fa-print text-secondary button" style="font-size: 35px!important" onclick="print_element('printables')"></i>

        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Ticket No.</th>
                            <th>Name</th>
                            <th>Schedule Date/Time</th>
                            <th>Ship</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Status</th>
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




<!-- Begin Page Content -->
<div class="container-fluid" id="printables" style="display: none;">

    <!-- Page Heading -->
    <div class="d-sm-flex justify-content-start flex-column mb-4">

        <div class="mb-4" style="display: flex; flex-direction: row;">

        </div>


        <h1 class="h3 mb-0 text-gray-800">Passenger Report</h1><br />
        <p class="h3">Date Covered: <?= (date('F d, Y', strtotime($dateFrom))) . ' - ' . (date('F d, Y', strtotime($dateTo))) ?></p>

    </div>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
        </div>
        <div class="card-body">


            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable12" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Ticket No.</th>
                            <th>Contact Person</th>
                            <th>Schedule Date/Time</th>
                            <th>Ship</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>



                    </tbody>
                </table>
            </div>


        </div>
    </div>

</div>


<script src="assets/admin/vendor/jquery/jquery.min.js"></script>

<?php include 'footer.php' ?>