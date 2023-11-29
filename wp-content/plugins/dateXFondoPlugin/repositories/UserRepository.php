<?php

namespace dateXFondoPlugin;

use mysqli;

class UserRepository
{

public static function getUserInfos(){
    $conn = new ConnectionFirstCity();
    $mysqli = $conn->connect();
    $sql = "SELECT * FROM DATE_user_form";
    $result = $mysqli->query($sql);
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    mysqli_close($mysqli);
    return $rows;
}
public static function checkNewTemplate(){
    $conn = new ConnectionFirstCity();
    $mysqli = $conn->connect();
    $sql = "SELECT * FROM DATE_template_fondo WHERE new=1";
    $result = $mysqli->query($sql);
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    mysqli_close($mysqli);
    return $rows;
}
function update_user_settings($request){
    if($request['citySelected'] == ''){
        $conn = new ConnectionFirstCity();
        $mysqli = $conn->connect();
        $sql = "UPDATE DATE_user_form SET titolo_ente=?, nome_soggetto_deliberante=?,responsabile=?,firma=?,riduzione_spesa=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("sssss",
            $request['titolo_ente'],
            $request['soggetto_deliberante'],
            $request['responsabile_documento'],
            $request['firma'],
            $request['riduzione_spesa']);
        $res = $stmt->execute();
    }
else{

    $url = DB_HOST . ":" . DB_PORT . "/";
    $username = DB_USER;
    $password = DB_PASSWORD;
    $dbname = 'c1date_'.$request['citySelected'];
    $mysqli = new mysqli($url, $username, $password, $dbname);
    $sql = "UPDATE DATE_user_form SET titolo_ente=?, nome_soggetto_deliberante=?,responsabile=?,firma=?,riduzione_spesa=?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("sssss",
        $request['titolo_ente'],
        $request['soggetto_deliberante'],
        $request['responsabile_documento'],
        $request['firma'],
        $request['riduzione_spesa']);
    $res = $stmt->execute();
}
    $mysqli->close();
    return $res;
}
/*function check_new_template($request){
    if($request['citySelected'] == ''){
        $conn = new ConnectionFirstCity();
        $mysqli = $conn->connect();
        $sql = "SELECT * FROM DATE_template_fondo WHERE new=1";
        $result = $mysqli->query($sql);
        $rows = $result->fetch_all(MYSQLI_ASSOC);
    }
else{

    $url = DB_HOST . ":" . DB_PORT . "/";
    $username = DB_USER;
    $password = DB_PASSWORD;
    $dbname = 'c1date_'.$request['citySelected'];
    $mysqli = new mysqli($url, $username, $password, $dbname);
    $sql = "SELECT * FROM DATE_template_fondo WHERE new=1";
    $result = $mysqli->query($sql);
    $rows = $result->fetch_all(MYSQLI_ASSOC);
}
    $mysqli->close();
    return $rows;
}*/



}