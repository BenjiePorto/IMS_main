<?php

session_start();

class SystemConfig
{
   private $conn;
   private $smtp_email;
   private $smtp_password;

   public function __construct()
   {
      $database = new Database();
      $this->conn = $database->dbConnection();

      $stmt = $this->runQuery("SELECT * FROM email_config");
      $stmt->execute();
      $email_config = $stmt->fetch(PDO::FETCH_ASSOC);

      $this->smtp_email = $email_config['email'];
      $this->smtp_password = $email_config['password'];

   }

   public function getSmtpEmail() {
      return $this->smtp_email;
   }

   public function getSmtpPassword(){
      return $this->smtp_password;
   }


   public function runQuery($sql){
      $stmt = $this->conn->prepare($sql);
      return $stmt;
   }
}