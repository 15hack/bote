Script para mostrar los movimientos de una cuenta Triodos en wordpress

1- Crear alias para que reciba el correo de triodos en el script de python

`sudo nano /etc/aliases`

```
movimientoscuenta:"|/ruta/movimientoscuenta.py"
```

2- Crear tabla en la misma base de datos de wordpres

Usar `table.sql`

3- Dar acceso al script te python a la base de datos

Crear un fichero .db en el mismo directorio del script con una linea de texto
de este tipo:

```
dbhost:dbuser:dbpass:dbname
```

4- Crear un plugin para wordpress e instalarlo

Se hace un zip de `bote.php` y se instala como plugin en wordpress

5- Usar shortcode

En la página o entrada donde se quiera que aparezca la tabla escribir `[bote]`
