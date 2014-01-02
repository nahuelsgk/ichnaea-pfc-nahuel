###############################################################################################################################################################
# MST - megavalidation.R
# Description: Constructs and saves a R object containing a (large) number of instances, in order to evaluate the performance of models.
#              Each instance is generated as follows:
#              - Some observation from "../data_objects/prepared_original_data.Rdata" is selected.
#              - Some pseudorandom subset of attributes is selected (see random_attributes function).
#              - The selected values are both randomly aged and diluted.
###############################################################################################################################################################

source( "constants.R" )
source( "ageing.R" )

library( "gtools" )

random_attributes <- function(available_attrs){
  if( length(available_attrs) > 1 && length(intersect(available_attrs, RELEVANT_ATTRS_MV)) > 0 ) {
    attrs <- c()
    num_attrs <- min(max(rbinom(1, 5, 0.7), 2), 6) # num_attrs \in {2,3,4,5,6}
    
    relevant <- sample(intersect(available_attrs, RELEVANT_ATTRS_MV), 1)
    rest <- sample( setdiff(available_attrs, relevant), num_attrs - 1)
    attrs <- union(relevant, rest)
    
    attrs
  }
  else { NA }
}

load( "../data_objects/prepared_original_data.Rdata" )

attrs <-  names( prepared_data )
attrs <- attrs[ attrs != "CLASS" ]

aging_coefs <- list()
aging_coefs[[WINTER]] <- aged_samples_lr( AGEING_AVAILABLE_ATTRS , WINTER , prepared_data , TRUE , TRUE , FALSE )
aging_coefs[[SUMMER]] <- aged_samples_lr( AGEING_AVAILABLE_ATTRS , SUMMER , prepared_data , TRUE , TRUE , FALSE )

if (DEBUG) { cat( "Generating VA data...\n" ) }

data_MV <- data.frame()
for (i in 1:NUM_SAMPLES_MV) {
  if (DEBUG && i%%(NUM_SAMPLES_MV/100) == 0) { cat("+") }
  
  # selecting random parameters
  rd_row <- sample(1:nrow(prepared_data), 1)
  rd_attrs <- random_attributes(attrs)
  rd_time <- runif(1, min=0, max=MAX_AGE_TIME)
  rd_dil <- runif(1, min=1, max=MAX_DIL_DEG)
  rd_season <- sample(c(WINTER, SUMMER), 1)
  
  # picking up the instance
  instance <- prepared_data[rd_row, rd_attrs]
  class <- prepared_data[rd_row, "CLASS"]
  
  # diluting and aging the instance
  instance <- instance/rd_dil
  instance <- age_instance( instance , rd_time , aging_coefs[[rd_season]] )
  
  # adding class and additional information
  instance$CLASS <- class
  instance$dil <- rd_dil
  instance$age <- rd_time
  instance$season <- rd_season
  
  if(i == 1){
    data_MV <- instance
  } else {
    data_MV <- smartbind(data_MV, instance)
  }
}

if (DEBUG) { cat( "\nDone!" ) }

save( data_MV , file = "../data_objects/data_MV.Rdata" )
