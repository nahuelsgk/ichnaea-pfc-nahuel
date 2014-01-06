<?php
namespace Ichnaea\WebApp\TrainingBundle\Model;

use Ichnaea\WebApp\TrainingBundle\Entity\Training;
use Ichnaea\WebApp\MatrixBundle\Entity\Matrix;
/*
 * This class manage the validation of a training. 
 * Will apply a set of rules
 * */
class TrainingValidation 
{
	/*
	 * The set of possible errors that a training can have
	 */
	const ERROR_MINIMUM_VARIABLE_SET                     = 0;
	const ERROR_MAXIMUM_VARIABLE_SET                     = 1;
	const ERROR_MINIMUM_VARIABLE_SET_BIGGER_THAN_MAXIMUM = 2;
	const ERROR_MAXIMUM_VARIABLE_SET_BIGGER_THAN_MATRIX  = 3;
	const ERROR_MIMIMUM_VARIABLE_SET_BIGGER_THAN_MATRIX  = 4;
	
	const STATUS_OK   = 1;
	const STATUS_FAIL = 0;
	/*
	 * The mapping errors description
	 */
	private $string_errors = array (
	self::ERROR_MINIMUM_VARIABLE_SET                     => "Minimum value not valid",
	self::ERROR_MAXIMUM_VARIABLE_SET                     => "Maximim value not valid",
	self::ERROR_MINIMUM_VARIABLE_SET_BIGGER_THAN_MAXIMUM => "Minimum is bigger than maximum",
	self::ERROR_MAXIMUM_VARIABLE_SET_BIGGER_THAN_MATRIX  => "Maximum is bigger than matrix",
	self::ERROR_MIMIMUM_VARIABLE_SET_BIGGER_THAN_MATRIX  => "Minimum is bigger than matrix",
		
	);
	
	private $errors;
	
	private $status;
	
	private $training;
	
	public function __construct(Training $training)
	{
		$this->training = $training;
		$this->errors   = array();
		
	}
	
	public function setTraining($training)
	{
		$this->training = $training;
	}
	
	public function getTraining()
	{
		return $this->training;
	}
	
	public function validate()
	{
		$this->validateVariableSet();
	}
	
	public function valid()
	{
		if (empty($this->errors)) return true;
		return false;
	}
	
	public function getErrorsAsArrayOfStrings()
	{
		$map_array = array();
		foreach ($this->errors as $key => $value)
		{
			$map_array[] = $this->string_errors[$key];
		}
		return $map_array;
	}
	/*
	 * Validates the minimum and the maximum size of the set of variables
	 */
	private function validateVariableSet()
	{	
		$matrix  = $this->training->getMatrix();
		$columns = $matrix->getColumns();
		$size_m  = $columns->count();
		$minimum = $this->training->getMinSizeVariableSet();
		$maximum = $this->training->getMaxSizeVariableSet();
		
		//Validate a set of rules
		if ($minimum <= 0)       $this->pushToErrors(self::ERROR_MINIMUM_VARIABLE_SET);
		if ($maximum <= 0)       $this->pushToErrors(self::ERROR_MAXIMUM_VARIABLE_SET);
		if ($minimum > $maximum) $this->pushToErrors(self::ERROR_MINIMUM_VARIABLE_SET_BIGGER_THAN_MAXIMUM);
		if ($maximum > $size_m)  $this->pushToErrors(self::ERROR_MAXIMUM_VARIABLE_SET_BIGGER_THAN_MATRIX);
		if ($minimum > $size_m)  $this->pushToErrors(self::ERROR_MIMIMUM_VARIABLE_SET_BIGGER_THAN_MATRIX);
		
	}
	
	private function pushToErrors($error)
	{
		$this->errors[$error] = 1;
	}
}
?>