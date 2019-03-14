<!doctype html>
<html lang="en">
<head>

	<?php
	
		class VerticalTunnelSlideshow extends SlideshowBase implements ISlideshow
		{
			public function begin()
			{
				$this->printPosition();
			}
			
			public function end()
			{
				$this->nextSection();
			}
			
			public function next($newSection = false)
			{
				if ($newSection)
					$this->nextSection();
				else
					$this->nextSlide();
			}
			
			private function nextSection()
			{
				$this->data_rotate_y += 90;
			
				if ($this->data_rotate_y == 90)
				{
					$this->data_x = 500;
					$this->data_z	= -500;
				}
				
				else if ($this->data_rotate_y == 180)
				{
					$this->data_x = 0;
					$this->data_z	= -1000;
				}
				
				else if ($this->data_rotate_y == 270)
				{
					$this->data_x = -500;
					$this->data_z	= -500;
				}
				
				else
				{
					$this->data_x = 0;
					$this->data_z	= 0;
				}
				
				$this->printPosition();
			}
			
			private function nextSlide()
			{
				$this->data_y += 1000;
				
				$this->printPosition();
			}
		}
		
		abstract class SlideshowBase
		{
			protected $data_x = 0;
			protected $data_y = 0;
			protected $data_z = 0;
			protected $data_rotate_x = 0;
			protected $data_rotate_y = 0;
			protected $data_rotate_z = 0;
			
			protected function printPosition() {
				print "data-x='$this->data_x' data-y='$this->data_y' data-z='$this->data_z' data-rotate-x='$this->data_rotate_x' data-rotate-y='$this->data_rotate_y' data-rotate-z='$this->data_rotate_z'";
			}
		}
		
		interface ISlideshow
		{
			function begin();
			function end();
			function next($newSection = false);
		}
	
		$slide = new VerticalTunnelSlideshow();
		
	?>

    <meta charset="utf-8" />
    <title>NExtra Release Management | by Daniel Saidi</title>
    
    <meta name="description" content="This slideshow presents the NExtra Release Management, which involves git, GitHub, NuGet and Phantom.">
    <meta name="author" content="Daniel Saidi" />

    <link href="http://fonts.googleapis.com/css?family=Open+Sans:regular,semibold,italic,italicsemibold|PT+Sans:400,700,400italic,700italic|PT+Serif:400,700,400italic,700italic" rel="stylesheet" />
    <link href="assets/impress/css/style.css" rel="stylesheet" />
    
	<style rel="general">
		body { background: #4a696d url(assets/nextra/img/bg.png); }
		
		a { color:#333; }
		h1 { font-size: 3em; margin: 50px 0px 80px 0px; color: }
		ul li { margin-bottom: 20px; }
		
		.slide { text-align:center; }
		
		.footer { position:absolute; left:0; bottom:60px; width:100%; font-size:1em; font-style:italic;}
	</style>
	
	<style rel="title">
		#title img { margin-top:100px; width:80%; }
		#title q { margin-top:70px; }
	</style>
	
	<style rel="title">
		#nextra img { height:80px; }
	</style>
	
	<style>
		#end h1 {
			font-size: 4em;
			margin-top: 30px;
		}
	</style>
</head>
<body>

<div id="impress" class="impress-not-supported">

    <div class="fallback-message">
        <p>Your browser <b>doesn't support the features required</b> by impress.js, so you are presented with a simplified version of this presentation.</p>
        <p>For the best experience please use the latest <b>Chrome</b> or <b>Safari</b> browser. Firefox 10 and Internet Explorer 10 <i>should</i> also handle it.</p>
    </div>

	
    <div id="title" class="step slide" <?php print $slide->begin(); ?>>
		<img src="assets/nextra/img/logo.png" />
		<q>Release Management<br/>
		<em>git</em>, <em>GitHub</em>, <em>NuGet</em> och <em>Phantom</em></q>
		<div class="footer">* ...presenterat genom att <b>gravt</b> missbruka impress.js</div>
    </div>

	
    <div id="nextra" class="step slide" <?php $slide->next(1); ?>>
		<h1>Vad är <img src="assets/nextra/img/logo.png" />?</h1>
		<ul>
			<li>Open Source .NET-bibliotek</li>
			<li>Består av 6 projekt<br/>(NExtra, .Web, .WebForms, .Mvc, .Wpf, .WinForms)</li>
			<li>&nbsp;</li>
			<li>Versionshanterat med <em>git</em></li>
			<li>Projektet ligger på <em>GitHub</em></li>
			<li>Paket finns på <em>NuGet</em></li>
		</ul>
    </div>

    <div id="git" class="step slide" <?php $slide->next(); ?>>
		<h1>Vad är git?</h1>
		<ul>
			<li>Distribuerad versionshantering</li>
			<li>Används mot bl.a. <em>GitHub</em> och <em>AppHarbor</em></li>
			<li>Kan även användas mot t.ex. Dropbox</li>
			<li>&nbsp;</li>
			<li><b>Inte bundet till kommandoraden!</b><br/>
			VS har plugins och GitHub har en grafisk klient</li>
			<li>&nbsp;</li>
			<li><a rel="external" href="http://git-scm.com/">Läs mer om git</a></li>
		</ul>
    </div>

    <div id="github" class="step slide" <?php $slide->next(); ?>>
		<h1>Vad är GitHub?</h1>
		<ul>
			<li>Projekthosting för alla typer av projekt</li>
			<li>Du kan pusha din källkod med git och klona andras</li>
			<li>Helt gratis (man betalar för privata repositories)</li>
			<li>Nedladdningar, ärendehantering, tag-zippar etc.</li>
			<li>Presentationssidor för både medlemmar och projekt</li>
			<li>&nbsp;</li>
			<li><a href="http://github.com/danielsaidi/NExtra" rel="external">.NExtras projektsida</a></li>
			<li><a href="http://danielsaidi.github.com/NExtra" rel="external">.NExtras presentationssida (notera versionsnumret)</a></li>
		</ul>
    </div>

    <div id="github" class="step slide" <?php $slide->next(); ?>>
		<h1>Vad är NuGet?</h1>
		<ul>
			<li>Pakethantering för .NET</li>
			<li>Paket byggs utifrån en .nuspec-fil</li>
			<li>Paket byggs med script eller NuGet Package Explorer</li>
			<li>Paket kan hämtas via sajten, prompt eller inifrån VS</li>
			<li>NuGet kan även gå mot andra källor (t.ex. kataloger)</li>
			<li>&nbsp;</li>
			<li>&nbsp;</li>
			<li><a href="https://nuget.org/packages?q=nextra&sortOrder=package-download-count" rel="external">.NExtra på nuget.org</a></li>
		</ul>
    </div>

	
    <div id="problem" class="step slide" <?php $slide->next(1); ?>>
		<h1>Problem</h1>
		<ul>
			<li>Jag måste komma ihåg att köra tester innan release</li>
			<li>Jag måste komma ihåg att skapa en separat git-tagg</li>
			<li>Releaser (zip-filer) krävde manuell paketering</li>
			<li>NuGet krävde manuella paketeringar för varje projekt</li>
			<li>Att arbeta i NuGet Package Explorer är långsamt</li>
			<li>&nbsp;</li>
			<li><em>All manuell hantering är tråkig ökar risken för fel!</em></li>
		</ul>
    </div>
	
    <div id="solution" class="step slide" <?php $slide->next(); ?>>
		<h1>Lösning</h1>
		<ul>
			<li>Just nu är Team City overkill, men...</li>
			<li>Jag lade till Phantom och gjorde ett byggscript som:</li>
			<li>Bygger och testar</li>
			<li>Hämtar ut versionsnummer</li>
			<li>Paketerar releasen</li>
			<li>Zippar</li>
			<li>Deployar</li>
			<li>Publicerar till NuGet och GitHub</li>
		</ul>
    </div>
	
    <div id="problem" class="step slide" <?php $slide->next(1); ?>>
		<h1>Demonstration</h1>
		<q>"build publish" bygger, testar, zippar och publicerar .NExtra</q>
		<ul>
			<li>&nbsp;</li>
			<li><a href="https://github.com/danielsaidi/NExtra/tags" rel="external">Resultat på GitHub</a></li>
			<li><a href="https://nuget.org/packages?q=nextra&sortOrder=package-download-count" rel="external">Resultat på NuGet.org</a></li>
		</ul>
		<div class="footer">* Påminn mig om att inte glömma verisonsnumret på sajten</div>
    </div>
	

    <div id="end" class="step slide" <?php $slide->next(1); ?>>
		<h1>Tack! *</h1>
		<div class="footer">* ...och säg till om ni vill hjälpa till med .NExtra ;)</div>
    </div>

</div>

<div class="hint">
    <p>Use a spacebar or arrow keys to navigate</p>
</div>

<script type="text/javascript" src="assets/impress/js/impress.js"></script>
<script type="text/javascript" src="assets/js/jquery.js"></script>
<script type="text/javascript" src="assets/js/rel.js"></script>

</body>
</html>
