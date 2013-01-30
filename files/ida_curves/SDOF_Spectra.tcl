# ****************************************************
# SDOF_Spectra.tcl
# Units: kN, m
#
# This is TCL file with SDOF model 
#
# Made from SDOF.tcl [Matjaz Dolsek 02.09.2005]
# Spring in X-direction
# priredil Iztok Perus za racun IDA krivulj za razlicne Takeda modele
#
# Model: TakedaDAsym hinge (element used: "zeroLength Element")
#
# author: Iztok Perus (iperus@ikpir.fgg.uni-lj.si)
# date: 31/08/2007
#
# ****************************************************

wipe;                       
# sistem z eno prostotno stopnjo
model BasicBuilder -ndm 3 -ndf 6

# VOZLISCE
#set meffX 100.0;
#source Mass.tcl;
node 2  0.0 0.0  0.0 -mass $meffX 1.0E-16 1.0E-16 1.0E-16 1.0E-16 1.0E-16;
node 1  0.0 0.0  0.0 
fix  2  0  1  1  1  1  1;
fix  1  1  1  1  1  1  1;

# Definition of hysteretic material
#source TakedaD.tcl

set k0n [expr -1*$k0];
set fyn [expr -1*$fy];
set fcrn [expr -1*$fcr];
set akyn [expr -1*$aky];
set kpyn [expr -1*$kpy];
set alfan [expr -1*$alfa];
set d_NSHn [expr -1*$d_NSH];	

uniaxialMaterial TakedaD 1 $k0 $fcr $aky $fy $kpy $beta $d_NSH $alfa
#uniaxialMaterial TakedaDAsym 1 $k0 $k0n $fcr $fcrn $aky $akyn $fy $fyn $kpy $kpyn $beta $d_NSH $d_NSHn $alfa $alfan

# Transformation
#geomTransf Linear 1

# plastic hinges          tag ndI ndJ    mat   orientation
element zeroLength          1   1   2   -mat 1  -dir 1


#######################################
##   DEFINICIJA PARAMETROV ANALIZE   ##
#######################################
#source DampPer.tcl
set  Pi 3.141592654   ;
#source AnalData.tcl;
#set  koncniCas 20.0   ;
#set  korAcc     0.02  ;                 # casovni korak za katerega so podani akcelerogrami 
#set  integCas   0.01  ;                 # integracijski korak
#source Integ.tcl;
set  alfaM  [expr 4.0*$Pi*$xDamp/$Per]; # faktor dusenja proporcionalno masi
set  betaKcurr  0.0   ;                 # faktor dusenja proporcionalno togosti
#set  agfact     1.0  ;
#source agfact.tcl     ;
#source AccFile.tcl    ;
          
#######################################
## DINAMIKA 			     ##
#######################################

test NormDispIncr 1.0e-6 12 0
algorithm Newton 
system SparseGeneral -piv
numberer RCM
constraints Transformation

recorder EnvelopeNode -file SDOF_Sd.out -time -node 2 -dof 1 disp 
recorder EnvelopeNode -file SDOF_Sa.out -time -node 2 -dof 1 accel 

recorder Element -file SDOF_For.out -ele  1 force
recorder Node    -file SDOF_Dis.out -node 2 -dof 1 disp    
recorder Node    -file SDOF_Vel.out -node 2 -dof 1 vel
#recorder Node    -file SDOF_Acc.out -node 2 -dof 1 accel

#                  $gamma $beta <$alphaM $betaK   $betaKinit $betaKcomm>
integrator Newmark    0.5  0.25   $alfaM    0     $betaKcurr  0      
         
analysis Transient
set ok 0;
set currentTime 0.0;

setTime 0.0;

set factAg [expr $agfact*$g];  # pretvorba iz g v m/s2
set accX "Path -filePath $AcFile -dt $korAcc -factor $factAg";
   
# Define the ground motion excitation
#                         tag dir         accel series args
pattern UniformExcitation  2   1  -accel    $accX

set ok 0;
set currentTime 0.0;

set napaka 1e-3
set dt $integCas;
set napaka2 1e-3
set dt2 $integCas;

   while {$ok==0 && $currentTime<$koncniCas} {
	set ok [analyze 1 $dt]
	if {$ok!=0} {
	        puts " Newton failed .. Trying Newton Initial .." 
		test NormDispIncr $napaka2 100 0
		algorithm Newton -initial
		set ok [analyze 1 $dt2]
		test NormDispIncr $napaka 12 0
		algorithm Newton  
		if {$ok == 0} {puts " that worked .. "}	
	}
	if {$ok!=0} {
		        puts " Newton failed .. Trying NewtonLineSearch .." 
			test NormDispIncr $napaka2 100 0
			algorithm NewtonLineSearch
			set ok [analyze 1 $dt2]
			test NormDispIncr $napaka 12 0
			algorithm Newton 
			if {$ok == 0} {puts " that worked .. "}	
	}
	if {$ok!=0} {
		puts " Newton failed .. Trying ModifiedNewton with Initial Tangent .." 
		test NormDispIncr $napaka2 100 0
		algorithm ModifiedNewton -initial
		set ok [analyze 1 $dt2]
		test NormDispIncr $napaka 12 0
		algorithm Newton
		if {$ok == 0} {puts " that worked .. "}	
	}
	set currentTime [getTime]
}
