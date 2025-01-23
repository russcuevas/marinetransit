<?php include 'header.php' ?>

<!-- Begin Page Content -->
<div class="container-fluid">



    <div class="card card-outline card-primary">
        <div class="card-header">
            <h5 class="card-title">System Information</h5>
        </div>
        <div class="card-body">
            <form id="systeminfo" name="systeminfo" enctype="multipart/form-data">
                <div id="msg" class="form-group"></div>
                <div class="form-group">
                    <label for="name" class="control-label">System Name</label>
                    <input type="text" class="form-control " name="systeminfo_name" id="systeminfo_name">

                </div>
                <div class="form-group">
                    <label for="short_name" class="control-label">System Short Name</label>
                    <input type="text" class="form-control " name="systeminfo_shortname" id="systeminfo_shortname">
                </div>

                <div class="form-group">
                    <label for="" class="control-label">System Logo</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input rounded-circle" id="systeminfo_icon" name="systeminfo_icon">
                        <label class="custom-file-label" for="systeminfo_icon">Choose file</label>
                    </div>
                </div>
                <div class="form-group d-flex justify-content-center">

                </div>
                <div class="form-group d-flex justify-content-center">
                    <img src="" alt="" id="cimg2" class="img-fluid img-thumbnail">
                </div>
        </div>
        <div class="card-footer">
            <div class="col-md-12">
                <div class="row">
                    <button class="btn btn-sm btn-primary" type="submit">Update</button>
                </div>
            </div>
        </div>

        </form>
    </div>



</div>
<!-- /.container-fluid -->



<script src="assets/admin/vendor/jquery/jquery.min.js"></script>


<?php include 'footer.php' ?>