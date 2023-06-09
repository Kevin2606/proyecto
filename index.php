<?php
$acciones = [
    "✅" => function () {
        enviarDatos();
    },
    "📝" => function () {
        editarDatos();
    },
    "❌" => function () {
        eliminarDatos();
    },
    "🔍" => function ($cedula) {
        buscarDatos($cedula);
    }
];


function credencialesHeader($data, $method)
{
    $credenciales["http"]["method"] = $method;
    if ($method == "DELETE") return stream_context_create($credenciales);
    $credenciales["http"]["header"] = "Content-Type: application/json";
    $credenciales["http"]["content"] = $data;
    return stream_context_create($credenciales);
}

function enviarDatos()
{
    $data = [
        "nombre" => $_POST["nombre"],
        "apellido" => $_POST["apellido"],
        "direccion" => $_POST["direccion"],
        "horario" => $_POST["horario"],
        "team" => $_POST["team"],
        "trainer" => $_POST["trainer"],
        "edad" => $_POST["edad"],
        "email" => $_POST["email"],
        "cedula" => $_POST["cedula"]
    ];
    $data = json_encode($data);
    $config = credencialesHeader($data, "POST");
    file_get_contents("https://645fecb5fe8d6fb29e2902d5.mockapi.io/api/php", false, $config);
}
function editarDatos()
{
    if (!isset($_POST["cedula"])){
        echo "No se ha ingresado una cedula";
        return;
    }
    $cedula = $_POST["cedula"];
    $data = file_get_contents("https://645fecb5fe8d6fb29e2902d5.mockapi.io/api/php/?cedula=" . $cedula);
    $data = json_decode($data, true);
    if (count($data) == 0) {
        echo "No se ha encontrado el usuario";
        return;
    }
    $id = $data[0]["id"];
    $data = [
        "nombre" => $_POST["nombre"],
        "apellido" => $_POST["apellido"],
        "direccion" => $_POST["direccion"],
        "horario" => $_POST["horario"],
        "team" => $_POST["team"],
        "trainer" => $_POST["trainer"],
        "edad" => $_POST["edad"],
        "email" => $_POST["email"],
        "cedula" => $_POST["cedula"]
    ];
    $data = json_encode($data);
    $config = credencialesHeader($data, "PUT");
    file_get_contents("https://645fecb5fe8d6fb29e2902d5.mockapi.io/api/php/" . $id, false, $config);
}
function eliminarDatos()
{
    if (!isset($_POST["cedula"])){
        echo "No se ha ingresado una cedula";
        return;
    }
    $cedula = $_POST["cedula"];
    $data = file_get_contents("https://645fecb5fe8d6fb29e2902d5.mockapi.io/api/php/?cedula=" . $cedula);
    $data = json_decode($data, true);
    if (count($data) == 0) {
        echo "No se ha encontrado el usuario";
        return;
    }
    $config = credencialesHeader($data, 'DELETE');
    file_get_contents("https://645fecb5fe8d6fb29e2902d5.mockapi.io/api/php/" . $data[0]["id"], false, $config);
}
function buscarDatos($cedula)
{
    $data = file_get_contents("https://645fecb5fe8d6fb29e2902d5.mockapi.io/api/php/?cedula=" . $cedula);
    $data = json_decode($data, true);
    $_SESSION["nombre"] = $data[0]["nombre"];
    $_SESSION["apellido"] = $data[0]["apellido"];
    $_SESSION["direccion"] = $data[0]["direccion"];
    $_SESSION["horario"] = $data[0]["horario"];
    $_SESSION["team"] = $data[0]["team"];
    $_SESSION["trainer"] = $data[0]["trainer"];
    $_SESSION["edad"] = $data[0]["edad"];
    $_SESSION["email"] = $data[0]["email"];
    $_SESSION["cedula"] = $data[0]["cedula"];
}
session_start();
if (isset($_POST['acciones'])) {
    if ($_POST['acciones'] == "🔍") {
        buscarDatos($_POST['cedula']);
        header("Location: index.php");
        exit();
    }
    else {
        $acciones[$_POST['acciones']]($_POST['cedula']);
        session_unset();
        header("Location: index.php");
        exit();
    }

} elseif (isset($_POST['seleccionar'])) {
    buscarDatos($_POST['seleccionado']);
    header("Location: index.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/index.css" />
    <title>Document</title>
</head>

<body>
    <form action="index.php" method="post">
        <div id="container">
            <div class="container_sections">
                <?php
                    global $recoveryData;
                    $recoveryData = [
                        "nombre" => isset($_SESSION["nombre"]) ? $_SESSION["nombre"]: '',
                        "apellido" => isset($_SESSION["apellido"]) ? $_SESSION["apellido"] : '',
                        "direccion" => isset($_SESSION["direccion"]) ? $_SESSION["direccion"] : '',
                        "horario" =>isset( $_SESSION["horario"]) ? $_SESSION["horario"] : '',
                        "team" => isset($_SESSION["team"]) ? $_SESSION["team"] : '',
                        "trainer" => isset($_SESSION["trainer"]) ? $_SESSION["trainer"] : '',
                        "edad" => isset($_SESSION["edad"]) ? $_SESSION["edad"] : '',
                        "email" => isset($_SESSION["email"]) ? $_SESSION["email"] : '',
                        "cedula" => isset($_SESSION["cedula"]) ? $_SESSION["cedula"] : '',
                    ];
                ?>
                <input type='text' name='nombre'  id='nombre' placeholder='Nombre' value='<?php echo $GLOBALS["recoveryData"]['nombre'] ?>'>
                <input type='text' name='apellido' id='apellido' placeholder='Apellido' value='<?php echo $GLOBALS["recoveryData"]['apellido'] ?>'>
                <input type='text' name='direccion' id='direccion' placeholder='Direccion' value='<?php echo $GLOBALS["recoveryData"]['direccion'] ?>'>
                <input type='text' name='horario' id='horario' placeholder='Horario de entrada' value='<?php echo $GLOBALS["recoveryData"]['horario'] ?>'>
                <input type='text' name='team' id='team' placeholder='Team' value='<?php echo $GLOBALS["recoveryData"]['team'] ?>'>
                <input type='text' name='trainer' id='trainer' placeholder='Trainer' value='<?php echo $GLOBALS["recoveryData"]['trainer'] ?>'>
            </div>
            <div class="container_sections">
                <p>Campuslands</p>
                <input type='text' name='edad' id='edad' placeholder='Edad' value="<?php echo $GLOBALS["recoveryData"]['edad'] ?>">
                <input type='email' name='email' id='email' placeholder='usuario@correo.com' value="<?php echo $GLOBALS["recoveryData"]['email'] ?>">
                <div id="acciones">
                    <input type="submit" name="acciones" value="✅">
                    <input type="submit" name="acciones" value="📝">
                    <input type="submit" name="acciones" value="❌">
                    <input type="submit" name="acciones" value="🔍">
                    <input type='number' name='cedula' id='cedula' placeholder='Cedula' value="<?php echo $GLOBALS["recoveryData"]['cedula'] ?>">
                </div>
            </div>
        </div>
    </form>
    <div id="container_table">
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Apellidos</th>
                    <th>Direccion</th>
                    <th>Edad</th>
                    <th>Team</th>
                    <th>Email</th>
                    <th>Horario de entrada</th>
                    <th>Team</th>
                    <th>Trainer</th>
                    <th>X</th>
                </tr>
            </thead>
            <tbody>
                    <?php
                    $data = file_get_contents("https://645fecb5fe8d6fb29e2902d5.mockapi.io/api/php");
                    $data = json_decode($data, true);
                    foreach ($data as $key => $value) {
                        echo "<form action='index.php' method='post'>";
                        echo "<tr>";
                        echo "<td>" . $value["nombre"] . "</td>";
                        echo "<td>" . $value["apellido"] . "</td>";
                        echo "<td>" . $value["direccion"] . "</td>";
                        echo "<td>" . $value["edad"] . "</td>";
                        echo "<td>" . $value["team"] . "</td>";
                        echo "<td>" . $value["email"] . "</td>";
                        echo "<td>" . $value["horario"] . "</td>";
                        echo "<td>" . $value["team"] . "</td>";
                        echo "<td>" . $value["trainer"] . "</td>";
                        echo "<td><input type='submit' name='seleccionar' value='Seleccionar' class='btn_seleccionar'></td>";
                        echo "<input type='hidden' name='seleccionado' value=" . $value["cedula"] . ">";
                        echo "</tr>";
                        echo "</form>";
                    }
                    ?>
            </tbody>
        </table>
    </div>

</body>

</html>