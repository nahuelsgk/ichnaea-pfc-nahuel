#!/bin/bash
SCRIPTNAME=`basename $0`
NAME="${SCRIPTNAME%.*}"
ICHNAEADIR="./ichnaea"
SEASON=""
DATAFILE=""
SECTION="1"
SUMMER="Estiu"
WINTER="Hivern"

function USAGE {
	echo "Ichnaea wrapper by Miguel Ibero <miguel@ibero.me>"
	echo "usage: $0 [--summer|--winter] [--section=N] data.csv"
	exit 0
}

set -- `getopt -n$0 -u --longoptions="summer winter section" "h" "$@"` || USAGE
[ $# -eq 0 ] && USAGE

while [ $# -gt 0 ]
do
    case "$1" in
       --summer)   	SEASON=$SUMMER;shift;;
       --winter) 	SEASON=$WINTER;shift;;
	   --section)   SECTION="$1";shift;;
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
	echo "section has to be an integer"
	USAGE
fi

if [ ! -f "$DATAFILE" ]
then
	echo "could not read data file $DATAFILE"
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
echo "working in temp directory $TMPDIR"
cp -r $ICHNAEADIR/* $TMPDIR
cp $DATAFILE $TMPDIR/data

pushd $TMPDIR/r > /dev/null
echo "building dataset..."
RESULT=`REXEC section_dataset_building.R`
echo "building models for season '$SEASON' and section '$SECTION'..."
RESULT=`REXEC section_models_building.R $SECTION $SEASON`

popd > /dev/null
rm -rf $TMPDIR