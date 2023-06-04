<?php

namespace System\Base\Providers\DatabaseServiceProvider\Ff\Classes;

use DateTime;
use Exception;
use System\Base\Providers\DatabaseServiceProvider\Ff\Exceptions\InvalidArgumentException;
use Throwable;

class ConditionsHandler
{
    public static function verifyCondition(string $condition, $fieldValue, $value): bool
    {
        if ($value instanceof DateTime) {
            if (empty($fieldValue)) {
                return false;
            }

            $value = $value->getTimestamp();

            $fieldValue = self::convertValueToTimeStamp($fieldValue);
        }

        $condition = strtolower(trim($condition));

        switch ($condition) {
            case "=":
            case "===":
                return ($fieldValue === $value);
            case "==":
                return ($fieldValue == $value);
            case "<>":
                return ($fieldValue != $value);
            case "!==":
            case "!=":
                return ($fieldValue !== $value);
            case ">":
                return ($fieldValue > $value);
            case ">=":
                return ($fieldValue >= $value);
            case "<":
                return ($fieldValue < $value);
            case "<=":
                return ($fieldValue <= $value);
            case "not like":
            case "like":
                if (!is_string($value)) {
                    throw new InvalidArgumentException("When using \"LIKE\" or \"NOT LIKE\" the value has to be a string.");
                }

                // escape characters that are part of regular expression syntax
                // https://www.php.net/manual/en/function.preg-quote.php
                // We can not use preg_quote because the following characters are also wildcard characters in sql
                // so we will not escape them: [ ^ ] -
                $charactersToEscape = [
                    '\\' => '\\\\', // Escape backslash to prevent unwanted behaviour
                    '/' => '\/', // slash needs to be escaped because it's used as delimiter
                    '.' => '\.',
                    '+' => '\+',
                    '*' => '\*',
                    '?' => '\?',
                    '$' => '\$',
                    '(' => '\(',
                    ')' => '\)',
                    '{' => '\{',
                    '}' => '\}',
                    '=' => '\=',
                    '!' => '\!',
                    '<' => '\<',
                    '>' => '\>',
                    '|' => '\|',
                    ':' => '\:',
                    '#' => '\#',
                    '%' => '.*',
                    '_' => '.{1}',
                    // Allow escaping of % and _ with backslash
                    '\.*' => '%',
                    '\.{1}' => '_',
                    // Allow escaping of wildcards
                    '\\\\[' => '\[',
                    '\\\\^' => '\^',
                    '\\\\]' => '\]',
                    '\\\\-' => '\-'
                ];

                // (zero or more characters) and (single character)
                $value = str_replace(array_keys($charactersToEscape), array_values($charactersToEscape), $value);

                $pattern = '/^' . $value . '$/i';

                $result = (preg_match($pattern, $fieldValue) === 1);

                return ($condition === 'not like') ? !$result : $result;
            case "not in":
            case "in":
                if (!is_array($value)) {
                    $value = (!is_object($value) && !is_array($value) && !is_null($value)) ? $value : gettype($value);

                    throw new InvalidArgumentException("When using \"in\" and \"not in\" you have to check against an array. Got: $value");
                }

                if (!empty($value)) {
                    (list($firstElement) = $value);

                    if ($firstElement instanceof DateTime) {
                        if (empty($fieldValue)) {
                            return false;
                        }

                        foreach ($value as $key => $item) {
                            if (!($item instanceof DateTime)) {
                                throw new InvalidArgumentException("If one DateTime object is given in an \"IN\" or \"NOT IN\" comparison, every element has to be a DateTime object!");
                            }

                            $value[$key] = $item->getTimestamp();
                        }

                        $fieldValue = self::convertValueToTimeStamp($fieldValue);
                    }
                }

                $result = in_array($fieldValue, $value, true);

                return ($condition === "not in") ? !$result : $result;
            case "not between":
            case "between":
                if (!is_array($value) ||
                    ($valueLength = count($value)) !== 2
                ) {
                    $value = (!is_object($value) && !is_array($value) && !is_null($value)) ? $value : gettype($value);

                    if (isset($valueLength)) {
                        $value .= " | Length: $valueLength";
                    }

                    throw new InvalidArgumentException("When using \"between\" you have to check against an array with a length of 2. Got: $value");
                }

                list($startValue, $endValue) = $value;

                $result = (
                    self::verifyCondition(">=", $fieldValue, $startValue)
                    && self::verifyCondition("<=", $fieldValue, $endValue)
                );

                return ($condition === "not between") ? !$result : $result;
            case "not contains":
            case "contains":
                if (!is_array($fieldValue)) {
                    return ($condition === "not contains");
                }

                $fieldValues = [];

                if ($value instanceof DateTime) {
                    $value = $value->getTimestamp();

                    foreach ($fieldValue as $item) {
                        if (empty($item)) {
                            continue;
                        }

                        try {
                            $fieldValues[] = self::convertValueToTimeStamp($item);
                        } catch (Exception $exception) {
                            throw $exception;
                        }
                    }
                }

                if (!empty($fieldValues)) {
                    $result = in_array($value, $fieldValues, true);
                } else {
                    $result = in_array($value, $fieldValue, true);
                }

                return ($condition === "not contains") ? !$result : $result;
            case 'exists':
                return $fieldValue === $value;
            default:
                throw new InvalidArgumentException("Condition \"$condition\" is not allowed.");
        }
    }

