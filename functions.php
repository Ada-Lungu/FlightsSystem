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
            echo '{"result":"ok"}';
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

    }

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
        $query = $conn->prepare("SELECT flights.*, airports1.city_name AS departure_city, airports2.city_name AS arrival_city FROM flights
                                LEFT JOIN airports as airports1
                                ON flights.flight_to = airports1.code
                                LEFT JOIN airports as airports2
                                ON flights.flight_from = airports2.code");
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
    function editFlight($flight_id, $flight_no, $flight_from, $flight_to,
                        $departure_time, $arrival_time, $price, $economy_seats, $business_seats)
    {
        include 'db.php';
        $query = $conn->prepare("UPDATE flights SET flight_no=:flight_no, flight_from=:flight_from,
                                    flight_to=:flight_to, departure_time=:departure_time,
                                    arrival_time=:arrival_time, price=:price,
                                    business_seats=:business_seats, economy_seats=:economy_seats
                                    WHERE id=:flight_id");

        $query->bindParam(':flight_id', $flight_id);
        $query->bindParam(':flight_no', $flight_no);
        $query->bindParam(':flight_from', $flight_from);
        $query->bindParam(':flight_to', $flight_to);
        $query->bindParam(':departure_time', $departure_time);
        $query->bindParam(':arrival_time', $arrival_time);
        $query->bindParam(':price', $price);
        $query->bindParam(':economy_seats', $economy_seats);
        $query->bindParam(':business_seats', $business_seats);

        $query->execute();
        $affected_rows = $query->rowCount();

        if ($affected_rows == 1) {
            echo '{"result":"ok"}';
        }else {
            echo '{"result":"error"}';
        }

    }


    // CREATE FLIGHT

    function createFlight($flight_no, $flight_from, $flight_to,
                          $departure_time, $arrival_time, $price, $economy_seats, $business_seats)
    {
        include 'db.php';
        $query = $conn->prepare("INSERT INTO flights (flight_no, flight_from,
                                    flight_to, departure_time,
                                    arrival_time, price,
                                    economy_seats, business_seats)
                                    VALUES(:flight_no, :flight_from, :flight_to, :departure_time, :arrival_time, :price, :economy_seats, :business_seats)
                                    ");

        $query->bindParam(':flight_no', $flight_no);
        $query->bindParam(':flight_from', $flight_from);
        $query->bindParam(':flight_to', $flight_to);
        $query->bindParam(':departure_time', $departure_time);
        $query->bindParam(':arrival_time', $arrival_time);
        $query->bindParam(':price', $price);
        $query->bindParam(':economy_seats', $economy_seats);
        $query->bindParam(':business_seats', $business_seats);

        $query->execute();
        $last_id = $conn->lastInsertId();

        $query = $conn->prepare("SELECT * FROM flights WHERE id = $last_id");
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_ASSOC)[0];


        if (count($result) == 0) {
            echo '{"result":"error"}';
        }else {
            echo json_encode($result);
        }

    }

    // GET PASSENGERS

    function getPassengers($flight_id) {
        include 'db.php';
        $query = $conn->prepare ("SELECT users.*, bookings.id AS booking_id FROM bookings
                                JOIN users
                                ON bookings.user_id = users.id
                                WHERE bookings.flight_id =:flight_id");

        $query->bindParam(':flight_id', $flight_id);
        $query->execute();
        $passengers =  $query->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($passengers);

    }

    function cancelBooking($booking_id){
        include 'db.php';
        $query = $conn->prepare("DELETE FROM bookings WHERE id = :booking_id");
        $query->bindParam(':booking_id', $booking_id);
        $query->execute();
        echo '{"result":"ok"}';
    }



    function getAirports(){
        include 'db.php';
        $query = $conn->prepare("SELECT * FROM airports");
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($result);
    }



    // INSERT FLIGHTS INTO TABLE

    function generateFlights() {
        include 'db.php';

    for ($i = 0; count(20); $i++) {
            $cities = array('MAD','SIN','OTP','OSL', 'ARD', 'SXF', 'VHA');
            $random_key = array_rand($cities, 2);
            $random_city1 = $cities[$random_key[0]];
            $random_city2 = $cities[$random_key[1]];
            echo $random_city1;
            echo $random_city2;

            // random date for this month and last month: -30, 30
            $random_date = date('Y-m-d', strtotime( '+'.mt_rand(0,30).' days')); //need to generate datetime not jus date . H:i:s ,
            echo $random_date;

            $price = array('120','140','220','80', '110');
            $random_key = array_rand($price, 1);
            $random_price = $price[$random_key];
    //        echo $random_price;

            $economy_seats = (rand(10, 50));
            $business_seats = (rand(7, 30));
    //        echo $seats;

            $flight_no = substr(md5(rand()), 0, 5);
    //         echo $flight_no;

            $query = $conn->prepare("INSERT INTO flights
                              VALUES (null, :fl_no, :rand_city1, :rand_city2, :rand_date1, :rand_date2, :rand_price, :rand_seats1, :rand_seats2)");

            $query->bindParam(':fl_no', $flight_no);
            $query->bindParam(':rand_city1', $random_city1);
            $query->bindParam(':rand_city2', $random_city2);
            $query->bindParam(':rand_date1', $random_date);
            $query->bindParam(':rand_date2', $random_date);
            $query->bindParam(':rand_price', $random_price);
            $query->bindParam(':rand_seats1', $economy_seats);
            $query->bindParam(':rand_seats2', $business_seats);

            $query->execute();
        }

        echo '{"result":"ok"}';

    }


    // SEND SMS TO USERS

    function sendSMS($flight_id)
    {
        include 'db.php';
        $query = $conn->prepare ("SELECT flight_no, flight_from, flight_to, departure_time FROM flights WHERE id = :flight_id");
        $query->bindParam(':flight_id', $flight_id);
        $query->execute();
        $flight_infos = $query->fetchAll(PDO::FETCH_ASSOC);
        var_dump($flight_infos);


        $flight_no = $flight_infos[0]['flight_no'];
        $flight_from = $flight_infos[0]['flight_from'];
        $flight_to = $flight_infos[0]['flight_to'];
        $departure_time = $flight_infos[0]['departure_time'];
        echo "$flight_no, $flight_from, $flight_to, $departure_time";


//       $user_id = getUserId();
        $user_data = getUserData();
        var_dump($user_data);

        $user_first_name = $user_data[0]['first_name'];
        echo $user_first_name;
        $user_last_name = $user_data[0]['last_name'];
        $user_phone_num = $user_data[0]['phone_num'];
//        $user_email = $user_data[0]['email'];


        $key = 'ODIz-MzVl-NzVh-ODFl-NTcy-OThm-ZmJi-M2Ix-MDMx-MTI2-MzM2';
        $message = urlencode("Dear $user_first_name $user_last_name.Thanks for flying with us. Your flight with the id $flight_id, flying from $flight_from to  $flight_to
                              will be departuring on $departure_time . Enjoy your flight."); // make the phrase URL friendly
        $sUrl = "http://ecuanota.com/api-send-sms"; // point to this URL
        $sLink = $sUrl."?key=".$key."&mobile=".$user_phone_num."&message=".$message; // create the SMS
        file_get_contents($sLink); // send the SMS
        // echo file_get_contents($sLink); // to see the JSON response from the server


    }


    // GENERATE PASSENGERS
    function generatePassengers($howmany){
        include 'db.php';
        $query = $conn->prepare("SELECT id from USERS");
        $query->execute();
        $passengers = $query->fetchAll(PDO::FETCH_COLUMN, 0);

        $query = $conn->prepare("SELECT id from FLIGHTS");
        $query->execute();
        $flights = $query->fetchAll(PDO::FETCH_COLUMN, 0);

        //$query = $conn->prepare(INSERT INTO bookings VALUES(null, $passenger, $flight, 1));
        //$query->execute();

       for($i = 0; $i<$howmany; $i++){
           $passenger = $passengers[rand(0, count($passengers) - 1)];
           $flight = $flights[rand(0, count($flights) - 1)];

           $query = $conn->prepare("INSERT INTO bookings VALUES(null, $passenger, $flight, 1)");
           $query->execute();

       }

    }

?>





