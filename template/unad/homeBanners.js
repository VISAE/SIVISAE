function cargarEventosBannersHome(){
	bannerTimmer();
jQuery("div.bannerPrincipal div.imagenPrincipal a.banner").mouseover(function(){
	controlBannersMouseOver();
});

jQuery("div.bannerPrincipal div.imagenPrincipal a.banner").mouseout(function(){
	timerBannersControlHide = setTimeout("controlBannersMouseOut()", 300);
});

jQuery("div.bannerPrincipal div.imagenPrincipal div.control").mouseover(function(){
	controlBannersMouseOver();
});

jQuery("div.bannerPrincipal div.imagenPrincipal div.control").mouseout(function(){
	timerBannersControlHide = setTimeout("controlBannersMouseOut()", 300);
});

jQuery("div.bannerPrincipal div.imagenPrincipal a.ban1").mouseover(function(){
	panelBanners(1,1);
});

jQuery("div.bannerPrincipal div.imagenPrincipal a.ban1").mouseout(function(){
	bannerTimmer();
});

jQuery("div.bannerPrincipal div.imagenPrincipal a.ban2").mouseover(function(){
	panelBanners(2,1);
});

jQuery("div.bannerPrincipal div.imagenPrincipal a.ban2").mouseout(function(){
	bannerTimmer();
});

jQuery("div.bannerPrincipal div.imagenPrincipal a.ban3").mouseover(function(){
	panelBanners(3,1);
});

jQuery("div.bannerPrincipal div.imagenPrincipal a.ban3").mouseout(function(){
	bannerTimmer();
});

jQuery("div.bannerPrincipal div.imagenPrincipal a.ban4").mouseover(function(){
	panelBanners(4,1);
});

jQuery("div.bannerPrincipal div.imagenPrincipal a.ban4").mouseout(function(){
	bannerTimmer();
});

jQuery("div.bannerPrincipal div.imagenPrincipal a.ban5").mouseover(function(){
	panelBanners(5,1);
});

jQuery("div.bannerPrincipal div.imagenPrincipal a.ban5").mouseout(function(){
	bannerTimmer();
});


jQuery("div.bannerPrincipal div.imagenPrincipal div.control img.miniatura1").click(function(){
	panelBanners(1,1);
});

jQuery("div.bannerPrincipal div.imagenPrincipal div.control img.miniatura1").mouseover(function(){
	panelBanners(1,1);
});

jQuery("div.bannerPrincipal div.imagenPrincipal div.control img.miniatura2").click(function(){
	panelBanners(2,1);
});

jQuery("div.bannerPrincipal div.imagenPrincipal div.control img.miniatura2").mouseover(function(){
	panelBanners(2,1);
});

jQuery("div.bannerPrincipal div.imagenPrincipal div.control img.miniatura3").click(function(){
	panelBanners(3,1);
});

jQuery("div.bannerPrincipal div.imagenPrincipal div.control img.miniatura3").mouseover(function(){
	panelBanners(3,1);
});

jQuery("div.bannerPrincipal div.imagenPrincipal div.control img.miniatura4").click(function(){
	panelBanners(4,1);
});

jQuery("div.bannerPrincipal div.imagenPrincipal div.control img.miniatura4").mouseover(function(){
	panelBanners(4,1);
});

jQuery("div.bannerPrincipal div.imagenPrincipal div.control img.miniatura5").click(function(){
	panelBanners(5,1);
});

jQuery("div.bannerPrincipal div.imagenPrincipal div.control img.miniatura5").mouseover(function(){
	panelBanners(5,1);
});

jQuery("div.bannerPrincipal div.textoPrincipal div.controlIndicadores div.num1").click(function(){
	panelBanners(1,1);
});

jQuery("div.bannerPrincipal div.textoPrincipal div.controlIndicadores div.num2").click(function(){
	panelBanners(2,1);
});

jQuery("div.bannerPrincipal div.textoPrincipal div.controlIndicadores div.num3").click(function(){
	panelBanners(3,1);
});

jQuery("div.bannerPrincipal div.textoPrincipal div.controlIndicadores div.num4").click(function(){
	panelBanners(4,1);
});

jQuery("div.bannerPrincipal div.textoPrincipal div.controlIndicadores div.num5").click(function(){
	panelBanners(5,1);
});

}

function controlBannersMouseOver(){
	jQuery("div.bannerPrincipal div.imagenPrincipal div.control").removeClass("hide");
	clearTimeout(timerBannersControlHide);
}

function controlBannersMouseOut(){
	jQuery("div.bannerPrincipal div.imagenPrincipal div.control").addClass("hide");
}




/* Banners Principal*/

var bannerActual=1;
var playBanners=true;
var animacionTerminada=true;
var jTimer=0;
var jTimerMenos=10;
var timerBannerID;
var timerBannerChange;
var timerBannerID_PRE;

function panelBanners(opcion,timerActivo){
	clearTimeout(timerBannerID_PRE);
	clearTimeout(timerBannerID);
	timerBannerID_PRE = setTimeout("panelBannersPOST("+opcion+","+timerActivo+")", 200);
}

