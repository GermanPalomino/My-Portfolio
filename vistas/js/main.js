$(document).ready(function(){

    // Mostrar/Ocultar Submenús
    $('.nav-btn-submenu').on('click', function(e){
        e.preventDefault(); // Prevenir la acción por defecto del enlace
        var SubMenu = $(this).next('ul'); // Seleccionar el siguiente elemento ul
        var iconBtn = $(this).children('.fa-chevron-down'); // Seleccionar el icono del botón
        if(SubMenu.hasClass('show-nav-lateral-submenu')){
            $(this).removeClass('active'); // Quitar clase 'active' del botón
            iconBtn.removeClass('fa-rotate-180'); // Quitar rotación del icono
            SubMenu.removeClass('show-nav-lateral-submenu'); // Ocultar submenú
        } else {
            $(this).addClass('active'); // Añadir clase 'active' al botón
            iconBtn.addClass('fa-rotate-180'); // Rotar icono
            SubMenu.addClass('show-nav-lateral-submenu'); // Mostrar submenú
        }
    });

    // Mostrar/Ocultar Navegación Lateral
    $('.show-nav-lateral').on('click', function(e){
        e.preventDefault(); // Prevenir la acción por defecto del enlace
        var NavLateral = $('.nav-lateral'); // Seleccionar el contenedor de navegación lateral
        var PageConten = $('.page-content'); // Seleccionar el contenedor del contenido de la página
        if(NavLateral.hasClass('active')){
            NavLateral.removeClass('active'); // Ocultar navegación lateral
            PageConten.removeClass('active'); // Ajustar contenido de la página
        } else {
            NavLateral.addClass('active'); // Mostrar navegación lateral
            PageConten.addClass('active'); // Ajustar contenido de la página
        }
    });

});

// Inicialización del plugin jQuery Custom Content Scroller
(function($){
    $(window).on("load",function(){
        $(".nav-lateral-content").mCustomScrollbar({
            theme: "light-thin", // Tema del scrollbar
            scrollbarPosition: "inside", // Posición del scrollbar
            autoHideScrollbar: true, // Ocultar scrollbar automáticamente
            scrollButtons: {enable: true} // Habilitar botones de desplazamiento
        });
        $(".page-content").mCustomScrollbar({
            theme: "dark-thin", // Tema del scrollbar
            scrollbarPosition: "inside", // Posición del scrollbar
            autoHideScrollbar: true, // Ocultar scrollbar automáticamente
            scrollButtons: {enable: true} // Habilitar botones de desplazamiento
        });
    });
})(jQuery);

// Inicialización del plugin Popover de Bootstrap
$(function(){
	$('[data-toggle="popover"]').popover();
});