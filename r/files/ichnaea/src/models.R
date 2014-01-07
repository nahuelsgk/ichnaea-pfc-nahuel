###############################################################################################################################################################
# MST - models.R
# Description: Provides methods to build different model types for each section and combining systematically any desired number of variables
###############################################################################################################################################################

source( "constants.R" )

library(ipred)
library(kernlab)
library(class)
library(boot)
library(TunePareto)
library(e1071)
library(pnn)
library(rvmbinary)

custom_predict_slda <- function( object, newdata, ... ) {
	X <- as.matrix( newdata ) %*% object$scores
	predict( object$mylda , as.data.frame(X) )
}

##### FUNCTION: 
##### accuracy <- function(tab)  :  numeric
##### Computes the accuracy of a confusion matrix
##### Params:
##### - real: factor with the actual class labels
##### - pred: factor with the predicted class labels
accuracy <- function(real, pred) {
  if (length(real) == length(pred)) {
    sum(pred == real) / length(pred)
  }
  else {
    NA
  }
}

##### FUNCTION: 
##### f_measure <- function(real, pred)  :  numeric
##### Computes the F-Measure of a confusion matrix
##### Params:
##### - real: factor with the actual class labels
##### - pred: factor with the predicted class labels
f_measure <- function(real, pred) {
  if (length(real) == length(pred)) {
    # sorting the levels of the factors
    real <- factor(real, levels=c(1,-1))
    pred <- factor(pred, levels=c(1,-1))
    
    tab <- table(real, pred)
    
    # true/false positive/negative (positive being human)
    TN <- tab[2,2]
    FP <- tab[2,1]
    TP <- tab[1,1]
    FN <- tab[1,2]
    
    if (TP > 0) {
      # precision
      P <- TP / (TP + FP)
      # recall
      R <- TP / (TP + FN)
      
      2*P*R/(P+R)  
    } else {
      0
    }
  }
  else {
    NA
  }
}


##### FUNCTION: 
##### predict_from_signature( sample, signature , train_data )  :  class label
##### Predicts a sample with a model according to a signature
##### Returns the label predicted for the sample by the method specified in the signature built with the provided train_data.
##### Params:
##### - sample: observation of a validation/test data frame containing at least the variables specified by the signature.
##### - signature: signature of the model to be built, contains: model algorithm , attribute combination , model performance , model parameters.
#####	- train_data: data.frame that contains training data
predict_from_signature <- function( sample, signature , train_data ){
  
  # SLDA building
  ##############################################################################
  if ( signature$alg == "slda" ){
    m <- ipred::slda( y = train_data[ , "CLASS" ] , X = train_data[ , signature$comb, drop=FALSE ] , CV = FALSE ) 
    m_pred <- custom_predict_slda( m , sample[ , signature$comb ] )$class
  } 
  ##############################################################################
  
  # SVM building
  ##############################################################################
  else if ( signature$alg == "svm" ){
    # converting training data to a matrix since it is required by SVM methods
    matr_train_data <- as.matrix( train_data[ , signature$comb ] )
    matr_class_data <- as.matrix( train_data[ , "CLASS" ] )
    
    # building SVM model according to kernel and the associated parameters
    if ( signature$args[[ 1 ]] == "lin" ){
      m <- kernlab::ksvm( matr_train_data , matr_class_data , kernel = "polydot" , 
                          kpar = list( degree = 1 , scale = 1 , offset = 1 ) , C = signature$args[[ 2 ]] , scaled = FALSE )
    } else if ( signature$args[[ 1 ]] == "poly" ){
      m <- kernlab::ksvm( matr_train_data , matr_class_data , kernel = "polydot" , 
                          kpar = list( degree = 2 ) , C = signature$args[[ 2 ]] , scaled = FALSE )
    } else if ( signature$args[[ 1 ]] == "rbf" ){
      m <- kernlab::ksvm(  matr_train_data , matr_class_data , kernel = "rbfdot" , 
                           kpar = signature$args[[ 3 ]] , C = signature$args[[ 2 ]] , scaled = FALSE )
    }
    
    m_pred <- predict(m, sample[, signature$comb ])
  } 
  ##############################################################################
  
  # KNN building
  ##############################################################################
  else if ( signature$alg == "knn" ){
    m <- class::knn( train = train_data[ , signature$comb, drop=FALSE  ] , test = sample[ , signature$comb, drop=FALSE  ] , 
                     cl = train_data[ , "CLASS" ] , k = signature$args[[ 1 ]] , prob = FALSE )
    m_pred <- m
  } 
  ##############################################################################
  
  # LR building
  ##############################################################################
  else if ( signature$alg == "lr" ){
    m_lr <- stats::glm( CLASS ~ . , data = train_data[, c( signature$comb , "CLASS" ) ] , family = binomial )
    m_lr_pred <- predict( m_lr , newdata = sample[ , c( signature$comb , "CLASS" ) ] ) 
    pt <- 1 - ( 1 / ( 1 + exp( m_lr_pred ) ) )
    glfpredt <- NULL
    glfpredt[ pt < 0.5 ] <- ANIMAL
    glfpredt[ pt >= 0.5 ] <- HUMAN
    
    m_pred <- glfpredt
  }
  ##############################################################################
  
  
  # NB building
  ##############################################################################
  else if ( signature$alg == "nb" ){
    m <- e1071::naiveBayes( x = train_data[ , signature$comb  ] , y = train_data[ , "CLASS" ])
    m_pred <- predict(m, sample[, signature$comb])
  }
  ##############################################################################
  
  
  # PNN building
  ##############################################################################
  else if ( signature$alg == "pnn" ){
    m <- pnn::smooth( pnn::learn( cbind( train_data[ , "CLASS" ], train_data[ , signature$comb  ] ) ), sigma=signature$args[[ 1 ]] )
    a <- pnn::guess(m, as.matrix(sample[, signature$comb]))
    m_pred <- as.numeric(a$category)
  }
  ##############################################################################
  
  # RVM building
  ##############################################################################
  else if ( signature$alg == "rvm" ){
    # converting training data to a matrix since it is required by RVM methods
    matr_train_data <- as.matrix( train_data[ , signature$comb ] )
    matr_class_data <- as.matrix( train_data[ , "CLASS" ] )
    
    sink(file="/dev/null")
    # building RVM model according to kernel and the associated parameters
    if ( signature$args[[ 1 ]] == "lin" ){
      m <- rvmbinary::rvmbinary( matr_train_data , as.factor(matr_class_data) , kernel = "polydot" , 
                          parameters = c( degree = 1 , scale = 1 , offset = 1 ) )
    } else if ( signature$args[[ 1 ]] == "poly" ){
      m <- rvmbinary::rvmbinary( matr_train_data , as.factor(matr_class_data) , kernel = "polydot" , 
                          parameters = c( degree = 2 , scale = 1 , offset = 1 ) )
    } else if ( signature$args[[ 1 ]] == "rbf" ){
      m <- rvmbinary::rvmbinary( matr_train_data , as.factor(matr_class_data) , kernel = "rbfdot" , 
                          parameters = signature$args[[ 2 ]] )
    }
    sink()
    
    m_rvm_pred <- predict(m, sample[, signature$comb ])
    if (m_rvm_pred < 0.5) {
      m_pred <- HUMAN
    } else {
      m_pred <- ANIMAL
    }
    
  } 
  ##############################################################################
  
  as.factor(m_pred)
}
###############################################################################################################################################################



