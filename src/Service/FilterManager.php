<?php declare(strict_types=1);

namespace Samsonos\AsyncTable\Service;

class FilterManager
{
    /**
     * Get date from parse string
     *
     * @param $date
     * @return mixed
     * @throws \Exception
     */
    public static function getParseDate(String $date)
    {
        $returnDate = null;
        // Parse data
        preg_match_all('/\d{4}.?\d{2}.?\d{2}/', $date, $parseDate);
        // If exits startDate and endDate
        if (isset($parseDate[0], $parseDate[0][0], $parseDate[0][1])) {
            $returnDate['startDate'] = $parseDate[0][0];
            $returnDate['endDate'] = $parseDate[0][1];
        } else {
            throw new \Exception('Error: Parse date from input string');
        }

        return $returnDate;
    }
}