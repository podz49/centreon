<?php

use Centreon\Test\Behat\CentreonContext;
use Centreon\Test\Behat\Configuration\SnmpTrapGroupConfigurationPage;
use Centreon\Test\Behat\Configuration\SnmpTrapGroupConfigurationListingPage;

class TrapsSNMPGroupConfigurationContext extends CentreonContext
{
    protected $currentPage;

    protected $initialProperties = array(
        'name' => 'trapGroupName',
        'traps' => array(
            '3com - secureViolation2',
            'Dell - adRebuildFailed'
        )
    );

    protected $updatedProperties = array(
        'name' => 'trapGroupNameChanged',
        'traps' => array(
            'Generic - coldStart',
            'HP - snTrapChasFanFailed'
        )
    );

    /**
     * @Given a trap group is configured
     */
    public function aTrapGroupIsConfigured()
    {
        $this->currentPage = new SnmpTrapGroupConfigurationPage($this);
        $this->currentPage->setProperties($this->initialProperties);
        $this->currentPage->save();
    }

    /**
     * @When I change the properties of a trap group
     */
    public function iChangeThePropertiesOfATrapGroup()
    {
        $this->currentPage = new SnmpTrapGroupConfigurationListingPage($this);
        $this->currentPage = $this->currentPage->inspect($this->initialProperties['name']);
        $this->currentPage->setProperties($this->updatedProperties);
        $this->currentPage->save();
    }

    /**
     * @Then the properties are updated
     */
    public function thePropertiesAreUpdated()
    {
        $this->tableau = array();
        try {
            $this->spin(
                function ($context) {
                    $this->currentPage = new SnmpTrapGroupConfigurationListingPage($this);
                    $this->currentPage = $this->currentPage->inspect($this->updatedProperties['name']);
                    $object = $this->currentPage->getProperties();
                    foreach ($this->updatedProperties as $key => $value) {
                        if ($value != $object[$key]) {
                            if (is_array($value)) {
                                $value = implode(' ', $value);
                            }
                            if ($value != $object[$key]) {
                                $this->tableau[] = $key;
                            }
                        }
                    }
                    return count($this->tableau) == 0;
                },
                "Some properties are not being updated : ",
                5
            );
        } catch (\Exception $e) {
            $this->tableau = array_unique($this->tableau);
            throw new \Exception("Some properties are not being updated : " . implode(',', $this->tableau));
        }
    }

    /**
     * @When I duplicate a trap group
     */
    public function iDuplicateATrapGroup()
    {
        $this->currentPage = new SnmpTrapGroupConfigurationListingPage($this);
        $object = $this->currentPage->getEntry($this->initialProperties['name']);
        $this->assertFind('css', 'input[type="checkbox"][name="select[' . $object['id'] . ']"]')->check();
        $this->setConfirmBox(true);
        $this->selectInList('select[name="o1"]', 'Duplicate');
    }

    /**
     * @Then the new object has the same properties
     */
    public function theNewObjectHasTheSameProperties()
    {
        $this->tableau = array();
        try {
            $this->spin(
                function ($context) {
                    $this->currentPage = new SnmpTrapGroupConfigurationListingPage($this);
                    $this->currentPage = $this->currentPage->inspect($this->initialProperties['name'] . '_1');
                    $object = $this->currentPage->getProperties();
                    foreach ($this->initialProperties as $key => $value) {
                        if ($key != 'name' && $value != $object[$key]) {
                            if (is_array($value)) {
                                $value = implode(' ', $value);
                            }
                            if ($value != $object[$key]) {
                                $this->tableau[] = $key;
                            }
                        }
                    }
                    return count($this->tableau) == 0;
                },
                "Some properties are not being updated : ",
                5
            );
        } catch (\Exception $e) {
            $this->tableau = array_unique($this->tableau);
            throw new \Exception("Some properties are not being updated : " . implode(',', $this->tableau));
        }
    }

    /**
     * @When I delete a trap group
     */
    public function iDeleteATrapGroup()
    {
        $this->currentPage = new SnmpTrapGroupConfigurationListingPage($this);
        $object = $this->currentPage->getEntry($this->initialProperties['name']);
        $this->assertFind('css', 'input[type="checkbox"][name="select[' . $object['id'] . ']"]')->check();
        $this->setConfirmBox(true);
        $this->selectInList('select[name="o1"]', 'Delete');
    }

    /**
     * @Then the deleted object is not displayed in the list
     */
    public function theDeletedObjectIsNotDisplayedInTheList()
    {
        $this->spin(
            function ($context) {
                $this->currentPage = new SnmpTrapGroupConfigurationListingPage($this);
                $object = $this->currentPage->getEntries();
                $bool = true;
                foreach ($object as $value) {
                    $bool = $bool && $value['name'] != $this->initialProperties['name'];
                }
                return $bool;
            },
            "The service is not being deleted.",
            5
        );
    }
}
