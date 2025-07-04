<? 
$host = "localhost";
$username = "root";
$password = "";
$dbname = "username";
try {
    $pdo = new PDO(mysqlhost =$host, dbname=$dbname, $username, $password);
    $pdo>set_attribute(pdo::ATTR_ERRMODE, pdo::ERRMODE_EXCEPTION  );
    {
        catch(PDOException $e)

    }
    die("Erro de Conexão:" .$e ->getMessage());
}
?>