###############################################################################################################################################################
# MST - constants.R
# Description: 
# This file contains the constants that will be used along all the software. All the files in the project are expecte to include this file.
###############################################################################################################################################################

##################################### GENERAL #####################################

# DEBUG constant: if TRUE, debug messages will appear on R console.
DEBUG = TRUE

# AGE_SECTIONS constant: list of the age sections, a bag of models will be generated for each one of the sections
AGE_SECTIONS = c(0, 25, 50, 75, 100, 125, 150)

CLASS <- "CLASS"

##################################### READING #####################################

# DECTECT_THRESHOLD constant: value below which attributes are undetectable.
DETECT_THRESHOLD = 25

# THRESHOLD_CONSTANT constant: value by which undetected attributes will be divided.
THRESHOLD_CONSTANT = 100

# HUMAN_CLASS constant: substring containedx by all the original data matrix rows that corresponds to human class
HUMAN_CLASS = "HM"

# HUMAN constant: number used to represent human instances
HUMAN = 1

# ANIMAL constant: number used to represent animal instances
ANIMAL = -1

UNKNOWN = 0

# NA label: constant used to represent temporally NA values
NOT_AVAILABLE = "NOT AVAILABLE"

# AGED_SAMPLES_DIR constant: directory in which ageing samples for differents attributes and seasons are stored
AGED_SAMPLES_DIR = "../data/aging/"

# WINTER and SUMMER constants: represent how summer and winter seasons are expressed in ageing samples filenames.
WINTER <- "Hivern"
SUMMER <- "Estiu"
SEASONS <- c(WINTER, SUMMER)

##################################### ATTRIBUTES #####################################

# AVAILABLE_ATTRS constant: list of the attributes that will be available for the system (both training and predicting)
AVAILABLE_ATTRS = c( "FC" , "FE" , "CL" , "SOMCPH" , "FRNAPH" ,"FRNAPH.I" , "FRNAPH.II" , "FRNAPH.III" , "FRNAPH.IV", "RYC2056" , "GA17" , "HBSA.Y", "HBSA.T" )

# LOG_10_ATTRS constant: list of the attributes in which log10 function must be applied before modelling
LOG_10_ATTRS = c( "FC" , "FE" , "CL" , "SOMCPH" , "FTOTAL" , "FRNAPH" , "RYC2056" , "GA17" , "FRNAPH.I" , "FRNAPH.II" , "FRNAPH.III" , "FRNAPH.IV" )

# NON_STD_ATTRS constant: list of all the attributes that must not be standardized (notice response variable CLASS and instance dilution degree information
# DIL_DEGREE are included)
NON_STD_ATTRS = c( "CLASS" , "Dentium" , "Adolescentis" , "DA" , "DIL_DEGREE")

# DIRECT_DILUTED_ATTRS constant: list of all the attributes that are directly diluted by dividing its value by the dilution degree
DIRECT_DILUTED_ATTRS = c(   "FC" , "FE" , "CL" , "SOMCPH" , "FTOTAL" , "FRNAPH" , "RYC2056" , "GA17" , "FRNAPH.I" , "FRNAPH.II" , "FRNAPH.III" , "FRNAPH.IV" ,
                            "HBSA.Y" , "HBSA.T" )

# AGEING_AVAILABLE_ATTRS constant: list of the attributes whose ageing linear regressions are available
AGEING_AVAILABLE_ATTRS = c( "FC" , "FE" , "CL" , "SOMCPH" , "FRNAPH" ,"FRNAPH.I" , "FRNAPH.II" , "FRNAPH.III" , "FRNAPH.IV", "RYC2056" , "GA17" , "HBSA.Y", 
                            "HBSA.T" )

# DIL_ATTRS constant: list of the attributes for which the dilution does take place
DIL_AVAILABLE_ATTRS = c( "FC" , "FE" , "CL" , "SOMCPH" , "FRNAPH" ,"FRNAPH.I" , "FRNAPH.II" , "FRNAPH.III" , "FRNAPH.IV", "RYC2056" , "GA17" , "HBSA.Y", "HBSA.T" )

