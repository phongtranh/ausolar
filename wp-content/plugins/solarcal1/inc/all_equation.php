<?php

$feedinPercentage = @$_POST['feedinPercentage'];

if(@$_POST['usage'] == 'allday')
{
		if( $feedinPercentage >= 59){
				$feedPercentage = 87;
			}else{
				$feedPercentage = $feedinPercentage * 1.5;
			}
}
elseif(@$_POST['usage'] == 'midday')
{
		$feedPercentage = $feedinPercentage * 1;
}
elseif(@$_POST['usage'] == 'evenings')
{	
		$feedPercentage = $feedinPercentage * .5;
}


if(@$_POST['size'] == ''){
	
		$sloarpro = 3.0;
	
	}else{
		
		$sloarpro = @$_POST['size'];
		
	}
	
if(@$_POST['billInflation'] == ''){
	
		$billinflation = 5;
	
	}else{
		
		$billinflation = @$_POST['billInflation'];
		
	}
	
if(@$_POST['systemDegradation'] == ''){
	
		$systemdegradation = 0.5;
	
	}else{
		
		$systemdegradation = @$_POST['systemDegradation'];
		
	}
	
if(@$_POST['lastBillAmount'] == ''){
	
		$lastbill = 250;
	
	}else{
		
		$lastbill = @$_POST['lastBillAmount'];
		
	}

if(@$_POST['retailPrice'] == ''){
	
		$retailprice = 26;
	
	}else{
		
		$retailprice = @$_POST['retailPrice'];
		
	}
	
if(@$_POST['fitPrice'] == ''){
	
		$fitprice = 7.7;
	
	}else{
		
		$fitprice = @$_POST['fitPrice'];
		
	}

if(@$_POST['systemPrice'] == ''){
	
		$systemprice = 4000;
	
	}else{
		
		$systemprice = @$_POST['systemPrice'];
		
	}

if(@$_POST['billingPeriod'] == 'quarterly' ){
	
		$valuefordevide = 4;
	
}elseif(@$_POST['billingPeriod'] == 'bimonthly' ){
	
		$valuefordevide = 6;
	
}elseif(@$_POST['billingPeriod'] == 'monthly' ){
	
		$valuefordevide = 12;
	
}else{
		$valuefordevide = 6;
	}

@$totalpro = $sloarpro * $pow_produ * 365;

@$pardaypro = $totalpro / 365;

@$fedback =  $feedPercentage * $totalpro / 100;

@$elbiwithoutsolar = $lastbill * $valuefordevide;

@$savingonpb = (( $totalpro - $fedback ) * ( $retailprice / 100 ) ) + ( $fedback * ( $fitprice /100 ) );

@$withsolarbill = $elbiwithoutsolar - $savingonpb;

// For Effective price/ kWh after installing solar

@$priceinsent = $retailprice / 100 ;

@$averebwos = $elbiwithoutsolar / $priceinsent ;

@$effpricekilowatt =  ($withsolarbill / $averebwos) * 100 ;

//end

@$payback = $systemprice / $savingonpb;

@$roi = ($savingonpb / $systemprice) * 100;

// For years BILL

$year1 = date("Y"); $year2 = date("Y") + 1; $year3 = date("Y") + 2; $year4 = date("Y") + 3; $year5 = date("Y") + 4; $year6 = date("Y") + 5; $year7 = date("Y") + 6; $year8 = date("Y") + 7; $year9 = date("Y") + 8; $year10 = date("Y") + 9; $year11 = date("Y") + 10; $year12 = date("Y") + 11; $year13 = date("Y") + 12; $year14 = date("Y") + 13; $year15 = date("Y") + 14; $year16 = date("Y") + 15; $year17 = date("Y") + 16; $year18 = date("Y") + 17; $year19 = date("Y") + 18; $year20 = date("Y") + 19; 

$billsyear1 = $elbiwithoutsolar; 
$billsyear2 = $billsyear1 + ( $billsyear1*$billinflation/100 ); 
$billsyear3 = $billsyear2 + ( $billsyear2*$billinflation/100 ); 
$billsyear4 = $billsyear3 + ( $billsyear3*$billinflation/100 ); 
$billsyear5 = $billsyear4 + ( $billsyear4*$billinflation/100 ); 
$billsyear6 = $billsyear5 + ( $billsyear5*$billinflation/100 ); 
$billsyear7 = $billsyear6 + ( $billsyear6*$billinflation/100 ); 
$billsyear8 = $billsyear7 + ( $billsyear7*$billinflation/100 ); 
$billsyear9 = $billsyear8 + ( $billsyear8*$billinflation/100 ); 
$billsyear10 = $billsyear9 + ( $billsyear9*$billinflation/100 ); 
$billsyear11 = $billsyear10 + ( $billsyear10*$billinflation/100 ); 
$billsyear12 = $billsyear11 + ( $billsyear11*$billinflation/100 ); 
$billsyear13 = $billsyear12 + ( $billsyear12*$billinflation/100 ); 
$billsyear14 = $billsyear13 + ( $billsyear13*$billinflation/100 ); 
$billsyear15 = $billsyear14 + ( $billsyear14*$billinflation/100 ); 
$billsyear16 = $billsyear15 + ( $billsyear15*$billinflation/100 ); 
$billsyear17 = $billsyear16 + ( $billsyear16*$billinflation/100 ); 
$billsyear18 = $billsyear17 + ( $billsyear17*$billinflation/100 ); 
$billsyear19 = $billsyear18 + ( $billsyear18*$billinflation/100 ); 
$billsyear20 = $billsyear19 + ( $billsyear19*$billinflation/100 ); 

