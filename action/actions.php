<?php
require_once('../LoginSoftExpert.php');
require_once('../LoginSoftExpertDAO.php');
require_once('../config.php');
header('Content-type: application/json');
//new Listar();
new Incluir();
class Listar
{
    public function __construct()
    {
        $this->procurarUsuario();
    }

    public function procurarUsuario()
    {
        $db      = new Database();
        $dao     = new LoginSoftExpertDAO($db);
        $conexao = $db->getConection();

        if (!isset($_GET['chapa'])) {
            print json_encode("Path is required 'CHAPA'", JSON_PRETTY_PRINT);
        } else {
            $dao->procurar($conexao, array(
                'chapa' => $_GET['chapa']
            ));
        }
        return 'Listar';
    }
}

class Incluir
{
    public function __construct()
    {
        $this->inserirUsuario();
    }
    public function inserirUsuario()
    {
        $db      = new Database();
        $dao     = new LoginSoftExpertDAO($db);
        $conexao = $db->getConection();
        $body = file_get_contents('php://input');
        $jsonBody = json_decode($body, true);

        $message = [];
        if (!isset($jsonBody["ID_EMPRESA"])) {
            array_push($message, "IDEMPRESA is required in body.");
        } else {
            $idEmpresa = $jsonBody["ID_EMPRESA"];
        }

        if (!isset($jsonBody["CHAPA"])) {
            array_push($message, "CHAPA is required in body.");
        } else {
            $chapa = $jsonBody["CHAPA"];
        }

        if (!isset($jsonBody["LMOVER"])) {
            array_push($message, "LMOVER is required in body.");
        } else {
            $lmover = $jsonBody["LMOVER"];
        }

        if (count($message) != 0) {
            echo json_encode($message, JSON_PRETTY_PRINT);
        } else {
            $dao->inserir($conexao, array(
                'idEmpresa' => $idEmpresa, 'chapa' => $chapa, 'lmover' => $lmover
            ));
        }
        return 'Incluir';
    }
}