# NON_ATTRS_COMBINATIONS constant: list of all the attributes that will not be considered when computing all the combinations of a given size
NON_COMBINATIONS_ATTRS = c( "CLASS" , "DIL_DEGREE")#,"SOMCPH" , "FRNAPH" ,"FRNAPH.I" , "FRNAPH.II" , "FRNAPH.III" , "FRNAPH.IV", "RYC2056" , "GA17" , "HBSA.Y", "HBSA.T")

# RELEVANT_ATTRS_MV constant: important attributes to include in megavalidation samples
RELEVANT_ATTRS_MV <-  c( "FC" , "FE" , "CL" , "SOMCPH" , "FRNAPH.II" , "GA17" , "HBSA.Y", "HBSA.T" )

##################################### MODELS #####################################

#SVM_C_VALUES constant: list of all the possible values to optimize of the SVM cost attribute
SVM_C_VALUES = c(0.1, 1, 2, 5, 10, 50, 100)

# NN_K_VALUES constant: list of all the possible values to optimize of the NN number of neighbours attribute
NN_K_VALUES = c(1, 3, 5)

# PNN_SIGMA_VALUES constant: list of all the possible values to optimize the PNN smoothing parameter
PNN_SIGMA_VALUES = c(1/8, 1/4, 1/2, 1, 2, 4, 8)

# CV_TIMES constant: value of the number of times that cross-validation will average on
CV_TIMES = 10
 
# FOLDS_NUMBER constant: value of the number of folds that will be generated in order to compute cross-validation accuracy
FOLDS_NUMBER = 10

# MODELS_ATTRS_SIZE_COMBINATIONS constant: list of the number of attributes that models will have, remember that for each of the sizes ALL the possible 
# attributes combination will be used
MODELS_ATTRS_SIZE_COMBINATIONS = c(2, 3, 4)

# MAX_MODELS constant: limit of the number of selected models for prediction
MAX_MODELS <- 10 #5

# SEARCH_ALG constant: represents the algorithms used in feature selection ("BLIND_SEARCH", "FORWARD_SEARCH" or "BACKWARD_SEARCH")
#SEARCH_ALG <- "BLIND_SEARCH"
SEARCH_ALG <- "FORWARD_SEARCH"
#SEARCH_ALG <- "BACKWARD_SEARCH"

##################################### MEGAVALIDATION #####################################

# SLOPE and THRESHOLD constants: indexes of the slope and threshold coefficients within the linear regression result
SLOPE = 2
THRESHOLD = 1

# NUM_SAMPLES_MV constant: Number of samples generated in megavalidation
NUM_SAMPLES_MV <- 1000

# MAX_AGE_TIME constant: maximum time with which samples in megavalidation will be aged
MAX_AGE_TIME <- 150

# MAX_DIL_DEG constant: maximum dilution degree with which samples in megavalidation will be aged
MAX_DIL_DEG <- 500










################################# EXPERIMENT PARAMETERS ################################# 

###### PERFORMANCE CRITERION
# CRITERION constant: represents the criterion used to compare models ("ACCURACY", "F-MEASURE")
CRITERION <- "ACCURACY"
#CRITERION <- "F-MEASURE"

###### CORRECTION
CORRECTION <- FALSE

###### 55 or 510
# MODELS_ATTRS_SIZE_LIMIT constant: generated models will use at most this number of variables
MODELS_ATTRS_SIZE_LIMIT <- 5

# MODELS_GUIDED_SEARCH_REPETITIONS constant: process of generating models will run this number of times
MODELS_GUIDED_SEARCH_REPETITIONS <- 5
#MODELS_GUIDED_SEARCH_REPETITIONS <- 10

###### COMBINING CLASSIFIERS PONDERATION
PONDERATION <- "MAJORITY_VOTE"
#PONDERATION <- "ERROR"
#PONDERATION <- "ERROR2"

###### TEST SET SELECTION
# Setting test set
TEST_SET <- "cyprus_test"
#TEST_SET <- "cyprus_test2"
#TEST_SET <- "env_test"
#TEST_SET <- "data_MV"

###### PARADIGM SELECTION
USE_SECTIONS <- FALSE

