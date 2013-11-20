###############################################################################################################################################################
# MST - data_processor.R
# Description:
# Provides methods to process data before giving it to the models, basically three main things are done:
# 	- applying log10 function to scientific notation attributes
# 	- creating derived variables that the experts determined that could be relevant (ratios)
#	- standardizing data
###############################################################################################################################################################

source( "constants.R" )

##### FUNCTION: 
##### standardize( vector )  :  list( std_vector , mean , sd )
##### Standardizes vector and returns a list that contains the standardized vector, its mean and its standard deviation
##### Params:
##### 	- vector: vector that will be standardized
standardize <- function( vector ){
	# calculating mean and standard deviation from vector, "mean" and "sd" are R core functions
	m <- mean( vector )
	s <- sd( vector )
	
	# correcting standard deviation in case this is 0 before standardizing
	if (s == 0){
		s <- 1
	}
	
	# standardizing
	vector <- ( vector - m ) / s
	
	# returning the standardized vector, its mean and its standard deviation
	list( std_vector = vector , mean = m , sd = s )
}
###############################################################################################################################################################


##### FUNCTION: 
##### process( data , standardize )  :  list( processed_data , mu_sigma_attrs )
##### Processes the data.frame data, concretely:
##### 	- log10 is applied to the required attributes
#####	- derived variables (ratios) are created
#####	- data is standardized (if standardize parameter is TRUE)
##### Returns a list with the processed data.frame and, if standardized, the mean and standard deviation for each attribute and (also for each attribute) the 
##### row indexes whose values are on the threshold.
##### Params:
##### 	- data: data.frame containing the data that wants to be processed
#####	- standardize: if TRUE data will be standardized
process <- function( data , standardize=FALSE ){
	# applying log10 only to scientific notation attributes
	# an EPSILON is added to avoid log10(1) = 0 since the detection threshold is 1 and maybe this attribute will be used as a denominator in a ratio
	log10_attrs <- intersect( colnames( data ) , LOG_10_ATTRS )
	data[ , log10_attrs ] <- log10( data[ , log10_attrs ] + EPSILON )
	###########################################################
	
	# creating derived variables, added as new columns on the data.frame data
	#sums
	if ( "FRNAPH.I" %in% colnames( data ) && "FRNAPH.IV" %in% colnames( data ) )	data$FRNAPH_I_IV 	<- data$FRNAPH.I + data$FRNAPH.IV
	if ( "FRNAPH.II" %in% colnames( data ) && "FRNAPH.III" %in% colnames( data ) )	data$FRNAPH_II_III 	<- data$FRNAPH.II + data$FRNAPH.III
	if ( "Adolescentis" %in% colnames( data ) && "Dentium" %in% colnames( data ) )	data$DA 			<- data$Adolescentis + data$Dentium
	#ratios
	if ( "SOMCPH" %in% colnames( data ) && "GA17" %in% colnames( data ) )		data$SOMCPH_GA17 	<- data$SOMCPH - data$GA17
	if ( "FC" %in% colnames( data ) && "GA17" %in% colnames( data ) )			data$FC_GA17		<- data$FC - data$GA17
	if ( "FC" %in% colnames( data ) && "SOMCPH" %in% colnames( data ) )			data$FC_SOMCPH 		<- data$FC - data$SOMCPH
	if ( "FC" %in% colnames( data ) && "FE" %in% colnames( data ) )				data$FC_FE 			<- data$FC - data$FE
	if ( "SOMCPH" %in% colnames( data ) && "RYC2056" %in% colnames( data ) )	data$SOMCPH_RYC2056 <- data$SOMCPH - data$RYC2056
	if ( "FC" %in% colnames( data ) && "RYC2056" %in% colnames( data ) )		data$FC_RYC2056 	<- data$FC - data$RYC2056
	#exception: creating ratio before applying log10 only on HBSA type variables
	if ( "HBSA.Y" %in% colnames( data ) && "HBSA.T" %in% colnames( data ) ){
		data$HBSAY_HBSAT <- data$HBSA.Y / data$HBSA.T
		data$HBSAY_HBSAT <- log10( data$HBSAY_HBSAT + EPSILON )
	}	
	if ( "HBSA.Y" %in% colnames( data ) ) data$HBSA.Y <- log10( data$HBSA.Y + EPSILON )
	if ( "HBSA.T" %in% colnames( data ) ) data$HBSA.T <- log10( data$HBSA.T + EPSILON )
	###########################################################

  
	# standardization
	if ( standardize == TRUE ){
		# attributes to be standardized, notice names(data) contains recent created ratios
		attrs_std <- setdiff( colnames( data ) , NON_STD_ATTRS )
		# storing variables for the mean and standard deviation of each attribute
		mus <- c()
		sigmas <- c()
		# for each one of the attributes that must be standardized
		for ( attr in attrs_std ){
			# standardizing and for each one of the attributes
			std_data <- standardize( data[ , attr ] )
			data[ , attr ] <- std_data$std_vector
			# storing its mean and standard deviation
			mus <- c( mus , std_data$mean )
			sigmas <- c( sigmas , std_data$sd )
		}
		# building a data.frame that contains the attributes as columns and two rows, first is the mean and second is the standard deviation
		mu_sigma_attrs_df <- data.frame()
		mu_sigma_attrs_df <- rbind( mu_sigma_attrs_df , mus , sigmas )
		colnames( mu_sigma_attrs_df ) <- attrs_std
	} else {
		mu_sigma_attrs_df <- NULL
	}
	###########################################################
	
	list( data = data , mu_sigma_attrs_df = mu_sigma_attrs_df )
}
###############################################################################################################################################################