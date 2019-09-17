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
     * @ORM\Column(name="row_index", type="smallint")
     */
    private $rowIndex;

    /**
     * @var int
     *
     * @ORM\Column(name="pos_index", type="smallint")
     */
    private $posIndex;

    /**
     * @var int
     *
     * @ORM\Column(name="type", type="smallint")
     */
    private $type;

    /**
     * @var int|null
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

    public function __construct($rowIndex = null, $posIndex = null, $type = null, $luckyNumber = null)
    {
        $this->setRowIndex($rowIndex);
        $this->setPosIndex($posIndex);
        $this->setType($type);
        $this->setLuckyNumber($luckyNumber);
    }

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
     * @return int
     */
    public function getRowIndex()
    {
        return $this->rowIndex;
    }

    /**
     * @param int $rowIndex
     */
    public function setRowIndex($rowIndex)
    {
        $this->rowIndex = $rowIndex;
    }

    /**
     * @return int
     */
    public function getPosIndex()
    {
        return $this->posIndex;
    }

    /**
     * @param int $posIndex
     */
    public function setPosIndex($posIndex)
    {
        $this->posIndex = $posIndex;
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
