<?php
/**
 * Created by PhpStorm.
 * User: lukas_jahoda
 * Date: 5.1.15
 * Time: 16:33
 */

namespace W3build\PaginateBundle\Twig;


use W3build\PaginateBundle\Result;

class Paginate extends \Twig_Extension {

    /**
     * @var \Twig_Environment
     */
    private $twig;

    public function __construct(\Twig_Environment $twig){
        $this->twig = $twig;
    }

    public function getFunctions(){
        return array(
            new \Twig_SimpleFunction('paginate', array($this, 'render'), array('pre_escape' => 'html', 'is_safe' => array('html'))),
        );
    }

    public function render(Result $paginate, $admin = false){
        if ($admin){
            $template = 'W3buildPaginateBundle::admin.html.twig';
        }
        else {
            $template = 'W3buildPaginateBundle::front.html.twig';
        }

        return $this->twig->render($template, array('paginate' => $paginate));
    }

    public function getName(){
        return 'twig_paginate_extension';
    }

}