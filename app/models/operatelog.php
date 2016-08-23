<?php

class Operatelog extends AppModel 
{
    public $name = 'Operatelog';
    public $useTable = 'smm_sdoperatelog';
    public $primaryKey = 'programguid';

    public $virtualFields = array(
        'day' => "to_char(operatetime, 'yyyy-mm-dd')",
        'week' => "to_char(operatetime, 'yyyy-ww')",
        'month' => "to_char(operatetime, 'yyyy-mm')",
        'year' => "to_char(operatetime, 'yyyy')",
    );
}