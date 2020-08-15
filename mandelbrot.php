<?
/********************************************************************************/
/* générateur de fractale, ensemble de Mandelbrot                               */
/*                                                                              */
/* copyright Daniel MIHALCEA (c) 2010-2020 http://mihalcea.fr/                  */
/*                                                                              */
/* @param   {Number} f: fichier pointant vers l'image source ou URL             */
/* @param   {Number} h: hauteur en pixels de l'image générée                    */
/* @param   {Number} w: largeur en pixels de l'image générée                    */
/* @param   {Number} cx: coordonnée x du centre de l'image                      */
/* @param   {Number} cy: coordonnée y du centre de l'image                      */
/* @param   {Number} s: facteur de zoom                                         */
/* @returns {Image}  ensemble de Mandelbrot                                     */
/*                                                                              */
/********************************************************************************/

header("Content-Type: image/png");
$t0 = hrtime(true); // début chrono

$height = (int) ($_GET['h'] ?? 200);
$width = (int) ($_GET['w'] ?? 320);
$cx = (float) ($_GET['cx'] ?? -0.5);
$cy = (float) ($_GET['cy'] ?? 0);
$scale = (float) ($_GET['s'] ?? 0.01);
$limit = 4;
$nmax = 128; // nombre maximum d'itérations

$h2 = $height/2;
$w2 = $width/2;

$im = ImageCreateTrueColor($width, $height);
for ($x=0; $x<$width; $x++) {
	$ax = $cx + ($x-$w2)*$scale;
	for ($y=0; $y<$height; $y++) {
		$ay = $cy + ($y-$h2)*$scale;
		$a1 = $ax;
		$b1 = $ay;
		$lp = 0;
		while($lp<$nmax) {
			$aa = $a1*$a1;
			$bb = $b1*$b1;
			if ($aa+$bb>$limit) {break;}
			$a2 = $aa-$bb+$ax;
			$b2 = 2*$a1*$b1+$ay;
			$a1 = $a2;
			$b1 = $b2;
			$lp++;
		}
		if ($lp === $nmax) { // couleur noir si la suite est convergeante (on est dans l'emsemble de Mandelbrot)
			$r = 0;
			$v = 0;
			$b = 0;
		} else { // couleur en fonction de la vitesse de divergeance
			$r = floor(128*log($lp, 10));
			$v = $lp*2;
			$b = $lp;	
		}
		$c = imagecolorallocate ($im, $r, $v, $b);
		imagesetpixel ($im, $x, $y, $c);
	}
}
$t1 = hrtime(true); // fin chrono
$blanc = imagecolorallocate($im, 255, 255, 255);
imagestring($im, 2, $width - 180, $height - 16, 'Daniel Mihalcea (c) 2010-2020', $blanc);
imagestring($im, 2, 0, 0, 'calcul : '.round(($t1 - $t0)/1000000000, 3).'s', $blanc);
imagepng($im);
imagedestroy($im);
?>
