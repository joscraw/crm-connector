<?php

namespace CRMConnector\Entity;

/**
 * Class ListPostType
 */
class ListPostType
{

    /**
     * @var string
     */
    private $list_name;

    /**
     * @var $is_export_from_chapter boolean
     */
    private $is_export_from_chapter;

    /**
     * @var integer
     */
    private $chapter;

    /**
     * @var integer
     */
    private $num_query_fields;

    /**
     * @var array
     */
    private $query_args = [];

    /**
     * @return string
     */
    public function getListName()
    {
        return $this->list_name;
    }

    /**
     * @param string $list_name
     */
    public function setListName($list_name)
    {
        $this->list_name = $list_name;
    }

    /**
     * @return string
     */
    public function isExportFromChapter()
    {
        return $this->is_export_from_chapter;
    }

    /**
     * @param string $export_from_chapter
     */
    public function setIsExportFromChapter($export_from_chapter)
    {
        $this->is_export_from_chapter = ($export_from_chapter === 'yes') ? true : false;

        if(!$this->is_export_from_chapter)
        {

        }
    }

    /**
     * @return int
     */
    public function getChapter()
    {
        return $this->chapter;
    }

    /**
     * @param int $chapter
     */
    public function setChapter($chapter)
    {
        $this->chapter = (int) unserialize($chapter)[0];
    }

    /**
     * @return int
     */
    public function getNumQueryFields()
    {
        return $this->num_query_fields;
    }

    /**
     * @param $num_query_fields
     */
    public function setNumQueryFields($num_query_fields)
    {
        $this->num_query_fields = (int) $num_query_fields;
    }

    /**
     * @param $query_args
     */
    public function addQueryArgs($query_args)
    {
        $query_args['key'] = trim($query_args['key']);
        $query_args['value'] = trim($query_args['value']);

        switch($query_args['compare'])
        {
            case 'Is equal to':
                $query_args['compare'] = '=';
                break;
            case 'Is not equal to':
                $query_args['compare'] = '!=';
                break;
            case 'Contains':
                $query_args['compare'] = 'LIKE';
                break;
            case 'Doesn\'t contain':
                $query_args['compare'] = 'NOT LIKE';
                break;
            case 'Greater than':
                $query_args['compare'] = '>';
                break;
            case 'Greater than or equal to':
                $query_args['compare'] = '>=';
                break;
            case 'Less than':
                $query_args['compare'] = '<';
                break;
            case 'Less than or equal to':
                $query_args['compare'] = '<=';
                break;
        }

        $this->query_args[] = $query_args;
    }

    /**
     * @return array
     */
    public function getQueryArgs()
    {
        return $this->query_args;
    }

    /**
     * @param $list array
     */
    public function from_array($list)
    {
        if(isset($list['list_name']) && !empty($list['list_name']))
            $this->setListName($list['list_name'][0]);

        if(isset($list['export_from_chapter']))
            $this->setIsExportFromChapter($list['export_from_chapter'][0]);

        if(isset($list['chapter']))
            $this->setChapter($list['chapter'][0]);

        if(isset($list['create_custom_export_query_fields']))
            $this->setNumQueryFields($list['create_custom_export_query_fields'][0]);

        for($i = 0; $i < $this->getNumQueryFields(); $i++)
        {
            $this->addQueryArgs([
                "key"          =>  isset($list["create_custom_export_query_fields_{$i}_name"]) && !empty($list["create_custom_export_query_fields_{$i}_name"]) ? $list["create_custom_export_query_fields_{$i}_name"][0] : "",
                "compare"     =>  isset($list["create_custom_export_query_fields_{$i}_condition"]) && !empty($list["create_custom_export_query_fields_{$i}_condition"]) ? $list["create_custom_export_query_fields_{$i}_condition"][0] : "",
                "value"         =>  isset($list["create_custom_export_query_fields_{$i}_value"]) && !empty($list["create_custom_export_query_fields_{$i}_value"])? $list["create_custom_export_query_fields_{$i}_value"][0] : "",
            ]);
        }
    }
}