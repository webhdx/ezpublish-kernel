<?php

/**
 * ContentService class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\Core\Repository\SiteAccessAware;

use eZ\Publish\API\Repository\ContentService as ContentServiceInterface;
use eZ\Publish\API\Repository\Values\Content\ContentCreateStruct;
use eZ\Publish\API\Repository\Values\Content\ContentUpdateStruct;
use eZ\Publish\API\Repository\Values\Content\ContentMetadataUpdateStruct;
use eZ\Publish\API\Repository\Values\Content\LocationCreateStruct;
use eZ\Publish\API\Repository\Values\Content\TranslationInfo;
use eZ\Publish\API\Repository\Values\Content\TranslationValues;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\API\Repository\Values\User\User;
use eZ\Publish\API\Repository\Values\Content\VersionInfo as APIVersionInfo;
use eZ\Publish\API\Repository\Values\Content\ContentInfo as APIContentInfo;
use eZ\Publish\Core\Repository\Helper\LanguageResolver;

/**
 * SiteAccess aware implementation of ContentService injecting languages where needed.
 */
class ContentService implements ContentServiceInterface
{
    /**
     * Aggregated service.
     *
     * @var \eZ\Publish\API\Repository\ContentService
     */
    protected $service;

    /**
     * Language resolver.
     *
     * @var LanguageResolver
     */
    protected $languageResolver;

    /**
     * Construct service object from aggregated service and LanguageResolver.
     *
     * @param \eZ\Publish\API\Repository\ContentService $service
     * @param LanguageResolver $languageResolver
     */
    public function __construct(
        ContentServiceInterface $service,
        LanguageResolver $languageResolver
    ) {
        $this->service = $service;
        $this->languageResolver = $languageResolver;
    }

    public function loadContentInfo($contentId)
    {
        return $this->service->loadContentInfo($contentId);
    }

    public function loadContentInfoByRemoteId($remoteId)
    {
        return $this->service->loadContentInfoByRemoteId($remoteId);
    }

    public function loadVersionInfo(APIContentInfo $contentInfo, $versionNo = null)
    {
        return $this->service->loadVersionInfo($contentInfo, $versionNo);
    }

    public function loadVersionInfoById($contentId, $versionNo = null)
    {
        return $this->service->loadVersionInfoById($contentId, $versionNo);
    }

    public function loadContentByContentInfo(APIContentInfo $contentInfo, array $languages = null, $versionNo = null, $useAlwaysAvailable = null)
    {
        return $this->service->loadContentByContentInfo(
            $contentInfo,
            $this->languageResolver->getPrioritizedLanguages($languages),
            $versionNo,
            $this->languageResolver->getUseAlwaysAvailable($useAlwaysAvailable, true)
        );
    }

    public function loadContentByVersionInfo(APIVersionInfo $versionInfo, array $languages = null, $useAlwaysAvailable = null)
    {
        return $this->service->loadContentByVersionInfo(
            $versionInfo,
            $this->languageResolver->getPrioritizedLanguages($languages),
            $this->languageResolver->getUseAlwaysAvailable($useAlwaysAvailable, true)
        );
    }

    public function loadContent($contentId, array $languages = null, $versionNo = null, $useAlwaysAvailable = null)
    {
        return $this->service->loadContent(
            $contentId,
            $this->languageResolver->getPrioritizedLanguages($languages),
            $versionNo,
            $this->languageResolver->getUseAlwaysAvailable($useAlwaysAvailable, true)
        );
    }

    public function loadContentByRemoteId($remoteId, array $languages = null, $versionNo = null, $useAlwaysAvailable = null)
    {
        return $this->service->loadContentByRemoteId(
            $remoteId,
            $this->languageResolver->getPrioritizedLanguages($languages),
            $versionNo,
            $this->languageResolver->getUseAlwaysAvailable($useAlwaysAvailable, true)
        );
    }

    public function createContent(ContentCreateStruct $contentCreateStruct, array $locationCreateStructs = array())
    {
        return $this->service->createContent($contentCreateStruct, $locationCreateStructs);
    }

    public function updateContentMetadata(APIContentInfo $contentInfo, ContentMetadataUpdateStruct $contentMetadataUpdateStruct)
    {
        return $this->service->updateContentMetadata($contentInfo, $contentMetadataUpdateStruct);
    }

    public function deleteContent(APIContentInfo $contentInfo)
    {
        return $this->service->deleteContent($contentInfo);
    }

    public function createContentDraft(APIContentInfo $contentInfo, APIVersionInfo $versionInfo = null, User $user = null)
    {
        return $this->service->createContentDraft($contentInfo, $versionInfo, $user);
    }

    public function loadContentDrafts(User $user = null)
    {
        return $this->service->loadContentDrafts($user);
    }

    public function translateVersion(TranslationInfo $translationInfo, TranslationValues $translationValues, User $user = null)
    {
        return $this->service->translateVersion($translationInfo, $translationValues, $user);
    }

    public function updateContent(APIVersionInfo $versionInfo, ContentUpdateStruct $contentUpdateStruct)
    {
        return $this->service->updateContent($versionInfo, $contentUpdateStruct);
    }

    public function publishVersion(APIVersionInfo $versionInfo)
    {
        return $this->service->publishVersion($versionInfo);
    }

    public function deleteVersion(APIVersionInfo $versionInfo)
    {
        return $this->service->deleteVersion($versionInfo);
    }

    public function loadVersions(APIContentInfo $contentInfo)
    {
        return $this->service->loadVersions($contentInfo);
    }

    public function copyContent(APIContentInfo $contentInfo, LocationCreateStruct $destinationLocationCreateStruct, APIVersionInfo $versionInfo = null)
    {
        return $this->service->copyContent($contentInfo, $destinationLocationCreateStruct, $versionInfo);
    }

    public function loadRelations(APIVersionInfo $versionInfo)
    {
        return $this->service->loadRelations($versionInfo);
    }

    public function loadReverseRelations(APIContentInfo $contentInfo)
    {
        return $this->service->loadReverseRelations($contentInfo);
    }

    public function addRelation(APIVersionInfo $sourceVersion, APIContentInfo $destinationContent)
    {
        return $this->service->addRelation($sourceVersion, $destinationContent);
    }

    public function deleteRelation(APIVersionInfo $sourceVersion, APIContentInfo $destinationContent)
    {
        return $this->service->deleteRelation($sourceVersion, $destinationContent);
    }

    public function addTranslationInfo(TranslationInfo $translationInfo)
    {
        return $this->service->addTranslationInfo($translationInfo);
    }

    public function loadTranslationInfos(APIContentInfo $contentInfo, array $filter = array())
    {
        return $this->service->loadTranslationInfos($contentInfo, $filter);
    }

    public function removeTranslation(APIContentInfo $contentInfo, $languageCode)
    {
        return $this->service->removeTranslation($contentInfo, $languageCode);
    }

    public function newContentCreateStruct(ContentType $contentType, $mainLanguageCode)
    {
        return $this->service->newContentCreateStruct($contentType, $mainLanguageCode);
    }

    public function newContentMetadataUpdateStruct()
    {
        return $this->service->newContentMetadataUpdateStruct();
    }

    public function newContentUpdateStruct()
    {
        return $this->service->newContentUpdateStruct();
    }

    public function newTranslationInfo()
    {
        return $this->service->newTranslationInfo();
    }

    public function newTranslationValues()
    {
        return $this->service->newTranslationValues();
    }
}
