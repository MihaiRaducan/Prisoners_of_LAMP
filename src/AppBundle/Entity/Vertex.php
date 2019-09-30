<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Vertex
 *
 * @ORM\Table(name="vertex")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\VertexRepository")
 */
class Vertex
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
     * @var string
     *
     * @ORM\Column(name="row_index", type="decimal", precision=2, scale=1)
     */
    private $rowIndex;

    /**
     * @var string
     *
     * @ORM\Column(name="pos_index", type="decimal", precision=2, scale=1)
     */
    private $posIndex;

    /**
     * @var int|null
     *
     * @ORM\Column(name="port_type", type="smallint", nullable=true)
     */
    private $portType;

    /**
     * @var int|null
     *
     * @ORM\Column(name="port_inclination", type="smallint", nullable=true)
     */
    private $portInclination;

    /**
     * Many Vertices have One Map
     * @ORM\ManyToOne(targetEntity="Map", inversedBy="vertices")
     * @ORM\JoinColumn(name="map_id", referencedColumnName="id")
     */
    private $map;

    public function __construct($rowIndex = null, $posIndex = null, $portType = null, $portInclination = null)
    {
        $this->setRowIndex($rowIndex);
        $this->setPosIndex($posIndex);
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
     * Set rowIndex.
     *
     * @param int $rowIndex
     *
     * @return Vertex
     */
    public function setRowIndex($rowIndex)
    {
        $this->rowIndex = $rowIndex;

        return $this;
    }

    /**
     * Get rowIndex.
     *
     * @return int
     */
    public function getRowIndex()
    {
        return $this->rowIndex;
    }

    /**
     * Set posIndex.
     *
     * @param int $posIndex
     *
     * @return Vertex
     */
    public function setPosIndex($posIndex)
    {
        $this->posIndex = $posIndex;

        return $this;
    }

    /**
     * Get posIndex.
     *
     * @return int
     */
    public function getPosIndex()
    {
        return $this->posIndex;
    }

    /**
     * Set portType.
     *
     * @param int|null $portType
     *
     * @return Vertex
     */
    public function setPortType($portType = null)
    {
        $this->portType = $portType;

        return $this;
    }

    /**
     * Get portType.
     *
     * @return int|null
     */
    public function getPortType()
    {
        return $this->portType;
    }

    /**
     * @return int|null
     */
    public function getPortInclination()
    {
        return $this->portInclination;
    }

    /**
     * @param int|null $portInclination
     */
    public function setPortInclination($portInclination)
    {
        $this->portInclination = $portInclination;
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
