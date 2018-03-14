//Iniciamos nuestra función jquery.
//$(function(){
//	$('#enviar').click(SubirFotos); //Capturamos el evento click sobre el boton con el id=enviar	y ejecutamos la función seleccionado.
//});

function subirArchivos(est_mat_id, per, fecha) {
    var t = "";
    if(per==='t'){
        t = "_t";
    }
    var auditor = $('#aud_id').val();
    var periodo = $('#periodo').val();
    var archivos = document.getElementById("archivos"+t);//Creamos un objeto con el elemento que contiene los archivos: el campo input file, que tiene el id = 'archivos'
    var archivo = archivos.files; //Obtenemos los archivos seleccionados en el imput
    //Creamos una instancia del Objeto FormDara.
    var archivos = new FormData();
    /* Como son multiples archivos creamos un ciclo for que recorra la el arreglo de los archivos seleccionados en el input
     Este y añadimos cada elemento al formulario FormData en forma de arreglo, utilizando la variable i (autoincremental) como 
     indice para cada archivo, si no hacemos esto, los valores del arreglo se sobre escriben*/
    for (i = 0; i < archivo.length; i++) {
        archivos.append('archivo' + i, archivo[i]); //Añadimos cada archivo a el arreglo con un indice direfente
    }
    archivos.append('est_mat_id', est_mat_id);
    archivos.append('accion', 'agr');
    archivos.append('persona', per);
    archivos.append('auditor', auditor);
    archivos.append('periodo', periodo);
    /*Ejecutamos la función ajax de jQuery*/
    $.ajax({
        url: 'src/upload.php', //Url a donde la enviaremos
        type: 'POST', //Metodo que usaremos
        contentType: false, //Debe estar en false para que pase el objeto sin procesar
        data: archivos, //Le pasamos el objeto que creamos con los archivos
        processData: false, //Debe estar en false para que JQuery no procese los datos a enviar
        cache: false //Para que el formulario no guarde cache
    }).done(function (msg) {//Escuchamos la respuesta y capturamos el mensaje msg
        mensajeFinal(msg, per);
        traerArchivos('n', est_mat_id, per, fecha);
    });
    return false;
}

function traerArchivos(seg_id, est_mat_id, per, fecha) {
    var archivos = new FormData();
    var auditor = $('#aud_id').val();
    var periodo = $('#periodo').val();
    var ruta = "";
//    if(seg_id!=='n'){
//        if(fecha!=='no'){        
//            ruta = periodo+"/";
//            var corte = new Date('2015','10','04');
//            var fecha_seg = new Date(fecha);
//            if(corte>fecha_seg){
//                alert(fecha+" fecha seg: " + fecha_seg + " CORTE: " + corte);
//            }else{
//                alert(fecha+" FECHA SEG: " + fecha_seg + " corte: " + corte);
//            }
//        }else {            
//            ruta = "corte_1/"+est_mat_id+"/";
//        }
//    }else {
//        ruta = "tod/"+periodo+"/"+auditor+"/"+est_mat_id+"/";
//    }
//    archivos.append('ruta_arch', ruta);
    archivos.append('accion', 'traer');
    archivos.append('persona', per);
    archivos.append('auditor', auditor);
    archivos.append('periodo', periodo);
    archivos.append('seguimiento', seg_id);
    archivos.append('est_mat_id', est_mat_id);
    archivos.append('fecha_seg', fecha);
    $.ajax({
        url: 'src/upload.php', //Url a donde la enviaremos
        type: 'POST', //Metodo que usaremos
        contentType: false, //Debe estar en false para que pase el objeto sin procesar
        data: archivos, //Le pasamos el objeto que creamos con los archivos
        processData: false, //Debe estar en false para que JQuery no procese los datos a enviar
        cache: false //Para que el formulario no guarde cache
    }).done(function (msg) {//Escuchamos la respuesta y capturamos el mensaje msg
//            var cont = $('#arch_'+per).html();
//alert(msg);
            $('#arch_'+per).html(msg);//A el div con la clase msg, le insertamos el mensaje en formato  thml
            $('.carg-arch_'+per).show('slow');
    });
    return false;
}

function mensajeFinal(msg, per) {
    $('.mensaje_'+per).html(msg);//A el div con la clase msg, le insertamos el mensaje en formato  thml
    $('.mensaje_'+per).show('slow');//Mostramos el div.
}

function borrarArchivo(nomArch, seg_id, est_mat_id, per, fecha) {
    var carp = nomArch.split('/');
    var archivos = new FormData();
    archivos.append('ruta_arch', nomArch);
    archivos.append('accion', 'borrar');
    archivos.append('persona', per);
    $.ajax({
        url: 'src/upload.php', //Url a donde la enviaremos
        type: 'POST', //Metodo que usaremos
        contentType: false, //Debe estar en false para que pase el objeto sin procesar
        data: archivos, //Le pasamos el objeto que creamos con los archivos
        processData: false, //Debe estar en false para que JQuery no procese los datos a enviar
        cache: false //Para que el formulario no guarde cache
    }).done(function (msg) {//Escuchamos la respuesta y capturamos el mensaje msg
        //mensajeFinal(msg)
//        alert(msg);
        traerArchivos(seg_id, est_mat_id, per, fecha);
    });
    return false;
}

function pasar(nomArch, seg_id, est_mat_id, per, fecha) {
    var carp = nomArch.split('/');
    var archivos = new FormData();
    archivos.append('ruta_arch', nomArch);
    archivos.append('accion', 'pasar');
    archivos.append('persona', per);
    $.ajax({
        url: 'src/upload.php', //Url a donde la enviaremos
        type: 'POST', //Metodo que usaremos
        contentType: false, //Debe estar en false para que pase el objeto sin procesar
        data: archivos, //Le pasamos el objeto que creamos con los archivos
        processData: false, //Debe estar en false para que JQuery no procese los datos a enviar
        cache: false //Para que el formulario no guarde cache
    }).done(function (msg) {//Escuchamos la respuesta y capturamos el mensaje msg
        //mensajeFinal(msg)
        alert(msg);
//        traerArchivos(seg_id, est_mat_id, per, fecha);
    });
    return false;
}