    public static function handleWhereConditions(array $element, array $data): bool
    {
        if (empty($element)) {
            throw new InvalidArgumentException("Malformed where statement! Where statements can not contain empty arrays.");
        }

        if (array_keys($element) !== range(0, (count($element) - 1))) {
            throw new InvalidArgumentException("Malformed where statement! Associative arrays are not allowed.");
        }

        if (is_string($element[0]) && is_string($element[1])) {
            if (count($element) !== 3) {
                throw new InvalidArgumentException("Where conditions have to be [fieldName, condition, value]");
            }

            $fieldName = $element[0];
            $condition = strtolower(trim($element[1]));
            $fieldValue = ($condition === 'exists')
            ? NestedHelper::nestedFieldExists($fieldName, $data)
            : NestedHelper::getNestedValue($fieldName, $data);

            return self::verifyCondition($condition, $fieldValue, $element[2]);
        }

        $results = [];

        foreach ($element as $value) {
            if (is_array($value)) {
                $results[] = self::handleWhereConditions($value, $data);
            } else if (is_string($value)) {
                $results[] = $value;
            } else if ($value instanceof \Closure) {
                $result = $value($data);
                if (!is_bool($result)) {
                    $resultType = gettype($result);
                    $errorMsg = "The closure in the where condition needs to return a boolean. Got: $resultType";
                    throw new InvalidArgumentException($errorMsg);
                }

                $results[] = $result;
            } else {
                $value = (!is_object($value) && !is_array($value) && !is_null($value)) ? $value : gettype($value);

                throw new InvalidArgumentException("Invalid nested where statement element! Expected condition or operation, got: \"$value\"");
            }
        }

        $returnValue = array_shift($results);

        if (is_bool($returnValue) === false) {
            throw new InvalidArgumentException("Malformed where statement! First part of the statement have to be a condition.");
        }

        $orResults = [];

        while (!empty($results) || !empty($orResults)) {

            if (empty($results)) {
                if ($returnValue === true) {
                    break;
                }

                $nextResult = array_shift($orResults);

                $returnValue = $returnValue || $nextResult;

                continue;
            }

            $operationOrNextResult = array_shift($results);

            if (is_string($operationOrNextResult)) {
                $operation = $operationOrNextResult;

                if (empty($results)) {
                    throw new InvalidArgumentException("Malformed where statement! Last part of a condition can not be a operation.");
                }

                $nextResult = array_shift($results);

                if (!is_bool($nextResult)) {
                    throw new InvalidArgumentException("Malformed where statement! Two operations in a row are not allowed.");
                }
            } else if (is_bool($operationOrNextResult)) {
                $operation = "AND";

                $nextResult = $operationOrNextResult;
            } else {
                throw new InvalidArgumentException("Malformed where statement! A where statement have to contain just operations and conditions.");
            }

            if (!in_array(strtolower($operation), ["and", "or"])) {
                $operation = (!is_object($operation) && !is_array($operation) && !is_null($operation)) ? $operation : gettype($operation);

                throw new InvalidArgumentException("Expected 'and' or 'or' operator got \"$operation\"");
            }

            if (strtolower($operation) === "or") {
                $orResults[] = $returnValue;

                $returnValue = $nextResult;

                continue;
            }

            $returnValue = $returnValue && $nextResult;
        }

        return $returnValue;
    }

    public static function handleDistinct(array $results, array $currentDocument, array $distinctFields): bool
    {
        foreach ($results as $result) {
            foreach ($distinctFields as $field) {
                try {
                    $storePassed = (NestedHelper::getNestedValue($field, $result) !== NestedHelper::getNestedValue($field, $currentDocument));
                } catch (Throwable $th) {
                    continue;
                }

                if ($storePassed === false) {
                    return false;
                }
            }
        }

        return true;
    }

    protected static function convertValueToTimeStamp($value): int
    {
        $value = (is_string($value)) ? trim($value) : $value;

        try {
            return (new DateTime($value))->getTimestamp();
        } catch (Exception $exception) {
            $value = (!is_object($value) && !is_array($value))
            ? $value
            : gettype($value);

            throw new InvalidArgumentException(
                "DateTime object given as value to check against. "
                . "Could not convert value into DateTime. "
                . "Value: $value"
            );
        }
    }
}