$(function(){

if ($(window).width() < 1200) {
  $("#expand_menu").show();
  $(".link_left,.link_left_2").hide()
}
	
$("#expand_menu").click(function(){

  if($("#img_expand").hasClass('rotated')){
  	$(".link_left,.link_left_2").hide();  
  } else{
  	$(".link_left,.link_left_2").addClass('nav_mobile').show();
  }
  $("#img_expand").toggleClass('rotated');
});	
 
 $( window ).resize(function() {
if ($(window).width() > 1200) {
		$(".link_left,.link_left_2").removeClass('nav_mobile').show();
		$("#img_expand").removeClass('rotated');
		$("#expand_menu").hide();
	}else{
		/*if($(".link_left")){
			$(".link_left").hide(); 
			$("#img_expand").removeClass('rotated');*/
			$("#expand_menu").show();
		}	
});
 
$(document).on( 'scroll', function(){
 
if ($(window).scrollTop() > 100) {
$('.scroll-top-wrapper').addClass('show');
} else {
$('.scroll-top-wrapper').removeClass('show');
}
});
 
$('.scroll-top-wrapper').on('click', scrollToTop);


});
 
function scrollToTop() {
verticalOffset = typeof(verticalOffset) != 'undefined' ? verticalOffset : 0;
element = $('body');
offset = element.offset();
offsetTop = offset.top;
$('html, body').animate({scrollTop: offsetTop}, 500, 'linear');
}





  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-1831799-4');


