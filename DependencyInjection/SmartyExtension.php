<?php
/*
 * This file is part of the NoiseLabs-SmartyBundle package.
 *
 * Copyright (c) 2011-2021 Vítor Brandão <vitor@noiselabs.io>
 *
 * NoiseLabs-SmartyBundle is free software; you can redistribute it
 * and/or modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or (at your option) any later version.
 *
 * NoiseLabs-SmartyBundle is distributed in the hope that it will be
 * useful, but WITHOUT ANY WARRANTY; without even the implied warranty
 * of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with NoiseLabs-SmartyBundle; if not, see
 * <http://www.gnu.org/licenses/>.
 */
declare(strict_types=1);

namespace NoiseLabs\Bundle\SmartyBundle\DependencyInjection;

use Exception;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * SmartyExtension.
 *
 * This is the class that loads and manages SmartyBundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class SmartyExtension extends Extension
{
    /**
     * Responds to the smarty configuration parameter.
     *
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('smarty.xml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $engineDefinition = $container->getDefinition('templating.engine.smarty');

        if (!empty($config['globals'])) {
            foreach ($config['globals'] as $key => $global) {
                if (isset($global['type']) && 'service' === $global['type']) {
                    $engineDefinition->addMethodCall('addGlobal', [$key, new Reference($global['id'])]);
                } else {
                    $engineDefinition->addMethodCall('addGlobal', [$key, $global['value']]);
                }
            }
        }

        $templateDirs = [];
        if (isset($config['options']['template_dir']) && is_string($config['options']['template_dir'])) {
            $templateDirs[] = $config['options']['template_dir'];
            unset($config['options']['template_dir']);
        }
        if (isset($config['options']['templates_dir']) && is_array($config['options']['templates_dir'])) {
            $templateDirs = array_merge($templateDirs, $config['options']['templates_dir']);
        }
        $config['options']['templates_dir'] = $templateDirs;

        $container->setParameter('smarty.options', $config['options']);
        foreach ($config['options'] as $k => $v) {
            $container->setParameter('smarty.options.'.$k, $v);
        }

        // Console commands
        if (class_exists('\\Symfony\\Component\\Console\\Application')) {
            $loader->load('console.xml');
        }

        /*
         * @note Caching of Smarty classes was causing issues because of the
         * include_once directives used in Smarty.class.php so this
         * feature is disabled.
         *
         * <code>
         * $this->addClassesToCompile(array(
         * 'Smarty',
         * 'Smarty_Internal_Data',
         * 'Smarty_Internal_Templatebase',
         * 'Smarty_Internal_Template',
         * 'Smarty_Resource',
         * 'Smarty_Internal_Resource_File',
         * 'Smarty_Cacheresource',
         * 'Smarty_Internal_Cacheresource_File',
         * ));
         * </code>
         */
    }

    public function getAlias()
    {
        return 'smarty';
    }
}
