<?php
// Inicializa la sesión
session_start();
 
/* Verifique si el usuario ya ha iniciado sesión, si es así, 
rediríjalo a la página de bienvenida (index.php)*/
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
  header("location: index.php");
  exit;
}
 
// Incluir el archivo de configuración
require_once "config.php";
 
// Definir variables e inicializar con valores vacíos
$username = $password = $username_err = $password_err = "";
 
// Procesamiento de datos del formulario cuando se envía el formulario
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Comprobar si el nombre de usuario está vacío
    if(empty(trim($_POST["username"]))){
        $username_err = "Por favor ingrese su usuario.";
    } else{
        $username = trim($_POST["username"]);
    }
    
    // Comprobar si la contraseña está vacía
    if(empty(trim($_POST["password"]))){
        $password_err = "Por favor ingrese su contraseña.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validar información del usuario
    if(empty($username_err) && empty($password_err)){
        // Preparar la consulta select
        $sql = "SELECT id, username, password FROM users WHERE username = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            /* Vincular variables a la declaración preparada como parámetros, s es por la
			variable de tipo string*/
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // Asignar parámetros
            $param_username = $username;
            
            // Intentar ejecutar la declaración preparada
            if(mysqli_stmt_execute($stmt)){
                // almacenar el resultado de la consulta
                mysqli_stmt_store_result($stmt);
                
                /*Verificar si existe el nombre de usuario, si es así,
				verificar la contraseña*/
                if(mysqli_stmt_num_rows($stmt) == 1){                    
                    // Vincular las variables del resultado
                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password);
					//obtener los valores de la consulta
                    if(mysqli_stmt_fetch($stmt)){
						/*comprueba que la contraseña ingresada sea igual a la 
						almacenada con hash*/
                        if(password_verify($password, $hashed_password)){
                            // La contraseña es correcta, así que se inicia una nueva sesión
                            session_start();
                            
                            // se almacenan los datos en las variables de la sesión
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;                            
                            
                            // Redirigir al usuario a la página de inicio
                            header("location: index.php");
                        } else{
                            // Mostrar un mensaje de error si la contraseña no es válida
                            $password_err = "La contraseña que ha ingresado no es válida.";
                        }
                    }
                } else{
                    // Mostrar un mensaje de error si el nombre de usuario no existe
                    $username_err = "No existe cuenta registrada con ese nombre de usuario.";
                }
            } else{
                echo "Algo salió mal, por favor vuelve a intentarlo.";
            }
        // Cerrar la sentencia de consulta
        mysqli_stmt_close($stmt);
        }
        
    }
    
    // Cerrar laconexión
		mysqli_close($link);
}
?>
 
<!DOCTYPE html>
<html lang="es">
	<head>
		<meta charset="UTF-8">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
		<title>Inicio de sesión</title>
	</head>
<body style="background-color: #121E12" >
   		<div class="container mt-5 card px-5 py-5 bg-dark text-white  " > 
        <h2 class="h3 text-center fw-semibold">Inicio de Sesión</h2>
				<p class="fw-normal text-center">Por favor, introduzca usuario y contraseña para iniciar sesión.</p>
			<div id="vueapplogin" class="form-group align-content-around">	
        <form class="card border-0 px-5 py-3 bg-dark text-white" >
                <h3 class="h4 text-center">Usuario</h3>
								<input 
											type="text" 
											name="username" 
											class="form-inputs form-control mb-3"  
											v-model="logDetails.username"
											v-on:keyup="keymonitor">
                <span ><?php echo $username_err; ?></span><br>
              
                <h3 class="h4 text-center">Contraseña</h3>
								<input 
												type="password" 
												name="password"
												v-model="logDetails.password"
												v-on:keyup="keymonitor"
												class="form-inputs form-control mb-3"
												>
								<div>
									<span></span><br>
           			</div> 
            
								<input 
												type="submit"  
												value="Ingresar"
												class="btn btn-primary"
												@click="checkLogin();"/><br>
                <p class="text-center ">¿No tienes una cuenta? <a href="register.php">Regístrate ahora</a>.</p>
				</form>
     </div>
			</div>
<script src="https://cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="applogin.js"></script>
</body>
</html>
