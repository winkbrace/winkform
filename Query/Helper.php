<?php namespace WinkForm\Query;

/**
 * Helper class for integration with the Query class
 * @author b-deruiter
 *
 */
class Helper
{

    /**
     * use $query object to populate options list
     * @param \Query $query
     * @param string $valueColumn
     * @param string $labelColumn
     * @param string $categoryColumn
     * @param optional int $flag
     */
    public static function createOptionsFromQuery(\Query $query, $valueColumn, $labelColumn, $categoryColumn = null)
    {
        if (! $query->execute())
            throw new \Exception($query->getError('Error executing query to create options for Input element'));
        
        $result = $query->fetchAll();
        if (empty($result))
            return null;
        
        $options = array();
        foreach ($result as $row)
        {
            $category = (! empty($categoryColumn) && isset($row[$categoryColumn])) ? $row[$categoryColumn] : 0;
            $options[$category][$row[$valueColumn]] = $row[$labelColumn];
        }
        
        return $options;
    }
    
}
