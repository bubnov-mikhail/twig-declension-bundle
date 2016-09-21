<?php

namespace Bubnov\TwigDeclensionBundle\Twig\Extension;

use Doctrine\ORM\EntityManager;
use Bubnov\TwigDeclensionBundle\Entity\Declension;
use cijic\phpMorphy\Morphy;

class TwigDeclensionExtension extends \Twig_Extension
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     *
     * @var String
     */
    private $locale;
    
    /**
     * Cached Declension
     * @var Array 
     */
    private $cached;
    
    /**
     * Do create Declension if not exist yet
     * @var Boolean 
     */
    private $autoCreate;

    private static $morphyGrammems = [
        Declension::INFINITIVE => ['ИМ', 'ЕД'],
        Declension::GENITIVE => ['РД', 'ЕД'],
        Declension::DATIVE => ['ДТ', 'ЕД'],
        Declension::ACCUSATIVE => ['ВН', 'ЕД'],
        Declension::ABLATIVE => ['ТВ', 'ЕД'],
        Declension::PREPOSITIONAL => ['ПР', 'ЕД'],
        Declension::MULTI => ['ИМ', 'МН'],
        Declension::GENITIVE_PLURAL => ['РД', 'МН'],
    ];

    /**
     *
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em, $locale = 'ru', $preChached = false, $autoCreate = false)
    {
        $this->cached = [];
        $this->em = $em;
        $this->locale = $locale;
        $this->autoCreate = $autoCreate;
        if($preChached){
            $this->preCache();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('declension', [$this, 'onDeclension']),
        ];
    }
    
    /**
     * 
     * @param string $infinitive
     * @return string
     */
    public function onDeclension($infinitive = '', $form = Declension::INFINITIVE, $count = null) {
        $infinitive = (string) $infinitive;
        if(empty($infinitive)){
            return $infinitive;
        }
        
        if($declension = $this->getDeclension($infinitive)){
            $form = $declension->getForm($form, $count);
            
            return $this->fixCase($infinitive, $form);
        }

        return $infinitive;
    }
    
    /**
     * Get geclensions of the infinitive
     * @param String $infinitive
     * @return Array
     */
    public function getDeclensions($infinitive = '') {
        if(empty($infinitive)){
            return null;
        }
        
        try {
            $morphy = new Morphy($this->locale);
            $paradigms = $morphy->findWord(mb_strtoupper($infinitive, 'UTF-8'));
            if(false === $paradigms || !isset($paradigms[0])){
                return null;
            }
            $paradigm = $paradigms[0];            
            $result = [];
            foreach(self::$morphyGrammems as $declention => $grammems){
                $wordsInForm = $paradigm->getWordFormsByGrammems($grammems);
                $res = (is_array($wordsInForm) && isset($wordsInForm[0]) && is_object($wordsInForm[0]))
                    ? mb_strtolower($wordsInForm[0]->getWord(), 'UTF-8')
                    : $infinitive
                ;
                $result[$declention] = $res;
            }
            
            return $result;
        } catch (\Exception $e){
            return null;
        }
    }
    
    /**
     * Gets all Declensions from DB for precache
     */
    private function preCache(){
        if($declensions = $this->em->getRepository(Declension::class)->findAll()){
            foreach($declensions as $declension){
                $this->setCached($declension);
            }
        }
    }
    
    /**
     * Make Uppercase for first letter in $form if case of the first letter in $infinitive is also Upper
     * @param string $infinitive
     * @param string $form
     * @return string
     */
    private function fixCase($infinitive = '', $form = '') {
        return ucfirst(strtolower($infinitive)) == $infinitive 
            ? self::my_mb_ucfirst($form)
            : $form
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'declension_extension';
    }
    
    /**
     * 
     * @param String $str
     * @return String
     */
    static private function my_mb_ucfirst($str, $encode = 'UTF-8') {
        $fc = mb_strtoupper(mb_substr($str, 0, 1, $encode), $encode);
        
        return $fc.mb_substr($str, 1 , mb_strlen($str), $encode);
    }

    /**
     * 
     * @param String $infinitive
     * @return Declension | null
     */
    private function getDeclension($infinitive = '')
    {
        if($declension = $this->getCached($infinitive)){
            return $declension;
        }
        if($declension === false){
            return null;
        }

        $repository = $this->em->getRepository(Declension::class);
        if($declension = $repository->findOneByInfinitive($infinitive)){
            $this->setCached($declension);

            return $declension;
        }
        
        if($this->autoCreate){
            $declension = new Declension();
            $declension->setInfinitive($infinitive);
            if($declensions = $this->getDeclensions($infinitive)){
                $declension
                    ->setGenitive($declensions[Declension::GENITIVE])
                    ->setGenitivePlural($declensions[Declension::GENITIVE_PLURAL])
                    ->setDative($declensions[Declension::DATIVE])
                    ->setAccusative($declensions[Declension::ACCUSATIVE])
                    ->setAblative($declensions[Declension::ABLATIVE])
                    ->setPrepositional($declensions[Declension::PREPOSITIONAL])
                    ->setMulti($declensions[Declension::MULTI])
                ;       
            }
            $this->em->persist($declension);
            $this->em->flush();

            $this->setCached($declension);

            return $declension;
        }
        
        $this->setCachedNull($infinitive);
        
        return null;
    }
    
    /**
     * 
     * @param String $infinitive
     * @return Declension | null
     */
    private function getCached($infinitive = '') {
        $infinitive = mb_strtolower($infinitive, 'UTF-8');
        if(isset($this->cached[md5($infinitive)])){

            return $this->cached[md5($infinitive)];
        }

        return null;
    }
    
    /**
     * 
     * @param Declension $declension
     * @return \Bubnov\TwigDeclensionBundle\Twig\Extension\TwigDeclensionExtension
     */
    private function setCached(Declension $declension) {
        $this->cached[md5(mb_strtolower($declension->getInfinitive(), 'UTF-8'))] = $declension;

        return $this;
    }
    
    /**
     * 
     * @param String $infinitive
     * @return \Bubnov\TwigDeclensionBundle\Twig\Extension\TwigDeclensionExtension
     */
    private function setCachedNull($infinitive = '') {
        $infinitive = mb_strtolower($infinitive, 'UTF-8');
        $this->cached[md5($infinitive)] = false;
        
        return $this;
    }
}