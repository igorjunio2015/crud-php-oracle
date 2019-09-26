<?php
class LoginSoftExpertDAO
{
    private $db;
    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function add(Produto $produto)
    {
        $nome = $produto->getNome();
        $descricao = $produto->getDescricao();
        $preco = $produto->getPreco();

        $query = "INSERT INTO produtos (nome, descricao, preco) VALUES(?,?,?)";
        $stmt = mysqli_prepare($this->db->getConection(), $query);
        mysqli_stmt_bind_param($stmt, 'sss', $nome, $descricao, $preco);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}
