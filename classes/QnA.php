<?php
define('__ROOT__', dirname(dirname(__FILE__)));
require_once('db/config.php');
class QnA{
    private $conn;
    public function __construct() {
        $this->connect();
    }
    private function connect() {
        $config = DATABASE;
        $options = array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,);
        try {
            $this->conn = new PDO('mysql:host=' . $config['HOST'] . ';dbname=' .$config['DBNAME'] . ';port=' . $config['PORT'], $config['USER_NAME'],
                $config['PASSWORD'], $options);
        } catch (PDOException $e) {
            die("Chyba pripojenia: " . $e->getMessage());
        }
    }

    public function insertQnA(){
        try {
            // Načítanie JSON súboru
            $data = json_decode(file_get_contents('data/datas.json'), true);
            $otazky = $data["otazky"];
            $odpovede = $data["odpovede"];
            // Vloženie otázok a odpovedí v rámci transakcie
            $this->conn->beginTransaction();
            $sqlCheck = "SELECT COUNT(*) AS count FROM qna WHERE otazka = :otazka AND odpoved = :odpoved";
            $statementCheck = $this->conn->prepare($sqlCheck);

            $sqlInsert = "INSERT INTO qna (otazka, odpoved) VALUES (:otazka, :odpoved)";
            $statementInsert = $this->conn->prepare($sqlInsert);

            for ($i = 0; $i < count($otazky); $i++) {
                // Kontrola, či takýto záznam už existuje
                $statementCheck->bindParam(':otazka', $otazky[$i]);
                $statementCheck->bindParam(':odpoved', $odpovede[$i]);
                $statementCheck->execute();
                $result = $statementCheck->fetch(PDO::FETCH_ASSOC);

                if ($result['count'] == 0) {
                    // Ak záznam neexistuje, vložim nový
                    $statementInsert->bindParam(':otazka', $otazky[$i]);
                    $statementInsert->bindParam(':odpoved', $odpovede[$i]);
                    $statementInsert->execute();
                }
            }
            $this->conn->commit();echo "Dáta boli vložené";
        }catch (Exception $e) {
            // Zobrazenie chybového hlásenia
            echo "Chyba pri vkladaní dát do databázy: " . $e->getMessage();
            $this->conn->rollback();
            // Vrátenie späť zmien v prípade chyby
        }
    }

    public function getQnA() {
        try {
            $sql = "SELECT DISTINCT otazka, odpoved FROM qna";
            $statement = $this->conn->query($sql);
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            throw new Exception("Chyba pri dostavani dát z databázy: " . $e->getMessage());
        }
        finally {
            // Uzatvorenie spojenia
            $this->conn = null;}
    }
}

?>