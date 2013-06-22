<?php
/**
 * File containing the ValueObjectVisitorPass class.
 *
 * @copyright Copyright (C) 2013 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */
namespace eZ\Bundle\EzPublishRestBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ValueObjectVisitorPass implements CompilerPassInterface
{
    public function process( ContainerBuilder $container )
    {
        if ( !$container->hasDefinition( 'ezpublish_rest.output.value_object_visitor.dispatcher' ) )
        {
            return;
        }

        $definition = $container->getDefinition( 'ezpublish_rest.output.value_object_visitor.dispatcher' );

        foreach ( $container->findTaggedServiceIds( 'ezpublish_rest.output.value_object_visitor' ) as $id => $attributes )
        {
            if ( !isset( $attributes[0]['type'] ) )
                throw new \LogicException( 'ezpublish_rest.output.value_object_visitor service tag needs a "type" attribute to identify the field type. None given.' );

            $definition->addMethodCall(
                'addVisitor',
                array( $attributes[0]["type"], new Reference( $id ) )
            );
        }

    }
}
