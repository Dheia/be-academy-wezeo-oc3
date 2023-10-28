<?php namespace App\Arrival\Models;

use Model;

/**
 * Arrival Model
 *
 * @link https://docs.octobercms.com/3.x/extend/system/models.html
 */
class Arrival extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string table name
     */
    public $table = 'app_arrival_arrivals';

    /**
     * @var array rules for validation
     */
    public $rules = [];
}
