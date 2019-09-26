<?
include_once("api-test/loginSoftExpert.php");

class LoginSoftExpertDAO
{
    function inserir($conexao, $LoginSoftExpert)
    {
        //method POST    
        //https://api.com/users
        /*  {
            "ID_EMPRESA": "2",
            "CHAPA": "17594",
            "LMOVER": "igor.melo.teste"
        }
    */
        header('Content-type: application/json');
        $body = file_get_contents('php://input');

        $jsonBody = json_decode($body, true);

        $message = [];
        if (!isset($jsonBody["ID_EMPRESA"])) {
            array_push($message, "IDEMPRESA is required in body.");
        } else {
            $id_empresa = $jsonBody["ID_EMPRESA"];
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
            $query_insert = "insert into sys.login_sistema (id_empresa, chapa, lmover)
                        select 
                             '$id_empresa'
                            ,'$chapa'
                            ,'$lmover'
                        from
                             dual
                        where not exists";
            $query_select = "(
                        select 
                                *
                        from 
                                sys.login_sistema sls
                        where
                                (sls.id_empresa = '$id_empresa'
                        and
                                sls.chapa = '$chapa'
                        and
                                sls.lmover = '$lmover'))";

            $statemen = oci_parse($conn, $query_select);
            $result_sql = oci_execute($statemen);

            $row = oci_fetch_array($statemen);

            if ($row[1] === $chapa) {
                print json_encode(["Exists" => "Value is exists in database."], JSON_PRETTY_PRINT);
            } else {
                $statemen = oci_parse($conn, ($query_insert . $query_select));
                $result_sql = oci_execute($statemen);
                if ($result_sql) {
                    print json_encode(["Success" => "Data successfully inserted into database.."], JSON_PRETTY_PRINT);
                } else {
                    print json_encode(["Error" => "Could not insert data into database, check."], JSON_PRETTY_PRINT);
                }
            }
        }
    }
    function pesquisar($conexao, $chapa)
    {
        $chapa = $_GET['chapa'];
        if ($chapa) {
            $sql =   "select 
                            sls.id_empresa
                           ,sls.chapa
                           ,sls.lmover 
                        from 
                            SYS.login_sistema sls 
                        where 
                            sls.chapa like '%$chapa%'";

            $query = oci_parse($conexao, $sql);
            oci_execute($query);
            while ($row = oci_fetch_array($query)) {
                return json_encode(
                    [
                        "COD_EMPRESA" => $row[0], "CHAPA" => $row[1], "LOGIN_MOVER" => $row[2]
                    ],
                    JSON_PRETTY_PRINT
                );
            }
        } else {
            return json_encode("Path is required 'CHAPA'", JSON_PRETTY_PRINT);
        }
    }
}
