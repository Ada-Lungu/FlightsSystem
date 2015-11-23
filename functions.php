<?php

    session_start();

    function login($email, $pw){
        include 'db.php';
        $query = $conn->prepare("SELECT * FROM users WHERE email=:email AND password=:password");
        $query->bindParam(':email', $email);
        $query->bindParam(':password', $pw);
        $query->execute();
        $user =  $query->fetchAll(PDO::FETCH_ASSOC);
        if (count($user) == 1 ) {

            $_SESSION['user'] = $user[0];
            echo json_encode($_SESSION['user']);

        }else{
            echo '{"result":"error"}';
        }
    }


    // LOGOUT
    function logout() {
        if (isset($_SESSION['user'])) {
            unset($_SESSION['user']);
            session_destroy();
            echo '{"result":"user has logged out"}';
        }else {
            echo '{"result":"error"}';
        }
    }


    // SIGN UP
    function signUp($first_name, $last_name, $phone_num, $email, $password) {
        include 'db.php';
        $query = $conn->prepare('INSERT INTO users (id, first_name, last_name, phone_num, email, password) VALUES (null, :first_name, :last_name, :phone_num, :email, :password)');
        $query->bindParam(':first_name', $first_name);
        $query->bindParam(':last_name', $last_name);
        $query->bindParam(':phone_num', $phone_num);
        $query->bindParam(':email', $email);
        $query->bindParam(':password', $password);
        $query->execute();
//      rowCount() returns the no of rows affected
        $result = $query->rowCount();

        if ($result == 1) {
            echo '{"result":"inserted"}';
        }
        else{
            echo '{"result":"failed"}';
        }

    }


    // RETURNS THE FLIGHTS SEARCHED FOR
    function searchFlights($flight_from, $flight_to, $date) {
        include 'db.php';
        $query = $conn->prepare("SELECT * FROM flights WHERE flight_from = (SELECT code FROM airports WHERE city_name = :flight_from) AND flight_to = (SELECT code FROM airports WHERE city_name = :flight_to) AND departure_time = :date");

        $query->bindParam(':flight_from', $flight_from);
        $query->bindParam(':flight_to', $flight_to);
        $query->bindParam(':date', $date);
        $query->execute();
        $flights = $query->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($flights);

    }


    // GET INFOS ABOUT SELECTED FLIGHT FOR DISPLAY IN MODAL
    function selectFlight($flight_id){
        if (isUserLoggedIn()) {
            include 'db.php';
            $query = $conn->prepare("SELECT * FROM flights WHERE id = $flight_id");
            $query->execute();
            $flight_info = $query->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode($flight_info[0]);

        }else{

            echo '{"result":"error"}';

        }
    }


    // INSERT FLIGHT INTO BOOKINGS/TICKETS
    function bookTicket($flight_id){
        include 'db.php';
        $user_id = getUserId();
        if( $user_id != false) {
            $query = $conn->prepare("INSERT INTO bookings(id, user_id, flight_id, num_tickets) VALUES (NULL, $user_id, :flight_id, 1)");
            $query->bindParam(':flight_id', $flight_id);
            $num_inserted_rows = $query->rowCount();
            $query->execute();

            if ($num_inserted_rows = 1) {
                $last_id = $conn->lastInsertId();
                getBookingById($last_id);
            } else {
                echo '{"result":"error"}';
            }
        }
        else {
            echo '{"result":"error"}';
        }
    }


    function getBookingById($booking_id) {
        include 'db.php';
        $query = $conn->prepare("SELECT flights.flight_no, flights.flight_from, flights.flight_to, flights.departure_time, flights.arrival_time, users.first_name, users.last_name
                                FROM bookings
                                INNER JOIN flights
                                ON bookings.flight_id = flights.id
                                INNER JOIN users
                                ON bookings.user_id = users.id
                                WHERE bookings.id = $booking_id");
        $query->execute();
        $booking_info = $query->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($booking_info[0]);


    }



    // DISPLAY USER'S BOOKINGS

    function displayUserBookings(){
        include 'db.php';
        $user_id = getUserId();
        if (isUserLoggedIn()) {
            $query = $conn->prepare("SELECT flights.flight_from, flights.flight_to, flights.flight_no, flights.departure_time, flights.arrival_time, bookings.num_tickets
                                    FROM flights INNER JOIN bookings
                                    ON flights.id = bookings.flight_id WHERE bookings.user_id = $user_id");
            $query->execute();
            $user_bookings = $query->fetchAll(PDO::FETCH_ASSOC);
//            var_dump($user_bookings);

            echo json_encode($user_bookings);
        }else{

            echo '{"result":"error"}';
        }

    }


    // EDIT USER'S PROFILE
    function editUserProfile(){
        include 'db.php';
        if (isUserLoggedIn()) {
            $user_data = getUserData();
            echo json_encode($user_data);

        }else{
            echo '{"result":"error"}';
        }

    };

    function updateUserProfile($phone_num, $email) {
        include 'db.php';
            $user_id = getUserId();
            //        DO I NEED TO CHECK IF USER LOGGED IN HERE ALSO?
            $query=$conn->prepare("UPDATE users SET phone_num=:phone_num, email=:email WHERE id=$user_id");
            $query->bindParam(':phone_num',$phone_num);
            $query->bindParam(':email',$email);
            $query->execute();
            $result = $query->rowCount();

            if ($result = 1 ) {
                echo '{"result":"success"}';
            }else {
                echo '{"result":"error"}';
            }
    }

    // SESSION RELATED
    // CHECK IF SESSION IS PRESENT/USER IS LOGGED IN

    function isUserLoggedIn() {
        if (isset($_SESSION['user'])) {
            return true;
        }else{
            return false;
        }
    }


    // GET USER'S ID
    function getUserId(){
        if (isset($_SESSION['user'])){
            return $_SESSION['user']['id'];
        }
        else {
            return false;
        }
    }

    // GET USER'S DATA
    function getUserData(){
        $result = json_decode('{"result":"error"}');

        if (isset($_SESSION['user'])){
            $result->result = 'ok';
            $result->id = $_SESSION['user']['id'];
            $result->first_name = $_SESSION['user']['first_name'];
            $result->last_name = $_SESSION['user']['last_name'];
            $result->phone_num = $_SESSION['user']['phone_num'];
            $result->email = $_SESSION['user']['email'];
            $result->is_admin = $_SESSION['user']['is_admin'];
        }

        return $result;
    }

    // CHECK IF USER IS ADMIN OR NOT
    function is_user_admin() {



    }



    // CHANGE PASSWORD
    function changePassword($old, $new){
        $result = json_decode('{"result":"error"}');

        if($old == $_SESSION['user']['password']){
            //change password
            include 'db.php';
            $user_id = $_SESSION['user']['id'];
            $query = $conn->prepare("UPDATE users SET password = :new_password WHERE id = $user_id");
            $query->bindParam(':new_password', $new);
            $query->execute();
            $update = $query->rowCount();

            if ($update == 1) {
                $result->result = "ok";
                $_SESSION['user']['password'] = $new;

            }
        }
        echo json_encode($result);
    }


    // GET ALL FLIGHTS -> ADMIN
    function getAdminAllFlights() {

        include 'db.php';
        $query = $conn->prepare('SELECT * FROM flights');
        $query->execute();

        $all_flights = $query->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($all_flights);

    }

    // DELETE FLIGHT -> ADMIN
    function deleteFlight($flight_id)
    {
        include 'db.php';
        $query = $conn->prepare("DELETE FROM flights WHERE id=:flight_id");
        $query->bindParam(':flight_id', $flight_id);
        $query->execute();
        $affected_rows = $query->rowCount();

        if ($affected_rows == 1) {
            echo '{"result":"ok"}';
        }else {
            echo '{"result":"error"}';
        }

    }


    // EDIT FLIGHT
    function editFlight($flight_id, $flight_no, $flight_from, $flight_to, $departure_time, $arrival_time, $price, $economy_seats, $business_seats)
    {
        include 'db.php';
        $query = $conn->prepare("UPDATE flights SET flight_no=:flight_no, flight_from=:flight_from,
                                    flight_to=:flight_to, departure_time=:departure_time,
                                    arrival_time=:arrival_time, price=:price,
                                    economy_seats=:economy_seats, business_seats=:business_seats
                                    WHERE id=:flight_id");

        $query->bindParam(':flight_id', $flight_id);
        $query->bindParam(':flight_no', $flight_no);
        $query->bindParam(':flight_from', $flight_from);
        $query->bindParam(':flight_to', $flight_to);
        $query->bindParam(':departure_time', $departure_time);
        $query->bindParam(':arrival_time', $arrival_time);
        $query->bindParam(':price', $price);
        $query->bindParam(':economy_seats', $economy_seats);
        $query->bindParam(':businee_seats', $business_seats);


        $query->execute();
        $affected_rows = $query->rowCount();

        if ($affected_rows == 1) {
            echo '{"result":"ok"}';
        }else {
            echo '{"result":"error"}';
        }

    }




?>





