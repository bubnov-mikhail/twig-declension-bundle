<?php

namespace Bubnov\TwigDeclensionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Declension
 *
 * @ORM\Table(name="declension")
 * @ORM\Entity(repositoryClass="Bubnov\TwigDeclensionBundle\Entity\Repository\DeclensionRepository")
 */
class Declension
{
    /**
     * @var String
     */
    const INFINITIVE = 'inf';
    
    /**
     * @var String
     */
    const MULTI = 'inf_multi';
    
    /**
     * @var String
     */
    const GENITIVE = 'gen';
    
    /**
     * @var String
     */
    const GENITIVE_PLURAL = 'gen_multi';
    
    /**
     * @var String
     */
    const DATIVE = 'dat';
    
    /**
     * @var String
     */
    const ACCUSATIVE = 'acc';
    
    /**
     * @var String
     */
    const ABLATIVE = 'abl';
    
    /**
     * @var String
     */
    const PREPOSITIONAL = 'pre';
    
    /**
     * @var String
     */
    const PLURAL = 'plural';
    
    /**
     * Avalible forms
     * @var Array
     */
    static public $forms = [
        self::INFINITIVE => 'infinitive',
        self::MULTI => 'multi',
        self::GENITIVE => 'genitive',
        self::GENITIVE_PLURAL => 'genitive_plural',
        self::DATIVE => 'dative',
        self::ACCUSATIVE => 'accusative',
        self::ABLATIVE => 'ablative',
        self::PREPOSITIONAL => 'prepositional',
        self::PLURAL => 'plural',
    ];
    
    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=false, unique=true)
     */
    private $infinitive;
    
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $genitive;
    
    
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $genitive_plural;
    
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $dative;
    
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $accusative;
    
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ablative;
    
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $prepositional;
    
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $multi;

    /**
     * 
     * @return String
     */
    public function __toString() {
        return $this->getInfinitive();
    }
    
    /**
     * 
     * @param String $form
     * @return Boolean
     */
    public function hasForm($form = self::INFINITIVE) {
        return isset(self::$forms[$form]);
    }
    
    /**
     * 
     * @param String $form
     * @return String
     */
    public function getForm($form = self::INFINITIVE, $count = null) {
        if($this->hasForm($form)){
            $method = 'get' . ucfirst(self::$forms[$form]);
        
            if($declensioned = $this->$method($count)){
                return $declensioned;
            }
        }
        
        return $this->getInfinitive();
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
     * Set infinitive
     *
     * @param string $infinitive
     * @return Declension
     */
    public function setInfinitive($infinitive)
    {
        $this->infinitive = $infinitive;

        return $this;
    }

    /**
     * Get infinitive
     *
     * @return string 
     */
    public function getInfinitive()
    {
        return $this->infinitive;
    }

    /**
     * Set genitive
     *
     * @param string $genitive
     * @return Declension
     */
    public function setGenitive($genitive)
    {
        $this->genitive = $genitive;

        return $this;
    }

    /**
     * Get genitive
     *
     * @return string 
     */
    public function getGenitive()
    {
        return $this->genitive;
    }

    /**
     * Set dative
     *
     * @param string $dative
     * @return Declension
     */
    public function setDative($dative)
    {
        $this->dative = $dative;

        return $this;
    }

    /**
     * Get dative
     *
     * @return string 
     */
    public function getDative()
    {
        return $this->dative;
    }

    /**
     * Set accusative
     *
     * @param string $accusative
     * @return Declension
     */
    public function setAccusative($accusative)
    {
        $this->accusative = $accusative;

        return $this;
    }

    /**
     * Get accusative
     *
     * @return string 
     */
    public function getAccusative()
    {
        return $this->accusative;
    }

    /**
     * Set ablative
     *
     * @param string $ablative
     * @return Declension
     */
    public function setAblative($ablative)
    {
        $this->ablative = $ablative;

        return $this;
    }

    /**
     * Get ablative
     *
     * @return string 
     */
    public function getAblative()
    {
        return $this->ablative;
    }

    /**
     * Set prepositional
     *
     * @param string $prepositional
     * @return Declension
     */
    public function setPrepositional($prepositional)
    {
        $this->prepositional = $prepositional;

        return $this;
    }

    /**
     * Get prepositional
     *
     * @return string 
     */
    public function getPrepositional()
    {
        return $this->prepositional;
    }
    
    /**
     * Set multi
     *
     * @param string $multi
     * @return Declension
     */
    public function setMulti($multi)
    {
        $this->multi = $multi;

        return $this;
    }

    /**
     * Get multi
     *
     * @return string 
     */
    public function getMulti()
    {
        return $this->multi;
    }
    
    /**
     * Get plural
     * @param Integer $n
     * @return string 
     */
    public function getPlural($n = null)
    {
        if(is_null($n) || !is_integer($n)){
            return $this->getInfinitive();
        }
        
        $forms = [
            $this->getInfinitive(),
            $this->getGenitive(),
            $this->getGenitivePlural(),
        ];

        // Magic!
        return ($n%10 == 1 && $n%100 != 11 ? $forms[0] : ($n%10 >= 2 && $n%10 <= 4 && ($n%100 < 10 || $n%100 >= 20))
            ? $forms[1]
            : $forms[2])
        ;
    }

    /**
     * Set genitive_plural
     *
     * @param string $genitivePlural
     * @return Declension
     */
    public function setGenitivePlural($genitivePlural)
    {
        $this->genitive_plural = $genitivePlural;

        return $this;
    }

    /**
     * Get genitive_plural
     *
     * @return string 
     */
    public function getGenitivePlural()
    {
        return $this->genitive_plural;
    }
    
    /**
     * @return Boolean
     */
    public function isNeedWork(){
        foreach(self::$forms as $form){
            if(empty($this->$form) && $form !== 'plural'){
                return true;
            }
        }
        
        return false;
    }
}
