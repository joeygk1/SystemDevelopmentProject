<?php
include_once "Model.php";
require_once 'config/config.php';

class Service extends Model
{
    public function getAllServices()
    {
        $conn = $this->connect();
        $stmt = $conn->prepare("SELECT * FROM services");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getServiceById($service_id)
    {
        $conn = $this->connect();
        $stmt = $conn->prepare("SELECT * FROM services WHERE service_id = ?");
        $stmt->execute([$service_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>