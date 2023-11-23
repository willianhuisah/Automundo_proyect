<?php
session_start(); // Iniciar sesión

// Conectar a la base de datos (reemplaza con tus propios detalles de conexión)
$dbhost = "localhost";
$dbuser = "root";
$dbpass = "";
$dbname = "autoservices_bd";

$conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Recoger datos del formulario de inicio de sesión
$usuario = $_POST['usuario'];
$password = $_POST['password'];

// Buscar el usuario en la base de datos
$sql = "SELECT * FROM usuarios WHERE usuario='$usuario' AND password='$password'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Usuario autenticado, almacenar datos en la sesión
    $row = $result->fetch_assoc();
    $_SESSION['usuario'] = $row['usuario'];
    $_SESSION['nombre'] = $row['nombre'];
    $_SESSION['apellidos'] = $row['apellidos'];
    $_SESSION['dni'] = $row['dni'];
    $_SESSION['email'] = $row['email'];
    $_SESSION['licencia'] = $row['licencia'];

    // ... otros datos que desees almacenar en la sesión

    // Redirigir a la página de perfil
    header("Location: shopauto.html");
    exit();
} else {
    $_SESSION['mensaje'] = "Nombre de usuario o contraseña incorrectos";
    header("Location: login.html"); // Redirigir a la página de inicio de sesión
    exit();
}

$conn->close();
?>