##### FUNCTION: 
##### build_models( attrs , train_data , folds )  :  matrix
##### Builds the different models using data.frame train_data as training data and attributes attrs as explanatory variables.
##### Returns a matrix in which each row contains: model algorithm , attribute combination , model performance , model parameters.
##### IMPORTANT! The build model is not returned, just its "signature"
##### Params:
##### - attrs: attributes from data.frame train_data that will be used as explanatory variables
#####	- train_data: data.frame that contains training data
#####	- folds: list of array in which each of the elements (indexed by attribute name) are the rows corresponding to a particular folds to compute CV performance
deprecated__build_models <- function( attrs , train_data , folds ){
  # matrix in which resulting models will be stored, this is the object to be returned
  models_matrix <- c()
  if ( DEBUG ){ cat( "******************************************************\n" ) }
  if ( DEBUG ){ cat( "Building models for attribute set" , attrs , "\n" ) }
  
  # SLDA building
  models_matrix <- rbind(models_matrix, build_slda(attrs, train_data, folds))
  
  # SVM with linear, polynomic and radial kernels building
  for (c in SVM_C_VALUES) {
    models_matrix <- rbind(models_matrix, build_svm(attrs, train_data, folds, c, "lin"))
    models_matrix <- rbind(models_matrix, build_svm(attrs, train_data, folds, c, "poly"))
    models_matrix <- rbind(models_matrix, build_svm(attrs, train_data, folds, c, "rbf"))
  }
  
  # k-NN building
  for (k in NN_K_VALUES){
    models_matrix <- rbind(models_matrix, build_knn(attrs, train_data, folds, k))
  }
  
  # Logistic Regression building
  models_matrix <- rbind(models_matrix, build_lr(attrs, train_data, folds))
  
  # Naive Bayes building
  models_matrix <- rbind(models_matrix, build_nb(attrs, train_data, folds))

  # Probabilistic Neural Network building
  for (sigma in PNN_SIGMA_VALUES){
    if(FALSE){ #no prediu bé!
      models_matrix <- rbind(models_matrix, build_pnn(attrs, train_data, folds, sigma))
    }
  }
  
  # RVM with linear, polynomic and radial kernels building
  # only available for two classes
  if (FALSE) { # ojo que de vegades retorna algun NaN!
    models_matrix <- rbind(models_matrix, build_rvm(attrs, train_data, folds, "lin"))
    models_matrix <- rbind(models_matrix, build_rvm(attrs, train_data, folds, "poly"))
    models_matrix <- rbind(models_matrix, build_rvm(attrs, train_data, folds, "rbf"))
  }
  
  # returning matrix in which resulting models are stored
  models_matrix
}
###############################################################################################################################################################

