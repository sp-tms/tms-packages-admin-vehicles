<?php

namespace Apps\Tms\Packages\Vehicles\Model;

use System\Base\BaseModel;

class AppsTmsVehicles extends BaseModel
{
    public $id;

    public $registration_no;

    public $vehicle_type;

    public $company_id;

    public $make;

    public $model;

    public $manufacturing_year;

    public $euro_norms;

    public $no_of_wheels;

    public $capacity;

    public $capacity_uom;

    public $purchase_odo;

    public $fuel_capacity;

    public $fuel_consumption;

    public $service_odo;

    public $service_months;

    public $documents;

    public $status;

    public $archived;
}