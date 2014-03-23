###############################################################################################################################################################
# MST - section_models_building.R
# Description: Builds the models for each one of the sections
###############################################################################################################################################################

source( "constants.R" )
source( "models.R" )

args <- commandArgs(trailingOnly=TRUE)
args <- c(7, SUMMER)

load("../data_objects/aged_processed_data_summer.Rdata")
load("../data_objects/aged_processed_data_winter.Rdata")

for(i in 1:1) {
  #for (s in c(SUMMER, WINTER)) {
  for (s in c(SUMMER)) {
    print(paste("********************************************", i, "/", s, "*********************************************", sep=""))
    args <- c(i,s)
    if (args[ 2 ] == SUMMER) {
      if ( DEBUG ){
        print(paste("Building all summer models for section", args[1]))
      }
      
      models <- build_all_section_models(aged_processed_data_summer, c(as.integer(args[1])))
      save(list = c("models"), file = paste("../data_objects/section_models_summer_", args[1], ".Rdata", sep = ""))
    } else if (args[2] == WINTER) {
      if (DEBUG){
        print(paste("Building all winter models for section", args[1]))
      }
      
      models <- build_all_section_models(aged_processed_data_winter, c(as.integer(args[1])))
      save(list = c("models"), file = paste("../data_objects/section_models_winter_", args[1], ".Rdata", sep = ""))
    }
  }
}