build_slda <- function(attrs, train_data, folds) {
  perfs <- sapply( folds , function ( fold_va_indexes ){
    m <- ipred::slda( y = train_data[ -fold_va_indexes , "CLASS" ] , X = train_data[ -fold_va_indexes , attrs, drop=F ] , CV = FALSE )
    m_pred <- custom_predict_slda( m , train_data[ fold_va_indexes , attrs ] )$class
    
    if (CRITERION == "ACCURACY") {
      accuracy(train_data[ fold_va_indexes , "CLASS" ], m_pred)
    } else if (CRITERION == "F-MEASURE"){
      f_measure(train_data[ fold_va_indexes , "CLASS" ], m_pred)
    }
  } )
  
  list(alg = "slda", comb = attrs, perf = mean( perfs ), args = list())
}

build_svm <- function(attrs, train_data, folds, c, kern) {
  # converting training data to a matrix since it is required by SVM methods
  matr_train_data <- as.matrix( train_data[ , attrs ] )
  matr_class_data <- as.matrix( train_data[ , "CLASS" ] )
  
  # selecting appropriate parameters according to the kernel
  if (kern == "lin") {
    kernel <- "polydot"
    kpar <- list( degree = 1 , scale = 1 , offset = 1 )
    params <- list("lin", c)
  } else if (kern == "poly") {
    kernel <- "polydot"
    kpar <- list( degree = 2 )
    params <- list("poly", c)
  } else if (kern == "rbf") {
    kernel <- "rbfdot"
    # estimating sigma value for RBF kernel SVM
    kpar <- list( sigma = mean( sigest( matr_train_data , scaled = FALSE )[ c( 1 , 3 ) ]))
    params <- list("rbf", c, kpar)
  }
  
  perfs <- sapply( folds , function ( fold_va_indexes ){ 
    # building model using the indexes NOT contained on fold_va_indexes array 
    m <- kernlab::ksvm( matr_train_data[ -fold_va_indexes , ], matr_class_data[ -fold_va_indexes , ], kernel = kernel, kpar = kpar, C = c , scaled = FALSE )
    
    # predicting the indexes contained on fold_va_indexes array 
    m_pred <- predict( m , matr_train_data[ fold_va_indexes , ] )
    
    # computing the performance
    if (CRITERION == "ACCURACY") {
      accuracy(as.factor(matr_class_data[ fold_va_indexes , ]), m_pred)
    } else if (CRITERION == "F-MEASURE"){
      f_measure(as.factor(matr_class_data[ fold_va_indexes , ]), m_pred)
    }
  } )
  
  list(alg = "svm", comb = attrs, perf = mean(perfs), args = params)
}

build_knn <- function(attrs, train_data, folds, k) {
  perfs <- sapply( folds , function ( fold_va_indexes ){
    # building model using the indexes NOT contained on fold_va_indexes array and predicting the indexes contained on fold_va_indexes array
    m <- class::knn( train = train_data[ -fold_va_indexes , attrs, drop=FALSE ] , test = train_data[ fold_va_indexes , attrs, drop=FALSE ] , 
                     cl = train_data[ -fold_va_indexes , "CLASS" ] , k = k , prob = FALSE )
    
    # computing the performance
    if (CRITERION == "ACCURACY") {
      accuracy(train_data[ fold_va_indexes , "CLASS" ], m)
    } else if (CRITERION == "F-MEASURE"){
      f_measure(train_data[ fold_va_indexes , "CLASS" ], m)
    }
  } )
  
  list(alg = "knn", comb = attrs, perf = mean(perfs), args = list(k))
}

build_lr <- function(attrs, train_data, folds) {
  perfs <- sapply( folds , function ( fold_va_indexes ){
    # building model using the indexes NOT contained on fold_va_indexes array 
    m_lr <- stats::glm( CLASS ~ . , data = train_data[ -fold_va_indexes , c( attrs , "CLASS" ) ] , family = binomial )
    # predicting the indexes contained on fold_va_indexes array 
    m_lr_pred = predict( m_lr , newdata = train_data[ fold_va_indexes , c( attrs , "CLASS" ) ] ) 
    pt = 1 - ( 1 / ( 1 + exp( m_lr_pred ) ) )
    glfpredt = NULL
    glfpredt[ pt < 0.5 ] = ANIMAL
    glfpredt[ pt >= 0.5 ] = HUMAN
    
    # computing the performance
    if (CRITERION == "ACCURACY") {
      accuracy(train_data[ fold_va_indexes , "CLASS" ], as.factor(glfpredt))
    } else if (CRITERION == "F-MEASURE"){
      f_measure(train_data[ fold_va_indexes , "CLASS" ], as.factor(glfpredt))
    }
  } )
  
  list(alg = "lr", comb = attrs, perf = mean(perfs), args = list())
}

