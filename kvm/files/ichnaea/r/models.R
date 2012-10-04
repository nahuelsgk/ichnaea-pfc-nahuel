###############################################################################################################################################################
# MST - models.R
# Description: Provides methods to build different model types for each section and combining systematically any desired number of variables
###############################################################################################################################################################

source( "constants.R" )

library( ipred )
library( kernlab )
library( class )
library( boot )
library( TunePareto )

custom_predict_slda <- function( object, newdata, ... ) {
	X <- as.matrix( newdata ) %*% object$scores
	predict( object$mylda , as.data.frame(X) )
}

##### FUNCTION: 
##### build_models( attrs , train_data , folds )  :  matrix
##### Builds the different models using data.frame train_data as training data and attributes attrs as explanatory variables.
##### Returns a matrix in which each row contains: model algorithm , attribute combination , model error , model parameters.
##### IMPORTANT! The build model is not returned, just its "signature"
##### Params:
##### 	- attrs: attributes from data.frame train_data that will be used as explanatory variables
#####	- train_data: data.frame that contains training data
#####	- folds: list of array in which each of the elements (indexed by attribute name) are the rows corresponding to a particular folds to compute CV error
build_models <- function( attrs , train_data , folds ){	
	# matrix in which resulting models will be stored, this is the object to be returned
	models_matrix <- c()
	
	if ( DEBUG ){ cat( "******************************************************\n" ) }
	if ( DEBUG ){ cat( "Building models for attribute set" , attrs , "\n" ) }
	
	# SLDA building
	##############################################################################
	if ( DEBUG ){ cat( "SLDA\n") }
	errs <- sapply( folds , function ( fold_va_indexes ){ 
				m <- ipred::slda( y = train_data[ -fold_va_indexes , "CLASS" ] , X = train_data[ -fold_va_indexes , attrs ] , CV = FALSE ) 
				m_pred <- custom_predict_slda( m , train_data[ fold_va_indexes , attrs ] )$class
				sum( m_pred != train_data[ fold_va_indexes , "CLASS" ] ) / length( m_pred )
			} )
	models_matrix <- rbind( models_matrix , list( 	alg = "slda" , comb = attrs , err = mean( errs ) , args = list() ) )
	##############################################################################
	
	# SVM with linear, polynomic and radial kernels building
	##############################################################################
	# converting training data to a matrix since it is required by SVM methods
	matr_train_data <- as.matrix( train_data[ , attrs ] )
	matr_class_data <- as.matrix( train_data[ , "CLASS" ] )
	# estimating sigma value for RBF kernel SVM
	kpar_rbf_kern <- list( sigma = mean( sigest( matr_train_data , scaled = FALSE )[ c( 1 , 3 ) ] ) )
	# for each one of the available cost parameter values to be optimized
	for ( c in SVM_C_VALUES ){ 
		# linear kernel
		# applying function for each one of the folds, errs will be an array with the error of each fold
		# current fold will be considered the validation fold, hence the name fold_va_indexes 
		if ( DEBUG ){ cat( "SVM, linear kernel, C=" , c , "\n") }
		errs <- sapply( folds , function ( fold_va_indexes ){ 
					# building model using the indexes NOT contained on fold_va_indexes array 
					m <- kernlab::ksvm( matr_train_data[ -fold_va_indexes , ] , matr_class_data[ -fold_va_indexes , ] , kernel = "polydot" , 
										kpar = list( degree = 1 , scale = 1 , offset = 1 ) , C = c , scale = FALSE )
					# computing the current va fold error predicting the indexes contained on fold_va_indexes array 
					m_pred <- predict( m , matr_train_data[ fold_va_indexes , ] )
					sum( m_pred != matr_class_data[ fold_va_indexes , ] ) / length( m_pred )
				} )
		# VA error will be the mean of the errors for each validation fold
		models_matrix <- rbind( models_matrix , list( alg = "svm" , comb = attrs , err = mean( errs ) , args = list( "lin" , c ) ) )
	
		# polynomial degree 2 kernel
		# applying function for each one of the folds, errs will be an array with the error of each fold
		# current fold will be considered the validation fold, hence the name fold_va_indexes 
		if ( DEBUG ){ cat( "SVM, poly 2-deg kernel, C=" , c , "\n") }
		errs <- sapply( folds , function ( fold_va_indexes ){
					# building model using the indexes NOT contained on fold_va_indexes array 
					m <- kernlab::ksvm( matr_train_data[ -fold_va_indexes , ] , matr_class_data[ -fold_va_indexes , ] , kernel = "polydot" , 
										kpar = list( degree = 2 ) , C = c , scale = FALSE )
					# computing the current va fold error predicting the indexes contained on fold_va_indexes array 
					m_pred <- predict( m , matr_train_data[ fold_va_indexes , ] )
					sum( m_pred != matr_class_data[ fold_va_indexes , ] ) / length( m_pred )
				} )
		# VA error will be the mean of the errors for each validation fold
		models_matrix <- rbind( models_matrix , list( alg = "svm" , comb = attrs , err = mean( errs ) , args = list( "poly" , c ) ) )
	
		# rbf kernel
		# applying function for each one of the folds, errs will be an array with the error of each fold
		# current fold will be considered the validation fold, hence the name fold_va_indexes 
		if ( DEBUG ){ cat( "SVM, rbf kernel, C=" , c , "\n") }
		errs <- sapply( folds , function ( fold_va_indexes ){
					# building model using the indexes NOT contained on fold_va_indexes array 
					m <- kernlab::ksvm(	matr_train_data[ -fold_va_indexes , ] , matr_class_data[ -fold_va_indexes , ] , kernel = "rbfdot" , 
										kpar = kpar_rbf_kern , C = c , scale = FALSE )
					# computing the current va fold error predicting the indexes contained on fold_va_indexes array 
					m_pred <- predict( m , matr_train_data[ fold_va_indexes , ] )
					sum( m_pred != matr_class_data[ fold_va_indexes , ] ) / length( m_pred )
				} )
		# VA error will be the mean of the errors for each validation fold
		models_matrix <- rbind( models_matrix , list( alg = "svm" , comb = attrs , err = mean( errs ) , args = list( "rbf" , c , kpar_rbf_kern ) ) )
	}
	##############################################################################
	
	# k-NN building
	##############################################################################
	# for each one of the k parameter values to be optimized
	for ( k in NN_K_VALUES ){ 
		# applying function for each one of the folds, errs will be an array with the error of each fold
		# current fold will be considered the validation fold, hence the name fold_va_indexes 
		if ( DEBUG ){ cat( "k-NN, k=" , k , "\n") }
		errs <- sapply( folds , function ( fold_va_indexes ){
					# building model using the indexes NOT contained on fold_va_indexes array 
					m <- class::knn( train = train_data[ -fold_va_indexes , attrs ] , test = train_data[ fold_va_indexes , attrs ] , 
										cl = train_data[ -fold_va_indexes , "CLASS" ] , k = k , prob = FALSE )
					# computing the current va fold error predicting the indexes contained on fold_va_indexes array 
					sum( m != train_data[ fold_va_indexes , "CLASS" ] ) / length( fold_va_indexes )
				} )
		# VA error will be the mean of the errors for each validation fold
		models_matrix <- rbind( models_matrix , list( alg = "knn" , comb = attrs , err = mean( errs ) , args = list( k ) ) )
	}
	##############################################################################
	
	# logistic regression building
	##############################################################################
	# applying function for each one of the folds, errs will be an array with the error of each fold
	# current fold will be considered the validation fold, hence the name fold_va_indexes 
	if ( DEBUG ){ cat( "LR \n") }
	errs <- sapply( folds , function ( fold_va_indexes ){
				# building model using the indexes NOT contained on fold_va_indexes array 
				m_lr <- stats::glm( CLASS ~ . , data = train_data[ -fold_va_indexes , c( attrs , "CLASS" ) ] , family = binomial )
				# computing the current va fold error predicting the indexes contained on fold_va_indexes array 
				m_lr_pred = predict( m_lr , newdata = train_data[ fold_va_indexes , c( attrs , "CLASS" ) ] ) 
				pt = 1 - ( 1 / ( 1 + exp( m_lr_pred ) ) )
				glfpredt = NULL
				glfpredt[ pt < 0.5 ] = ANIMAL
				glfpredt[ pt >= 0.5 ] = HUMAN
				sum( glfpredt != train_data[ fold_va_indexes , "CLASS" ] ) / length( fold_va_indexes )
			} )
	# VA error will be the mean of the errors for each validation fold
	models_matrix <- rbind( models_matrix , list( alg = "lr" , comb = attrs , err = mean( errs ) , args = list() ) )
	##############################################################################
	
	# returning matrix in which resulting models are stored
	models_matrix
}
###############################################################################################################################################################

