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
	data <- read.csv( file = file , head = TRUE , dec = decimal_symbol , sep = separator_symbol, stringsAsFactors=FALSE)
  
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
  
  for (attr in colnames(data[, -1])) {
    v <- data[, attr]
    
    # substituting commas by dots as decimal separators 
    v <- gsub(",", ".", v)
    
    # labeling missing values (this has to be done in order to ignore NAs in (<x) treatment)
    v[which(is.na(v))] <- NOT_AVAILABLE
    aux <- v[nchar(v) == 0]
    if (length(aux) > 0) {
      v[nchar(v) == 0] <- NOT_AVAILABLE
    }
        
    # dividing values with the label (<x) by THRESHOLD_CONSTANT
    aux <- v[substring(v, 1, 1) == "<"]
    if (length(aux) > 0) {
      v[substring(v, 1, 1) == "<"] <- as.character(as.numeric(substring(aux, 2, nchar(aux)))/THRESHOLD_CONSTANT)
    }
    
    # substituting missing values by NA
    aux <- v[v == NOT_AVAILABLE]
    if (length(aux) > 0) {
      v[v == NOT_AVAILABLE] <- NA
    } 
    
    data[, attr] <- as.numeric(v)
  }
  
	# converting FRNAPH.X from percentages to values given that FRNAPH.I + FRNAPH.II + FRNAPH.III + FRNAPH.IV = FRNAPH
	if ( "FRNAPH" %in% colnames( data ) && "FRNAPH.I" %in% colnames( data ) ) { data$FRNAPH.I <- data$FRNAPH * data$FRNAPH.I / 100 }
	if ( "FRNAPH" %in% colnames( data ) && "FRNAPH.II" %in% colnames( data ) ) { data$FRNAPH.II <- data$FRNAPH * data$FRNAPH.II / 100 }
	if ( "FRNAPH" %in% colnames( data ) && "FRNAPH.III" %in% colnames( data ) ) { data$FRNAPH.III <- data$FRNAPH * data$FRNAPH.III / 100 }
	if ( "FRNAPH" %in% colnames( data ) && "FRNAPH.IV" %in% colnames( data ) ) { data$FRNAPH.IV <- data$FRNAPH * data$FRNAPH.IV / 100 }
	
	# returning data object with just the available attributes (plus the CLASS)
	data[ , c( intersect(colnames(data), AVAILABLE_ATTRS) , "CLASS" ) ]
 
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