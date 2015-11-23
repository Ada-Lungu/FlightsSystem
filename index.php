
<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="favicon.png">
    <title>My Flights System</title>


    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">

    <!-- Fontawesome -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    <link href='https://fonts.googleapis.com/css?family=Handlee' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" type="text/css" href="flights-project.css">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>

    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<style>


</style>



<div class="container">

    <?php include 'components/header.html'; ?>
    <?php include 'components/header_admin.html'; ?>
    <div class="view" id="home">
        <?php include 'components/user-page.html'; ?>
        <?php include 'components/admin-page.html'; ?>
        <?php include 'components/login.html'; ?>
        <?php include 'components/sign-up.html'; ?>
        <?php include 'components/search.html'; ?>
        <?php include 'components/flights-display-table.html'; ?>
        <?php include 'components/modal.html'; ?>
<!--        --><?php //include 'components/modal-delete-flight.html'; ?>

    </div>
    <div class="view container" id="my_flights" style="display: none">
    <!--    here we show the my bookings table-->
        <?php include 'components/user-bookings-display.html'; ?>
    </div>

    <div class="view container" id="settings" style="display: none">
        <?php include 'components/edit-user-profile.html'; ?>
    </div>

    <div class="view container" id="admin_flights" style="display: none">
        <?php include 'components/admin_flights_display.html'; ?>
        <?php include 'components/modal-delete-flight.html'; ?>
        <?php include 'components/create_flight.html'; ?>
    </div>

<!--    <div class="view container" id="create_flight" style="display: none">-->
<!--        --><?php //include 'components/create_flight.html'; ?>
<!--    </div>-->

</div>


<!-- JQuery -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>

<!-- Latest compiled and minified JavaScript bootstrap -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<script src="date.js"></script>
<script src="functions.js"></script>

<script>

    $('.component').hide();
    $('#search_flights').show();
    $('#header').show();


    isUserLoggedIn();

//    check if user logged in
    function isUserLoggedIn() {
        $.ajax('ajax.php', {
            dataType: "json",
            data: {"action":"is_user_logged_in"}
        }).done(function(response){
            console.log(response);
            if(response.result == "ok"){
                $('.menu_item').show();
                $('#login_header .text').html('Log Out');
                $('#login_header').attr('data-button', 'logout');
            }

        }).fail(function(response){
            console.log(response)
        })
    }


