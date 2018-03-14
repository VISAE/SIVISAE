/* 
 * 
 *   @author Ing. Andres Mendez
 * 
 */

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
                color: '#004669' // #rgb or #rrggbb or array of colors 
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
                color: '#004669' // #rgb or #rrggbb or array of colors 
            }
        });
    }
}
function stopLoad(div) {
//                    $('#list_graduados').show();
    $('#' + div).hide();
    var loader = $('#' + div).data('introLoader');
    loader.stop();
}

function limpiaForm(miForm) {
    // recorremos todos los campos que tiene el formulario
    
    $(':input', miForm).each(function () {
        var type = this.type;
        var tag = this.tagName.toLowerCase();
        //limpiamos los valores de los campos…
        if (type === 'text' || type === 'password' || tag === 'textarea' || type === 'number' || type==='file'){
            this.value = "";
            $(this).removeClass("error");
        }
        // excepto de los checkboxes y radios, le quitamos el checked
        // pero su valor no debe ser cambiado
        else if (type === 'checkbox' || type === 'radio')
            this.checked = false;
        // los selects le ponesmos el indice a -
        else if (tag === 'select')
        {
            this.selectedIndex = 0;
            $(this).val('').trigger("chosen:updated");
//            $(this).chosen('destroy');
//            $(this).chosen({no_results_text: "No se encontraron Coincidencias!", width: "200px"});
//            $(this).prop('selectedIndex', 0);
        }
    });
}