<?php

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
        $conexao = $db->getConection();
        $dao     = new LoginSoftExpertDAO($db);

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
