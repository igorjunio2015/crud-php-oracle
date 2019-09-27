<?php
require_once('../LoginSoftExpert.php');
require_once('../LoginSoftExpertDAO.php');
require_once('../config.php');

header('Content-type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    new Listar();
} else if ($method === 'POST') {
    new Incluir();
} else if ($method === 'PUT') {
    new Modificar();
}
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
        // set IDEMPRESA
        if (!isset($jsonBody["ID_EMPRESA"])) {
            array_push($message, "IDEMPRESA is required in body.");
        } else {
            $idEmpresa = $jsonBody["ID_EMPRESA"];
        }
        // set CHAPA
        if (!isset($jsonBody["CHAPA"])) {
            array_push($message, "CHAPA is required in body.");
        } else {
            $chapa = $jsonBody["CHAPA"];
        }
        // set LMOVER
        if (!isset($jsonBody["LMOVER"])) {
            array_push($message, "LMOVER is required in body.");
        } else {
            $lmover = $jsonBody["LMOVER"];
        }

        if (count($message) === 0) {
            // check values exists in database
            $checarExiste = $dao->procurar($conexao, array(
                'idEmpresa' => $idEmpresa, 'chapa' => $chapa, 'lmover' => $lmover
            ));
            if (!empty($checarExiste)) {
                echo json_encode(["Exists" => "The user already exists in the database."], JSON_PRETTY_PRINT);
            } else {
                $dao->inserir($conexao, array(
                    'idEmpresa' => $idEmpresa, 'chapa' => $chapa, 'lmover' => $lmover
                ));
            }
        } else {
            echo json_encode($message, JSON_PRETTY_PRINT);
        }
        return 'Incluir';
    }
}

class Modificar
{
    public function __construct()
    {
        $this->modificarUsuario();
    }
    public function modificarUsuario()
    {
        $db      = new Database();
        $dao     = new LoginSoftExpertDAO($db);
        $conexao = $db->getConection();
        $body = file_get_contents('php://input');
        $jsonBody = json_decode($body, true);

        if (isset($_GET['idempresa']) && isset($_GET['chapa']) && isset($_GET['lmover'])) {
            $dao->modificar(
                $conexao,
                array(
                    'idEmpresa' => $jsonBody['ID_EMPRESA'], 'chapa' => $jsonBody['CHAPA'], 'lmover' => $jsonBody['LMOVER']
                ),
                array(
                    'idEmpresaSelect' => $_GET['idempresa'], 'chapaSelect' => $_GET['chapa'], 'lmoverSelect' => $_GET['lmover']
                )
            );
        } else {
            print json_encode(["Error" => "Verify params url."], JSON_PRETTY_PRINT);
        }
        return 'Modificar';
    }
}