//  SEARCH BUTTON + CREATE FLIGHTS TABLE
    $(document).on('click', '#btn_search', function() {
        var flight_from = $('#flight_from').val();
        var flight_to = $('#flight_to').val();
        var date = $('#calendar').val();

        $('#flights_table').show();
        $('#flights_table tbody').html('');

        if (date == '') {
            var date = new Date();
            date = date.getFullYear() + '-' + date.getMonth() + '-' + date.getDate();
        }

        $.ajax('ajax.php', {

                data: {"action":"search_flights", "flight_from":flight_from, "flight_to":flight_to, "date":date},
                dataType: 'json'

            }).done(function(response) {
//              array of flights, put them into the flights table
                console.log(response);
                for (var i= 0; i<response.length; i++) {


                    $('#flights_table tbody').append('<tr>' + '<td><input class="checkbox" data-id="' +response[i].id+ '" type="checkbox"></td>' +

                        '<td>'+response[i].flight_from+'</td>' +
                        '<td>'+response[i].flight_to+'</td>' +
                        '<td>'+response[i].flight_no+'</td>' +
                        '<td>'+response[i].departure_time+'</td>' +
                        '<td>'+response[i].arrival_time+'</td>' +
                        '<td>'+response[i].price+'</td>' +
                        '</tr>');

                }

            }).fail(function(response) {
                console.log(response);

            });
    });


    // CHECKBOX

    $(document).on('change', '.checkbox', function() {
        var flight_id = $(this).attr('data-id');
        if (this.checked) {
            $('.checkbox').not(this).attr('checked', false); // Unchecks it
            console.log('checked');
        }

    });



    //  DISPLAY SIGN UP

    $(document).on('click', '#sign_up_header', function() {
        $('#sign_up_form').show();
        $('#search_flights').hide();

    });


    // SIGN UP USER

    $(document).on('click', '#btn_signup', function() {

            var first_name = $('#first_name').val();
            var last_name = $('#last_name').val();
            var phone_num = $('#phone_num').val();
            var email = $('#email').val();
            var password = $('#password').val();

            $.ajax('ajax.php', {
                data: {"action":"sign-up", "first_name": first_name, "last_name": last_name, "phone_num": phone_num, "email": email, "password": password},
                dataType: 'json'

            }).done(function(response) {
                console.log(response.result);

            }).fail(function(response) {
                console.log(response);

            });

    });



    // DISPLAY LOGIN/LOGOUT

    $(document).on('click', '#login_header', function() {
        var button_type = $(this).attr('data-button');
        if(button_type == 'login'){
            $('#login_form').show();
            $('#search_flights').hide();
        }
        // PERFORM LOGOUT
        else {
            $.ajax('ajax.php', {
                data: {"action":"logout"},
                dataType: 'json'
            }).done(function(response) {

                console.log(response.result);

                $('#login_header').show();
                $('#sign_up_header').hide();
                $('#logout_header').attr('id', '#login_header');
                $('.menu_item').not('#home').hide();
                $('*[data-item="home"]').show();

                $('#login_header').attr('data-button','login');
                $('#login_header .text').html('Login');
                // CHANGE logout button back to login button + attributes
                // show the search flights form


            }).fail(function(response){
                console.log(response);
            });

        }

    });


    // LOGIN USER

    $(document).on('click', '#btn_login', function() {
        var email = $('#email').val();
        var password = $('#password').val();
        console.log(email);
        console.log(password);

        $.ajax('ajax.php', {
            data: {"action": "login", "email": email, "password": password},
            dataType: 'json'

        }).done(function(response) {

            $('#login_header .text').html('Log Out');
            $('#login_header').attr('data-button', 'logout');
            $('.menu_item').show();

            // For Users
            if (response.is_admin == 0) {
                console.log("You are not an admin");
                $('#search_flights').show();

                $('#sign_up_header .text').html(response.first_name + ' ' + response.last_name);
                $('#sign_up_header').attr('id', '#username_header');
                $('#login_form').hide();
                $('#myModal #login_form').hide();

                if ($('#myModal').css('display') == 'block') {

                    $('#flight_infos').show();
                    $('#payment_form').show();

                }
            // For Admin
            }else{
                console.log("User");

                // change the id's for the header's elements
                $('#header').hide();
                $('.admin_header').show();

//                  $('#header #flights_header').attr('data-item','admin_flights');
//                  $('#header #settings_header').attr('data-item','admin_settings');


            }

        }).fail(function (response) {
            console.log(response);

        });

    });



    // SELECT FLIGHT / DISPLAY INFO FOR SELECTED FLIGHT IN MODAL

    $(document).on('click', '#btn_select_flight', function() {
        for (var i=0; i<$('.checkbox').length; i++) {
            if ($('.checkbox')[i].checked) {
                var flight_id = ($('.checkbox')[i].getAttribute('data-id'));
                $('#myModal').attr('data-flight-id', flight_id);
            }
        }


        $.ajax('ajax.php', {
            data: {"action": "select_flight", "flight_id":flight_id},
            dataType: 'json'

        }).done(function(response) {
            console.log(response);
            // IF USER IS LOGGED IN
            if (response.result != 'error')  {
                console.log("User Logged IN");

                $('#myModal').modal('show');
                $('#ticket_info').hide();
                $('#myModal #login_form').hide();
                $('#flight_infos').show();
                $('#payment_form').show();


                $('.modal-body #flight_infos').html('<p>' + response.flight_no + '</p>' +
                    '<p>' + response.flight_from + '</p>' +
                    '<p>' + response.flight_to + '</p>' +
                    '<p>' + response.departure_time + '</p>' +
                    '<p>' + response.arrival_time + '</p>');

            }else {
                // USER IS LOGGED OUT
                console.log("User Logged OUT");

                $('#myModal').modal('show');
                $('#myModal #login_form').show();

                $('#flight_infos').hide();
                $('#ticket_info').hide();
                $('#payment_form').hide();


                // tell the user to login!!

            }

            }).fail(function (response) {
                console.log(response);

            });
    });


    // BUY TICKET
    // INSERT FLIGHT INTO BOOKINGS/TICKETS - on click on buy button

    $(document).on('click', '#btn_buy_ticket', function() {

        var flight_id = $('#myModal').attr('data-flight-id');

        $.ajax('ajax.php', {
            data: {"action": "book_ticket", "flight_id": flight_id},
            dataType: 'json'
        }).done(function(response) {
            console.log(response);
            $('#flight_infos').hide();
            $('#payment_form').hide();
            $('#ticket_info').show();

            $('#ticket_info #full_name span').text(' ' + response.first_name + ' ' + response.last_name);
            $('#ticket_info #flight_from span').text(' ' + response.flight_from);
            $('#ticket_info #flight_to span').text(' ' + response.flight_to);
            $('#ticket_info #departure_time span').text(' ' + response.departure_time);
            $('#ticket_info #arrival_time span').text(' ' + response.arrival_time);

        }).fail(function(response) {
            console.log(response);
        });

    });


    // DISPLAY USER'S BOOKINGS

    $(document).on('click', '.menu_item', function() {
        var menu_item = $(this).attr('data-item');
        createBookingsCalendar();

        if (menu_item == 'my_flights') {
        //  var user_id = getUserId();
            $.ajax('ajax.php', {
                data: {"action": "display_user_bookings"},
                dataType: 'json'

            }).done(function (response) {
                // if user logged in
                if(response.result != 'error') {

                    $('#my_flights.view').show();

                    for (var i = 0; i < response.length; i++) {
                        console.log(response[i]);

//                        $('#user_bookings_table tbody')
                        markDate(response[i]);

                        $('#user_bookings_table tbody').append('<tr> <td>' + response[i].flight_from + '</td>' +
                            '<td>' + response[i].flight_to + '</td>' +
                            '<td>' + response[i].flight_no + '</td>' +
                            '<td>' + response[i].departure_time + '</td>' +
                            '<td>' + response[i].arrival_time + '</td>' + '</tr>');
                    }
                }

            }).fail(function (response) {
                console.log(response);

            });
        }else{
            console.log('NO!');
        }


    });


    // CHANGE BETWEEN VIEWS

    $(document).on("click",".menu_item", function() {
        var item_type = $(this).attr('data-item');
        $('.menu_item').removeClass('active');
        $(this).addClass('active');
        $('.view').hide();
        $('#'+ item_type +'.view').show();


    });


    // EDIT USER -> RETRIVE INFO ABOUT THE USER IN THE EDIT FORM

    $(document).on('click', '.menu_item', function() {
        var menu_item = $(this).attr('data-item');

        if (menu_item == 'settings') {

            $.ajax('ajax.php', {
                data: {"action": "edit_user_profile"},
                dataType: 'json'

            }).done(function(response) {

                if (response.result != 'error') {
                    $('#settings.view').show();
                    $('#edit_user_form').show();

                    $('#edit_user_form #first_name').val(response.first_name);
                    $('#edit_user_form #last_name').val(response.last_name);
                    $('#edit_user_form #phone_num').val(response.phone_num);
                    $('#edit_user_form #email').val(response.email);
                    $('#edit_user_form #password').val(response.password);

                }

            }).fail(function(response) {
                console.log(response);

            });
        }
    });


    // EDIT USER -> UPDATE THE DB WITH THE NEW DATA
    $(document).on('click', '#btn_save_changes', function() {
        var phone_num = $('#edit_user_form #phone_num').val();
        var email = $('#edit_user_form #email').val();
        $(this).attr('class', 'btn btn-warning');

        $.ajax('ajax.php', {
                data: {"action":"update_user_profile", "phone_num":phone_num, "email":email},
                dataType: 'json'

            }).done(function(response) {
                console.log(response);
                $('#alert_edited_profile').attr('class', 'alert alert-success');
                $('#alert_edited_profile').html('The changes has been successfully saved');
                $('#alert_edited_profile').slideDown(300);
                setTimeout(function(){$('#alert_edited_profile').slideUp(300)}, 5000);
                $('#btn_save_changes').attr('class', 'btn btn-success');

            }).fail(function() {
                $('#alert_edited_profile').attr('class', 'alert alert-danger');
                $('#alert_edited_profile').html('Some error has occured. Please try again.');
                $('#alert_edited_profile').slideDown(300);
                setTimeout(function(){$('#alert_edited_profile').slideUp(300)}, 5000);

            });
    });


    // CHANGE PASSWORD
    $(document).on('click', '#btn_change_password', function(){
        var old_password = $('#old_password').val();
        var new_password = $('#new_password').val();
        $.ajax('ajax.php', {
            data: {"action":"change_password", "old_password":old_password, "new_password":new_password},
            dataType: 'json'
        }).done(function(response){
            console.log(response);
            if(response.result != 'error'){
                $('#alert_password').attr('class', 'alert alert-success');
                $('#alert_password').html('The password has been successfully changed');
                $('#alert_password').slideDown(300);
                setTimeout(function(){$('#alert_password').slideUp(300)}, 5000);
                $('#btn_change_password').attr('class', 'btn btn-success');
            }
            else {
                $('#alert_password').attr('class', 'alert alert-danger');
                $('#alert_password').html('The old password was incorrect');
                $('#alert_password').slideDown(300);
                setTimeout(function(){$('#alert_password').slideUp(300)}, 5000);
            }
        })

    })


    // create a function that sends to the main page => because it's used many times

