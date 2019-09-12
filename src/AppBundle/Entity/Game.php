<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\OneToOne;

/**
 * Game
 *
 * @ORM\Table(name="game")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\GameRepository")
 */
class Game
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
     * @ORM\Column(name="victorious", type="integer", nullable=true)
     */
    private $victorious;

    /**
     * One Game has One GameTable.
     * @OneToOne(targetEntity="GameTable", inversedBy="game")
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
     * Set victorious.
     *
     * @param int|null $victorious
     *
     * @return Game
     */
    public function setVictorious($victorious = null)
    {
        $this->victorious = $victorious;

        return $this;
    }

    /**
     * Get victorious.
     *
     * @return int|null
     */
    public function getVictorious()
    {
        return $this->victorious;
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
