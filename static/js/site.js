$(document).ready(function() {
  
	function setupExternalLinks() {
		$("a[rel='external']").attr("target", "_blank");
	}

	function rotateElement(element, degrees) {
		element.css('-webkit-transform', 'rotate(' + degrees + 'deg)');
	  	element.css('-moz-transform', 'rotate(' + degrees + 'deg)');
	  	element.css('-ms-transform', 'rotate(' + degrees + 'deg)');
	  	element.css('-o-transform', 'rotate(' + degrees + 'deg)');
	  	element.css('transform', 'rotate(' + degrees + 'deg)');
	}

	function rotateBubbles() {
		var amplitude = 1.0;
		$.each($("div.bubble:not(.no-rotate)"), function(i, el){
			el = $(el);
			rotateElement(el, 2 * amplitude * (Math.random()-0.5));
			amplitude = -amplitude;
		});
	}

	function rotateRibbons() {
		var amplitude = 0.9;
		$.each($("h2:not(.no-rotate)"), function(i, el){
			el = $(el);
			rotateElement(el, 2 * amplitude * (Math.random()-0.5));
			amplitude = -amplitude;
		});
	}

	rotateBubbles();
	rotateRibbons();
});