//     BUY TICKET / SHOW RECEIPT ON BUY-TICKET BUTTON

//    $(document).on('click', '#btn_buy_ticket', function() {
//        // i could take the user's info from session, he is logged in, right?
//        var user_first_name= $('#payment_form #name').val();
//        var user_last_name = $('#payment_form #last_name').val();
//
//        $.ajax('ajax.php', {
//            data: {"action": "select_flight", "flight_id":flight_id},
//            dataType: 'json'
//
//        }).done(function (response) {
//            console.log(response.flight_from);
//
//
//        }).fail(function (response) {
//            console.log(response);
//
//        });
//
//    });


    // BOOKINGS CALENDAR
    function createBookingsCalendar() {
        $('#bookings_calendar').html("");

        var now = new Date();
        var year = now.getYear();
        var month = now.getMonth() + 1;
        var date = now.getDate();
        var daysInMonth = Date.getDaysInMonth(year, month);


        for (var i=1; i<=daysInMonth; i++) {
            if (i < date) {
                $('#bookings_calendar').append('<div id="'+ i +'" class="day-square day-past">'+ i +'</div>');
            }else{
                $('#bookings_calendar').append('<div id="'+ i +'" class="day-square">'+i+'</div>');
            }
        }
    }


    // MARK FLIGHTS IN BOOKINGS CALENDAR
    function markDate(flight) {
        var now = new Date();
        var year = now.getFullYear();
        var month = now.getMonth() + 1;
        var date = now.getDate();

        var mydate = flight.departure_time;
        console.log(mydate);
        var my_year = Number(mydate.substring(0,4));
        var my_month = Number(mydate.substring(5,7));
        var my_day = Number(mydate.substring(8,10));
        console.log(year);
        console.log(my_year);
        console.log(month);
        console.log(my_month);

        if (my_year == year && my_month == month) {
            console.log('x');

            if (date > my_day) {
                $('#bookings_calendar #'+my_day).html('<div class="circle circle_red"><div>Went to <span>'+flight.flight_to+'</span></div></div>');
            }
            else {
                $('#bookings_calendar #'+my_day).html('<div class="circle"><div>Going to <span>'+flight.flight_to+'</span></div></div>');

            }
        }
    }


    // ADMIN
    // SHOW ALL FLIGHTS
    $(document).on("click","#list_flights", function() {
//       delete the already there table

        $('#login_form').hide();
        $('#admin_flights.view').show();
//        $('.admin_header').show();

        $.ajax('ajax.php', {
            data: {"action":"get_all_flights"},
            dataType:'json'
        }).done(function(response) {
            console.log(response);
            for (var i=0; i<response.length; i++) {
                $('#admin_flights_table tbody').append('<tr id="'+response[i].id+'"><td id="flight_from">' + response[i].flight_from + '</td>' +
                    '<td id="flight_to">' + response[i].flight_to + '</td>' +
                    '<td id="flight_no">' + response[i].flight_no + '</td>' +
                    '<td id="departure_time">' + response[i].departure_time + '</td>' +
                    '<td id="arrival_time">' + response[i].arrival_time + '</td>' +
                    '<td id="price">' + response[i].price + '</td>' +
                    '<td id="economy_seats">' + response[i].economy_seats + '</td>' +
                    '<td id="business_setas">' + response[i].business_seats + '</td>' +
                    '<td><i id="edit_flight" data-flight-id="'+ response[i].id +'" class="fa fa-pencil"></i><i id="delete_flight" data-flight-id="'+ response[i].id +'" class="fa fa-trash-o"></i></td>' +
                    '</tr>')
            }
        }).fail(function(response) {
            console.log(response);
        });

    });



    // DELETE FLIGHT

    $(document).on("click","#delete_flight", function() {
        console.log('Delete');
        $('#deleteModal').modal('show');
        $flight_id = $(this).attr('data-flight-id');
        $('#deleteModal').attr('data-flight-id', $flight_id);

    });


    $(document).on("click","#btn_delete_flight", function() {

        var flight_id = $('#deleteModal').attr('data-flight-id');
        console.log("The flight id is" + $flight_id);
        $.ajax('ajax.php', {
            data: {"action": "delete_flight", "flight_id": flight_id},
            dataType: 'json'

        }).done(function (response) {
            console.log(response);
            if (response.result = 'ok') {
                $('#admin_flights_table tbody tr#' + flight_id).hide();
            }

        }).fail(function (response) {
            console.log(response);

        });

    });


    // EDIT FLIGHT

    $(document).on("click","#edit_flight", function() {
        var flight_id = $(this).attr('data-flight-id');


//      $('#admin_flights_table tbody tr#' + flight_id).html('<td><input type="text" value="'+response[i].flight_to+'">' + response[i].flight_to + '</td>');
        var text_flight_from = $('#admin_flights_table tbody tr#' + flight_id + ' #flight_from').html();
        var text_flight_to = $('#admin_flights_table tbody tr#' + flight_id + ' #flight_to').text();
        var text_flight_no = $('#admin_flights_table tbody tr#' + flight_id + ' #flight_no').html();
        var text_departure_time = $('#admin_flights_table tbody tr#' + flight_id + ' #departure_time').text();
        var text_arrival_time = $('#admin_flights_table tbody tr#' + flight_id + ' #arrival_time').text();
        var text_price = $('#admin_flights_table tbody tr#' + flight_id + ' #price').html();
        var text_economy_seats = $('#admin_flights_table tbody tr#' + flight_id + ' #economy_seats').html();
        var text_business_seats = $('#admin_flights_table tbody tr#' + flight_id + ' #business_seats').html();

        console.log(text_flight_from);
        $('#admin_flights_table tbody tr#' + flight_id + ' #flight_from').html($('<input />',{'value' : text_flight_from}).val(text_flight_from));
        $('#admin_flights_table tbody tr#' + flight_id + ' #flight_to').html($('<input />',{'value' : text_flight_to}).val(text_flight_to));
        $('#admin_flights_table tbody tr#' + flight_id + ' #flight_no').html($('<input />',{'value' : text_flight_no}).val(text_flight_no));
        $('#admin_flights_table tbody tr#' + flight_id + ' #departure_time').html($('<input />',{'value' : text_departure_time}).val(text_departure_time));
        $('#admin_flights_table tbody tr#' + flight_id + ' #arrival_time').html($('<input />',{'value' : text_arrival_time}).val(text_arrival_time));
        $('#admin_flights_table tbody tr#' + flight_id + ' #price').html($('<input />',{'value' : text_price}).val(text_price));
        $('#admin_flights_table tbody tr#' + flight_id + ' #economy_seats').html($('<input />',{'value' : text_economy_seats}).val(text_economy_seats));
        $('#admin_flights_table tbody tr#' + flight_id + ' #business_seats').html($('<input />',{'value' : text_business_seats}).val(text_business_seats));

        $('#admin_flights_table tbody tr#' + flight_id + ' #edit_flight').attr('class', 'fa fa-check-circle');
        $(this).attr('id','save_edited_flight');


    });



    // SAVE EDITED FLIGHT
    $(document).on("click","#save_edited_flight", function() {
        icon_class = $(this).attr('class');
        console.log(icon_class);

        var flight_id = $(this).attr('data-flight-id');
        var flight_from = $('#admin_flights_table tbody tr#' + flight_id + ' #flight_from input').val();
        var flight_to = $('#admin_flights_table tbody tr#' + flight_id + ' #flight_to input').val();
        var flight_no = $('#admin_flights_table tbody tr#' + flight_id + ' #flight_no input').val();
        var departure_time = $('#admin_flights_table tbody tr#' + flight_id + ' #departure_time input').val();
        var arrival_time = $('#admin_flights_table tbody tr#' + flight_id + ' #arrival_time input').val();
        var price = $('#admin_flights_table tbody tr#' + flight_id + ' #price input').val();
        var economy_seats = $('#admin_flights_table tbody tr#' + flight_id + ' #economy_seats input').val();
        var business_seats = $('#admin_flights_table tbody tr#' + flight_id + ' #business_seats input').val();


        console.log("Flight" + flight_from);

        $.ajax('ajax.php', {
            data: {"action": "edit_flight", "flight_id": flight_id, "flight_from": flight_from,
                    "flight_to": flight_to, "flight_no": flight_no, "departure_time": departure_time,
                    "arrival_time": arrival_time, "price": price, "economy_seats":economy_seats,
                    "business_seats": business_seats},
            dataType: 'json'

        }).done(function (response) {
            if (response.result = 'ok') {
                console.log("Dates updated in DB");

                $('#admin_flights_table tbody tr#' + flight_id + ' #flight_from').html(flight_from);
//                $('#admin_flights_table tbody tr#' + flight_id + ' #flight_to').html($('<input />',{'value' : text_flight_to}).val(text_flight_to));
//                $('#admin_flights_table tbody tr#' + flight_id + ' #flight_no').html($('<input />',{'value' : text_flight_no}).val(text_flight_no));
//                $('#admin_flights_table tbody tr#' + flight_id + ' #departure_time').html($('<input />',{'value' : text_departure_time}).val(text_departure_time));
//                $('#admin_flights_table tbody tr#' + flight_id + ' #arrival_time').html($('<input />',{'value' : text_arrival_time}).val(text_arrival_time));
//                $('#admin_flights_table tbody tr#' + flight_id + ' #price').html($('<input />',{'value' : text_price}).val(text_price));
//                $('#admin_flights_table tbody tr#' + flight_id + ' #economy_seats').html($('<input />',{'value' : text_economy_seats}).val(text_economy_seats));
//                $('#admin_flights_table tbody tr#' + flight_id + ' #business_seats').html($('<input />',{'value' : text_business_seats}).val(text_business_seats));
//
//                $('#admin_flights_table tbody tr#' + flight_id + ' #edit_flight').attr('class', 'fa fa-check-circle');


            }

        }).fail(function (response) {
            console.log(response);

        });
    });


    // CREATE FLIGHT

    $(document).on("click","#create_flight", function() {

        $('#login_form').hide();
//        $('#admin_flights.view').hide();
//        $('#create_flight.view').show();
//        $('#admin_flights_table').hide();
        $('#create_flight_form').show();





    });

</script>


</body>
</html>