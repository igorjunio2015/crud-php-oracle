<?php
require_once('../LoginSistema.php');
require_once('../LoginSistemaDAO.php');
require_once('../database/config.php');

header('Content-type: application/json');

$db      = new Database();
$conexao = $db->getConection();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $listar = new Listar();
    $listar->execute($conexao);
} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $incluir = new Incluir();
    $incluir->execute($conexao);
} else if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $modificar = new Modificar();
    $modificar->execute($conexao);
}

class Listar
{
    public function execute($conexao)
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method === 'GET') {
            $db      = new Database();
            $conexao = $db->getConection();

            $loginSistemaDAO     = new LoginSistemaDAO($db);
            if (!isset($_GET['chapa'])) {
                $message = json_encode(["response" => "Path is required 'CHAPA'"]);
            } else {
                $message = json_encode($loginSistemaDAO->procurar($conexao, array(
                    'chapa' => $_GET['chapa']
                )));
            }
        } else {
            $message = json_encode(["Error method" => "Method not permited, please use GET."], JSON_PRETTY_PRINT);
        }
        echo $message;
    }
}

class Incluir
{
    public function execute($conexao)
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method === 'POST') {
            $db      = new Database();
            $loginSistemaDAO     = new LoginSistemaDAO($db);

            $body = file_get_contents('php://input');
            $jsonBody = json_decode($body, true);
            $message = [];

            if ((!isset($jsonBody["ID_EMPRESA"]))
                || (!isset($jsonBody["CHAPA"]))
                || (!isset($jsonBody["LMOVER"]))
            ) {
                $message["MENSAGEM"] = "NAO FOI POSSIVEL PROSSEGUIR, FALTA DADOS";
            }
            // set IDEMPRESA
            if (!isset($jsonBody["ID_EMPRESA"])) {
                $message["CORPO"]["IDEMPRESA"] = "INFORMAR VALOR PARA 'IDEMPRESA' NO BODY";
            } else {
                $idEmpresa = $jsonBody["ID_EMPRESA"];
            }
            // set CHAPA
            if (!isset($jsonBody["CHAPA"])) {
                $message["CORPO"]["CHAPA"] = "INFORMAR VALOR PARA 'CHAPA' NO BODY";
            } else {
                $chapa = $jsonBody["CHAPA"];
            }
            // set LMOVER
            if (!isset($jsonBody["LMOVER"])) {
                $message["CORPO"]["LMOVER"] = "INFORMAR VALOR PARA 'LMOVER' NO BODY";
            } else {
                $lmover = $jsonBody["LMOVER"];
            }
            // contador de erro
            if (count($message) === 0) {
                // check values exists in database
                $checarExiste = $loginSistemaDAO->procurar($conexao, array(
                    'idEmpresa' => $idEmpresa, 'chapa' => $chapa, 'lmover' => $lmover
                ));
                if ($checarExiste["EXISTE"] === false) {
                    $inserido = $loginSistemaDAO->inserir($conexao, array(
                        'idEmpresa' => $idEmpresa, 'chapa' => $chapa, 'lmover' => $lmover
                    ));
                    echo json_encode($inserido);
                } else {
                    $erroInserir["INSERIDO"] = false;
                    $erroInserir["MENSAGEM"] = "DADOS JA EXISTENTE";
                    $erroInserir["DADOS"] = $checarExiste["RESPOSTA"];
                    echo json_encode($erroInserir);
                }
            } else {
                echo json_encode($message, JSON_PRETTY_PRINT);
            }
        } else {
            $error = ["METODO" => "METODO NAO PERMITIDO, USAR POST PARA ESSA REQUISICAO."];
            echo json_encode($error);
        }
        return 'Incluir';
    }
}

class Modificar
{
    public function execute($conexao)
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method === 'PUT') {
            $db      = new Database();
            $loginSistemaDAO     = new LoginSistemaDAO($db);

            $body = file_get_contents('php://input');
            $jsonBody = json_decode($body, true);

            $message = [];

            if ((!isset($jsonBody["ID_EMPRESA"]))
                || (!isset($jsonBody["CHAPA"]))
                || (!isset($jsonBody["LMOVER"]))
            ) {
                $message["MENSAGEM"] = "NAO FOI POSSIVEL PROSSEGUIR, FALTA DADOS";
            }
            // set IDEMPRESA
            if (!isset($jsonBody["ID_EMPRESA"])) {
                $message["CORPO"]["IDEMPRESA"] = "INFORMAR VALOR PARA 'IDEMPRESA' NO BODY";
            } else {
                $idEmpresa = $jsonBody["ID_EMPRESA"];
            }
            // set CHAPA
            if (!isset($jsonBody["CHAPA"])) {
                $message["CORPO"]["CHAPA"] = "INFORMAR VALOR PARA 'CHAPA' NO BODY";
            } else {
                $chapa = $jsonBody["CHAPA"];
            }
            // set LMOVER
            if (!isset($jsonBody["LMOVER"])) {
                $message["CORPO"]["LMOVER"] = "INFORMAR VALOR PARA 'LMOVER' NO BODY";
            } else {
                $lmover = $jsonBody["LMOVER"];
            }
            // contador de erro

            if (isset($_GET['idempresa']) && isset($_GET['chapa']) && isset($_GET['lmover'])) {
                $checarExiste = $loginSistemaDAO->procurar(
                    $conexao,
                    array(
                        'idEmpresa' => $jsonBody['ID_EMPRESA'], 'chapa' => $jsonBody['CHAPA'], 'lmover' => $jsonBody['LMOVER']
                    )
                );
                if (!empty($checarExiste)) {
                    $loginSistemaDAO->modificar(
                        $conexao,
                        array(
                            'idEmpresa' => $jsonBody['ID_EMPRESA'], 'chapa' => $jsonBody['CHAPA'], 'lmover' => $jsonBody['LMOVER']
                        ),
                        array(
                            'idEmpresaSelect' => $_GET['idempresa'], 'chapaSelect' => $_GET['chapa'], 'lmoverSelect' => $_GET['lmover']
                        )
                    );
                } else {
                    echo json_encode(["Not Exists" => "The user not exists in the database."], JSON_PRETTY_PRINT);
                }
            } else {
                print json_encode(["Error" => "Verify params url."], JSON_PRETTY_PRINT);
            }
        } else {
            echo json_encode(["Error method" => "Method not permited, please use PUT."], JSON_PRETTY_PRINT);
        }
        return 'Modificar';
    }
}
