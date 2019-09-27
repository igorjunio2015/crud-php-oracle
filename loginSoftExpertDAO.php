<?php
class LoginSoftExpertDAO
{
    private $db;
    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function inserir($conexao, $parametros)
    {
        try {

        }catch(Exception $e){
            return $e;
        }
    }

    public function procurar($conexao, $parametros)
    {
        try {
            if (!empty($parametros['chapa'])) {
                if (!empty($parametros['chapa'])) {
                    $chapa = $parametros['chapa'];
                    $sql = "select 
                         sls.id_empresa
                        ,sls.chapa
                        ,sls.lmover
                    from 
                         SYS.login_sistema sls 
                    where 
                         sls.chapa like '%$chapa%'";
                }
                if (!empty($parametros['idEmpresa'])) {
                    $idEmpresa = $parametros['idEmpresa'];
                    $sql .= "and sls.id_empresa = $idEmpresa";
                }
                if (!empty($parametros['lmover'])) {
                    $lmover = $parametros['lmover'];
                    $sql .= "and sls.id_empresa = $lmover";
                }
                $query = oci_parse($conexao, $sql);
                oci_execute($query);
                while ($row = oci_fetch_array($query)) {
                    print json_encode([
                        "COD_EMPRESA" => $row[0],
                        "CHAPA" => $row[1],
                        "LOGIN_MOVER" => $row[2]
                    ], JSON_PRETTY_PRINT);
                }
                
            } else {
                print json_encode("Path is required 'CHAPA'", JSON_PRETTY_PRINT);
            }
        } catch (Exception $e) {
            return $e;
        }
    }
}
