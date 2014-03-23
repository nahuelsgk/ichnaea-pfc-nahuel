###############################################################################################################################################################
# MST - section_testing.R
# Description: Performs the testing of the models
###############################################################################################################################################################

source( "constants.R" )
source( "data_reader.R" )
source( "data_processor.R" )
source( "models.R" )
source( "ageing.R" )

load( "../data_objects/aged_processed_data_summer.Rdata" )
load( "../data_objects/aged_processed_data_winter.Rdata" )

test_pond_results <- NULL
for(TEST_SET in c("cyprus_test")) {
  for(PONDERATION in c("MAJORITY_VOTE", "ERROR", "ERROR2")) {

MEGAVALIDATION <- TEST_SET == "data_MV"

if (MEGAVALIDATION) {
  # reading prepared test data
  if ( DEBUG ){ print( "Reading prepared data from MegaValidation file..." ) }
  load("../data_objects/data_MV.Rdata")
  
  prepared_test_data <- data_MV
} else {
  # reading test data
  if ( DEBUG ){ print( "Reading data from CSV file..." ) }
  test_data <- read( paste("../data/", TEST_SET,".csv", sep="") , "," , ";" , "CLASS" )
  
  # preparing test data: dropping useless variables, variable conversion and threshold correction
  if ( DEBUG ){ print( "Preparing data..." ) }
  prepared_test_data <- prepare( test_data )
}

predicted_test_data <- NULL
N <- nrow(prepared_test_data)
#N <- 1
for(i in 1:N) {
  if(DEBUG) {cat(paste("***New sample, #", i, "/", N, "\n"))}
  na.sample <- prepared_test_data[i,]
  sample <- na.sample[, !is.na(na.sample)]
  sample_variables <- colnames(sample)
  
  # finding summer/winter
  season <- sample$season
  
  if (is.null(season) || !(season %in% SEASONS)) {
    season <- SUMMER
  }
  
  if (season == WINTER) {
    season_text <- "winter"
  } else {
    season_text <- "summer"
  }
  
  class_prediction <- data.frame(stringsAsFactors = F)
  for (class in c(HUMAN, ANIMAL)) {
    if (class == HUMAN) {
      class_text <- "HUMAN"
    } else {
      class_text <- "ANIMAL"
    }
    
    # finding aging section
    t_alpha <- age_dil(sample, season, class)

    age_time <- t_alpha[[1]]
    dil <- t_alpha[[2]]
    
    if (class == HUMAN) {
      na.sample$tHM <- age_time
      na.sample$dHM <- dil
    } else {
      na.sample$tAN <- age_time
      na.sample$dAN <- dil
    }
    
    if (USE_SECTIONS) {
      section <- find_section(age_time)
      sample_variables <- setdiff(colnames(sample), c("CLASS", "dil", "age", "season"))
      sample[, sample_variables] <- sample[, sample_variables]*dil
    } else {
      section <- 1
      sample_variables <- setdiff(colnames(sample), c("CLASS", "dil", "age", "season"))
      sample[, sample_variables] <- deage(sample[, sample_variables], age_time, season)*dil
    }
    
    # loading the corresponding models object
    load( paste("../data_objects/section_models_", season_text, "_", section,".Rdata", sep="") )
    section_models <- models[[section]]
    
    train_data <- aged_processed_data_summer[[section]]$data
    
    # processing sample: applying log10 and creating derived variables
    processed_sample <- process(sample)$data
    sample_variables <- colnames(processed_sample)
    
    # deleting models using non available variables
    suitable_models <- NULL
    for(j in 1:length(section_models)) {
      model <- section_models[[j]]
      
      if (all(model$comb %in% sample_variables)) {
        suitable_models <- rbind(suitable_models, model)
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
    
    if (!is.null(best_models)) {
      # prediction for each of the selected models
      predicted_models <- NULL
      for(j in 1:dim(best_models)[1]) {
        model <- best_models[j,]
        model$pred <- predict_from_signature(processed_sample, model, train_data)
        predicted_models <- rbind(predicted_models, model)
      }
          
      if (PONDERATION == "MAJORITY_VOTE") {
        predictions <- unlist(predicted_models[, "pred"])
        predictions <- table(predictions)
        predictions <- predictions/sum(predictions)                           # we convert the votes to percentages
      } else {
        predictions <- unlist(predicted_models[, "pred"])
        errors <- 1 - unlist(predicted_models[, "perf"])
        
        if(length(predictions) > 1) {
          if (sum(errors) == 0) {
            confidences <- 1 - errors
            n <- length(errors)
          } else {
            if (PONDERATION == "ERROR") {
              confidences <- 1 - errors/sum(errors)
            } else {
              confidences <- 1 - (errors^2)/sum(errors^2)
            }
            n <- length(errors) - 1
          }
          predictions <- tapply(confidences, predictions, FUN=sum)
          
          predictions[is.na(predictions)] <- 0
          predictions <- predictions/n
        } else {
          predicted_class <- levels(predictions)[predictions]
          predictions <- 1
          names(predictions) <- predicted_class
        }
      }
      
      max_confidence <- max(predictions)
      predictions <- predictions[predictions == max_confidence]             # we keep only the most voted classes
      predictions <- predictions[sample(1:length(predictions), size=1)]     # and pick one randomly among the most voted
      
      final_prediction <- names(predictions)
      class_prediction <- rbind(class_prediction, data.frame(class_text, final_prediction, max_confidence, stringsAsFactors = F))

      if (class == HUMAN) {
        na.sample$PREDHM <- final_prediction
      } else {
        na.sample$PREDAN <- final_prediction
      }
    } else {
      if (class == HUMAN) {
        na.sample$PREDHM <- NA
      } else {
        na.sample$PREDAN <- NA
      }
    }
  }
  
  if (nrow(class_prediction) > 0) {
    max_confidence <- max(class_prediction$max_confidence)
    final_prediction <- sample(class_prediction$final_prediction[which(class_prediction$max_confidence == max_confidence)], 1)
    
    # adding the prediction to the sample
    na.sample$PRED <- final_prediction
    predicted_test_data <- rbind(predicted_test_data, na.sample)
  } else {
    print("No puc predir :(")
  }
}

# changing order of levels to get the confusion matrix
predicted_test_data$PRED <- factor(predicted_test_data$PRED, levels=c(ANIMAL, HUMAN))


# Results
cat("\nTable:\n")
print (predicted_test_data)

tab <- table(factor(predicted_test_data$CLASS, levels=c(-1,1)), predicted_test_data$PRED)
test_error <- 1 - sum(diag(tab))/sum(tab)

cat("\nSummary:\n")

cat(paste(dim(predicted_test_data)[1], "test samples predicted out of", N, "\n"))
cat("Confusion matrix (real\\pred):")

print(tab)

cat("\n")
cat(paste("Test error: ", test_error*100, "%", sep=""))

cat("\n")
info <- paste(TEST_SET, "-", PONDERATION, ": ", dim(predicted_test_data)[1], "/", N, ", ", tab[1,1], " ", tab[1,2], " ", tab[2,1], " ", tab[2,2], ", ", test_error*100, "%", "\n", sep="")
cat(info)
test_pond_results <- c(test_pond_results, info)
}
}

print(test_pond_results)