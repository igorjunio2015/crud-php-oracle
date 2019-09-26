<?php
class LoginSoftExpertDAO
{
    private $db;
    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function inserir(LoginSoftExpert $LoginSoftExpert)
    {
        $idEmpresa = $LoginSoftExpert->getIdEmpresa();
        $chapa = $LoginSoftExpert->getChapa();
        $lmover = $LoginSoftExpert->getLmover();

        $query = "INSERT INTO produtos (idEmpresa, chapa, lmover) VALUES(?,?,?)";

        $stmt = mysqli_prepare($this->db->getConection(), $query);
        mysqli_stmt_bind_param($stmt, 'sss', $idEmpresa, $chapa, $lmover);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    public function procurar(LoginSoftExpert $LoginSoftExpert)
    {
        $chapa = $LoginSoftExpert->setChapa($_GET['chapa']);
        if ($chapa) {
            $sql = "select * from SYS.login_sistema sls 
              where sls.chapa like '%$chapa%'";
            $query = oci_parse($this->db->getConection(), $sql);
            oci_execute($query);
            while ($row = oci_fetch_array($query)) {
                print json_encode(["COD_EMPRESA" => $row[0], "CHAPA" => $row[1], "LOGIN_SAP" => $row[2]], JSON_PRETTY_PRINT);
            }
        } else {
            print json_encode("Path is required 'CHAPA'", JSON_PRETTY_PRINT);
        }
    }
}
