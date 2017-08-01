<?php

namespace eZ\Publish\Core\Repository\Helper;

/**
 * Resolves language settings for use in SiteAccess aware Repository.
 *
 * @todo BC BREAK !!: SiteAccessAware Repository will when switched on on by default in current state break all api
 * @todo calls where prioritized languages is used for $language filter parameter! Given it will then by default always
 * @todo filter results in cases where it was from before set to null specifically to return all translations.
 * @todo Solution is either to expose a ShowAllTranslations argument like in UrlAliasService (not in interface yet)
 * @todo or expose new $prioritizedLanguages argument that does not filter and align UrlAliasService $languagesFilter +
 * @todo $prioritizedLanguages logic on this, in order to also omit undocumented $ShowAllTranslations argument.
 *       No matter what the outcome is, what should be exposed in config to users is a ShowAllTranslations or similar
 *       config that in backend UI allows all translations to be shown, while in frontend languages are by default
 *       filtered.
 *
 */
class LanguageResolver
{
    /**
     * Values typically provided by configuration.
     *
     * These will need to change when configuration (scope) changes using setters below.
     */
    private $configLanguages;
    private $useAlwaysAvailable;
    private $showAllTranslations;

    /**
     * Values typically provided by user context, will need to be set depending on your own custom logic using setter.
     *
     * E.g. Backend UI might expose a language selector for the whole backend that should be reflected on both
     *      UI strings as well as default languages to prioritize for repository objects.
     */
    private $contextLanguage;

    public function __construct(array $configLanguages, $useAlwaysAvailable = null, $showAllTranslations = null)
    {
        $this->configLanguages = $configLanguages;
        $this->useAlwaysAvailable = $useAlwaysAvailable;
        $this->showAllTranslations = $showAllTranslations;
    }

    /**
     * For use by event listening to config resolver scope changes (or other event changing configured languages).
     *
     * @param array $configLanguages
     */
    public function setConfigLanguages(array $configLanguages)
    {
        $this->configLanguages = $configLanguages;
    }

    /**
     * For use by custom events / logic setting language for all retrieved objects from repository.
     *
     * User language will, if set, will have prepended before configured languages. But in cases PHP API consumer
     * specifies languages to retrieve repository objects in it will instead be appended as a fallback.
     *
     * @param string|null $contextLanguage
     */
    public function setContextLanguage($contextLanguage)
    {
        $this->contextLanguage = $contextLanguage;
    }

    /**
     * Get prioritized languages taking into account forced-, context- and lastly configured-languages.
     *
     * @param array|null $forcedLanguages Optional, typically arguments provided to API, will be used first if set.
     *
     * @return array
     */
    public function getPrioritizedLanguages(array $forcedLanguages = null)
    {
        $languages = empty($forceLanguages) ? [] : $forcedLanguages;
        if ($this->contextLanguage !== null) {
            $languages[] = $this->contextLanguage;
        }

        return array_unique($languages + $this->configLanguages);
    }

    /**
     * For use by event listening to config resolver scope changes (or other event changing configured languages).
     *
     * @param bool $useAlwaysAvailable
     */
    public function setUseAlwaysAvailable($useAlwaysAvailable)
    {
        $this->useAlwaysAvailable = $useAlwaysAvailable;
    }

    /**
     * Get currently set UseAlwaysAvailable.
     *
     * @param bool|null $forcedUseAlwaysAvailable Optional, if set will be used instead of configured value,
     *        typically arguments provided to API.
     * @param bool $defaultUseAlwaysAvailable
     *
     * @return bool
     */
    public function getUseAlwaysAvailable($forcedUseAlwaysAvailable = null, $defaultUseAlwaysAvailable = true)
    {
        if ($forcedUseAlwaysAvailable !== null) {
            return $forcedUseAlwaysAvailable;
        }

        if ($this->useAlwaysAvailable !== null) {
            return $this->useAlwaysAvailable;
        }

        return $defaultUseAlwaysAvailable;
    }

    /**
     * For use by event listening to config resolver scope changes (or other event changing configured languages).
     *
     * @param bool $showAllTranslations
     */
    public function setShowAllTranslations($showAllTranslations)
    {
        $this->showAllTranslations = $showAllTranslations;
    }

    /**
     * Get currently set UseAlwaysAvailable.
     *
     * @param bool|null $forcedShowAllTranslations Optional, if set will be used instead of configured value,
     *        typically arguments provided to API.
     * @param bool $defaultShowAllTranslations
     *
     * @return bool
     */
    public function getShowAllTranslations($forcedShowAllTranslations = null, $defaultShowAllTranslations = false)
    {
        if ($forcedShowAllTranslations !== null) {
            return $forcedShowAllTranslations;
        }

        if ($this->showAllTranslations !== null) {
            return $this->showAllTranslations;
        }

        return $defaultShowAllTranslations;
    }
}
