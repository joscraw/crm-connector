<?php

namespace CRMConnector;

use CRMConnector\Models\Contact;
use PhpOffice\PhpSpreadsheet\Worksheet\RowIterator;

/**
 * Class ExcelMapper
 * @package CRMConnector
 */
class ExcelMapper implements \Iterator
{

    use ContactTransformer;

    /**
     * @var array
     */
    protected $columns = [];

    /**
     * @var RowIterator
     */
    protected $rowIterator;

    /**
     * ContactMapper constructor.
     * @param $rowIterator
     */
    public function __construct($rowIterator)
    {
        $this->rowIterator = $rowIterator;
    }

    public function current()
    {
        $values = [];
        $cellIterator = $this->rowIterator->current()->getCellIterator();

        foreach($cellIterator as $index => $cell)
        {
            $this->columns[] = $cell->getValue();
        }

        $cellIterator = $this->rowIterator->current()->getCellIterator();

        foreach($cellIterator as $index => $cell)
        {
            $values[] = $cell->getValue();
        }

        $record = array_combine($this->columns, $values);

        return $this->transform_record($record);
    }

    public function next()
    {
        $this->rowIterator->next();
    }

    public function key()
    {
        return $this->rowIterator->key();
    }

    public function valid()
    {
        return $this->rowIterator->valid();
    }

    public function rewind()
    {
        $this->rowIterator->rewind();
    }
}