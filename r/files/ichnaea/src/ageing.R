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

age_instance <- function(instance, time, age_lr) {
	aging_related_attrs <- intersect( intersect( AVAILABLE_ATTRS , AGEING_AVAILABLE_ATTRS ) , colnames( instance ) )
  
	for (attr in aging_related_attrs){
    val <- instance[, attr]
    if (val < 0) {
      cat(paste("Error: Reading a negative value of", val, "for attribute", attr))
    } else if (val > 0) {
      aged_val <- max(10^(log10(val) + age_lr[[attr]][SLOPE]*time), 0)
      instance[, attr] <- aged_val
    }
	}
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


age_dil <- function(sample, season, class) {
  load("../data_objects/aging_coefs.Rdata")
  season_coefs <- aging_coefs[[season]]
  
  attrs <- intersect(colnames(sample), AGEING_AVAILABLE_ATTRS)
  n <- length(attrs)
  
  load("../data_objects/prepared_original_data.Rdata")
  
  prepared_data <- keep_season(prepared_data, season)
  prepared_data <- keep_class(prepared_data, class)
  prepared_data <- prepared_data[, attrs]
  
  # estimation of t*
  combs <- combn(1:n, 2)
  
  df <- data.frame(matrix(ncol=2, nrow=0))
  for (k in 1:ncol(combs)) {
    # Adding equations like: log(uij/uij') + (aj - aj')t = log(v~j/v~j')
    j1 <- combs[1,k]
    j2 <- combs[2,k]
    
    a1 <- season_coefs[[attrs[j1]]][2]
    a2 <- season_coefs[[attrs[j2]]][2]
    coef <- a1 - a2
    
    vj1 <- sample[1, attrs[j1]]
    vj2 <- sample[1, attrs[j2]]
    
    for (i in 1:nrow(prepared_data)) {
      uij1 <- prepared_data[i, attrs[j1]]
      uij2 <- prepared_data[i, attrs[j2]]

      if (vj1 != 0 && vj2 != 0 && uij1 != 0 && uij2 != 0) {
        indep <- log10(vj1/vj2) - log10(uij1/uij2)
        df <- rbind(df, c(coef, indep))
      }
    }
  }
  colnames(df) <- c("t", "ind")

  if (nrow(df) > 0) {
    t <- as.numeric(lm(ind ~ t - 1, df)$coefficients[1])
  } else {
    t <- 0
  }
  
  # estimation of alpha
  df <- data.frame(matrix(ncol=2, nrow=0))
  for (attr in attrs) {
    aj <- season_coefs[[attr]][2]
    vj <- sample[1, attr]
    coef <- vj
    
    for (i in 1:nrow(prepared_data)) {
      uij <- prepared_data[i, attr]
      
      if (uij != 0) {
        indep <- uij*10^(aj*t)
        df <- rbind(df, c(coef, indep))
      }
    }
  }
  colnames(df) <- c("alpha", "ind")
  
  if (nrow(df) > 0) {
    alpha <- lm(ind ~ alpha - 1, df)$coefficients[1]
  } else {
    alpha <- 1
  }
  
  if (!is.finite(alpha)) {
    alpha <- 1
  }
  
  list(t, alpha)
}

keep_season <- function(data, season) {
  data
}

keep_class <- function(data, class) {
  data[as.character(data$CLASS) == as.character(class),]
}

find_section <- function(time) {
  diffs <- abs(AGE_SECTIONS - time)
  section <- which(diffs == min(diffs))
  
  min(section)
}

deage <- function(sample, t, season) {
  load("../data_objects/aging_coefs.Rdata")
  season_coefs <- aging_coefs[[season]]
  
  attrs <- colnames(sample)
  for (attr in attrs) {
    a <- season_coefs[[attr]][2]
    sample[,attr] <- sample[,attr]/(10^(a*t))
  }
  
  sample
}

###############################################################################################################################################################