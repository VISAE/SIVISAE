/* 
 * 
 *   @author Ing. Andres Mendez
 * 
 */


///Seguridad - Inicio
$(document).ready(function () {
    $("#actualizacion").steps({
        headerTag: "h3",
        bodyTag: "section",
        transitionEffect: "slideLeft",
//        stepsOrientation: "vertical",
        //                        enableAllSteps: true,
        labels: {
            finish: "Guardar",
            loading: "Cargando..."
        },
        onStepChanging: function (event, currentIndex, newIndex) {
//            $('.wizard .content').css("height", '900px');
            document.getElementById("p_fieldset_autenticacion_2").scrollIntoView(true);
//            if (currentIndex === 0) {
//                $('.wizard .content').css("height", '1000px');
//            }
            if (newIndex === 1) {
                var h = 450;
                if ($('#programa1').length > 0) {
//                    $('.wizard .content').css("height", h+'px');
                } else {
                    var doc = $('#documento').val();
                    var tp = $('#graduado_id').val();

                    $.ajax({
                        type: "POST",
                        url: "src/actualizacion_datosCB.php",
                        data: "accion=getTitulos&documento=" + doc + "&tp=" + tp,
                        dataType: 'json',
                        beforeSend: function () {
//alert("BEFORESEND");
                        },
                        success: function (data) {
//                                alert(data["data"][0]['titulo_id']);
                            var tot = new Array("0");
                            for (var i in data["data"]) {
//                                var cod_prog = data["data"][i]['cod_prog'];
                                agregarTitulo(data["data"][i]['titulo_id'], data["data"][i]['cod_prog'], data["data"][i]['programa'], data["data"][i]['escuela'], data["data"][i]['cod_cead'], data["data"][i]['cead'], data["data"][i]['zona'], data["data"][i]['fecha_grado']);
                                h = h + 50;
//                                tot[tot.length] = cod_prog;
                                tot.push(data["data"][i]['cod_prog']);
//                                $("#actualizacion-p-1").height('auto');
//                                $('.wizard .content').css("height", h + 'px');
                                $('#cant_t').val(parseInt($('#cant_t').val()) + 1);
                            }
                            $('#inf-prog').addClass("tcar").show('slideDown');
                            $("#tot").val(tot.join("|"));
                        }
                    });
                }
            }
            var alertas = validarCampos(currentIndex);
            if (alertas.length > 0) {
                var alerta = alertas.join('<br>');
                swal({
                    title: 'Falta información!',
                    text: 'Tiene campos vacíos: <br>' + alerta,
					html: true,
                    type: 'error',
                    confirmButtonColor: '#004669',
                    confirmButtonText: 'Aceptar'
                },
                function () {
                    $("html,body").animate({scrollTop: $("#p_fieldset_autenticacion_2").offset().top}, 2000);
                }
                );
//                    $('#tel_cel').addClass("falta");
                return false;
            } else {
                return true;
            }
        },
        onFinishing: function (event, currentIndex) {
            var alertas = validarCampos(currentIndex);
            if (alertas.length > 0) {
                var alerta = alertas.join('<br>');
                swal({
                    title: 'Falta información!',
                    html: 'Tiene campos vacíos: <br>' + alerta,
                    type: 'error',
                    confirmButtonColor: '#004669',
                    confirmButtonText: 'Aceptar'
                });
                return false;
            } else {
                actualizarGraduado();
            }
        }
    });
    $(".chosen-select").chosen({no_results_text: "No se encontraron Coincidencias!", width: "80%"});
    $("#programa").chosen({no_results_text: "No se encontraron Coincidencias!", width: "100%"});
    $("input:text").width("80%");
    $("input").iCheck({
        checkboxClass: "icheckbox_polaris",
        radioClass: "iradio_polaris",
        increaseArea: "-20%" // optional
    });
    $("#sit_lab").change(function () {
        if ($(this).val() === "Empleado" || $(this).val() === "Empresario" || $(this).val() === "Comerciante" || $(this).val() === "Independiente") {
            $(".inf_lab").show("slideDown");
            $(".inf_cual").hide("slideUp");
//            $("#actualizacion-p-3").height(700);
        } else {
            if ($(this).val() === "Otra actividad (¿cuál?)") {
                $(".inf_cual").show("slideDown");
                $(".inf_lab").hide("slideUp");
            } else {
                $(".inf_lab").hide("slideUp");
                $(".inf_cual").hide("slideUp");
                //            $("#actualizacion-p-3").height(450);
            }
        }
    });
    $("#pais_nac").change(function () {
        $.ajax({
            type: "POST",
            url: "src/actualizacion_datosCB.php",
            data: "accion=ciudades&pais=" + $(this).val(),
            beforeSend: function () {
                $("#city_nac").html("<option value=''>Cargando...</option>").trigger("chosen:updated");
            },
            success: function (data) {
                $("#city_nac").html(data).trigger("chosen:updated");
            }
        });
    });
    $("#programa").change(function () {
        var tot = $("#tot").val().split("|");
//        alert(tot.indexOf($(this).val()));
//        alert(jQuery.inArray($(this).val(), tot));
        if (jQuery.inArray($(this).val(), tot) > 0) {
            swal({
                title: '¡Imposible!',
                text: "El programa seleccionado ya se encuentra entre sus Estudios.",
                type: 'error',
                confirmButtonColor: '#004669',
                confirmButtonText: 'Aceptar'
            });
            $(this).val("").trigger("chosen:updated");
        } else {
            $("#cod_programa").val($(this).val());
            $.ajax({
                type: "POST",
                url: "src/actualizacion_datosCB.php",
                data: "accion=escuela&cod_prog=" + $(this).val(),
                beforeSend: function () {
                    $("#escuela").val("Cargando...");
                },
                success: function (data) {
                    var res = data.split("|");
                    $("#escuela").val(res[0]);
                    $("#niv_aca").val(res[1]);
                }
            });
        }
    });
    $("#cead").change(function () {
        $("#cod_cead").val($(this).val());
        $.ajax({
            type: "POST",
            url: "src/actualizacion_datosCB.php",
            data: "accion=zona&cead=" + $(this).val(),
            beforeSend: function () {
                $("#zona").val("Cargando...");
            },
            success: function (data) {
                $("#zona").val(data);
            }
        });
    });
    $("#pais_res").change(function () {
        $.ajax({
            type: "POST",
            url: "src/actualizacion_datosCB.php",
            data: "accion=ciudades&pais=" + $(this).val(),
            beforeSend: function () {
                $("#city_res").html("<option value=''>Cargando...</option>").trigger("chosen:updated");
            },
            success: function (data) {
                $("#city_res").html(data).trigger("chosen:updated");
            }
        });
    });
    $("#documento, #tel_res, #tel_cel, #tel_res_fam, #tel_cel_fam, #tel_of").keydown(function (event) {
        if (event.shiftKey) {
            event.preventDefault();
        }
        if (event.keyCode === 46 || event.keyCode === 8) {
        } else {
            if (event.keyCode < 95) {
                if (event.keyCode < 48 || event.keyCode > 57) {
                    event.preventDefault();
                }
            } else {
                if (event.keyCode < 96 || event.keyCode > 105) {
                    event.preventDefault();
                }
            }
        }
    });

    $("#cancel").click(function () {
        $('#info-titulo').slideUp('500');
        limpiaForm($('#info-titulo'));
        $('#add').slideDown('300');
        return false;
    });
    $("#save").click(function () {
        var $tr = $("#inf-prog").
                find("tbody tr:last").
                clone().
                appendTo("#inf-prog tbody").
                show();
        // obtener el atributo name para los inputs y selects
        $tr.find("input:text").val("");
        var n = "";
        $tr.find("input, a").attr("name", function () {
            //  separar el campo name y su numero en dos partes
            var parts = this.id.match(/(\D+)(\d+)$/);
            // crear un nombre nuevo para el nuevo campo incrementando el numero para los previos campos en 1
            n = ++parts[2];
            return parts[1] + n;
            // repetir los atributos ids
        }).attr("num", n).attr("id", function () {
            var parts = this.id.match(/(\D+)(\d+)$/);
            return parts[1] + ++parts[2];
        }).val(function () {
            var parts = this.id.match(/(\D+)(\d+)$/);
            var value = "";
            if (parts[1] === 'programa' || parts[1] === 'niv_aca' || parts[1] === 'cead') {
                value = $('#' + parts[1] + ' option:selected').html();
            } else {
                value = $('#' + parts[1]).val();
            }

            return value;
        }).width(function () {
            var parts = this.id.match(/(\D+)(\d+)$/);
            var value = "";
            if (parts[1] === 'programa') {
                value = '100%';
            }
            if (parts[1] === 'cead') {
                value = '100%';
            }
            if (parts[1] === 'fecha_grado') {
                value = '100%';
            }
            return value;
        });
        // añadir la nueva fila a la tabla
        $("#inf-prog").find("tbody tr:last").after($tr);
//        $("#inf-prog tbody tr:eq(0)").clone().removeClass('base').appendTo("#inf-prog tbody").show();
//        alert($(this).parents('table')
//          .find('input, select')
//          .not('input[type=button]')
//          .serialize());
        $('#fecha_g' + n).val($('#fecha_grado' + n).val());
        $('#cant_t').val(parseInt($('#cant_t').val()) + 1);
        $('#info-titulo').slideUp('500');
//        $('#info-titulo').find('input, select').val("").trigger("chosen:updated");
        limpiaForm($('#info-titulo'));
        $('#add').slideDown('300');
        return false;
    });

//    $(".editar").click(function () {
//        var num = $(this).attr("num");
//        alert(num);
//        alert($("#inf-prog").find("tbody tr:eq("+num+")")
//          .find('input,textarea')
//          .not('a')
//          .serialize());
//        return false;
//    });

//    $(".enviar").click(function () {
//        alert($(this).parents('table')
//          .find('input')
//          .not('a')
//          .serialize());
//        return false;
//    });
    $(document).on("click", ".eliminar", function () {
        var parent = $(this).parents().get(0);
//        alert(parent);
        $(parent).remove();
//                alert($(parent)
//          .find('input, textarea')
//          .serialize());
    });
    $(document).on("click", ".editar", function () {
//        var parent = $(this).parents().get(0);
        var parent = $(this).parents().get(0);
//        alert(parent);
//        $(parent).remove();
//                alert($(parent)
//          .find('input').attr("num"));
//            alert($(this).find('input').serializeArray().val());
//          $('#programa').val().trigger('chosen:updated');
    });

    $("#tel_cel").change(function () {
        var str = $("#tel_cel").val();
        if (str.length < 10) {
            swal({
                title: '¡Por favor!',
                text: 'El número de celular debe ser mínimo de 10 dígitos',
                type: 'error',
                confirmButtonColor: '#004669',
                confirmButtonText: 'Aceptar'
            });
            $("#tel_cel").focus();
            $("#tel_cel").val("");
            return false;
        }

        var n = str.indexOf("@");
        if (ValidaEmail($("#email").val()) === false)
        {
            swal({
                title: '¡Por favor!',
                text: 'Ingrese una dirección de correo electrónico válido',
                type: 'error',
                confirmButtonColor: '#004669',
                confirmButtonText: 'Aceptar'
            });
            $("#email").focus();
            $("#email").val("");
            return false;
        }
    });

    $("#email").change(function () {
        var str = $("#email").val();
        var n = str.indexOf("@");
        if (ValidaEmail($("#email").val()) === false)
        {
            swal({
                title: '¡Por favor!',
                text: 'Ingrese una dirección de correo electrónico válido',
                type: 'error',
                confirmButtonColor: '#004669',
                confirmButtonText: 'Aceptar'
            });
            $("#email").focus();
            $("#email").val("");
            return false;
        }
    });

    $("#email2").change(function () {
        var str = $("#email2").val();
        var n = str.indexOf("@");
        if (ValidaEmail($("#email2").val()) === false)
        {
            swal({
                title: '¡Por favor!',
                text: 'Ingrese una dirección de correo electrónico alterno válido',
                type: 'error',
                confirmButtonColor: '#004669',
                confirmButtonText: 'Aceptar'
            });
            $("#email2").focus();
            $("#email2").val("");
            return false;
        }
    });

//    $("#inf-prog").jqGrid({
//        url:'src/titulos.php',
//                    datatype: 'json',
//                    mtype: 'POST',
//                    width: '90%',
//                    colNames:['NOMBRE', 'TITULO','CEAD','FECHA'],
//                    colModel:[
//                        {name:'nombre', index:'nombre', width:160,resizable:false, sortable:true},
//                        {name:'titulo', index:'titulo', width:150},
//                        {name:'cead', index:'cead', width:120},
//                        {name:'fecha', index:'fecha', width:70}
//                    ],
//                    pager: '#paginacion',
//                    rowNum:10,
//                    rowList:[15,30],
//                    sortname: 'fecha',
//                    sortorder: 'asc',
//                    viewrecords: true,
//                    caption: 'TITULOS OBTENIDOS'
//    });
});
function ValidaEmail(email) {
    var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    return regex.test(email);
}

