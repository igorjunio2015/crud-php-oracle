<?php
include("./config.php");

$db = new Database();
$conexao = $db->getConection();

$method = $_SERVER['REQUEST_METHOD'];

header('Content-type: application/json');
$body = file_get_contents('php://input');

$id_empresa = "";
$chapa = "";
$lmover = "";

if ($method === 'GET') {
    $path = explode('/', $_GET['chapa']);
    if ($path[0]) {
        $query = "select * from SYS.login_sistema sls 
              where sls.chapa like '%$path[0]%'";
        $statemen = oci_parse($conexao, $query);
        oci_execute($statemen);
        while ($row = oci_fetch_array($statemen)) {
            print json_encode(["COD_EMPRESA" => $row[0], "CHAPA" => $row[1], "LOGIN_SAP" => $row[2]], JSON_PRETTY_PRINT);
        }
    } else {
        print json_encode("Path is required 'CHAPA'", JSON_PRETTY_PRINT);
    }
} else if ($method === 'POST') {
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

        $statemen = oci_parse($conexao, $query_select);
        $result_sql = oci_execute($statemen);

        $row = oci_fetch_array($statemen);

        if ($row[1] === $chapa) {
            print json_encode(["Exists" => "Value is exists in database."], JSON_PRETTY_PRINT);
        } else {
            $statemen = oci_parse($conexao, ($query_insert . $query_select));
            $result_sql = oci_execute($statemen);
            if ($result_sql) {
                print json_encode(["Success" => "Data successfully inserted into database.."], JSON_PRETTY_PRINT);
            } else {
                print json_encode(["Error" => "Could not insert data into database, check."], JSON_PRETTY_PRINT);
            }
        }
    }
} else if ($method === 'PUT') {
    //method PUT    
    //https://api.com/users?id_empresa=X&chapa=XXXXX&lmover=XXXXX
    /*  {
            "ID_EMPRESA": "2",
            "CHAPA": "17594",
            "LMOVER": "igor.melo.teste"
        }
    */
    $jsonBody = json_decode($body, true);

    $message = [];
    // Param and Body = ID_EMPRESA
    if (!isset($jsonBody["ID_EMPRESA"]) || !isset($_GET["ID_EMPRESA"])) {
        array_push($message, "ID_EMPRESA is required in body.");
    } else {
        $param_id_empresa = $_GET['ID_EMPRESA'];
        $id_empresa = $jsonBody["ID_EMPRESA"];
    }
    // Param and Body = CHAPA
    if (!isset($jsonBody["CHAPA"]) || !isset($_GET["CHAPA"])) {
        array_push($message, "CHAPA is required in body and param.");
    } else {
        $param_chapa = $_GET['CHAPA'];
        $chapa = $jsonBody["CHAPA"];
    }
    // Param and Body = LMOVER
    if (!isset($jsonBody["LMOVER"]) || !isset($_GET["LMOVER"])) {
        array_push($message, "LMOVER is required in body and param.");
    } else {
        $param_lmover = $_GET['LMOVER'];
        $lmover = $jsonBody["LMOVER"];
    }

    if (count($message) != 0) {
        echo json_encode($message, JSON_PRETTY_PRINT);
    } else {
        $query_update = "update sys.login_sistema sls
                        set
                             sls.id_empresa ='$id_empresa'
                            ,sls.chapa ='$chapa'
                            ,sls.lmover ='$lmover'
                        where
                            (sls.id_empresa = '$param_id_empresa'
                        and
                             sls.chapa = '$param_chapa'
                        and
                             sls.lmover = '$param_lmover')";
        $query_select = "(
                        select 
                                *
                        from 
                                sys.login_sistema sls
                        where
                                (sls.id_empresa = '$param_id_empresa'
                        and
                                sls.chapa = '$param_chapa'
                        and
                                sls.lmover = '$param_lmover'))";


        $statemen = oci_parse($conexao, $query_select);
        $result_sql = oci_execute($statemen);

        $row = oci_fetch_array($statemen);

        if ($row[1] != $chapa) {
            print json_encode(["Not Exists" => "Value is not exists in database, check."], JSON_PRETTY_PRINT);
        } else {
            $statemen = oci_parse($conexao, ($query_update));
            $result_sql = oci_execute($statemen);
            if ($result_sql) {
                print json_encode(["Success" => "Data successfully updated into database.."], JSON_PRETTY_PRINT);
            } else {
                print json_encode(["Error" => "Could not updated data into database, check."], JSON_PRETTY_PRINT);
            }
        }
    }
} else {
    echo json_encode("Method not permited", JSON_PRETTY_PRINT);
}
oci_close($conexao);
