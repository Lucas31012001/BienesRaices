<?php
    use App\Propiedad;

    if($_SERVER['SCRIPT_NAME'] === '/anuncios.php'){
        $propiedades = Propiedad::all();
    } else {
        $propiedades = Propiedad::get(3);
    }
?>


<div class="contenedor-anuncios">
    <?php foreach($propiedades as $propiedad){ ?>
    <div class="anuncio">

        <img loading="lazy" src="/bienesraices_inicio/imagenes/<?php echo $propiedad->imagen; ?>" alt="anuncio" class="imagen-anuncio"> <!-- Si el navegador no es compatible con el elemento <picture> o las fuentes de imagen proporcionadas, cargará esta imagen predeterminada. En este caso, también tiene el atributo loading="lazy", que le dice al navegador que cargue la imagen de forma perezosa, es decir, solo cuando esté cerca del área visible de la ventana del navegador, lo que puede mejorar el rendimiento de la página. -->

        <div class="contenido-anuncio">
            <h3><?php echo $propiedad->titulo; ?></h3>
            <p><?php echo $propiedad->descripcion; ?></p>
            <p class="precio"><?php echo $propiedad->precio; ?></p>

            <ul class="iconos-caracteristicas">
                <li>
                    <img class="icono" loading="lazy" src="build/img/icono_wc.svg" alt="icono wc">
                    <p><?php echo $propiedad->wc; ?></p>
                </li>
                <li>
                    <img class="icono" loading="lazy" src="build/img/icono_estacionamiento.svg" alt="icono estacionamiento">
                    <p><?php echo $propiedad->estacionamiento; ?></p>
                </li>
                <li>
                    <img class="icono" loading="lazy" src="build/img/icono_dormitorio.svg" alt="icono habitaciones">
                    <p><?php echo $propiedad->habitaciones; ?></p>
                </li>
            </ul>

            <a href="/bienesraices_inicio/anuncio.php?id=<?php echo $propiedad->id; ?>" class="boton-amarillo-block">
                Ver Propiedad
            </a>
        </div><!--.contenido-anuncio-->
    </div><!--.anuncio-->
    <?php } ?>
</div><!--.contenedor-anuncios-->
