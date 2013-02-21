#!/bin/sh

#
# Execute
#
sed -e "s/%koncniCas%/$1/g" -e "s/%pga%/$2/g" -e "s/%acc_filename%/$3/g" -e "s/%Per%/$4/g" -e "s/%xDamp%/$5/g" -e "s/%out_filename%/$6/g" ida_template.tcl > ida.tcl
./OpenSees_1_6_0_IKPIR ida.tcl
rm ida.tcl

