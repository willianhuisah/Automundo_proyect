<?php
session_start(); // Iniciar sesión

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

// Recoger datos del formulario de registro
$usuario = $_POST['usuario'];
$password = $_POST['password'];
$dni = $_POST['dni'];
$licencia = $_POST['licencia'];
$nombre = $_POST['nombre'];
$apellidos = $_POST['apellidos'];
$email = $_POST['email'];
$direccion = $_POST['direccion']; // Nuevo campo de dirección

// Obtener información de la imagen
$imagenNombre = $_FILES['imagen']['name'];
$imagenTmpNombre = $_FILES['imagen']['tmp_name'];
$imagenError = $_FILES['imagen']['error'];

// Ruta donde se almacenarán las imágenes (ajusta según tu estructura de carpetas)
$rutaAlmacenamiento = 'uploads/';

// Verificar si se proporcionó una imagen y no hubo errores
if ($imagenError === UPLOAD_ERR_OK) {
    // Generar un nombre único para la imagen
    $imagenNombreUnico = uniqid('', true) . '_' . $imagenNombre;

    // Construir la ruta completa del archivo
    $rutaArchivo = $rutaAlmacenamiento . $imagenNombreUnico;

    // Mover la imagen al directorio de almacenamiento
    move_uploaded_file($imagenTmpNombre, $rutaArchivo);

    // Insertar datos en la base de datos, incluyendo la ruta de la imagen
    $sql = "INSERT INTO usuarios (usuario, password, dni, licencia, nombre, apellidos, email, direccion, imagen) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    // Preparar la sentencia SQL
    $stmt = mysqli_prepare($conn, $sql);

    // Vincular los parámetros
    mysqli_stmt_bind_param($stmt, 'sssssssss', $usuario, $password, $dni, $licencia, $nombre, $apellidos, $email, $direccion, $rutaArchivo);

    // Ejecutar la sentencia SQL
    if (mysqli_stmt_execute($stmt)) {
        echo "<script> alert('Usuario registrado exitosamente: $nombre'); 
                        window.location='login.html'; 
                </script>";
        // Detener la ejecución del script aquí
        die("Se ha registrado exitosamente");
    } else {
        echo "Error al insertar datos en la base de datos: " . mysqli_stmt_error($stmt);
    }

    // Cerrar la sentencia preparada
    mysqli_stmt_close($stmt);
} else {
    echo "Error al subir la imagen. Código de error: $imagenError";
}

// Cerrar la conexión a la base de datos
mysqli_close($conn);
?>