##### FUNCTION: 
##### build_model_from_signature( signature , train_data ,  folds )  :  matrix
##### Builds a single model according to signature
##### Returns a matrix in which each row contains: model algorithm , attribute combination , model error , model parameters.
##### IMPORTANT! The build model is not returned, just its "signature"
##### Params:
##### 	- signature: signature of the model to be build, contains: model algorithm , attribute combination , model error , model parameters.
#####	- train_data: data.frame that contains training data
#####	- folds: list of array in which each of the elements is the fold (array of row indices) that will be used to compute the validation error; of course,
#####			the other indices not contained in each fold will be the ones used to train the model in that particular fold.
build_model_from_signature <- function( signature , train_data ,  folds ){
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
								kpar = list( degree = 1 , scale = 1 , offset = 1 ) , C = signature$args[[ 2 ]] , scale = FALSE )
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
								kpar = list( degree = 2 ) , C = signature$args[[ 2 ]] , scale = FALSE )
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
								kpar = signature$args[[ 3 ]] , C = signature$args[[ 2 ]] , scale = FALSE )
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

build_models_from_signatures <- function( model_signatures , training_data ){
	models <- list()
	folds_10 <- TunePareto::generateCVRuns( training_data[ , "CLASS"] , 10 , 10 , stratified = TRUE )
	folds_10 <- unlist( folds_10 , recursive = FALSE ) # converting list of list to list of array
	models <- c( models , apply( model_signatures , MARGIN = 1 , build_model_from_signature , training_data , folds_10 ) )
	do.call( rbind , models )
}

