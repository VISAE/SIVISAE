//Iniciamos nuestra función jquery.
//$(function(){
//	$('#enviar').click(SubirFotos); //Capturamos el evento click sobre el boton con el id=enviar	y ejecutamos la función seleccionado.
//});
$(document).ready(function () {
    $('input[name=banner_eve]').change(function () {
        if ($(this).val() !== '') {
            subirArchivos('n', "banner_eve");
        }
    });
    $('input[name=banner_eve_e]').change(function () {
        if ($(this).val() !== '') {
            subirArchivos($('#even_id_e').val(), "banner_eve_e");
        }
    });
    $('input[name=doc_eve]').change(function () {
        if ($(this).val() !== '') {
            subirArchivos('n', "doc_eve");
        }
    });
    $('input[name=doc_eve_e]').change(function () {
        if ($(this).val() !== '') {
            subirArchivos($('#even_id_e').val(), "doc_eve_e");
        }
    });
});
function subirArchivos(id, cual) {
    var archivos = document.getElementById(cual);//Creamos un objeto con el elemento que contiene los archivos: el campo input file, que tiene el id = 'archivos'
    var archivo = archivos.files; //Obtenemos los archivos seleccionados en el imput
    //Creamos una instancia del Objeto FormDara.
    var archivos = new FormData();
    /* Como son multiples archivos creamos un ciclo for que recorra la el arreglo de los archivos seleccionados en el input
     Este y añadimos cada elemento al formulario FormData en forma de arreglo, utilizando la variable i (autoincremental) como 
     indice para cada archivo, si no hacemos esto, los valores del arreglo se sobre escriben*/
    for (i = 0; i < archivo.length; i++) {
        archivos.append('archivo' + i, archivo[i]); //Añadimos cada archivo a el arreglo con un indice direfente
    }
    archivos.append('cual', cual);
    archivos.append('accion', 'agr');
    archivos.append('evento_id', id);
    /*Ejecutamos la función ajax de jQuery*/
    $.ajax({
        url: 'src/upload.php', //Url a donde la enviaremos
        type: 'POST', //Metodo que usaremos
        contentType: false, //Debe estar en false para que pase el objeto sin procesar
        data: archivos, //Le pasamos el objeto que creamos con los archivos
        processData: false, //Debe estar en false para que JQuery no procese los datos a enviar
        cache: false //Para que el formulario no guarde cache
    }).done(function (msg) {//Escuchamos la respuesta y capturamos el mensaje msg
//        mensajeFinal(msg, cual);
        traerArchivos(id, cual);
    });
    return false;
}

function traerArchivos(evento_id, cual) {
    var archivos = new FormData();
    archivos.append('evento_id', evento_id);
    archivos.append('accion', 'traer1');
    archivos.append('cual', cual);
//    archivos.append('arch', cual);
//alert(evento_id+cual);
    $.ajax({
        url: 'src/upload.php', //Url a donde la enviaremos
        type: 'POST', //Metodo que usaremos
        data: "accion=traer&evento_id=" + evento_id + "&cual=" + cual, //Para que el formulario no guarde cache
        success: function (data) {
//            alert(data);
            $('#carg-' + cual).html(data);//A el div con la clase msg, le insertamos el mensaje en formato  thml
            $('.carg_' + cual).show('slow');
        }
    });
    return false;
}

function mensajeFinal(msg, per) {
    $('.mensaje_' + per).html(msg);//A el div con la clase msg, le insertamos el mensaje en formato  thml
    $('.mensaje_' + per).show('slow');//Mostramos el div.
}

