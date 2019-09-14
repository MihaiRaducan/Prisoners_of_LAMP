<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tile
 *
 * @ORM\Table(name="tile")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TileRepository")
 */
class Tile
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
     * @var int
     *
     * @ORM\Column(name="x_index", type="smallint")
     */
    private $xIndex;

    /**
     * @var int
     *
     * @ORM\Column(name="y_index", type="smallint")
     */
    private $yIndex;

    /**
     * @var int
     *
     * @ORM\Column(name="type", type="smallint")
     */
    private $type;

    /**
     * @var int
     *
     * @ORM\Column(name="lucky_number", type="smallint")
     */
    private $luckyNumber;

    /**
     * Many Tiles have One Map
     * @ORM\ManyToOne(targetEntity="Map", inversedBy="tiles")
     * @ORM\JoinColumn(name="map_id", referencedColumnName="id")
     */
    private $map;


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
     * Set xIndex.
     *
     * @param int $xIndex
     *
     * @return Tile
     */
    public function setXIndex($xIndex)
    {
        $this->xIndex = $xIndex;

        return $this;
    }

    /**
     * Get xIndex.
     *
     * @return int
     */
    public function getXIndex()
    {
        return $this->xIndex;
    }

    /**
     * Set yIndex.
     *
     * @param int $yIndex
     *
     * @return Tile
     */
    public function setYIndex($yIndex)
    {
        $this->yIndex = $yIndex;

        return $this;
    }

    /**
     * Get yIndex.
     *
     * @return int
     */
    public function getYIndex()
    {
        return $this->yIndex;
    }

    /**
     * Set type.
     *
     * @param int $type
     *
     * @return Tile
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type.
     *
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set luckyNumber.
     *
     * @param int $luckyNumber
     *
     * @return Tile
     */
    public function setLuckyNumber($luckyNumber)
    {
        $this->luckyNumber = $luckyNumber;

        return $this;
    }

    /**
     * Get luckyNumber.
     *
     * @return int
     */
    public function getLuckyNumber()
    {
        return $this->luckyNumber;
    }

    /**
     * @return mixed
     */
    public function getMap()
    {
        return $this->map;
    }

    /**
     * @param mixed $map
     */
    public function setMap($map)
    {
        $this->map = $map;
    }


}
