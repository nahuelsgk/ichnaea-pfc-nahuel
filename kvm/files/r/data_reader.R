###############################################################################################################################################################
# MST - data_reader.R
# Description:
# Provides methods to read data from CSV files and prepare it to be exploited
###############################################################################################################################################################

source( "constants.R" )

library( car )


##### FUNCTION 
##### read( file , decimal_symbol , separator_symbol , response_var )  :  data.frame
##### Returns a data.frame with the data read from csv file "file"; csv file is assumed to used decimal_symbol and separator_symbol for decimal and column
##### separation, respectively.
##### Params:
##### 	- file: csv file that will be read.
##### 	- decimal_symbol: symbol used to separate the decimal part of the numbers contained in csv file.
##### 	- separator_symbol: symbol used to separate several columns in the csv file
#####	- response_var: attribute in csv file that shoul be considered the response variable
read <- function( file , decimal_symbol , separator_symbol , response_var ){
	# reading csv file
	data <- read.csv( file = file , head = TRUE , dec = decimal_symbol , sep = separator_symbol )
	
	# transforming response variable for it to have only tow values {1,-1} depending whether the example is a human or not
	data[ , response_var ] <- sapply( data[ , response_var ] , function( x ){
		if ( length( grep( HUMAN_CLASS , x ) ) > 0 ){
			HUMAN
		} else {
			ANIMAL
		}
	} )

	# forcing response variable to be an R factor, it is mandatory for most of the used prediction methods
	data[ , response_var ] <- factor( data[ , response_var ] )
	
	# returning data object
	data
}
###############################################################################################################################################################


##### FUNCTION
##### prepare( data )  :  data.frame
##### Makes all the prior necessary transformations to data.frame data, after this function the returned data.frame is intented to be prepared for be exploited
##### Params:
##### 	- data: data.frame in which all the transformations will be done
prepare <- function ( data ){
	# removing chemical variables
	data$CHOL <- NULL
	data$COP <- NULL
	data$EPICOP <- NULL
	data$ETHYLCOP <- NULL
	
	# converting FRNAPH.X from percentages to values given that FRNAPH.I + FRNAPH.II + FRNAPH.III + FRNAPH.IV = FRNAPH
	data$FRNAPH.I = data$FRNAPH * data$FRNAPH.I / 100
	data$FRNAPH.II = data$FRNAPH * data$FRNAPH.II / 100
	data$FRNAPH.III = data$FRNAPH * data$FRNAPH.III / 100
	data$FRNAPH.IV = data$FRNAPH * data$FRNAPH.IV / 100
	
	# the original matrix containted some values with the label (<50), all them are set to be the detection threshold
	data$FC[ data$FC == 50 ] 				 <- DETECT_THRESHOLD
	data$FE[ data$FE == 50 ] 				 <- DETECT_THRESHOLD
	data$CL[ data$CL == 50 ] 				 <- DETECT_THRESHOLD
	data$SOMCPH[ data$SOMCPH == 50 ] 		 <- DETECT_THRESHOLD
	data$FTOTAL[ data$FTOTAL == 50 ] 		 <- DETECT_THRESHOLD
	data$FRNAPH[ data$FRNAPH == 50 ] 		 <- DETECT_THRESHOLD
	data$RYC2056[ data$RYC2056 == 50 ]		 <- DETECT_THRESHOLD
	data$GA17[ data$GA17 == 50 ] 			 <- DETECT_THRESHOLD
	data$HBSA.Y[ data$HBSA.Y == 50 ] 		 <- DETECT_THRESHOLD
	data$HBSA.T[ data$HBSA.T == 50 ] 		 <- DETECT_THRESHOLD
	data$FRNAPH.I[ data$FRNAPH.I == 50 ] 	 <- DETECT_THRESHOLD
	data$FRNAPH.II[ data$FRNAPH.II == 50 ] 	 <- DETECT_THRESHOLD
	data$FRNAPH.III[ data$FRNAPH.III == 50 ] <- DETECT_THRESHOLD
	data$FRNAPH.IV[ data$FRNAPH.IV == 50 ] 	 <- DETECT_THRESHOLD
	
	# returning data object with just the available attributes (plus the CLASS)
	data[ , c( AVAILABLE_ATTRS , "CLASS" ) ]
}
###############################################################################################################################################################

##### FUNCTION
##### plot_dataset( dataset , attrs ) 
##### Plots a 2D scatterplot of attributes attrs from dataset
##### Params:
##### 	- dataset: data.frame in which attrs are contained
#####	- attrs: attributes from dataset whose values will be scatterplotted
plot_dataset <- function( dataset , attrs ){
	attrs <- attrs[ attrs != "CLASS" ]
	x11()
	car::scatterplot( as.formula( paste( attrs[1] , "~" , attrs[2] , "|CLASS" ) ) , data = dataset , reg.line = FALSE , smooth = FALSE )
}
###############################################################################################################################################################