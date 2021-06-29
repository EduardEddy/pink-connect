## Proyecto Pink Connect Venta Prevee
Este proyecto realiza la carga listado y edicion de registro desde venta prevee al sistema pink connect mediante su api

## Intalacion
El proyecto fue desarrollado bajo un entorno de php 7.4
el ambiente debe tener instalado los paquetes de conexion entre php y mysql
se requiere tener composer instalado para ejecutar comandos e instalaciones https://getcomposer.org/
una vez se descargara el proyecto mediante git ejecutar el comando "composer install" este instalará las dependencias del proyecto (solo se encuentran instaladas las que trae laravel por defecto)

## CRON JOB
    - Creacion de archivos command
    Se crea el archivo mediante el comando php artisan make:command este se crea el la carpeta app/Console/Commands

    - Configuracion del archivo command
        * se le asigna el el comando a ejecutar en la variable protegida signature 
        * se agrega una descripcion opcional si se desea en la variable descripcion
        * en la funcion handle se desarrolla la funcionabilidad de los requerimientos

    - Configuracion del archivo kernel.php
    Se debe ir a la carpeta app/Console aca se encuentr ael archivo Kernel.php en la funcion protegida shedule se agregara el llamado a los comandos configurados en el paso anterior se le agrega el llamado a una funcion propia para manejar los intervalos de tiempo en el que se va a ejecutar la tarea, como por ejemplo cada minuto, una vez al dia, una vez a la semana, una vez al año, todos los dias a una hora especifica

    - Configuracion del serviro para ejecutar el cron
        * Ejecutar en la consola o terminal el comando "crontab -e" este abrira el archivo crontab para editarlo
        * agregar la siguiente instruccion en una linea nueva del archivo * * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1 de esta manera se ejecutaran los cron job existentes en el proyecto donde /path-to-your-project es la ruta al proyecto en el servidor

