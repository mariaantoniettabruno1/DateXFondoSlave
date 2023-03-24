<?php

namespace dateXFondoPlugin;
use mysqli;

/**
classe per la connessione al db di dateXFondo al comune di Rubiana
 */

class ConnectionFirstCity
{
    private $url;
    private $username;
    private $password;
    private $dbname;

    /**
     * @return string
     */
    public function getDbname(): string
    {
        return $this->dbname;
    }



    public function __construct()
    {
        $id = my_get_current_user_id()[0];
        $conn = new Connection();
        $mysqli = $conn->connect();
        $sql = "SELECT db FROM DATE_users WHERE id_user=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $id);
        $res = $stmt->execute();
        $res = $stmt->get_result();
        $db_name = $res->fetch_all(MYSQLI_ASSOC);
        $this->url = DB_HOST . ":" . DB_PORT . "/";
        $this->username = DB_USER;
        $this->password = DB_PASSWORD;
        $this->dbname = $db_name[0]['db'];
    }

    function connect()
    {
        $conn = new mysqli($this->url, $this->username, $this->password, $this->dbname);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        return $conn;
    }
    public static function create_connection(){
        echo DB_HOST."<br>";
        return new ConnectionFirstCity();
    }


}