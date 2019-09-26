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
     * @var int|null
     *
     * @ORM\Column(name="port_type", type="smallint", nullable=true)
     */
    private $portType;

    /**
     * Many Vertices have One Map
     * @ORM\ManyToOne(targetEntity="Map", inversedBy="vertices")
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
}
