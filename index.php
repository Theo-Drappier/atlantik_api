<?php
use RedBeanPHP\R;

require_once 'vendor/autoload.php';

R::setup('mysql:host=mysql.hostinger.fr; dbname=u990570955_ppe', 'u990570955_ppe', 'pwsio2012');

$app = new \Slim\Slim();

function encode($arrayObject){
    $values= array();
    foreach($arrayObject as $row){
            $values[] = $row;
    }
    return $values;
}

//Liste de toutes les donnÃ©es d'une table
$app->get('/find/:table', function ($table) {
    echo json_encode(encode(R::findAll($table)), JSON_UNESCAPED_UNICODE);
});

//un enregistrement d'une table (rÃ©cupÃ©ration par son id)
$app->get('/find/:table/:id', function ($table, $id) {
    echo json_encode(encode(R::find($table, 'id=' . $id)), JSON_UNESCAPED_UNICODE);
});


/**
 * Routes pour les bateaux
 */
$app->group('/boat', function() use ($app) {

    //affiche un bateau selon son nom
    $app->get('/byName/:name', function($name) {
        $boat = R::find('boat', ' name = ?', array($name));
        echo json_encode(encode($boat), JSON_UNESCAPED_UNICODE);
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
 * Routes pour les rÃ©servations
 */
$app->group('/booking', function() use ($app) {

    //affiche une rÃ©servation selon son nom
    $app->get('/byName/:name', function($name) {
        $booking = R::find('booking', ' name = ?', array($name));
        echo json_encode(encode($booking), JSON_UNESCAPED_UNICODE);
    });
    
    //affiche une rÃ©servation selon la traversÃ©e
    $app->get('/byCrossing/:crossing_id', function($crossing_id) {
        $booking = R::find('booking', ' crossing_id = ?', array($crossing_id));
        echo json_encode(encode($booking), JSON_UNESCAPED_UNICODE);
    });

    //ajout d'une rÃ©servation
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
    
    //modification d'une rÃ©servation
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
    
    //suppression d'une rÃ©servation
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
 * Routes pour les types de rÃ©servation
 */
$app->group('/bookingtype', function() use ($app) {

    //affiche un type de rÃ©servation selon la rÃ©servation
    $app->get('/byBooking/:booking_id', function($booking_id) {
        $booking = R::find('bookingtype', ' booking_id = ?', array($booking_id));
        echo json_encode(encode($booking), JSON_UNESCAPED_UNICODE);
    });
    
    //affiche un type de rÃ©servation selon le type
    $app->get('/byType/:type_id', function($type_id) {
        $booking = R::find('bookingtype', ' type_id = ?', array($type_id));
        echo json_encode(encode($booking), JSON_UNESCAPED_UNICODE);
    });

    //ajout d'une rÃ©servation
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
    
    //modification d'une rÃ©servation
    $app->get('/update/:id/:quantity/:booking_id/:type_id', function($id,$quantity,$booking_id,$type_id) {
        $booking = R::load('bookingtype', $id);
        $booking->quantity = $quantity;
        $booking->booking_id = $booking_id;
        $booking->type_id = $type_id;
        R::store($booking);
        echo json_encode($booking, JSON_UNESCAPED_UNICODE);
    });
    
    //suppression d'une rÃ©servation
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
 * Routes pour les capacitÃ©s
 */
$app->group('/capacity', function() use ($app) {

    //affiche une capacitÃ© selon sa catÃ©gorie
    $app->get('/byCategory/:category_id', function($category_id) {
        $capacity = R::find('capacity', ' category_id = ?', array($category_id));
        echo json_encode(encode($capacity), JSON_UNESCAPED_UNICODE);
    });
    
    //affiche une capacitÃ© selon le bateau
    $app->get('/byBoat/:type_id', function($boat_id) {
        $capacity = R::find('capacity', ' boat_id = ?', array($boat_id));
        echo json_encode(encode($capacity), JSON_UNESCAPED_UNICODE);
    });

    //ajout d'une capacitÃ©
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
    
    //modification d'une capacitÃ©
    $app->get('/update/:id/:number/:category_id/:boat_id', function($id,$number,$category_id,$boat_id) {
        $capacity = R::load('capacity', $id);
        $capacity->number = $number;
        $capacity->category_id = $category_id;
        $capacity->boat_id = $boat_id;
        R::store($capacity);
        echo json_encode($capacity, JSON_UNESCAPED_UNICODE);
    });
    
    //suppression d'une capacitÃ©
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
 * Routes pour les catÃ©gories
 */
$app->group('/category', function() use ($app) {

    //affiche une catÃ©gorie selon son label
    $app->get('/byLabel/:label', function($label) {
        $category = R::find('category', ' label = ?', array($label));
        echo json_encode(encode($category), JSON_UNESCAPED_UNICODE);
    });

    //ajout d'une catÃ©gorie
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
    
    //modification d'une catÃ©gorie
    $app->get('/update/:id/:code/:label', function($id,$code,$label) {
        $category = R::load('category', $id);
        $category->code = $code;
        $category->label = $label;
        R::store($category);
        echo json_encode($category, JSON_UNESCAPED_UNICODE);
    });
    
    //suppression d'une catÃ©gorie
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

/**
 * Routes pour les traversÃ©e
 */
$app->group('/crossing', function() use ($app) {

    //affiche une traversÃ©e selon sa date
    $app->get('/byDate/:date', function($date) {
        $crossing = R::find('crossing', ' date = ?', array($date));
        echo json_encode(encode($crossing), JSON_UNESCAPED_UNICODE);
    });
    
    //affiche une traversÃ©e selon sa date et son heure
    $app->get('/byDateTime/:date/:time', function($date,$time) {
        $crossing = R::find('crossing', ' date = ? AND time_start = ?', array($date,$time));
        echo json_encode(encode($crossing), JSON_UNESCAPED_UNICODE);
    });
    
    //affiche une traversÃ©e selon son lien
    $app->get('/byLink/:link_id', function($link_id) {
        $crossing = R::find('crossing', ' link_id = ?', array($link_id));
        echo json_encode(encode($crossing), JSON_UNESCAPED_UNICODE);
    });
    
    //affiche une traversÃ©e selon son bateau
    $app->get('/byBoat/:boat_id', function($boat_id) {
        $crossing = R::find('crossing', ' boat_id = ?', array($boat_id));
        echo json_encode(encode($crossing), JSON_UNESCAPED_UNICODE);
    });


    //ajout d'une traversÃ©e
    $app->get('/add/:date/:time/:link/:boat', function($date,$time,$link,$boat) {
        $query = R::getAll('SELECT * FROM crossing 
                          WHERE date = :date
                          AND time_start = :time
                          AND link_id = :link
                          AND boat_id = :boat', 
                    [':date' => $date,
                     ':time'=>$time,
                     ':link'=>$link,
                     ':boat'=>$boat]
        );
        if (empty($query)) {
            $crossing = R::dispense('crossing');
            $crossing->date = $date;
            $crossing->time_start = $time;
            $crossing->link_id = $link;
            $crossing->boat_id = $boat;
            R::store($crossing);
            echo json_encode(['message' => 'Success']);
        } else {
            echo json_encode(['message' => 'Error, query already insert']);
        }
    });
    
    //modification d'une traversÃ©e
    $app->get('/update/:id/:date/:time/:link/:boat', function($id,$date,$time,$link,$boat) {
        $crossing = R::load('crossing', $id);
        $crossing->date = $date;
        $crossing->time_start = $time;
        $crossing->link_id = $link;
        $crossing->boat_id = $boat;
        R::store($crossing);
        echo json_encode($crossing, JSON_UNESCAPED_UNICODE);
    });
    
    //suppression d'une traversÃ©e
    $app->get('/delete/:id', function($id) {
        $query = R::getAll('SELECT * FROM crossing WHERE id = '.$id);
        
        if($query){
            R::getAll('DELETE FROM crossing WHERE id = '.$id);
            echo json_encode(['message' => 'Success']);
        }else{
            echo json_encode(['message' => 'Error, this row doesn\'t exist']);
        }
    });
});

/**
 * Routes pour les ports
 */
$app->group('/harbor', function() use ($app) {

    //affiche un port selon son nom
    $app->get('/byName/:name', function($name) {
        $harbor = R::find('harbor', ' name = ?', array($name));
        echo json_encode(encode($harbor), JSON_UNESCAPED_UNICODE);
    });

    //ajout d'un port
    $app->get('/add/:name', function($name) {
        $query = R::getAll('SELECT * FROM harbor 
                          WHERE name = :name', 
                    [':name' => $name]
        );
        if (empty($query)) {
            $harbor = R::dispense('harbor');
            $harbor->name = $name;
            R::store($harbor);
            echo json_encode(['message' => 'Success']);
        } else {
            echo json_encode(['message' => 'Error, query already insert']);
        }
    });
    
    //modification d'un port
    $app->get('/update/:id/:name', function($id,$name) {
        $harbor = R::load('harbor', $id);
        $harbor->name = $name;
        R::store($harbor);
        echo json_encode($harbor, JSON_UNESCAPED_UNICODE);
    });
    
    //suppression d'un port
    $app->get('/delete/:id', function($id) {
        $query = R::getAll('SELECT * FROM harbor WHERE id = '.$id);
        
        if($query){
            R::getAll('DELETE FROM harbor WHERE id = '.$id);
            echo json_encode(['message' => 'Success']);
        }else{
            echo json_encode(['message' => 'Error, this row doesn\'t exist']);
        }
    });
});

/**
 * Routes pour les liens
 */
$app->group('/link', function() use ($app) {

    //affiche un lien selon son port de dÃ©part
    $app->get('/byStartingHarbor/:harbor_id', function($harbor_id) {
        $link = R::find('link', ' starting_harbor_id = ?', array($harbor_id));
        echo json_encode(encode($link), JSON_UNESCAPED_UNICODE);
    });
    
    //affiche un lien selon le port d'arrivÃ©
    $app->get('/byArrivalHarbor/:harbor_id', function($harbor_id) {
        $link = R::find('link', ' arrival_harbor_id = ?', array($harbor_id));
        echo json_encode(encode($link), JSON_UNESCAPED_UNICODE);
    });
    
    //affiche lien(s) selon le secteur
    $app->get('/bySector/:sector_id', function($sector_id) {
        $link = R::find('link', ' sector_id = ?', array($sector_id));
        echo json_encode(encode($link), JSON_UNESCAPED_UNICODE);
    });

    //ajout d'un lien
    $app->get('/add/:starting_harbor/:arrival_harbor/:sector', function($starting_harbor,$arrival_harbor,$sector) {
        $query = R::getAll('SELECT * FROM link 
                          WHERE starting_harbor_id = :starting_harbor
                          AND arrival_harbor_id = :arrival_harbor
                          AND sector_id = :sector', 
                    [':starting_harbor' => $starting_harbor,
                     ':arrival_harbor'=>$arrival_harbor,
                     ':sector'=>$sector]
        );
        if (empty($query)) {
            $link = R::dispense('link');
            $link->starting_harbor_id = $starting_harbor;
            $link->arrival_harbor_id = $arrival_harbor;
            $link->sector_id = $sector;
            R::store($link);
            echo json_encode(['message' => 'Success']);
        } else {
            echo json_encode(['message' => 'Error, query already insert']);
        }
    });
    
    //modification d'un lien
    $app->get('/update/:id/:starting_harbor/:arrival_harbor/:sector', function($id,$starting_harbor,$arrival_harbor,$sector) {
        $link = R::load('link', $id);
        $link->starting_harbor_id = $starting_harbor;
        $link->arrival_harbor_id = $arrival_harbor;
        $link->sector_id = $sector;
        R::store($link);
        echo json_encode($link, JSON_UNESCAPED_UNICODE);
    });
    
    //suppression d'un lien
    $app->get('/delete/:id', function($id) {
        $query = R::getAll('SELECT * FROM link WHERE id = '.$id);
        
        if($query){
            R::getAll('DELETE FROM link WHERE id = '.$id);
            echo json_encode(['message' => 'Success']);
        }else{
            echo json_encode(['message' => 'Error, this row doesn\'t exist']);
        }
    });
});

/**
 * Routes pour les periodes
 */
$app->group('/period', function() use ($app) {

    //affiche une pÃ©riode selon la date de dÃ©but
    $app->get('/byStartDate/:start_date', function($start_date) {
        $period = R::find('period', ' start_date = ?', array($start_date));
        echo json_encode(encode($period), JSON_UNESCAPED_UNICODE);
    });
    
    //affiche une pÃ©riode selon la date de fin
    $app->get('/byEndDate/:end_date', function($end_date) {
        $period = R::find('period', ' end_date = ?', array($end_date));
        echo json_encode(encode($period), JSON_UNESCAPED_UNICODE);
    });
    
    //affiche une pÃ©riode selon la date de dÃ©but et de fin
    $app->get('/byStartDate/byEndDate/:start_date/:end_date', function($start_date,$end_date) {
        $period = R::find('period', ' start_date = ? AND end_date = ?', array($start_date,$end_date));
        echo json_encode(encode($period), JSON_UNESCAPED_UNICODE);
    });

    //ajout d'une pÃ©riode
    $app->get('/add/:start_date/:end_date', function($start_date,$end_date) {
        $query = R::getAll('SELECT * FROM period 
                          WHERE start_date = :start_date
                          AND end_date = :end_date', 
                    [':start_date' => $start_date,
                     ':end_date'=>$end_date]
        );
        if (empty($query)) {
            $period = R::dispense('period');
            $period->start_date = $start_date;
            $period->end_date = $end_date;
            R::store($period);
            echo json_encode(['message' => 'Success']);
        } else {
            echo json_encode(['message' => 'Error, query already insert']);
        }
    });
    
    //modification d'une pÃ©riode
    $app->get('/update/:id/:start_date/:end_date', function($id,$start_date,$end_date) {
        $period = R::load('period', $id);
        $period->start_date = $start_date;
        $period->end_date = $end_date;
        R::store($period);
        echo json_encode($period, JSON_UNESCAPED_UNICODE);
    });
    
    //suppression d'une pÃ©riode
    $app->get('/delete/:id', function($id) {
        $query = R::getAll('SELECT * FROM period WHERE id = '.$id);
        
        if($query){
            R::getAll('DELETE FROM period WHERE id = '.$id);
            echo json_encode(['message' => 'Success']);
        }else{
            echo json_encode(['message' => 'Error, this row doesn\'t exist']);
        }
    });
});

/**
 * Routes pour les prix
 */
$app->group('/price', function() use ($app) {

    //affiche prix selon un lien
    $app->get('/byLink/:link_id', function($link_id) {
        $price = R::find('price', ' link_id = ?', array($link_id));
        echo json_encode(encode($price), JSON_UNESCAPED_UNICODE);
    });
    
    //affiche prix selon le type
    $app->get('/byType/:type_id', function($type_id) {
        $price = R::find('price', ' type_id = ?', array($type_id));
        echo json_encode(encode($price), JSON_UNESCAPED_UNICODE);
    });
    
    //affiche prix selon la pÃ©riode
    $app->get('/byPeriod/:period_id', function($period_id) {
        $price = R::find('price', ' period_id = ?', array($period_id));
        echo json_encode(encode($price), JSON_UNESCAPED_UNICODE);
    });

    //ajout d'un prix
    $app->get('/add/:link_id/:type_id/:period_id/:price', function($link_id,$type_id,$period_id,$price_value) {
        $query = R::getAll('SELECT * FROM price 
                          WHERE link_id = :link
                          AND type_id = :type
                          AND period_id = :period
                          AND price = :price', 
                    [':link' => $link_id,
                     ':type'=>$type_id,
                     ':period'=>$period_id,
                     ':price'=>$price_value]
        );
        if (empty($query)) {
            $price = R::dispense('price');
            $price->link_id = $link_id;
            $price->type_id = $type_id;
            $price->period_id = $period_id;
            $price->price = $price_value;
            R::store($price);
            echo json_encode(['message' => 'Success']);
        } else {
            echo json_encode(['message' => 'Error, query already insert']);
        }
    });
    
    //modification d'un prix
    $app->get('/update/:id/:link_id/:type_id/:period_id/:price', function($id,$link_id,$type_id,$period_id,$price_value) {
        $price = R::load('price', $id);
        $price->link_id = $link_id;
        $price->type_id = $type_id;
        $price->period_id = $period_id;
        $price->price = $price_value;
        R::store($price);
        echo json_encode($price, JSON_UNESCAPED_UNICODE);
    });
    
    //suppression d'un prix
    $app->get('/delete/:id', function($id) {
        $query = R::getAll('SELECT * FROM price WHERE id = '.$id);
        
        if($query){
            R::getAll('DELETE FROM price WHERE id = '.$id);
            echo json_encode(['message' => 'Success']);
        }else{
            echo json_encode(['message' => 'Error, this row doesn\'t exist']);
        }
    });
});

/**
 * Routes pour les secteurs
 */
$app->group('/sector', function() use ($app) {

    //affiche un secteur selon son nom
    $app->get('/byName/:name', function($name) {
        $sector = R::find('sector', ' name = ?', array($name));
        echo json_encode(encode($sector), JSON_UNESCAPED_UNICODE);
    });

    //ajout d'un secteur
    $app->get('/add/:name', function($name) {
        $query = R::getAll('SELECT * FROM sector 
                          WHERE name = :name', 
                    [':name' => $name]
        );
        if (empty($query)) {
            $sector = R::dispense('sector');
            $sector->name = $name;
            R::store($sector);
            echo json_encode(['message' => 'Success']);
        } else {
            echo json_encode(['message' => 'Error, query already insert']);
        }
    });
    
    //modification d'un secteur
    $app->get('/update/:id/:name', function($id,$name) {
        $sector = R::load('sector', $id);
        $sector->name = $name;
        R::store($sector);
        echo json_encode($sector, JSON_UNESCAPED_UNICODE);
    });
    
    //suppression d'un secteur
    $app->get('/delete/:id', function($id) {
        $query = R::getAll('SELECT * FROM sector WHERE id = '.$id);
        
        if($query){
            R::getAll('DELETE FROM sector WHERE id = '.$id);
            echo json_encode(['message' => 'Success']);
        }else{
            echo json_encode(['message' => 'Error, this row doesn\'t exist']);
        }
    });
});

/**
 * Routes pour les types
 */
$app->group('/type', function() use ($app) {

    //affiche un type selon son label
    $app->get('/byLabel/:label', function($label) {
        $type = R::find('type', ' label = ?', array($label));
        echo json_encode(encode($type), JSON_UNESCAPED_UNICODE);
    });

    //ajout d'un type
    $app->get('/add/:code/:label/:category_id', function($code,$label,$category_id) {
        $query = R::getAll('SELECT * FROM type 
                          WHERE code = :code
                          AND label = :label
                          AND category_id = :category', 
                    [':code' => $code,
                     ':label'=>$label,
                     ':category'=>$category_id]
        );
        if (empty($query)) {
            $type = R::dispense('type');
            $type->code = $code;
            $type->label = $label;
            $type->category_id = $category_id;
            R::store($type);
            echo json_encode(['message' => 'Success']);
        } else {
            echo json_encode(['message' => 'Error, query already insert']);
        }
    });
    
    //modification d'un type
    $app->get('/update/:id/:code/:label/:category_id', function($id,$code,$label,$category_id) {
        $type = R::load('type', $id);
        $type->code = $code;
        $type->label = $label;
        $type->category_id = $category_id;
        R::store($type);
        echo json_encode($type, JSON_UNESCAPED_UNICODE);
    });
    
    //suppression d'un type
    $app->get('/delete/:id', function($id) {
        $query = R::getAll('SELECT * FROM type WHERE id = '.$id);
        
        if($query){
            R::getAll('DELETE FROM type WHERE id = '.$id);
            echo json_encode(['message' => 'Success']);
        }else{
            echo json_encode(['message' => 'Error, this row doesn\'t exist']);
        }
    });
    
});

$app->run();
