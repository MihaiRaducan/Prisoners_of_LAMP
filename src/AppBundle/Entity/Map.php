<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\OneToOne;

/**
 * Map
 *
 * @ORM\Table(name="map")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MapRepository")
 */
class Map
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int|null
     *
     * @ORM\Column(name="winner_id", type="integer", nullable=true)
     */
    private $winnerId;

    /**
     * One Map has One GameTable.
     * @OneToOne(targetEntity="GameTable", inversedBy="map")
     * @JoinColumn(name="gameTable_id", referencedColumnName="id")
     */
    private $gameTable;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set winnerId.
     *
     * @param int|null $winnerId
     *
     * @return Map
     */
    public function setWinnerId($winnerId = null)
    {
        $this->winnerId = $winnerId;

        return $this;
    }

    /**
     * Get winnerId.
     *
     * @return int|null
     */
    public function getWinnerId()
    {
        return $this->winnerId;
    }

    /**
     * @return mixed
     */
    public function getGameTable()
    {
        return $this->gameTable;
    }

    /**
     * @param mixed $gameTable
     */
    public function setGameTable($gameTable)
    {
        $this->gameTable = $gameTable;
    }
}