function borrarArchivo(id, arch, cual) {
    var archivos = new FormData();
    archivos.append('arch', arch);
    archivos.append('accion', 'borrar');
    archivos.append('evento_id', id);
    $.ajax({
        url: 'src/upload.php', //Url a donde la enviaremos
        type: 'POST', //Metodo que usaremos
        data: "accion=borrar&evento_id=" + id + "&arch=" + arch, //Le pasamos el objeto que creamos con los archivos
        success: function (data) {
            traerArchivos(id, cual);
        }
    });
    return false;
}
//-----------------Cargue archivos Encuestas----------------//
$(document).ready(function () {
    $('input[name=imagen_preg]').change(function () {
        if ($(this).val() !== '' && $("#hid_preg_id").val() === '') {
//            alert($(this).attr("id"));
            subirArchivosPreg('n', $(this).attr("id"));
        } else {
            subirArchivosPreg($("#hid_preg_id").val(), $(this).attr("id"));
        }
    });
});
function archivosPreg(id) {
    var archivos = new FormData();
    var enc = $("#enc_id").val();
    var mod = $("#modulo").val();
    archivos.append('accion', 'traer_preg');
    archivos.append('pregunta', id);
    archivos.append('encuesta', enc);
    archivos.append('modulo', mod);
//    archivos.append('arch', cual);
//alert(evento_id+cual);
    $.ajax({
        url: 'src/upload.php', //Url a donde la enviaremos
        type: 'POST', //Metodo que usaremos
        data: "accion=traer_preg&pregunta=" + id + "&encuesta=" + enc + "&modulo=" + mod, //Para que el formulario no guarde cache
        success: function (data) {
//            alert(data);
            $("#imagen_preg").slideUp(300);
            $('#carg-img_preg').html(data);//A el div con la clase msg, le insertamos el mensaje en formato  thml
            $('#carg-img_preg').slideDown(300);
        }
    });
    return false;
}

function subirArchivosPreg(id, cual) {
    var archivos = document.getElementById(cual);//Creamos un objeto con el elemento que contiene los archivos: el campo input file, que tiene el id = 'archivos'
    var archivo = archivos.files; //Obtenemos los archivos seleccionados en el imput
    //Creamos una instancia del Objeto FormDara.
    var archivos = new FormData();
    /* Como son multiples archivos creamos un ciclo for que recorra la el arreglo de los archivos seleccionados en el input
     Este y añadimos cada elemento al formulario FormData en forma de arreglo, utilizando la variable i (autoincremental) como 
     indice para cada archivo, si no hacemos esto, los valores del arreglo se sobre escriben*/
    for (i = 0; i < archivo.length; i++) {
        archivos.append('archivo' + i, archivo[i]); //Añadimos cada archivo a el arreglo con un indice direfente
    }
    var enc = $("#enc_id").val();
    var mod = $("#modulo").val();
    archivos.append('cual', cual);
    archivos.append('accion', 'agr_preg');
    archivos.append('pregunta', id);
    archivos.append('encuesta', enc);
    archivos.append('modulo', mod);
    /*Ejecutamos la función ajax de jQuery*/
    $.ajax({
        url: 'src/upload.php', //Url a donde la enviaremos
        type: 'POST', //Metodo que usaremos
        contentType: false, //Debe estar en false para que pase el objeto sin procesar
        data: archivos, //Le pasamos el objeto que creamos con los archivos
        processData: false, //Debe estar en false para que JQuery no procese los datos a enviar
        cache: false //Para que el formulario no guarde cache
    }).done(function (msg) {//Escuchamos la respuesta y capturamos el mensaje msg
//        mensajeFinal(msg, cual);
        archivosPreg(id);
//        alert(msg);
    });
    return false;
}

function borrarArchivoPreg(id) {
    var archivos = new FormData();
    var enc = $("#enc_id").val();
    var mod = $("#modulo").val();
    var arch = $("#hid_url_preg").val();
    archivos.append('accion', 'borrar_preg');
    archivos.append('pregunta', id);
    archivos.append('encuesta', enc);
    archivos.append('modulo', mod);
    $.ajax({
        url: 'src/upload.php', //Url a donde la enviaremos
        type: 'POST', //Metodo que usaremos
        data: "accion=borrar_preg&pregunta=" + id + "&encuesta=" + enc + "&modulo=" + mod + "&arch=" + arch, //Le pasamos el objeto que creamos con los archivos
        success: function (data) {
            $("#carg-img_preg").slideUp(300);
            $("#carg-img_preg").html("");
            $('#imagen_preg').val("");
            $('#imagen_preg').slideDown(300);
        }
    });
    return false;
}

