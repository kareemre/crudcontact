<?php 

class Validation
{

    
    /**
     * db instance
     *
     * @var mixed
     */
    private $mySqlQueryBuilder;
    
    /**
     * errors container
     *
     * @var array
     */
    private $errors = [];
    

    public function __construct(MySqlQueryBuilder $mySqlQueryBuilder)
    {
        $this->mySqlQueryBuilder = $mySqlQueryBuilder;
    }

    /**
     * Determine if the given input is unique in database
     *
     * @param string $inputName
     * @param array $databaseData
     * @param string $customErrorMessage
     * @return 
     */
    public function unique($inputName, array $databaseData, $customErrorMessage = null)
    {
        if ($this->hasErrors($inputName)) {
            return $this;
        }

        $inputValue = $this->value($inputName);
        $table = null;
        $column = null;

        list($table, $column) = $databaseData; 

        $result = $this->mySqlQueryBuilder->select($column)
                                          ->from($table)
                                          ->where($column . ' = ?' , $inputValue)
                                          ->get();

        if ($result) {
            $message = $customErrorMessage ?: sprintf('%s already exists', ucfirst($inputName));
            $this->addError($inputName, $message);
        }
    }


    /**
     * Determine if the given input is not empty
     *
     * @param string $inputName
     * @param string $customErrorMessage
     * @return $this
     */
    public function required($inputName, $customErrorMessage = null)
    {
        if ($this->hasErrors($inputName)) {
            return $this;
        }

        $inputValue = $this->value($inputName);

        if ($inputValue === '') {
            $message = $customErrorMessage ?: sprintf('%s Is Required', ucfirst($inputName));
            $this->addError($inputName, $message);
        }

        return $this;
    }

    // public function sanitizeInput($input)
    // {
    //     $inputValue = $this->value($input);
    //     htmlspecialchars(trim($inputValue), ENT_QUOTES, 'UTF-8');
    //     return $this;
    // }


    /**
     * Determine if the given input is valid email
     *
     * @param string $inputName
     * @param string $customErrorMessage
     * @return $this
     */
    public function email($inputName, $customErrorMessage = null)
    {
        if ($this->hasErrors($inputName)) {
            return $this;
        }

        $inputValue = $this->value($inputName);

        if (! filter_var($inputValue, FILTER_VALIDATE_EMAIL)) {
            $message = $customErrorMessage ?: sprintf('%s is not valid email', ucfirst($inputName));
            $this->addError($inputName, $message);
        }

        return $this;
    }


    /**
     * Determine if the given input has previous errors
     *
     * @param string $inputName
     */
    private function hasErrors($inputName)
    {
        return array_key_exists($inputName, $this->errors);
    }

    /**
     * Add input error
     *
     * @param string $inputName
     * @param string $errorMessage
     * @return void
     */
    private function addError($inputName, $errorMessage)
    {
        $this->errors[$inputName] = $errorMessage;
    }
    
    /**
     * get the value of of the given input 
     *
     * @param  mixed $inputName
     * @return string
     */
    private function value($input)
    {
        return $_POST[$input];
    }

    /**
     * Determine if all inputs are valid
     *
     * @return bool
     */
    public function passes()
    {
        return empty($this->errors);
    }

    /**
     * Determine if there are any invalid inputs
     *
     * @return bool
     */
    public function fails()
    {
        return !empty($this->errors);
    }

    /**
     * Get All errors
     *
     * @return array
     */
    public function getMessages()
    {
        return $this->errors;
    }
}