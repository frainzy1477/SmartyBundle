<?php
/*
 * This file is part of NoiseLabs-SmartyBundle
 *
 * Copyright 2011-2017 Vítor Brandão <vitor@noiselabs.io>
 *
 * NoiseLabs-SmartyBundle is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser
 * General Public License as published by the Free Software Foundation; either version 3 of the License, or (at your
 * option) any later version.
 *
 * NoiseLabs-SmartyBundle is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the
 * implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU Lesser General Public License
 * for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License along with NoiseLabs-SmartyBundle; if not,
 * see <http://www.gnu.org/licenses/>.
 */

namespace NoiseLabs\Bundle\SmartyBundle\Tests;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use NoiseLabs\Bundle\SmartyBundle\Loader\TemplateLoader;
use NoiseLabs\Bundle\SmartyBundle\SmartyEngine;
use Smarty;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Templating\Loader\Loader;
use Symfony\Component\Templating\Storage\StringStorage;
use Symfony\Component\Templating\TemplateNameParser;
use Symfony\Component\Templating\TemplateReference;
use Symfony\Component\Templating\TemplateReferenceInterface;

/**
 * @internal
 * @coversNothing
 */
class TestCase extends PHPUnitTestCase
{
    /**
     * @var Smarty
     */
    protected $smarty;

    /**
     * @var Loader
     */
    protected $loader;

    /**
     * @var ProjectTemplateEngine
     */
    protected $engine;

    /**
     * @var string
     */
    protected $tmpDir;

    protected function setUp(): void
    {
        if (!class_exists('Smarty')) {
            $this->markTestSkipped('Smarty is not available.');
        }

        $this->tmpDir = sys_get_temp_dir().'/noiselabs-smarty-bundle-test';

        $this->smarty = $this->getSmarty();
        $this->loader = new ProjectTemplateLoader();
        $this->engine = $this->getSmartyEngine();
    }

    public function tearDown(): void
    {
        $this->deleteTmpDir();
    }

    protected function deleteTmpDir()
    {
        if (!file_exists($dir = $this->tmpDir)) {
            return;
        }

        $fs = new Filesystem();
        $fs->remove($dir);
    }

    public function getSmarty()
    {
        return new Smarty();
    }

    public function getSmartyOptions()
    {
        return [
            'caching' => false,
            'compile_dir' => $this->tmpDir.'/templates_c',
        ];
    }

    public function createTemplate($filepath)
    {
        return $this->engine->getSmarty()->createTemplate($filepath);
    }

    protected function renderXml($name, $context = [])
    {
        $template = $this->createTemplate($name);

        return new \SimpleXMLElement($this->engine->render($template));
    }

    public function getSmartyEngine(array $options = [], $global = null, $logger = null)
    {
        $container = $this->createContainer();
        $options = array_merge(
            $this->getSmartyOptions(),
            $options
        );

        $templateParser = new TemplateNameParser();
        $templateLoader = new TemplateLoader($templateParser, $this->loader);

        $engine = new ProjectTemplateEngine(
            $this->smarty,
            $templateLoader,
            $container,
            $options,
            $global,
            $logger
        );

        $engine->setLoader($this->loader);

        return $engine;
    }

    public function getKernel()
    {
        return new KernelForTest('test', true);
    }

    protected function createContainer(array $data = [])
    {
        return new ContainerBuilder(new ParameterBag(array_merge([
            'kernel.bundles' => ['SmartyBundle' => 'NoiseLabs\\Bundle\\SmartyBundle\\SmartyBundle'],
            'kernel.cache_dir' => __DIR__,
            'kernel.compiled_classes' => [],
            'kernel.debug' => false,
            'kernel.environment' => 'test',
            'kernel.name' => 'kernel',
            'kernel.root_dir' => __DIR__,
        ], $data)));
    }
}

/**
 * @author Vítor Brandão <vitor@noiselabs.io>
 *
 * @internal
 * @coversNothing
 */
class KernelForTest extends Kernel
{
    public function getName()
    {
        return 'testkernel';
    }

    public function registerBundles()
    {
    }

    public function init()
    {
    }

    public function getBundles()
    {
        return [];
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
    }
}

/**
 * @author Vítor Brandão <vitor@noiselabs.io>
 */
class ProjectTemplateEngine extends SmartyEngine
{
    /**
     * @var ProjectTemplateLoader
     */
    private $loader;

    public function setLoader(ProjectTemplateLoader $loader)
    {
        $this->loader = $loader;
    }

    public function setTemplate($name, $content)
    {
        $this->loader->setTemplate($name, $content);
    }

    public function getLoader()
    {
        return $this->loader;
    }
}

/**
 * @author Vítor Brandão <vitor@noiselabs.io>
 */
class ProjectTemplateLoader extends Loader
{
    public $templates = [];

    public function setTemplate($name, $content)
    {
        $template = new TemplateReference($name, 'smarty');
        $this->templates[$template->getLogicalName()] = $content;
    }

    public function load(TemplateReferenceInterface $template)
    {
        if (isset($this->templates[$template->getLogicalName()])) {
            $storage = new StringStorage($this->templates[$template->getLogicalName()]);

            return 'string:'.$storage->getContent();
        }

        return false;
    }

    public function isFresh(TemplateReferenceInterface $template, $time)
    {
        return false;
    }
}
