<?php
require_once('../LoginSoftExpert.php');
require_once('../LoginSoftExpertDAO.php');
require_once('../config.php');
new Listar();
class Listar
{
    public function __construct()
    {
        $this->procurarUsuario();
    }

    public function procurarUsuario()
    {
        require_once('../LoginSoftExpert.php');
        require_once('../LoginSoftExpertDAO.php');
        require_once('../config.php');

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
    public function inserirUsuario()
    {
        require_once('../LoginSoftExpert.php');
        require_once('../LoginSoftExpertDAO.php');
        require_once('../config.php');

        $db      = new Database();
        $dao     = new LoginSoftExpertDAO($db);
        $conexao = $db->getConection();
        $c       = 0;

        if (!isset($_GET['chapa'])) {
            $c += 1;
            print json_encode("Path is required 'CHAPA'.", JSON_PRETTY_PRINT);
        }
        if (!isset($_GET['idempresa'])) {
            $c += 1;
            print json_encode("Path is required 'IDEMPRESA'.", JSON_PRETTY_PRINT);
        }
        if (!isset($_GET['lmover'])) {
            $c += 1;
            print json_encode("Path is required 'LMOVER'.", JSON_PRETTY_PRINT);
        }
        if ($c === 0) {
            $dao->inserir($conexao, array(
                'idempresa' => $_GET['idempresa'], 'chapa' => $_GET['chapa'], 'lmover' => $_GET['lmover']
            ));
        }
        return 'Incluir';
    }
}
