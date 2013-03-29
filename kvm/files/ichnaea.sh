#!/bin/bash
SCRIPTNAME=`basename $0`
NAME="${SCRIPTNAME%.*}"
pushd `dirname $0` > /dev/null
SCRIPTPATH=`pwd -P`
popd > /dev/null

SEASON=""
DATAFILE=""
OUTFILE=""
SECTION="1"
SUMMER="Estiu"
WINTER="Hivern"
ICHNAEADIR=""
FAKE=""

function USAGE {
	echo "Ichnaea wrapper by Miguel Ibero <miguel@ibero.me>"
	echo "usage: $0 --season [summer|winter] [--section=N] [--output=file.zip] [--fake=duration:interval] data.csv"
	exit 0
}

function PRINT_LOG {
	echo "$1">&2
}

OPTS=`getopt -o scfo -l "season:,section:,output:,fake:" -- "$@"`
if [ $? != 0 ]
then
    exit 1
fi

eval set -- "$OPTS"

while true
do
    case "$1" in
        -s|--season) SEASON="$2"; shift 2;;
        -c|--section) SECTION="$2"; shift 2;;
        -o|--putput) OUTFILE="$2"; shift 2;;
        -f|--fake) FAKE="$2"; shift 2;;
        --) shift; break;;
		-*) PRINT_LOG "invalid option $1"; USAGE; shift; break;;
		\?) PRINT_LOG "unknown option: -$OPTARG"; USAGE; shift; break;;
        : ) PRINT_LOG "missing option argument for -$OPTARG"; USAGE; shift; break;;
        * ) PRINT_LOG "unimplimented option: -$OPTARG"; USAGE; shift; break;;
    esac
done

shift $(($OPTIND - 1))

DATAFILE="$1"

function TIME_START {
	STARTTIME=`date +%s`
	PRINT_LOG "starting at `date`"
}

function TIME_END {
	ENDTIME=`date +%s`
	DURATION=$((ENDTIME - STARTTIME))

	DURATIONSTR="$(( DURATION / 60 ))m $(( DURATION % 60 ))s"
	PRINT_LOG "finished at `date`"
	PRINT_LOG "finished in $DURATIONSTR"
}

function REXEC {
	ERRFILE=`tempfile`
	RFILE="$1"
	shift
    R --vanilla --no-readline --quiet --slave --args $@ < "$RFILE" 2> $ERRFILE
    ERROR=`cat $ERRFILE`
    rm $ERRFILE
}

function CALC {
	echo "$1" | bc
}

TIME_START

if [ "$FAKE" == "" ]
then

	if [ "$DATAFILE" == "" ]
	then
		PRINT_LOG "no input file specified"
		USAGE
	fi

	case "$SEASON" in
		summer) SEASON=$SUMMER;;
		winter) SEASON=$WINTER;;
		*) SEASON="";;
	esac

	if [ "$SEASON" == "" ]
	then
		PRINT_LOG "no season specified"
		USAGE
	fi

	if ! [[ "$SECTION" =~ ^[0-9]+$ ]]
	then
		PRINT_LOG "section has to be an integer"
		USAGE
	fi

	if [ ! -f "$DATAFILE" ]
	then
		PRINT_LOG "could not read data file $DATAFILE"
		USAGE
	fi

	if [ "$ICHNAEADIR" == "" ]
	then
		ICHNAEADIR="$SCRIPTPATH/ichnaea"
	fi

	TMPDIR=`mktemp -d --suffix="-$NAME"`
	PRINT_LOG "working in temp directory $TMPDIR"
	pushd $TMPDIR/r > /dev/null
	PRINT_LOG "building dataset..."
	cp -r $ICHNAEADIR/* $TMPDIR
	cp $DATAFILE $TMPDIR/data

	RESULT=`REXEC section_dataset_building.R` > 2
	PRINT_LOG "building models for season '$SEASON' and section '$SECTION'..."
	REXEC section_models_building.R $SECTION $SEASON

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
else
	DURATION=$( echo $FAKE | sed -e "s/\(.*\):.*/\1/g")
	INTERVAL=$( echo $FAKE | sed -e "s/.*:\(.*\)/\1/g")
	
	if ! [[ "$DURATION" =~ ^[0-9]+$ || "$INTERVAL" =~ ^[0-9]+$ ]]
	then
		PRINT_LOG "invalid fake duration and interval"
		USAGE
	fi
	CURRENT="0"
	PRINT_LOG "starting fake run of $DURATION seconds in $INTERVAL intervals"
	while [[ $(CALC "$CURRENT<$DURATION") == "1" ]]
	do
		sleep $INTERVAL
		CURRENT=$(CALC "$CURRENT+$INTERVAL")
		PERCENT=$(CALC "100*$CURRENT/$DURATION")
		PRINT_LOG "$PERCENT%"
	done
fi

TIME_END