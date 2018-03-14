/* 
 * 
 *   @author Ing. Andres Mendez
 * Clase para envio y recepcion de notificaciones
 */

function limpiaForm(miForm) {
    // recorremos todos los campos que tiene el formulario
    $(':input', miForm).each(function () {
        var type = this.type;
        var tag = this.tagName.toLowerCase();
        //limpiamos los valores de los campos…
        if (type === 'text' || type === 'password' || tag === 'textarea')
            this.value = "";
        // excepto de los checkboxes y radios, le quitamos el checked
        // pero su valor no debe ser cambiado
        else if (type === 'checkbox' || type === 'radio')
            this.checked = false;
        // los selects le ponesmos el indice a -
        else if (tag === 'select')
        {
            this.selectedIndex = 0;
            $(this).chosen('destroy');
            $(this).prop('selectedIndex', 0);
        }
    });
}
                
function cargar_push() { 	
    $.ajax({
        type: "POST",
        url: "src/notificacionesCB.php",
        data: "accion=contar",
        dataType: "JSON",
        success: function(data){
//            var res = data.split('||');
//            alert(data.cant);
            if(data.cant==="-1"){     
                if(data.msj>0){
                    $('#notificaciones').text("☎ ("+data.msj+")");
                    $("#a_notif").attr('class', 'last_not');
                }else {
                    $("#a_notif").attr('class', 'last');
                    $('#notificaciones').text("☎");
                }
            }else {
                $.growl.notice({title: "SIE dice:", message: data.msj});
                if(res[0]>0){
                    $('#notificaciones').text("☎ ("+data.cant+")");
                    $("#a_notif").attr('class', 'last_not');
                }else {
                    $("#a_notif").attr('class', 'last');
                    $('#notificaciones').text("☎");
                }
            }
        }
    });	
    setTimeout('cargar_push()',5000); 	
}

function leerNotif(notif_id){
    $.ajax({
        type: "POST",
        url: "src/notificacionesCB.php",
        data: "accion=leer&notif_id="+notif_id,
        dataType:"JSON",
        success: function(data){
//            alert(data);
             if (data.res === "1") {     
                        location.reload();   
                    } else {     
                        alert(data);
                    } 
        }
    });
}

function verNotif(notif_id){
    $.ajax({
        async:	true, 
        type: "POST",
        url: "src/notificacionesCB.php",
        data: "accion=ver&notif_id="+notif_id,
        dataType:"html",
        success: function(data){
            var res = data.split('|');
            var typ = res[1];
            swal({  title: res[2],   
                    text: "<p  style='word-wrap: break-word; text-align:justify'>"+res[0]+"</p>",   
                    type: typ,   
                    showCancelButton: true,   
                    confirmButtonColor: "#004669",   
                    confirmButtonText: "Aceptar",   
                    cancelButtonText: "Cancelar",   
                    closeOnConfirm: true,   
                    closeOnCancel: true,
                    html: true }, 
                function(isConfirm){   
                    if (isConfirm) {     
                        leerNotif(notif_id);   
                    } else {     
                        
                    } 
                });
        }
    });
}