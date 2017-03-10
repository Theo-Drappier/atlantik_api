<?php

use RedBeanPHP\R;

require_once 'vendor/autoload.php';

R::setup('mysql:host=localhost; dbname=atlantik_v0', 'root', 'pwsio');

$app = new \Slim\Slim();

//Liste de toutes les données d'une table
$app->get('/find/:table', function ($table) {
    echo json_encode(R::findAll($table), JSON_UNESCAPED_UNICODE);
});

//un enregistrement d'une table (récupération par son id)
$app->get('/find/:table/:id', function ($table, $id) {
    echo json_encode(R::find($table, 'id=' . $id), JSON_UNESCAPED_UNICODE);
});


/**
 * Routes pour les bateaux
 */
$app->group('/boat', function() use ($app) {

    //affiche un bateau selon son nom
    $app->get('/byName/:name', function($name) {
        $boat = R::find('boat', ' name = ?', array($name));
        echo json_encode($boat, JSON_UNESCAPED_UNICODE);
    });

    //ajout d'un bateau
    $app->get('/add/:name', function($name) {
        $query = R::getAll('SELECT * FROM boat 
                          WHERE name = :name', 
                    [':name' => $name]
        );
        if (empty($query)) {
            $boat = R::dispense('boat');
            $boat->name = $name;
            R::store($boat);
            echo json_encode(['message' => 'Success']);
        } else {
            echo json_encode(['message' => 'Error, query already insert']);
        }
    });
    
    //modification d'un bateau
    $app->get('/update/:id/:name', function($id,$name) {
        $boat = R::load('boat', $id);
        $boat->name = $name;
        R::store($boat);
        echo json_encode($boat, JSON_UNESCAPED_UNICODE);
    });
    
    //suppression d'un bateau
    $app->get('/delete/:id', function($id) {
        $query = R::getAll('SELECT * FROM boat WHERE id = '.$id);
        
        if($query){
            R::getAll('DELETE FROM boat WHERE id = '.$id);
            echo json_encode(['message' => 'Success']);
        }else{
            echo json_encode(['message' => 'Error, this row doesn\'t exist']);
        }
    });
});

/**
 * Routes pour les réservations
 */
$app->group('/booking', function() use ($app) {

    //affiche une réservation selon son nom
    $app->get('/byName/:name', function($name) {
        $booking = R::find('booking', ' name = ?', array($name));
        echo json_encode($booking, JSON_UNESCAPED_UNICODE);
    });
    
    //affiche une réservation selon la traversée
    $app->get('/byCrossing/:crossing_id', function($crossing_id) {
        $booking = R::find('booking', ' crossing_id = ?', array($crossing_id));
        echo json_encode($booking, JSON_UNESCAPED_UNICODE);
    });

    //ajout d'une réservation
    $app->get('/add/:name/:address/:postcode/:city/:crossing_id', function($name,$address,$postcode,$city,$crossing_id) {
        $query = R::getAll('SELECT * FROM booking 
                          WHERE name = :name
                          AND address = :address
                          AND postcode = :postcode
                          AND city = :city
                          AND crossing_id = :crossing_id', 
                    [':name' => $name,
                     ':address'=>$address,
                     ':postcode'=>$postcode,
                     ':city'=>$city,
                     ':crossing_id'=>$crossing_id]
        );
        if (empty($query)) {
            $booking = R::dispense('booking');
            $booking->name = $name;
            $booking->address = $address;
            $booking->postcode = $postcode;
            $booking->city = $city;
            $booking->crossing_id = $crossing_id;
            R::store($booking);
            echo json_encode(['message' => 'Success']);
        } else {
            echo json_encode(['message' => 'Error, query already insert']);
        }
    });
    
    //modification d'une réservation
    $app->get('/update/:id/:name/:address/:postcode/:city/:crossing_id', function($id,$name,$address,$postcode,$city,$crossing_id) {
        $booking = R::load('booking', $id);
        $booking->name = $name;
        $booking->address = $address;
        $booking->postcode = $postcode;
        $booking->city = $city;
        $booking->crossing_id = $crossing_id;
        R::store($booking);
        echo json_encode($booking, JSON_UNESCAPED_UNICODE);
    });
    
    //suppression d'une réservation
    $app->get('/delete/:id', function($id) {
        $query = R::getAll('SELECT * FROM booking WHERE id = '.$id);
        
        if($query){
            R::getAll('DELETE FROM booking WHERE id = '.$id);
            echo json_encode(['message' => 'Success']);
        }else{
            echo json_encode(['message' => 'Error, this row doesn\'t exist']);
        }
    });
});

/**
 * Routes pour les types de réservation
 */