build_nb <- function(attrs, train_data, folds) {
  perfs <- sapply( folds , function ( fold_va_indexes ){
    # building model using the indexes NOT contained on fold_va_indexes array
    m <- e1071::naiveBayes(x = train_data[ -fold_va_indexes , attrs ], y = train_data[ -fold_va_indexes , "CLASS" ])
    # predicting the indexes contained on fold_va_indexes array 
    m_pred <- predict(m, train_data[ fold_va_indexes , attrs ])
    
    # computing the performance
    if (CRITERION == "ACCURACY") {
      accuracy(train_data[ fold_va_indexes , "CLASS" ], m_pred)
    } else if (CRITERION == "F-MEASURE"){
      f_measure(train_data[ fold_va_indexes , "CLASS" ], m_pred)
    }
  } )
  
  list(alg = "nb", comb = attrs, perf = mean(perfs), args = list())
}

build_pnn <- function(attrs, train_data, folds, sigma) {
  perfs <- sapply( folds , function ( fold_va_indexes ){
    # building model using the indexes NOT contained on fold_va_indexes array
    m <- pnn::smooth( pnn::learn( cbind( train_data[ -fold_va_indexes , "CLASS" ], train_data[ -fold_va_indexes , attrs ] ) ), sigma=sigma )
    # predicting the indexes contained on fold_va_indexes array
    m_pred <- NULL
    for (i in 1:length(fold_va_indexes)) {
      a <- train_data[fold_va_indexes[i], attrs]
      print(str(c(1,1)))
      print(str(a))

      b <- pnn::guess(m, a)
      print(b)
      dsdmsdmksm
      m_pred <- c(m_pred, as.numeric(b))
    }
    
    # computing the performance
    if (CRITERION == "ACCURACY") {
      accuracy(train_data[ fold_va_indexes , "CLASS" ], as.factor(m_pred))
    } else if (CRITERION == "F-MEASURE"){
      f_measure(train_data[ fold_va_indexes , "CLASS" ], as.factor(m_pred))
    }
  } )
  
  list(alg = "pnn", comb = attrs, perf = mean(perfs), args = list(sigma))
}

build_rvm <- function(attrs, train_data, folds, kern) {
  # converting training data to a matrix since it is required by RVM methods
  matr_train_data <- as.matrix( train_data[ , attrs ] )
  matr_class_data <- as.matrix( train_data[ , "CLASS" ] )
  
  # selecting appropriate parameters according to the kernel
  if (kern == "lin") {
    kernel <- "polydot"
    kpar <- list( degree = 1 , scale = 1 , offset = 1 )
    params <- list("lin")
  } else if (kern == "poly") {
    kernel <- "polydot"
    kpar <- list( degree = 2 , scale = 1 , offset = 1 )
    params <- list("poly")
  } else if (kern == "rbf") {
    kernel <- "rbfdot"
    # estimating sigma value for RBF kernel RVM
    kpar <- list( sigma = mean( sigest( matr_train_data , scaled = FALSE )[ c( 1 , 3 ) ]))
    params <- list("rbf", kpar)
  }
  
  sink(file="/dev/null")
  perfs <- sapply( folds , function ( fold_va_indexes ){
    # building model using the indexes NOT contained on fold_va_indexes array
    m <- rvmbinary::rvmbinary(matr_train_data[-fold_va_indexes,], as.factor(matr_class_data[-fold_va_indexes,]), kernel = kernel, parameters = kpar)
    # predicting the indexes contained on fold_va_indexes array 
    m_pnn_pred <- predict( m , matr_train_data[ fold_va_indexes , ] )
    m_pred <- NULL
    m_pred[m_pnn_pred < 0.5] = HUMAN
    m_pred[m_pnn_pred >= 0.5] = ANIMAL
    
    # computing the performance
    if (CRITERION == "ACCURACY") {
      accuracy(as.factor(matr_class_data[ fold_va_indexes , ]), as.factor(m_pred))
    } else if (CRITERION == "F-MEASURE"){
      f_measure(as.factor(matr_class_data[ fold_va_indexes , ]), as.factor(m_pred))
    }
  } )
  sink()
  
  list(alg = "rvm", comb = attrs, perf = mean(perfs), args = params)
}

###############################################################################################################################################################

