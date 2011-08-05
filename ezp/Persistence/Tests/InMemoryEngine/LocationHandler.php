<?php
/**
 * File containing the LocationHandler implementation
 *
 * @copyright Copyright (C) 1999-2011 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 *
 */

namespace ezp\Persistence\Tests\InMemoryEngine;
use ezp\Persistence\Content\Location\Handler as LocationHandlerInterface,
    ezp\Persistence\Content\Location\CreateStruct;

/**
 * @see ezp\Persistence\Content\Location\Handler
 *
 * @version //autogentag//
 */
class LocationHandler implements LocationHandlerInterface
{
    /**
     * @var RepositoryHandler
     */
    protected $handler;

    /**
     * @var Backend
     */
    protected $backend;

    /**
     * Setups current handler instance with reference to RepositoryHandler object that created it.
     *
     * @param RepositoryHandler $handler
     * @param Backend $backend The storage engine backend
     */
    public function __construct( RepositoryHandler $handler, Backend $backend )
    {
        $this->handler = $handler;
        $this->backend = $backend;
    }

    /**
     * @see ezp\Persistence\Content\Location\Handler
     */
    public function load( $locationId )
    {
        return $this->backend->load( 'Content\\Location', $locationId );
    }

    /**
     * @see ezp\Persistence\Content\Location\Handler
     */
    public function copySubtree( $sourceId, $destinationParentId )
    {
    }

    /**
     * @see ezp\Persistence\Content\Location\Handler
     */
    public function move( $sourceId, $destinationParentId )
    {
    }

    /**
     * @see ezp\Persistence\Content\Location\Handler
     */
    public function hide( $id )
    {
    }

    /**
     * @see ezp\Persistence\Content\Location\Handler
     */
    public function unHide( $id )
    {
    }

    /**
     * @see ezp\Persistence\Content\Location\Handler
     */
    public function swap( $locationId1, $locationId2 )
    {
        $location1 = $this->backend->load( 'Content\\Location', $locationId1 );
        $location2 = $this->backend->load( 'Content\\Location', $locationId2 );
        $this->backend->update( 'Content\\Location', $locationId1, array( 'contentId' => $location2->contentId ) );
        $this->backend->update( 'Content\\Location', $locationId2, array( 'contentId' => $location1->contentId ) );
    }

    /**
     * @see ezp\Persistence\Content\Location\Handler
     */
    public function updatePriority( $locationId, $priority )
    {
        return $this->backend->update( 'Content\\Location', $locationId, array( 'priority' => $priority ) );
    }

    /**
     * @see ezp\Persistence\Content\Location\Handler
     */
    public function createLocation( CreateStruct $locationStruct, $parentId )
    {
        $params = (array)$locationStruct;
        $params['parentId'] = $parentId;
        return $this->backend->create( 'Content\\Location', $params );
    }

    /**
     * @see ezp\Persistence\Content\Location\Handler
     */
    public function removeSubtree( $locationId )
    {

    }

    /**
     * @see ezp\Persistence\Content\Location\Handler
     */
    public function trashSubtree( $locationId )
    {
    }

    /**
     * @see ezp\Persistence\Content\Location\Handler
     */
    public function untrashSubtree( $locationId )
    {
    }

    /**
     * @see ezp\Persistence\Content\Location\Handler
     */
    public function setSectionForSubtree( $locationId, $sectionId )
    {
    }

    /**
     * @see ezp\Persistence\Content\Location\Handler
     */
    public function storeUrlAliasPath( $path, $locationId, $languageName = null, $alwaysAvailable = false )
    {
    }

    /**
     * @see ezp\Persistence\Content\Location\Handler
     */
    public function createCustomUrlAlias( $alias, $locationId, $forwarding = false, $languageName = null, $alwaysAvailable = false )
    {
    }

    /**
     * @see ezp\Persistence\Content\Location\Handler
     */
    public function createUrlHistoryEntry( $historicUrl, $locationId )
    {
    }

    /**
     * @see ezp\Persistence\Content\Location\Handler
     */
    public function listUrlsForLocation( $locationId, $urlType )
    {
    }

    /**
     * @see ezp\Persistence\Content\Location\Handler
     */
    public function removeUrlsForLocation( $locationId, array $urlIdentifier )
    {
    }

    /**
     * @see ezp\Persistence\Content\Location\Handler
     */
    public function getPath( $locationId, $languageCode )
    {
    }

    /**
     * @see ezp\Persistence\Content\Location\Handler
     */
    public function delete( $locationId )
    {
        $return = $this->backend->delete( 'Content\\Location', $locationId );
        if ( !$return )
            return $return;
    }
}
?>
