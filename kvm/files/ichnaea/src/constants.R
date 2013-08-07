###############################################################################################################################################################
# MST - constants.R
# Description: 
# This file contains the constants that will be used along all the software. All the files in the project are expecte to include this file.
###############################################################################################################################################################

##################################### GENERAL #####################################

# DEBUG constant: if TRUE, debug messages will appear on R console.
DEBUG = TRUE

# EPSILON constant: value to be added to the attribute values when applying log10 function in order to avoid log10(1) = 0 since this value could be used later
# as a denominator in some ratio
EPSILON = 0.00001

# SLOPE and THRESHOLD constants: indexes of the slope and threshold coefficients within the linear regression result
SLOPE = 2
THRESHOLD = 1

# AGE_SECTIONS constant: list of the age sections, a bag of models will be generated for each one of the sections
AGE_SECTIONS = c( 0 , 25 , 50) # , 75 , 100 , 125 , 150 )

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

# NA label: constant used to represent temporally NA values
NOT_AVAILABLE = "NOT AVAILABLE"

# AGED_SAMPLES_DIR constant: directory in which ageing samples for differents attributes and seasons are stored
AGED_SAMPLES_DIR = "../data/aging/"

# WINTER and SUMMER constants: represent how summer and winter seasons are expressed in ageing samples filenames.
WINTER <- "Hivern"
SUMMER <- "Estiu"

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

# NON_ATTRS_COMBINATIONS constant: list of all the attributes that will not be considered when computing all the combinations of a given size
NON_COMBINATIONS_ATTRS = c( "CLASS" , "DIL_DEGREE",                    "SOMCPH" , "FRNAPH" ,"FRNAPH.I" , "FRNAPH.II" , "FRNAPH.III" , "FRNAPH.IV", "RYC2056" , "GA17" , "HBSA.Y", "HBSA.T")

##################################### MODELS #####################################

#SVM_C_VALUES constant: list of all the possible values to optimize of the SVM cost attribute
SVM_C_VALUES = c( 0.1 )#, 1 , 2 , 5 , 10 , 50 , 100 )

# NN_K_VALUES constant: list of all the possible values to optimize of the NN number of neighbours attribute
NN_K_VALUES = c( 1 )#, 3 , 5 )

# PNN_SIGMA_VALUES constant: list of all the possible values to optimize the PNN smoothing parameter
PNN_SIGMA_VALUES = c(1/8)#, 1/4, 1/2, 1, 2, 4, 8)

# FOLDS_NUMBER constant: value of the number of folds that will be generated in order to compute cross-validation accuracy
FOLDS_NUMBER = 10

# MODELS_ATTRS_SIZE_COMBINATIONS constant: list of the number of attributes that models will have, remember that for each of the sizes ALL the possible 
# attributes combination will be used
MODELS_ATTRS_SIZE_COMBINATIONS = c( 2)#, 3 , 4 )

# MAX_MODELS constant: limit of the number of selected models for prediction
MAX_MODELS <- 5

# SEARCH_ALG constant: represents the algorithms used in feature selection ("BLIND_SEARCH", "FORWARD_SEARCH" or "BACKWARD_SEARCH")
#SEARCH_ALG <- "BLIND_SEARCH"
#SEARCH_ALG <- "FORWARD_SEARCH"
SEARCH_ALG <- "BACKWARD_SEARCH"

# CRITERION constant: represents the criterion used to compare models ("ACCURACY", "F-MEASURE")
#CRITERION <- "ACCURACY"
CRITERION <- "F-MEASURE"










					


