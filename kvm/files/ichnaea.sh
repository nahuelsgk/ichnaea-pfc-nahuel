#!/bin/bash
SCRIPTNAME=`basename $0`
NAME="${SCRIPTNAME%.*}"

SEASON=""
DATAFILE=""
OUTFILE=""
SECTION="1"
SUMMER="Estiu"
WINTER="Hivern"

pushd `dirname $0` > /dev/null
SCRIPTPATH=`pwd -P`
popd > /dev/null
ICHNAEADIR="$SCRIPTPATH/ichnaea"

function USAGE {
	echo "Ichnaea wrapper by Miguel Ibero <miguel@ibero.me>"
	echo "usage: $0 [--summer|--winter] [--section=N] data.csv"
	exit 0
}

set -- `getopt -n$0 -u --longoptions="summer winter section output" "h" "$@"` || USAGE
[ $# -eq 0 ] && USAGE

while [ $# -gt 0 ]
do
    case "$1" in
       --summer)   	SEASON=$SUMMER;shift;;
       --winter) 	SEASON=$WINTER;shift;;
	   --section)   SECTION="$1";shift;;
       --output)    OUTFILE="$1";shift;;
       -h)        	USAGE;;
       --)        	shift;break;;
       -*)        	USAGE;;
       *)         	DATAFILE="$1";break;;
    esac
    shift
done

if [ "$SEASON" == "" ] || [ "$DATAFILE" == "" ]
then
	USAGE
fi

if ! [[ "$SECTION" =~ ^[0-9]+$ ]]
then
	echo "section has to be an integer" > 2
	USAGE
fi

if [ ! -f "$DATAFILE" ]
then
	echo "could not read data file $DATAFILE" > 2
	USAGE
fi

function REXEC {
	ERRFILE=`tempfile`
	RFILE="$1"
	shift
    R --vanilla --no-readline --quiet --slave --args $@ < "$RFILE" 2> $ERRFILE
    ERROR=`cat $ERRFILE`
    rm $ERRFILE
} 

TMPDIR=`mktemp -d --suffix="-$NAME"`
echo "working in temp directory $TMPDIR" > 2
cp -r $ICHNAEADIR/* $TMPDIR
cp $DATAFILE $TMPDIR/data

STARTTIME=`date +%s`
echo "starting at `date`" > 2
pushd $TMPDIR/r > /dev/null
echo "building dataset..." > 2
RESULT=`REXEC section_dataset_building.R` > 2
echo "building models for season '$SEASON' and section '$SECTION'..." > 2
REXEC section_models_building.R $SECTION $SEASON

ENDTIME=`date +%s`
DURATION=$((ENDTIME - STARTTIME))

DURATIONSTR="$(( DURATION / 60 ))m $(( DURATION % 60 ))s"
echo "finished at `date`" > 2
echo "finished in $DURATIONSTR" > 2

ZIPFILE=$TMPDIR/build_models.zip
zip -j $ZIPFILE $TMPDIR/data_objects/section_models_*.Rdata

if [ "$OUTFILE" == "" ]
then
	cat $ZIPFILE
else
	cp $ZIPFILE $OUTFILE
fi

popd > /dev/null
rm -rf $TMPDIR