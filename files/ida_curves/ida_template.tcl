# ****************************************************
# IDA.tcl
# Units: kN, m
#
# This is TCL file for parametric analysis for 
# calculation of IDA curve!
#
# author: Iztok Perus (iperus@ikpir.fgg.uni-lj.si)
# date: 30/01/2008
#
# ****************************************************

set meffX           100.0000;
set koncniCas       %koncniCas%;
set korAcc          0.0050;
set integCas        0.0050;
set PGA             %pga%;      # PGA: acceleration "time history" in units of G!
set AcFile          "%acc_filename%.acc";
set gAccFile        "%acc_filename%.AEi";
set OutFileName     "%out_filename%.out";

set dPGAc       0.0200;
set dPGA        0.0500;

set NumP     20;
set Rg      0.1;
set g      9.81;
set kpy 0.00001;
set beta    0.5;

set blankline " ";
set collapse 0;
set NumAnal [expr $koncniCas/$integCas];

file delete $OutFileName;
set OutFileNameID [open $OutFileName "w"];

set k 0;

# reads ground acceleration time history
set indic 0;
set fdata [open $gAccFile r]
while {[gets $fdata line] >= 0} {
  set indic [expr $indic+1];
  lappend gAcc $line
}
close $fdata								

set Per   %Per%;
set xDamp %xDamp%;

