<?php
class LoginSistemaDAO
{
    private $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function procurar($conexao, $parametros)
    {
        try {
            if (!empty($parametros['chapa'])) {
                if (!empty($parametros['chapa'])) {
                    $sql = "
                    select 
                         sls.id_empresa
                        ,sls.chapa
                        ,sls.lmover
                    from 
                         SYS.login_sistema sls 
                    where 
                         sls.chapa = '" . $parametros['chapa'] . "'";
                }
                if (!empty($parametros['idEmpresa'])) {
                    $sql .= " and sls.id_empresa = " . $parametros['idEmpresa'] . "";
                }
                if (!empty($parametros['lmover'])) {
                    $sql .= " and sls.lmover = '" . $parametros['lmover'] . "'";
                }
                $query = oci_parse($conexao, $sql);

                if (!oci_execute($query)) {
                    $error  = oci_error($query);
                    $e      = implode(', ', $error);
                    oci_rollback($conexao);
                    $aux["SUCESSO"] = false;
                    $aux["RESPOSTA"] = "ERRO AO PESQUISAR" . $e;
                    return $aux;
                }
                $aux["SUCESSO"] = true;
                $arrResposta = array();

                $row = oci_fetch_array($query);
                if ($row) {
                    $arrResposta["COD_EMPRESA"] = $row[0];
                    $arrResposta["CHAPA"] = $row[1];
                    $arrResposta["LOGIN_MOVER"] = $row[2];
                    $aux["EXISTE"] = true;
                } else {
                    $aux["EXISTE"] = false;
                    $arrResposta["RESPOSTA"] = "NAO ENCONTRADO";
                }

                $aux["RESPOSTA"] = $arrResposta;
                return $aux;

                if (!oci_fetch_array($query)) { }
            } else {
                print json_encode("Check params, 'ID_EMPRESA', 'CHAPA', 'LMOVER' is required.", JSON_PRETTY_PRINT);
            }
        } catch (Exception $e) {
            return $e;
        }
    }

    public function inserir($conexao, $parametros)
    {
        try {
            $idEmpresa  = $parametros['idEmpresa'];
            $chapa      = $parametros['chapa'];
            $lmover     = $parametros['lmover'];

            $sql = "insert into sys.login_sistema (id_empresa, chapa, lmover)
                        select 
                             '$idEmpresa'
                            ,'$chapa'
                            ,'$lmover'
                        from
                             dual
                        where not exists
                        (
                        select 
                                *
                        from 
                                sys.login_sistema sls
                        where
                                (sls.id_empresa = '$idEmpresa'
                        and
                                sls.chapa = '$chapa'
                        and
                                sls.lmover = '$lmover'))";

            $query = oci_parse($conexao, $sql);

            if (!oci_execute($query)) {
                $error  = oci_error($query);
                $e      = implode(', ', $error);
                oci_rollback($conexao);
                $aux["SUCESSO"]     = false;
                $aux["INSERIDO"]    = false;
                $aux["RESPOSTA"]    = "ERRO AO INSERIR" . $e;
                return $aux;
            }
            $aux["SUCESSO"] = true;
            $aux["INSERIDO"] = true;
            $arrResposta = array(
                "MENSAGEM"  => "INSERIDO COM SUCESSO"
            );
            $aux["RESPOSTA"] = $arrResposta;
            return $aux;
        } catch (Exception $e) {
            return $e;
        }
    }

    public function modificar($conexao, $parametros, $parametrosSelect)
    {
        try {
            if (
                !empty($parametros['idEmpresa'] &&
                    !empty($parametros['chapa'] &&
                        !empty($parametros['lmover'] &&
                            !empty($parametrosSelect['idEmpresaSelect'] &&
                                !empty($parametrosSelect['chapaSelect'] &&
                                    !empty($parametrosSelect['lmoverSelect']))))))
            ) {
                $sql = "
                    update
                        sys.login_sistema sls
                    set 
                        sls.id_empresa = " . $parametros['idEmpresa'] . "
                        ,sls.chapa = '" . $parametros['chapa'] . "'
                        ,sls.lmover = '" . $parametros['lmover'] . "'
                    where
                            sls.id_empresa = " . $parametrosSelect['idEmpresaSelect'] . "
                    and
                            sls.chapa = '" . $parametrosSelect['chapaSelect'] . "'
                    and
                            sls.lmover = '" . $parametrosSelect['lmoverSelect'] . "'";
                $query = oci_parse($conexao, $sql);
                $result = oci_execute($query);

                if ($result) {
                    print json_encode(["Success" => "Data uptated in database."], JSON_PRETTY_PRINT);
                } else {
                    print json_encode(["Error" => "Could not uptated data in database, check."], JSON_PRETTY_PRINT);
                }
            } else {
                print json_encode([
                    "Params" => "Check 'idEmpresa', 'chapa', 'lmover'.",
                    "Body" => "Check 'ID_EMPRESA', 'CHAPA', 'LMOVER'."
                ], JSON_PRETTY_PRINT);
            }
        } catch (Exception $e) {
            return $e;
        }
    }
}
