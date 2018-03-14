function registrarAtencion(opc) {
    if (opc == 1)//Registra aspirantes
    {
        if (registrarAtencionAspirante())
        {
            var nombre = $('#nombre').val();
            var correo = $('#correo').val();
            var programa_at = $('#programa_at').val();
            var centro_at = $('#centro_at').val();
            var telefono = $('#telefono').val();
            var direccion = $('#direccion').val();
            var cedula_at = $('#cedula_at').val();
            var cat_atencion = $('#cat_atencion').val();
            var observacion = $('#observacion').val();
            var elements = document.getElementsByName("atencion_b[]");
            var tipo = "";
            for (var i = 0; i < elements.length; i++) {
                if (i != 0)
                {
                    tipo += ",";
                }
                tipo += elements[i].value;
            }

            var parametros = {
                "opcion": opc,
                "nombre": nombre,
                "correo": correo,
                "programa_at": programa_at,
                "centro_at": centro_at,
                "telefono": telefono,
                "direccion": direccion,
                "cedula_at": cedula_at,
                "cat_atencion": cat_atencion,
                "atencion_b": tipo,
                "observacion": observacion,
            };
            $.ajax({
                data: parametros,
                url: 'src/registrarAtencionCB.php',
                type: 'POST',
                beforeSend: function () {

                },
                success: function (response) {
                    $("#result2").show();
                    $("#result2").html(response);
                }
            });
            return false;
        }
    } else//Registra estudiantes y/o graduados
    {
        if (registrarAtencionGraduadoEstudiante())
        {
            var cedula_at = $('#cedula_at').val();
            var cat_atencion = $('#cat_atencion').val();
            var elements = document.getElementsByName("atencion_b[]");
            var observacion = $('#observacion').val();
var tipo = "";



            for (var i = 0; i < elements.length; i++) {
                if (i != 0)
                {
                    tipo += ",";
                }
                tipo += elements[i].value;
            }
            var parametros = {
                "opcion": opc,
                "cedula_at": cedula_at,
                "cat_atencion": cat_atencion,
                "atencion_b": tipo,
                "observacion": observacion,
            };
            $.ajax({
                data: parametros,
                url: 'src/registrarAtencionCB.php',
                type: 'POST',
                beforeSend: function () {

                },
                success: function (response) {
                    $("#result2").show();
                    $("#result2").html(response);
                }
            });
            return false;


        }
    }

}



function verificarCheck() {
    var chequeado = false;
    var elements = document.getElementsByName("atencion_b[]");
    for (var i = 0; i < elements.length; i++) {
        if (elements[i].checked) {
            chequeado = true;
        }
    }
    return chequeado;
}


function registrarAtencionAspirante() {
    if ($('#nombre').val() !== '') {
        if ($('#correo').val() !== '') {
            if (validarEmail($('#correo').val())) {
                if ($('#programa_at').val() !== '') {
                    if ($('#centro_at').val() !== '') {
                        if ($('#telefono').val() !== '') {
                            if ($('#direccion').val() !== '') {
                                if ($('#cat_atencion').chosen().val() !== null) {
                                    if (verificarCheck()) {
                                        return true;
                                    } else {
                                        swal({
                                            title: 'Campos Incompletos',
                                            text: '¡Debe seleccionar al menos el tipo de estamento al cual brindo la atención!',
                                            type: 'error',
                                            confirmButtonColor: '#004669',
                                            confirmButtonText: 'Aceptar'
                                        });
                                        $('#atencion_b').focus();
                                        return false;
                                    }

                                } else {
                                    swal({
                                        title: 'Campos Incompletos',
                                        text: '¡Debe seleccionar al menos una categoria de atención!',
                                        type: 'error',
                                        confirmButtonColor: '#004669',
                                        confirmButtonText: 'Aceptar'
                                    });
                                    $('#cat_atencion').focus();
                                    return false;
                                }
                            } else {
                                swal({
                                    title: 'Campos Incompletos',
                                    text: '¡Debe ingresar dirección!',
                                    type: 'error',
                                    confirmButtonColor: '#004669',
                                    confirmButtonText: 'Aceptar'
                                });
                                $('#correo').focus();
                                return false;
                            }
                        } else {
                            swal({
                                title: 'Campos Incompletos',
                                text: '¡Debe ingresar un número teléfonico!',
                                type: 'error',
                                confirmButtonColor: '#004669',
                                confirmButtonText: 'Aceptar'
                            });
                            $('#correo').focus();
                            return false;
                        }
                    } else {
                        swal({
                            title: 'Campos Incompletos',
                            text: '¡Debe seleccionar un centro!',
                            type: 'error',
                            confirmButtonColor: '#004669',
                            confirmButtonText: 'Aceptar'
                        });
                        $('#correo').focus();
                        return false;
                    }
                } else {
                    swal({
                        title: 'Campos Incompletos',
                        text: '¡Debe seleccionar un programa!',
                        type: 'error',
                        confirmButtonColor: '#004669',
                        confirmButtonText: 'Aceptar'
                    });
                    $('#correo').focus();
                    return false;
                }
            } else {
                swal({
                    title: 'Campos Incompletos',
                    text: '¡Debe ingresar una dirección de correo correcta!',
                    type: 'error',
                    confirmButtonColor: '#004669',
                    confirmButtonText: 'Aceptar'
                });
                $('#correo').focus();
                return false;
            }
        } else {
            swal({
                title: 'Campos Incompletos',
                text: '¡Debe ingresar el email de la persona que atendio!',
                type: 'error',
                confirmButtonColor: '#004669',
                confirmButtonText: 'Aceptar'
            });
            $('#correo').focus();
            return false;
        }
    } else {
        swal({
            title: '¡Debe ingresar el nombre de la persona que atendio!',
            text: '',
            type: 'error',
            confirmButtonColor: '#004669',
            confirmButtonText: 'Aceptar'
        });
        $('#nombre').focus();
        return false;
    }
}

function registrarAtencionGraduadoEstudiante() {
    if ($('#cat_atencion').chosen().val() !== null) {
        if (verificarCheck()) {
            return true;
        } else {
            swal({
                title: 'Campos Incompletos',
                text: '¡Debe seleccionar al menos el tipo de estamento al cual brindo la atención!',
                type: 'error',
                confirmButtonColor: '#004669',
                confirmButtonText: 'Aceptar'
            });
            $('#atencion_b').focus();
            return false;
        }

    } else {
        swal({
            title: 'Campos Incompletos',
            text: '¡Debe seleccionar al menos una categoria de atención!',
            type: 'error',
            confirmButtonColor: '#004669',
            confirmButtonText: 'Aceptar'
        });
        $('#cat_atencion').focus();
        return false;
    }
}

function validarEmail(email) {
    expr = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    if (!expr.test(email))
        return false;
    else
        return true;
}