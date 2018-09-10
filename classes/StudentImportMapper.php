<?php

namespace CRMConnector;

use CRMConnector\Models\Collection;
use CRMConnector\Models\Contact;
use PhpOffice\PhpSpreadsheet\Worksheet\RowIterator;

/**
 * Class StudentImportMapper
 * @package CRMConnector
 */
class StudentImportMapper extends ExcelMapper
{

    use StudentImportContactTransformer;

    /**
     * @var array
     */
    private $database_column_names;

    /**
     * @var array
     */
    private $selected_file_columns;

    /**
     * @var Collection
     */
    private $potential_duplicates;

    /**
     * StudentImportMapper constructor.
     * @param $rowIterator
     * @param $database_column_names
     * @param $selected_file_columns
     */
    public function __construct($rowIterator, $database_column_names, $selected_file_columns)
    {
        $this->potential_duplicates = new Collection();

        $this->database_column_names = $database_column_names;

        $this->selected_file_columns = $selected_file_columns;

        parent::__construct($rowIterator);
    }

    /**
     * @return Contact
     */
    public function current()
    {
        $values = [];
        $cellIterator = $this->rowIterator->current()->getCellIterator();

        foreach($cellIterator as $index => $cell)
        {
            $this->columns[] = $cell->getValue();
        }

        $this->next();
        $cellIterator = $this->rowIterator->current()->getCellIterator();

        foreach($cellIterator as $index => $cell)
        {
            $values[] = $cell->getValue();
        }

        $record = [];
        foreach($this->selected_file_columns as $key => $selectedFileColumn)
        {
            $record[$this->database_column_names[$key]] = $values[$selectedFileColumn];
        }

        $record = $this->transform_record($record);

        $contact = new Contact();

        $contact->fromArray($record);

        return $contact;
    }

    /**
     * @return Collection
     */
    public function get_potential_duplicates()
    {
        return $this->potential_duplicates;
    }
}