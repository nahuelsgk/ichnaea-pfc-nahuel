# MST - ageing.R
# Description: Provides methods for ageing both a single instance as well as a whole dataset.
###############################################################################################################################################################

source( "constants.R" )

##### FUNCTION: 
##### aged_samples_lr( attributes , season , orig_data , correction , ret_mean_coef , plot ) : list of matrices
##### Performs a linear regression (LR) over desired attribute ageing essays.
##### Params:
##### 	- attributes: set of attributes in which aged samples LR will be applied
#####	- season: season in which aged samples LR will be applied for the specified attributes
#####	- orig_data: data.frame containing original data, mandatory if correction parameter is TRUE
#####	- correction: if TRUE, essay sample will be corrected according to data provided on parameters orig_data
#####	- mean_coef: if TRUE, the mean of both slope and increment for all the essays (given an attribute) will be returned.
#####				 if FALSE, a list of slopes and increments (one for each essay) will be returned for each one of the attributes						
#####	- plot: if TRUE, all the LR for all the essays for all the attributes will be plotted.
##### Returns a list whose index is the attribute name, each element in the list can contain:
##### 	- a single slope and increment (if mean_coef attribute is TRUE)
##### 	- a list of slopes and increments, one for each essay given an attribute and a season (if mean_coef attribute is FALSE)
aged_samples_lr <- function( attributes , season , orig_data , correction , mean_coef , plot ){
	 
  # list that will be returned
	aged_samples_lr_list <- list()
	
	# if plot is TRUE: requiring user key event to continue interpreting code
	if ( plot ){ par( ask = TRUE ) }
	
	# for all the attributes contained in attributes parameter that belong to the available attributes.
	for ( attr in intersect( attributes , AGEING_AVAILABLE_ATTRS ) ){
		
		# reading all the samples for the corresponding attribute and season
		aged_samples_file_name <- paste( AGED_SAMPLES_DIR , "env" , attr , "-" , season , ".txt" , sep = "" )
		aged_samples <- read.csv( blank.lines.skip=TRUE, file = aged_samples_file_name , header = FALSE , sep = "" , col.names = c( "time" , "value" ), comment.char="#"  )
		
		# list in which the essays LR coefficients (slope and increment) will be stored
		essays_coef <- list()
	
		# obtaining in which indexes of the aged samples start each one of the essays (new essay => t=0)
		essay_splits <- c( which( aged_samples$time == 0 ) , length( aged_samples$time ) + 1 )
		essay_splits <- essay_splits[ -1 ] # removing the first (there will always be a zero on first aged_samples row)
		last_split <- 1 # initializing last split variable to the beginning of th first essay
		
		# for each one of the essays of the current attribute and season
		for ( split in essay_splits ){
		    
			# obtaining the samples of the current essay
			essay_samples <- aged_samples[ last_split : ( split - 1 ) , ] # selecting only the essay_samples
      
			# if correction is TRUE values are considered slightly diluted and therefore are corrected
			if ( correction ){
    		# correction is done using the log10 of the median of the attribute human rows of the original matrix
				essay_samples[ , 2 ] <- essay_samples[ , 2 ] + 
					log10( median( orig_data[ which( orig_data$CLASS == HUMAN ) , attr ] ) ) - aged_samples[ last_split , 2 ]  
			}
   
      # performing LR over the current essay values and storing in the list of all the essays LR coefficients
			essays_coef <- c( essays_coef , list( lm( value ~ time , data = essay_samples )$coefficients ) )
  
      # if plot is TRUE, plotting the samples and the line corresponding to the regression
			if ( plot ){ plot( essay_samples[ , 1 ] , essay_samples[ , 2 ], main = attr ) }
			if ( plot ){ abline( coef = lm( value ~ time , data = essay_samples )$coefficients ) }
			
			# updating last_split variable for the next iteration
			last_split <- split
		}
		
		# converting essay_coef list to a matrix, will have two columns (slope and increment) and as rows as essays
		essays_coef <- do.call( rbind , essays_coef )
		
		# if mean_coef is TRUE, the mean of all essays slope and increment is returned, otherwise a matrix with the slopes and increments for each one of the
		# essays is returned
		if ( mean_coef ){
			aged_samples_lr_list[[ attr ]] <- c( mean( essays_coef[ , 1 ] ) , mean( essays_coef[ , 2 ] ) )
		} else {
			aged_samples_lr_list[[ attr ]] <- essays_coef
		}
		
	}
	
	# returning list with all the results
	aged_samples_lr_list
}
###############################################################################################################################################################


