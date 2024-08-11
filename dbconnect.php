<?php 
    class Connection{
        function CheckConnect(){
            //Điều chỉnh phù hợp với server đang kết nối
            $hostName = "";
            $port = "";
            $databaseName = "";
            $userName = "";
            $passWord = "";

            try {
                $pdo = new PDO("mysql:host=$hostName;port=$port;dbname=$databaseName", $userName, $passWord);
                return $pdo;
            } catch (PDOException $ex) {
                echo "Failed to connect to MySQL: " . $ex->getMessage();
                die();
            }
        }
    }
?>