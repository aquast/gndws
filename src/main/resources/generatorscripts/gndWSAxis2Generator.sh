#!/bin/bash
echo "wsdl Generator for gndWS, using Axis2 Version 1.4.1"

export CODEGEN_CACHE=~/axisCodeGenerator/gndWS
rm -R $CODEGEN_CACHE/resources/
rm -R $CODEGEN_CACHE/src/
#rm -R $CODEGEN_CACHE/*.*

export CODE_BASE=~/git//gndWS/gndws

cp $CODE_BASE/src/main/resources/META-INF/gndRequester.wsdl $CODEGEN_CACHE/gndRequester.wsdl 

export AXIS2_BIN=/opt/axis2-1.4.1/bin
export AXIS2_HOME=/opt/axis2-1.4.1/

$AXIS2_BIN/wsdl2java.sh -u -uri $CODEGEN_CACHE/gndRequester.wsdl  -o $CODEGEN_CACHE/
$AXIS2_BIN/wsdl2java.sh -ss -ssi -sd -uri $CODEGEN_CACHE/gndRequester.wsdl -o $CODEGEN_CACHE/

cp -f $CODEGEN_CACHE/src/de/qterra/gnd/services/*.* $CODE_BASE/src/main/java/de/qterra/gnd/services
cp -f $CODEGEN_CACHE/src/de/qterra/gnd/webservice/*.* $CODE_BASE/src/main/java/src/de/qterra/gnd/webservice