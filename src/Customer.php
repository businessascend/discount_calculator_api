<?php
declare(strict_types=1);

/**
  * Class Customer | core/Customer.class.php
  *
  * @api
  * @license GPL
  * @license http://opensource.org/licenses/gpl-license.php GNU Public License
  * @package app\src
  * @author Adedoyin Adedipe <a.adedip@businessascend.com>
  * @copyright No restrictions.
  * @filesource
  */
namespace app\src;
/**
 * Class Customer models business client.
 * To use class: 
 * Create an instance, passing in the name, date when he joined, and revenue.
 */
class Customer
{
    /** @var int $id The id of the customer */
    private int $id;

    /**
     * Constructor sets the name, date when joined and revenue of client
     * @param string $name The name of customer
     * @param string $since Date when customer joined
     * @param float $revenue Financial data of customer
     * @return void. 
     */
    public function __construct(
        private string $name,
        private string $since,
        private float $revenue
    ) {
    }

    /**
     * Getter for id
     * @return int 
     */    
    public function getId() :int
    {
        return $this->id;
    }

    /**
     * Setter for name
     * @return void 
     */
    public function setName(string $name) :void
    {
        $this->name = $name;
    }

    /**
     * Getter for name
     * @return string 
     */
    public function getName() :string
    {
        return $this->name;
    }

    /**
     * Setter for since
     * @return void 
     */
    public function setSince(string $since) :void
    {
        $this->since = $since;
    }

    /**
     * Getter for since
     * @return string 
     */
    public function getSince() :string
    {
        return $this->since;
    }

    /**
     * Setter for revenue
     * @return void 
     */
    public function setRevenue(float $revenue) :void
    {
        $this->revenue = $revenue;
    }

    /**
     * Getter for revenue
     * @return float 
     */ 
    public function getRevenue() :float
    {
        return $this->revenue;
    }
}