build_all_section_models <- function(sections_train_data , active_sections){
  section_models <- list()
  for ( i in active_sections ){
    if (DEBUG) { print(paste("*********** SECTION", i, "******************")) }
    
    # obtaining the training data set that will be used for this section models to be trained
    section_tr_data <- sections_train_data[[ i ]]$data
    
    # generating the cross-validation folds that will be used to estimate the validation performance for this section models
    # number of folds will be ONE TIME FOLDS_NUMBER-Cross Validation
    folds <- TunePareto::generateCVRuns( section_tr_data[ , "CLASS"] , 1 , FOLDS_NUMBER , stratified = TRUE )
    folds <- unlist( folds , recursive = FALSE ) # converting list of list to list of array
    
    # obtaining the vector with the available attributes
    available_attrs <- setdiff(names(section_tr_data), NON_COMBINATIONS_ATTRS)
    
    t0 <- Sys.time()
    if (DEBUG){ cat(paste("Building models (started at ", t0, ")\n")) }      
    
    models <- feature_selection(available_attrs, section_tr_data, folds)
    
    t1 <- Sys.time()
    if (DEBUG){ cat(paste("Finished building models (started at ", t0, ", finished at ", t1, ")\n")) }
    
    # Adding them to the returning list
    section_models[[i]] <- models
  }

  # returning the list that contains the models' signature for each one of the sections
  section_models
}

feature_selection <- function(available_attrs, data, folds) {
  models <- c()
  
  # SLDA building
  if (DEBUG) { cat(paste(">>>SLDA", "\n")) }
  models <- rbind(models, search(available_attrs, data, folds, build_slda))
  
  # SVM with linear, polynomic and radial kernels building
  for (c in SVM_C_VALUES) {
    if (DEBUG) { cat(paste(">>>SVM lin c=", c, "\n")) }
    models <- rbind(models, search(available_attrs, data, folds, build_svm, c, "lin"))
    if (DEBUG) { cat(paste(">>>SVM poly c=", c, "\n")) }
    models <- rbind(models, search(available_attrs, data, folds, build_svm, c, "poly"))
    if (DEBUG) { cat(paste(">>>SVM rbf c=", c, "\n")) }
    models <- rbind(models, search(available_attrs, data, folds, build_svm, c, "rbf"))
  }
  
  # k-NN building
  for (k in NN_K_VALUES){
    if (DEBUG) { cat(paste(">>>kNN k=", k, "\n")) }
    models <- rbind(models, search(available_attrs, data, folds, build_knn, k))
  }
  
  # Logistic Regression building
  if (DEBUG) { cat(paste(">>>LR", "\n")) }
  models <- rbind(models, search(available_attrs, data, folds, build_lr))
  
  
  # Naive Bayes building
  if (DEBUG) { cat(paste(">>>NB", "\n")) }
  models <- rbind(models, search(available_attrs, data, folds, build_nb))
  
  # Probabilistic Neural Network building
  for (sigma in PNN_SIGMA_VALUES){
    if (DEBUG) { cat(paste(">>>PNN sigma=", sigma, "\n")) }
    if(FALSE){ #no prediu bé, retorna NA!
      models <- rbind(models, search(available_attrs, data, folds, build_pnn, sigma))
    }
  }
  
  # RVM with linear, polynomic and radial kernels building
  # only available for two classes
  if (DEBUG) { cat(paste(">>>RVM lin", "\n")) }
  models <- rbind(models, search(available_attrs, data, folds, build_rvm, "lin"))
  if (DEBUG) { cat(paste(">>>RVM poly", "\n")) }
  models <- rbind(models, search(available_attrs, data, folds, build_rvm, "poly"))
  if (DEBUG) { cat(paste(">>>RVM rbf", "\n")) }
  models <- rbind(models, search(available_attrs, data, folds, build_rvm, "rbf"))
  
  # returning list in which resulting models are stored
  models
}

search <- function(available_attrs, data, folds, build, ...) {
  if (SEARCH_ALG == "BLIND_SEARCH") {
    blind_search(available_attrs, data, folds, build, ...)
  } else if (SEARCH_ALG == "FORWARD_SEARCH") {
    forward_search(available_attrs, data, folds, build, ...)
  } else if (SEARCH_ALG == "BACKWARD_SEARCH") {
    backward_search(available_attrs, data, folds, build, ...)
  }
}

blind_search <- function(available_attrs, data, folds, build, ...) {
  for ( num_attrs in MODELS_ATTRS_SIZE_COMBINATIONS ){
    # computing all possible ways of getting num_attrs attributes:
    # all the atributes from current section tr data will be considered except the ones in NON_ATTRS_COMBINATIONS
    current_combs <- combn(available_attrs, num_attrs)
    # building models for each one of the combinations of num_attrs attributes
    models <- c(models, apply(current_combs, MARGIN = 2, build, data, folds, ...))
  }
  models
}

