<?php
require_once explode('\js', getcwd())[0]. '\vendor\autoload.php';
include "config.php";

class reposit
{

    private $ip = SERVIDOR;
    private $user = USUARIO;
    private $pass = SENHA;
    private $database = BANCO;
    private $porta = PORTA;
    //private $socket = SOCKET;
    private $sqlconnect;
    const SCHEMA = 'dbo';


    function AbreConexao($banco)
    {
        switch ($banco) {
            case 'mysql':
            case 'sql':
                try {
                    $dsn = "sqlsrv:server=$this->ip;database=$this->database;";
                    $pdo = new PDO($dsn, $this->user, $this->pass);
                    $pdo->setAttribute(
                        PDO::ATTR_ERRMODE,
                        PDO::ERRMODE_EXCEPTION
                    );
                    $pdo->setAttribute(
                        PDO::ATTR_DEFAULT_FETCH_MODE,
                        PDO::FETCH_ASSOC
                    );
                    $pdo->setAttribute(
                        PDO::SQLSRV_ATTR_FETCHES_NUMERIC_TYPE,
                        true
                    );
                    $this->sqlconnect = $pdo;
                    $ok = 1;
                    setlocale(LC_ALL, "pt_BR");
                } catch (Exception $e) {
                    echo "<p>Conexão falhou !!!.</p>\n";
                }
            case 'oracle':
            case 'cache':
        }
        return;
    }

    function FechaConexao()
    {
        $this->sqlconnect = null;
    }

    // executa store procedure
    function Execprocedure($config)
    {
        $conf = explode("|", $config);

        try {
            $this->AbreConexao("sql");
            $pstmt = $this->sqlconnect->prepare('SET NOCOUNT ON; EXEC '
                // . '[' . self::SCHEMA . ']. '
                . $conf[0]);
            $pstmt->execute();

            if ($pstmt->rowCount() >= 0) {
                $pstmt->nextRowset();
            } else {
                if ($pstmt->columnCount() === 0) {
                    $pstmt->nextRowset();
                }
            }

            $result = $pstmt->fetchAll();
            $this->FechaConexao();
            return $result;
        } catch (Exception $e) {
            $GLOBALS["error"] = $e->getMessage();
            return 0;
        }
    }

    // executa store procedure
    function ExecprocedureSemSchema($config) //fecha no gerenc de tarefas do windows ai o cgi
    {
        $conf = explode("|", $config);

        try {
            $this->AbreConexao("sql");
            $pstmt = $this->sqlconnect->prepare('SET NOCOUNT ON; EXEC '
                . $conf[0]);
            $pstmt->execute();
            $result = $pstmt->fetchAll();
            $this->FechaConexao();
            return $result;
        } catch (Exception $e) {
            $GLOBALS["error"] = $e->getMessage();
            return 0;
        }
    }

    // Retorna todas as colunas
    function SelectAll($config)
    {
        $conf = explode("|", $config);
        $conf[0] = $this->anti_injection($conf[0]);

        try {
            $this->AbreConexao("sql");
            $pstmt = $this->sqlconnect->prepare('SELECT * FROM '
                . '[' . self::SCHEMA . ']. '
                . $conf[0]);
            $pstmt->execute();
            $result = $pstmt->fetchAll();
            $this->FechaConexao();
            return $result;
        } catch (Exception $e) {
            $GLOBALS["error"] = $e->getMessage();
            return 0;
        }
    }

    // Retorna todas as colunas de acordo com uma condição
    function RunQuery($sql)
    {

        try {
            $this->AbreConexao("sql");
            $pstmt = $this->sqlconnect->prepare($sql);
            $pstmt->execute();
            $result = $pstmt->fetchAll();
            $GLOBALS["rows"] = $pstmt->rowCount();
            $this->FechaConexao();
            return $result;
        } catch (Exception $e) {
            $GLOBALS["error"] = $e->getMessage();
            return 0;
        }
    }