function panelBannersPOST(opcion,timerActivo){
	clearTimeout(timerBannerID_PRE);
	clearTimeout(timerBannerID);
	if(bannerActual!=opcion)
	{
		for(i=1;i<=5;i++)
		{
			jQuery("div.bannerPrincipal div.imagenPrincipal a.ban"+i).css("display","none");
			jQuery("div.bannerPrincipal div.imagenPrincipal div.control img.miniatura"+i).css("border","2px solid #333");
			jQuery("div.bannerPrincipal div.textoPrincipal div.con"+i).css("display","none");
			/*window.document.getElementById('bannerMensajeContenedor'+i).style.display="none";
			window.document.getElementById('bannerImagenPrincipalMiniLoad'+i).className="bannerImagenPrincipalMiniatura";
			window.document.getElementById('bannerImagenPrincipalIndicador'+i).className="bannerImagenPrincipalIndicador";*/
			jQuery("div.bannerPrincipal div.textoPrincipal div.controlIndicadores div.num"+i).removeClass("indicadorSeleccionado");
		}
		jQuery("div.bannerPrincipal div.imagenPrincipal a.ban"+opcion).css("display","block");
		jQuery("div.bannerPrincipal div.imagenPrincipal div.control img.miniatura"+opcion).css("border","2px solid #FFF");
		jQuery("div.bannerPrincipal div.imagenPrincipal a.ban"+bannerActual).css("display","block");
		jQuery("div.bannerPrincipal div.imagenPrincipal a.ban"+bannerActual).css("opacity","1");
		jQuery("div.bannerPrincipal div.imagenPrincipal a.ban"+bannerActual).css("filter","alpha(opacity=100)");
		jQuery("div.bannerPrincipal div.imagenPrincipal a.ban"+opcion).css("opacity","0");
		jQuery("div.bannerPrincipal div.imagenPrincipal a.ban"+opcion).css("filter","alpha(opacity=0)");
		bannerChancheTime(bannerActual,opcion);
		jQuery("div.bannerPrincipal div.textoPrincipal div.con"+opcion).css("display","block");
		/*window.document.getElementById('bannerMensajeContenedor'+opcion).style.display="block";*/
		jQuery("div.bannerPrincipal div.textoPrincipal div.controlIndicadores div.num"+opcion).addClass("indicadorSeleccionado");
		jQuery("div.bannerPrincipal div.textoPrincipal div.controlIndicadores div.numero").html(opcion+" de 5");

		
	}
	 bannerActual=opcion;
}

function bannerTimmer(){
	clearTimeout(timerBannerID_PRE);
	clearTimeout(timerBannerID);
	if(playBanners==true){		
	timerBannerID = setTimeout("bannerChange()", 5000);
	}
}

function bannerChancheTime(bannerChangeActual,bannerChangeSiguiente){
	
	timerBannerChange = setTimeout("bannerChancheTimmer("+bannerChangeActual+","+bannerChangeSiguiente+")", 20); //30
}

function bannerChancheTimmer(bannerChangeNActual,bannerChangeNSiguiente){	
jTimer++;
jTimerMenos--;
jQuery("div.bannerPrincipal div.imagenPrincipal a.ban"+bannerChangeNSiguiente).css("opacity","0."+jTimer);
jQuery("div.bannerPrincipal div.imagenPrincipal a.ban"+bannerChangeNSiguiente).css("filter","alpha(opacity="+jTimer+")");
jQuery("div.bannerPrincipal div.imagenPrincipal a.ban"+bannerChangeNActual).css("opacity","0."+jTimerMenos);
jQuery("div.bannerPrincipal div.imagenPrincipal a.ban"+bannerChangeNActual).css("filter","alpha(opacity="+jTimerMenos+")");
if(jTimer>=9){
	clearTimeout(timerBannerChange);
	jTimer=0;
	jTimerMenos=10;
	jQuery("div.bannerPrincipal div.imagenPrincipal a.ban"+bannerChangeNSiguiente).css("opacity","1");
	jQuery("div.bannerPrincipal div.imagenPrincipal a.ban"+bannerChangeNSiguiente).css("filter","alpha(opacity=100)");
	jQuery("div.bannerPrincipal div.imagenPrincipal a.ban"+bannerChangeNActual).css("display","none");
	jQuery("div.bannerPrincipal div.imagenPrincipal a.ban"+bannerChangeNActual).css("opacity","1");
	jQuery("div.bannerPrincipal div.imagenPrincipal a.ban"+bannerChangeNActual).css("filter","alpha(opacity=100)");
	}
	else{
	bannerChancheTime(bannerChangeNActual,bannerChangeNSiguiente);
	}
}

					
function bannerChange(){
	clearTimeout(timerBannerID);
	clearTimeout(timerBannerID_PRE);
	switch (bannerActual) 
	{ 
		case 1: 
		panelBannersPOST(2,1);
      	break 
   		case 2: 
      	panelBannersPOST(3,1);
      	break 
		case 3: 
      	panelBannersPOST(4,1);
      	break
		case 4: 
      	panelBannersPOST(5,1);
      	break
      	case 5: 
      	panelBannersPOST(1,1);
      	break					
	}
	bannerTimmer();
}

function bannerChangeBack(){
	clearTimeout(timerBannerID);
	clearTimeout(timerBannerID_PRE);
	switch (bannerActual) 
	{ 
		case 1: 
		panelBannersPOST(4,1);
      	break 
   		case 2: 
      	panelBannersPOST(1,1);
      	break 
		case 3: 
      	panelBannersPOST(2,1);
      	break
		case 4: 
      	panelBannersPOST(3,1);
      	break						
	}
	bannerTimmer();
}