forward_search <- function(available_attrs, data, folds, build, ...) {
  models <- list()
  
  selected_attrs <- NULL
  selected_perfs <- NULL
  
  # Iterating until no available attributes remain
  while (length(available_attrs) > 0) {
    performances <- data.frame(available_attrs, rep(1, length(available_attrs)), stringsAsFactors=FALSE)
    colnames(performances) <- c("attr", "perf")

    attr_models <- NULL
    # Looking for the best attribute to include
    for (attr in available_attrs) {
      attr_models[[attr]] <- build(union(selected_attrs, attr), data, folds, ...)
      performances$perf[performances$attr == attr] <- attr_models[[attr]]$perf
    }
    
    best_perf <- max(performances$perf)
    best_attr <- performances$attr[performances$perf == best_perf]
    
    # Ties are broken at random
    if(length(best_attr) > 1) {
      best_attr <- sample(best_attr, 1)
    }
    
    if ( DEBUG ){ cat( paste( "Chosen attribute", best_attr, "with a performance of", best_perf, "\n")) }
    
    # Updating with the best models
    models[[length(models) + 1]] <- attr_models[[best_attr]]
    
    selected_perfs <- c(selected_perfs, best_perf)
    selected_attrs <- union(selected_attrs, best_attr)
    available_attrs <- setdiff(available_attrs, best_attr)
  }

  models
}

backward_search <- function(available_attrs, data, folds) {
  remaining_attrs <- available_attrs
  discarded_attrs <- NULL
  discarded_perfs <- NULL
  
  # Iterating until no available attributes remain
  while (length(remaining_attrs) > 2) {
    performances <- data.frame(remaining_attrs, rep(1, length(remaining_attrs)))
    colnames(performances) <- c("attr", "perf")
    
    attr_models <- NULL
    # Looking for the best attribute to exclude
    for (attr in remaining_attrs) {
      attr_models[[attr]] <- build_models(setdiff(remaining_attrs, attr), data, folds)
      performances$perf[performances$attr == attr] <- max(unlist(attr_models[[attr]][, "perf"]))
    }
    best_perf <- max(performances$perf)
    best_attr <- performances$attr[performances$perf == best_perf]
    
    # Ties are broken at random
    if(length(best_attr) > 1) {
      best_attr <- sample(best_attr, 1)
    }
    
    if ( DEBUG ){ cat( paste( "Chosen attribute", best_attr, "with a performance of", best_perf, "\n")) }
    
    # Updating with the best models
    models[[length(models) + 1]] <- attr_models[[best_attr]]
    
    discarded_perfs <- c(discarded_perfs, best_perf)
    discarded_attrs <- union(discarded_attrs, best_attr)
    remaining_attrs <- setdiff(remaining_attrs, best_attr)
  }
  
  # Adding the final two
  discarded_perfs <- c(discarded_perfs, performances$perf[performances$attr != best_attr])
  discarded_attrs <- union(discarded_attrs, performances$attr[performances$attr != best_attr])
  
}

###############################################################################################################################################################

#**************************************************************************************************************************************************************
# deprecated!
##### FUNCTION: 
##### build_all_section_models( sections_train_data , active_sections )  :  list of matrices
##### Builds all the models for the active_sections using the i-th train dataset from sections_train_data list.
##### Returns a list in which each element is a matrix in which each row contains: model algorithm , attribute combination , model performance , model parameters.
##### The indexes of the returned list are the number of the sections, the ones contained on list active_sections
##### Params:
#####   - sections_train_data: list of data.frames, each element i is the training dataset for section i
#####	- train_data: data.frame that contains training data
deprecated_build_all_section_models <- function( sections_train_data , active_sections ){
  section_models <- list()
  # for all the sections
  for ( i in active_sections ){
    if ( DEBUG ){ print( paste( "*********** SECTION" , i , "******************" ) ) }
    # obtaining the training data set that will be used for this section models to be trained
    section_tr_data <- sections_train_data[[ i ]]$data
    # generating the cross-validation folds that will be used to estimate the validation performance for this section models
    # number of folds will be ONE TIME FOLDS_NUMBER-Cross Validation
    folds <- TunePareto::generateCVRuns( section_tr_data[ , "CLASS"] , 1 , FOLDS_NUMBER , stratified = TRUE )
    folds <- unlist( folds , recursive = FALSE ) # converting list of list to list of array
    
    if (SEARCH_ALG == "BLIND_SEARCH") {
      # Blind search
      for ( num_attrs in MODELS_ATTRS_SIZE_COMBINATIONS ){
        # computing all possible ways of getting num_attrs attributes:
        # all the atributes from current section tr data will be considered except the ones in NON_ATTRS_COMBINATIONS
        current_combs <- combn( setdiff( names( section_tr_data ) , NON_COMBINATIONS_ATTRS ) , num_attrs  )
        # building models for each one of the combinations of num_attrs attributes
        if ( DEBUG ){ print( paste( "Building" , dim( current_combs )[2] , "models of" , num_attrs , "attributes ( started at" , Sys.time() , ")" ) ) }
        models <- c( models , apply( current_combs , MARGIN = 2 , build_models , section_tr_data , folds ) )
      }
    } else if (SEARCH_ALG == "FORWARD_SEARCH") {
      # Forward search      
      t0 <- Sys.time()
      if ( DEBUG ){ cat( paste( "Building models with forward search (started at " , t0 , ")\n" ) ) }
      available_attrs <- setdiff(setdiff(names(section_tr_data), NON_COMBINATIONS_ATTRS), "FE")
      
      models <- forward_search(available_attrs, section_tr_data, folds)
      
      t1 <- Sys.time()
      if ( DEBUG ){ cat( paste( "Finished building models with forward search (started at " , t0, ", finished at ", t1, ")\n" ) ) }
    } else if (SEARCH_ALG == "BACKWARD_SEARCH") {
      # Backward search
      t0 <- Sys.time()
      if ( DEBUG ){ cat( paste( "Building models with backward search (started at " , t0 , ")\n" ) ) }
      available_attrs <- setdiff(names(section_tr_data), NON_COMBINATIONS_ATTRS)
      
      models <- backward_search(available_attrs, section_tr_data, folds)
      
      t1 <- Sys.time()
      if ( DEBUG ){ cat( paste( "Finished building models with backward search (started at " , t0, ", finished at ", t1, ")\n" ) ) }
    }
    
    # Building a matrix with all the model signatures for the current section
    do.call( rbind , models )
    # Adding them to the returning list
    section_models[[ length( section_models ) + 1 ]] <- models
  }
  # returning the list that contains the models' signature for each one of the sections
  section_models
}