//------------------ARCHIVOS RESPUESTAS----------------------//
function archivosResp(cual) {
    var enc = $("#enc_id").val();
    var preg = $("#pregunta").val();
    var n = cual.match(/(\D+)(\d+)$/);
    var resp = $("#hid_resp_id"+n[2]).val();
    $.ajax({
        url: 'src/upload.php', //Url a donde la enviaremos
        type: 'POST', //Metodo que usaremos
        data: "accion=traer_resp&respuesta=" + resp + "&encuesta=" + enc + "&pregunta=" + preg + "&cual=" + n[2], //Para que el formulario no guarde cache
        success: function (data) {
//            alert(data);
            $("#imagen_resp"+n[2]).slideUp(300);
            $('#carg-img_resp'+n[2]).html(data);//A el div con la clase msg, le insertamos el mensaje en formato  thml
            $('#carg-img_resp'+n[2]).slideDown(300);
        }
    });
    return false;
}

function subirArchivosResp(cual) {
    var archivos = document.getElementById(cual);//Creamos un objeto con el elemento que contiene los archivos: el campo input file, que tiene el id = 'archivos'
    var archivo = archivos.files; //Obtenemos los archivos seleccionados en el imput
    //Creamos una instancia del Objeto FormDara.
    var data = new FormData();
    var n = cual.match(/(\D+)(\d+)$/);
//    alert($("#hid_resp_id"+n[2]).val());
    /* Como son multiples archivos creamos un ciclo for que recorra la el arreglo de los archivos seleccionados en el input
     Este y añadimos cada elemento al formulario FormData en forma de arreglo, utilizando la variable i (autoincremental) como 
     indice para cada archivo, si no hacemos esto, los valores del arreglo se sobre escriben*/
    var enc = $("#enc_id").val();
    var preg = $("#pregunta").val();
    var resp = $("#hid_resp_id"+n[2]).val();
//    alert(cual);
    data.append('accion', 'agr_resp');
    data.append('encuesta', enc);
    data.append('pregunta', preg);
    data.append('respuesta', resp);
    for (i = 0; i < archivo.length; i++) {
        data.append('archivo' + i, archivo[i]); //Añadimos cada archivo a el arreglo con un indice direfente
    }
//    data.append('modulo', mod);
    /*Ejecutamos la función ajax de jQuery*/
    $.ajax({
        url: 'src/upload.php', //Url a donde la enviaremos
        type: 'POST', //Metodo que usaremos
        contentType: false, //Debe estar en false para que pase el objeto sin procesar
        data: data, //Le pasamos el objeto que creamos con los archivos
//        data: "accion=agr_resp&respuesta=" + resp + "&encuesta=" + enc + "&pregunta=" + preg +"&"+data, //Le pasamos el objeto que creamos con los archivos
        processData: false, //Debe estar en false para que JQuery no procese los datos a enviar
        cache: false //Para que el formulario no guarde cache
    }).done(function (msg) {//Escuchamos la respuesta y capturamos el mensaje msg
//        mensajeFinal(msg, cual);
        archivosResp(cual);
//        alert(msg);
    });
    return false;
}

function borrarArchivoResp(id,cual) {
    var archivos = new FormData();
    var enc = $("#enc_id").val();
    var preg = $("#pregunta").val();
//    var n = cual.match(/(\D+)(\d+)$/);
    var arch = $("#hid_url_resp"+cual).val();
    var resp = $("#hid_resp_id"+cual).val();
    $.ajax({
        url: 'src/upload.php', //Url a donde la enviaremos
        type: 'POST', //Metodo que usaremos
        data: "accion=borrar_resp&respuesta=" + id + "&encuesta=" + enc + "&pregunta=" + preg + "&arch=" + arch, //Le pasamos el objeto que creamos con los archivos
        success: function (data) {
            $("#carg-img_resp"+cual).slideUp(300);
            $("#carg-img_resp"+cual).html("");
            $('#imagen_resp'+cual).val("");
            $('#imagen_resp'+cual).slideDown(300);
        }
    });
    return false;
}