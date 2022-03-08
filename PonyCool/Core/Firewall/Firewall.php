<?php
declare(strict_types=1);

namespace PonyCool\Core\Firewall;

use PonyCool\Core\Firewall\Entry\EntryFactory;
use PonyCool\Core\Firewall\Lists\ListMerger;

class Firewall
{
    protected EntryFactory $entryFactory;
    protected ListMerger $listMerger;
    protected $defaultState;
    protected $ipAddress;

    public function __construct(EntryFactory $entryFactory = null, ListMerger $listMerger = null)
    {
        if (is_null($entryFactory)) {
            $this->entryFactory = new EntryFactory();
        } else {
            $this->entryFactory = $entryFactory;
        }
        if (is_null($listMerger)) {
            $this->listMerger = new ListMerger();
        } else {
            $this->listMerger = $listMerger;
        }
    }

    /**
     * @return mixed
     */
    public function getEntryFactory()
    {
        return $this->entryFactory;
    }

    /**
     * @param mixed $entryFactory
     */
    public function setEntryFactory($entryFactory): void
    {
        $this->entryFactory = $entryFactory;
    }

    /**
     * @return mixed
     */
    public function getListMerger()
    {
        return $this->listMerger;
    }

    /**
     * @param mixed $listMerger
     */
    public function setListMerger($listMerger): void
    {
        $this->listMerger = $listMerger;
    }

    /**
     * @return mixed
     */
    public function getDefaultState()
    {
        return $this->defaultState;
    }

    /**
     * @param bool $state
     * @return $this
     */
    public function setDefaultState(bool $state): Firewall
    {
        $this->defaultState = $state;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIpAddress()
    {
        return $this->ipAddress;
    }

    /**
     * @param $ipAddress
     * @return $this
     */
    public function setIpAddress($ipAddress): Firewall
    {
        $this->ipAddress = $ipAddress;
        return $this;
    }

    /**
     * 添加列表
     * @param array $list
     * @param string $listName
     * @param bool $state
     * @return $this
     */
    public function addList(array $list, string $listName, bool $state): Firewall
    {
        $entryList = $this->entryFactory->getEntryList($list, $state);
        $this->listMerger->addList($entryList, $listName);

        return $this;
    }

    /**
     * 处理当前请求
     * @param callable|null $callBack
     * @return bool|mixed
     */
    public function handle(callable $callBack = null)
    {
        $ip = $this->getIpAddress();

        $isAllowed = $this->listMerger->isAllowed($ip, $this->defaultState);

        if ($callBack !== null) {
            return call_user_func($callBack, array($this, $isAllowed));
        } else {
            return $isAllowed;
        }
    }
}
