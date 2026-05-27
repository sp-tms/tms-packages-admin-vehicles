<?php

namespace Apps\Tms\Packages\Vehicles;

use Apps\Tms\Packages\Vehicles\Model\AppsTmsVehicles;
use System\Base\BasePackage;

class Vehicles extends BasePackage
{
    protected $modelToUse = AppsTmsVehicles::class;

    protected $packageName = 'vehicles';

    public $vehicles;

    public function init()
    {
        parent::init();

        return $this;
    }

    public function getVehicle($vehicleId)
    {
        if ($this->config->databasetype === 'db') {
            $vehiclesObj = $this->getFirst('id', $vehicleId);

            if ($vehiclesObj) {
                $vehicle = $vehiclesObj->toArray();

                $addressObj = $vehiclesObj->getAddresses();

                $vehicle['address'] = [];

                if ($addressObj) {
                    $vehicle['address'] = $addressObj->toArray();
                }

                return $vehicle;
            }
        } else {
            // $this->setFFRelations(true);
            // $this->setFFRelationsConditions(['addresses' => ['package_name', '=', 'Companies'], 'contacts' => ['package_name', '=', 'Companies']]);

            $vehicle = $this->getFirst('id', $vehicleId, false, true, null, [], true);

            return $vehicle;
        }

        return false;
    }

    public function addVehicle($data)
    {
        if ($this->add($data)) {
            $vehicle = $this->packagesData->last;

            $this->addResponse('Vehicle added');

            return true;
        }

        $this->addResponse('Error Adding Vehicle', 1);
    }

    public function updateVehicle($data)
    {
        if ($this->update($data)) {
            $this->addResponse('Vehicle updated');

            return true;
        }

        $this->addResponse('Error Updating Vehicle', 1);
    }

    public function removeVehicle($data)
    {
        $vehicle = $this->getVehicle($data['id']);

        //Archive Vehicle and do not delete it!
        $vehicle['archived'] = true;

        if ($this->updateVehicle($vehicle)) {
            $this->addResponse('Vehicle archived');

            return true;
        }

        $this->addResponse('Error removing vehicle', 1);

        return false;
    }

    public function getVehicleByRegistrationNo($registrationNo)
    {
        if ($this->config->databasetype === 'db') {
            $params =
                [
                    'conditions'    => 'registration_no = :registrationNo:',
                    'bind'          =>
                        [
                            'registrationNo'         => $registrationNo
                        ]
                ];
        } else {
            $params = ['conditions' => ['registration_no', '=', $registrationNo]];
        }

        $vehicle = $this->getByParams($params);

        if ($vehicle && count($vehicle) > 0) {
            $vehicle = $this->getVehicle($vehicle[0]['id']);

            return $vehicle;
        }

        return false;
    }
}