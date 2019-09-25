<?php
include("index.php");

$method = $_SERVER['REQUEST_METHOD'];

header('Content-type: application/json');
$body = file_get_contents('php://input');

if ($method === 'GET') {
    $path = explode('/', $_GET['chapa']);
    if ($path[0]) {
        $query = "select * from SYS.login_sistema sls 
              where sls.chapa like '%$path[0]%'";
        $statemen = oci_parse($conn, $query);
        oci_execute($statemen);
        while ($row = oci_fetch_array($statemen)) {
            echo $res_json = json_encode(
                [
                    "COD_EMPRESA" => $row[0], "CHAPA" => $row[1], "LOGIN_SAP" => $row[2]
                ],
                JSON_PRETTY_PRINT
            );
        }
        oci_close($conn);
    } else {
        echo json_encode("Path is required 'CHAPA'", JSON_PRETTY_PRINT);
    }
} else if ($method === 'POST') {
    $jsonBody = json_decode($body, true);

    $message = [];
    if (!isset($jsonBody["ID_EMPRESA"])) {
        $id_empresa = "";
        array_push($message, "IDEMPRESA is required in body.");
    } else {
        $id_empresa = $jsonBody["ID_EMPRESA"];
    }
    if (!isset($jsonBody["CHAPA"])) {
        $chapa = "";
        array_push($message, "CHAPA is required in body.");
    } else {
        $chapa = $jsonBody["CHAPA"];
    }
    if (!isset($jsonBody["LMOVER"])) {
        $lmover = "";
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
    oci_close($conn);
} else if ($method === 'PUT') {
    $param_id_empresa = explode('/', $_GET['id_empresa']);
    $param_chapa = explode('/', $_GET['chapa']);
    $param_lmover = explode('/', $_GET['lmover']);

    echo $param_chapa[0];

    $jsonBody = json_decode($body, true);

    $message = [];
    if (!isset($jsonBody["ID_EMPRESA"])) {
        $id_empresa = "";
        array_push($message, "IDEMPRESA is required in body.");
    } else {
        $id_empresa = $jsonBody["ID_EMPRESA"];
    }
    if (!isset($jsonBody["CHAPA"])) {
        $chapa = "";
        array_push($message, "CHAPA is required in body.");
    } else {
        $chapa = $jsonBody["CHAPA"];
    }
    if (!isset($jsonBody["LMOVER"])) {
        $lmover = "";
        array_push($message, "LMOVER is required in body.");
    } else {
        $lmover = $jsonBody["LMOVER"];
    }

    if (count($message) != 0) {
        echo json_encode($message, JSON_PRETTY_PRINT);
    } else { }
} else {
    echo json_encode("Method not permited", JSON_PRETTY_PRINT);
}
