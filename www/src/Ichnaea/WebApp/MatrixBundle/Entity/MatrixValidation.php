<?php 
namespace Ichnaea\WebApp\MatrixBundle\Entity;

use Ichnaea\WebApp\MatrixBundle\Entity\Matrix as Matrix;

class MatrixValidation
{
	private $matrix;

	private $errors = array();
	
	const ERROR_SAMPLE_ORIGIN_EMPTY = 0;
	const ERROR_SAMPLE_DATE_EMPTY   = 1;
	const ERROR_SAMPLE_NAME_EMPTY   = 2;
	
	private $map_errors_to_string = array(
		self::ERROR_SAMPLE_ORIGIN_EMPTY => 'Sample %s(row %d) has no origin setted',
		self::ERROR_SAMPLE_DATE_EMPTY => 'Sample %s(row %d) has no date setted',
		self::ERROR_SAMPLE_NAME_EMPTY => 'Row %s no name setted'
	);
	
	public function __construct($matrix)
	{
		$this->matrix = $matrix;
	}
	
	public function validate()
	{
		//Browse all samples for
		$samples = $this->matrix->getRows();
		$row = 1;
		foreach ($samples as $sample)
		{
			if ($sample->getOrigin() == '') $this->pushError(self::ERROR_SAMPLE_ORIGIN_EMPTY, array($sample->getName(), $row));
			if ($sample->getDate() == '') $this->pushError(self::ERROR_SAMPLE_DATE_EMPTY, array($sample->getName(), $row));
			if ($sample->getName() == '') $this->pushError(self::ERROR_SAMPLE_NAME_EMPTY, array($row));
			$row++;
		}
	}
	
	public function getErrorsAsArrayOfStrings()
	{
		return $this->errors;
	}
	
	private function pushError($error, $params)
	{
		array_push($this->errors, vsprintf($this->map_errors_to_string[$error], $params) );
	}
	
}
?>
