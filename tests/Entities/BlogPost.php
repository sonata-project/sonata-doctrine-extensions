<?php

namespace Sonata\Doctrine\Tests\Entities;

/**
 * @Entity
 */
class BlogPost
{
    /**
     * @Id @Column(type="string")
     * @GeneratedValue
     */
    public $id;

    /**
     * @Column(type="string")
     */
    protected $title;

    /**
     * @Column(type="int")
     */
    protected $position;
}
