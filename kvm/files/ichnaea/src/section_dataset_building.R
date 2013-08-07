###############################################################################################################################################################
# MST - section_data_building.R
# Description: Builds the training datasets for each one of the sections
###############################################################################################################################################################

source( "constants.R" )
source( "data_reader.R" )
source( "data_processor.R" )
source( "ageing.R" )

# reading original data
if ( DEBUG ){ print( "Reading data from CSV file..." ) }
original_data <- read( "../data/cyprus.csv" , "," , ";" , "CLASS" )

# preparing original data: dropping useless variables, variable conversion and threshold correction
if ( DEBUG ){ print( "Preparing data..." ) }
prepared_data <- prepare( original_data )

# computing logistic regressions for ageing essays on both seasions
if ( DEBUG ){ print( "Computing ageing essays linear regressions for winter..." ) }
aslr_win <- aged_samples_lr( AGEING_AVAILABLE_ATTRS , WINTER , prepared_data , TRUE , TRUE , FALSE )
if ( DEBUG ){ print( "Computing ageing essays linear regressions for summer..." ) }
aslr_sum <- aged_samples_lr( AGEING_AVAILABLE_ATTRS , SUMMER , prepared_data , TRUE , TRUE , FALSE )

# ageing prepared data (for both winter and summer seasons)
aged_data_summer <- age_dataset( prepared_data , c( AGE_SECTIONS ) , aslr_sum )
aged_data_winter <- age_dataset( prepared_data , c( AGE_SECTIONS ) , aslr_win )

# processing aged data
if ( DEBUG ){ print( "Processing all the sections..." ) }
aged_processed_data_summer <- lapply( aged_data_summer , process , TRUE )
save( list = c( "aged_processed_data_summer" ) , file = "../data_objects/aged_processed_data_summer.Rdata" )
aged_processed_data_winter <- lapply( aged_data_winter , process , TRUE )
save( list = c( "aged_processed_data_winter" ) , file = "../data_objects/aged_processed_data_winter.Rdata" )
