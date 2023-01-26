<?php

namespace Controllers;

use MVC\Router;
use Classes\Email;
use Model\Usuario;
use PHPMailer\PHPMailer\PHPMailer;

class LoginController {

    public static function login(Router $router) {
        
        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = new Usuario($_POST);
            $alertas = $usuario->validarLogin();

            if (empty($alertas)) {
                // Verificar que el usuario exista
                $usuario = Usuario::where('email', $usuario->email);

                if (!$usuario || !$usuario->confirmado) {
                    Usuario::setAlerta('error', 'El Usuario no Existe o no esta Confirmado');
                } else {
                    // El Usuario existe

                    if (password_verify($_POST['password'], $usuario->password)) {
                        // Iniciar sesión
                        session_start();
                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;

                        // Redireccionar
                        header('Location: /dashboard');
                    } else {
                        Usuario::setAlerta('error', 'Password Incorrecto');
                    }
                }
            }
        }

        $alertas = Usuario::getAlertas();

        // Render a la vista
        $router->render('auth/login', [
            'titulo' => 'Iniciar Sesión',
            'alertas' => $alertas
        ]);
    }

    public static function logout() {
        session_start();
        $_SESSION = [];
        header('Location: /');
    }

    public static function crear(Router $router) {
        
        $usuario = new Usuario;
        $alertas =[];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarNuevaCuenta();

            if (empty($alertas)) {
                $existeUsuario = Usuario::where('email', $usuario->email);
                if($existeUsuario) {
                    Usuario::setAlerta('error', 'El Usuario ya esta registrado');
                    $alertas = Usuario::getAlertas();
                } else {
                    // Hashear password
                    $usuario->hashPassword();

                    // Eliminar password2
                    unset($usuario->password2);

                    // Crear el token   
                    $usuario->crearToken();

                    // Enviar el email de confirmación
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarConfirmacion();

                    // Crear un nuevo usuario
                    $resultado = $usuario->guardar();

                    // Redireccionar al usuario
                    if($resultado) {
                        header('Location: /mensaje');
                    }
                }
            }

        }
                // Render a la vista
                $router->render('auth/crear', [
                    'titulo' => 'Crea tu cuenta en UpTask',
                    'usuario' => $usuario,
                    'alertas' => $alertas
                ]);
    }

    public static function olvide(Router $router) {
        // Inicializar arreglo de alertas para evitar errores
        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $usuario = new Usuario($_POST);
            $alertas = $usuario->validarEmail();

            if(empty($alertas)) {
                // Buscar el usuario
                $usuario = Usuario::where('email', $usuario->email);

                if($usuario && $usuario->confirmado) {
                    // Generar un nuevo token
                    $usuario->crearToken();
                    unset($usuario->password2);

                    // Actualizar el usuario;
                    $usuario->guardar();

                    // Enviar el email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token); 
                    $email->enviarReset();
                    // Imprimir la alerta
                    Usuario::setAlerta('exito', 'Hemos enviado las instrucciones a tu email');

                } else {
                    Usuario::setAlerta('error', 'El Usuario no existe o no esta confirmado');
                }
            }
        }

        $alertas = Usuario::getAlertas();
        
        // Render a la vista
        $router->render('auth/olvide', [
            'titulo' => 'Olvide mi password',
            'alertas' => $alertas
        ]);
    }

    public static function reestablecer(Router $router) {

        $token = s($_GET['token']);
        $mostrar = true;

        if (!$token) header('Location: /');

        $usuario = Usuario::where('token', $token);

        if (empty($usuario)) {
            Usuario::setAlerta('error', 'Token No Válido');
            $mostrar=false;
        }

        $alertas = Usuario::getAlertas();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Añadir el nuevo password
            $usuario->sincronizar($_POST);

            $alertas = $usuario->validarPassword();

            if (empty($alertas)) {
                // Hashear el password
                $usuario->hashPassword();
                unset($usuario->password2);

                // Eliminar el token
                $usuario->token = '';

                // Guardar el usuario en la BD
                $resultado =$usuario->guardar();

                // Redireccionar
                if($resultado) header('Location: /');

            }
        }

        // Render a la vista
        $router->render('auth/reestablecer', [
            'titulo' => 'Reestablecer Password',
            'alertas' => $alertas,
            'mostrar' => $mostrar
        ]);
    }

    public static function mensaje(Router $router) {
        // Render a la vista
        $router->render('auth/mensaje', [
            'titulo' => 'Cuenta Creada'
        ]);
    }

    public static function confirmar(Router $router) {
        
        // Obtener y validar la existencia del token
        $token = s($_GET['token']);
        if(!$token) header('Location:/');

        // encontrar al usuario con el token dado
        $usuario =  Usuario::where('token', $token);

        if (empty($usuario)) {
            // No se encontró usuario con el token proporcionado
            Usuario::setAlerta('error', 'Token no válido');
        } else {
            // confirmar la cuenta
            Usuario::setAlerta('exito', 'Tu cuenta ha sido confirmada');
            $usuario->token = '';
            $usuario->confirmado = 1;
            unset($usuario->password2);

            $usuario->guardar();
        }

        $alertas = Usuario::getAlertas();


        // Render a la vista
        $router->render('auth/confirmar', [
            'titulo' => 'Confirmación de Cuenta',
            'alertas' => $alertas
        ]);
    }
}