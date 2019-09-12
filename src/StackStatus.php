<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 12.09.19
 * Time: 18:28
 */

namespace Rudl;


use Phore\FileSystem\PhoreFile;

class StackStatus
{

    /**
     * @var PhoreFile
     */
    private $file;

    private $data = [];

    public function __construct(PhoreFile $filename)
    {
        $this->file = $filename;
        if ( ! $this->file->exists())
            $this->file->set_json([]);
        $this->data = $this->file->get_json();
    }


    public function flush()
    {
        $this->file->set_json($this->data);
    }

    public function set($key, $value)
    {
        $this->data[$key] = $value;
        $this->flush();
    }


    public function getData() : array
    {
        return $this->data;
    }


}