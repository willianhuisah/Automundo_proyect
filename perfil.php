
<?php
session_start(); // Iniciar sesión

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: login.html"); // Redirigir a la página de inicio de sesión si no ha iniciado sesión
    exit();
}

// Conectar a la base de datos (reemplaza con tus propios detalles de conexión)
$dbhost = "localhost";
$dbuser = "root";
$dbpass = "";
$dbname = "autoservices_bd";

$conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

// Verificar la conexión
if (!$conn) {
    die("Conexión fallida: " . mysqli_connect_error());
}

// Recuperar la información del usuario, incluida la imagen, de la base de datos
$sql = "SELECT direccion, imagen FROM usuarios WHERE usuario = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 's', $_SESSION['usuario']);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);
mysqli_stmt_bind_result($stmt, $direccion, $imagen_path); // Nueva variable $direccion
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);


// Cerrar la conexión a la base de datos
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario</title>
    <!-- Agrega estilos CSS según sea necesario -->
    <style>
        body {
    font-family: Arial, sans-serif;
    background: url('assets/image/pe.jpg') center center fixed;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

#perfil-container {
    background: white;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    padding: 20px;
    max-width: 400px;
    width: 100%;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
    background-size: cover;
    text-align: center;
}

#profile-image {
    max-width: 100%; /* Hace la imagen responsive */
    height: 200px; /* Mantiene la proporción de la imagen */
    border-radius: 50%;
    margin-bottom: 10px;
}

h2 {
    color: #333;
}

p {
    margin: 10px 0;
    color: #666;
}

form {
    margin-top: 20px;
}

a {
    text-decoration: none;
    color: #2196F3;
    font-weight: bold;
    margin-left: 10px; /* Ajustado margen a la izquierda */
}

a:hover {
    color: #0C2567;
}

input[type="submit"],
input[type="file"] {
    background-color: #0C2567;
    color: white;
    border: none;
    padding: 10px 20px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 16px;
    cursor: pointer;
    border-radius: 5px;
    margin-right: 10px; /* Ajustado margen a la derecha */
}

input[type="file"] {
    display: none;
}

label {
    cursor: pointer;
}

/* Estilos para dispositivos móviles */
@media (max-width: 767px) {
    body {
        background-attachment: scroll;
    }
}
    </style>

</head>

<body>
    <div id="perfil-container">
        <!-- Muestra la imagen -->
        <img id="profile-image" src="<?php echo $imagen_path; ?>" alt="Imagen de perfil">

        <h2>Bienvenido, <?php echo $_SESSION['nombre']; ?></h2> 
        <p>Apellidos: <?php echo $_SESSION['apellidos']; ?></p>
        <p>Nombre de Usuario: <?php echo $_SESSION['usuario']; ?></p>
        <p>DNI: <?php echo $_SESSION['dni']; ?></p>
        <p>Licencia de conducir: <?php echo $_SESSION['licencia']; ?></p>
        <p>Email: <?php echo $_SESSION['email']; ?></p>
        <p>Dirección: <?php echo $direccion; ?></p>

        <!-- ... otros campos de perfil ... -->

        <form method="post" action="cerrar_sesion.php">
            <input type="submit" name="cerrar_sesion" value="Cerrar Sesión">
            <a href="javascript:history.go(-1);">Volver</a>
        </form>
    </div>
</body>

</html>