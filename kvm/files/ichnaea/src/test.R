source( "constants.R" )
source( "models.R" )

load( "../data_objects/aged_processed_data_summer.Rdata" )

sections_train_data <- aged_processed_data_summer

section_tr_data <- sections_train_data[[ 1 ]]$data

folds <- TunePareto::generateCVRuns( section_tr_data[ , "CLASS"] , 1 , FOLDS_NUMBER , stratified = TRUE )
folds <- unlist( folds , recursive = FALSE ) # converting list of list to list of array

available_attrs <- setdiff(names(section_tr_data), NON_COMBINATIONS_ATTRS)

#a <- build_models(available_attrs, section_tr_data, folds)

b <- forward_search(available_attrs, section_tr_data, folds)
