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

    public function updateDocument($data)
    {
        $this->setFFValidation(false);

        $vehicle = $this->getById((int) $data['id']);

        if (!isset($data['documents'])) {
            $data['documents'] = [];
        }
        if (is_string($vehicle['documents'])) {
            $vehicle['documents'] = $this->helper->decode($vehicle['documents'], true);
        }

        if (isset($vehicle['documents']) && count($vehicle['documents']) > 0) {
            $vehicle['documents'] = array_replace($data['documents'], array_intersect_key($data['documents'], $vehicle['documents']));
        } else {
            $vehicle['documents'] = $data['documents'];
        }

        foreach ($vehicle['documents'] as $uuid => &$document) {
            if (!isset($document['account_id'])) {
                $document['account_id'] = 0;
            } else {
                $document['account_id'] = (int) $document['account_id'];
            }

            if ($document['account_id'] === 0) {
                if ($this->access->auth->check()) {
                    $document['account_id'] = $this->access->auth->account()['id'];
                    $account = $this->basepackages->accounts->getAccountById($this->access->auth->account()['id']);

                    if ($account && isset($account['contact']['full_name'])) {
                        $document['account_name'] = $account['contact']['full_name'];
                    }
                } else {
                    $document['account_name'] = '-';
                }
            }

            if (!isset($document['date'])) {
                $document['date'] = (\Carbon\Carbon::now('Asia/Kolkata'))->toAtomString();
            }
        }

        if ($this->update($vehicle)) {
            $this->addResponse('Added documents to vehicle', 0, ['documents' => $vehicle['documents']]);

            return true;
        }

        $this->addResponse('Error while updating documents for vehicle', 1);

        return false;
    }

    public function getVehicleAvailableStatus()
    {
        return
            [
                '0' =>
                    [
                        'id' => '0',
                        'name'  => 'Idle'
                    ],
                '1' =>
                    [
                        'id' => '1',
                        'name'  => 'On Trip'
                    ],
                '2' =>
                    [
                        'id' => '2',
                        'name'  => 'At Service'
                    ]
            ];
    }
}