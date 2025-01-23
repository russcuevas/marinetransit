<?php include 'header.php' ?>

<!-- Top content -->
<div class="top-content" style="padding-bottom: 10px">

    <div class="col-sm-12 " style="display: flex; justify-content: start; margin-top: 30px; color: white;">
        <img class="img-profile" src='assets/user/img/backgrounds/icon.jpg' style="height: 70px; width: auto; border-radius: 100%;">
        <h4 style="font-family: 'Recursive', 'Times New Roman', Times, serif; color: white; margin-left: 10px; font-size: 30px; font-weight: bold"><i>Marinetransport</i></h4>
    </div>

    <div class="col-sm-12 " style="display: flex; justify-content: start; margin-top: 0px; color: white; align-items: center;">
        <img class="img-profile" src='assets/user/img/barko.webp' style="height: 50px; width: auto; border-radius: 100%;">
        <div style="display: flex; flex-direction: column; justify-content: start; text-align: left;">
            <u>

                <h4 style="font-family: 'Recursive', 'Times New Roman', Times, serif; color: white; margin-left: 10px; font-size: 30px; font-weight: bold"><i>Sail with Us</i></h4>
                <h4 style="font-family: 'Recursive', 'Times New Roman', Times, serif; color: white; margin-left: 10px; font-size: 30px; font-weight: bold"><i>02</i></h4>
            </u>
        </div>
    </div>


    <div class="container">


        <div class="row">
            <div class="col-sm-12" style="color: white!important; text-align: left;">
                <h1 style="color: white!important"><strong><u><i>MarineTransit: Balingoan Port </i></u></strong></h1>
                <h3 style="color: white!important"><strong><i>Explore Beyond the Waves</i></strong>
                    <img class="img-profile" src='assets/user/img/wave2.png' style="height: 30px; width: 50px; width: auto; color: blue; margin-left: 10px">
                </h3>
                <div style="border-left: 3px white solid; padding-left: 10px;">

                    <h3 style="color: white!important"><strong>Don’t Miss Out! Book Your Balingoan Port Ticket by October 30, 2024</strong></h3>

                    <h3 style="color: white!important"><strong>Sail from October 30 to December 25, 2024, with Balingoan Port Marine Transit!</strong></h3>

                </div>
            </div>
        </div>


        <div class="container">

            <form id="AddSchedule" class="user" method="POST">
                <input type="hidden" name="ticket_type" id="ticket_type" value="passenger">


                <div style="background: linear-gradient(to right, #8c52ff, #00bf63); padding: 50px 10px; display: flex; flex-direction: column; align-content: space-between; text-align: start; gap: 40px;">


                    <div class="col-lg-12" style="background: linear-gradient(to right, #8c52ff, #00bf63); padding: 10px; display: flex; flex-direction: row; align-items: start; text-align: start; justify-content: center;">

                        <div class="col-sm-4" style="display: flex; flex-direction: column; gap: 10px;">

                            <button class="btn btn-info" id="btn1" type="button"><i class="fa fa-user"></i> Passenger</button>

                            <button class="btn btn-info" id="btn2" type="button"><i class="fa fa-car"></i> Car</button>
                            <div id="radiobutton" style="display: none; flex-direction: row; gap: 10px">
                                <label style="color: white;display: flex; align-items: center;">
                                    <input type="radio" name="radiochoice" value="oneway" checked style="width: 25px;height: 25px; margin: 0px; margin-right: 5px;"> One Way
                                </label>

                                <label style="color: white;display: flex; align-items: center;">
                                    <input type="radio" name="radiochoice" value="roundtrip" style="width: 25px;height: 25px; margin: 0px; margin-right: 5px;"> Round Trip
                                </label>
                            </div>


                        </div>

                        <div class="col-sm-4" style="display: flex; flex-direction: column; gap: 10px;">
                            <div class="input-group">
                                <div class="input-group-addon">From</div>
                                <select class="form-control" name="route_from" id="route_from" required>
                                    <option value=""></option>
                                    <?php foreach ($ports as $key => $value) : ?>
                                        <option value="<?= $value->port_id; ?>"><?= $value->port_name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="input-group">
                                <div class="input-group-addon">To &nbsp;&nbsp;&nbsp;&nbsp;</div>
                                <select class="form-control" name="route_to" id="route_to" required>

                                </select>
                            </div>

                        </div>

                        <div class="col-sm-4" style="display: flex; flex-direction: column; gap: 10px;">


                            <div class="input-group">
                                <div class="input-group-addon">Depart</div>
                                <input class="form-control" required type="date" name="schedule_date" id="schedule_date" min="<?= date('Y-m-d') ?>">

                            </div>


                            <div class="input-group" style="display: none;">
                                <div class="input-group-addon">Return</div>
                                <input class="form-control schedule_date_return" type="date" name="schedule_date_return" id="schedule_date_return" min="<?= date('Y-m-d') ?>">

                            </div>

                            <div id="section1">
                                <div class="input-group" style="margin-bottom: 10px">
                                    <div class="input-group-addon">No. of Passenger</div>
                                    <input class="form-control" type="number" name="passenger_no" id="passenger_no" min="1">

                                </div>
                                <button style="float: right;" class="btn btn-info" id="btn2" type="submit"><i class="fa fa-search"></i> Search Trips</button>



                            </div>



                            <div class="col-sm-12" id="section1" style="display: flex; flex-direction: row;">
                                <div class="col-sm-4" style="display: flex; flex-direction: row; gap: 10px;">

                                </div>


                                <div class="col-sm-4" style="display: flex; flex-direction: column; gap: 10px;">

                                </div>

                            </div>


                        </div>


                    </div>







                    <div class="col-sm-12" id="section2" style="border: 1px solid black; border: 1px solid black; border-radius: 5px; padding: 10px;">

                        <h4 style="color: white;">Car Information</h4>

                        <div class="row" style="margin-bottom: 20px">

                            <div class="col-sm-4" style="display: flex; flex-direction: column; gap: 10px;">
                                <div class="input-group">
                                    <div class="input-group-addon">Category</div>

                                    <select class="form-control" id="cargo_id" name="cargo_id" required>
                                        <?php foreach ($cargos as $key => $value) : ?>
                                            <option value="<?= $value->accomodation_id; ?>"><?= $value->accomodation_name; ?></option>
                                        <?php endforeach; ?>
                                    </select>

                                </div>
                            </div>

                            <div class="col-sm-4" style="display: flex; flex-direction: column; gap: 10px;">
                                <div class="input-group">
                                    <div class="input-group-addon">Brand/Model</div>
                                    <input class="form-control" type="text" name="passenger_cargo_brand" id="passenger_cargo_brand">
                                </div>
                            </div>
                            <div class="col-sm-4" style="display: flex; flex-direction: column; gap: 10px; margin-bottom: 10px;">
                                <div class="input-group">
                                    <div class="input-group-addon">Plate No.</div>
                                    <input class="form-control" type="text" name="passenger_cargo_plate" id="passenger_cargo_plate">
                                </div>
                            </div>





                            <div class="col-sm-4" style="display: flex; flex-direction: column; gap: 10px;">
                                <div class="input-group">
                                    <div class="input-group-addon">No. of Passenger</div>
                                    <input class="form-control" type="number" name="passenger_no_cargo" id="passenger_no_cargo" min="1">
                                </div>
                                <p class="m-0" style="color: white;">(Including Driver)</p>
                            </div>



                            <div class="col-sm-4" style="display: flex; flex-direction: column; gap: 10px;">

                                <button class="btn btn-info" id="btn2" type="submit"><i class="fa fa-search"></i> Cargo Trips</button>
                            </div>

                        </div>




                        <div class="row">



                        </div>



                    </div>



                </div>


            </form>



        </div>
    </div>







    <script src="assets/admin/vendor/jquery/jquery.min.js"></script>

    <script src="assets/user/js/jquery-1.11.1.min.js"></script>

    <?php include 'footer.php' ?>