##### FUNCTION: 
##### age_instance( instance , time , age_lr )  :  1-row data.frame
##### Ages an instance according to each attribute's ageing samples linear regression
##### Params:
##### 	- instance: 1-row data.frame instance of attributes that will be diluted
#####	- time: time by which instance will be aged
#####	- age_lr: list containing the slope and increment coefficients for the ageing regression line for each attribute, each element in the list is expected
#####			  to have just ONE slope and ONE increment
##### Returns 1-row data.frame that contains the diluted instance
age_instance <- function( instance , time , age_lr ){
  
	# setting which are the attributes that will be aged, they are the intersection of sets AVAILABLE_ATTRS, AGEING_AVAILABLE_ATTRS and colnames( instance )
	aging_related_attrs <- intersect( intersect( AVAILABLE_ATTRS , AGEING_AVAILABLE_ATTRS ) , colnames( instance ) )
  
	# applying log10 to the ageing related attributes since ageing information (age_lr) is in logarithmic scale
	instance[ , aging_related_attrs ] = log10(instance[ , aging_related_attrs ] + EPSILON )
  
	# for each one of the available attributes from which ageing data is available
	for ( attr in aging_related_attrs){
		# computing new value for the current attribute attr
		new_value <- instance[ , attr ] + ( age_lr[[ attr ]][ SLOPE ] * time )
		# if new value is greater than log10( EPSILON ) (quasi-zero on non-log scale) we set it as is, otherwise is set as zero.
		if ( new_value > log10( EPSILON ) ){
			instance[ , attr ] <- new_value
		} else {
			instance[ , attr ] <- log10( EPSILON )
		}
		
	}
	
	# returning the de-logarithmized aged instance
	instance[ , aging_related_attrs ] = 10 ^ instance[ , aging_related_attrs ]
	instance
}
###############################################################################################################################################################

##### FUNCTION: 
##### age_dataset( dataset , times , age_lr )  :  list
##### Ages a dataset by all the values contained in times 
##### Params:
##### 	- dataset: data.frame to be aged
#####	- times: list of times by which instance will be aged
#####	- age_lr: list containing the slope and increment coefficients for the ageing regression line for each attribute, each element in the list is expected
#####			  to have just ONE slope and ONE increment
##### Returns a list of size length(times) with the dataset ages by all the ages of times
age_dataset <- function( dataset , times , age_lr  ){
	# list to store all the diluted datasets
	list_aged_data <- list()
  
	# for all the values that are in dilution_degrees
	for ( time in times ){
		if ( DEBUG ){ print( paste( "Ageing dataset by a time of" , time ) ) }
    # a data.frame containing dataset diluted by dilution_degree is added at the end of the returning list
		list_aged_data[[ length( list_aged_data ) + 1 ]] <- 
			data.frame( do.call( "rbind" , by( 	data = dataset , simplify = FALSE , INDICES = 1 : nrow( dataset ) , FUN = age_instance , time , age_lr ) ) )
	}
  
	# returning list
	list_aged_data
}


age <- function(sample, season) {
  load("../data_objects/aging_coefs.Rdata")
  season_coefs <- aging_coefs[[season]]
  
  attrs <- intersect(colnames(sample), AGEING_AVAILABLE_ATTRS)
  n <- length(attrs)
  
  load("../data_objects/prepared_original_data.Rdata")
  
  prepared_data <- keep_season(prepared_data, season)
  prepared_data <- prepared_data[, attrs]
  
  combs <- combn(1:n, 2)
  
  df <- data.frame(matrix(ncol=2, nrow=0))
  for (k in 1:ncol(combs)) {
    # Adding equations like: log(uij/uij') - (aj - aj')t = log(v~j/v~j')
    j1 <- combs[1,k]
    j2 <- combs[2,k]
    
    a1 <- season_coefs[[attrs[j1]]][2]
    a2 <- season_coefs[[attrs[j2]]][2]
    coef <- a1 - a2
    
    for (i in 1:nrow(prepared_data)) {
      uij1 <- prepared_data[i, attrs[j1]]
      uij2 <- prepared_data[i, attrs[j2]]
      vj1 <- sample[1, attrs[j1]]
      vj2 <- sample[1, attrs[j2]]
      
      if (vj1 != 0 && vj2 != 0 && uij1 != 0 && uij2 != 0) {
        indep <- -(log10(vj1/vj2) - log10(uij1/uij2))
        if (is.nan(indep)) {
          print(sample)
          print(a2)
          print(vj2)
          skdjskdjsdj
        }
        df <- rbind(df, c(coef, indep))
      }
    }
  }
  colnames(df) <- c("t", "ind")
  
  a <- tapply(df$ind, df$t, mean)
  b <- as.numeric(names(a))
  c <- a/b
  
  d1 <- mean(c)
  d2 <- mean(c, trim = 0.1)
  d3 <- median(c)
  
  lsq <- lm(ind ~ t, df)
  
  cat(paste("Least squares únic. t* =", as.numeric(lsq$coefficients[2]), "\n"))
  cat(paste("Least squares per segments, mean. t* =", d1, "\n"))
  cat(paste("Least squares per segments, trimmed mean 0.1. t* =", d2, "\n"))
  cat(paste("Least squares per segments, median. t* =", d3, "\n"))
  
  cat(paste("\nTemps envelliment real. t*=", sample$age, "\n"))
}

keep_season <- function(data, season) {
  data
}



###############################################################################################################################################################