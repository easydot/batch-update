<?php

namespace Easydot\BatchUpdate;

use Illuminate\Support\Facades\DB;

class BatchUpdate
{
    protected string $table;
    protected array $multipleData = [];
    protected string $referenceColumn;
    protected array $wheres = [];

    public function __construct($tableName, $data = [], $referenceColumn = 'id')
    {
        $this->table = $tableName;
        $this->multipleData = $data;
        $this->referenceColumn = $referenceColumn;
    }

    public function multipleData($data = [])
    {
        $this->multipleData = $data;
        return $this;
    }

    public function referenceColumn($column)
    {
        $this->referenceColumn = $column;
        return $this;
    }

    public function where($column, $operator, $value)
    {
        $this->wheres[] = "{$column} {$operator} '{$value}'";
        return $this;
    }

    public function whereIn($column, $values)
    {
        $values = array_map(function ($item) {
            return "'" . $item . "'";
        }, $values);
        $valuesAsString = implode(', ', $values);
        $this->wheres[] = "{$column} IN ({$valuesAsString})";
        return $this;
    }

    public function run()
    {
        if ($this->table && !empty($this->multipleData)) {
            // column or fields to update
            $this->multipleData = array_values($this->multipleData);
            $updateColumns = array_keys($this->multipleData[0]);
            unset($updateColumns[array_search($this->referenceColumn, $updateColumns)]);
            $whereIn = "";

            $q = "UPDATE " . $this->table . " SET ";
            foreach ($updateColumns as $uColumn) {
                $q .= $uColumn . " = CASE ";

                foreach ($this->multipleData as $data) {
                    $valOrNull = $data[$uColumn] ?? 'NULL';
                    if ($valOrNull != 'NULL') {
                        if (is_bool($valOrNull)) {
                            $valOrNull = (int)$valOrNull;
                        }
                        $valOrNull = addslashes($valOrNull);
                        $valOrNull = "'{$valOrNull}'";
                    }

                    $q .= "WHEN " . $this->referenceColumn . " = '" . addslashes($data[$this->referenceColumn]) . "' THEN {$valOrNull} ";
                }
                $q .= "ELSE " . $uColumn . " END, ";
            }
            foreach ($this->multipleData as $data) {
                $whereIn .= "'" . addslashes($data[$this->referenceColumn]) . "', ";
            }
            $q = rtrim($q, ", ") . " WHERE " . $this->referenceColumn . " IN (" . rtrim($whereIn, ', ') . ")";

            if ($this->wheres) {
                $q .= 'AND ' . implode(' AND ', $this->wheres);
            }


            // Update
            return DB::update(DB::raw($q));

        } else {
            return false;
        }
    }
}