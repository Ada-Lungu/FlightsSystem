<?php

    include 'db.php';
    include 'functions.php';

// CLEAN AJAX
//$.ajax('ajax.php', {
//            data: {"action":"},
//            dataType: 'json'
//
//        }).done(function(response) {
//
//
//        }).fail(function() {
//
//        });


    $action = $_GET['action'];

    if($action == 'login'){

        login($_GET['email'], $_GET['password']);
    }

    if($action == 'logout'){
        logout();
    }

    if($action == 'sign_up'){
        signUp($_GET['first-name'], $_GET['last-name'], $_GET['phone-num'], $_GET['email'], $_GET['password']);
    }

    if($action == 'search_flights') {
        searchFlights($_GET['flight_from'], $_GET['flight_to'], $_GET['date']);
    }

    if($action == 'select_flight') {
        selectFlight($_GET['flight_id']);
    }

    if($action == 'display_user_bookings') {
        displayUserBookings();
    }
//
//    if($action == 'check_session'){
//        checkSession();
//    }
//
//    if($action == 'get_user_id'){
//        echo getUserId();
//    }

    if($action == 'book_ticket'){
        bookTicket($_GET['flight_id']);
    }

    if($action == 'is_user_logged_in') {
        echo json_encode(getUserData());
    }

    if($action == 'edit_user_profile') {
        editUserProfile();
    }

    if($action == 'update_user_profile') {
        updateUserProfile($_GET['phone_num'], $_GET['email']);
    }

    if($action == 'change_password') {
        changePassword($_GET['old_password'], $_GET['new_password']);
    }

    if($action == 'get_all_flights') {
        getAdminAllFlights();
    }

    if($action == 'delete_flight') {
        deleteFlight($_GET['flight_id']);
    }

    if($action == 'edit_flight') {
        editFlight(
            $_GET['flight_id'], $_GET['flight_no'],
            $_GET['flight_from'], $_GET['flight_to'],
            $_GET['departure_time'], $_GET['arrival_time'],
            $_GET['price'], $_GET['economy_seats'], $_GET['business_seats']
        );
    }

    if($action == 'create_flight') {
        createFlight($_GET['flight_no'],
                    $_GET['flight_from'],
                    $_GET['flight_to'],
                    $_GET['departure_time'],
                    $_GET['arrival_time'],
                    $_GET['price'],
                    $_GET['economy_seats'],
                    $_GET['business_seats']
        );
    }

    if($action == 'get_passengers') {
        getPassengers($_GET['flight_id']);
    }

    if($action == 'cancel_booking') {
        cancelBooking($_GET['booking_id']);
    }

    if($action == 'get_airports') {
        getAirports();
    }

    if($action == 'generate_flights') {
        generateFlights();
    }




?>