$app->group('/bookingtype', function() use ($app) {

    //affiche une réservation selon son nom
    $app->get('/byBooking/:booking_id', function($booking_id) {
        $booking = R::find('bookingtype', ' booking_id = ?', array($booking_id));
        echo json_encode($booking, JSON_UNESCAPED_UNICODE);
    });
    
    //affiche une réservation selon la traversée
    $app->get('/byType/:type_id', function($type_id) {
        $booking = R::find('bookingtype', ' type_id = ?', array($type_id));
        echo json_encode($booking, JSON_UNESCAPED_UNICODE);
    });

    //ajout d'une réservation
    $app->get('/add/:quantity/:booking_id/:type_id', function($quantity,$booking_id,$type_id) {
        $query = R::getAll('SELECT * FROM bookingtype 
                          WHERE quantity = :quantity
                          AND booking_id = :booking
                          AND type_id = :type', 
                    [':quantity' => $quantity,
                     ':booking'=>$booking_id,
                     ':type'=>$type_id]
        );
        if (empty($query)) {
            $booking = R::dispense('bookingtype');
            $booking->quantity = $quantity;
            $booking->booking_id = $booking_id;
            $booking->type_id = $type_id;
            R::store($booking);
            echo json_encode(['message' => 'Success']);
        } else {
            echo json_encode(['message' => 'Error, query already insert']);
        }
    });
    
    //modification d'une réservation
    $app->get('/update/:id/:quantity/:booking_id/:type_id', function($id,$quantity,$booking_id,$type_id) {
        $booking = R::load('bookingtype', $id);
        $booking->quantity = $quantity;
        $booking->booking_id = $booking_id;
        $booking->type_id = $type_id;
        R::store($booking);
        echo json_encode($booking, JSON_UNESCAPED_UNICODE);
    });
    
    //suppression d'une réservation
    $app->get('/delete/:id', function($id) {
        $query = R::getAll('SELECT * FROM bookingtype WHERE id = '.$id);
        
        if($query){
            R::getAll('DELETE FROM bookingtype WHERE id = '.$id);
            echo json_encode(['message' => 'Success']);
        }else{
            echo json_encode(['message' => 'Error, this row doesn\'t exist']);
        }
    });
});

/**
 * Routes pour les capacités
 */
$app->group('/capacity', function() use ($app) {

    //affiche une capacité selon son nom
    $app->get('/byCategory/:category_id', function($category_id) {
        $capacity = R::find('capacity', ' category_id = ?', array($category_id));
        echo json_encode($capacity, JSON_UNESCAPED_UNICODE);
    });
    
    //affiche une capacité selon la traversée
    $app->get('/byBoat/:type_id', function($boat_id) {
        $capacity = R::find('capacity', ' boat_id = ?', array($boat_id));
        echo json_encode($capacity, JSON_UNESCAPED_UNICODE);
    });

    //ajout d'une capacité
    $app->get('/add/:number/:category_id/:boat_id', function($number,$category_id,$boat_id) {
        $query = R::getAll('SELECT * FROM capacity 
                          WHERE number = :number
                          AND category_id = :category_id
                          AND boat_id = :boat_id', 
                    [':number' => $number,
                     ':category_id'=>$category_id,
                     ':boat_id'=>$boat_id]
        );
        if (empty($query)) {
            $capacity = R::dispense('capacity');
            $capacity->number = $number;
            $capacity->category_id = $category_id;
            $capacity->boat_id = $boat_id;
            R::store($capacity);
            echo json_encode(['message' => 'Success']);
        } else {
            echo json_encode(['message' => 'Error, query already insert']);
        }
    });
    
    //modification d'une capacité
    $app->get('/update/:id/:number/:category_id/:boat_id', function($id,$number,$category_id,$boat_id) {
        $capacity = R::load('capacity', $id);
        $capacity->number = $number;
        $capacity->category_id = $category_id;
        $capacity->boat_id = $boat_id;
        R::store($capacity);
        echo json_encode($capacity, JSON_UNESCAPED_UNICODE);
    });
    
    //suppression d'une capacité
    $app->get('/delete/:id', function($id) {
        $query = R::getAll('SELECT * FROM capacity WHERE id = '.$id);
        
        if($query){
            R::getAll('DELETE FROM capacity WHERE id = '.$id);
            echo json_encode(['message' => 'Success']);
        }else{
            echo json_encode(['message' => 'Error, this row doesn\'t exist']);
        }
    });
});

/**
 * Routes pour les catégories
 */
$app->group('/category', function() use ($app) {

    //affiche une réservation selon son nom
    $app->get('/byLabel/:label', function($label) {
        $category = R::find('category', ' label = ?', array($label));
        echo json_encode($category, JSON_UNESCAPED_UNICODE);
    });

    //ajout d'une réservation
    $app->get('/add/:code/:label', function($code,$label) {
        $query = R::getAll('SELECT * FROM category 
                          WHERE code = :code
                          AND label = :label', 
                    [':code' => $code,
                     ':label'=>$label]
        );
        if (empty($query)) {
            $category = R::dispense('category');
            $category->code = $code;
            $category->label = $label;
            R::store($category);
            echo json_encode(['message' => 'Success']);
        } else {
            echo json_encode(['message' => 'Error, query already insert']);
        }
    });
    
    //modification d'une réservation
    $app->get('/update/:id/:code/:label', function($id,$code,$label) {
        $category = R::load('category', $id);
        $category->code = $code;
        $category->label = $label;
        R::store($category);
        echo json_encode($category, JSON_UNESCAPED_UNICODE);
    });
    
    //suppression d'une réservation
    $app->get('/delete/:id', function($id) {
        $query = R::getAll('SELECT * FROM category WHERE id = '.$id);
        
        if($query){
            R::getAll('DELETE FROM category WHERE id = '.$id);
            echo json_encode(['message' => 'Success']);
        }else{
            echo json_encode(['message' => 'Error, this row doesn\'t exist']);
        }
    });
});

$app->run();