#foreach Per {0.1} {
#  foreach xDamp {0.01} {
    foreach Rv {0.1 0.3 0.5 0.7 0.9} {
      foreach Rh {0.1 0.3 0.5 0.7 0.9} {
        foreach duct {2 3 4 5 6 7 8} {
          foreach alfa {-0.05 -0.25 -0.5} {
					  if {$Rv > $Rh || $Rv == 0.9} {
						  # bilinear hysteresis
							if {$Rv == $Rh} {
							  set Rh [expr $Rh-0.001]
								set Rv [expr $Rv+0.001]
							}
							
							# TakedaD hysteresis - calculation of input data
				      set k0 [expr ((2*3.141592654/$Per)*(2*3.141592654/$Per))*$meffX];
				      set fy [expr $meffX*$g*$Rg];
  				    set fcr [expr $fy*$Rv];
							set ucr [expr $fcr/$k0];
				      set yield [expr $ucr/$Rh];
				      set aky [expr $fy/($yield*$k0)];
				      set d_NSH $duct;
							set uc [expr $duct*$yield+($fy/($k0*-1*$alfa))];
							set k [expr $k+1];
              set IM 0.5;
							
							if {$Per >= 0.5} { set dPGA 0.1000; }
							if {$Per >= 1.0} { set dPGA 0.2000; }
							if {$Per >= 1.5} { set dPGA 0.4000; }
							
              #find collapse point!
				      while {$collapse == 0} {
							  set IMi_1 $IM;
							  set IM [expr 2*$IM];
								set IMi $IM;
								set agfact [expr $IM*$dPGA/$PGA];
							  source SDOF_Spectra.tcl;

						    # reads max. displacement
  						  if [catch {open SDOF_Sd.out r} outFileID] { 
                  puts stderr "Cannot open SDOF_Sd.out for reading"; 
                } else {
                  foreach line [split [read $outFileID] \n] { ; # Look at each line
                    if {[llength $line] == 0} {; # Blank line --> do nothing
                      continue;
                    } else {
                      set Sd $line; # execute operation on reading data
                    }
                  } 
                  close $outFileID; ; # Close the "input file"
                } 
								
								if {$Sd >= $uc} { set collapse 1; } 
							}								
              
              # find capacity point with bisection!
							set tolerance [expr $IMi - $IMi_1];
							set IMcmin $IMi;
							set IMnmax $IMi_1;
				      while {$tolerance > $dPGAc} {								
							  set IMi [expr $IMcmin - 0.5*($IMcmin-$IMnmax)];
								set agfact [expr $IMi*$dPGA/$PGA];	
							  source SDOF_Spectra.tcl;
						    # reads max. displacement
  						  if [catch {open SDOF_Sd.out r} outFileID] {
                  puts stderr "Cannot open SDOF_Sd.out for reading"; # output "error statement"
                } else {
                  foreach line [split [read $outFileID] \n] { ; # Look at each line
                    if {[llength $line] == 0} {; # Blank line --> do nothing
                      continue;
                    } else {
                      set Sd $line; # execute operation on reading data
                    }
                  } 
                  close $outFileID; ; # Close the "input file"
                } 

# reads the number of analysis steps
#								set NumAnalT 0;
#                                set fdata [open "SDOF_Dis.out" r]
#                                while {[gets $fdata line] >= 0} {
#									set NumAnalT [expr $NumAnalT+1];
#                                }
#                                close $fdata									
#								
#                                set IMnmax $IMi;								
#								if {$NumAnalT >= $NumAnal && $Sd >= $uc || $NumAnalT < $NumAnal && $Sd >= $uc } {
#								    set IMcmin $IMi;
#                                    }
#								if {$NumAnalT < $NumAnal && $Sd < $uc } {
#                                    set IMcmin $IMi;
#                                    }
#								set tolerance [expr $IMcmin - $IMnmax];

								if {$Sd >= $uc} {
								  set IMcmin $IMi;
                } else {
                  set IMnmax $IMi;
                }
								set tolerance [expr $IMcmin - $IMnmax];
    					}		
															
  				    # write model data
              # set fileID [open $OutFileName "a+"];
              puts $OutFileNameID "$k $Per $xDamp $Rv $Rh $duct $alfa $yield $uc"
              # close $fileID
						    
							set Num 0;
				      while {$Num < $NumP} {
							  set Num [expr $Num + 1]; 
								set agfact [expr $IMi/$NumP*$Num * $dPGA/$PGA];	
							  source SDOF_Spectra.tcl;
								if {$Num > 1} {
								   unset Displ;
								   unset Force;
								   unset Vel;
								   # unset gAcc;
								}
								
                # CALCULATION OF HYSTERETIC ENERGY								
						    # reads displacement time history
                set fdata [open "SDOF_Dis.out" r]
                while {[gets $fdata line] >= 0} {
                  lappend Displ $line
                }
                close $fdata

						    # reads base shear time history
								set indic 0;
                set fdata [open "SDOF_For.out" r]
                while {[gets $fdata line] >= 0} {
								  set indic [expr $indic+1];
                  lappend Force $line
                }
                close $fdata								

						    # calculate Hysteretic energy
								set Eh 0.0;
								set indic [expr $indic-1];
		            for {set i 0} {$i < $indic} {incr i} {
								  set Q1 [lindex $Force $i];
									set d1 [lindex $Displ $i];
									set ii [expr $i+1];
								  set Q2 [lindex $Force $ii];
									set d2 [lindex $Displ $ii];									
									set deltaEh [expr (($Q1+$Q2)/2 * ($d2-$d1))];
                  set Eh [expr $Eh + $deltaEh];
                }
                set Eh [expr $Eh/$meffX];
								
						    # reads max. acceleration
						    if [catch {open SDOF_Sa.out r} outFileID] {
                  puts stderr "Cannot open SDOF_Sa.out for reading"; # output "error statement"
                } else {
                  foreach line [split [read $outFileID] \n] { ; # Look at each line
                    if {[llength $line] == 0} {; # Blank line --> do nothing
                      continue;
                    } else {
                      set Sa $line; # execute operation on reading data
                    }
                  } 
                  close $outFileID; # Close the "input file"
                }   

						    # reads max. displacement
  						  if [catch {open SDOF_Sd.out r} outFileID] {
                  puts stderr "Cannot open SDOF_Sd.out for reading"; # output "error statement"
                } else {
                  foreach line [split [read $outFileID] \n] { ; # Look at each line
                    if {[llength $line] == 0} {; # Blank line --> do nothing
                      continue;
                    } else {
                      set Sd $line; # execute operation on reading data
                    }
                  } 
                  close $outFileID; # Close the "input file"
                }  
								
						    set factAgg [expr $agfact*$g*$PGA];  # transformation from g in m/s2
						    set ductTH [expr $Sd/$yield];
								
                # CALCULATION OF INPUT ENERGY
                # reads velocity time history
                set fdata [open "SDOF_Vel.out" r]
                while {[gets $fdata line] >= 0} {
                  lappend Vel $line
                }
                close $fdata

						    # calculate Input energy
								set Eimax 0.0;
								set Ei 0.0;
								set NumAnalT $indic;
								set indic [expr $indic-1];
		            for {set i 0} {$i < $indic} {incr i} {
								  set v1 [lindex $Vel $i];
									set a1 [lindex $gAcc $i];
									set ii [expr $i+1];
								  set v2 [lindex $Vel $ii];
									set a2 [lindex $gAcc $ii];
 								  set deltaEi [expr (($v1+$v2)/2 * ($a1*$agfact*$g + $a2*$agfact*$g)/2 * $integCas)];
                  set Ei [expr $Ei - $deltaEi];
#									if {$Ei >= $Eimax} {
#								       set Eimax $Ei;
#                                    }
                }		
								set Ehi [expr $Eh/$Ei];
								
								# COLLAPSE CHECK: non-collapse = 0; collapse = 1; non-convergency = 2
								set collapse 0;
								if {$NumAnalT >= $NumAnal && $Sd >= $uc || $NumAnalT < $NumAnal && $Sd >= $uc } {
								  set collapse 1;
								}									
								if {$NumAnalT < $NumAnal && $Sd < $uc } {
								  set collapse 2;
								}		
						    
						    # write IDA data (PGA, ductility, Sd, Sa, Eh, Ei, Ehi)
                #set fileID [open $OutFileName "a+"];
                puts $OutFileNameID "$collapse $factAgg $ductTH $Sd $Sa $Eh $Ei $Ehi"
                #close $fileID
				      }; 
						  
						  #set fileID [open $OutFileName "a+"];
              puts $OutFileNameID $blankline
              #close $fileID	
						} else {
						  continue
 						} 
			    } 
		    } 
      } 	
    } 
#  } 
#}

close $OutFileNameID
