
library( XML )
library( StatDataML )

args <- commandArgs( trailingOnly=TRUE )
len <- nchar( args[ 1 ] )
ext <- tolower(substr( args[ 1 ], len-5, len))

if( ext == ".rdata" ) {
	data <- load( args[ 1 ] )
	writeSDML( data, args[ 2 ] )
} else {
	data <- readSDML( args[ 1 ] )
	save( data , args[ 2 ] )
}