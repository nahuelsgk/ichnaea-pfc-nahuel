# MST - .R
# Description: Provides methods for ageing both a single instance as well as a whole dataset.
###############################################################################################################################################################

source("constants.R")
source("ageing.R")

load("../data_objects/data_MVT.Rdata")

prepared_data <- data_MV
all_attrs <- setdiff(colnames(data_MV), c("dil", "age", "CLASS", "season"))

results <- data.frame(stringsAsFactors = F)
for(i in 1:nrow(prepared_data)) {
  if(DEBUG) {cat("·")}
  na.sample <- prepared_data[i,]
  sample <- na.sample[, !is.na(na.sample)]
  sample_variables <- colnames(sample)
  
  # finding summer/winter
  season <- sample$season
  
  # finding t*
  estimated_timeH <- age(sample, season, HUMAN)
  estimated_timeA <- age(sample, season, ANIMAL)
  
  real_time <- sample$age
  
  errorH <- real_time - estimated_timeH
  errorH2 <- errorH^2
  
  errorA <- real_time - estimated_timeA
  errorA2 <- errorA^2
  
  attrs <- paste(sort(setdiff(sample_variables, c("dil", "age", "CLASS", "season"))), collapse="/")
  class <- levels(sample$CLASS)[sample$CLASS]
  
  results <- rbind(results, data.frame(attrs, real_time, estimated_timeH, estimated_timeA, errorH, errorA, errorH2, errorA2, class))
}
cat("\n")

results <- data.frame(results)
colnames(results) <- c("attrs", "real_time", "estimated_timeH", "estimated_timeA", "errorH", "errorA", "errorH2", "errorA2", "CLASS")

results$attrs <- as.character(results$attrs)
results$CLASS <- as.numeric(levels(results$CLASS)[results$CLASS])

min_e2 <- data.frame(stringsAsFactors = F)
for (i in 1:nrow(results)) {
  min_e2 <- rbind(min_e2, min(results$errorH2[i], results$errorA2[i]))
}
colnames(min_e2) <- "min_err2"

results$min_err2 <- min_e2

results <- results[order(results$min_err2),]

# checking if error depends on the used variables
for (attr in all_attrs) {
   v <- grepl(attr, results[,1])
   plot(v, main=attr)
}

print(results)