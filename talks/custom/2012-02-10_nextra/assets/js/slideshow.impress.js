function CanvasPosition()
{
	this.data_x = 0;
	this.data_y = 0;
	this.data_z = 0;
	this.data_rotate_x = 0;
	this.data_rotate_y = 0;
	this.data_rotate_z = 0;
	this.data_scale = 1;
}


function Slideshow() { }

Slideshow.position = new CanvasPosition();


Slideshow.init = function(slideShow)
{
	Slideshow.position = new CanvasPosition();
	
	var steps = $(".step");
	for (var i=0; i<steps.length; i++)
	{
		slideShow.initStep($(steps[i]), i);
	}
}

Slideshow.positionStep = function(step)
{
	step.attr("data-x", Slideshow.position.data_x);
	step.attr("data-y", Slideshow.position.data_y);
	step.attr("data-z", Slideshow.position.data_z);
	step.attr("data-rotate-x", Slideshow.position.data_rotate_x);
	step.attr("data-rotate-y", Slideshow.position.data_rotate_y);
	step.attr("data-rotate-z", Slideshow.position.data_rotate_z);
	step.attr("data-scale", Slideshow.position.data_scale);
}