#!/bin/bash
SCRIPTNAME=`basename $0`
NAME="${SCRIPTNAME%.*}"
pushd `dirname $0` > /dev/null
SCRIPTPATH=`pwd -P`
popd > /dev/null

AGING=""
DATAFILE=""
OUTFILE=""
ICHNAEADIR=""
FAKE=""
INSTALL=""
SECTION="1"
SEASON="Hivern"
DEBUG=""
RBIN=`which R`

function USAGE {
	echo "Ichnaea wrapper by Miguel Ibero <miguel@ibero.me>"
	echo "usage: $0 --install --debug --aging=path/to/aging [--output=file.zip] [--fake=duration:interval] data.csv"
	exit 0
}

function PRINT_LOG {
	echo "$1" 
}

OPTS=`getopt -o scfoid -l "aging:,output:,fake:,install,debug" -- "$@"`
if [ $? != 0 ]
then
    exit 1
fi

eval set -- "$OPTS"

while true
do
    case "$1" in
    	-i|--install) INSTALL="1"; shift 1;;
    	-d|--debug) DEBUG="1"; shift 1;;
        -a|--aging) AGING="$2"; shift 2;;
        -o|--output) OUTFILE="$2"; shift 2;;
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
	PRINT_LOG "started: `date`"
}

function TIME_CURRENT {
	CURRTIME=`date +%s`
	CURRDURATION=$((CURRTIME - STARTTIME))

	CURRDURATIONSTR="$(( CURRDURATION / 60 ))m $(( CURRDURATION % 60 ))s"
	PRINT_LOG "current: `date` ($CURRDURATIONSTR)"
}

function TIME_END {
	ENDTIME=`date +%s`
	ENDDURATION=$((ENDTIME - STARTTIME))

	ENDDURATIONSTR="$(( ENDDURATION / 60 ))m $(( ENDDURATION % 60 ))s"
	PRINT_LOG "finished: `date` ($ENDDURATIONSTR)"
}

function REXEC {
	ERRFILE=`tempfile`
	RFILE="$1"
	shift
	if [ "$DEBUG" == "1" ]
	then
		$RBIN --vanilla --no-readline --slave --args $@ < "$RFILE" | tee $ERRFILE 2>&1
	else
		$RBIN --vanilla --no-readline --quiet --slave --args $@ < "$RFILE" 2> $ERRFILE
	fi
    ERROR=`cat $ERRFILE`
    rm $ERRFILE
}

function CALC {
	echo "$1" | bc
}

TIME_START

if [ "$FAKE" == "" ]
then

	if [ "$ICHNAEADIR" == "" ]
	then
		ICHNAEADIR="$SCRIPTPATH/ichnaea"
	fi

	if [ "$RBIN" == "" ]
	then
		PRINT_LOG "binary R executable was not found on the path"
		exit
	fi

	if [ "$INSTALL" == "1" ]
	then
		PRINT_LOG "installing required R modules"
		pushd $ICHNAEADIR/src > /dev/null
		REXEC install.R
		popd > /dev/null
	else

		if [ "$DATAFILE" == "" ]
		then
			PRINT_LOG "no input file specified"
			USAGE
		fi

		if [ "$AGING" == "" ]
		then
			PRINT_LOG "no aging specified"
			USAGE
		fi
		if [ ! -d "$AGING" ]
		then
			PRINT_LOG "aging '$AGING' is not a directory"
			USAGE	
		fi

		if [ ! -f "$DATAFILE" ]
		then
			PRINT_LOG "could not read data file $DATAFILE"
			USAGE
		fi

		TMPDIR=`mktemp -d`
		mkdir -p $TMPDIR
		if [ "$DEBUG" == "1" ]
		then
			PRINT_LOG "working in temp directory $TMPDIR"
		fi
		cp -r $ICHNAEADIR/* $TMPDIR
		mkdir -p $TMPDIR/data
		mkdir -p $TMPDIR/data_objects
		cp -r $AGING $TMPDIR/data/aging
		cp $DATAFILE $TMPDIR/data/cyprus.csv
		pushd $TMPDIR/src > /dev/null	

		PRINT_LOG "building dataset..."
		RESULT=`REXEC section_dataset_building.R`
		PRINT_LOG "building models..."
		REXEC section_models_building.R $SEASON

		ZIPFILE=$TMPDIR/build_models.zip
		zip -j $ZIPFILE $TMPDIR/data_objects/section_models_*.Rdata

		if [ "$OUTFILE" == "" ]
		then
			cat $ZIPFILE
		else
			cp $ZIPFILE $OUTFILE
		fi

		popd > /dev/null

		if [ "$DEBUG" == "" ]
		then
			rm -rf $TMPDIR
		fi
	fi
else
	FAKE_DURATION=`echo $FAKE | sed -e "s/\(.*\):.*/\1/g"`
	FAKE_INTERVAL=`echo $FAKE | sed -e "s/.*:\(.*\)/\1/g"`
	
	if ! [[ "$FAKE_DURATION" =~ ^[0-9.]+$ || "$FAKE_INTERVAL" =~ ^[0-9.]+$ ]]
	then
		PRINT_LOG "invalid fake duration and interval"
		USAGE
	fi
	FAKE_CURRENT="0"
	PRINT_LOG "starting fake run of $FAKE_DURATION seconds in $FAKE_INTERVAL intervals"
	FAKE_ENDTIME=`date -d "$FAKE_DURATION seconds"`
	while [[ $(CALC "$FAKE_CURRENT<$FAKE_DURATION") == "1" ]]
	do
		sleep $FAKE_INTERVAL
		FAKE_CURRENT=`CALC "$FAKE_CURRENT + $FAKE_INTERVAL"`
		FAKE_PERCENT=`CALC "100 * $FAKE_CURRENT / $FAKE_DURATION"`
		PRINT_LOG "----"
		TIME_CURRENT
		PRINT_LOG "percent: $FAKE_PERCENT%"
		PRINT_LOG "finish: $FAKE_ENDTIME"
		PRINT_LOG "----"
	done
fi

TIME_END
