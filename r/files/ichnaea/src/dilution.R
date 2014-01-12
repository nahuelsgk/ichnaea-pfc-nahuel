# MST - dilution.R
# Description: Provides methods for diluting both a single instance as well as a whole dataset
###############################################################################################################################################################


##### FUNCTION: 
##### dilute_instance( instance , dilution_degree )  :  1-row data.frame
##### Dilutes an instance by dilution_degree according to the dilution rules
##### Params:
##### 	- instance: 1-row data.frame instance of attributes that will be diluted
#####	- dilution_degree: dilution degree by which instance will be diluted
##### Returns 1-row data.frame that contains the diluted instance
dilute_instance <- function( instance , dilution_degree ){
	# Computing which attributes will fit in the direct dilution, will be the intersection of the sets: AVAILABLE_ATTRS, DIRECT_DILUTED_ATTRS and the 
	# column names of instance.
	dir_dil_attrs <- intersect( colnames( instance ) , DIRECT_DILUTED_ATTRS )
	
	# Performing direct dilutions.
	instance[ , dir_dil_attrs ] <-  instance[ , dir_dil_attrs ] / dilution_degree 
	
	# Dilution rules.
	if ( "FE" %in% colnames( instance ) && instance$FE == DETECT_THRESHOLD ){
		if ( "DiE" %in% colnames( instance ) )	instance$DiE 	<- 0
		if ( "FM.FS" %in% colnames( instance ) )	instance$FM.FS 	<- 0
		if ( "Hir" %in% colnames( instance ) )	instance$Hir 	<- 0
	}
	if ( "FC" %in% colnames( instance ) && instance$FC == DETECT_THRESHOLD ){
		if ( "DiC" %in% colnames( instance ) )	instance$DiC <- 0
		if ( "ECP" %in% colnames( instance ) )	instance$ECP <- 0
		if ( "ECT" %in% colnames( instance ) )	instance$ECT <- 0
	}
	if ( "HBSA.Y" %in% colnames( instance ) && instance$HBSA.Y < 1000 ){
		if ( "Dentium" %in% colnames( instance ) )		instance$Dentium <- 0
		if ( "Adolescentis" %in% colnames( instance ) )	instance$Adolescentis <- 0
	}
	
	# Assingning column DIL_DEGREE to the instance indicating by which dilution degree it has been diluted.
	instance$DIL_DEGREE <- dilution_degree
	
	# Returning diluted instance.
	instance	
}
###############################################################################################################################################################

##### FUNCTION: 
##### dilute_dataset( dataset , dilution_degrees )  :  list
##### Dilutes a dataset by all the values contained in dilution_degrees 
##### Params:
##### 	- dataset: data.frame to be diluted
#####	- dilution_degrees: list of dilution degrees by which dataset will be diluted
##### Returns a list of size length(dilution_degrees) with the dataset diluted by all the values of dilution_degrees
dilute_dataset <- function( dataset , dilution_degrees ){
	# list to store all the diluted datasets
	list_diluted_data <- list() 
	
	# for all the values that are in dilution_degrees
	for ( dilution_degree in dilution_degrees ){
		# a data.frame containing dataset diluted by dilution_degree is added at the end of the returning list
		list_diluted_data[[ length( list_diluted_data ) + 1 ]] <- 
				data.frame( do.call( "rbind" , by( 	data = dataset , simplify = FALSE , INDICES = 1 : nrow( dataset ) , FUN = dilute_instance , 
										dilution_degree ) ) )
	}
	
	# returning list
	list_diluted_data
}
###############################################################################################################################################################

