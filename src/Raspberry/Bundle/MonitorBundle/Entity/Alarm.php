<?php

namespace Raspberry\Bundle\MonitorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use APY\DataGridBundle\Grid\Mapping as GRID;
use \Raspberry\Bundle\MonitorBundle\Entity\Site;

/**
 * Alarm
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Raspberry\Bundle\MonitorBundle\Entity\AlarmRepository")
 * @GRID\Source(orderBy={"id"})
 */
class Alarm
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @GRID\Column(visible=false, filterable=false)
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     * @GRID\Column(title="Date Time", filterable=false)
     */
    private $createdAt;

    /**
     * @var Site
     *
     * @ORM\ManyToOne(targetEntity="Site")
     * @ORM\JoinColumn(name="site_id", referencedColumnName="id")
     * @GRID\Column(field="site.name", title="Site Name", operatorsVisible=false, searchOnClick= true, filter="select",  selectFrom="source")
     */
    private $site;

    /**
     * @var string
     *
     * @ORM\Column(name="error", type="string", length=255, nullable=true)
     * @GRID\Column(title="Error", operatorsVisible=false)
     */
    private $error = null;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text")
     *  @GRID\Column(title="Message")
     */
    private $message;

    /**
     * Set default DateTime to createdAt
     *
     *
     */
    public function __construct()
    {
        $this->createdAt= new \DateTime();

    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set createdAt
     *
     * @param  \DateTime $createdAt
     * @return Alarm
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set error
     *
     * @param  string $error
     * @return Alarm
     */
    public function setError($error)
    {
        $this->error = $error;

        return $this;
    }

    /**
     * Get error
     *
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Set message
     *
     * @param  string $message
     * @return Alarm
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set site
     *
     * @param  Site $site
     * @return Alarm
     */
    public function setSite(Site $site = null)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Get site
     *
     * @return Site
     */
    public function getSite()
    {
        return $this->site;
    }
}
