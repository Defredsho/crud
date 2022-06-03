<?php
// se incluye el archivo de configuración
require_once "config.php";
 
// Definir variables e inicializar con valores vacíos
$username = $password = $confirm_password = "";
$username_err = $password_err = $confirm_password_err = "";
 
// Procesamiento de datos del formulario cuando se envía el formulario
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Validar el nombre de usuario
    if(empty(trim($_POST["username"]))){
        $username_err = "Por favor ingrese un usuario.";
    } else{
        // Preparar la consulta
        $sql = "SELECT id FROM users WHERE username = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Vincular variables a la declaración preparada como parámetros
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // asignar parámetros
            $param_username = trim($_POST["username"]);
            
            // Intentar ejecutar la declaración preparada
            if(mysqli_stmt_execute($stmt)){
                /* almacenar resultado*/
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $username_err = "Este usuario ya fue tomado.";
                } else{
                    $username = trim($_POST["username"]);
                }
            } else{
                echo "Al parecer algo salió mal.";
            }
        // Declaración de cierre
        mysqli_stmt_close($stmt);
				}

    }
    
    // Validar contraseña
    if(empty(trim($_POST["password"]))){
        $password_err = "Por favor ingresa una contraseña.";     
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "La contraseña al menos debe tener 6 caracteres.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validar que se confirma la contraseña
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Confirma tu contraseña.";     
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "No coincide la contraseña.";
        }
    }
    
    // Verifique los errores de entrada antes de insertar en la base de datos
    if(empty($username_err) && empty($password_err) && empty($confirm_password_err)){
        
        // Prepare una declaración de inserción
        $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
         
        if($stmt = mysqli_prepare($link, $sql)){
            // Vincular variables a la declaración preparada como parámetros
            mysqli_stmt_bind_param($stmt, "ss", $param_username, $param_password);
            
            // Establecer parámetros
            $param_username = $username;
			$param_password = password_hash($password, PASSWORD_DEFAULT); // Crear una contraseña hash
            
            // Intentar ejecutar la declaración preparada
            if(mysqli_stmt_execute($stmt)){
                // Redirigir a la página de inicio de sesión (login.php)
                header("location: login.php");
            } else{
                echo "Algo salió mal, por favor inténtalo de nuevo.";
            }
        // claración de cierre
        mysqli_stmt_close($stmt);
        }
         
    }
    
    // Cerrar la conexión
    mysqli_close($link);
}
?>
 
<!DOCTYPE html>
<html lang="es">
<head>
		<meta charset="UTF-8">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    <title>Registro</title>
</head>
<body style="background : #121E12" >
				<div class="form-group align-content-around">
   			<div class="container mt-5 py-5 px-5 card bg-dark text-white"> 
        <h2 class="h3 text-center fw-bold">Registro</h2>
        <p class="text-center">Por favor complete este formulario para crear una cuenta.</p>
        <form class="card bg-dark text-white border-0 " action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <h3 class="h4 text-center bg-dark py-2">Usuario</h3>
								<input 
											type="text" 
											name="username"
											class="form-inputs mb-3 form-control"	
											value="<?php echo $username; ?>">
                <span><?php echo $username_err; ?></span><br>
            
                <h3 class="h4 text-center">Contraseña</h3>
								<input 
											type="password" 
											name="password"  
											class="form-inputs mb-3 form-control"
											value="<?php echo $password; ?>">
                <span><?php echo $password_err; ?></span><br>
            
            
                <h3 class="h4 text-center">Confirmar contraseña</h3>
								<input 
											type="password" 
											name="confirm_password" 
											class="form-inputs mb-3 form-control" 
											value="<?php echo $confirm_password; ?>">
                <span><?php echo $confirm_password_err; ?></span><br>
            
								<input 
											type="submit"  
											value="Registrar"
											class="btn btn-success center-block mb-2">
                <input type="reset"  value="Borrar" class="btn btn-danger"><br>
            
            <p class="text-center">¿Ya tienes una cuenta? <a href="login.php">Ingresa aquí</a>.</p>
        </form>
		</div>
		</div>
</body>
</html>
