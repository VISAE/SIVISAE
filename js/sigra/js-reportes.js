/* 
 * 
 *   @author Ing. Andres Mendez
 * 
 */
$(document).ready(function () {
    $("#asistentes").click(function(){
        var dataString = $('#filtros').find("select, input").serialize();
//        var dataString = {
//                                "accion": "listado",
//                                "buscar": $("#buscar").val(),
//                                "proyecto": $("#proyecto").val(),
//                                "evento": $("#evento").val(),
//                                "linea": $("#linea").val(),
//                                "cobertura": $("#cobertura").val()
//                            };
                    $.ajax({
                        type: 'POST',
                        url: 'src/reportesCB.php',
//                        data: "accion=listado",
                        data: "accion=listado_eventos_asistentes&"+dataString,
                        beforeSend: function () {
                            startLoad("carg");
                        },
                        success: function (data) {
//                            alert(data);
                            $('#list_graduados').html(data);
//alert(data);
                            stopLoad("carg");
                            
                            $("#list_graduados").on("click", ".pagination a", function (e) {
                                startLoad("carg");
                                e.preventDefault();
                                var page = $(this).attr("data-page"); //get page number from link
                                $("#list_graduados").load("src/reportesCB.php", {
                                    "accion":"listado",
                                    "page": page,
                                    "buscar": $("#buscar").val(),
                                    "proyecto": $("#proyecto").val(),
                                "evento": $("#evento").val(),
                                "linea": $("#linea").val(),
                                "cobertura": $("#cobertura").val(),
                                    "registros": $("#registros").val()
                                },
                                function () { //get content from PHP page
                                    stopLoad("carg");
                                });
                                document.getElementById('p_fieldset_autenticacion_2').scrollIntoView(true);
                            });
                        }

                    });
    });
    $("#registrados").click(function(){
        var dataString = $('#filtros').find("select, input").serialize();
//        var dataString = {
//                                "accion": "listado",
//                                "buscar": $("#buscar").val(),
//                                "proyecto": $("#proyecto").val(),
//                                "evento": $("#evento").val(),
//                                "linea": $("#linea").val(),
//                                "cobertura": $("#cobertura").val()
//                            };
                    $.ajax({
                        type: 'POST',
                        url: 'src/reportesCB.php',
//                        data: "accion=listado",
                        data: "accion=listado_eventos_registrados&"+dataString,
                        beforeSend: function () {
                            startLoad("carg");
                        },
                        success: function (data) {
//                            alert(data);
                            $('#list_graduados').html(data);
//alert(data);
                            stopLoad("carg");
                            
                            $("#list_graduados").on("click", ".pagination a", function (e) {
                                startLoad("carg");
                                e.preventDefault();
                                var page = $(this).attr("data-page"); //get page number from link
                                $("#list_graduados").load("src/reportesCB.php", {
                                    "accion":"listado",
                                    "page": page,
                                    "buscar": $("#buscar").val(),
                                    "proyecto": $("#proyecto").val(),
                                "evento": $("#evento").val(),
                                "linea": $("#linea").val(),
                                "cobertura": $("#cobertura").val(),
                                    "registros": $("#registros").val()
                                },
                                function () { //get content from PHP page
                                    stopLoad("carg");
                                });
                                document.getElementById('p_fieldset_autenticacion_2').scrollIntoView(true);
                            });
                        }

                    });
    });
});
function crearReporte(cual) {
//    var form = document.estudiantes_asignados;
//    var dataS = $(form).serialize();
    var dataS = $('#filtros').find("select, input").serialize();
    $.ajax({
        type: 'POST',
        url: 'src/reportesCB.php',
        data: "accion="+cual+"&"+dataS,
        beforeSend: function () {
            $('#spinner').show();
        },
        success: function (data) {
            $('#spinner').hide();
            swal({
                title: '¡Descargue su documento!',
                html: "<a href='" + data + "' id='pdf' class='botones'>AQUI</a>",
                type: 'success',
                confirmButtonColor: '#004669',
                confirmButtonText: 'Aceptar'
            });

        }
    });
    return false;
}
function startLoad(div) {
    $('#' + div).show();
    if (div !== "carg") {
        $("#" + div).introLoader({
            animation: {
                name: 'simpleLoader',
                options: {
                    stop: false,
                    fixed: false,
                    exitFx: 'fadeOut',
                    ease: "linear",
                    style: 'light',
                    customGifBgColor: '#E8E8E8'
                }
            },
            spinJs: {
                lines: 13, // The number of lines to draw 
                length: 10, // The length of each line 
                width: 5, // The line thickness 
                radius: 10, // The radius of the inner circle 
                corners: 1, // Corner roundness (0..1) 
                color: '#004669', // #rgb or #rrggbb or array of colors 
            }
        });
    } else {
        $("#" + div).introLoader({
            animation: {
                name: 'simpleLoader',
                options: {
                    stop: false,
                    fixed: false,
                    exitFx: 'fadeOut',
                    ease: "linear",
                    style: 'light'
                }
            },
            spinJs: {
                lines: 13, // The number of lines to draw 
                length: 30, // The length of each line 
                width: 10, // The line thickness 
                radius: 30, // The radius of the inner circle 
                corners: 1, // Corner roundness (0..1) 
                color: '#004669', // #rgb or #rrggbb or array of colors 
            }
        });
    }
}
function stopLoad(div) {
//                    $('#list_graduados').show();
    $('#' + div).hide();
    var loader = $('#' + div).data('introLoader');
    if(loader){
        loader.stop();
    }
}