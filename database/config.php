<?php
class Database
{
    private $host = "localhost:1522/xe";
    private $username = "sys";
    private $password = "asad2045";
    private $session_mode = OCI_SYSDBA;
    private $conexao = null;

    public function __construct()
    {
        $this->conecta();
    }
    public function getConection()
    {
        return $this->conexao;
    }

    private function conecta()
    {
        $this->conexao = oci_connect(
            $this->username,
            $this->password,
            $this->host,
            '',
            $this->session_mode
        );
        if (!$this->conexao) {
            $m = oci_error();
            echo $m['message'], "\n";
            exit;
        }
    }
}