#**************************************************************************************************************************************************************
# deprecated?

##### FUNCTION: 
##### build_model_from_signature( signature , train_data ,  folds )  :  matrix
##### Builds a single model according to signature
##### Returns a matrix in which each row contains: model algorithm , attribute combination , model error , model parameters.
##### IMPORTANT! The build model is not returned, just its "signature"
##### Params:
#####   - signature: signature of the model to be build, contains: model algorithm , attribute combination , model error , model parameters.
#####	- train_data: data.frame that contains training data
#####	- folds: list of array in which each of the elements is the fold (array of row indices) that will be used to compute the validation error; of course,
#####			the other indices not contained in each fold will be the ones used to train the model in that particular fold.
deprecated__build_model_from_signature <- function( signature , train_data ,  folds ){
  # matrix in which resulting model will be stored, this is the object to be returned
  models_matrix <- c()
  
  # SLDA building
  ##############################################################################
  if ( signature$alg == "slda" ){
    if ( DEBUG ){ cat( "SLDA\n") }
    errs <- sapply( folds , function ( fold_va_indexes ){ 
      m <- ipred::slda( y = train_data[ -fold_va_indexes , "CLASS" ] , X = train_data[ -fold_va_indexes , signature$comb ] , CV = FALSE ) 
      m_pred <- custom_predict_slda( m , train_data[ fold_va_indexes , signature$comb ] )$class
      sum( m_pred != train_data[ fold_va_indexes , "CLASS" ] ) / length( m_pred )
    } )
    models_matrix <- rbind( models_matrix , list( 	alg = "slda" , comb = signature$comb , err = mean( errs ) , args = list() ) )	
  } 
  ##############################################################################
  
  # SVM building
  ##############################################################################
  else if ( signature$alg == "svm" ){
    if ( DEBUG ){ cat( "SVM," , unlist( signature$args ) , "\n" ) }
    # converting training data to a matrix since it is required by SVM methods
    matr_train_data <- as.matrix( train_data[ , signature$comb ] )
    matr_class_data <- as.matrix( train_data[ , "CLASS" ] )
    
    # building SVM model according to kernel and the associated parameters
    if ( signature$args[[ 1 ]] == "lin" ){
      errs <- sapply( folds , function ( fold_va_indexes ){ 
        # building model using the indexes NOT contained on fold_va_indexes array 
        m <- kernlab::ksvm( matr_train_data[ -fold_va_indexes , ] , matr_class_data[ -fold_va_indexes , ] , kernel = "polydot" , 
                            kpar = list( degree = 1 , scale = 1 , offset = 1 ) , C = signature$args[[ 2 ]] , scaled = FALSE )
        # computing the current va fold error predicting the indexes contained on fold_va_indexes array 
        m_pred <- predict( m , matr_train_data[ fold_va_indexes , ] )
        sum( m_pred != matr_class_data[ fold_va_indexes , ] ) / length( m_pred )
      } )
      # VA error will be the mean of the errors for each validation fold
      models_matrix <- rbind( models_matrix , list( alg = "svm" , comb = signature$comb , err = mean( errs ) , 
                                                    args = list( "lin" , signature$args[[ 2 ]] ) ) )
    } else if ( signature$args[[ 1 ]] == "poly" ){
      errs <- sapply( folds , function ( fold_va_indexes ){
        # building model using the indexes NOT contained on fold_va_indexes array 
        m <- kernlab::ksvm( matr_train_data[ -fold_va_indexes , ] , matr_class_data[ -fold_va_indexes , ] , kernel = "polydot" , 
                            kpar = list( degree = 2 ) , C = signature$args[[ 2 ]] , scaled = FALSE )
        # computing the current va fold error predicting the indexes contained on fold_va_indexes array 
        m_pred <- predict( m , matr_train_data[ fold_va_indexes , ] )
        sum( m_pred != matr_class_data[ fold_va_indexes , ] ) / length( m_pred )
      } )
      # VA error will be the mean of the errors for each validation fold
      models_matrix <- rbind( models_matrix , list( alg = "svm" , comb = signature$comb , err = mean( errs ) , 
                                                    args = list( "poly" , signature$args[[ 2 ]] ) ) )
    } else if ( signature$args[[ 1 ]] == "rbf" ){
      errs <- sapply( folds , function ( fold_va_indexes ){
        # building model using the indexes NOT contained on fold_va_indexes array 
        m <- kernlab::ksvm(	matr_train_data[ -fold_va_indexes , ] , matr_class_data[ -fold_va_indexes , ] , kernel = "rbfdot" , 
                            kpar = signature$args[[ 3 ]] , C = signature$args[[ 2 ]] , scaled = FALSE )
        # computing the current va fold error predicting the indexes contained on fold_va_indexes array 
        m_pred <- predict( m , matr_train_data[ fold_va_indexes , ] )
        sum( m_pred != matr_class_data[ fold_va_indexes , ] ) / length( m_pred )
      } )
      # VA error will be the mean of the errors for each validation fold
      models_matrix <- rbind( models_matrix , list( alg = "svm" , comb = signature$comb , err = mean( errs ) , 
                                                    args = list( "rbf" , signature$args[[ 2 ]]  , signature$args[[ 3 ]]  ) ) )
    }
  } 
  ##############################################################################
  
  # KNN building
  ##############################################################################
  else if ( signature$alg == "knn" ){
    if ( DEBUG ){ cat( "k-NN, k=" , signature$args[[ 1 ]] , "\n") }
    errs <- sapply( folds , function ( fold_va_indexes ){
      # building model using the indexes NOT contained on fold_va_indexes array 
      m <- class::knn( train = train_data[ -fold_va_indexes , signature$comb  ] , test = train_data[ fold_va_indexes , signature$comb  ] , 
                       cl = train_data[ -fold_va_indexes , "CLASS" ] , k = signature$args[[ 1 ]] , prob = FALSE )
      # computing the current va fold error predicting the indexes contained on fold_va_indexes array 
      sum( m != train_data[ fold_va_indexes , "CLASS" ] ) / length( fold_va_indexes )
    } )
    # VA error will be the mean of the errors for each validation fold
    models_matrix <- rbind( models_matrix , list( alg = "knn" , comb = signature$comb , err = mean( errs ) , args = list( signature$args[[ 1 ]] ) ) )
  } 
  ##############################################################################
  
  # LR building
  ##############################################################################
  else if ( signature$alg == "lr" ){
    if ( DEBUG ){ cat( "LR \n") }
    errs <- sapply( folds , function ( fold_va_indexes ){
      # building model using the indexes NOT contained on fold_va_indexes array 
      m_lr <- stats::glm( CLASS ~ . , data = train_data[ -fold_va_indexes , c( signature$comb , "CLASS" ) ] , family = binomial )
      # computing the current va fold error predicting the indexes contained on fold_va_indexes array 
      m_lr_pred <- predict( m_lr , newdata = train_data[ fold_va_indexes , c( signature$comb , "CLASS" ) ] ) 
      pt <- 1 - ( 1 / ( 1 + exp( m_lr_pred ) ) )
      glfpredt <- NULL
      glfpredt[ pt < 0.5 ] <- ANIMAL
      glfpredt[ pt >= 0.5 ] <- HUMAN
      sum( glfpredt != train_data[ fold_va_indexes , "CLASS" ] ) / length( fold_va_indexes )
    } )
    # VA error will be the mean of the errors for each validation fold
    models_matrix <- rbind( models_matrix , list( alg = "lr" , comb = signature$comb , err = mean( errs ) , args = list() ) )
  }
  ##############################################################################
  
  models_matrix
}
###############################################################################################################################################################

deprecated__build_models_from_signatures <- function( model_signatures , training_data ){
  models <- list()
  folds_10 <- TunePareto::generateCVRuns( training_data[ , "CLASS"] , 10 , 10 , stratified = TRUE )
  folds_10 <- unlist( folds_10 , recursive = FALSE ) # converting list of list to list of array
  models <- c( models , apply( model_signatures , MARGIN = 1 , build_model_from_signature , training_data , folds_10 ) )
  do.call( rbind , models )
}