##### FUNCTION: 
##### build_all_section_models( sections_train_data , active_sections )  :  list of matrices
##### Builds all the models for the active_sections using the i-th train dataset from sections_train_data list.
##### Returns a list in which each element is a matrix in which each row contains: model algorithm , attribute combination , model error , model parameters.
##### The indexes of the returned list are the number of the sections, the ones contained on list active_sections
##### Params:
##### 	- sections_train_data: list of data.frames, each element i is the training dataset for section i
#####	- train_data: data.frame that contains training data
build_all_section_models <- function( sections_train_data , active_sections ){
	section_models <- list()
	# for all the sections
	for ( i in active_sections ){
		if ( DEBUG ){ print( paste( "*********** SECTION" , i , "******************" ) ) }
		# initializing the list in which the current section models will be stored
		models <- list()
		# obtaining the trainind data set that will be used for this section models to be trained
		section_tr_data <- sections_train_data[[ i ]]$data
		# generating the cross-validation folds that will be used to estimate the validation error for this section models
		# number of folds will be ONE TIME FOLDS_NUMBER-Cross Validation
		folds <- TunePareto::generateCVRuns( section_tr_data[ , "CLASS"] , 1 , FOLDS_NUMBER , stratified = TRUE )
		folds <- unlist( folds , recursive = FALSE ) # converting list of list to list of array
		# for all the desired lengths of the attributes size
		for ( num_attrs in MODELS_ATTRS_SIZE_COMBINATIONS ){
			# computing all possible ways of getting num_attrs attributes:
			# all the atributes from current section tr data will be considered except the ones in NON_ATTRS_COMBINATIONS
			current_combs <- combn( setdiff( names( section_tr_data ) , NON_COMBINATIONS_ATTRS ) , num_attrs  )
			# building models for each one of the combinations of num_attrs attributes
			if ( DEBUG ){ print( paste( "Building" , dim( current_combs )[2] , "models of" , num_attrs , "attributes ( started at" , Sys.time() , ")" ) ) }
			models <- c( models , apply( current_combs , MARGIN = 2 , build_models , section_tr_data , folds ) )
		}
		# building a matrix with all the model signatures for the current section
		do.call( rbind , models )
		# adding them to the returning list
		section_models[[ length( section_models ) + 1 ]] <- models
		if ( DEBUG ){ print( "**********************************************" ) }
	}
	# returning the list that contains the models' signature for each one of the sections
	section_models
}
###############################################################################################################################################################