    // Retorna todas as colunas
    function SelectAllJoinDistinct($config)
    {

        $conf = explode("|", $config);
        $conf[2] = $this->anti_injection($conf[2]);
        $sql = "SELECT distinct(" . $conf[3] . "),* FROM "
            . '[' . self::SCHEMA . '] .' . $conf[0] . " a ,"
            . $conf[1] . " b WHERE " . $conf[2];

        try {
            $this->AbreConexao("sql");
            $pstmt = $this->sqlconnect->prepare($sql);
            $pstmt->execute();
            $result = $pstmt->fetchAll();
            $GLOBALS["rows"] = $pstmt->rowCount();
            $this->FechaConexao();
            return $result;
        } catch (Exception $e) {
            $GLOBALS["error"] = $e->getMessage();
            return 0;
        }
    }

    // Retorna todas as colunas
    function SelectAllCampos($config)
    {
        $conf = explode("|", $config);
        $sql = "select " . $conf[1] . " FROM " . '[' . self::SCHEMA . ']. ' . $conf[0]
            . " LIMIT $conf[2],$conf[3]";

        try {
            $this->AbreConexao("sql");
            $pstmt = $this->sqlconnect->prepare($sql);
            $pstmt->execute();
            $result = $pstmt->fetchAll();
            $GLOBALS["rows"] = $pstmt->rowCount();
            $this->FechaConexao();
            return $result;
        } catch (Exception $e) {
            $GLOBALS["error"] = $e->getMessage();
            return 0;
        }
    }

    // Retorna colunas pre-fixadas
    function SelectCampos($config)
    {
        $conf = explode("|", $config);
        $sql = "select " . $conf[1] . " FROM " . '[' . self::SCHEMA . ']. ' . $conf[0] . "";

        try {
            $this->AbreConexao("sql");
            $pstmt = $this->sqlconnect->prepare($sql);
            $pstmt->execute();
            $result = $pstmt->fetchAll();
            $GLOBALS["rows"] = $pstmt->rowCount();
            $this->FechaConexao();
            return $result;
        } catch (Exception $e) {
            $GLOBALS["error"] = $e->getMessage();
            return 0;
        }
    }

    // Retorna colunas pre-fixadas com clausula WHERE
    function SelectCamposCond($config)
    {
        $conf = explode("|", $config);
        $sql = "SELECT " . $conf[1] . " FROM " . '[' . self::SCHEMA . ']. ' . $conf[0]
            . " WHERE " . $conf[2];

        try {
            $this->AbreConexao("sql");
            $pstmt = $this->sqlconnect->prepare($sql);
            $pstmt->execute();
            $result = $pstmt->fetchAll();
            $GLOBALS["rows"] = $pstmt->rowCount();
            $this->FechaConexao();
            return $result;
        } catch (Exception $e) {
            $GLOBALS["error"] = $e->getMessage();
            return 0;
        }
    }

    // Retorna de acordo com as condições passadas
    function SelectCondTrue($config)
    {
        $conf = explode("|", $config);
        $args = $conf[1];
        $args = $this->anti_injection($args);
        $sql = "SELECT * FROM " . '[' . self::SCHEMA . ']. ' . $conf[0] . " WHERE "
            . $conf[1];

        try {
            $this->AbreConexao("sql");
            $pstmt = $this->sqlconnect->prepare($sql);
            $pstmt->execute();
            $result = $pstmt->fetchAll();
            $GLOBALS["rows"] = $pstmt->rowCount();
            $this->FechaConexao();
            return $result;
        } catch (Exception $e) {
            $GLOBALS["error"] = $e->getMessage();
            return 0;
        }
    }

    function anti_injection($sql)
    {
        //return $sql;
        //$sql = preg_replace("/(from|select|insert|delete|where|drop table|show tables|#|\|--|\\\\)/", "", $sql);
        $sql
            = preg_replace(
                "/(from|select|insert|delete|where|drop table|show tables|\|--|\\\\)/",
                "",
                $sql
            );
        $sql = trim($sql);
        return $sql;
    }

