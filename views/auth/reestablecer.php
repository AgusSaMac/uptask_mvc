<div class="contenedor reestablecer">
    <?php include_once(__DIR__ . '/../templates/nombre-sitio.php'); ?>

    <div class="contenedor-sm">
        <p class="descripcion-pagina">Introduce un nuevo password</p>

        <?php 
            include_once(__DIR__ . '/../templates/alertas.php'); 
            if ($mostrar):
        
        ?>

        <form method="post" class="formulario">
            <div class="campo">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" placeholder="Tu Password">
            </div>
            <div class="campo">
                <label for="password2">Repetir Password</label>
                <input type="password" name="password2" id="password2" placeholder="Repite tu password">
            </div>
            <input type="submit" value="Guardar Password" class="boton">
        </form>

        <?php endif; ?>
        
        <div class="acciones">
            <a href="/">¿Ya tienes una cuenta? Iniciar Sesión</a>
            <a href="/crear">¿Aún no tienes una cuenta? Obtener una</a>
        </div>
    </div>
</div>