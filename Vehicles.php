<?php

namespace Apps\Tms\Packages\Vehicles;

use System\Base\BasePackage;

class Vehicles extends BasePackage
{
    //protected $modelToUse = ::class;

    protected $packageName = 'vehicles';

    public $vehicles;

    public function getVehiclesById($id)
    {
        $vehicles = $this->getById($id);

        if ($vehicles) {
            //
            $this->addResponse('Success');

            return;
        }

        $this->addResponse('Error', 1);
    }

    public function addVehicles($data)
    {
        //
    }

    public function updateVehicles($data)
    {
        $vehicles = $this->getById($id);

        if ($vehicles) {
            //
            $this->addResponse('Success');

            return;
        }

        $this->addResponse('Error', 1);
    }

    public function removeVehicles($data)
    {
        $vehicles = $this->getById($id);

        if ($vehicles) {
            //
            $this->addResponse('Success');

            return;
        }

        $this->addResponse('Error', 1);
    }
}