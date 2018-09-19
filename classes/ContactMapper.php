<?php

namespace CRMConnector;

use CRMConnector\Models\Contact;
use Iterator;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Class ContactMapper
 * @package CRMConnector
 */
class ContactMapper implements Iterator
{
    use ContactTransformer;

    /**
     * @var Spreadsheet
     */
    private $rows;

    /**
     * @var array
     */
    private $columns = [];

    /**
     * ContactMapper constructor.
     * @param array $rows
     */
    public function __construct($rows)
    {
        $this->rows = $rows;
        $this->columns = $this->rows[0];
    }

    private $index = 1;

    public function current()
    {
        $row =  $this->rows[$this->index];

        $record = array_combine($this->columns, $row);

        $contact = new Contact;

        $transformed_record = $this->transform_record($record);

        $contact->fromArray($transformed_record);

        return $contact;
    }

    public function next()
    {
        $this->index ++;
    }

    public function key()
    {
        return $this->index;
    }

    public function valid()
    {
        return isset($this->rows[$this->key()]);
    }

    public function rewind()
    {
        $this->index = 1;
    }

    public function reverse()
    {
        $this->testing = array_reverse($this->rows);
        $this->rewind();
    }
}