    // Retorna todas as colunas de acordo com uma condição
    function SelectCond($config)
    {
        $conf = explode("|", $config);
        $conf[1] = $this->anti_injection($conf[1]);
        $sql = "SELECT * FROM " . '[' . self::SCHEMA . ']. ' . $conf[0] . " WHERE "
            . $conf[1] . " ";

        try {
            $this->AbreConexao("sql");
            $pstmt = $this->sqlconnect->prepare($sql);
            $pstmt->execute();
            $result = $pstmt->fetchAll();
            $GLOBALS["rows"] = $pstmt->rowCount();
            $this->FechaConexao();
            return $result;
        } catch (Exception $e) {
            $GLOBALS["error"] = $e->getMessage();
            return 0;
        }
    }

    // Retorna todas as colunas de acordo com uma condição
    function TestaExiste($config)
    {
        $conf = explode("|", $config);
        $conf[1] = $this->anti_injection($conf[1]);
        $sql = "SELECT * FROM " . '[' . self::SCHEMA . ']. ' . $conf[0] . " WHERE "
            . $conf[1] . " ";

        try {
            $this->AbreConexao("sql");
            $pstmt = $this->sqlconnect->prepare($sql);
            $pstmt->execute();
            $result = $pstmt->fetchAll();
            $GLOBALS["rows"] = $pstmt->rowCount();
            $this->FechaConexao();
            return $result;
        } catch (Exception $e) {
            $GLOBALS["error"] = $e->getMessage();
            return 0;
        }
    }

    // Retorna todas as colunas de acordo com uma condição
    function SelectCondJoin($config)
    {
        $conf = explode("|", $config);
        $conf[2] = $this->anti_injection($conf[2]);
        $sql = "SELECT * FROM " . '[' . self::SCHEMA . ']. ' . $conf[0] . " a, " . '['
            . self::SCHEMA . ']. ' . $conf[1] . " b WHERE "
            . $conf[2] . " ";

        try {
            $this->AbreConexao("sql");
            $pstmt = $this->sqlconnect->prepare($sql);
            $pstmt->execute();
            $result = $pstmt->fetchAll();
            $GLOBALS["rows"] = $pstmt->rowCount();
            $this->FechaConexao();
            return $result;
        } catch (Exception $e) {
            $GLOBALS["error"] = $e->getMessage();
            return 0;
        }
    }

    // faz update em uma tabela
    function Update($config)
    {
        $conf = explode("|", $config);
        $listacampos = str_replace(";", ",", $conf[1]) ?: $conf[1];
        $listacampos = $this->anti_injection($listacampos);
        $conf[2] = $this->anti_injection($conf[2]);
        $sql = "UPDATE "
           // . '[' . self::SCHEMA . ']. '
            . $conf[0] . " SET " . $listacampos
            . " WHERE "
            . $conf[2] . " ";

        try {
            $this->AbreConexao("sql");
            $this->sqlconnect->beginTransaction();
            $pstmt = $this->sqlconnect->prepare($sql);
            $pstmt->execute();
            $GLOBALS["rows"] = $pstmt->rowCount();
            $this->sqlconnect->commit();
            $this->FechaConexao();
            return $pstmt->rowCount();
        } catch (Exception $e) {
            $GLOBALS["error"] = $e->getMessage();
            return 0;
        }
    }

