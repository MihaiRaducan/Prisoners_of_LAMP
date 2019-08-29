<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Player
 *
 * @ORM\Table(name="player")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PlayerRepository")
 */
class Player
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
     * @ORM\Column(name="initialDice", type="smallint", nullable=true)
     */
    private $initialDice;

    /**
     * @var int|null
     *
     * @ORM\Column(name="turnOrder", type="smallint", nullable=true)
     */
    private $turnOrder;

    /**
     * @var string|null
     *
     * @ORM\Column(name="color", type="string", length=8, nullable=true)
     */
    private $color;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="players")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="GameTable", inversedBy="players")
     * @ORM\JoinColumn(name="gametable_id", referencedColumnName="id")
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
     * Set initialDice.
     *
     * @param int|null $initialDice
     *
     * @return Player
     */
    public function setInitialDice($initialDice = null)
    {
        $this->initialDice = $initialDice;

        return $this;
    }

    /**
     * Get initialDice.
     *
     * @return int|null
     */
    public function getInitialDice()
    {
        return $this->initialDice;
    }

    /**
     * Set turnOrder.
     *
     * @param int|null $turnOrder
     *
     * @return Player
     */
    public function setTurnOrder($turnOrder = null)
    {
        $this->turnOrder = $turnOrder;

        return $this;
    }

    /**
     * Get turnOrder.
     *
     * @return int|null
     */
    public function getTurnOrder()
    {
        return $this->turnOrder;
    }

    /**
     * Set color.
     *
     * @param string|null $color
     *
     * @return Player
     */
    public function setColor($color = null)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Get color.
     *
     * @return string|null
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
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
