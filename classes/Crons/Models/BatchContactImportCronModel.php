<?php

namespace CRMConnector\Crons\Models;

use CRMConnector\Utils\CRMCFunctions;
use finfo;


/**
 * Class BatchContactImportCronModel
 *
 * @package CRMConnector\Crons\Models
 */
class BatchContactImportCronModel
{

    /**
     * @var array
     */
    private $database_column_names = [];

    /**
     * @var array
     */
    private $selected_file_columns = [];

    /**
     * The post ID for the chapter we are importing students for
     * @var int
     */
    private $chapter_id;

    /**
     * @var string
     */
    private $file_extension;

    /**
     * path to the direcotry
     * @var string
     */
    private $directory_upload_path;

    /**
     * path to the file
     * @var string
     */
    private $file_upload_path;

    /**
     * @var
     */
    private $student_file;

    /**
     * Array or errors which are attached when is_valid() gets called
     * @var array
     */
    private $errors = [];

    /**
     * @return array
     */
    public function getDatabaseColumnNames()
    {
        return $this->database_column_names;
    }

    /**
     * @param array $database_column_names
     */
    public function setDatabaseColumnNames($database_column_names)
    {
        $this->database_column_names = serialize($database_column_names);
    }

    /**
     * @return array
     */
    public function getSelectedFileColumns()
    {
        return $this->selected_file_columns;
    }

    /**
     * @param array $selected_file_columns
     */
    public function setSelectedFileColumns($selected_file_columns)
    {
        $this->selected_file_columns = serialize(json_decode($selected_file_columns));
    }

    /**
     * @return int
     */
    public function getChapterId()
    {
        return $this->chapter_id;
    }

    /**
     * @param int $chapter_id
     */
    public function setChapterId($chapter_id)
    {
        $this->chapter_id = sanitize_key($chapter_id);
        // once the chapter id has been set you can set the directory upload path
        $directory_upload_path = sprintf(
            '%s%s/%s',
            CRMCFunctions::plugin_dir() . '/',
            'imports',
            $this->getChapterId()
        );

        $this->setDirectoryUploadPath($directory_upload_path);

    }

    /**
     * @return mixed
     */
    public function getStudentFile()
    {
        return $this->student_file;
    }

    /**
     * @param mixed $student_file
     */
    public function setStudentFile($student_file)
    {
        $this->student_file = $student_file;

        $this->setFileExtension(pathinfo($this->student_file['name'], PATHINFO_EXTENSION));

    }

    /**
     * @return string
     */
    public function getFileExtension()
    {
        return $this->file_extension;
    }

    /**
     * @param string $file_extension
     */
    public function setFileExtension($file_extension)
    {
        $this->file_extension = $file_extension;
    }

    /**
     * @return string
     */
    public function getDirectoryUploadPath()
    {
        return $this->directory_upload_path;
    }

    /**
     * @param string $directory_upload_path
     */
    public function setDirectoryUploadPath($directory_upload_path)
    {
        $this->directory_upload_path = $directory_upload_path;

        if ( ! is_dir($this->directory_upload_path))
        {
            mkdir($this->directory_upload_path);
        }
    }

    /**
     * @return bool
     */
    public function move_file()
    {
        return move_uploaded_file($this->getStudentFile()['tmp_name'], $this->getFileUploadPath());
    }

    /**
     * @return string
     */
    public function getFileUploadPath()
    {
        return $this->file_upload_path = sprintf(
            '%s/%s.%s',
            $this->getDirectoryUploadPath(),
            date('m-d-Y_hia'),
            $this->getFileExtension()
        );
    }

    /**
     * @return mixed
     */
    public function getErrors()
    {
        return $this->errors;
    }


    /**
     * @return bool
     */
    public function is_valid()
    {
        $this->validate_student_file()
            ->validate_chapter_id()
            ->validate_database_column_names();

        return count($this->errors) === 0;
    }


    public function handle_request($request, $files)
    {
        if(isset($request['selected_file_columns']))
        {
            $this->setSelectedFileColumns($request['selected_file_columns']);
        }

        if(isset($request['database_column_name']))
        {
            $this->setDatabaseColumnNames($request['database_column_name']);
        }

        if(isset($request['chapter_id']))
        {
            $this->setChapterId($request['chapter_id']);
        }

        if(isset($files['studentFile']))
        {
            $this->setStudentFile($files['studentFile']);
        }
    }


    private function validate_student_file()
    {
        if(empty($this->student_file))
        {
            $this->errors['main'][] = 'Please add an excel file to import';
            return $this;
        }

        // Check the file MIME Type
        $supported_file_extensions = array(
            'xsl' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        );

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        if(false === $this->file_extension = array_search($finfo->file($this->student_file['tmp_name']), $supported_file_extensions))
        {
            $this->errors['main'][] = sprintf("Supported file types are (%s)", implode(", ", array_keys($supported_file_extensions)));
        }

        // Check $_FILES['upfile']['error'] value.
        switch ($this->student_file['error'])
        {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                $this->errors['main'][] = 'No file sent.';
                break;
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $this->errors['main'][] = 'Exceeded filesize limit.';
                break;
            default:
                $this->errors['main'][] = 'Unknown error.';
        }

        // You should also check filesize here.
        if ($_FILES['studentFile']['size'] > 50000000)
        {
            $this->errors['main'][] = 'Exceeded filesize limit.';
        }

        return $this;
    }

    private function validate_chapter_id()
    {
        if(empty($this->chapter_id))
        {
            $this->errors['main'][] = 'Invalid Form Submission';
            return $this;
        }
        return $this;
    }

    private function validate_database_column_names()
    {
        if(empty(unserialize($this->getDatabaseColumnNames())))
        {
            $this->errors['main'][] = "Please map at least one database column to an excel spreadsheet column\n";
            return $this;
        }

        $dupe_array = array();
        foreach (unserialize($this->getDatabaseColumnNames()) as $val) {
            if (++$dupe_array[$val] > 1) {
                $this->errors['main'][] = 'You cannot use the same database column name twice!';
                return $this;
            }
        }

        // check to make sure that a student email has been added to the import
        if(!in_array('Personal Email', unserialize($this->getDatabaseColumnNames())))
        {
            $this->errors['main'][] = 'You must map an email address!';
            return $this;
        }

        return $this;
    }
}