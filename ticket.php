<?php include 'header.php' ?>


<!-- Top content -->
<div class="top-content" style="padding-bottom: 50px!important;">
    <div class="container">
        <div class="row">
            <div class="col-sm-8 col-sm-offset-2 form-box">
                <form role="form" id="AddTicketForm" method="post" class="f1" style="width: 700px!important">

                    <input type="hidden" name="net_fare" id="net_fare">
                    <input type="hidden" name="return_net_fare" id="return_net_fare" value="0">

                    <div class="f1-steps">
                        <div class="f1-step active">
                            <div class="f1-step-icon"><i class="fa fa-ship"></i></div>
                            <p>Departure</p>
                        </div>
                        <div class="f1-step">
                            <div class="f1-step-icon"><i class="fa fa-users"></i></div>
                            <p>Traveler Details</p>
                        </div>
                        <div class="f1-step">
                            <div class="f1-step-icon"><i class="fa fa-flag"></i></div>
                            <p>Finish</p>
                        </div>
                    </div>

                    <fieldset>
                        <h3>Manila -----> Cebu</h3>
                        <div class="form-group">
                            <label for="schedule_date"><strong>Departure Date</strong></label>
                            <input type="date" name="schedule_date" value="2025-02-01" class="f1-first-name form-control" id="schedule_date" min="2025-01-23">
                        </div>

                        <div style="margin: 20px 0; padding: 10px; border: 1px black solid;" class="dynamicDiv">
                            <div class="card">
                                <h2 class="card-header">10:00 AM</h2>
                                <div class="card-body">
                                    <div class="col-md-12" style="display: flex; flex-direction: row; align-items: center;">
                                        <div class="col-lg-6" style="display: flex; flex-direction: column;">
                                            <select class="form-control">
                                                <option>Economy</option>
                                            </select>
                                        </div>

                                        <div class="col-lg-6" style="display: flex; flex-direction: column;">
                                            <span class="price-value" style="margin-right: 20px;">₱ 1000</span>
                                            <span style="font-weight: 800; color: black;">Ticket Price</span>
                                        </div>

                                        <div class="col-lg-12" style="display: flex; flex-direction: row;">
                                            <img class="img-profile" src="assets/user/img/ssr.jpeg" style="height: 40px; width: 40px; border-radius: 100%; margin-right: 20px;">
                                            <div class="col-lg-12" style="border: 1px black solid;">
                                                <span style="font-weight: 800; color: #FF8C00;">MV Super Ferry</span>
                                            </div>
                                        </div>
                                    </div>
                                    <a href="#" class="btn btn-primary">Selected</a>
                                </div>
                            </div>
                        </div>


                        <div class="f1-buttons">
                            <button type="button" class="btn btn-next">Next</button>
                        </div>
                    </fieldset>

                    <fieldset>
                        <h4>Contact Information</h4>
                        <div class="form-group">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="contact_person">Contact Person</label>
                                    <input type="text" class="form-control form-control-sm" name="contact_person" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="contact_number">Mobile Number</label>
                                    <input type="text" class="form-control form-control-sm" name="contact_number" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="contact_email">Email Address</label>
                                    <input type="email" class="form-control form-control-sm" name="contact_email" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="contact_email_confirm">Confirm Email Address</label>
                                    <input type="email" class="form-control form-control-sm" name="contact_email_confirm">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-12">
                                <label for="contact_address">Address</label>
                                <textarea rows="3" class="form-control form-control-sm" name="contact_address" required></textarea>
                            </div>
                        </div>

                        <h4>Passenger/s Details</h4>
                        <div class="form-group row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="passenger_fname">First Name</label>
                                    <input type="text" class="form-control form-control-sm" name="passenger_fname[]" required>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="passenger_mname">Middle Name</label>
                                    <input type="text" class="form-control form-control-sm" name="passenger_mname[]">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="passenger_lname">Last Name</label>
                                    <input type="text" class="form-control form-control-sm" name="passenger_lname[]" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="passenger_bdate">Birthdate</label>
                                    <input type="date" class="form-control form-control-sm" name="passenger_bdate[]" required>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="passenger_contact">Contact</label>
                                    <input type="text" class="form-control form-control-sm" name="passenger_contact[]">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="passenger_gender">Gender</label>
                                    <select class="form-control form-control-solid" name="passenger_gender[]">
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="passenger_address">Address</label>
                                    <textarea rows="3" class="form-control form-control-sm" name="passenger_address[]" required></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="w-100 d-flex justify-content-center py-1">
                            <button class="btn btn-primary btn-sm btn-flat" type="button">Add Passenger</button>
                        </div>

                        <div class="f1-buttons">
                            <button type="button" class="btn btn-previous">Previous</button>
                            <button type="button" class="btn btn-next">Next</button>
                        </div>
                    </fieldset>

                    <fieldset>
                        <h4>Ticket Information: </h4>
                        <h5>Manila -----> Cebu</h5>
                        <h5>Departure Date : 2025-02-01</h5>
                        <h5>Passenger: 1</h5>

                        <h3>Total: ₱ 2200</h3>

                        <div class="f1-buttons">
                            <button type="button" class="btn btn-previous">Previous</button>
                            <button type="submit" class="btn btn-submit">Submit</button>
                        </div>
                    </fieldset>

                </form>
            </div>
        </div>
    </div>
</div>









<?php include 'footer.php' ?>