function agregar() {
//        var num = $('.editar').attr("num");
//        alert(num);
//        alert($("#inf-prog").find("tbody tr:eq("+num+")")
//          .find('input,label')
//          .not('a')
//          .serialize());
//        return false;
    $('#info-titulo').slideDown('500');
    $('#add').slideUp('300');
    return false;
}

function agregarTitulo(titulo_id, cod_prog, prog, escuela, cod_cead, cead, zona, fecha_grado) {
    var $tr = $("#inf-prog").find("tbody tr:last").clone().appendTo("#inf-prog tbody").show();
    // obtener el atributo name para los inputs y selects
    $tr.find("input:text").val("");
    var n = "";
    $tr.find("input, a").attr("name", function () {
        //  separar el campo name y su numero en dos partes
        var parts = this.id.match(/(\D+)(\d+)$/);
        // crear un nombre nuevo para el nuevo campo incrementando el numero para los previos campos en 1
        return parts[1] + ++parts[2];
        // repetir los atributos ids
    }).attr("id", function () {
        var parts = this.id.match(/(\D+)(\d+)$/);
        n = ++parts[2];
        return parts[1] + n;
    }).val(function () {
    }).attr("num", n)
            .val(function () {
                var parts = this.id.match(/(\D+)(\d+)$/);
                var value = "";
                if (parts[1] === 'programa') {
                    value = prog;
                }
                if (parts[1] === 'titulo_id') {
                    value = titulo_id;
                }
                if (parts[1] === 'cod_programa') {
                    value = cod_prog;
                }
                if (parts[1] === 'cod_cead') {
                    value = cod_cead;
                }
                if (parts[1] === 'cead') {
                    value = cead;
                }
                if (parts[1] === 'escuela') {
                    value = escuela;
                }
                if (parts[1] === 'zona') {
                    value = zona;
                }
                if (parts[1] === 'fecha_grado') {
                    value = fecha_grado;
                }

                return value;
            }).width(function () {
        var parts = this.id.match(/(\D+)(\d+)$/);
        var value = "";
        if (parts[1] === 'programa') {
            value = '100%';
        }
        if (parts[1] === 'cead') {
            value = '100%';
        }
        if (parts[1] === 'fecha_grado') {
            value = '100%';
        }
        return value;
    });
    // añadir la nueva fila a la tabla
    $('#fecha_g' + n).val($('#fecha_grado' + n).val());
    $("#inf-prog").find("tbody tr:last").after($tr);
}