$billswiyear1 = $withsolarbill; 
$billswiyear2 = $billswiyear1 + ( $billswiyear1*$billinflation/100 ) + ( $billswiyear1*$systemdegradation/100 ); $billswiyear3 = $billswiyear2 + ( $billswiyear2*$billinflation/100 ) + ( $billswiyear2*$systemdegradation/100 ); $billswiyear4 = $billswiyear3 + ( $billswiyear3*$billinflation/100 ) + ( $billswiyear3*$systemdegradation/100 ); $billswiyear5 = $billswiyear4 + ( $billswiyear4*$billinflation/100 ) + ( $billswiyear4*$systemdegradation/100 ); $billswiyear6 = $billswiyear5 + ( $billswiyear5*$billinflation/100 ) + ( $billswiyear5*$systemdegradation/100 ); $billswiyear7 = $billswiyear6 + ( $billswiyear6*$billinflation/100 ) + ( $billswiyear6*$systemdegradation/100 ); $billswiyear8 = $billswiyear7 + ( $billswiyear7*$billinflation/100 ) + ( $billswiyear7*$systemdegradation/100 ); $billswiyear9 = $billswiyear8 + ( $billswiyear8*$billinflation/100 ) + ( $billswiyear8*$systemdegradation/100 ); $billswiyear10 = $billswiyear9 + ( $billswiyear9*$billinflation/100 ) + ( $billswiyear9*$systemdegradation/100 ); $billswiyear11 = $billswiyear10 + ( $billswiyear10*$billinflation/100 ) + ( $billswiyear10*$systemdegradation/100 ); $billswiyear12 = $billswiyear11 + ( $billswiyear11*$billinflation/100 ) + ( $billswiyear11*$systemdegradation/100 ); $billswiyear13 = $billswiyear12 + ( $billswiyear12*$billinflation/100 ) + ( $billswiyear12*$systemdegradation/100 ); $billswiyear14 = $billswiyear13 + ( $billswiyear13*$billinflation/100 ) + ( $billswiyear13*$systemdegradation/100 ); $billswiyear15 = $billswiyear14 + ( $billswiyear14*$billinflation/100 ) + ( $billswiyear14*$systemdegradation/100 ); $billswiyear16 = $billswiyear15 + ( $billswiyear15*$billinflation/100 ) + ( $billswiyear15*$systemdegradation/100 ); $billswiyear17 = $billswiyear16 + ( $billswiyear16*$billinflation/100 ) + ( $billswiyear16*$systemdegradation/100 ); $billswiyear18 = $billswiyear17 + ( $billswiyear17*$billinflation/100 ) + ( $billswiyear17*$systemdegradation/100 ); $billswiyear19 = $billswiyear18 + ( $billswiyear18*$billinflation/100 ) + ( $billswiyear18*$systemdegradation/100 ); $billswiyear20 = $billswiyear19 + ( $billswiyear19*$billinflation/100 ) + ( $billswiyear19*$systemdegradation/100 ); 

