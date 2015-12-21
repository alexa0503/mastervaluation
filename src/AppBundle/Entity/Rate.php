<?php
namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="t_rate")
 */
class Rate
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
   /**
     * @ORM\Column(name="status",type="integer")
     */
    protected $status;
   /**
     * @ORM\Column(name="type",type="integer")
     */
    protected $type;
   /**
     * @ORM\Column(name="grade",type="integer")
     */
    protected $grade;
   /**
     * @ORM\Column(name="regional",type="integer")
     */
    protected $regional;
   /**
     * @ORM\Column(name="standard",type="integer")
     */
    protected $standard;
   /**
     * @ORM\Column(name="layer",type="integer")
     */
    protected $layer;
   /**
     * @ORM\Column(name="rate",type="decimal", precision=8, scale=2)
     */
    protected $rate;
    /**
     * @ORM\Column(name="create_time",  type="datetime")
     */
    private $createTime;
    /**
     * @ORM\Column(name="create_ip", type="string", length=60)
     */
    private $createIp;


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
     * Set type
     *
     * @param integer $type
     * @return Rate
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set grade
     *
     * @param integer $grade
     * @return Rate
     */
    public function setGrade($grade)
    {
        $this->grade = $grade;

        return $this;
    }

    /**
     * Get grade
     *
     * @return integer 
     */
    public function getGrade()
    {
        return $this->grade;
    }

    /**
     * Set regional
     *
     * @param integer $regional
     * @return Rate
     */
    public function setRegional($regional)
    {
        $this->regional = $regional;

        return $this;
    }

    /**
     * Get regional
     *
     * @return integer 
     */
    public function getRegional()
    {
        return $this->regional;
    }

    /**
     * Set standard
     *
     * @param integer $standard
     * @return Rate
     */
    public function setStandard($standard)
    {
        $this->standard = $standard;

        return $this;
    }

    /**
     * Get standard
     *
     * @return integer 
     */
    public function getStandard()
    {
        return $this->standard;
    }

    /**
     * Set layer
     *
     * @param integer $layer
     * @return Rate
     */
    public function setLayer($layer)
    {
        $this->layer = $layer;

        return $this;
    }

    /**
     * Get layer
     *
     * @return integer 
     */
    public function getLayer()
    {
        return $this->layer;
    }

    /**
     * Set rate
     *
     * @param string $rate
     * @return Rate
     */
    public function setRate($rate)
    {
        $this->rate = $rate;

        return $this;
    }

    /**
     * Get rate
     *
     * @return string 
     */
    public function getRate()
    {
        return $this->rate;
    }

    /**
     * Set createTime
     *
     * @param \DateTime $createTime
     * @return Rate
     */
    public function setCreateTime($createTime)
    {
        $this->createTime = $createTime;

        return $this;
    }

    /**
     * Get createTime
     *
     * @return \DateTime 
     */
    public function getCreateTime()
    {
        return $this->createTime;
    }

    /**
     * Set createIp
     *
     * @param string $createIp
     * @return Rate
     */
    public function setCreateIp($createIp)
    {
        $this->createIp = $createIp;

        return $this;
    }

    /**
     * Get createIp
     *
     * @return string 
     */
    public function getCreateIp()
    {
        return $this->createIp;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return Rate
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }
}
