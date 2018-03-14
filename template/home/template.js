function cargarEventosTemplate(){
	
jQuery("div.menuMobile").click(function() {
	jQuery("nav").toggleClass("mostrarMobile");
	jQuery("div.menuMobileGhost").toggleClass("menuMobileGhostMostrar");
});

jQuery("div.menuMobileGhost").click(function() {
	jQuery("nav").toggleClass("mostrarMobile");
	jQuery("div.menuMobileGhost").toggleClass("menuMobileGhostMostrar");
});

}