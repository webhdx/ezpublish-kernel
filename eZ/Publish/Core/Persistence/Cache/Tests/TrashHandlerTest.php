<?php

/**
 * File contains Test class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\Core\Persistence\Cache\Tests;

use eZ\Publish\Core\Persistence\Cache\ContentHandler;
use eZ\Publish\Core\Persistence\Cache\LocationHandler;
use eZ\Publish\SPI\Persistence\Content\Location;
use eZ\Publish\SPI\Persistence\Content\Location\Trash\Handler as TrashHandler;

/**
 * Test case for Persistence\Cache\SectionHandler.
 */
class TrashHandlerTest extends AbstractCacheHandlerTest
{
    public function getHandlerMethodName(): string
    {
        return 'trashHandler';
    }

    public function getHandlerClassName(): string
    {
        return TrashHandler::class;
    }

    public function providerForUnCachedMethods(): array
    {
        // string $method, array $arguments, array? $tags, string? $key
        return [
            ['loadTrashItem', [6]],
            ['emptyTrash', []],
            ['deleteTrashItem', [6]],
        ];
    }

    public function providerForCachedLoadMethods(): array
    {
        // string $method, array $arguments, string $key, mixed? $data
        return [
        ];
    }

    public function testRecover()
    {
        $originalLocationId = 6;
        $targetLocationId = 2;
        $contentId = 42;

        $tags = [
            'content-' . $contentId,
            'content-fields-' . $contentId,
            'location-' . $originalLocationId,
            'location-path-' . $originalLocationId,
        ];

        $handlerMethodName = $this->getHandlerMethodName();

        $this->loggerMock->expects($this->once())->method('logCall');

        $innerHandler = $this->createMock($this->getHandlerClassName());
        $contentHandlerMock = $this->createMock(ContentHandler::class);
        $locationHandlerMock = $this->createMock(LocationHandler::class);

        $locationHandlerMock
            ->method('load')
            ->will($this->returnValue(new Location(['id' => $originalLocationId, 'contentId' => $contentId])));

        $this->persistenceHandlerMock
            ->method('contentHandler')
            ->will($this->returnValue($contentHandlerMock));

        $this->persistenceHandlerMock
            ->method('locationHandler')
            ->will($this->returnValue($locationHandlerMock));

        $this->persistenceHandlerMock
            ->expects($this->once())
            ->method($handlerMethodName)
            ->will($this->returnValue($innerHandler));

        $innerHandler
            ->expects($this->once())
            ->method('recover')
            ->with($originalLocationId, $targetLocationId)
            ->will($this->returnValue(null));

        $this->cacheMock
            ->expects($this->once())
            ->method('invalidateTags')
            ->with($tags);

        $handler = $this->persistenceCacheHandler->$handlerMethodName();
        $handler->recover($originalLocationId, $targetLocationId);
    }

    public function testTrashSubtree()
    {
        $locationId = 6;
        $contentId = 42;

        $tags = [
            'content-' . $contentId,
            'content-fields-' . $contentId,
            'location-' . $locationId,
            'location-path-' . $locationId,
        ];

        $handlerMethodName = $this->getHandlerMethodName();

        $this->loggerMock->expects($this->once())->method('logCall');

        $innerHandler = $this->createMock($this->getHandlerClassName());
        $contentHandlerMock = $this->createMock(ContentHandler::class);
        $locationHandlerMock = $this->createMock(LocationHandler::class);

        $locationHandlerMock
            ->method('load')
            ->will($this->returnValue(new Location(['id' => $locationId, 'contentId' => $contentId])));

        $this->persistenceHandlerMock
            ->method('contentHandler')
            ->will($this->returnValue($contentHandlerMock));

        $this->persistenceHandlerMock
            ->method('locationHandler')
            ->will($this->returnValue($locationHandlerMock));

        $this->persistenceHandlerMock
            ->expects($this->once())
            ->method($handlerMethodName)
            ->will($this->returnValue($innerHandler));

        $innerHandler
            ->expects($this->once())
            ->method('trashSubtree')
            ->with($locationId)
            ->will($this->returnValue(null));

        $this->cacheMock
            ->expects($this->once())
            ->method('invalidateTags')
            ->with($tags);

        $handler = $this->persistenceCacheHandler->$handlerMethodName();
        $handler->trashSubtree($locationId);
    }
}
