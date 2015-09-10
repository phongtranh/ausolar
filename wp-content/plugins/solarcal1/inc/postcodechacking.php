<?php 
if($_POST['postcode'] == '2000'){
$jsonpost = (array(
   'type' => 'success',
   'retailers' => 
  array (
    0 => (array(
       'name' => 'ActewAGL(ACT)',
       'pay' => 18.304,
       'fit' => 7.5,
    )),
    1 => (array(
       'name' => 'AGL Energy(NSW)',
       'pay' => 29.66,
       'fit' => 8,
    )),
    2 => (array(
       'name' => 'AGL Energy(SA)',
       'pay' => 30.48,
       'fit' => 8,
    )),
    3 => (array(
       'name' => 'AGL Energy(VIC)',
       'pay' => 28.4,
       'fit' => 8,
    )),
    4 => (array(
       'name' => 'AGL Energy(QLD)',
       'pay' => 25.37,
       'fit' => 8,
    )),
    5 => (array(
       'name' => 'Energy Australia(QLD)',
       'pay' => 25.37,
       'fit' => 6,
    )),
    6 => (array(
       'name' => 'Energy Australia(NSW)',
       'pay' => 34,
       'fit' => 5.1,
    )),
    7 => (array(
       'name' => 'Energy Australia(VIC)',
       'pay' => 34,
       'fit' => 8,
    )),
    8 => (array(
       'name' => 'Energy Australia(SA)',
       'pay' => 35,
       'fit' => 7.6,
    )),
    9 => (array(
       'name' => 'Momentum Energy(SA)',
       'pay' => 35,
       'fit' => 7.6,
    )),
    10 => (array(
       'name' => 'Alinta Energy(VIC)',
       'pay' => 34,
       'fit' => 8,
    )),
	11 => (array(
       'name' => 'Alinta Energy(SA)',
       'pay' => 41,
       'fit' => 7.6,
    )),
	12 => (array(
       'name' => 'Diamond Energy(SA)',
       'pay' => 8,
       'fit' => 8,
    )),
	13 => (array(
       'name' => 'Simply Energy(VIC)',
       'pay' => 35,
       'fit' => 8,
    )),
	14 => (array(
       'name' => 'Simply Energy(SA)',
       'pay' => 35.96,
       'fit' => 7.6,
    )),
	15 => (array(
       'name' => 'Synergy(WA)',
       'pay' => 8.8529,
       'fit' => 8.8529,
    )),
	16 => (array(
       'name' => 'Horizon Power(WA)',
       'pay' => 50,
       'fit' => 50,
    )),
	17 => (array(
       'name' => 'Other',
       'pay' => 1,
       'fit' => 0,
    )),
  ),
   'lat' => -33.855598450000002,
   'lng' => 151.20820617999999,
));
}elseif(isset($_POST['postcode'])){
$jsonpost = (array(
   'type' => 'success',
   'retailers' => 
  array (
    0 => (array(
       'name' => 'ActewAGL(ACT)',
       'pay' => 18.304,
       'fit' => 7.5,
    )),
    1 => (array(
       'name' => 'AGL Energy(NSW)',
       'pay' => 29.66,
       'fit' => 8,
    )),
    2 => (array(
       'name' => 'AGL Energy(SA)',
       'pay' => 30.48,
       'fit' => 8,
    )),
    3 => (array(
       'name' => 'AGL Energy(VIC)',
       'pay' => 28.4,
       'fit' => 8,
    )),
    4 => (array(
       'name' => 'AGL Energy(QLD)',
       'pay' => 25.37,
       'fit' => 8,
    )),
    5 => (array(
       'name' => 'Energy Australia(QLD)',
       'pay' => 25.37,
       'fit' => 6,
    )),
    6 => (array(
       'name' => 'Energy Australia(NSW)',
       'pay' => 34,
       'fit' => 5.1,
    )),
    7 => (array(
       'name' => 'Energy Australia(VIC)',
       'pay' => 34,
       'fit' => 8,
    )),
    8 => (array(
       'name' => 'Energy Australia(SA)',
       'pay' => 35,
       'fit' => 7.6,
    )),
    9 => (array(
       'name' => 'Momentum Energy(SA)',
       'pay' => 35,
       'fit' => 7.6,
    )),
    10 => (array(
       'name' => 'Alinta Energy(VIC)',
       'pay' => 34,
       'fit' => 8,
    )),
	11 => (array(
       'name' => 'Alinta Energy(SA)',
       'pay' => 41,
       'fit' => 7.6,
    )),
	12 => (array(
       'name' => 'Diamond Energy(SA)',
       'pay' => 8,
       'fit' => 8,
    )),
	13 => (array(
       'name' => 'Simply Energy(VIC)',
       'pay' => 35,
       'fit' => 8,
    )),
	14 => (array(
       'name' => 'Simply Energy(SA)',
       'pay' => 35.96,
       'fit' => 7.6,
    )),
	15 => (array(
       'name' => 'Synergy(WA)',
       'pay' => 8.8529,
       'fit' => 8.8529,
    )),
	16 => (array(
       'name' => 'Horizon Power(WA)',
       'pay' => 50,
       'fit' => 50,
    )),
	17 => (array(
       'name' => 'Other',
       'pay' => 1,
       'fit' => 0,
    )),
  ),
   'lat' => -27.467599870000001,
   'lng' => 153.02789307,
));
}else{
	$jsonpost = 'No match found!';
	}


