###############################################################################################################################################################
# MST - section_testing.R
# Description: Performs the testing of the models
###############################################################################################################################################################

source( "constants.R" )
source( "data_reader.R" )
source( "data_processor.R" )
source( "models.R" )

load( "../data_objects/aged_processed_data_summer.Rdata" )
load( "../data_objects/aged_processed_data_winter.Rdata" )

# reading test data
if ( DEBUG ){ print( "Reading data from CSV file..." ) }
test_data <- read( "../data/cyprus_test.csv" , "," , ";" , "CLASS" )

# preparing test data: dropping useless variables, variable conversion and threshold correction
if ( DEBUG ){ print( "Preparing data..." ) }
prepared_test_data <- prepare( test_data )

# processing test data: applying log10 and creating derived variables
if ( DEBUG ){ print( "Processing data..." ) }
processed_test_data <- process( prepared_test_data )$data

predicted_test_data <- NULL
for(i in 1:nrow(processed_test_data)) {
  if(DEBUG) {cat(paste("***New sample, #", i, "\n"))}
  
  na.sample <- processed_test_data[i,]
  sample <- na.sample[, !is.na(na.sample)]
  sample_variables <- colnames(sample)
  
  # finding summer/winter
  season <- "summer"
  
  # finding aging section
  section <- 1
  
  # loading the corresponding models object
  load( paste("../data_objects/section_models_", season, "_", section,".Rdata", sep="") )
  
  train_data <- aged_processed_data_summer[[section]]$data
  mu_sigma_attrs_df <- aged_processed_data_summer[[section]]$mu_sigma_attrs_df
  
  # standarisation
  mu <- mu_sigma_attrs_df[1, setdiff(sample_variables, "CLASS")]
  sigma <- mu_sigma_attrs_df[2, setdiff(sample_variables, "CLASS")]
  sample[, setdiff(sample_variables, "CLASS")] <- (sample[, setdiff(sample_variables, "CLASS")] - mu)/sigma
  
  
  # deleting models using non available variables
  suitable_models <- NULL
  for(j in 1:length(models[[section]])) {
    models_matrix <- models[[section]][[j]]
    
    if (all(models_matrix[1,]$comb %in% sample_variables)) {
      suitable_models <- rbind(suitable_models, models_matrix)
    }
  }
  
  # keeping only high-performance models
  performances <- unlist(suitable_models[, "perf"])
  best_models <- NULL
  if (length(performances) > MAX_MODELS) {
    performances <- sort(performances, decreasing=T)
    min_perf <- performances[MAX_MODELS]
    
    for(j in 1:dim(suitable_models)[1]) {
      model <- suitable_models[j,]
            
      if (model$perf >= min_perf) {
        best_models <- rbind(best_models, model)
      }
    }
  } else {
    best_models <- suitable_models
  }
  
  # prediction for each of the selected models
  predicted_models <- NULL
  for(j in 1:dim(best_models)[1]) {
    model <- best_models[j,]
    model$pred <- predict_from_signature(sample, model, train_data)
    predicted_models <- rbind(predicted_models, model)
  }
  
    
  # majority vote
  predictions <- unlist(predicted_models[, "pred"])
  predictions <- table(predictions)
  predictions <- predictions[predictions == max(predictions)]           # we keep only the most voted classes
  predictions <- predictions[sample(1:length(predictions), size=1)]     # and pick one randomly among the most voted
  
  final_prediction <- names(predictions)
  
  # adding the prediction to the sample
  na.sample$PRED <- final_prediction
  predicted_test_data <- rbind(predicted_test_data, na.sample)
}

# changing order of levels to get the confusion matrix
predicted_test_data$PRED <- factor(predicted_test_data$PRED, levels=c(ANIMAL, HUMAN))


# Results
print (predicted_test_data)

tab <- table(predicted_test_data$CLASS, predicted_test_data$PRED)
test_error <- 1 - sum(diag(tab))/sum(tab)

cat("\nSummary:\n")

cat(paste(dim(predicted_test_data)[1], "test samples predicted\n"))
cat("Confusion matrix (real\\pred):")

print(tab)

cat(paste("Test error: ", test_error*100, "%", sep=""))
