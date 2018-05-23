<?php

namespace Muchacuba;

/**
 * @di\service()
 */
class OrderedUpgradeCollections
{
    /**
     * @var Upgrade\SelectCollection
     */
    private $selectCollection;

    /**
     * @var UpgradeCollections[]
     */
    private $upgradeCollectionsServices;

    /**
     * @param Upgrade\SelectCollection $selectCollection
     * @param UpgradeCollections[]     $upgradeCollectionsServices
     *
     * @di\arguments({
     *     upgradeCollectionsServices: '#muchacuba.upgrade_collections'
     * })
     */
    public function __construct(
        Upgrade\SelectCollection $selectCollection,
        array $upgradeCollectionsServices
    ) {
        $this->selectCollection = $selectCollection;
        $this->upgradeCollectionsServices = $upgradeCollectionsServices;
    }

    /**
     * @cli\resolution({command: "/upgrade"})
     *
     * @return string[]
     */
    public function upgrade()
    {
        $ids = [];

        ksort($this->upgradeCollectionsServices);

        foreach ($this->upgradeCollectionsServices as $key => $upgrade) {
            $last = $this->getLastUpgrade();

            $result = $upgrade->upgrade($last);

            if ($result == true) {
                $this->addUpgrade((string) $key);

                $ids[] = $key;
            }
        }

        return array_filter($ids);
    }

    /**
     * @return string|null
     */
    private function getLastUpgrade()
    {
        $upgrade = $this->selectCollection->select()->findOne(
            [],
            [
                'sort' => [
                    '_id' => -1
                ]
            ]
        );

        if ($upgrade == null) {
            return null;
        }

        return $upgrade->_id;
    }

    /**
     * @param string $id
     */
    private function addUpgrade($id)
    {
        $this->selectCollection->select()->insertOne(
            ['_id' => $id]
        );
    }
}