    // faz insert em uma tabela
    function Insert($config)
    {
        $this->AbreConexao("sql"); // Abrimos a conexão
        $conf = explode("|", $config); // Aqui explodimos e jogamos em array
        $listacampos = $conf[1];
        $listavalores = $conf[2];
        $listacampos = $this->anti_injection($listacampos);
        $conf[2] = $this->anti_injection($conf[2]);

        $conf = explode("|", $config);
        $listacampos = $conf[1];
        $listavalores = $conf[2];
        $listacampos = $this->anti_injection($listacampos);
        $conf[2] = $this->anti_injection($conf[2]);

        $sql = "insert into " . '[' . self::SCHEMA . ']. ' . $conf[0] . "(" . $listacampos
            . ") values("
            . $conf[2] . ") ";

        try {
            $this->AbreConexao("sql");
            $this->sqlconnect->beginTransaction();
            $pstmt = $this->sqlconnect->prepare($sql);
            $pstmt->execute();
            $GLOBALS["rows"] = $pstmt->rowCount();
            $this->sqlconnect->commit();
            $this->FechaConexao();
            return 1;
        } catch (Exception $e) {
            $GLOBALS["error"] = $e->getMessage();
            return 0;
        }
    }

    // faz delete em uma tabela
    function Delete($config)
    {
        $conf = explode("|", $config); // Aqui explodimos e jogamos em array
        $conf[1] = $this->anti_injection($conf[1]);
        $sql = "delete from " . '[' . self::SCHEMA . ']. ' . $conf[0] . " WHERE " . $conf[1]
            . " ";

        try {
            $this->AbreConexao("sql");
            $pstmt = $this->sqlconnect->prepare($sql);
            $pstmt->execute();
            $result = $pstmt->fetchAll();
            $GLOBALS["rows"] = $pstmt->rowCount();
            $this->FechaConexao();
            return $result;
        } catch (Exception $e) {
            $GLOBALS["error"] = $e->getMessage();
            return 0;
        }
    }

    function PossuiPermissao($config)
    {

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $usuario = $_SESSION['login'];
        $grupo = $_SESSION['grupo'];
        
        $conf = explode("|", $config); // Aqui explodimos e jogamos em array

        $possuiPermissao = 0;
        $result = $this->SelectCondTrue("usuario| login='" . $usuario
            . "' and ativo=1");
        if ($row = $result[0]) {
            $codigoUsuario = $row['codigo'];
            $tipoUsuario = $row['tipoUsuario'];

            if ($grupo) {
                $sql= "SELECT F.CODIGO FROM Ntl.funcionalidade F WHERE (f.nome = '". $conf[0] . "' OR f.nome = '" . $conf[1] . "') ";
                $sql = $sql . " EXCEPT ";
                $sql = $sql . " SELECT usuf.funcionalidade FROM Ntl.usuarioGrupoFuncionalidade usuf INNER JOIN Ntl.funcionalidade faux on faux.codigo = usuf.funcionalidade ";
                $sql = $sql . " AND (faux.nome = '" . $conf[0] . "' OR faux.nome = '" . $conf[1] . "') ";
                $sql = $sql . " WHERE usuf.usuarioGrupo=" . $grupo . " ";

                $result = $this->RunQuery($sql);
                if (empty($result)) {
                    $possuiPermissao = 1;
                }
                return $possuiPermissao;
            }

            if ($tipoUsuario === "C") {
                $sql= "SELECT F.CODIGO FROM Ntl.funcionalidade F WHERE (f.nome = '". $conf[0] . "' OR f.nome = '" . $conf[1] . "') ";
                $sql = $sql . " EXCEPT ";
                $sql = $sql . " SELECT usuf.funcionalidade FROM Ntl.usuarioFuncionalidade usuf INNER JOIN Ntl.funcionalidade faux on faux.codigo = usuf.funcionalidade ";
                $sql = $sql . " AND (faux.nome = '" . $conf[0] . "' OR faux.nome = '" . $conf[1] . "') ";
                $sql = $sql . " WHERE usuf.usuario=" . $codigoUsuario . " ";

                $result = $this->RunQuery($sql);
                if (empty($result)) {
                    $possuiPermissao = 1;
                }
            }
            if ($tipoUsuario === "S") {
                $possuiPermissao = 1;
            }
        }
        return $possuiPermissao;
    }
}
