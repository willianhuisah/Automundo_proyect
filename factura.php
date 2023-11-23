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

// Recuperar la información del usuario, incluidos el DNI y los apellidos, de la base de datos
$usuario = $_SESSION['usuario'];
$sql = "SELECT nombre, apellidos, dni, direccion FROM usuarios WHERE usuario = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 's', $usuario);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);
mysqli_stmt_bind_result($stmt, $nombre, $apellidos, $dni, $direccion);  // Agregar $apellidos aquí
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);
$fechaActual = date("d/m/Y");
$numeroAleatorio = "10" . str_pad(mt_rand(0, 999999999), 7, "0", STR_PAD_LEFT);

?>



<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura Electrónica</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 20px;
            background-color: rgb(67, 66, 66);
        }

        .invoice {
            max-width: 800px;
            margin: auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .invoice-header,
        .invoice-footer {
            background-color: #0C2567;
            color: #fff;
            padding: 15px;
            text-align: center;
            margin-bottom: 20px; /* Mayor espacio entre encabezado y contenido */
        }

        .invoice-header h1 {
            margin: 0;
        }

        .company-info {
            overflow: hidden;
        }

        .company-info img {
            max-width: 150px;
            height: auto;
            float: right;
            margin-bottom: 10px;
        }

        .company-details {
            float: left;
            text-align: left; /* Alinea a la izquierda */
            margin-right: 10px; /* Espacio entre la información de la empresa y el logo */
        }

        .invoice-body {
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #0C2567;
            color: #fff;
        }

        .total {
            font-weight: bold;
            font-size: 1.2em;
        }

        /* Estilo adicional */
        p {
            margin: 10px 0;
        }

        strong {
            font-weight: bold;
        }

        .btn-container {
            text-align: center;
            margin-top: 20px;
        }

        .btn-pagar {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border: none;
            border-radius: 5px;
        }
        .btn-container {
            text-align: center;
            margin-top: 20px;
        }

        .btn-pagar {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border: none;
            border-radius: 5px;
        }

        .thanks-message {
            text-align: center;
            color: #4CAF50;
            font-size: 18px;
            margin-top: 20px;
            display: none;
        }
        .customer-info {
            overflow: hidden;
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px; /* Agregado para mayor separación */
        }

        .customer-info-left {
            float: left;
            text-align: left;
        }

        .customer-info-right {
            float: right;
            text-align: right;
        }
    
    </style>
</head>

<body>
    <div class="invoice">
        <div class="invoice-header">
            <h1>Factura Electrónica</h1>
        </div>

        <div class="company-info">
            <div class="company-details">
                <p><strong>Nombre de la Empresa:</strong> Automundo Services SAC</p>
                <p><strong>Dirección:</strong> Av. Los Industriales 123, La Molina - Lima</p>
                <p><strong>RUC:</strong> 20042214390</p>
                
            </div>
            <img src="assets/img/SERVICES.svg" alt="Logo de la Empresa">
        </div>

        <div class="invoice-body">
            <div class="customer-info">
            <div class="customer-info-left">
    <h2>Información del cliente</h2>
    <?php
    // Mostrar la información del cliente si está disponible
    if (isset($nombre) && isset($apellidos) && isset($dni) && isset($direccion)) {
        echo "<p><strong>Nombre del Cliente:</strong> $nombre</p>";
        echo "<p><strong>Apellidos:</strong> $apellidos</p>";
        echo "<p><strong>DNI:</strong> $dni</p>";
        echo "<p><strong>Dirección:</strong> $direccion</p>";
    } else {
        echo "<p><strong>Información del Cliente:</strong> No disponible</p>";
    }
    ?>
</div>
    <div class="customer-info-right">
    <p><strong>Fecha:</strong> <?php echo $fechaActual; ?></p>
    <br>
    <p><strong>RUC:</strong> <?php echo $numeroAleatorio; ?></p>
    </div>
   </div>

            <table>
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Precio Unitario</th>
                        <th>Cantidad</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody id="cart-items">
                    <!-- Aquí se agregarán los elementos del carrito dinámicamente con JavaScript -->
                </tbody>
            </table>
            <br>

            <p>Total: $<span id="subtotal">0.00</span></p>
            <p>IGV (18%): $<span id="igv">0.00</span></p>
            <p class="total">Total con IGV: $<span id="cart-total">0.00</span></p>

            <div class="btn-container">
                <button class="btn-pagar" onclick="realizarPago()">Pagar</button>
            </div>

            <p id="thanks-message" class="thanks-message">Gracias por su compra</p>

            <script>
                const cartItems = document.getElementById('cart-items');
                const subtotalElement = document.getElementById('subtotal');
                const igvElement = document.getElementById('igv');
                const cartTotal = document.getElementById('cart-total');
                const cart = JSON.parse(localStorage.getItem('cart')) || [];

                // Mostrar los elementos del carrito en la factura
                cart.forEach(item => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${item.name}</td>
                        <td>$${item.price.toFixed(2)}</td>
                        <td>${item.quantity}</td>
                        <td>$${(item.price * item.quantity).toFixed(2)}</td>
                    `;
                    cartItems.appendChild(row);
                });

                // Calcular el subtotal
                const subtotal = cart.reduce((acc, item) => acc + item.price * item.quantity, 0);
                subtotalElement.innerText = subtotal.toFixed(2);

                // Calcular el IGV (18%)
                const igv = subtotal * 0.18;
                igvElement.innerText = igv.toFixed(2);

                // Calcular el total con IGV
                const total = subtotal + igv;
                cartTotal.innerText = total.toFixed(2);

                function realizarPago() {
        // Lógica de procesamiento de pago

        // Confirmación de pago
        if (confirm('¿Desea confirmar el pago?')) {
            // Mostrar mensaje de agradecimiento
            const thanksMessage = document.getElementById('thanks-message');
            thanksMessage.style.display = 'block';

            // Imprimir factura después de un breve retraso
            setTimeout(function () {
                window.print();
            }, 1000); // Imprimir después de 1 segundo

            // Limpiar carrito
            localStorage.removeItem('cart');

            // Redireccionar al shopauto.html (puedes cambiar la URL según tu estructura de carpetas)
            setTimeout(function () {
                window.location.href = 'shopauto.html';
            }, 2000); // Redireccionar después de 2 segundos
        }
    }
            </script>
        </div>

</body>

</html>