$saveyear1 = $savingonpb; 
$saveyear2 = $saveyear1 + ( $saveyear1*$billinflation/100 ) - ( $saveyear1*$systemdegradation/100 ); 
$saveyear3 = $saveyear2 + ( $saveyear2*$billinflation/100 ) - ( $saveyear2*$systemdegradation/100 ); 
$saveyear4 = $saveyear3 + ( $saveyear3*$billinflation/100 ) - ( $saveyear3*$systemdegradation/100 ); 
$saveyear5 = $saveyear4 + ( $saveyear4*$billinflation/100 ) - ( $saveyear4*$systemdegradation/100 ); 
$saveyear6 = $saveyear5 + ( $saveyear5*$billinflation/100 ) - ( $saveyear5*$systemdegradation/100 ); 
$saveyear7 = $saveyear6 + ( $saveyear6*$billinflation/100 ) - ( $saveyear6*$systemdegradation/100 ); 
$saveyear8 = $saveyear7 + ( $saveyear7*$billinflation/100 ) - ( $saveyear7*$systemdegradation/100 ); 
$saveyear9 = $saveyear8 + ( $saveyear8*$billinflation/100 ) - ( $saveyear8*$systemdegradation/100 ); 
$saveyear10 = $saveyear9 + ( $saveyear9*$billinflation/100 ) - ( $saveyear9*$systemdegradation/100 ); 
$saveyear11 = $saveyear10 + ( $saveyear10*$billinflation/100 ) - ( $saveyear10*$systemdegradation/100 ); 
$saveyear12 = $saveyear11 + ( $saveyear11*$billinflation/100 ) - ( $saveyear11*$systemdegradation/100 ); 
$saveyear13 = $saveyear12 + ( $saveyear12*$billinflation/100 ) - ( $saveyear12*$systemdegradation/100 ); 
$saveyear14 = $saveyear13 + ( $saveyear13*$billinflation/100 ) - ( $saveyear13*$systemdegradation/100 ); 
$saveyear15 = $saveyear14 + ( $saveyear14*$billinflation/100 ) - ( $saveyear14*$systemdegradation/100 ); 
$saveyear16 = $saveyear15 + ( $saveyear15*$billinflation/100 ) - ( $saveyear15*$systemdegradation/100 ); 
$saveyear17 = $saveyear16 + ( $saveyear16*$billinflation/100 ) - ( $saveyear16*$systemdegradation/100 ); 
$saveyear18 = $saveyear17 + ( $saveyear17*$billinflation/100 ) - ( $saveyear17*$systemdegradation/100 ); 
$saveyear19 = $saveyear18 + ( $saveyear18*$billinflation/100 ) - ( $saveyear18*$systemdegradation/100 ); 
$saveyear20 = $saveyear19 + ( $saveyear19*$billinflation/100 ) - ( $saveyear19*$systemdegradation/100 ); 

if(@$_POST['yrs'] == '5'){
	$billsyearforyears = $billsyear1+$billsyear2+$billsyear3+$billsyear4+$billsyear5;
	$billswiyearforyears = $billswiyear1+$billswiyear2+$billswiyear3+$billswiyear4+$billswiyear5;
	$saveyearforyears = $saveyear1+$saveyear2+$saveyear3+$saveyear4+$saveyear5;
}elseif(@$_POST['yrs'] == '10'){
	$billsyearforyears = $billsyear1+$billsyear2+$billsyear3+$billsyear4+$billsyear5+$billsyear6+$billsyear7+$billsyear8+$billsyear9+$billsyear10;
	$billswiyearforyears = $billswiyear1+$billswiyear2+$billswiyear3+$billswiyear4+$billswiyear5+$billswiyear6+$billswiyear7+$billswiyear8+$billswiyear9+$billswiyear10;
	$saveyearforyears = $saveyear1+$saveyear2+$saveyear3+$saveyear4+$saveyear5+$saveyear6+$saveyear7+$saveyear8+$saveyear9+$saveyear10;
}elseif(@$_POST['yrs'] == '20'){
	$billsyearforyears =  $billsyear1+$billsyear2+$billsyear3+$billsyear4+$billsyear5+$billsyear6+$billsyear7+$billsyear8+$billsyear9+$billsyear10+$billsyear11+$billsyear12+$billsyear13+$billsyear14+$billsyear15+$billsyear16+$billsyear17+$billsyear18+$billsyear19+$billsyear20;
	$billswiyearforyears = $billswiyear1+$billswiyear2+$billswiyear3+$billswiyear4+$billswiyear5+$billswiyear6+$billswiyear7+$billswiyear8+$billswiyear9+$billswiyear10+$billswiyear11+$billswiyear12+$billswiyear13+$billswiyear14+$billswiyear15+$billswiyear16+$billswiyear17+$billswiyear18+$billswiyear19+$billswiyear20;
	$saveyearforyears = $saveyear1+$saveyear2+$saveyear3+$saveyear4+$saveyear5+$saveyear6+$saveyear7+$saveyear8+$saveyear9+$saveyear10+$saveyear11+$saveyear12+$saveyear13+$saveyear14+$saveyear15+$saveyear16+$saveyear17+$saveyear18+$saveyear19+$saveyear20;
}else{
	$billsyearforyears = $billsyear1;
	$billswiyearforyears = $billswiyear1;
	$saveyearforyears = $saveyear1;
}

$graphmax = $billsyear20 + ($billsyear20*20/100);

if( $billswiyear1 < '500' ){
	$graphmin = 0;
}elseif( $billswiyear1 < '1000' ){
	$graphmin = 100;
}elseif( $billswiyear1 < '2000' ){
	$graphmin = 200;
}elseif( $billswiyear1 < '2500' ){
	$graphmin = 300;
}elseif( $billswiyear1 < '3500' ){
	$graphmin = 400;
}elseif( $billswiyear1 < '6000' ){
	$graphmin = 500;
}else{
	$graphmin = 0;
}
