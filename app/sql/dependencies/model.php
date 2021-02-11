<?php
require_once __DIR__."/manager.php";

class Model {
    private $SQLM;
    private $tablename;

    function __construct($tablename) {
        $this->SQLM = new SQLManager();
        $this->tablename = $tablename;
    }

    private $attributes = [
        "mode" => "",
        "value" => "",
        "distinct" => false,
        "where" => [],
        "limit" => 0,
        "order_column" => "",
        "order" => "",
        "columns" => [],
        "values" => []
    ];

    /**
     * Executes a SQL Request.
     */
    public function execute() {
        $mode = $this->attributes["mode"];
        switch ($mode) {
            case 'D':
                $query = "DELETE FROM $this->tablename";
                
                $where = $this->attributes["where"];
                if ($where != []) {
                    for ($i = 0; $i < count($where); $i++) { 
                        $key = $where[$i]["key"];
                        $operator = $where[$i]["operator"];
                        $value = $where[$i]["value"];
                        if ($i == 0) {
                            if (gettype($value) == "string") {
                                $query .= " WHERE $key$operator'$value'";
                            } else {
                                $query .= " WHERE $key$operator$value";
                            }
                            
                        } else {
                            if (gettype($value) == "string") {
                                $query .= " AND $key$operator'$value'";
                            } else {
                                $query .= " AND $key$operator $value";
                            }
                        }
                    }
                }
                return $this->SQLM->query($query);
                break;

            case 'I':
                $query = "INSERT INTO $this->tablename ";
                
                $columns = $this->attributes["columns"];
                $values = $this->attributes["values"];

                $query .= "(".implode(", ", $columns).")";
                $query .= " VALUES ";
                
                for ($i=0; $i < count($values); $i++) { 
                    $value = $values[$i];
                    $values[$i] = str_replace("'", "\\'", $value);
                    if (gettype($value) == "string") {
                        $values[$i] = "'$value'";
                    }
                }
                $query .= "(".implode(", ", $values).")";

                return $this->SQLM->query($query);
                break;

            case 'S':
                $query = "";
                $value = $this->attributes["value"];
                $distinct = $this->attributes["distinct"];
                if ($distinct) {
                    $query = "SELECT DISTINCT $value FROM $this->tablename";
                } else {
                    $query = "SELECT $value FROM $this->tablename";
                }

                $where = $this->attributes["where"];
                if ($where != []) {
                    for ($i = 0; $i < count($where); $i++) { 
                        $key = $where[$i]["key"];
                        $operator = $where[$i]["operator"];
                        $value = $where[$i]["value"];
                        if ($i == 0) {
                            if (gettype($value) == "string") {
                                $query .= " WHERE $key$operator'$value'";
                            } else {
                                $query .= " WHERE $key$operator$value";
                            }
                            
                        } else {
                            if (gettype($value) == "string") {
                                $query .= " AND $key$operator'$value'";
                            } else {
                                $query .= " AND $key$operator $value";
                            }
                        }
                    }
                }

                $limit = $this->attributes["limit"];
                if ($limit > 0) {
                    $query .= " LIMIT $limit";
                }

                $order_c = $this->attributes["order_column"];
                $order = $this->attributes["order"];
                if ($order_c && $order) {
                    $query .= " ORDER BY $order_c $order";
                }
                return $this->SQLM->query($query);

                break;

            case 'U':
                $elements = [];
                $columns = $this->attributes["columns"];
                $values = $this->attributes["values"];

                for ($i=0; $i < count($columns); $i++) { 
                    $column = $columns[$i];
                    $value = $values[$i];
                    if (gettype($value) == "string") {
                        array_push($elements, "$column = '$value'");
                    } else {
                        array_push($elements, "$column = $value");
                    }
                }
                $query = "UPDATE $this->tablename SET ";
                $query .= implode(", ", $elements);

                $where = $this->attributes["where"];
                if ($where != []) {
                    for ($i = 0; $i < count($where); $i++) { 
                        $key = $where[$i]["key"];
                        $operator = $where[$i]["operator"];
                        $value = $where[$i]["value"];
                        if ($i == 0) {
                            if (gettype($value) == "string") {
                                $query .= " WHERE $key$operator'$value'";
                            } else {
                                $query .= " WHERE $key$operator$value";
                            }
                            
                        } else {
                            if (gettype($value) == "string") {
                                $query .= " AND $key$operator'$value'";
                            } else {
                                $query .= " AND $key$operator $value";
                            }
                        }
                    }
                }
                return $this->SQLM->query($query);
                break;
            
            default:
                break;
        }
    }

    /**
     * Fetch a SQL-Result
     * Modes: all, array, assoc
     * 
     * If SQLite3 is used, all modes return to "fetchArray", since SQLite3 only supports fetchArray.
     */
    public function fetch($mode = "all", $sqlresult) {
        switch (strtolower($mode)) {
            case 'all':
                return $this->SQLM->fetch_all($sqlresult);
                break;
            
            case 'array':
                return $this->SQLM->fetch_array($sqlresult);
                break;
            
            case 'assoc':
                return $this->SQLM->fetch_assoc($sqlresult);
                break;
            
            default:
                throw new Exception("mode $mode is not a valid mode for fetching SQL Results valid. Modes for fetching: All, Array, Assoc", 1);
                break;
        }
    }

    //DELETE
    /**
     * Use the where statement for specification
     */
    public function delete() {
        $this->attributes = [
            "mode" => "D",
            "where" => []
        ];
    }

    //INSERT

    /**
     * Insert a row into the database
     */
    public function insert(array $columns, array $values) {
        $this->attributes = [
            "mode" => "I",
            "columns" => $columns,
            "values" => $values
        ];
    }

    //SELECT
    /**
     * Creates a select request.
     */
    public function get(string $value, bool $distinct = false) {
        $this->attributes = [
            "mode" => "S",
            "value" => $value,
            "distinct" => $distinct,
            "where" => [],
            "limit" => 0,
            "order_column" => "",
            "order" => ""
        ];
    }

    /**
     * Specifies a limit.
     */
    public function limit(int $limit) {
        $this->attributes["limit"] = $limit;
    }

    /**
     * Specifies an order.
     */
    public function orderBy(string $column, string $order) {
        $this->attributes["order_column"] = $column;
        $this->attributes["order"] = $order;
    }

    //ALSO USED FOR DELETE
    //ALSO USED FOR UPDATE
    /**
     * Specifies a where-condition.
     * To use multiple where-conditions, use this function multiple times.
     */
    public function where(string $key, string $operator, $value) {
        array_push($this->attributes["where"], ["key" => $key, "operator" => $operator, "value" => $value]);
    }

    //UPDATE

    /**
     * Update a row or all rows.
     * Use the where statement for specification.
     */
    public function update(array $columns, array $values) {
        $this->attributes = [
            "mode" => "U",
            "where" => [],
            "columns" => $columns,
            "values" => $values
        ];
    }

    /**
     * Test if a record exists in a database
     */
    public function isRecord($key, $value) {
        if (gettype($value) == "string") {
            $value = "'$value'";
        }
        return mysqli_num_rows($this->SQLM->query("SELECT 1 FROM $this->tablename WHERE $key=$value;")) == 1;
    }
}